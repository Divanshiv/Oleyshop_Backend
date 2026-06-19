<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Mail\Customer\OrderPlaced;
use App\Model\BusinessSetting;
use App\Model\Coupon;
use App\Model\CustomerAddress;
use App\Model\DMReview;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\Review;
use App\Models\GuestUser;
use App\Models\OfflinePayment;
use App\Models\OrderArea;
use App\Models\OrderImage;
use App\Models\OrderPartialPayment;
use App\Models\PaymentRequest;
use App\Traits\CalculateOrderDataTrait;
use App\Traits\HelperTrait;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use function App\CentralLogics\translate;

class OrderController extends Controller
{
    use HelperTrait, CalculateOrderDataTrait;
    public function __construct(
        private Coupon $coupon,
        private DMReview $deliverymanReview,
        private Order $order,
        private OrderDetail $orderDetail,
        private Product $product,
        private Review $review,
        private User $user,
        private OrderArea $orderArea
    ) {}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function trackOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'phone' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $phone = $request->input('phone');
        $userId = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $userType = (bool)auth('api')->user() ? 0 : 1;

        $order = $this->order->find($request['order_id']);

        if (!isset($order)) {
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]
            ], 404);
        }

        if (!is_null($phone)) {
            if ($order['is_guest'] == 0) {
                $trackOrder = $this->order
                    ->with(['customer', 'delivery_address'])
                    ->where(['id' => $request['order_id']])
                    ->whereHas('customer', function ($customerSubQuery) use ($phone) {
                        $customerSubQuery->where('phone', $phone);
                    })
                    ->first();
            } else {
                $trackOrder = $this->order
                    ->with(['delivery_address'])
                    ->where(['id' => $request['order_id']])
                    ->whereHas('delivery_address', function ($addressSubQuery) use ($phone) {
                        $addressSubQuery->where('contact_person_number', $phone);
                    })
                    ->first();
            }
        } else {
            $trackOrder = $this->order
                ->where(['id' => $request['order_id'], 'user_id' => $userId, 'is_guest' => $userType])
                ->first();
        }

        if (!isset($trackOrder)) {
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]
            ], 404);
        }

        return response()->json(OrderLogic::track_order($request['order_id']), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function placeOrder(StoreOrderRequest $request): JsonResponse
    {
        $customerId = auth('api')->id() ?? $request->header('guest-id');
        $userType = auth('api')->user() ? 0 : 1;
        $customer = auth('api')->user() ? $this->user->find(auth('api')->user()->id) : null;
        $deliveryChargeInfo = [
            'branch_id' => $request['branch_id'],
            'distance' => $request['distance'],
            'selected_delivery_area' => $request['selected_delivery_area'],
            'order_type' => $request['order_type'],
        ];
        $isPartiallyPaid = (bool)$request['is_partial'] == 1;
        $paymentInfo = [
            'payment_method' => $request['payment_method'],
        ];
        try {
            $amountData = $this->calculateOrderAmount(cart: $request['cart'], couponCode: $request['coupon_code'], customerId: $customerId, deliveryChargeInfo: $deliveryChargeInfo, isPartiallyPaid: $isPartiallyPaid, paymentInfo: $paymentInfo);
        }  catch (ValidationException $e) {
            $formattedErrors = [];

            foreach ($e->errors() as $code => $messages) {
                foreach ($messages as $message) {
                    $formattedErrors[] = [
                        'code' => $code,
                        'message' => $message
                    ];
                }
            }

            return response()->json(['errors' => $formattedErrors], 403);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => [['message' => $e->getMessage()]]], 403);
        }

        $orderAmount = $amountData['ordered_amount'];
        $couponDiscount = $amountData['coupon_discount_amount'];
        $weightChargeAmount = $amountData['weight_charge_amount'];
        $freeDeliveryAmount = $amountData['free_delivery_charge_amount'];

        $maximumAmount = Helpers::get_business_settings('maximum_amount_for_cod_order');
        if ($request->payment_method == 'cash_on_delivery' && Helpers::get_business_settings('maximum_amount_for_cod_order_status') && ($maximumAmount < $orderAmount)) {
            $errors = [];
            $errors[] = ['code' => 'auth-001', 'message' => 'For Cash on Delivery, maximum order amount must be equal or less than ' . $maximumAmount];
            return response()->json(['errors' => $errors], 401);
        }

        $isCodOrOffline = in_array($request->payment_method, ['cash_on_delivery', 'offline_payment']);

        if ($isPartiallyPaid) {
            $paymentStatus = $isCodOrOffline ? 'partially_paid' : 'paid';
        } else {
            $paymentStatus = $isCodOrOffline ? 'unpaid' : 'paid';
        }

        $orderStatus = $isCodOrOffline ? 'pending' : 'confirmed';

        try {
            DB::beginTransaction();
            $orderId = 100000 + Order::all()->count() + 1;
            $or = [
                'id' => $orderId,
                'user_id' => $customerId,
                'is_guest' => $userType,
                'order_amount' => $orderAmount,
                'coupon_code' =>  $request['coupon_code'],
                'coupon_discount_amount' => $couponDiscount,
                'coupon_discount_title' => $request->coupon_discount_title == 0 ? null : 'coupon_discount_title',
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'total_tax_amount' => $amountData['total_product_tax_amount'],
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference ?? null,
                'delivery_address_id' => $request->delivery_address_id,
                'delivery_charge' => $amountData['delivery_charge_amount'],
                'order_note' => $request['order_note'],
                'order_type' => $request['order_type'],
                'branch_id' => $request['branch_id'],
                'time_slot_id' => $request->time_slot_id,
                'date' => date('Y-m-d'),
                'delivery_date' => $request->delivery_date,
                'delivery_address' => json_encode(CustomerAddress::find($request->delivery_address_id) ?? null),
                'payment_by' => $request['payment_method'] == 'offline_payment' ? $request['payment_by'] : null,
                'payment_note' => $request['payment_method'] == 'offline_payment' ? $request['payment_note'] : null,
                'free_delivery_amount' => $freeDeliveryAmount,
                'weight_charge_amount' => $weightChargeAmount,
                'bring_change_amount' => $request->payment_method != 'cash_on_delivery' ? 0 : ($request->bring_change_amount != null ? $request->bring_change_amount : 0),
                'point_value' => $request['point_value'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $orderTimeSlotId = $or['time_slot_id'];
            $orderDeliveryDate = $or['delivery_date'];

            foreach ($request['cart'] as $item) {
                $product = $this->product->find($item['product_id']);
                $price = count(json_decode($product['variations'], true)) > 0 ? Helpers::variation_price($product, json_encode($item['variation'])) : $product['price'];
                $categoryId = collect(json_decode($product['category_ids'], true))->firstWhere('position', 1)['id'] ?? null;
                $category_discount = Helpers::category_discount_calculate($categoryId, $price);
                $product_discount = Helpers::discount_calculate($product, $price);
                $discount = ($category_discount >= $price) ? $product_discount : max($category_discount, $product_discount);
                $discount_type = ($category_discount >= $price) ? 'discount_on_product' : ($product_discount > $category_discount ? 'discount_on_product' : 'discount_on_category');
                $tax_on_product = Helpers::tax_calculate($product, ($price - $discount));


                $or_d = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'time_slot_id' => $orderTimeSlotId,
                    'delivery_date' => $orderDeliveryDate,
                    'product_details' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'unit' => $product['unit'],
                    'tax_amount' => $tax_on_product,
                    'discount_on_product' => $discount,
                    'discount_type' => $discount_type,
                    'variant' => json_encode($item['variant']),
                    'variation' => json_encode($item['variation']),
                    'is_stock_decreased' => 1,
                    'vat_status' => Helpers::get_business_settings('product_vat_tax_status') === 'included' ? 'included' : 'excluded',
                    'point_value' => $item['point_value'] ?? $product['point_value'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $type = $item['variation'][0]['type'];
                $variationStore = [];
                foreach (json_decode($product['variations'], true) as $var) {
                    if ($type == $var['type']) {
                        $var['stock'] -= $item['quantity'];
                    }
                    $variationStore[] = $var;
                }

                $this->product->where(['id' => $product['id']])->update([
                    'variations' => json_encode($variationStore),
                    'total_stock' => $product['total_stock'] - $item['quantity'],
                    'popularity_count' => $product['popularity_count'] + 1
                ]);

                DB::table('order_details')->insert($or_d);
            }

            DB::table('orders')->insertGetId($or);

            if ($request->payment_method == 'wallet_payment') {
                $amount = $or['order_amount'];
                CustomerLogic::create_wallet_transaction($or['user_id'], $amount, 'order_place', $or['id']);
            }

            if ($isPartiallyPaid) {
                $totalOrderAmount = $or['order_amount'];
                $walletAmount = $customer->wallet_balance;
                $dueAmount = $totalOrderAmount - $walletAmount;

                $walletTransaction = CustomerLogic::create_wallet_transaction($or['user_id'], $walletAmount, 'order_place', $or['id']);

                $partial = new OrderPartialPayment();
                $partial->order_id = $or['id'];
                $partial->paid_with = 'wallet_payment';
                $partial->paid_amount = $walletAmount;
                $partial->due_amount = $dueAmount;
                $partial->save();

                if (!in_array($request['payment_method'], ['cash_on_delivery', 'offline_payment'])) {
                    $partial = new OrderPartialPayment;
                    $partial->order_id = $or['id'];
                    $partial->paid_with = $request['payment_method'];
                    $partial->paid_amount = $dueAmount;
                    $partial->due_amount = 0;
                    $partial->save();
                }
            }

            if (Helpers::get_business_settings('order_image_status') == 1 && !empty($request->file('order_images'))) {
                self::uploadOrderImage(orderImages: $request->order_images, orderId: $orderId);
            }

            if ($request['selected_delivery_area']) {
                $orderArea = $this->orderArea;
                $orderArea->order_id = $or['id'];
                $orderArea->branch_id = $or['branch_id'];
                $orderArea->area_id = $request['selected_delivery_area'];
                $orderArea->save();
            }

            DB::commit();

            // Point value flow:
            // - Active member: all point_value credited directly to wallet
            // - Non-member: accumulated to total_point_value, excess after milestone goes to wallet
            if ($or['user_id'] && $or['point_value'] > 0) {
                $orderUser = User::find($or['user_id']);
                if ($orderUser) {
                    if ($orderUser->is_member) {
                        // Member: all points go directly to wallet
                        CustomerLogic::create_wallet_transaction(
                            $orderUser->id,
                            (int)$or['point_value'],
                            'point_value',
                            'Point value credit from order #' . $orderId
                        );
                    } else {
                        // Non-member: accumulate to total_point_value, check activation
                        $orderUser->increment('total_point_value', (int)$or['point_value']);
                        CustomerLogic::processMemberActivation($orderUser->id);
                    }
                }
            }

            if ((bool)auth('api')->user()) {
                $customerFcmToken = auth('api')->user()->cm_firebase_token;
                $languageCode = auth('api')->user()->language_code ?? 'en';
            } else {
                $guest = GuestUser::find($request->header('guest-id'));
                $customerFcmToken = $guest ? $guest->fcm_token : '';
                $languageCode = $guest ? $guest->language_code : 'en';
            }

            $orderStatusMessage = ($request->payment_method == 'cash_on_delivery' || $request->payment_method == 'offline_payment') ? 'pending' : 'confirmed';
            $message = Helpers::order_status_update_message($orderStatusMessage);

            if ($languageCode != 'en') {
                $message = $this->translate_message($languageCode, $orderStatusMessage);
            }

            $order = $this->order->find($orderId);
            $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

            try {
                if ($value) {
                    $data = [
                        'title' => 'Order',
                        'description' => $value,
                        'order_id' => $orderId,
                        'image' => '',
                        'type' => 'order'
                    ];
                    Helpers::send_push_notif_to_device($customerFcmToken, $data);
                }

                $emailServices = Helpers::get_business_settings('mail_config');
                if (isset($emailServices['status']) && $emailServices['status'] == 1 && isset($customer->email)) {
                    Mail::to($customer->email)->send(new OrderPlaced($orderId));
                }
            } catch (\Exception $e) {
            }

            try {
                $data = [
                    'title' => translate('New Order Notification'),
                    'description' => translate('You have new order, Check Please'),
                    'order_id' => $orderId,
                    'image' => '',
                    'type' => 'order_request',
                ];

                Helpers::send_push_notif_to_topic(data: $data, topic: 'grofresh_admin_message', web_push_link: route('admin.orders.list', ['status' => 'all']));
                Helpers::send_push_notif_to_topic(data: $data, topic: 'grofresh_branch_' . $or['branch_id'] . '_message', web_push_link: route('branch.orders.list', ['status' => 'all']));
            } catch (\Exception $exception) {
            }

            return response()->json([
                'message' => 'Order placed successfully!',
                'order_id' => $orderId,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([$e], 403);
        }
    }

    /**
     * @param $orderImages
     * @param $orderId
     * @return true
     */
    private function uploadOrderImage($orderImages, $orderId): bool
    {
        foreach ($orderImages as $image) {
            $image = Helpers::upload('order/', APPLICATION_IMAGE_FORMAT, $image);
            $orderImage = new OrderImage();
            $orderImage->order_id = $orderId;
            $orderImage->image = $image;
            $orderImage->save();
        }
        return true;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderList(Request $request): JsonResponse
    {
        $userId = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $userType = (bool)auth('api')->user() ? 0 : 1;

        $orders = $this->order->with(['customer', 'delivery_man.rating', 'details:id,order_id,quantity'])
            ->where(['user_id' => $userId, 'is_guest' => $userType])
            ->get();

        $orders->each(function ($order) {
            $order->total_quantity = $order->details->sum('quantity');
        });

        $orders->map(function ($data) {
            $data['deliveryman_review_count'] = $this->deliverymanReview->where(['delivery_man_id' => $data['delivery_man_id'], 'order_id' => $data['id']])->count();
            return $data;
        });

        return response()->json($orders->map(function ($data) {
            $data->details_count = (int)$data->details_count;
            return $data;
        }), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'phone' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $phone = $request->input('phone');
        $userId = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $userType = (bool)auth('api')->user() ? 0 : 1;

        $order = $this->order->find($request['order_id']);
        if (!isset($order)) {
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]
            ], 404);
        }

        if (!is_null($phone)) {
            if ($order['is_guest'] == 0) {
                $details = $this->orderDetail
                    ->with(['order', 'order.delivery_address', 'order.customer', 'order.partial_payment', 'order.offline_payment', 'order.order_image'])
                    ->where(['order_id' => $request['order_id']])
                    ->whereHas('order.customer', function ($customerSubQuery) use ($phone) {
                        $customerSubQuery->where('phone', $phone);
                    })
                    ->get();
            } else {
                $details = $this->orderDetail
                    ->with(['order', 'order.delivery_address', 'order.partial_payment', 'order.offline_payment', 'order.order_image'])
                    ->where(['order_id' => $request['order_id']])
                    ->whereHas('order.delivery_address', function ($addressSubQuery) use ($phone) {
                        $addressSubQuery->where('contact_person_number', $phone);
                    })
                    ->get();
            }
        } else {
            $details = $this->orderDetail
                ->with(['order', 'order.partial_payment', 'order.offline_payment'])
                ->where(['order_id' => $request['order_id']])
                ->whereHas('order', function ($q) use ($userId, $userType) {
                    $q->where(['user_id' => $userId, 'is_guest' => $userType]);
                })
                ->orderBy('id', 'desc')
                ->get();
        }


        if ($details->count() > 0) {
            foreach ($details as $detail) {

                $keepVariation = $detail['variation'];

                $variation = json_decode($detail['variation'], true);

                $detail['add_on_ids'] = json_decode($detail['add_on_ids']);
                $detail['add_on_qtys'] = json_decode($detail['add_on_qtys']);
                if (gettype(json_decode($keepVariation)) == 'array') {
                    $new_variation = json_decode($keepVariation);
                } else {
                    $new_variation = [];
                    $new_variation[] = json_decode($detail['variation']);
                }

                $detail['variation'] = $new_variation;

                //                $detail['formatted_variation'] = $new_variation[0] ?? null;
                //                if (isset($new_variation[0]) && $new_variation[0]->type == null){
                //                    $detail['formatted_variation'] = null;
                //                }

                if (is_null($new_variation)) {
                    $detail['formatted_variation'] = null;
                } elseif (is_array($new_variation) && isset($new_variation[0])) {
                    $detail['formatted_variation'] = $new_variation[0];
                    if (isset($new_variation[0]->type) && $new_variation[0]->type == null) {
                        $detail['formatted_variation'] = null;
                    }
                } elseif (is_object($new_variation)) {
                    $detail['formatted_variation'] = $new_variation;
                } else {
                    $detail['formatted_variation'] = null;
                }

                $detail['review_count'] = $this->review->where(['order_id' => $detail['order_id'], 'product_id' => $detail['product_id']])->count();
                $detail['product_details'] = Helpers::product_data_formatting(json_decode($detail['product_details'], true));
            }
            return response()->json($details, 200);
        } else {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => 'Order not found!']
                ]
            ], 404);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancelOrder(Request $request): JsonResponse
    {
        $order = $this->order::find($request['order_id']);

        if (!isset($order)) {
            return response()->json(['errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        if ($order->order_status != 'pending') {
            return response()->json(['errors' => [['code' => 'order', 'message' => 'Order can only cancel when order status is pending!']]], 403);
        }

        $userId = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $userType = (bool)auth('api')->user() ? 0 : 1;

        if ($this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->first()) {

            $order = $this->order->with(['details'])->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->first();

            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    $product = $this->product->find($detail['product_id']);
                    if (isset($product)) {
                        $type = json_decode($detail['variation'])[0]->type;
                        $variationStore = [];
                        foreach (json_decode($product['variations'], true) as $var) {
                            if ($type == $var['type']) {
                                $var['stock'] += $detail['quantity'];
                            }
                            $variationStore[] = $var;
                        }

                        $this->product->where(['id' => $product['id']])->update([
                            'variations' => json_encode($variationStore),
                            'total_stock' => $product['total_stock'] + $detail['quantity'],
                        ]);

                        $this->orderDetail->where(['id' => $detail['id']])->update([
                            'is_stock_decreased' => 0,
                        ]);
                    }
                }
            }
            $this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->update([
                'order_status' => 'canceled',
            ]);
            return response()->json(['message' => 'Order canceled'], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => 'not found!'],
            ],
        ], 401);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePaymentMethod(Request $request): JsonResponse
    {
        $userId = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $userType = (bool)auth('api')->user() ? 0 : 1;

        if ($this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->first()) {
            $this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->update([
                'payment_method' => $request['payment_method'],
            ]);
            return response()->json(['message' => 'Payment method is updated.'], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => 'not found!'],
            ],
        ], 401);
    }

    public function storeOfflinePaymentData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'is_partial' => 'required|in:0,1',
            'payment_info' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = $this->order->find($request['order_id']);

        if (!isset($order)) {
            return response()->json(['errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        // Check if the booking_id already exists
        $existingPayment = OfflinePayment::where('order_id', $request->order_id)->first();

        if ($request->is_partial == 1) {
            $user = auth('api')->user();
            $walletBalance = $user->wallet_balance ?? 0;

            if ($walletBalance <= 0 || $walletBalance >= $order->order_amount) {
                return response()->json(['errors' => [['code' => 'order', 'message' => 'Invalid partial payment data']]], 403);
            }

            $paidAmount = $walletBalance;
            $dueAmount = $order->order_amount - $paidAmount;

            OrderPartialPayment::create([
                'order_id' => $order->id,
                'paid_with' => 'wallet_payment',
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
            ]);

            // Save remaining payment
            OrderPartialPayment::create([
                'order_id' => $order->id,
                'paid_with' => 'offline_payment',
                'paid_amount' => $dueAmount,
                'due_amount' => 0,
            ]);

            CustomerLogic::create_wallet_transaction($user->id, $paidAmount, 'order_place', $order->id);

            $order->update(['payment_status' => 'partially_paid']);
        } else {
            $partialData = OrderPartialPayment::where(['order_id' => $order['id']])->first();

            if ($partialData && $partialData->paid_with !== 'offline_payment' && !$existingPayment) {
                $partial = new OrderPartialPayment;
                $partial->order_id = $order['id'];
                $partial->paid_with = 'offline_payment';
                $partial->paid_amount = $partialData->due_amount;
                $partial->due_amount = 0;
                $partial->save();
            }
        }

        if (!$existingPayment) {
            // If no existing record, create a new one
            $existingPayment = new OfflinePayment();
            $existingPayment->order_id = $request->order_id;
        }

        $existingPayment->payment_info = json_encode($request['payment_info']);
        $existingPayment->status = 0;
        $existingPayment->save();

        $order->update(['payment_method' => 'offline_payment']);

        return response()->json(['message' => 'successfully updated'], 200);
    }

    public function switchPaymentMethod(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'payment_method' => 'required',
            'is_partial' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = $this->order->find($request['order_id']);

        if (!$order) {
            return response()->json(['errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        $user = auth('api')->user();

        if (!$user && $request->payment_method == 'wallet_payment') {
            return response()->json(['errors' => [['code' => 'auth', 'message' => 'Guest users cannot use wallet payment']]], 403);
        }

        $walletBalance = $user->wallet_balance ?? 0;
        $orderAmount = $order->order_amount;

        if ($request->is_partial == 1) {
            if (!$user) {
                return response()->json(['errors' => [['code' => 'auth', 'message' => 'Guest users cannot use partial wallet payment']]], 403);
            }

            if ($walletBalance <= 0 || $walletBalance >= $orderAmount) {
                return response()->json(['errors' => [['code' => 'wallet', 'message' => 'Invalid partial payment data']]], 403);
            }

            $paidAmount = $walletBalance;
            $dueAmount = $orderAmount - $paidAmount;

            $order->partial_payment()->delete();

            OrderPartialPayment::create([
                'order_id' => $order->id,
                'paid_with' => 'wallet_payment',
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
            ]);

            OrderPartialPayment::create([
                'order_id' => $order->id,
                'paid_with' => $request['payment_method'],
                'paid_amount' => $dueAmount,
                'due_amount' => 0,
            ]);

            CustomerLogic::create_wallet_transaction($user->id, $paidAmount, 'order_place', $order->id);

            $order->update([
                'payment_method' => $request['payment_method'],
                'payment_status' => 'partially_paid',
                'bring_change_amount' => $request['bring_change_amount'] ?? 0,
            ]);
        } else {

            if ($request->payment_method == 'wallet_payment') {
                if ($walletBalance < $orderAmount) {
                    return response()->json(['errors' => [['code' => 'wallet', 'message' => 'Insufficient wallet balance']]], 403);
                }

                CustomerLogic::create_wallet_transaction($user->id, $orderAmount, 'order_place', $order->id);

                $order->update([
                    'payment_method' => 'wallet_payment',
                    'payment_status' => 'paid',
                ]);
            } elseif ($request->payment_method == 'cash_on_delivery') {

                if ($order->partial_payment->isNotEmpty()) {

                    $order->update([
                        'payment_method' => 'cash_on_delivery',
                        'payment_status' => 'partially_paid',
                        'bring_change_amount' => $request['bring_change_amount'] ?? 0,
                    ]);

                    // Update rows where `paid_with` is not 'wallet_payment'
                    $order->partial_payment()
                        ->where('paid_with', '!=', 'wallet_payment')
                        ->delete();
                } else {

                    $order->update([
                        'payment_method' => 'cash_on_delivery',
                        'payment_status' => 'unpaid',
                        'bring_change_amount' => $request['bring_change_amount'] ?? 0,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Payment method successfully updated'], 200);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function switchDigitalPaymentOrderResponse(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $payment_info = PaymentRequest::where('transaction_id', $request->transaction_id)->first();

        if (!$payment_info) {
            return response()->json([
                'errors' => [['code' => 'payment-info', 'message' => 'Payment info not found!']]
            ], 404);
        }

        $additional_info = json_decode($payment_info->additional_data);

        if (!is_object($additional_info) || !isset($additional_info->order_id)) {
            return response()->json([
                'errors' => [['code' => 'order_id', 'message' => 'Order ID not found in payment info!']]
            ], 404);
        }

        $order_id = $additional_info->order_id;

        return response()->json(['order_id' => $order_id], 200);
    }
}
