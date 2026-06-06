<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\CustomerAddress;
use App\Model\Order;
use App\Traits\CalculateOrderDataTrait;
use App\User;
use Illuminate\Http\Request;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use App\Traits\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use function App\CentralLogics\translate;

class PaymentController extends Controller
{
    use CalculateOrderDataTrait;

    public function __construct()
    {

        if (is_dir('App\Traits') && trait_exists('App\Traits\Payment')) {
            $this->extendWithPaymentGatewayTrait();
        }
    }

    private function extendWithPaymentGatewayTrait()
    {
        $extendedControllerClass = $this->generateExtendedControllerClass();
        eval($extendedControllerClass);
    }

    private function generateExtendedControllerClass()
    {
        $baseControllerClass = get_class($this);
        $traitClassName = 'App\Traits\Payment';

        $extendedControllerClass = "
            class ExtendedController extends $baseControllerClass {
                use $traitClassName;
            }
        ";

        return $extendedControllerClass;
    }

    public function addFund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'payment_platform' => 'required|string|in:web,app',
            'call_back' => 'required|url',
            'customer_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $add_fund_to_wallet = Helpers::get_business_settings('add_fund_to_wallet');
        if ($add_fund_to_wallet == 0) {
            return response()->json(['errors' => ['message' => 'Add fund to wallet is not active']], 403);
        }

        $customer_id = $request->input('customer_id');
        $customer = User::firstWhere(['id' => $customer_id, 'is_block' => 0]);
        if (!isset($customer)) {
            return response()->json(['errors' => ['message' => 'Customer not found']], 403);
        }

        $additional_data = [
            'business_name' => Helpers::get_business_settings('restaurant_name') ?? '',
            'business_logo' => asset('storage/app/public/restaurant/' . Helpers::get_business_settings('logo'))
        ];

        $payer = new Payer($customer['f_name'] . ' ' . $customer['l_name'], $customer['email'], $customer['phone'], '');

        $payment_info = new PaymentInfo(
            success_hook: 'add_fund_success',
            failure_hook: 'add_fund_fail',
            currency_code: Helpers::currency_code(),
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: $customer->id,
            receiver_id: null,
            additional_data: $additional_data,
            payment_amount: $request->amount,
            external_redirect_link: $request->call_back,
            attribute: 'add-fund',
            attribute_id: time()
        );

