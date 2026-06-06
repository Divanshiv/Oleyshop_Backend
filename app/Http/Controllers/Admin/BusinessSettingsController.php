<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\Translation;
use App\Model\BusinessSetting;
use App\Model\Currency;
use App\Model\SocialMedia;
use App\Models\AddonSetting;
use App\Traits\HelperTrait;
use App\Traits\UploadSizeHelper;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isArray;

class BusinessSettingsController extends Controller
{
    use HelperTrait, UploadSizeHelper;
    public function __construct(
        private BusinessSetting $businessSettings
    ){
        $this->initUploadLimits();
    }

    /**
     * @return Application|Factory|View
     */
    public function businessSettingsIndex(): View|Factory|Application
    {
        $logoName = Helpers::get_business_settings('logo');
        $logo = Helpers::onErrorImage($logoName, asset('storage/app/public/restaurant') . '/' . $logoName, asset('public/assets/admin/img/160x160/img2.jpg'), 'restaurant/');

        $favIconName = Helpers::get_business_settings('fav_icon');
        $favIcon = Helpers::onErrorImage($favIconName, asset('storage/app/public/restaurant') . '/' . $favIconName, asset('public/assets/admin/img/160x160/img2.jpg'), 'restaurant/');

        if (!$this->businessSettings->where(['key' => 'fav_icon'])->first()) {
            $this->InsertOrUpdateBusinessData(['key' => 'fav_icon'], [
                'value' => ''
            ]);
        }

        return view('admin-views.business-settings.ecom-index', compact('logo', 'favIcon'));
    }

