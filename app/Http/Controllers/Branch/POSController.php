<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Mail\Customer\OrderPlaced;
use App\Model\Branch;
use App\Model\Category;
use App\Model\CustomerAddress;
use App\Model\DeliveryMan;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Models\DeliveryChargeByArea;
use App\Models\OrderChangeAmount;
use App\Traits\HelperTrait;
use App\User;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CentralLogics\translate;

class POSController extends Controller
{
    use HelperTrait;
    public function __construct(
        private Branch $branch,
        private Category $category,
        private Order $order,
        private OrderDetail $orderDetail,
        private Product $product,
        private User $user,
        private DeliveryMan $deliveryman
    ) {}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $category = $request->query('category_id', 0);
        $categories = $this->category->where(['position' => 0])->active()->get();
        $keyword = $request->keyword;
        $key = explode(' ', $keyword);
        $users = $this->user->all();

        $products = $this->product->where('total_stock', '>', 0)
            ->when($request->has('category_id') && $request['category_id'] != 0, function ($query) use ($request) {
            $query->whereJsonContains('category_ids', [['id' => (string)$request['category_id']]]);
        })
            ->when($keyword, function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->active()->latest()->paginate(Helpers::getPagination());

        $branch = $this->branch->find(auth('branch')->id());
        return view('branch-views.pos.index', compact('categories', 'products', 'category', 'keyword', 'branch', 'users'));
    }
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function quickView(Request $request): JsonResponse
    {
        $product = $this->product->findOrFail($request->product_id);
        $discount = self::discountCalculation($product, $product['price']);
        $cart = collect(session()->get('cart', []))->filter(fn($value, $key) => is_array($value))->values();
        $cartProduct = $cart->where('id', $request->product_id)->values();
        $variations = json_decode($product->variations, true) ?? [];
        $productVariation = collect($variations)->first()['type'] ?? null;
        $quantity = $cartProduct[0]['quantity'] ?? 1 ;
        $stock = collect($variations)->first()['stock'] ?? $product->total_stock;
        $price = ($product->price - $discount) * $quantity;
        $buttonText = !empty($cartProduct) && count($cartProduct)>0? translate('Update Cart') : translate('Add to Cart');


        if (!empty($productVariation) && is_array($variations)) {
            $matchedVariation = collect($variations)->firstWhere('type', $productVariation);
            if ($matchedVariation) {
                $matchedCart = $cartProduct->firstWhere('variant', $productVariation);
                $stock = $matchedVariation['stock'];

                if ($matchedCart) {
                    $quantity = $matchedCart['quantity'];
                    $price = ($matchedCart['price'] - self::discountCalculation($product, $matchedCart['price'])) * $quantity;
                    $buttonText = translate('Update Cart');
                } else {
                    $quantity = 1;
                    $price = $matchedVariation['price'] - self::discountCalculation($product, $matchedVariation['price']);
                    $buttonText = translate('Add to Cart');

                }
            }
        }

        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos._quick-view-data', compact('product', 'discount', 'quantity', 'price', 'stock', 'buttonText'))->render(),
        ]);
    }


    public function quickViewModalFooter(Request $request)
    {
        $product = $this->product->findOrFail($request->id);
        $discount = self::discountCalculation($product, $product['price']);

        $cart = collect(session()->get('cart', []))->filter(fn($value, $key) => is_array($value))->values();
        $cartProduct = $cart->where('id', $request->id)->values();
        $str = '';
        foreach (json_decode($product->choice_options) as $key => $choice) {
            $option = str_replace(' ', '', $request[$choice->name]);
            $str .= ($str !== '') ? '-' . $option : $option;
        }
        $quantity = 1;
        $price = 0;
        $stock = 0;
        $buttonText = translate('Add to Cart');
        $variations = json_decode($product->variations, true) ?? [];
        if (!empty($str) && is_array($variations)) {
            $matchedVariation = collect($variations)->firstWhere('type', $str);
            if ($matchedVariation) {
                $matchedCart = $cartProduct->firstWhere('variant', $str);
                $stock = $matchedVariation['stock'];
                if ($matchedCart) {
                    $quantity = $matchedCart['quantity'];
                    $price = ($matchedCart['price'] - self::discountCalculation($product, $matchedCart['price'])) * $quantity;
                    $buttonText = translate('Update Cart');
                } else {
                    $price = $matchedVariation['price'] - self::discountCalculation($product, $matchedVariation['price']);
                }
            }
        }

        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos.partials._quick-view-modal-footer', compact('product', 'discount', 'quantity', 'price', 'stock', 'buttonText'))->render(),
            'stock' => $stock
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function variantPrice(Request $request): array
    {
        $product = $this->product->find($request->id);
        $str = '';
        $price = 0;

        foreach (json_decode($product->choice_options) as $key => $choice) {
            if ($str != null) {
                $str .= '-' . str_replace(' ', '', $request[$choice->name]);
            } else {
                $str .= str_replace(' ', '', $request[$choice->name]);
            }
        }

        if ($str != null) {
            $count = count(json_decode($product->variations));
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variations)[$i]->type == $str) {
                    $price = json_decode($product->variations)[$i]->price;
                    $discount = self::discountCalculation($product, $price);
                    $price = $price - $discount;
                    $stock = json_decode($product->variations)[$i]->stock;
                }
            }
        } else {
            $price = $product->price;
            $discount = self::discountCalculation($product, $price);
            $price = $price - $discount;
            $stock = $product->total_stock;
        }

        return array('price' => ($price * $request->quantity), 'stock' => $stock);
    }

    /**
     * @param $product
     * @param $price
     * @return float
     */
    public function discountCalculation($product, $price): float
    {
        $categoryId = null;
        foreach (json_decode($product['category_ids'], true) as $cat) {
            if ($cat['position'] == 1) {
                $categoryId = ($cat['id']);
            }
        }

        $categoryDiscount = Helpers::category_discount_calculate($categoryId, $price);
        $productDiscount = Helpers::discount_calculate($product, $price);
        if ($categoryDiscount >= $price) {
            $discount = $productDiscount;
        } else {
            $discount = max($categoryDiscount, $productDiscount);
        }
        return $discount;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomers(Request $request): JsonResponse
    {
        $key = explode(' ', $request['q']);
        $data = DB::table('users')
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            })
            ->whereNotNull(['f_name', 'l_name', 'phone'])
            ->limit(8)
            ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone ,")") as text')]);

        $data[] = (object)['id' => false, 'text' => translate('walk_in_customer')];

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateTax(Request $request): RedirectResponse
    {
        if ($request->tax < 0) {
            Toastr::error(translate('Tax_can_not_be_less_than_0_percent'));
            return back();
        } elseif ($request->tax > 100) {
            Toastr::error(translate('Tax_can_not_be_more_than_100_percent'));
            return back();
        }

        $cart = $request->session()->get('cart', collect([]));
        $cart['tax'] = $request->tax;
        $request->session()->put('cart', $cart);
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateExtraDiscount(Request $request): JsonResponse
    {
        $total = session()->get('total', 0);
        $discount = $request->input('discount') ?? 0;
        $type = $request->input('type');

        $errors = [];

        if ($discount < 0) {
            $errors['discount'][] = translate('Discount must be greater than or equal to 0');
        }

        if ($type === 'percent' && $discount > 100) {
            $errors['discount'][] = translate('Discount percentage cannot exceed 100%');
        }

        if ($type === 'amount' && $discount > $total) {
            $errors['discount'][] = translate('Discount amount cannot exceed') . ' ' . Helpers::set_symbol($total);
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $request->session()->put('extra_discount', $discount);
        $request->session()->put('extra_discount_type', $type);

        return response()->json([
            'success' => true,
            'message' => translate('Extra discount updated successfully'),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteExtraDiscount(Request $request): JsonResponse
    {
        session()->forget('extra_discount');
        session()->forget('extra_discount_type');

        return response()->json([
            'success' => true,
            'success_message' => translate('Extra discount deleted successfully')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateQuantity(Request $request): JsonResponse
    {
        $cart = $request->session()->get('cart', collect([]));

        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });
        $request->session()->put('cart', $cart);

        $message = Helpers::checkExtraDiscount(
            $cart->sum(fn($item) => ($item['price'] - $item['discount']) * $item['quantity'])
        );

        return $message
            ? response()->json(['status' => 'warning', 'message' => $message], 200)
            : response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addToCart(Request $request): JsonResponse
    {
        $product = $this->product->find($request->id);

        $data = array();
        $data['id'] = $product->id;
        $str = '';
        $variations = [];
        $price = 0;
        $stock = 0;

        foreach (json_decode($product->choice_options) as $key => $choice) {
            $data[$choice->name] = $request[$choice->name];
            $variations[$choice->title] = $request[$choice->name];
            if ($str != null) {
                $str .= '-' . str_replace(' ', '', $request[$choice->name]);
            } else {
                $str .= str_replace(' ', '', $request[$choice->name]);
            }
        }

        if ($str != null) {
            $count = count(json_decode($product->variations));
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variations)[$i]->type == $str) {
                    $price = json_decode($product->variations)[$i]->price;
                    $stock = json_decode($product->variations)[$i]->stock;

                    if ($stock == 0 || $stock < (int) $request['quantity']) {
                        return response()->json([
                            'data' => 0
                        ]);
                    }
                }
            }
        } else {
            $price = $product->price;
            $stock = $product->total_stock;

            if ($stock == 0 || $stock < (int) $request['quantity']) {
                return response()->json([
                    'data' => 0
                ]);
            }
        }

        $data['variations'] = $variations;
        $data['variant'] = $str;

        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart');
            if (count($request->session()->get('cart')) > 0) {
                foreach ($cart as $key => $cartItem) {
                    if (is_array($cartItem) && $cartItem['id'] == $request['id'] && $cartItem['variant'] == $str) {
                        $cart = $cart->map(function ($object) use ($request, $cartItem) {
                            if ($object['id'] == $request->id && $object['variant'] == $cartItem['variant']) {
                                $object['quantity'] = $request->quantity;
                            }
                            return $object;
                        });

                        $request->session()->put('cart', $cart);

                        $message = Helpers::checkExtraDiscount(
                            $cart->sum(fn($item) => ($item['price'] - $item['discount']) * $item['quantity'])
                        );

                        return $message
                            ? response()->json(['status' => 'warning', 'message' => $message, 'data' => 1], 200)
                            : response()->json(['data' => 1], 200);
                    }
                }
            }
        }

        $discount = self::discountCalculation($product, $price);
        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['name'] = $product->name;
        $data['discount'] = $discount;
        $data['image'] = $product->image;
        $data['weight'] = $product->weight;
        $data['total_stock'] = $stock;

        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->push($data);
        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }

        return response()->json([
            'data' => $data,
            'quantity' => $product->total_stock
        ]);
    }

    /**
     * @return Factory|View|Application
     */
    public function cartItems(): View|Factory|Application
    {
        return view('branch-views.pos._cart');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function emptyCart(Request $request): JsonResponse
    {
        session()->forget('cart');
        session()->forget('customer_id');
        session()->forget('address');
        session()->forget('order_type');
        session()->forget('total');
        session()->forget('extra_discount');
        session()->forget('extra_discount_type');
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFromCart(Request $request): JsonResponse
    {
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);

            if (empty($cart) || count($cart) === 0) {
                session()->forget('extra_discount');
                session()->forget('extra_discount_type');
            }
        }

        $message = Helpers::checkExtraDiscount(
            $cart->sum(fn($item) => ($item['price'] - $item['discount']) * $item['quantity'])
        );

        return $message
            ? response()->json(['status' => 'warning', 'message' => $message], 200)
            : response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function order_list(Request $request): View|Factory|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());

        $search = $request['search'];
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];

        $this->order->where(['checked' => 0])->update(['checked' => 1]);

        $query = $this->order->pos()->where(['branch_id' => auth('branch')->id()])->with(['customer', 'branch'])
            ->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            });

        $queryParam = ['start_date' => $startDate, 'end_date' => $endDate, 'per_page' => $perPage];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });
            $queryParam['search'] = $request->search;;
        }

        $orders = $query->latest()->paginate($perPage)->appends($queryParam);

        return view('branch-views.pos.order.list', compact('orders', 'search', 'startDate', 'endDate', 'perPage'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function order_details($id): View|Factory|RedirectResponse|Application
    {
        $order = $this->order->with('details')->where(['id' => $id, 'branch_id' => auth('branch')->id()])->first();
        $deliverymanList = $this->deliveryman->where(['is_active' => 1])
            ->where(function ($query) use ($order) {
                $query->where('branch_id', auth('branch')->id())
                    ->orWhere('branch_id', 0);
            })
            ->get();
        if (isset($order)) {
            return view('branch-views.order.order-view', compact('order', 'deliverymanList'));
        } else {
            Toastr::info(translate('No more orders!'));
            return back();
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function place_order(Request $request): RedirectResponse
    {
        if ($request->session()->has('cart')) {
            if (count($request->session()->get('cart')) < 1) {
                Toastr::error(translate('cart_empty_warning'));
                return back();
            }
        } else {
            Toastr::error(translate('cart_empty_warning'));
            return back();
        }

        $orderType = session()->has('order_type') ? session()->get('order_type') : 'take_away';

        $deliveryCharge = 0;
        if ($orderType == 'home_delivery') {
            if (!session()->has('customer_id')) {
                Toastr::error(translate('please select a customer'));
                return back();
            }

            if (!session()->has('address')) {
                Toastr::error(translate('please select a delivery address'));
                return back();
            }

            $addressData = session()->get('address');
            $distance = $addressData['distance'] ?? 0;
            $areaId = $addressData['area_id'];

            $deliveryCharge = Helpers::get_delivery_charge(branchId: auth('branch')->id(), distance: $distance, selectedDeliveryArea: $areaId);

            $address = [
                'address_type' => 'Home',
                'contact_person_name' => $addressData['contact_person_name'],
                'contact_person_number' => $addressData['contact_person_number'],
                'address' => $addressData['address'],
                'floor' => $addressData['floor'],
                'road' => $addressData['road'],
                'house' => $addressData['house'],
                'longitude' => (string)$addressData['longitude'],
                'latitude' => (string)$addressData['latitude'],
                'user_id' => session()->get('customer_id'),
                'is_guest' => 0,
            ];
            $customerAddress = CustomerAddress::create($address);
        }

        $cart = $request->session()->get('cart');
        $totalTaxAmount = 0;
        $productPrice = 0;
        $orderDetails = [];

        $orderId = 100000 + $this->order->all()->count() + 1;
        if ($this->order->find($orderId)) {
            $orderId = $this->order->orderBy('id', 'DESC')->first()->id + 1;
        }

        $order = $this->order;
        $order->id = $orderId;

        $order->user_id = session()->has('customer_id') ? session('customer_id') : null;
        $order->coupon_discount_title = $request->coupon_discount_title == 0 ? null : 'coupon_discount_title';
        $order->payment_status = $orderType == 'take_away' ? 'paid' : 'unpaid';
        $order->order_status = $orderType == 'take_away' ? 'delivered' : 'confirmed';
        $order->order_type = $orderType == 'take_away' ? 'pos' : 'delivery';
        $order->coupon_code = $request->coupon_code ?? null;
        $order->payment_method = $request->type;
        $order->transaction_reference = $request->transaction_reference ?? null;
        $order->delivery_charge = $deliveryCharge;
        $order->delivery_address_id = $orderType == 'home_delivery' ? $customerAddress->id : null;
        $order->delivery_date = Carbon::now()->format('Y-m-d');
        $order->order_note = null;
        $order->checked = 1;
        $order->created_at = now();
        $order->updated_at = now();

        foreach ($cart as $c) {
            if (is_array($c)) {
                $product = $this->product->find($c['id']);
                if (!empty($product['variations'])) {
                    $type = $c['variant'];
                    foreach (json_decode($product['variations'], true) as $var) {
                        if ($type == $var['type'] && $var['stock'] < $c['quantity']) {
                            Toastr::error($var['type'] . ' ' . translate('is out of stock'));
                            return back();
                        }
                    }
                } else {
                    if (($product->total_stock - $c['quantity']) < 0) {
                        Toastr::error($product->name . ' ' . translate('is out of stock'));
                        return back();
                    }
                }
            }
        }

        $productWeight = 0;

        foreach ($cart as $c) {
            if (is_array($c)) {

                $discountOnProduct = 0;
                $productSubtotal = ($c['price']) * $c['quantity'];
                $discountOnProduct += ($c['discount'] * $c['quantity']);

                $product = $this->product->find($c['id']);
                if ($product) {
                    $price = $c['price'];
                    $discount = $c['discount'];
                    $subTotal = $price - $discount;
                    $taxOnProduct = Helpers::tax_calculate($product, $subTotal);

                    $categoryId = null;
                    foreach (json_decode($product['category_ids'], true) as $cat) {
                        if ($cat['position'] == 1) {
                            $categoryId = ($cat['id']);
                        }
                    }

                    $categoryDiscount = Helpers::category_discount_calculate($categoryId, $price);
                    $productDiscount = self::discountCalculation($product, $price);

                    if ($categoryDiscount >= $price) {
                        $discount = $productDiscount;
                        $discountType = 'discount_on_product';
                    } else {
                        $discount = max($categoryDiscount, $productDiscount);
                        $discountType = $productDiscount > $categoryDiscount ? 'discount_on_product' : 'discount_on_category';
                    }

                    $productWeight += $product['weight'] * $c['quantity'];
                    $product = Helpers::product_data_formatting($product);

                    $or_d = [
                        'product_id' => $c['id'],
                        'product_details' => $product,
                        'quantity' => $c['quantity'],
                        'price' => $price,
                        'tax_amount' => $taxOnProduct,
                        'discount_on_product' => $discount,
                        'discount_type' => $discountType,
                        'variant' => json_encode($c['variant']),
                        'variation' => json_encode($c['variations']),
                        'vat_status' => Helpers::get_business_settings('product_vat_tax_status') === 'included' ? 'included' : 'excluded',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $totalTaxAmount += $or_d['tax_amount'] * $c['quantity'];
                    $productPrice += $productSubtotal - $discountOnProduct;
                    $orderDetails[] = $or_d;
                }

                $variationStore = [];
                if (!empty($product['variations'])) {
                    $type = $c['variant'];
                    foreach ($product['variations'] as $var) {
                        if ($type == $var->type) {
                            $var->stock -= $c['quantity'];
                        }
                        $variationStore[] = $var;
                    }
                }

                $this->product->where(['id' => $product['id']])->update([
                    'variations' => json_encode($variationStore),
                    'total_stock' => $product['total_stock'] - $c['quantity'],
                    'popularity_count' => $product['popularity_count'] + 1
                ]);
            }
        }

        $totalPrice = $productPrice;

        if (session()->has('extra_discount') && session()->has('extra_discount_type')) {
            $extraDiscount = session('extra_discount');
            $extraDiscountType = session('extra_discount_type');
            $extraDiscount = $extraDiscountType == 'percent' && $extraDiscount > 0 ? ($totalPrice * $extraDiscount) / 100 : $extraDiscount;
            $totalPrice -= $extraDiscount;
        }

        $tax = $cart['tax'] ?? 0;
        $totalTaxAmount = ($tax > 0) ? (($totalPrice * $tax) / 100) : $totalTaxAmount;

        $productWeightCharge = 0;
        if ($orderType == 'home_delivery') {
            $productWeightCharge = Helpers::productWeightChargeCalculation(branchId: auth('branch')->id(), weight: $productWeight);
        }

        try {
            $order->extra_discount = $extraDiscount ?? 0;
            $order->total_tax_amount = $totalTaxAmount;
            $order->order_amount = $totalPrice + $totalTaxAmount + $order->delivery_charge + $productWeightCharge;
            $order->coupon_discount_amount = 0.00;
            $order->branch_id = auth('branch')->id();
            $order->weight_charge_amount = $productWeightCharge;
            $order->save();

            foreach ($orderDetails as $key => $item) {
                $orderDetails[$key]['order_id'] = $order->id;
            }

            $this->orderDetail->insert($orderDetails);

            if ($request->type == 'cash' || $request->type == 'card') {
                $orderChangeAmount = new OrderChangeAmount();
                $orderChangeAmount->order_id = $order->id;
                $orderChangeAmount->order_amount = $order->order_amount;
                $orderChangeAmount->paid_amount = $request->paid_amount;
                $orderChangeAmount->save();
            }

            if (session()->has('customer_id')) {
                $emailServices = Helpers::get_business_settings('mail_config');
                $customer = $this->user->find($order->user_id);
                if (isset($emailServices['status']) && isset($customer->email) && $emailServices['status'] == 1) {
                    try {
                        Mail::to($customer->email)->send(new OrderPlaced($order->id));
                    } catch (\Exception $e) {
                        //
                    }
                }

                if ($orderType == 'home_delivery' && isset($customer)) {
                    $customerFcmToken = $customer->cm_firebase_token;
                    $customerLanguageCode = $customer->language_code ?? 'en';

                    $message = Helpers::order_status_update_message('confirmed');

                    if ($customerLanguageCode != 'en') {
                        $message = $this->translate_message($customerLanguageCode, 'confirmed');
                    }

                    $order = $this->order->find($orderId);
                    $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

                    try {
                        if ($value && $customerFcmToken != null) {
                            $data = [
                                'title' => 'Order',
                                'description' => $value,
                                'order_id' => $orderId,
                                'image' => '',
                                'type' => 'order'
                            ];
                            Helpers::send_push_notif_to_device($customerFcmToken, $data);
                        }
                    } catch (\Exception $e) {
                        //
                    }
                }
            }

            session()->forget('cart');
            session()->forget('customer_id');
            session()->forget('address');
            session()->forget('order_type');
            session()->forget('extra_discount');
            session()->forget('extra_discount_type');
            session(['last_order' => $order->id]);

            Toastr::success(translate('order_placed_successfully'));
            return back();
        } catch (\Exception $e) {
            info($e);
        }
        Toastr::warning(translate('failed_to_place_order'));
        return back();
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function generateInvoice($id): JsonResponse
    {
        $order = $this->order->where('id', $id)->first();
        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos.order.invoice', compact('order'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeKeys(Request $request): JsonResponse
    {
        session()->put($request['key'], $request['value']);
        return response()->json('', 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function newCustomerStore(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users'
        ], [
            'f_name.required' => translate('first name is required'),
            'l_name.required' => translate('last name is required'),
            'email.required' => translate('email name is required'),
            'phone.required' => translate('phone name is required'),
            'email.unique' => translate('email must be unique'),
            'phone.unique' => translate('phone must be unique'),
        ]);

        $customer = $this->user;
        $customer->f_name = $request->f_name;
        $customer->l_name = $request->l_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->password = Hash::make('12345678');
        $customer->save();
        Toastr::success(translate('Customer added successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return StreamedResponse|string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function exportOrders(Request $request): StreamedResponse|string
    {
        $queryParam = [];
        $search = $request['search'];
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];

        $query = $this->order->pos()->where(['branch_id' => auth('branch')->id()])->with(['customer', 'branch'])
            ->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            });

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });
            $queryParam['search'] = $request->search;;
        }

        $orders = $query->with('details')->orderBy('id', 'DESC')->get();

        $storage = [];
        foreach ($orders as $order) {
            $vatStatus = $order->details[0] ? $order->details[0]->vat_status : '';
            if ($vatStatus == 'included') {
                $orderAmount = $order['order_amount'] - $order['total_tax_amount'];
            } else {
                $orderAmount = $order['order_amount'];
            }

            $branch = $order->branch ? $order->branch->name : '';
            $customer = $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : 'Walking Customer';
            $storage[] = [
                'order_id' => $order['id'],
                'customer' => $customer,
                'order_amount' => $orderAmount,
                'coupon_discount_amount' => $order['coupon_discount_amount'],
                'payment_status' => $order['payment_status'],
                'order_status' => $order['order_status'],
                'total_tax_amount' => $order['total_tax_amount'],
                'payment_method' => $order['payment_method'],
                'order_type' => $order['order_type'],
                'branch' => $branch,
                'delivery_date' => $order['delivery_date'],
            ];
        }
        return (new FastExcel($storage))->download('pos-orders.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addDeliveryInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 200);
        }

        $branchId = auth('branch')->id();
        $branch = $this->branch->find($branchId);
        $originLat = $branch['latitude'];
        $originLng = $branch['longitude'];
        $destinationLat = $request['latitude'];
        $destinationLng = $request['longitude'];

        if ($request->has('latitude') && $request->has('longitude')) {
            $data = $this->getDistance($originLat, $originLng, $destinationLat, $destinationLng);

            $distance_value = $data[0]['distanceMeters'];
            $distance = $distance_value / 1000;
        }

        if ($request['selected_area_id']) {
            $area = DeliveryChargeByArea::find($request['selected_area_id']);
        }

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => 'Home',
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'distance' => $distance ?? 0,
            'longitude' => (string)$request->longitude,
            'latitude' => (string)$request->latitude,
            'area_id' => $request['selected_area_id'],
            'area_name' => $area->area_name ?? null
        ];

        $request->session()->put('address', $address);

        return response()->json([
            'data' => $address,
            'view' => view('admin-views.pos._address', compact('address'))->render(),
        ]);
    }

    private function getDistance($origin_lat, $origin_lng, $destination_lat, $destination_lng)
    {
        $apiKey = Helpers::get_business_settings('map_api_server_key');
        $url = 'https://routes.googleapis.com/distanceMatrix/v2:computeRouteMatrix';

        $origin = [
            "waypoint" => [
                "location" => [
                    "latLng" => [
                        "latitude" =>  $origin_lat,
                        "longitude" => $origin_lng
                    ]
                ]
            ]
        ];

        $destination = [
            "waypoint" => [
                "location" => [
                    "latLng" => [
                        "latitude" => $destination_lat,
                        "longitude" => $destination_lng
                    ]
                ]
            ]
        ];

        $data = [
            "origins" => $origin,
            "destinations" => $destination,
            "travelMode" => "DRIVE",
            "routingPreference" => "TRAFFIC_AWARE"
        ];

        // API Headers
        $headers = [
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $apiKey,
            'X-Goog-FieldMask' => '*'
        ];

        // Send POST request
        $response = Http::withHeaders($headers)->post($url, $data);
        return $response->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function orderTypeStore(Request $request): JsonResponse
    {
        session()->put('order_type', $request['order_type']);
        return response()->json($request['order_type'], 200);
    }
}
