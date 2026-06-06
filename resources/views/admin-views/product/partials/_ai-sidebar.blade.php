{{-- Foating AI Button --}}
<div class="floating-ai-button">
    <button type="button" class="btn btn-lg rounded-circle shadow-lg" data-toggle="modal"
        data-target="#aiAssistantModal" data-action="main" title="AI Assistant">
        <span class="ai-btn-animation">
            <span class="gradientCirc"></span>
        </span>
        <span class="position-relative z-1 text-white d-flex flex-column gap-1 align-items-center">
            <img width="16" height="17" src="{{ asset('public/assets/admin/img/ai/hexa-ai.svg') }}"
                alt="">
            <span class="fs-12 fw-semibold">{{ translate('Use_AI') }}</span>
        </span>
    </button>
    <div class="ai-tooltip">
        <span>{{translate("AI_Assistant")}}</span>
    </div>
</div>

<!-- AI Assistant Modal -->
<div class="modal fade p-0" id="aiAssistantModal" tabindex="-1" aria-labelledby="aiAssistantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-slideInRight modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex gap-2 aign-items-center justify-content-between">
                <h5 class="modal-title d-flex align-items-center gap-2 aiAssistantModalLabel" id="aiAssistantModalLabel">
                    <span class="square-div">
                        <span class="ai-btn-animation">
                            <span class="gradientCirc"></span>
                        </span>
                        <img class="position-relative z-1" width="15" height="12" src="{{ asset('public/assets/admin/img/ai/blink-right.svg') }}" alt="">
                    </span>
                    <span id="modalTitle">{{ translate('AI_Assistant') }}</span>
                </h5>
                <button type="button" class="btn btn-circle bg-body-light text-white h-1" style="--size: 20px;" data-dismiss="modal" data-target="#aiAssistantModal" aria-label="{{ translate('Close') }}">
                    <i class="tio-clear"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Main AI Assistant Content -->
                <div id="mainAiContent" class="ai-modal-content" style="display: none;">
                    <div class="text-center mb-4">
                        <div class="ai-avatar mb-3">
                            <div class="avatar-circle mx-auto">
                                <span class="ai-btn-animation">
                                    <span class="gradientCirc"></span>
                                </span>
                                <img class="position-relative z-1" width="40" height="34" src="{{ asset('public/assets/admin/img/ai/blink-right.svg') }}" alt="">
                            </div>
                        </div>

                        <div class="ai-greeting mb-5">
                            <h4 class="text-dark">{{ translate('Hi_There') }},</h4>
                            <h2 class="mb-2">{{ translate('I_am_here_to_help_you') }}!</h2>
                            <p class="">
                                {{ translate('i’m_your_personal_assistance_to_easy_your_long_task_smile') }}.
                                {{ translate('just_select_below_how_you_give_me_instruction_to_get_your_products_all_data') }}.
                            </p>
                        </div>

                        <div class="ai-actions d-flex flex-column align-items-center gap-3">
                            <button type="button" class="btn btn-outline-primary text-dark bg-transparent rounded-10 btn-block max-w-250 d-flex gap-2 ai-action-btn"
                                data-action="upload">
                                <img width="18" height="18" src="{{ asset('public/assets/admin/img/ai/picture.svg') }}" alt="">
                                <span class="text-dark fw-medium">{{ translate('Upload_Image') }}</span>
                            </button>
                            <button type="button" class="btn btn-light border text-dark rounded-10 btn-block max-w-250 d-flex gap-2 ai-action-btn"
                                data-action="title">
                                <img width="18" height="18" src="{{ asset('public/assets/admin/img/ai/text-generate.svg') }}" alt="">
                                <span class="text-dark fw-medium">{{ translate('Generate_Product_Name') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="uploadImageContent" class="ai-modal-content h-100" style="display: none;">
                    <div class="d-flex justify-content-center align-items-end w-100 h-100">
                        <div class="">
                            <div class="mb-4">
                                <h5 class="fs-16 fw-bold opacity-lg">
                                    {{ translate('great!_Now,_upload_your_product_image_to_get_started.') }}
                                </h5>
                                <p class="mb-3">{{ translate('we’ll_use_your_image_to_generate_full_product_data_automatically') }}
                                </p>
                                <ul class="d-flex flex-column gap-2 mb-5">
                                    <li>{{ translate('use_a_clear,_high-quality_image') }}</li>
                                    <li>{{ translate('avoid_blurry_or_low-light_photos') }}</li>
                                    <li>{{ translate('make_sure_it’s_as_close_as_possible_to_your_actual_product') }}</li>
                                </ul>
                            </div>
                            <div class="text-center mb-4 upload-image-for-generating-content">
                                <label class="upload-zone w-100 mx-auto" id="chooseImageBtn">
                                    <input type="file" id="aiImageUpload" class="image-compressor d-none"  hidden
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*">
                                    <input type="file" id="aiImageUploadOriginal" class="d-none" accept="image/*">
                                    <div class="text-box mx-auto">
                                        <div class="w-100 d-flex flex-column gap-2 justify-content-center align-items-center py-4">
                                            <img width="40" height="40" src="{{ asset('public/assets/admin/img/ai/image-upload.svg') }}"
                                                alt="">
                                            <div class="d-flex gap-2 align-items-center justify-content-center flex-wrap fs-14">
                                                <span class="text-dark">{{ translate('drag_&_drop_your_image') }}</span>
                                                <span class="text-lowercase">{{ translate('or') }}</span>
                                                <span type="button" class="text-primary fw-semibold fs-12 text-underline">
                                                    {{ translate('Browse_Image') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="imagePreview" class="mx-auto position-relative" style="display: none;">
                                        <img id="previewImg" src="" alt="{{ translate('Preview') }}" class="upload-zone_img"
                                            style="max-height: 200px;">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <button type="button" class="btn btn-danger p-0 square-div z-2 remove_image_btn" id="removeImageBtn"
                                                data-toggle="tooltip" title="{{ translate('Remove_image') }}">
                                                <i class="tio-clear"></i>
                                            </button>
                                        </div>
                                    </div>
                                </label>

                                <div>
                                    <p class="fs-10 m-0 text-center mt-3">
                                        {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} : Max {{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}
                                        <span class="text-dark font-semibold">(1:1)</span>
                                    </p>
                                </div>


                                <div class="mt-4 text-center analyzeImageBtn_wrapper">
                                    <button type="button" class="btn btn-primary text-white mb-3 d-flex align-items-center gap-2 opacity-1 border-0 mx-auto px-4 position-relative"
                                        id="analyzeImageBtn" data-url="{{ route('admin.product.analyze-image-auto-fill') }}"
                                        data-lang="en">
                                        <span class="ai-btn-animation d-none">
                                            <span class="gradientRect"></span>
                                        </span>
                                        <span class="position-relative z-1 d-flex gap-2 align-items-center">
                                            <span class="d-flex align-items-center bg-transparent text-white btn-text">{{ translate('Generate_Product_Description') }}</span>
                                            <img width="17" height="15" src="{{ asset('public/assets/admin/img/ai/blink-left.svg') }}"
                                                alt="">
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="giveTitleContent" class="ai-modal-content" style="display: none;">
                    <div class="mb-4">
                        <div class="giveTitleContent_text">
                            <h5 class="mb-3 fs-16 fw-bold text-body lh-base">
                                {{ translate('great!') }}
                                <br>
                                {{ translate('now,_tell_me_which_product_you_want_to_create._just_type_it_simply,_like:') }}
                            </h5>
                            <ul class="d-flex flex-column gap-2 mb-3">
                                <li>{{ translate('i_need_product_details_for_an_instant_noodle_cup') }}</li>
                                <li>{{ translate('i_want_to_add_a_ready-to-eat_chicken_burger') }}</li>
                                <li>{{ translate('i_want_to_create_a_product_for_frozen_vegetable_samosas') }}</li>
                            </ul>
                            <p class="mb-4">{{ translate('feel_free_to_describe_it_your_own_way!') }}</p>
                        </div>
                        <div class="generate-text-input-group">
                            <input type="text" class="form-control" id="productKeywords"
                                placeholder="{{ translate('Tell_me_about_your_item') }}" data-role="tagsinput">
                            <button type="button" class="btn btn-primary border-0" id="generateTitleBtn"
                                data-route="{{ route('admin.product.generate-title-suggestions') }}" data-lang="en">
                                <span class="ai-loader-animation z-2 d-none">
                                    <span class="loader-circle"></span>
                                    <div class="position-relative h-100 d-flex justify-content-center align-items-center">
                                        <img width="15" height="15" class=""
                                        src="{{ asset('public/assets/admin/img/ai/blink-left.svg') }}" alt="">
                                    </div>
                                </span>
                                <span class="position-rtelative z-1 text-generate-icon"><i class="tio-arrow-forward"></i></span>
                            </button>
                        </div>

                    </div>

                    <div id="generatedTitles" style="">
                        <div class="text-primary generate_btn_wrapper show_generating_text d-none mb-3">
                            <div class="btn-svg-wrapper">
                                <img width="18" height="18" class="" src="{{ asset('public/assets/admin/img/ai/blink-right-small.svg') }}"
                                alt="">
                            </div>
                            <span class="ai-text-animation ai-text-animation-visible">
                                {{ translate('Just_a_second') }}
                            </span>
                        </div>
                        <h4 class="mb-2 titlesList_title fs-14 fw-bold mb-4 d-none">{{ translate('Suggest_Product_Name') }}</h4>
                        <div id="titlesList" class="list-group gap-4">
                            <!-- Generated titles will appear here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-transparent border-0 shadow-none d-flex justify-content-center align-items-center pt-0">
                <div class="bg-F4F4F4 px-2 py-1 rounded text-center">
                    <p class="mb-0">{{ translate('AI_may_make_mistakes._please_recheck_important_data.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
