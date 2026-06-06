<?php

namespace Modules\AI\app\Http\Requests;

use App\Traits\UploadSizeHelper;
use Illuminate\Foundation\Http\FormRequest;

class GenerateTitleFromImageRequest extends FormRequest
{
    use UploadSizeHelper;

    public function __construct()
    {
        $this->initUploadLimits();
    }


    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {

        return [
            'image' => 'required|image|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')) .'|max:' . $this->maxImageSizeKB,
        ];
    }

    public function messages(): array{
        return [
            'image.required' => translate('Image is required for analysis.'),
            'image.image' => translate('The uploaded file must be an image.'),
            'image.mimes' => translate('Only JPEG, PNG, JPG, and GIF images are allowed.'),
            'image.max' => translate('Image size must not exceed .' . $this->maxImageSizeReadable),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
