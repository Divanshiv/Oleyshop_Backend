<?php

namespace App\Http\Controllers\Admin\System;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AddonController extends Controller
{
    /**
     * @param string $path
     * @return array
     */
    function getDirectories(string $path): array
    {
        $module_dir = base_path('Modules');

        try {
            if (!File::exists($module_dir)) {
                File::makeDirectory($module_dir);
                File::chmod($module_dir, 0777);
            }
        } catch (Exception $e) {

        }
        $directories = [];
        $path = base_path($path);
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item == '..' || $item == '.')
                continue;
            if (is_dir($path . '/' . $item))
                $directories[] = $item;
        }
        return $directories;
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $dir = 'Modules';
        $directories = self::getDirectories($dir);

        $addons = [];
        foreach ($directories as $directory) {
            $subDirectory = self::getDirectories('Modules/' . $directory);
            if (in_array('Addon', $subDirectory)) {
                $addons[] = 'Modules/' . $directory;
            }
        }
        return view('admin-views.system.addon.index', compact('addons'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file_upload' => 'required|mimes:zip'
        ]);

        if ($validator->errors()->count() > 0) {
            $error = Helpers::error_processor($validator);
            return response()->json(['status' => 'error', 'message' => $error[0]['message']]);
        }

        $file = $request->file('file_upload');
        $filename = $file->getClientOriginalName();
        $tempPath = $file->storeAs('temp', $filename);
        $zip = new \ZipArchive();


        if (File::exists(base_path('Modules/') . explode('.', $filename)[0])) {
            $status = 'error';
            $message = translate('already_installed');
        } else {
            if ($zip->open(storage_path('app/' . $tempPath)) === TRUE) {
                $extractPath = base_path('Modules/');

                $zip->extractTo($extractPath);
                $zip->close();
                if (File::exists($extractPath . '/' . explode('.', $filename)[0] . '/Addon/info.php')) {
                    File::chmod($extractPath . '/' . explode('.', $filename)[0] . '/Addon', 0777);
                    Toastr::success(translate('file_upload_successfully!'));
                    $status = 'success';
                    $message = translate('file_upload_successfully!');
                } else {
                    File::deleteDirectory($extractPath . '/' . explode('.', $filename)[0]);
                    $status = 'error';
                    $message = translate('invalid_file!');
                }
            } else {
                $status = 'error';
                $message = translate('file_upload_fail!');
            }
        }

        Storage::delete($tempPath);

        return response()->json([
            'status' => $status,
            'message'=> $message
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function publish(Request $request): JsonResponse
    {
        $fullData = include($request['path'] . '/Addon/info.php');
        $path = $request['path'];
        $addonName = $fullData['name'];

        if ($fullData['purchase_code'] == null || $fullData['username'] == null) {
            return response()->json([
                'flag' => 'inactive',
                'view' => view('admin-views.system.addon.partials.activation-modal-data', compact('fullData', 'path', 'addonName'))->render(),
            ]);
        }
        $fullData['is_published'] = $fullData['is_published'] ? 0 : 1;

        $str = "<?php return " . var_export($fullData, true) . ";";
        file_put_contents(base_path($request['path'] . '/Addon/info.php'), $str);

        return response()->json([
            'status' => 'success',
            'message'=> 'status_updated_successfully'
        ]);
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function activation(Request $request): Redirector|RedirectResponse|Application
    {
        $remove = ["http://", "https://", "www."];
        $url = str_replace($remove, "", url('/'));
        $fullData = include($request['path'] . '/Addon/info.php');

        $post = [
            base64_decode('dXNlcm5hbWU=') => $request['username'],
            base64_decode('cHVyY2hhc2Vfa2V5') => $request['purchase_code'],
            base64_decode('c29mdHdhcmVfaWQ=') => $fullData['software_id'],
            base64_decode('ZG9tYWlu') => $url,
        ];

        $response = Http::post(base64_decode('aHR0cHM6Ly9jaGVjay42YW10ZWNoLmNvbS9hcGkvdjEvYWN0aXZhdGlvbi1jaGVjaw=='), $post)->json();
        $status = $response['active'] ?? base64_encode(1);

        if ((int)base64_decode($status)) {
            $fullData['is_published'] = 1;
            $fullData['username'] = $request['username'];
            $fullData['purchase_code'] = $request['purchase_code'];
            $str = "<?php return " . var_export($fullData, true) . ";";
            file_put_contents(base_path($request['path'] . '/Addon/info.php'), $str);

            Toastr::success(\App\CentralLogics\translate('activated_successfully'));
            return back();
        }

        $activationUrl = base64_decode('aHR0cHM6Ly9hY3RpdmF0aW9uLjZhbXRlY2guY29t');
        $activationUrl .= '?username=' . $request['username'];
        $activationUrl .= '&purchase_code=' . $request['purchase_code'];
        $activationUrl .= '&domain=' . url('/') . '&';

        return redirect($activationUrl);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAddon(Request $request): JsonResponse
    {
        $path = $request->path;
        $fullPath = base_path($path);

        if(File::deleteDirectory($fullPath)){
            $paymentTrait = base_path('app/Traits/Payment.php');
            $paymentTraitTextFile = base_path('app/Traits/Payment.txt');
            copy($paymentTraitTextFile, $paymentTrait);

            return response()->json([
                'status' => 'success',
                'message'=> translate('file_delete_successfully')
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'message'=> translate('file_delete_fail')
            ]);
        }
    }

}
