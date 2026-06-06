@php use App\CentralLogics\Helpers; @endphp
<div class="card card-body mb-3">
    <h3 class="mb-3">{{ translate('Background_Image') }}</h3>
    <div class="bg-section rounded-10 p-3 p-sm-4">
        <div class="d-flex flex-column gap-4">
            <div class="upload-file">
                <input type="file" name="background_image" class="upload-file__input single_file_input"
                       accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                       value=""
                       data-maxFileSize="{{ Helpers::readableUploadMaxFileSize('image') }}">
                <label class="upload-file__wrapper ratio-7-1">
                    <div class="upload-file-textbox text-center">
                                <span class="text-primary fs-16">
                                    <i class="tio-camera-enhance"></i>
                                </span>
                        <h6 class="fs-10 mt-1 fw-medium lh-base text-center text-body">
                            {{ translate('Add_image') }}
                        </h6>
                    </div>
                    @php
                        $value = json_decode($data['value'], true);
                        $backgroundImage = $value['background_image'] ?? null;
                        $imagePath = $backgroundImage ? 'storage/app/public/business-settings/page-setup/' . $backgroundImage : null;
                    @endphp

                    @if($backgroundImage && file_exists(base_path($imagePath)))
                        <img class="upload-file-img" loading="lazy"
                             src="{{ asset($imagePath) }}"
                             data-default-src=""
                             alt="">
                    @else
                        <img class="upload-file-img" loading="lazy" src=""
                             data-default-src=""
                             alt="">
                    @endif
                </label>
                <div class="overlay">
                    <div class="d-flex gap-10px justify-content-center align-items-center h-100">
                        <button type="button" class="btn btn-danger text-danger bg-white icon-btn remove_btn">
                            <i class="tio-delete-outlined"></i>
                        </button>
                        <button type="button" class="btn btn-info text-info bg-white icon-btn view_btn">
                            <i class="tio-visible-outlined"></i>
                        </button>
                        <button type="button" class="btn btn-info text-info bg-white icon-btn edit_btn">
                            <i class="tio-edit"></i>
                        </button>
                    </div>
                </div>
            </div>
            <p class="fs-10 m-0 text-center">
                {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} : Max {{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}
                <span class="text-dark font-semibold">(7:1)</span>
            </p>
        </div>
    </div>
</div>

<input type="hidden" name="existing_background_image"
       value="{{ $backgroundImage && file_exists(base_path($imagePath)) ? json_decode($data['value'], true)['background_image'] :  null }}">