        $receiver_info = new Receiver('receiver_name', 'example.png');
        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return response()->json(['redirect_link' => $redirect_link], 200);


    }

    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'call_back' => 'required',
            'payment_platform' => 'required|string|in:web,app',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (!session()->has('payment_method')) {
            session()->put('payment_method', 'ssl_commerz');
        }

        if ($request->filled('call_back')) {
            session()->put('call_back', $request->call_back);
        }


        session()->put('customer_id', auth('api')->id() ?? $request->header('guest-id'));

        if ($request->filled('is_guest')) {
            session()->put('is_guest', auth('api')->user() ? 0 : 1);
        }

        $customer_id = session('customer_id');
        $is_guest = session('is_guest') == 1 ? 1 : 0;

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

        if ($request->order_id) {
            $order = Order::find($request['order_id']);

            if (!isset($order)) {
                return response()->json(['errors' => ['message' => 'Order not found']], 403);
            }
            session()->put('order_amount', $order['order_amount']);

        } else {
            try {
                $amountData = $this->calculateOrderAmount(cart: $request['cart'], couponCode: $request['coupon_code'], customerId: $customer_id, deliveryChargeInfo: $deliveryChargeInfo, isPartiallyPaid: $isPartiallyPaid, paymentInfo: $paymentInfo, userType: $is_guest);
            } catch (ValidationException $e) {
                $errors = collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'code' => $field,
                        'message' => $messages[0] ?? ''
                    ];
                })->values();

                return response()->json(['errors' => $errors], 403);
            } catch (\Exception $e) {
                return response()->json(['errors' => [
                    ['code' => 'coupon_code', 'message' => $e->getMessage()]
                ]], 403);
            }
            session()->put('order_amount', $amountData['ordered_amount']);
        }

        $order_amount = session('order_amount');

        if (!isset($order_amount)) {
            return response()->json(['errors' => ['message' => 'Amount not found']], 403);
        }

        if ($order_amount < 0) {
            return response()->json(['errors' => ['message' => 'Amount is less than 0']], 403);
        }

        if (!$request->has('payment_method')) {
            return response()->json(['errors' => ['message' => 'Payment not found']], 403);
        }

        if ($request->has('switch_offline_to_digital')) {
            if (!$request->has('order_id')) {
                return response()->json(['errors' => ['message' => 'Order id is required']], 403);
            }
        }

        $switch_offline_to_digital = $request['switch_offline_to_digital'];

        //partial payment validation
        $customer_wallet_balance = 0;

        if ($isPartiallyPaid) {
            if ($is_guest == 1) {
                return response()->json(['errors' => [['code' => 'payment_method', 'message' => translate('partial order does not applicable for guest user')]]], 403);
            }

            $customer = User::firstWhere(['id' => $customer_id, 'is_block' => 0]);

            if (Helpers::get_business_settings('wallet_status') != 1) {
                return response()->json(['errors' => [['code' => 'payment_method', 'message' => translate('customer_wallet_status_is_disable')]]], 403);
            }
            if (isset($customer) && $customer->wallet_balance < 1) {
                return response()->json(['errors' => [['code' => 'payment_method', 'message' => translate('since your wallet balance is less than 1, you can not place partial order')]]], 403);
            }

            $customer_wallet_balance = $customer->wallet_balance;
            $order_amount -= $customer_wallet_balance;
        }

        $additional_data = [
            'business_name' => Helpers::get_business_settings('restaurant_name') ?? '',
            'business_logo' => asset('storage/app/public/restaurant/' . Helpers::get_business_settings('logo'))
        ];


        if ($is_guest == 1) {//guest order
            $address = CustomerAddress::where(['user_id' => $customer_id, 'is_guest' => 1])->first();
            if ($address) {
                $customer = collect([
                    'f_name' => $address['contact_person_name'] ?? '',
                    'l_name' => '',
                    'phone' => $address['contact_person_number'] ?? '',
                    'email' => '',
                ]);
            } else {
                $customer = collect([
                    'f_name' => 'example',
                    'l_name' => 'customer',
                    'phone' => '+88011223344',
                    'email' => 'example@customer.com',
                ]);
            }
        } else { //normal order
            $customer = User::firstWhere(['id' => $customer_id, 'is_block' => 0]);
            if (!isset($customer)) {
                return response()->json(['errors' => ['message' => 'Customer not found']], 403);
            }
            $customer = collect([
                'f_name' => $customer['f_name'],
                'l_name' => $customer['l_name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
            ]);
        }

        $payer = new Payer($customer['f_name'] . ' ' . $customer['l_name'], $customer['email'], $customer['phone'], '');

        if ($switch_offline_to_digital) {

            if ($request['is_partial'] == 1) {
                $data = [
                    'wallet_paid_amount' => $customer_wallet_balance,
                    'digitally_paid_amount' => $order_amount,
                    'order_id' => $order->id,
                    'payment_method' => $request['payment_method'],
                    'is_partial' => $request['is_partial'] ?? 0
                ];
            } else {
                $data = [
                    'digitally_paid_amount' => $order_amount,
                    'order_id' => $order->id,
                    'payment_method' => $request['payment_method'],
                    'is_partial' => $request['is_partial'] ?? 0
                ];
            }

            $additional_data = array_merge($additional_data, $data);

            $payment_info = new PaymentInfo(
                success_hook: 'switch_offline_to_digital_payment_success',
                failure_hook: 'switch_offline_to_digital_payment_fail',
                currency_code: Helpers::currency_code(),
                payment_method: $request->payment_method,
                payment_platform: $request->payment_platform,
                payer_id: session('customer_id'),
                receiver_id: null,
                additional_data: $additional_data,
                payment_amount: $order_amount,
                external_redirect_link: session('call_back'),
                attribute: 'order',
                attribute_id: time()
            );

            $receiver_info = new Receiver('receiver_name', 'example.png');
            $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

            return response()->json(['redirect_link' => $redirect_link], 200);

        }

        // normal order place

        $payment_info = new PaymentInfo(
            success_hook: 'order_place',
            failure_hook: 'order_cancel',
            currency_code: Helpers::currency_code(),
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: session('customer_id'),
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $order_amount,
            external_redirect_link: session('call_back'),
            attribute: 'order',
            attribute_id: time()
        );

        $receiver_info = new Receiver('receiver_name', 'example.png');
        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return response()->json(['redirect_link' => $redirect_link], 200);
    }

    public function success()
    {
        if (session()->has('call_back')) {
            return redirect(session('call_back') . '/success');
        }
        return response()->json(['message' => 'Payment succeeded'], 200);
    }

    public function fail()
    {
        if (session()->has('call_back')) {
            return redirect(session('call_back') . '/fail');
        }
        return response()->json(['message' => 'Payment failed'], 403);
    }
}
