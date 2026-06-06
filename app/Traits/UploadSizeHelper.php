<?php

namespace App\Traits;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use function App\CentralLogics\convertToBytes;
use function App\CentralLogics\convertToReadableSize;
use function App\CentralLogics\uploadMaxFileSize;

trait UploadSizeHelper
{
    protected $maxImageSizeBytes;
    protected $maxImageSizeKB;
    protected $maxImageSizeReadable;

    public function initUploadLimits(string $fileType = 'image')
    {
        $this->maxImageSizeBytes = uploadMaxFileSize($fileType);
        $this->maxImageSizeKB = $this->maxImageSizeBytes / 1024;
        $this->maxImageSizeReadable = convertToReadableSize($this->maxImageSizeBytes);
    }

    protected function validateUploadedFile(Request $request, array $fieldNames, string $fileType = 'image')
    {
        $this->initUploadLimits($fileType);

        foreach ($fieldNames as $fieldName) {

            if (!isset($_FILES[$fieldName])) {
                continue;
            }

            $files = $request->file($fieldName);
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                if (!$file) {
                    continue;
                }
                if ($file->getError() === UPLOAD_ERR_INI_SIZE) {
                    $message = \App\CentralLogics\translate(
                        $fieldName . ' size must be less than ' . $this->maxImageSizeReadable
                    );
                    return $this->uploadErrorResponse($request, $message, $fieldName);
                }
            }
        }

        return true;
    }


    private function uploadErrorResponse(Request $request, string $message, $fieldName)
    {
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'errors' => [['code' => $fieldName, 'message' => $message]]
            ]);
        }

        Toastr::error($message);
        return back();
    }


}
