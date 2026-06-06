<?php

namespace App\CentralLogics;

use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\CategoryDiscount;
use App\Model\Currency;
use App\Model\DMReview;
use App\Model\Order;
use App\Model\Review;
use App\Models\DeliveryChargeByArea;
use App\Models\LoginSetup;
use App\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $err_keeper[] = ['code' => $index, 'message' => $error[0]];
        }
        return $err_keeper;
    }

    public static function combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public static function variation_price($product, $variation)
    {
        if (empty(json_decode($variation, true))) {
            $result = $product['price'];
        } else {
            $match = json_decode($variation, true)[0];
            $result = 0;
            foreach (json_decode($product['variations'], true) as $property => $value) {
                if ($value['type'] == $match['type']) {
                    $result = $value['price'];
                }
            }
        }

        return self::set_price($result);
    }

    public static function product_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $variations = [];
                $item['category_ids'] = json_decode($item['category_ids']);
                $item['image'] = json_decode($item['image']);
                $item['attributes'] = json_decode($item['attributes']);
                $item['choice_options'] = json_decode($item['choice_options']);

                $categories = gettype($item['category_ids']) == 'array' ? $item['category_ids'] : json_decode($item['category_ids']);
                if (!is_null($categories) && count($categories) > 0) {
                    $ids = [];
                    foreach ($categories as $value) {
                        if ($value->position == 1) {
                            $ids[] = $value->id;
                        }
                    }
                    $item['category_discount'] = CategoryDiscount::active()->where('category_id', $ids)->first();
                } else {
                    $item['category_discount'] = [];
                }

                foreach (json_decode($item['variations'], true) as $var) {
                    $variations[] = [
                        'type' => $var['type'],
                        'price' => (float)$var['price'],
                        'stock' => isset($var['stock']) ? (int)$var['stock'] : (int)0,
                    ];
                }
                $item['variations'] = $variations;

                if (count($item['translations'])) {
                    foreach ($item['translations'] as $translation) {
                        if ($translation['key'] == 'name') {
                            $item['name'] = $translation['value'];
                        }
                        if ($translation['key'] == 'description') {
                            $item['description'] = $translation['value'];
                        }
                    }
                }
                unset($item['translations']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $variations = [];
            $data['category_ids'] = gettype($data['category_ids']) == 'array' ? $data['category_ids'] : json_decode($data['category_ids']);
            $data['image'] =  gettype($data['image']) == 'array' ? $data['image'] : json_decode($data['image']);
            $data['attributes'] = gettype($data['attributes']) == 'array' ? $data['attributes'] : json_decode($data['attributes']);
            $data['choice_options'] = gettype($data['choice_options']) == 'array' ? $data['choice_options'] : json_decode($data['choice_options']);

            $categories = gettype($data['category_ids']) == 'array' ? $data['category_ids'] : json_decode($data['category_ids'], true);

            if (!is_null($categories) && count($categories) > 0) {
                $ids = [];
                foreach ($categories as $value) {
                    $value = (array)$value;
                    if ($value['position'] == 1) {
                        $ids[] = $value['id'];
                    }
                }
                $data['category_discount'] = CategoryDiscount::active()->where('category_id', $ids)->first();
            } else {
                $data['category_discount'] = [];
            }

            $data['variations'] = gettype($data['variations']) == 'array' ? $data['variations'] : json_decode($data['variations']);

            if (count($data['translations']) > 0) {
                foreach ($data['translations'] as $translation) {
                    if ($translation['key'] == 'name') {
                        $data['name'] = $translation['value'];
                    }
                    if ($translation['key'] == 'description') {
                        $data['description'] = $translation['value'];
                    }
                }
            }
        }

        return $data;
    }

    public static function order_details_formatter($product)
    {
        $data = json_decode($product, true);
        $variations = [];
        $data['category_ids'] = is_array($data['category_ids']) ? $data['category_ids'] : json_decode($data['category_ids'], true);
        $data['image'] = is_array($data['image']) ? $data['image'] : json_decode($data['image'], true);
        $data['attributes'] = is_array($data['attributes']) ? $data['attributes'] : json_decode($data['attributes'], true);
        $data['choice_options'] = is_array($data['choice_options']) ? $data['choice_options'] : json_decode($data['choice_options'], true);


        $categories = gettype($data['category_ids']) == 'array' ? $data['category_ids'] : json_decode($data['category_ids'], true);

        if (!is_null($categories) && count($categories) > 0) {
            $ids = [];
            foreach ($categories as $value) {
                $value = (array)$value;
                if ($value['position'] == 1) {
                    $ids[] = $value['id'];
                }
            }
            $data['category_discount'] = CategoryDiscount::active()->where('category_id', $ids)->first();
        } else {
            $data['category_discount'] = [];
        }
        $data['variations'] = gettype($data['variations']) == 'array' ? $data['variations'] : json_decode($data['variations']);


        return $data;
    }

    public static function get_business_settings($name)
    {
        $config = null;
        $settings = Cache::rememberForever(CACHE_BUSINESS_SETTINGS_TABLE, function () {
            return BusinessSetting::all();
        });

        $data = $settings?->firstWhere('key', $name);
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }

            if (in_array($name, ['about_us', 'terms_and_conditions', 'privacy_policy', 'faq', 'cancellation_policy', 'refund_policy', 'return_policy'], true)) {
                if (is_array($config))
                {
                    $backgroundImage = $config['background_image'] ?? null;

                    if ($backgroundImage && file_exists(storage_path('app/public/business-settings/page-setup/' . $backgroundImage))) {
                        $config['background_image_url'] = asset('storage/app/public/business-settings/page-setup/' . $backgroundImage);
                    } else {
                        $config['background_image_url'] = null;
                    }
                } else {
                    $config = null;
                }
            }
        }
        return $config;
    }


    public static function get_login_settings($name)
    {
        $config = null;
        $settings = Cache::rememberForever(CACHE_LOGIN_SETUP_TABLE, function () {
            return LoginSetup::all();
        });

        $data = $settings?->firstWhere('key', $name);
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }


    public static function currency_code()
    {
        $currency_code = BusinessSetting::where(['key' => 'currency'])->first()->value;
        return $currency_code;
    }

    public static function currency_symbol()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        return $currency_symbol;
    }

    public static function set_symbol($amount)
    {
        $decimal_point_settings = Helpers::get_business_settings('decimal_point_settings');

        $position = Helpers::get_business_settings('currency_symbol_position');

        if (!is_null($position) && $position == 'left') {
            $string = self::currency_symbol() . '' . number_format($amount, $decimal_point_settings);
        } else {
            $string = number_format($amount, $decimal_point_settings) . '' . self::currency_symbol();
        }
        return $string;
    }

    public static function set_price($amount)
    {
        $decimal_point_settings = Helpers::get_business_settings('decimal_point_settings');
        $amount = number_format($amount, $decimal_point_settings, '.', '');

        return $amount;
    }

    /**
     * @param array|null $data
     * @return false|void
     */
    public static function sendNotificationToHttp(array|null $data)
    {
        $config = self::get_business_settings('push_notification_service_file_content');
        $key = (array)$config;
        $url = 'https://fcm.googleapis.com/v1/projects/' . $key['project_id'] . '/messages:send';
        $headers = [
            'Authorization' => 'Bearer ' . self::getAccessToken($key),
            'Content-Type' => 'application/json',
        ];
        try {
            Http::withHeaders($headers)->post($url, $data);
        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function getAccessToken($key)
    {
        $jwtToken = [
            'iss' => $key['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => time() + 3600,
            'iat' => time(),
        ];
        $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtPayload = base64_encode(json_encode($jwtToken));
        $unsignedJwt = $jwtHeader . '.' . $jwtPayload;
        openssl_sign($unsignedJwt, $signature, $key['private_key'], OPENSSL_ALGO_SHA256);
        $jwt = $unsignedJwt . '.' . base64_encode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);
        return $response->json('access_token');
    }


    public static function send_push_notif_to_device($fcm_token, $data, ?string $senderType = null, ?string $name = null, ?string $profile_image = null)
    {
        $postData = [
            'message' => [
                "token" => $fcm_token,
                "data" => [
                    "title" => (string) $data['title'],
                    "body" => (string) $data['description'],
                    "image" => (string) $data['image'],
                    "order_id" => (string) $data['order_id'],
                    "type" => (string) $data['type'],
                    "sender_type" => (string) $senderType,
                    "name" => (string) $name,
                    "profile_image" => (string) $profile_image,
                ],
                "notification" => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                    "image" => (string)$data['image'],
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "notification.wav"
                        ]
                    ]
                ],
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function send_push_notif_to_topic($data, $topic, ?string $web_push_link = null)
    {
        $image = asset('storage/app/public/notification') . '/' . $data['image'];

        $postData = [
            'message' => [
                "topic" => $topic,
                "data" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "order_id" => (string)$data['order_id'],
                    "type" => (string)$data['type'],
                    "image" => (string)$image,
                    "click_action" => $web_push_link ? (string)$web_push_link : '',
                ],
                "notification" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "image" => (string)$image,
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "notification.wav"
                        ]
                    ]
                ],
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function sendPushNotifToTopicForMaintenanceMode($data, $topic)
    {
        $postData = [
            'message' => [
                "topic" => $topic,
                "data" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "type" => (string)$data['type'],
                ],
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function rating_count($product_id, $rating)
    {
        return Review::where(['product_id' => $product_id, 'rating' => $rating])->count();
    }

    public static function dm_rating_count($deliveryman_id, $rating)
    {
        return DMReview::where(['delivery_man_id' => $deliveryman_id, 'rating' => $rating])->count();
    }

    public static function tax_calculate($product, $price)
    {
        if ($product['tax_type'] == 'percent') {
            $price_tax = ($price / 100) * $product['tax'];
        } else {
            $price_tax = $product['tax'];
        }
        return $price_tax;
    }

    public static function discount_calculate($product, $price)
    {
        if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return self::set_price($price_discount);
    }

    public static function category_discount_calculate($category_id, $price)
    {
        $category_discount = CategoryDiscount::active()->where(['category_id' => $category_id])->first();

        if ($category_discount) {
            if ($category_discount['discount_type'] == 'percent') {
                $price_discount = ($price / 100) * $category_discount['discount_amount'];
                if ($category_discount['maximum_amount'] < $price_discount) {
                    $price_discount = $category_discount['maximum_amount'];
                }
            } else {
                $price_discount = $category_discount['discount_amount'];
            }
        } else {
            $price_discount = 0;
        }
        return self::set_price($price_discount);
    }

    public static function max_earning()
    {
        $data = Order::where(['order_status' => 'delivered'])->select('id', 'created_at', 'order_amount')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += $order['order_amount'];
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function max_orders()
    {
        $data = Order::select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function order_status_update_message($status)
    {
        if ($status == 'pending') {
            $data = self::get_business_settings('order_pending_message');
        } elseif ($status == 'confirmed') {
            $data = self::get_business_settings('order_confirmation_msg');
        } elseif ($status == 'processing') {
            $data = self::get_business_settings('order_processing_message');
        } elseif ($status == 'out_for_delivery') {
            $data = self::get_business_settings('out_for_delivery_message');
        } elseif ($status == 'delivered') {
            $data = self::get_business_settings('order_delivered_message');
        } elseif ($status == 'delivery_boy_delivered') {
            $data = self::get_business_settings('delivery_boy_delivered_message');
        } elseif ($status == 'del_assign') {
            $data = self::get_business_settings('delivery_boy_assign_message');
        } elseif ($status == 'ord_start') {
            $data = self::get_business_settings('delivery_boy_start_message');
        } elseif ($status == 'returned') {
            $data = self::get_business_settings('returned_message');
        } elseif ($status == 'failed') {
            $data = self::get_business_settings('failed_message');
        } elseif ($status == 'canceled') {
            $data = self::get_business_settings('canceled_message');
        } elseif ($status == 'customer_notify_message') {
            $data = self::get_business_settings('customer_notify_message');
        } elseif ($status == 'deliveryman_order_processing') {
            $data = self::get_business_settings('deliveryman_order_processing_message');
        } elseif ($status == 'add_fund_wallet') {
            $data = self::get_business_settings('add_fund_wallet_message');
        } elseif ($status == 'add_fund_wallet_bonus') {
            $data = self::get_business_settings('add_fund_wallet_bonus_message');
        } else {
            $data = '{"status":"0","message":""}';
        }

        if ($data == null || $data['status'] == 0) {
            return 0;
        }
        return $data['message'];
    }

    public static function day_part()
    {
        $part = "";
        $morning_start = date("h:i:s", strtotime("5:00:00"));
        $afternoon_start = date("h:i:s", strtotime("12:01:00"));
        $evening_start = date("h:i:s", strtotime("17:01:00"));
        $evening_end = date("h:i:s", strtotime("21:00:00"));

        if (time() >= $morning_start && time() < $afternoon_start) {
            $part = "morning";
        } elseif (time() >= $afternoon_start && time() < $evening_start) {
            $part = "afternoon";
        } elseif (time() >= $evening_start && time() <= $evening_end) {
            $part = "evening";
        } else {
            $part = "night";
        }

        return $part;
    }

    public static function remove_dir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        Helpers::remove_dir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function get_language_name($key)
    {
        $languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian - shqip",
            "am" => "Amharic - አማርኛ",
            "ar" => "Arabic - العربية",
            "an" => "Aragonese - aragonés",
            "hy" => "Armenian - հայերեն",
            "ast" => "Asturian - asturianu",
            "az" => "Azerbaijani - azərbaycan dili",
            "eu" => "Basque - euskara",
            "be" => "Belarusian - беларуская",
            "bn" => "Bengali - বাংলা",
            "bs" => "Bosnian - bosanski",
            "br" => "Breton - brezhoneg",
            "bg" => "Bulgarian - български",
            "ca" => "Catalan - català",
            "ckb" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)",
            "zh" => "Chinese - 中文",
            "zh-HK" => "Chinese (Hong Kong) - 中文（香港）",
            "zh-CN" => "Chinese (Simplified) - 中文（简体）",
            "zh-TW" => "Chinese (Traditional) - 中文（繁體）",
            "co" => "Corsican",
            "hr" => "Croatian - hrvatski",
            "cs" => "Czech - čeština",
            "da" => "Danish - dansk",
            "nl" => "Dutch - Nederlands",
            "en" => "English",
            "en-AU" => "English (Australia)",
            "en-CA" => "English (Canada)",
            "en-IN" => "English (India)",
            "en-NZ" => "English (New Zealand)",
            "en-ZA" => "English (South Africa)",
            "en-GB" => "English (United Kingdom)",
            "en-US" => "English (United States)",
            "eo" => "Esperanto - esperanto",
            "et" => "Estonian - eesti",
            "fo" => "Faroese - føroyskt",
            "fil" => "Filipino",
            "fi" => "Finnish - suomi",
            "fr" => "French - français",
            "fr-CA" => "French (Canada) - français (Canada)",
            "fr-FR" => "French (France) - français (France)",
            "fr-CH" => "French (Switzerland) - français (Suisse)",
            "gl" => "Galician - galego",
            "ka" => "Georgian - ქართული",
            "de" => "German - Deutsch",
            "de-AT" => "German (Austria) - Deutsch (Österreich)",
            "de-DE" => "German (Germany) - Deutsch (Deutschland)",
            "de-LI" => "German (Liechtenstein) - Deutsch (Liechtenstein)",
            "de-CH" => "German (Switzerland) - Deutsch (Schweiz)",
            "el" => "Greek - Ελληνικά",
            "gn" => "Guarani",
            "gu" => "Gujarati - ગુજરાતી",
            "ha" => "Hausa",
            "haw" => "Hawaiian - ʻŌlelo Hawaiʻi",
            "he" => "Hebrew - עברית",
            "hi" => "Hindi - हिन्दी",
            "hu" => "Hungarian - magyar",
            "is" => "Icelandic - íslenska",
            "id" => "Indonesian - Indonesia",
            "ia" => "Interlingua",
            "ga" => "Irish - Gaeilge",
            "it" => "Italian - italiano",
            "it-IT" => "Italian (Italy) - italiano (Italia)",
            "it-CH" => "Italian (Switzerland) - italiano (Svizzera)",
            "ja" => "Japanese - 日本語",
            "kn" => "Kannada - ಕನ್ನಡ",
            "kk" => "Kazakh - қазақ тілі",
            "km" => "Khmer - ខ្មែរ",
            "ko" => "Korean - 한국어",
            "ku" => "Kurdish - Kurdî",
            "ky" => "Kyrgyz - кыргызча",
            "lo" => "Lao - ລາວ",
            "la" => "Latin",
            "lv" => "Latvian - latviešu",
            "ln" => "Lingala - lingála",
            "lt" => "Lithuanian - lietuvių",
            "mk" => "Macedonian - македонски",
            "ms" => "Malay - Bahasa Melayu",
            "ml" => "Malayalam - മലയാളം",
            "mt" => "Maltese - Malti",
            "mr" => "Marathi - मराठी",
            "mn" => "Mongolian - монгол",
            "ne" => "Nepali - नेपाली",
            "no" => "Norwegian - norsk",
            "nb" => "Norwegian Bokmål - norsk bokmål",
            "nn" => "Norwegian Nynorsk - nynorsk",
            "oc" => "Occitan",
            "or" => "Oriya - ଓଡ଼ିଆ",
            "om" => "Oromo - Oromoo",
            "ps" => "Pashto - پښتو",
            "fa" => "Persian - فارسی",
            "pl" => "Polish - polski",
            "pt" => "Portuguese - português",
            "pt-BR" => "Portuguese (Brazil) - português (Brasil)",
            "pt-PT" => "Portuguese (Portugal) - português (Portugal)",
            "pa" => "Punjabi - ਪੰਜਾਬੀ",
            "qu" => "Quechua",
            "ro" => "Romanian - română",
            "mo" => "Romanian (Moldova) - română (Moldova)",
            "rm" => "Romansh - rumantsch",
            "ru" => "Russian - русский",
            "gd" => "Scottish Gaelic",
            "sr" => "Serbian - српски",
            "sh" => "Serbo-Croatian - Srpskohrvatski",
            "sn" => "Shona - chiShona",
            "sd" => "Sindhi",
            "si" => "Sinhala - සිංහල",
            "sk" => "Slovak - slovenčina",
            "sl" => "Slovenian - slovenščina",
            "so" => "Somali - Soomaali",
            "st" => "Southern Sotho",
            "es" => "Spanish - español",
            "es-AR" => "Spanish (Argentina) - español (Argentina)",
            "es-419" => "Spanish (Latin America) - español (Latinoamérica)",
            "es-MX" => "Spanish (Mexico) - español (México)",
            "es-ES" => "Spanish (Spain) - español (España)",
            "es-US" => "Spanish (United States) - español (Estados Unidos)",
            "su" => "Sundanese",
            "sw" => "Swahili - Kiswahili",
            "sv" => "Swedish - svenska",
            "tg" => "Tajik - тоҷикӣ",
            "ta" => "Tamil - தமிழ்",
            "tt" => "Tatar",
            "te" => "Telugu - తెలుగు",
            "th" => "Thai - ไทย",
            "ti" => "Tigrinya - ትግርኛ",
            "to" => "Tongan - lea fakatonga",
            "tr" => "Turkish - Türkçe",
            "tk" => "Turkmen",
            "tw" => "Twi",
            "uk" => "Ukrainian - українська",
            "ur" => "Urdu - اردو",
            "ug" => "Uyghur",
            "uz" => "Uzbek - o‘zbek",
            "vi" => "Vietnamese - Tiếng Việt",
            "wa" => "Walloon - wa",
            "cy" => "Welsh - Cymraeg",
            "fy" => "Western Frisian",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba - Èdè Yorùbá",
            "zu" => "Zulu - isiZulu",
        );
        return array_key_exists($key, $languages) ? $languages[$key] : $key;
    }

    public static function get_default_language()
    {
        $data = self::get_business_settings('language');
        $default_lang = 'en';
        if ($data && array_key_exists('code', $data)) {
            foreach ($data as $lang) {
                if ($lang['default'] == true) {
                    $default_lang = $lang['code'];
                }
            }
        }

        return $default_lang;
    }

    public static function language_load()
    {
        if (\session()->has('language_settings')) {
            $language = \session('language_settings');
        } else {
            $language = BusinessSetting::where('key', 'language')->first();
            \session()->put('language_settings', $language);
        }
        return $language;
    }

    public static function fileUpload(string $dir, string $format, $file = null)
    {
        if ($file != null) {
            $fileName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            Storage::disk('public')->put($dir . $fileName, file_get_contents($file));
        } else {
            $fileName = 'def.png';
        }

        return $fileName;
    }

    public static function upload(string $dir, string $format = APPLICATION_IMAGE_FORMAT, array|object|null $image = null) {
        if (!$image) {
            return null;
        }

        set_time_limit(300);

        $dir = rtrim($dir, '/') . '/';
        $sourcePath = $image instanceof UploadedFile
            ? $image->getRealPath()
            : $image;

        $info = @getimagesize($sourcePath);
        if (!$info || empty($info['mime'])) {
            return false;
        }

        $mime = strtolower($info['mime']);

        // Detect format safely
        $format = match ($mime) {
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => $format,
        };

        $imageName = Carbon::now()->format('Y-m-d') . '-' . uniqid() . '.' . $format;

        // Ensure directory exists
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $savePath = storage_path("app/public/{$dir}{$imageName}");

        /**
         * 🚨 IMPORTANT
         * Never process GIF with GD (animation will break)
         */
        if ($mime === 'image/gif') {
            return copy($sourcePath, $savePath) ? $imageName : false;
        }

        /**
         * WEBP copy-only if already webp
         */
        if ($mime === 'image/webp' && $format === 'webp') {
            return copy($sourcePath, $savePath) ? $imageName : false;
        }

        /**
         * Create GD image
         */
        $gdImage = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png'  => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default      => false,
        };

        if (!$gdImage) {
            return false;
        }

        if (!imageistruecolor($gdImage)) {
            $trueColor = imagecreatetruecolor(imagesx($gdImage), imagesy($gdImage));

            // Preserve transparency
            imagealphablending($trueColor, false);
            imagesavealpha($trueColor, true);

            imagecopy(
                $trueColor,
                $gdImage,
                0,
                0,
                0,
                0,
                imagesx($gdImage),
                imagesy($gdImage)
            );

            imagedestroy($gdImage);
            $gdImage = $trueColor;
        }

        /**
         * Preserve transparency
         */
        if (in_array($mime, ['image/png', 'image/webp'])) {
            imagealphablending($gdImage, false);
            imagesavealpha($gdImage, true);
        }

        /**
         * Resize logic
         */
        $maxSize = 2500;
        $width   = imagesx($gdImage);
        $height  = imagesy($gdImage);

        if ($width > $maxSize || $height > $maxSize) {
            $ratio = min($maxSize / $width, $maxSize / $height);
            $newW  = (int)($width * $ratio);
            $newH  = (int)($height * $ratio);

            $temp = imagecreatetruecolor($newW, $newH);

            if (in_array($mime, ['image/png', 'image/webp'])) {
                imagealphablending($temp, false);
                imagesavealpha($temp, true);
            }

            imagecopyresampled(
                $temp,
                $gdImage,
                0,
                0,
                0,
                0,
                $newW,
                $newH,
                $width,
                $height
            );

            imagedestroy($gdImage);
            $gdImage = $temp;
        }

        /**
         * Save image
         */
        $saved = match ($format) {
            'jpg', 'jpeg' => imagejpeg($gdImage, $savePath, 85),
            'png'         => imagepng($gdImage, $savePath, -1),
            'webp'        => imagewebp($gdImage, $savePath, 78),
            default       => false,
        };

        imagedestroy($gdImage);

        return $saved ? $imageName : false;
    }

    public static function update(string $dir, $old_image, string $format, ?object $image = null)
    {
        if (Storage::disk('public')->exists($dir . $old_image)) {
            Storage::disk('public')->delete($dir . $old_image);
        }
        $imageName = Helpers::upload($dir, $format, $image);
        return $imageName;
    }

    public static function delete($full_path)
    {
        if (Storage::disk('public')->exists($full_path)) {
            Storage::disk('public')->delete($full_path);
        }
        return [
            'success' => 1,
            'message' => 'Removed successfully !'
        ];
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        if (is_bool(env($envKey))) {
            $oldValue = var_export(env($envKey), true);
        } else {
            $oldValue = env($envKey);
        }

        if (strpos($str, $envKey) !== false) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);
        } else {
            $str .= "{$envKey}={$envValue}\n";
        }
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }

    public static function requestSender($request): array
    {
        $remove = array("http://", "https://", "www.");
        $url = str_replace($remove, "", url('/'));

        $post = [
            base64_decode('dXNlcm5hbWU=') => $request['username'], //un
            base64_decode('cHVyY2hhc2Vfa2V5') => $request['purchase_key'], //pk
            base64_decode('c29mdHdhcmVfaWQ=') => base64_decode(env(base64_decode('U09GVFdBUkVfSUQ='))), //sid
            base64_decode('ZG9tYWlu') => $url,
        ];

        //session()->put('domain', 'https://' . preg_replace("#^[^:/.]*[:/]+#i", "", $request['domain']));

        $ch = curl_init('https://check.6amtech.com/api/v1/domain-register');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);

        try {
            if (base64_decode(json_decode($response, true)['active'])) {
                return [
                    'active' => (int)base64_decode(json_decode($response, true)['active'])
                ];
            }
            return [
                'active' => 0
            ];
        } catch (\Exception $exception) {
            return [
                'active' => 1
            ];
        }
    }

    public static function getPagination()
    {
        $pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
        return $pagination_limit->value;
    }

    public static function remove_invalid_charcaters($str)
    {
        return str_ireplace(['"', ',', ';', '<', '>', '?'], ' ', $str);
    }

    public static function get_delivery_charge($branchId, ?float $distance = null, int|string|null $selectedDeliveryArea = null)
    {
        $branch = Branch::with(['delivery_charge_setup', 'delivery_charge_by_area'])
            ->where(['id' => $branchId])
            ->first(['id', 'name', 'status']);

        $deliveryType = $branch->delivery_charge_setup->delivery_charge_type ?? 'fixed';
        $deliveryType = $deliveryType === 'area' ? 'area' : ($deliveryType === 'distance' ? 'distance' : 'fixed');

        if ($deliveryType == 'area') {
            $area = DeliveryChargeByArea::find($selectedDeliveryArea);
            $deliveryCharge = $area->delivery_charge ?? 0;
        } elseif ($deliveryType == 'distance') {
            $minDeliveryCharge = $branch->delivery_charge_setup->minimum_delivery_charge;
            $shippingChargePerKM = $branch->delivery_charge_setup->delivery_charge_per_kilometer;
            $minDistanceForFreeDelivery = $branch->delivery_charge_setup->minimum_distance_for_free_delivery;

            if ($distance < $minDistanceForFreeDelivery) {
                $deliveryCharge = 0;
            } else {
                $distanceDeliveryCharge = $shippingChargePerKM * $distance;
                $deliveryCharge = max($distanceDeliveryCharge, $minDeliveryCharge);
            }
        } else {
            $deliveryCharge = $branch->delivery_charge_setup->fixed_delivery_charge ?? 0;
        }

        return self::set_price($deliveryCharge);
    }

    public static function module_permission_check($mod_name)
    {
        $permission = auth('admin')->user()->role->module_access;

        if (isset($permission) && in_array($mod_name, (array)json_decode($permission)) == true) {
            return true;
        }

        if (auth('admin')->user()->admin_role_id == 1) {
            return true;
        }
        return false;
    }

    public static function file_remover(string $dir, $image)
    {
        if (!isset($image)) return true;

        if (Storage::disk('public')->exists($dir . $image)) Storage::disk('public')->delete($dir . $image);

        return true;
    }

    public static function generate_referer_code()
    {
        $ref_code = Str::random('20');
        if (User::where('referral_code', '=', $ref_code)->exists()) {
            return generate_referer_code();
        }
        return $ref_code;
    }

    public static function gen_mpdf($view, $file_prefix, $file_postfix)
    {
        $mpdf = new \Mpdf\Mpdf(['default_font' => 'FreeSerif', 'mode' => 'utf-8', 'format' => [190, 250]]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
        $mpdf->WriteHTML($mpdf_view);
        $mpdf->Output($file_prefix . $file_postfix . '.pdf', 'D');
    }

    public static function text_variable_data_format(string $value, ?string $user_name = null, ?string $store_name = null, ?string $delivery_man_name = null, int|string|null $transaction_id = null, int|string|null $order_id = null): string
    {
        $data = $value;
        if ($value) {
            if ($user_name) {
                $data =  str_replace("{userName}", $user_name, $data);
            }

            if ($store_name) {
                $data =  str_replace("{storeName}", $store_name, $data);
            }

            if ($delivery_man_name) {
                $data =  str_replace("{deliveryManName}", $delivery_man_name, $data);
            }

            if ($order_id) {
                $data =  str_replace("{orderId}", $order_id, $data);
            }
        }
        return $data;
    }

    public static function order_status_message_key($status)
    {
        if ($status == 'pending') {
            $data = 'order_pending_message';
        } elseif ($status == 'confirmed') {
            $data = 'order_confirmation_msg';
        } elseif ($status == 'processing') {
            $data = 'order_processing_message';
        } elseif ($status == 'out_for_delivery') {
            $data = 'out_for_delivery_message';
        } elseif ($status == 'delivered') {
            $data = 'order_delivered_message';
        } elseif ($status == 'delivery_boy_delivered') {
            $data = 'delivery_boy_delivered_message';
        } elseif ($status == 'del_assign') {
            $data = 'delivery_boy_assign_message';
        } elseif ($status == 'ord_start') {
            $data = 'delivery_boy_start_message';
        } elseif ($status == 'returned') {
            $data = 'returned_message';
        } elseif ($status == 'failed') {
            $data = 'failed_message';
        } elseif ($status == 'canceled') {
            $data = 'canceled_message';
        } elseif ($status == 'customer_notify_message') {
            $data = 'customer_notify_message';
        } elseif ($status == 'deliveryman_order_processing') {
            $data = 'deliveryman_order_processing_message';
        } elseif ($status == 'add_fund_wallet') {
            $data = 'add_fund_wallet_message';
        } elseif ($status == 'add_fund_wallet_bonus') {
            $data = 'add_fund_wallet_bonus_message';
        } else {
            $data = $status;
        }

        return $data;
    }

    public static function onErrorImage($data, $src, $error_src, $path)
    {
        if (isset($data) && strlen($data) > 1 && Storage::disk('public')->exists($path . $data)) {
            return $src;
        }
        return $error_src;
    }

    /**
     * @param $branchId
     * @param $weight
     * @return float|int
     */
    public static function productWeightChargeCalculation($branchId, $weight): float|int
    {
        if ($weight <= 0 || !isset($branchId)) {
            return 0;
        }

        $branch = Branch::with(['weight_settings_status', 'weight_charge_type', 'weight_unit', 'weight_range'])
            ->where(['id' => $branchId])
            ->first(['id', 'name', 'status']);

        if (!$branch) {
            return 0;
        }

        $delivery_weight_settings_status = $branch->weight_settings_status->value ?? 0;
        $delivery_weight_charge_type = $branch->weight_charge_type->value ?? '';
        $delivery_count_charge_from = collect($branch->weight_unit)->firstWhere('key', 'count_charge_from')['value'] ?? 0;
        $delivery_additional_charge_per_unit = collect($branch->weight_unit)->firstWhere('key', 'additional_charge_per_unit')['value'] ?? 0;
        $delivery_count_charge_from_operation = collect($branch->weight_unit)->firstWhere('key', 'count_charge_from_operation')['value'] ?? '';
        $delivery_weight_range = $branch->weight_range ? json_decode($branch->weight_range->value, true) : [];

        if ($delivery_weight_settings_status == 0) {
            return 0;
        }

        if ($delivery_weight_charge_type == 'unit') {
            if ($delivery_count_charge_from_operation == 'greater' && $delivery_count_charge_from < $weight) {
                $chargeableWeight = max(0, $weight - $delivery_count_charge_from);
                return $delivery_additional_charge_per_unit * $chargeableWeight;
            } elseif ($delivery_count_charge_from_operation == 'greater_or_equal' && $delivery_count_charge_from <= $weight) {
                $chargeableWeight = max(0, $weight - $delivery_count_charge_from);
                $chargeableWeight = $chargeableWeight + 1;
                return $delivery_additional_charge_per_unit * $chargeableWeight;
            } else {
                return 0;
            }
        }

        if ($delivery_weight_charge_type == 'range') {
            foreach ($delivery_weight_range as $range) {
                $minWeight = (float)$range['min_weight'];
                $maxWeight = (float)$range['max_weight'];
                $minOperation = $range['min_operation'];
                $maxOperation = $range['max_operation'];
                $deliveryCharge = (float)$range['delivery_charge'];

                $minConditionMet = false;
                $maxConditionMet = false;

                if ($minOperation == 'greater' && $weight > $minWeight) {
                    $minConditionMet = true;
                } elseif ($minOperation == 'greater_or_equal' && $weight >= $minWeight) {
                    $minConditionMet = true;
                }

                if ($maxOperation == 'less' && $weight < $maxWeight) {
                    $maxConditionMet = true;
                } elseif ($maxOperation == 'less_or_equal' && $weight <= $maxWeight) {
                    $maxConditionMet = true;
                }

                if ($minConditionMet && $maxConditionMet) {
                    return $deliveryCharge;
                }
            }
        }
        return 0;
    }


    public static function paginateValueNumberOptions(?int $custom = null): array
    {
        $allowedNumberOptions = [5, 10, 20, 30, 40, 50, 100, (int) Helpers::getPagination()];

        if ($custom) {
            $allowedNumberOptions[] = (int) $custom;
        }

        $uniqueAllowedNumberOptions = array_unique($allowedNumberOptions);
        sort($uniqueAllowedNumberOptions);

        return $uniqueAllowedNumberOptions;
    }

    public static function getDateRange($request)
    {
        if (is_array($request)) {
            return [
                'start' => Carbon::parse($request['start'])->startOfDay(),
                'end' => Carbon::parse($request['end'])->endOfDay(),
            ];
        }

        return match ($request) {
            TODAY => [
                'start' => Carbon::parse(now())->startOfDay(),
                'end' => Carbon::parse(now())->endOfDay()
            ],
            PREVIOUS_DAY => [
                'start' => Carbon::yesterday()->startOfDay(),
                'end' => Carbon::yesterday()->endOfDay(),
            ],
            THIS_WEEK => [
                'start' => Carbon::parse(now())->startOfWeek(Carbon::SUNDAY),
                'end' => Carbon::parse(now())->endOfWeek(Carbon::SATURDAY),
            ],
            THIS_MONTH => [
                'start' => Carbon::parse(now())->startOfMonth(),
                'end' => Carbon::parse(now())->endOfMonth(),
            ],
            LAST_7_DAYS => [
                'start' => Carbon::today()->subDays(7)->startOfDay(),
                'end' => Carbon::parse(now())->endOfDay(),
            ],
            LAST_WEEK => [
                'start' => Carbon::now()->subWeek()->startOfWeek(),
                'end' => Carbon::now()->subWeek()->endOfWeek(),
            ],
            LAST_MONTH => [
                'start' => Carbon::now()->subMonth()->startOfMonth(),
                'end' => Carbon::now()->subMonth()->endOfMonth(),
            ],
            THIS_YEAR => [
                'start' => Carbon::now()->startOfYear(),
                'end' => Carbon::now()->endOfYear(),
            ],
            ALL_TIME => [
                'start' => Carbon::parse(BUSINESS_START_DATE),
                'end' => Carbon::now(),
            ]
        };
    }

    public static function getDateRangeType($date) {
        $start = $date['start']->copy()->startOfDay();
        $end   = $date['end']->copy()->endOfDay();
        // Week boundaries (Sunday → Saturday)
        $weekStart = $start->copy()->startOfWeek(Carbon::SUNDAY);
        $weekEnd   = $start->copy()->endOfWeek(Carbon::SATURDAY);

        // Same day
        if ($start->isSameDay($end)) {
            return "day";
        }
        // Same week (any range within the same week)
        elseif ($start->greaterThanOrEqualTo($weekStart) && $end->lessThanOrEqualTo($weekEnd)) {
            return "week";
        }
        // Full month
        // Month (any range within the same month & year)
        elseif ($start->month === $end->month && $start->year === $end->year) {
            return "month";
        }

        // Full year
        elseif (
            $start->isSameDay($start->copy()->startOfYear()) &&
            $end->isSameDay($end->copy()->endOfYear())
        ) {
            return "year";
        }

        // Otherwise
        return "all_time";
    }

    public static function checkExtraDiscount($subtotal)
    {
        if (session()->has('extra_discount') && session()->has('extra_discount_type')) {
            $extraDiscount = session('extra_discount');
            $extraDiscountType = session('extra_discount_type');
            $discountAmount = $extraDiscountType === 'percent'
                ? ($subtotal * $extraDiscount) / 100
                : $extraDiscount;

            if ($discountAmount > $subtotal) {
                session()->forget('extra_discount');
                session()->forget('extra_discount_type');

                return 'Extra discount removed';
            }
        }

        return null;
    }

    public static function order_status_card_style(?string $status)
    {
        $key = strtolower(trim($status ?? ''));

        $map = [
            'ongoing' => [
                'bg' => 'bg--1',
                'text_class' => 'text-info',
                'text_color' => '#3C76F1',
                'circle_colors' => ['#a5e1cb', '#3C76F1'],
            ],
            'delivered' => [
                'bg' => 'bg--2',
                'text_class' => 'text-success',
                'text_color' => '#14cc60',
                'circle_colors' => ['#d4f8e6', '#14cc60'],
            ],
            'failed' => [
                'bg' => 'bg--4',
                'text_class' => 'text-danger',
                'text_color' => '#ff5f5f',
                'circle_colors' => ['#ffdede', '#ff5f5f'],
            ],
            'returned' => [
                'bg' => 'bg--3',
                'text_class' => 'text-warning',
                'text_color' => '#ffb21d',
                'circle_colors' => ['#fff2d6', '#ffb21d'],
            ],
            'cancelled' => [
                'bg' => 'bg-soft-danger',
                'text_class' => 'text-secondary',
                'text_color' => '#6c757d',
                'circle_colors' => ['#eeeeee', '#6c757d'],
            ],
        ];

        $default = [
            'bg' => 'bg-opacity-light-10',
            'text_class' => 'text-muted',
            'text_color' => '#6c757d',
            'circle_colors' => ['#f0f0f0', '#6c757d'],
        ];

        return $map[$key] ?? $default;
    }

    public static function formatCurrency($number, $currency = '$') {
    if ($number >= 1_000_000_000) {
        $formatted = number_format($number / 1_000_000_000, 1) . 'B';
    } elseif ($number >= 1_000_000) {
        $formatted = number_format($number / 1_000_000, 1) . 'M';
    } elseif ($number >= 1_000) {
        $formatted = number_format($number / 1_000, 1) . 'K';
    } else {
        $formatted = number_format($number, 2);
    }

    return $currency . $formatted;
}

public static function readableUploadMaxFileSize($fileType)
{
    $uploadMaxFileSize = uploadMaxFileSize($fileType);

    return  convertToReadableSize($uploadMaxFileSize);
}

public static function convertStringAmountToNumber($amount)
{
    $decimal_point_settings = Helpers::get_business_settings('decimal_point_settings');

    return (float)number_format($amount, $decimal_point_settings);
}
}