    public function maintenanceMode(): \Illuminate\Http\JsonResponse
    {
        $mode = Helpers::get_business_settings('maintenance_mode');
        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_mode'], [
            'value' => isset($mode) ? !$mode : 1
        ]);

        $this->sendMaintenanceModeNotification();
        Cache::forget('maintenance');

        if (!$mode){
            return response()->json(['message' => translate('Maintenance Mode is On.')]);
        }
        return response()->json(['message' => translate('Maintenance Mode is Off.')]);
    }

    public function currencySymbolPosition($side): \Illuminate\Http\JsonResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'currency_symbol_position'], [
            'value' => $side
        ]);
        return response()->json(['message' => 'Symbol position is ' . $side]);
    }

    public function phoneVerificationStatus($status): \Illuminate\Http\JsonResponse
    {
        $emailStatus = DB::table('business_settings')->where('key','email_verification')->first()->value;

        if ($emailStatus == 1){
            return response()->json([
                'status' => 0,
                'message' => 'Both email and phone verification can not be active at a time!'
            ]);
        }

        $this->InsertOrUpdateBusinessData(['key' => 'phone_verification'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => translate('Phone verification status updated')
        ]);
    }

    public function emailVerificationStatus($status): \Illuminate\Http\JsonResponse
    {
        $phoneStatus = DB::table('business_settings')->where('key','phone_verification')->first()->value;

        if ($phoneStatus == 1){
            return response()->json([
                'status' => 0,
                'message' => 'Both email and phone verification can not be active at a time!'
            ]);
        }

        $this->InsertOrUpdateBusinessData(['key' => 'email_verification'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Email verification status updated'
        ]);
    }

    public function selfPickupStatus($status): \Illuminate\Http\JsonResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'self_pickup'], [
            'value' => $status
        ]);
        return response()->json(['message' => translate('Pickup status updated')]);
    }

    public function deliverymanSelfRegistrationStatus($status): \Illuminate\Http\JsonResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'dm_self_registration'], [
            'value' => $status
        ]);
        return response()->json(['message' => translate('Delivery man self registration status updated')]);
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function guestCheckoutStatus($status): JsonResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'guest_checkout'], [
            'value' => $status
        ]);
        return response()->json(['message' => translate('guest checkout status updated')]);
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function partialPaymentStatus($status): JsonResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'partial_payment'], [
            'value' => $status
        ]);
        return response()->json(['message' => translate('partial payment status updated') ]);
    }

    public function maximumAmountStatus($status): \Illuminate\Http\JsonResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'maximum_amount_for_cod_order_status'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => translate('status updated')
        ]);
    }

    public function businessSetup(Request $request): \Illuminate\Http\RedirectResponse
    {
        $check = $this->validateUploadedFile($request, ['logo', 'fav_icon']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'logo' => 'nullable|mimes:jpg,jpeg,png|max:' . $this->maxImageSizeKB,
            'fav_icon' => 'nullable|mimes:jpg,jpeg,png|max:' . $this->maxImageSizeKB,
        ], [
            'logo.mimes' => 'Logo must be a file of type: jpg, jpeg, png',
            'logo.max' => translate('Logo size must be below ' . $this->maxImageSizeReadable),
            'fav_icon.mimes' => 'Fav Icon must be a file of type: jpg, jpeg, png',
            'fav_icon.max' => translate('Fav Icon size must be below ' . $this->maxImageSizeReadable),

        ]);

        $request['google_map_status'] = $request->has('google_map_status') ? 1 : 0;
        $request['admin_order_notification'] = $request->has('admin_order_notification') ? 1 : 0;

        $this->InsertOrUpdateBusinessData(['key' => 'country'], [
            'value' => $request['country']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'time_zone'], [
            'value' => $request['time_zone'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'time_format'], [
            'value' => $request['time_format']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'currency'], [
            'value' => $request['currency'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'decimal_point_settings'], [
            'value' => $request['decimal_point_settings'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'footer_text'], [
            'value' => $request['footer_text'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'pagination_limit'], [
            'value' => $request['pagination_limit'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'restaurant_name'], [
            'value' => $request->restaurant_name
        ]);

        $currentLogo = $this->businessSettings->where(['key' => 'logo'])->first();
        if ($request->has('logo')) {
            $imageName = Helpers::update('restaurant/', $currentLogo->value, APPLICATION_IMAGE_FORMAT, $request->file('logo'));
        } else {
            $imageName = $currentLogo['value'];
        }

        $this->InsertOrUpdateBusinessData(['key' => 'logo'], [
            'value' => $imageName,
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'phone'], [
            'value' => $request['phone'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'email_address'], [
            'value' => $request['email'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'address'], [
            'value' => $request['address'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'partial_payment_combine_with'], [
            'value' => $request['partial_payment_combine_with'],
        ]);

        $currentFavIcon = $this->businessSettings->where(['key' => 'fav_icon'])->first();
        $this->InsertOrUpdateBusinessData(['key' => 'fav_icon'], [
            'value' => $request->has('fav_icon') ? Helpers::update('restaurant/', $currentFavIcon->value, APPLICATION_IMAGE_FORMAT, $request->file('fav_icon')) : $currentFavIcon->value
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'google_map_status'], [
            'value' => $request['google_map_status'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'admin_order_notification'], [
            'value' => $request['admin_order_notification'],
        ]);

        if ($request->has('admin_order_notification_type')){
            $this->InsertOrUpdateBusinessData(['key' => 'admin_order_notification_type'], [
                'value' => $request['admin_order_notification_type'],
            ]);
        }

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function mailIndex(): Factory|View|Application
    {
        return view('admin-views.business-settings.mail-index');
    }

    public function mailSend(Request $request): \Illuminate\Http\JsonResponse
    {
        $responseFlag = 0;
        try {
            $emailServices = Helpers::get_business_settings('mail_config');

            if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                Mail::to($request->email)->send(new \App\Mail\TestEmailSender());
                $responseFlag = 1;
            }
        } catch (\Exception $exception) {
            $responseFlag = 2;
        }

        return response()->json(['success' => $responseFlag]);
    }

    public function mailConfig(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = Helpers::get_business_settings('mail_config');

        $this->InsertOrUpdateBusinessData(['key' => 'mail_config'],[
            'value' => json_encode([
                "status" => $data['status'],
                "name"       => $request['name'],
                "host"       => $request['host'],
                "driver"     => $request['driver'],
                "port"       => $request['port'],
                "username"   => $request['username'],
                "email_id"   => $request['email'],
                "encryption" => $request['encryption'],
                "password"   => $request['password'],
            ]),
        ]);
        Toastr::success(translate('Configuration updated successfully!'));

        return back();
    }

    public function mailConfigStatus($status): \Illuminate\Http\JsonResponse
    {
        $data = Helpers::get_business_settings('mail_config');
        $data['status'] = $status == '1' ? 1 : 0;

        $this->InsertOrUpdateBusinessData(['key' => 'mail_config'],[
            'value' => $data,
        ]);
        return response()->json(['message' => 'Mail config status updated']);
    }

    /**
     * @return Application|Factory|View
     */
    public function paymentIndex(): Factory|View|Application
    {
        $published_status = 0; // Set a default value
        $payment_published_status = config('get_payment_publish_status');
        if (isset($payment_published_status[0]['is_published'])) {
            $published_status = $payment_published_status[0]['is_published'];
        }

        $routes = config('addon_admin_routes');
        $desiredName = 'payment_setup';
        $payment_url = '';

        foreach ($routes as $routeArray) {
            foreach ($routeArray as $route) {
                if ($route['name'] === $desiredName) {
                    $payment_url = $route['url'];
                    break 2;
                }
            }
        }

        $data_values = AddonSetting::whereIn('settings_type', ['payment_config'])
            ->whereIn('key_name', ['ssl_commerz','paypal','stripe','razor_pay','senang_pay','paystack','paymob_accept','flutterwave','bkash','mercadopago'])
            ->get();

        return view('admin-views.business-settings.payment-index', compact('published_status', 'payment_url', 'data_values'));
    }

    public function paymentUpdate(Request $request, $name): \Illuminate\Http\RedirectResponse
    {

        if ($name == 'cash_on_delivery') {
            $payment = $this->businessSettings->where('key', 'cash_on_delivery')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'cash_on_delivery',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $this->InsertOrUpdateBusinessData(['key' => 'cash_on_delivery'],[
                    'key'        => 'cash_on_delivery',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                ]);
            }
        } elseif ($name == 'digital_payment') {
            $payment = $this->businessSettings->where('key', 'digital_payment')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'digital_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $this->InsertOrUpdateBusinessData(['key' => 'digital_payment'],[
                    'key'        => 'digital_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                ]);
            }
        } elseif ($name == 'offline_payment') {
            $payment = $this->businessSettings->where('key', 'offline_payment')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'offline_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $this->InsertOrUpdateBusinessData(['key' => 'offline_payment'],[
                    'key'        => 'offline_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                ]);
            }
        }elseif ($name == 'ssl_commerz_payment') {
            $payment = $this->businessSettings->where('key', 'ssl_commerz_payment')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'ssl_commerz_payment',
                    'value'      => json_encode([
                        'status'         => 1,
                        'store_id'       => '',
                        'store_password' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'ssl_commerz_payment'])->update([
                    'key'        => 'ssl_commerz_payment',
                    'value'      => json_encode([
                        'status'         => $request['status'],
                        'store_id'       => $request['store_id'],
                        'store_password' => $request['store_password'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'razor_pay') {
            $payment = $this->businessSettings->where('key', 'razor_pay')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'razor_pay',
                    'value'      => json_encode([
                        'status'       => 1,
                        'razor_key'    => '',
                        'razor_secret' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'razor_pay'])->update([
                    'key'        => 'razor_pay',
                    'value'      => json_encode([
                        'status'       => $request['status'],
                        'razor_key'    => $request['razor_key'],
                        'razor_secret' => $request['razor_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'paypal') {
            $payment = $this->businessSettings->where('key', 'paypal')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'paypal',
                    'value'      => json_encode([
                        'status'           => 1,
                        'paypal_client_id' => '',
                        'paypal_secret'    => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'paypal'])->update([
                    'key'        => 'paypal',
                    'value'      => json_encode([
                        'status'           => $request['status'],
                        'paypal_client_id' => $request['paypal_client_id'],
                        'paypal_secret'    => $request['paypal_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'stripe') {
            $payment = $this->businessSettings->where('key', 'stripe')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'stripe',
                    'value'      => json_encode([
                        'status'        => 1,
                        'api_key'       => '',
                        'published_key' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'stripe'])->update([
                    'key'        => 'stripe',
                    'value'      => json_encode([
                        'status'        => $request['status'],
                        'api_key'       => $request['api_key'],
                        'published_key' => $request['published_key'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'senang_pay') {
            $payment = $this->businessSettings->where('key', 'senang_pay')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'senang_pay',
                    'value'      => json_encode([
                        'status'      => 1,
                        'secret_key'  => '',
                        'merchant_id' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'senang_pay'])->update([
                    'key'        => 'senang_pay',
                    'value'      => json_encode([
                        'status'      => $request['status'],
                        'secret_key'  => $request['secret_key'],
                        'merchant_id' => $request['merchant_id'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }elseif ($name == 'paystack') {
            $payment = $this->businessSettings->where('key', 'paystack')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key' => 'paystack',
                    'value' => json_encode([
                        'status' => 1,
                        'publicKey' => '',
                        'secretKey' => '',
                        'paymentUrl' => '',
                        'merchantEmail' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'paystack'])->update([
                    'key' => 'paystack',
                    'value' => json_encode([
                        'status' => $request['status'],
                        'publicKey' => $request['publicKey'],
                        'secretKey' => $request['secretKey'],
                        'paymentUrl' => $request['paymentUrl'],
                        'merchantEmail' => $request['merchantEmail'],
                    ]),
                    'updated_at' => now()
                ]);
            }
        } else if ($name == 'bkash') {
            $this->InsertOrUpdateBusinessData(['key' => 'bkash'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'api_secret' => $request['api_secret'],
                    'username' => $request['username'],
                    'password' => $request['password'],
                ])
            ]);
        } else if ($name == 'paymob') {
            $this->InsertOrUpdateBusinessData(['key' => 'paymob'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'iframe_id' => $request['iframe_id'],
                    'integration_id' => $request['integration_id'],
                    'hmac' => $request['hmac']
                ])
            ]);
        } else if ($name == 'flutterwave') {
            $this->InsertOrUpdateBusinessData(['key' => 'flutterwave'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'public_key' => $request['public_key'],
                    'secret_key' => $request['secret_key'],
                    'hash' => $request['hash']
                ])
            ]);
        } else if ($name == 'mercadopago') {
            $this->InsertOrUpdateBusinessData(['key' => 'mercadopago'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'public_key' => $request['public_key'],
                    'access_token' => $request['access_token']
                ])
            ]);
        }else if ($name == '6cash') {
            $this->InsertOrUpdateBusinessData(['key' => '6cash'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'public_key' => $request['public_key'],
                    'secret_key' => $request['secret_key'],
                    'merchant_number' => $request['merchant_number']
                ])
            ]);
        }

        Toastr::success(translate('payment settings updated!'));
        return back();
    }

    public function paymentConfigUpdate(Request $request)
    {
        $check = $this->validateUploadedFile($request, ['gateway_image']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'gateway_image' => 'nullable|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ]);

        $validation = [
            'gateway' => 'required|in:ssl_commerz,paypal,stripe,razor_pay,senang_pay,paystack,paymob_accept,flutterwave,bkash,mercadopago',
            'mode' => 'required|in:live,test'
        ];

        $request['status'] = $request->has('status') ? 1 : 0;

        $additionalData = [];

        if ($request['gateway'] == 'ssl_commerz') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'store_id' => 'required_if:status,1',
                'store_password' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'paypal') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'client_id' => 'required_if:status,1',
                'client_secret' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'stripe') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'api_key' => 'required_if:status,1',
                'published_key' => 'required_if:status,1',
            ];
        } elseif ($request['gateway'] == 'razor_pay') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'api_key' => 'required_if:status,1',
                'api_secret' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'senang_pay') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required_if:status,1',
                'secret_key' => 'required_if:status,1',
                'merchant_id' => 'required_if:status,1'
            ];
        }elseif ($request['gateway'] == 'paystack') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'public_key' => 'required_if:status,1',
                'secret_key' => 'required_if:status,1',
                'merchant_email' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'paymob_accept') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required_if:status,1',
                'api_key' => 'required_if:status,1',
                'iframe_id' => 'required_if:status,1',
                'integration_id' => 'required_if:status,1',
                'hmac' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'mercadopago') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'access_token' => 'required_if:status,1',
                'public_key' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'flutterwave') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'secret_key' => 'required_if:status,1',
                'public_key' => 'required_if:status,1',
                'hash' => 'required_if:status,1'
            ];
        }  elseif ($request['gateway'] == 'bkash') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'app_key' => 'required_if:status,1',
                'app_secret' => 'required_if:status,1',
                'username' => 'required_if:status,1',
                'password' => 'required_if:status,1',
            ];
        }

        $request->validate(array_merge($validation, $additionalData));

        $settings = AddonSetting::where('key_name', $request['gateway'])->where('settings_type', 'payment_config')->first();

        $additionalDataImage = $settings['additional_data'] != null ? json_decode($settings['additional_data']) : null;

        if ($request->has('gateway_image')) {
            $gatewayImage = Helpers::upload('payment_modules/gateway_image/', APPLICATION_IMAGE_FORMAT, $request['gateway_image']);
        } else {
            $gatewayImage = $additionalDataImage != null ? $additionalDataImage->gateway_image : '';
        }

        $payment_additional_data = [
            'gateway_title' => $request['gateway_title'],
            'gateway_image' => $gatewayImage,
        ];

        $validator = Validator::make($request->all(), array_merge($validation, $additionalData));

        AddonSetting::updateOrCreate(['key_name' => $request['gateway'], 'settings_type' => 'payment_config'], [
            'key_name' => $request['gateway'],
            'live_values' => $validator->validate(),
            'test_values' => $validator->validate(),
            'settings_type' => 'payment_config',
            'mode' => $request['mode'],
            'is_active' => $request->status,
            'additional_data' => json_encode($payment_additional_data),
        ]);

        Toastr::success(GATEWAYS_DEFAULT_UPDATE_200['message']);
        return back();

    }

    /**
     * @return Application|Factory|View
     */
    public function currencyIndex(): Factory|View|Application
    {
        return view('admin-views.business-settings.currency-index');
    }

    public function currencyStore(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'currency_code' => 'required|unique:currencies',
        ]);

        Currency::create([
            "country"         => $request['country'],
            "currency_code"   => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate"   => $request['exchange_rate'],
        ]);
        Toastr::success(translate('Currency added successfully!'));
        return back();
    }


    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function currencyEdit($id): Factory|View|Application
    {
        $currency = Currency::find($id);
        return view('admin-views.business-settings.currency-update', compact('currency'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function currencyUpdate(Request $request, $id): Redirector|Application|RedirectResponse
    {
        Currency::where(['id' => $id])->update([
            "country"         => $request['country'],
            "currency_code"   => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate"   => $request['exchange_rate'],
        ]);
        Toastr::success(translate('Currency updated successfully!'));
        return redirect('admin/business-settings/currency-add');
    }

    public function currencyDelete($id): \Illuminate\Http\RedirectResponse
    {
        Currency::where(['id' => $id])->delete();
        Toastr::success(translate('Currency removed successfully!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function termsAndConditions(): View|Factory|Application
    {
        $data = $this->businessSettings->where(['key' => 'terms_and_conditions'])->first();
        if (!$data) {
            $this->businessSettings->insert([
                'key'   => 'terms_and_conditions',
                'value' => '',
            ]);
        }
        return view('admin-views.business-settings.terms-and-conditions', compact('data'));
    }

    public function termsAndConditionsUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->updateBusinessPagesData(request: $request, key: 'terms_and_conditions');
        Toastr::success(translate('Terms and Conditions updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function privacyPolicy(): Factory|View|Application
    {
        $data = $this->businessSettings->where(['key' => 'privacy_policy'])->first();
        if (!$data) {
            $data = [
                'key' => 'privacy_policy',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }
        return view('admin-views.business-settings.privacy-policy', compact('data'));
    }

    public function privacyPolicyUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->updateBusinessPagesData(request: $request, key: 'privacy_policy');
        Toastr::success(translate('Privacy policy updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function aboutUs(): Factory|View|Application
    {
        $data = $this->businessSettings->where(['key' => 'about_us'])->first();
        if (!$data) {
            $data = [
                'key' => 'about_us',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }
        return view('admin-views.business-settings.about-us', compact('data'));
    }

    public function aboutUsUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->updateBusinessPagesData(request: $request, key: 'about_us');

        Toastr::success(translate('About us updated!'));
        return back();
    }


    private function updateBusinessPagesData(Request $request, $key)
    {
        $businessPage = $this->businessSettings->where(['key' => $key])->first();
        $backgroundImage = $request->existing_background_image;

        if ($request->hasFile('background_image'))
        {
            if (empty(json_decode($businessPage->value, true)))
            {
                $backgroundImage = Helpers::upload(dir: 'business-settings/page-setup/', format: APPLICATION_IMAGE_FORMAT, image: $request->file('background_image'));
            } else {
                $backgroundImage = Helpers::update(dir: 'business-settings/page-setup/', old_image: json_decode($businessPage['value'], true)['background_image'], format: APPLICATION_IMAGE_FORMAT, image: $request->file('background_image'));
            }
        }

        $value = ['background_image' => $backgroundImage, 'description' => $request->description];

        $this->InsertOrUpdateBusinessData(['key' => $key], $value);

        return true;
    }

    /**
     * @return Application|Factory|View
     */
    public function faq(): Factory|View|Application
    {
        $data = $this->businessSettings->where(['key' => 'faq'])->first();
        if (!$data) {
            $data = [
                'key' => 'faq',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }
        return view('admin-views.business-settings.faq', compact('data'));
    }

    public function faqUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->updateBusinessPagesData(request: $request, key: 'faq');

        Toastr::success(translate('FAQ updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function cancellationPolicy(): Factory|View|Application
    {
        $data = $this->businessSettings->where(['key' => 'cancellation_policy'])->first();
        $status = $this->businessSettings->where(['key' => 'cancellation_policy_status'])->first();
        if (!$data) {
            $data = [
                'key' => 'cancellation_policy',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }
        if (!$status) {
            $status = [
                'key' => 'cancellation_policy_status',
                'value' => 0,
            ];
            $this->businessSettings->insert($status);
        }
        return view('admin-views.business-settings.cancellation-policy', compact('data', 'status'));
    }

    public function cancellationPolicyUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->updateBusinessPagesData(request: $request, key: 'cancellation_policy');

        Toastr::success(translate('Cancellation Policy updated!'));
        return back();
    }

    public function cancellationPolicyStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'cancellation_policy_status'],[
            'value' => $request->status,
        ]);
        Toastr::success(translate('Status updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function refundPolicy(): Factory|View|Application
    {
        $data = $this->businessSettings->where(['key' => 'refund_policy'])->first();
        $status = $this->businessSettings->where(['key' => 'refund_policy_status'])->first();
        if (!$data) {
            $data = [
                'key' => 'refund_policy',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }
        if (!$status) {
            $status = [
                'key' => 'refund_policy_status',
                'value' => 0,
            ];
            $this->businessSettings->insert($status);
        }
        return view('admin-views.business-settings.refund-policy', compact('data', 'status'));
    }

    public function refundPolicyUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {

        $this->updateBusinessPagesData(request: $request, key: 'refund_policy');

        Toastr::success(translate('Refund Policy updated!'));
        return back();
    }

    public function refundPolicyStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'refund_policy_status'],[
            'value' => $request->status,
        ]);
        Toastr::success(translate('Status updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function returnPolicy(): Factory|View|Application
    {
        $data = $this->businessSettings->where(['key' => 'return_policy'])->first();
        $status = $this->businessSettings->where(['key' => 'return_policy_status'])->first();
        if (!$data) {
            $data = [
                'key' => 'return_policy',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }

        if (!$status) {
            $status = [
                'key' => 'return_policy_status',
                'value' => 0,
            ];
            $this->businessSettings->insert($status);
        }
        return view('admin-views.business-settings.return-policy', compact('data', 'status'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function returnPolicyUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->updateBusinessPagesData(request: $request, key: 'return_policy');
        Toastr::success(translate('Return Policy updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function returnPolicyStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'return_policy_status'],[
            'value' => $request->status,
        ]);
        Toastr::success(translate('Status updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function fcmIndex(): View|Factory|Application
    {
        if (!$this->businessSettings->where(['key' => 'order_pending_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_pending_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'order_confirmation_msg'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_confirmation_msg',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'order_processing_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_processing_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'out_for_delivery_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'out_for_delivery_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'order_delivered_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_delivered_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'delivery_boy_assign_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'delivery_boy_assign_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'delivery_boy_start_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'delivery_boy_start_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'delivery_boy_delivered_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'delivery_boy_delivered_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'customer_notify_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'customer_notify_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }
        if (!$this->businessSettings->where(['key' => 'returned_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'returned_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }if (!$this->businessSettings->where(['key' => 'failed_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'failed_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }if (!$this->businessSettings->where(['key' => 'canceled_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'canceled_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        return view('admin-views.business-settings.fcm-index');
    }

    /**
     * @return Application|Factory|View
     */
    public function fcmConfig(): View|Factory|Application
    {
        if (!$this->businessSettings->where(['key' => 'fcm_topic'])->first()) {
            $this->businessSettings->insert([
                'key' => 'fcm_topic',
                'value' => '',
            ]);
        }
        if (!$this->businessSettings->where(['key' => 'fcm_project_id'])->first()) {
            $this->businessSettings->insert([
                'key' => 'fcm_project_id',
                'value' => '',
            ]);
        }
        if (!$this->businessSettings->where(['key' => 'push_notification_key'])->first()) {
            $this->businessSettings->insert([
                'key' => 'push_notification_key',
                'value' => '',
            ]);
        }

        return view('admin-views.business-settings.fcm-config');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateFcm(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'fcm_project_id'], [
            'value' => $request['fcm_project_id'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'push_notification_service_file_content'], [
            'value' => $request['push_notification_service_file_content'],
        ]);


        $this->InsertOrUpdateBusinessData(['key' => 'firebase_message_config'], [
            'value' => json_encode([
                'apiKey'=> $request->apiKey,
                'authDomain'=> $request->authDomain,
                'projectId'=> $request->projectId,
                'storageBucket'=> $request->storageBucket,
                'messagingSenderId'=> $request->messagingSenderId,
                'appId'=> $request->appId,
                'measurementId'=> $request->measurementId
            ])
        ]);

        self::firebaseMessageConfigFileGen();

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return void
     */
    function firebaseMessageConfigFileGen(): void
    {
        $config = Helpers::get_business_settings('firebase_message_config');

        $apiKey = $config['apiKey'] ?? '';
        $authDomain = $config['authDomain'] ?? '';
        $projectId = $config['projectId'] ?? '';
        $storageBucket = $config['storageBucket'] ?? '';
        $messagingSenderId = $config['messagingSenderId'] ?? '';
        $appId = $config['appId'] ?? '';
        $measurementId = $config['measurementId'] ?? '';

        $filePath = base_path('firebase-messaging-sw.js');

        try {
            if (file_exists($filePath) && !is_writable($filePath)) {
                if (!chmod($filePath, 0644)) {
                    throw new \Exception('File is not writable and permission change failed: ' . $filePath);
                }
            }

            $fileContent = <<<JS
                importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
                importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

                firebase.initializeApp({
                    apiKey: "$apiKey",
                    authDomain: "$authDomain",
                    projectId: "$projectId",
                    storageBucket: "$storageBucket",
                    messagingSenderId: "$messagingSenderId",
                    appId: "$appId",
                    measurementId: "$measurementId"
                });

                const messaging = firebase.messaging();
                messaging.setBackgroundMessageHandler(function (payload) {
                    return self.registration.showNotification(payload.data.title, {
                        body: payload.data.body ? payload.data.body : '',
                        icon: payload.data.icon ? payload.data.icon : ''
                    });
                });
                JS;


            if (file_put_contents($filePath, $fileContent) === false) {
                throw new \Exception('Failed to write to file: ' . $filePath);
            }

        } catch (\Exception $e) {
            //
        }

    }


    /**
     * @param $business_key
     * @param $status_key
     * @param $default_message_key
     * @param $multi_lang_message_key
     * @param $request
     * @return void
     */
    private function updateOrInsertMessage($business_key, $status_key , $default_message_key, $multi_lang_message_key, $request): void
    {
        $status = $request[$status_key] == 1 ? 1 : 0;
        $message = $request[$default_message_key];

        $this->InsertOrUpdateBusinessData(['key' => $business_key], [
            'value' => json_encode([
                'status' => $status,
                'message' => $message,
            ]),
        ]);

        $setting = $this->businessSettings->where('key', $business_key)->first();

        foreach ($request->lang as $index => $lang) {
            if ($lang === 'default') {
                continue;
            }
            $messageValue = $request[$multi_lang_message_key][$index - 1] ?? null;
            if ($messageValue !== null) {
                Translation::updateOrInsert(
                    [
                        'translationable_type' => 'App\Model\BusinessSetting',
                        'translationable_id' => $setting->id,
                        'locale' => $lang,
                        'key' => $multi_lang_message_key,
                    ],
                    ['value' => $messageValue]
                );
            }
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateFcmMessages(Request $request): RedirectResponse
    {
        $this->updateOrInsertMessage('order_pending_message', 'pending_status','pending_message' ,'order_pending_message', $request);
        $this->updateOrInsertMessage('order_confirmation_msg', 'confirm_status','confirm_message' ,'order_confirmation_message', $request);
        $this->updateOrInsertMessage('order_processing_message', 'processing_status','processing_message' ,'order_processing_message', $request);
        $this->updateOrInsertMessage('out_for_delivery_message', 'out_for_delivery_status','out_for_delivery_message' ,'order_out_for_delivery_message', $request);
        $this->updateOrInsertMessage('order_delivered_message', 'delivered_status','delivered_message' ,'order_delivered_message', $request);
        $this->updateOrInsertMessage('delivery_boy_assign_message', 'delivery_boy_assign_status','delivery_boy_assign_message' ,'assign_deliveryman_message', $request);
        $this->updateOrInsertMessage('delivery_boy_start_message', 'delivery_boy_start_status','delivery_boy_start_message' ,'deliveryman_start_message', $request);
        $this->updateOrInsertMessage('delivery_boy_delivered_message', 'delivery_boy_delivered_status','delivery_boy_delivered_message' ,'deliveryman_delivered_message', $request);
        $this->updateOrInsertMessage('customer_notify_message', 'customer_notify_status','customer_notify_message' ,'customer_notification_message', $request);
        $this->updateOrInsertMessage('returned_message', 'returned_status','returned_message' ,'return_order_message', $request);
        $this->updateOrInsertMessage('failed_message', 'failed_status','failed_message' ,'failed_order_message', $request);
        $this->updateOrInsertMessage('canceled_message', 'canceled_status','canceled_message' ,'canceled_order_message', $request);
        $this->updateOrInsertMessage('deliveryman_order_processing_message', 'dm_order_processing_status','dm_order_processing_message' ,'deliveryman_order_processing_message', $request);
        $this->updateOrInsertMessage('add_fund_wallet_message', 'add_fund_status','add_fund_message' ,'add_fund_wallet_message', $request);
        $this->updateOrInsertMessage('add_fund_wallet_bonus_message', 'add_fund_bonus_status','add_fund_bonus_message' ,'add_fund_wallet_bonus_message', $request);

        Toastr::success(translate('Message updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function mapApiSetting(): Factory|View|Application
    {
        return view('admin-views.business-settings.map-api');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function mapApiStore(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'map_api_server_key'], [
            'value' => $request['map_api_server_key'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'map_api_client_key'], [
            'value' => $request['map_api_client_key'],
        ]);
        Toastr::success(translate('Map API updated successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function recaptchaIndex(Request $request): Factory|View|Application
    {
        return view('admin-views.business-settings.recaptcha-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function recaptchaUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'recaptcha'], [
            'key' => 'recaptcha',
            'value' => json_encode([
                'status' => $request['status'],
                'site_key' => $request['site_key'],
                'secret_key' => $request['secret_key']
            ]),
        ]);

        Toastr::success(translate('Updated Successfully'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function appSettingIndex(): Factory|View|Application
    {
        return View('admin-views.business-settings.app-setting-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function appSettingUpdate(Request $request): RedirectResponse
    {
        if($request->platform == 'android')
        {
            $this->InsertOrUpdateBusinessData(['key' => 'play_store_config'], [
                'value' => json_encode([
                    'status' => $request['play_store_status'],
                    'link' => $request['play_store_link'],
                    'min_version' => $request['android_min_version'],

                ]),
            ]);

            Toastr::success(translate('Updated Successfully for Android'));
            return back();
        }

        if($request->platform == 'ios')
        {
            $this->InsertOrUpdateBusinessData(['key' => 'app_store_config'], [
                'value' => json_encode([
                    'status' => $request['app_store_status'],
                    'link' => $request['app_store_link'],
                    'min_version' => $request['ios_min_version'],
                ]),
            ]);

            Toastr::success(translate('Updated Successfully for IOS'));
            return back();
        }

        Toastr::error(translate('Updated failed'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function socialMedia(): Factory|View|Application
    {
        $socialMediaCount = SocialMedia::count();
        return view('admin-views.business-settings.social-media', compact('socialMediaCount'));
    }

    public function fetch(Request $request)
    {
        $socialMedia = SocialMedia::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => 1,
            'view' => view('admin-views.business-settings.partial._social_media_table', compact('socialMedia'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaStore(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:social_medias,name,' . $request->id,
            'link' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if($request->id){
            SocialMedia::updateOrInsert([
                'id' => $request->get('id'),
            ], [
                'name' => $request->get('name'),
                'link' => $request->get('link'),
            ]);
        }else{
            SocialMedia::insert(
                [
                'name' => $request->get('name'),
                'link' => $request->get('link'),
            ]);
        }
        return response()->json([]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaEdit(Request $request): JsonResponse
    {
        $data = SocialMedia::where('id', $request->id)->first();
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaUpdate(Request $request): JsonResponse
    {
        $socialMedia = SocialMedia::find($request->id);
        $socialMedia->name = $request->name;
        $socialMedia->link = $request->link;
        $socialMedia->save();
        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaDelete(Request $request): JsonResponse
    {
        $socialMedia = SocialMedia::find($request->id);
        $socialMedia->delete();
        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaStatusUpdate(Request $request): JsonResponse
    {
        SocialMedia::where(['id' => $request['id']])->update([
            'status' => $request['status'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }

    /**
     * @return Application|Factory|View
     */
    public function mainBranchSetup(): View|Factory|Application
    {
        $main_branch = Branch::where(['id' => 1])->first();
        return view('admin-views.business-settings.main-branch-setup', compact('main_branch'));
    }

    /**
     * @return Application|Factory|View
     */
    public function socialMediaLogin(): Factory|View|Application
    {
        $apple = BusinessSetting::where('key', 'apple_login')->first();
        if (!$apple) {
            $this->InsertOrUpdateBusinessData(['key' => 'apple_login'], [
                'value' => '{"login_medium":"apple","client_id":"","client_secret":"","team_id":"","key_id":"","service_file":"","redirect_url":"","status":""}',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $apple = BusinessSetting::where('key', 'apple_login')->first();
        }
        $appleLoginService = json_decode($apple->value, true);

        return view('admin-views.business-settings.social-media-login', compact('appleLoginService'));
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function googleSocialLogin($status): JsonResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'google_social_login'], [
            'value' => $status
        ]);
        return response()->json(['message' => 'Status updated']);
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function facebookSocialLogin($status): JsonResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'facebook_social_login'], [
            'value' => $status
        ]);
        return response()->json(['message' => 'Status updated']);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateAppleLogin(Request $request): RedirectResponse
    {
        $check = $this->validateUploadedFile($request, ['service_file'], 'file');
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'service_file'=> 'nullable|file:p8|max:' . $this->maxImageSizeKB
        ],[
            'service_file.mimes' => 'service_file must be a file of type: p8',
            'service_file.max' => translate('service_file size must be below ' . $this->maxImageSizeReadable)
        ]);

        $appleLogin = Helpers::get_business_settings('apple_login');

        if ($request->hasFile('service_file')) {
            $fileName = Helpers::fileUpload('apple-login/', 'p8', $request->file('service_file'));
        }

        $data = [
            'value' => json_encode([
                'login_medium' => 'apple',
                'client_id' => $request['client_id'],
                'client_secret' => '',
                'team_id' => $request['team_id'],
                'key_id' => $request['key_id'],
                'service_file' => $fileName ?? $appleLogin['service_file'],
                'redirect_url' => '',
                'status' => $request->has('status') ? 1 : 0,
            ]),
        ];

        $this->InsertOrUpdateBusinessData(['key' => 'apple_login'], $data);

        Toastr::success(translate('settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function productSetup(): Factory|View|Application
    {
        return view('admin-views.business-settings.product-setup-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function productSetupUpdate(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'minimum_stock_limit'], [
            'value' => $request['minimum_stock_limit'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'product_vat_tax_status'], [
            'value' => $request['product_vat_tax_status'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'featured_product_status'], [
            'value' => $request['featured_product_status'] ? 1 : 0,
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'trending_product_status'], [
            'value' => $request['trending_product_status'] ? 1 : 0,
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'most_reviewed_product_status'], [
            'value' => $request['most_reviewed_product_status'] ? 1 : 0,
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'recommended_product_status'], [
            'value' => $request['recommended_product_status'] ? 1 : 0,
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'product_weight_unit'], [
            'value' => $request['product_weight_unit'],
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function cookiesSetup(): Factory|View|Application
    {
        return view('admin-views.business-settings.cookies-setup-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cookiesSetupUpdate(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'cookies'], [
            'value' => json_encode([
                'status' => $request['status'],
                'text' => $request['text'],
            ])
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function OTPSetup(): Factory|View|Application
    {
        return view('admin-views.business-settings.otp-setup');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function OTPSetupUpdate(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'maximum_otp_hit'], [
            'value' => $request['maximum_otp_hit'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'otp_resend_time'], [
            'value' => $request['otp_resend_time'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'temporary_block_time'], [
            'value' => $request['temporary_block_time'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'maximum_login_hit'], [
            'value' => $request['maximum_login_hit'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'temporary_login_block_time'], [
            'value' => $request['temporary_login_block_time'],
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function chatIndex(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'whatsapp'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'whatsapp',
                'value' => json_encode([
                    'status'  => 0,
                    'number' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'telegram'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'telegram',
                'value' => json_encode([
                    'status'  => 0,
                    'user_name' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'messenger'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'messenger',
                'value' => json_encode([
                    'status'  => 0,
                    'user_name' => '',
                ]),
            ]);
        }

        return view('admin-views.business-settings.chat-index');
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateChat(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'whatsapp'], [
            'value' => json_encode([
                'status'  => $request['whatsapp_status'] == 1 ? 1 : 0,
                'number' => $request['whatsapp_number'],
            ]),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'telegram'], [
            'value' => json_encode([
                'status'  => $request['telegram_status'] == 1 ? 1 : 0,
                'user_name' => $request['telegram_user_name'],
            ]),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'messenger'], [
            'value' => json_encode([
                'status'  => $request['messenger_status'] == 1 ? 1 : 0,
                'user_name' => $request['messenger_user_name'],
            ]),
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function customerSetup(): Factory|View|Application
    {
        $data = $this->businessSettings->where('key','like','wallet_%')
            ->orWhere('key','like','loyalty_%')
            ->orWhere('key','like','ref_earning_%')
            ->orWhere('key','like','add_fund_to_wallet%')
            ->orWhere('key','like','ref_earning_%')->get();
        $data = array_column($data->toArray(), 'value','key');

        return view('admin-views.business-settings.customer-setup', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function customerSetupUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'loyalty_point_exchange_rate'=>'nullable|numeric',
            'ref_earning_exchange_rate'=>'nullable|numeric',
            'loyalty_point_minimum_point'=>'numeric|min:0|not_in:0',
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'wallet_status'], [
            'value' => $request['customer_wallet']??0
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'loyalty_point_status'], [
            'value' => $request['customer_loyalty_point']??0
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'ref_earning_status'], [
            'value' => $request['ref_earning_status'] ?? 0
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'loyalty_point_exchange_rate'], [
            'value' => $request['loyalty_point_exchange_rate'] ?? 0
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'ref_earning_exchange_rate'], [
            'value' => $request['ref_earning_exchange_rate'] ?? 0
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'loyalty_point_percent_on_item_purchase'], [
            'value' => $request['loyalty_point_percent_on_item_purchase']??0
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'loyalty_point_minimum_point'], [
            'value' => $request['minimun_transfer_point']??1
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'add_fund_to_wallet'], [
            'value' => $request['add_fund_to_wallet']??0
        ]);

        Toastr::success(translate('customer_settings_updated_successfully'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function orderSetup(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'minimum_order_value'])->first()) {
            $this->InsertOrUpdateBusinessData(['key' => 'minimum_order_value'], [
                'value' => 1,
            ]);
        }

        return view('admin-views.business-settings.order-setup-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function orderSetupUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'order_image_label_name' => 'required_if:order_image_status,on|max:30',
        ]);

       $status = $request->maximum_amount_for_cod_order_status ? 1 : 0;
       $orderImageStatus = $request->order_image_status ? 1 : 0;

        $this->InsertOrUpdateBusinessData(['key' => 'maximum_amount_for_cod_order_status'], [
            'value' => $status
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'maximum_amount_for_cod_order'], [
            'value' => $request['maximum_amount_for_cod_order'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'minimum_order_value'], [
            'value' => $request['minimum_order_value'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'order_image_status'], [
            'value' => $orderImageStatus
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'order_image_label_name'], [
            'value' => $request['order_image_label_name'],
        ]);

        Toastr::success(translate('order_settings_updated_successfully'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function firebaseOTPVerification(): Factory|View|Application
    {
        return view('admin-views.business-settings.firebase-otp-verification');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function firebaseOTPVerificationUpdate(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'firebase_otp_verification'], [
            'value' => json_encode([
                'status'  => $request->has('status') ? 1 : 0,
                'web_api_key' => $request['web_api_key'],
            ]),
        ]);

        if ($request->has('status')){
            foreach (['twilio','nexmo','2factor','msg91', 'signal_wire', 'alphanet_sms'] as $gateway) {
                $keep = AddonSetting::where(['key_name' => $gateway, 'settings_type' => 'sms_config'])->first();
                if (isset($keep)) {
                    $hold = $keep->live_values;
                    $hold['status'] = 0;
                    AddonSetting::where(['key_name' => $gateway, 'settings_type' => 'sms_config'])->update([
                        'live_values' => $hold,
                        'test_values' => $hold,
                        'is_active' => 0,
                    ]);
                }
            }
        }

        Toastr::success(translate('updated_successfully'));
        return back();
    }

    public function maintenanceModeSetup(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_mode'], [
            'value' => $request->has('maintenance_mode') ? 1 : 0
        ]);

        $selectedSystems = [];
        $systems = ['branch_panel', 'customer_app', 'web_app', 'deliveryman_app'];

        foreach ($systems as $system) {
            if ($request->has($system)) {
                $selectedSystems[] = $system;
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_system_setup'], [
            'value' => json_encode($selectedSystems)],
        );

        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_duration_setup'], [
            'value' => json_encode([
                'maintenance_duration'  => $request['maintenance_duration'],
                'start_date' => $request['start_date'] ?? null,
                'end_date' => $request['end_date'] ?? null,
            ]),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_message_setup'], [
            'value' => json_encode([
                'business_number'  => $request->has('business_number') ? 1 : 0,
                'business_email' => $request->has('business_email') ? 1 : 0,
                'maintenance_message' => $request['maintenance_message'],
                'message_body' => $request['message_body']
            ]),
        ]);

        $maintenanceStatus = (integer)(Helpers::get_business_settings('maintenance_mode') ?? 0);
        $selectedMaintenanceDuration = Helpers::get_business_settings('maintenance_duration_setup') ?? [];
        $selectedMaintenanceSystem = Helpers::get_business_settings('maintenance_system_setup')?? [];
        $isBranch = in_array('branch_panel', $selectedMaintenanceSystem) ? 1 : 0;

        $maintenance = [
            'status' => $maintenanceStatus,
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
            'branch_panel' => $isBranch,
            'maintenance_duration' => $selectedMaintenanceDuration['maintenance_duration'],
            'maintenance_messages' => Helpers::get_business_settings('maintenance_message_setup') ?? [],
        ];

        Cache::put('maintenance', $maintenance, now()->addDays(30));

        $this->sendMaintenanceModeNotification();

        Toastr::success(translate('Settings updated!'));
        return back();

    }

    private function sendMaintenanceModeNotification(): void
    {
        $data =[
            'title' => translate('Maintenance Mode Settings Updated'),
            'description' => translate('Maintenance Mode Settings Updated'),
            'type' => 'maintenance',
        ];

        try {
            Helpers::sendPushNotifToTopicForMaintenanceMode($data, 'grofresh');
            Helpers::sendPushNotifToTopicForMaintenanceMode($data, "deliveryman");
        } catch (\Exception $e) {
            //
        }
    }

    public function marketingTools()
    {
        return view('admin-views.business-settings.marketing-tools');
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    private function InsertOrUpdateBusinessData($key, $value): void
    {
        $businessSetting = $this->businessSettings->where(['key' => $key['key']])->first();
        if (in_array($key['key'], ['about_us', 'terms_and_conditions', 'privacy_policy', 'faq', 'cancellation_policy', 'refund_policy', 'return_policy']))
        {
            $data = $value;
        } else {
            $data = $value['value'];
        }
        if ($businessSetting) {
            $businessSetting->value = $data;
            $businessSetting->save();
        } else {
            $this->businessSettings->create($key, $data);
        }
    }

}
