<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\CentralLogics\helpers;
use Illuminate\Support\Facades\DB;
use App\Model\BusinessSetting;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LanguageController extends Controller
{
    public function __construct(
        private BusinessSetting $businessSetting
    ){}

    /**
     * @return Factory|View|Application
     */
    public function index(): View|Factory|Application
    {
        $languages = json_decode(
            BusinessSetting::where('key', 'language')->value('value') ?? '[]',
            true
        );
        return view('admin-views.business-settings.language.index', compact('languages'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|void
     */
    public function store(Request $request)
    {
        $languages = BusinessSetting::where('key', 'language')->first();
        $language = json_decode($languages->value, true);
        if(!isset($language)) {
            $this->InsertOrUpdateBusinessData(['key' => 'language'], [
                'value' => '[{"id":"1","name":"english","direction":"ltr","code":"en","status":1,"default":true}]'
            ]);
            $language = Helpers::get_business_settings('language');
        }
        $languageArray = [];
        $codes = [];
        foreach ($language as $key => $data) {
            if ($data['code'] != $request['code']) {
                if (!array_key_exists('default', $data)) {
                    $default = array('default' => $data['code'] == 'en');
                    $data = array_merge($data, $default);
                }
                $languageArray[] = $data;
                $codes[] = $data['code'];
            }
        }
        $codes[] = $request['code'];

        if (!file_exists(base_path('resources/lang/' . $request['code']))) {
            mkdir(base_path('resources/lang/' . $request['code']), 0777, true);
        }

        $languageFile = fopen(base_path('resources/lang/' . $request['code'] . '/' . 'messages.php'), "w") or die("Unable to open file!");
        $read = file_get_contents(base_path('resources/lang/en/messages.php'));
        fwrite($languageFile, $read);

        $languageArray[] = [
            'id' => count($language) + 1,
            'name' => $request['name'],
            'code' => $request['code'],
            'direction' => 'ltr',
            'status' => 0,
            'default' => false,
        ];

        $this->InsertOrUpdateBusinessData(['key' => 'language'], [
            'value' => $languageArray
        ]);

        Toastr::success(translate('Language Added!'));
        return back();
    }

    public function updateStatus(Request $request)
    {
        $languages = BusinessSetting::where('key', 'language')->first();
        $language = json_decode($languages->value, true);
        $languageArray = [];
        foreach ($language as $key => $data) {
            if ($data['code'] == $request['code']) {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'] == 1 ? 0 : 1,
                    'default' => (array_key_exists('default', $data) ? $data['default'] : $data['code'] == 'en'),
                ];
            } else {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'],
                    'default' => (array_key_exists('default', $data) ? $data['default'] : $data['code'] == 'en'),
                ];
            }
            $languageArray[] = $lang;
        }

         $this->InsertOrUpdateBusinessData(['key' => 'language'], [
            'value' => $languageArray
        ]);

        $businessSetting = Helpers::get_business_settings('language');
        return $businessSetting ;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateDefaultStatus(Request $request): RedirectResponse
    {
        $languages = BusinessSetting::where('key', 'language')->first();
        $language = json_decode($languages->value, true);
        $languageArray = [];
        foreach ($language as $key => $data) {
            if ($data['code'] == $request['code']) {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => $data['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => 1,
                    'default' => true,
                ];
            } else {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => $data['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'],
                    'default' => false,
                ];
            }
            $languageArray[] = $lang;
        }
        $this->businessSetting->where('key', 'language')->update([
            'value' => $languageArray
        ]);

        Toastr::success(translate('Default Language Changed!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $languages = BusinessSetting::where('key', 'language')->first();
        $language = json_decode($languages->value, true);

        $languageArray = [];
        foreach ($language as $key => $data) {
            if ($data['code'] == $request['code']) {
                $lang = [
                    'id' => $data['id'],
                    'name' => $request['name'],
                    'direction' => $request['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'] ?? 0,
                    'default' => (array_key_exists('default', $data) ? $data['default'] : $data['code'] == 'en'),
                ];
            } else {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => $data['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'],
                    'default' => (array_key_exists('default', $data) ? $data['default'] : $data['code'] == 'en'),
                ];
            }
            $languageArray[] = $lang;
        }

        $this->InsertOrUpdateBusinessData(['key' => 'language'], [
            'value' => $languageArray
        ]);

        Toastr::success(translate('Language updated!'));
        return back();
    }

    /**
     * @param $lang
     * @return Factory|View|Application
     */
    public function translate($lang): View|Factory|Application
    {
        $fullData = include(base_path('resources/lang/' . $lang . '/messages.php'));
        $lang_data = [];
        ksort($fullData);
        foreach ($fullData as $key => $data) {
            $lang_data[] = ['key' => $key, 'value' => $data];
        }
        return view('admin-views.business-settings.language.translate', compact('lang', 'lang_data'));
    }

    /**
     * @param Request $request
     * @param $lang
     * @return void
     */
    public function translateKeyRemove(Request $request, $lang): void
    {
        $fullData = include(base_path('resources/lang/' . $lang . '/messages.php'));
        unset($fullData[$request['key']]);
        $str = "<?php return " . var_export($fullData, true) . ";";
        file_put_contents(base_path('resources/lang/' . $lang . '/messages.php'), $str);
    }

    /**
     * @param Request $request
     * @param $lang
     * @return void
     */
    public function translateSubmit(Request $request, $lang): void
    {
        $fullData = include(base_path('resources/lang/' . $lang . '/messages.php'));
        $fullData[urldecode($request['key'])] = $request['value'];
        $str = "<?php return " . var_export($fullData, true) . ";";
        file_put_contents(base_path('resources/lang/' . $lang . '/messages.php'), $str);
    }

    /**
     * @param $lang
     * @return RedirectResponse
     */
    public function delete($lang): RedirectResponse
    {
        $languages = BusinessSetting::where('key', 'language')->first();
        $language = json_decode($languages->value, true);

        $del_default = false;
        foreach ($language as $key => $data) {
            if (
                $data['code'] == $lang &&
                array_key_exists('default', $data) &&
                $data['default']
            ) {
                $del_default = true;
            }
        }

        // Rebuild language list
        $languageArray = [];
        foreach ($language as $data) {
            if ($data['code'] != $lang) {
                $langData = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => $data['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => ($del_default && $data['code'] == 'en') ? 1 : $data['status'],
                    'default' => ($del_default && $data['code'] == 'en')
                        ? true
                        : (array_key_exists('default', $data)
                            ? $data['default']
                            : (($data['code'] == 'en') ? true : false)),
                ];
                $languageArray[] = $langData;
            }
        }

        // Update language data
        $this->InsertOrUpdateBusinessData(['key' => 'language'], [
            'value' => $languageArray
        ]);

        // Delete language directory safely
        $dir = base_path('resources/lang/' . $lang);

        try {
            if (is_dir($dir)) {
                $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

                foreach ($files as $file) {
                    if ($file->isDir()) {
                        @rmdir($file->getRealPath());
                    } else {
                        @unlink($file->getRealPath());
                    }
                }

                @rmdir($dir);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to delete language directory ({$lang}): " . $e->getMessage());
        }

        Toastr::success(translate('Removed Successfully!'));
        return back();
    }

    /**
     * @param $local
     * @return RedirectResponse
     */
    public function lang($local): \Illuminate\Http\RedirectResponse
    {
        $direction = 'ltr';
        $languages = BusinessSetting::where('key', 'language')->first();
        $language = json_decode($languages->value, true);
        foreach ($language as $key => $data) {
            if ($data['code'] == $local) {
                $direction = $data['direction'] ?? 'ltr';
            }
        }
        session()->forget('language_settings');
        Helpers::language_load();
        session()->put('local', $local);
        Session::put('direction', $direction);
        return redirect()->back();
    }

    private function InsertOrUpdateBusinessData($key, $value)
    {
        $businessSetting = $this->businessSetting->where(['key' => $key['key']])->first();
        if ($businessSetting) {
            $businessSetting->value = $value['value'];
            $businessSetting->save();
        } else {
            $this->businessSetting->create($key, $value);
        }
    }
}