function translate($key)
{
    $local = session()->has('local') ? session('local') : 'en';
    App::setLocale($local);

    try {
        $lang_array = include(base_path('resources/lang/' . $local . '/messages.php'));
        $processed_key = ucfirst(str_replace('_', ' ', Helpers::remove_invalid_charcaters($key)));

        if (!array_key_exists($key, $lang_array)) {
            $lang_array[$key] = $processed_key;
            $str = "<?php return " . var_export($lang_array, true) . ";";
            file_put_contents(base_path('resources/lang/' . $local . '/messages.php'), $str);
            $result = $processed_key;
        } else {
            $result = __('messages.' . $key);
        }
    } catch (Exception $exception) {
        $result = __('messages.' . $key);
    }

    return $result;
}



function convertToBytes($value)
{
    $value = trim($value);
    $last = strtolower($value[strlen($value) - 1]);
    $num = (int) $value;

    switch ($last) {
        case 'g':
            $num *= 1024;
        case 'm':
            $num *= 1024;
        case 'k':
            $num *= 1024;
    }

    return $num;
}

function convertToReadableSize($bytes)
{
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824) . 'GB';
    } elseif ($bytes >= 1048576) {
        return round($bytes / 1048576) . 'MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024) . 'KB';
    } else {
        return $bytes . 'B';
    }
}

function uploadMaxFileSize($fileType)
{
    $uploadMaxFileSize = convertToBytes(ini_get('upload_max_filesize'));
    $uploadMaxFileSize = $uploadMaxFileSize > convertToBytes(($fileType == 'image') ? '20M' : '50M') ? convertToBytes(($fileType == 'image') ? '20M' : '50M') : $uploadMaxFileSize;

    return $uploadMaxFileSize;
}
