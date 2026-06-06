@extends('layouts.admin.app')

@section('title', translate('AI Configuration'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/ai-sidebar.css')}}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/ai/blink-right-small.svg') }}" class="w--22" alt="">
                </span>
                <span>{{ translate('AI_Setup') }}</span>
            </h1>
        </div>

        <form action="{{ route('admin.business-settings.ai-configuration.store') }}" method="POST">
            @csrf
            <div class="card card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div class="mb-4">
                        <h5 class="card-title mb-1">{{ translate('AI_Configuration') }}</h5>
                        <p class="fs-12 mb-0">{{ translate('Fill_up_the_necessary_info_to_activate_AI_feature.') }}</p>
                    </div>
                    <div class="">
                        <label class="toggle-switch my-0">
                            <input type="checkbox"
                                   class="toggle-switch-input ai-configuration-status-change-alert"
                                   id="stocksCheckbox2"
                                   data-status-on-image="{{ asset('public/assets/admin/img/icons/status-on.png') }}"
                                   data-status-off-image="{{ asset('public/assets/admin/img/icons/status-off.png') }}"
                                   data-status-on-title="{{ translate('Do you want to activate AI feature') }}?"
                                   data-status-off-title="{{ translate('Do you want to deactivate AI feature') }}?"
                                   data-status-on-subtitle="{{ translate('If enabled, AI feature will be active and able to to generate content by AI') }}"
                                   data-status-off-subtitle="{{ translate('If disabled, AI feature will be inactive and could not able to generate content by AI.') }}"
                                   data-cancel-btn-text="{{ translate('cancel') }}"
                                   data-confirm-btn-text="{{ translate('Yes') }}"
                                   name="status"
                                @checked($aiSetting?->status ?? 0)
                            >
                            <span class="toggle-switch-label mx-auto text"><span class="toggle-switch-indicator"></span></span>
                        </label>
                    </div>
                </div>
                <div class="bg-light rounded-10 p-4">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="form-group mb-0">
                                <label class="text-capitalize">{{ translate('Open_AI_Key') }}
                                    <i class="tio-info-outined" data-toggle="tooltip" data-placement="top" title="{{ translate('Sign in to OpenAI, create an API key, and use it here.') }}"></i>
                                </label>
                                <input type="text" class="form-control" name="api_key" placeholder="{{translate('Type API Key')}} *"  value="{{env('APP_MODE')=='demo'?'':$aiSetting?->api_key}}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-0">
                                <label class="text-capitalize">{{translate('Open_AI_Organization')}}
                                    <i class="tio-info-outined" data-toggle="tooltip" data-placement="top" title="{{ translate('Get your OpenAI Organization ID and enter it here for access and billing.') }}"></i>
                                </label>
                                <input type="text" class="form-control" name="organization_id" placeholder="{{translate('Type Organization Id')}} *"  value="{{env('APP_MODE')=='demo'?'':$aiSetting?->organization_id}}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn--container justify-content-end mt-4">
                <button type="reset" class="btn btn--reset">{{ translate('Reset') }}</button>
                <button type="{{env('APP_MODE')=='demo'?'button':'submit'}}" class="btn btn--primary call-demo">{{ translate('Submit') }}</button>
            </div>
        </form>
    </div>

    {{-- View guideline btn --}}
    <div class="d-flex gap-2 flex-column align-items-center bg-white position-fixed cursor-pointer view-guideline-btn" data-toggle="modal" data-target="#aiSetupGuideline">
        <span class="bg-primary p-5px text-white rounded d-flex justify-content-center align-items-center">
            <img src="{{ asset('public/assets/admin/img/ai/redo.svg') }}" alt="" class="svg" width="10" height="8">
        </span>
        <span class="view-guideline-btn-text text-dark fw-medium text-nowrap">
            {{ translate('View_Guideline') }}
        </span>
    </div>
    {{-- modal --}}
    <div class="modal fade p-0" id="aiSetupGuideline" tabindex="-1" aria-labelledby="#aiSetupGuidelineLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-slideInRight modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex gap-2 aign-items-center justify-content-between">
                    <h5 class="modal-title d-flex align-items-center gap-2 #aiSetupGuidelineLabel" id="#aiSetupGuidelineLabel">
                        <span id="modalTitle">{{ translate('AI_Configuration_Guideline') }}</span>
                    </h5>
                    <button type="button" class="btn btn-circle bg-body-light text-white h-1" style="--size: 20px;" data-dismiss="modal" aria-label="{{ translate('Close') }}">
                        <i class="tio-clear"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3 p-sm-4 bg-light rounded mb-3 mb-sm-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between overflow-hidden">
                            <button class="btn-collapse d-flex gap-3 align-items-center bg-transparent border-0 p-0" type="button"
                                    data-toggle="collapse" data-target="#collapsePurpose" aria-expanded="true">
                                <div class="btn-collapse-icon border bg-light icon-btn rounded-circle text-dark fs-20">
                                    <i class="tio-chevron-right"></i>
                                </div>
                                <span class="font-weight-bold text-start">{{ translate('Purpose') }} </span>
                            </button>
                        </div>

                        <div class="collapse mt-3 show" id="collapsePurpose">
                            <div class="card card-body">
                                <p class="fs-12 mb-0">
                                    {{ translate('To_configure_your_preferred_AI_provider_(e.g.,_OpenAI)_by_entering_the_necessary_credentials_and_AI_based_features_like_content_generation_or_image_processing') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 p-sm-4 bg-light rounded mb-3 mb-sm-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between overflow-hidden">
                            <button class="btn-collapse d-flex gap-3 align-items-center bg-transparent border-0 p-0 collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseAiFeatureEnableOpenAlConfigurationToggle" aria-expanded="true">
                                <div class="btn-collapse-icon border bg-light icon-btn rounded-circle text-dark fs-20">
                                    <i class="tio-chevron-right"></i>
                                </div>
                                <span class="font-weight-bold text-start">{{ translate('Enable OpenAl Configuration') }} </span>
                            </button>
                        </div>

                        <div class="collapse mt-3" id="collapseAiFeatureEnableOpenAlConfigurationToggle">
                            <div class="card card-body">
                                <ul class="fs-12 mb-0">
                                    <li>
                                        {{ translate('Go to the OpenAl API platform and') }}
                                        <a target="_blank" href="{{ 'https://platform.openai.com/docs/overview' }}">{{ translate('Sign up') }}</a>
                                        <span class="px-1">{{ translate('or') }}</span>
                                        <a target="_blank" href="{{ 'https://platform.openai.com/docs/overview' }}">{{ translate('Log in.') }}</a>
                                    </li>
                                    <li>
                                        {{ translate('Create a new API key and use it in the OpenAI API key section.') }}
                                    </li>
                                    <li>
                                        {{ translate('Get your OpenAI Organization ID and enter it here for access and billing.') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 p-sm-4 bg-light rounded mb-3 mb-sm-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between overflow-hidden">
                            <button class="btn-collapse d-flex gap-3 align-items-center bg-transparent border-0 p-0 collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseAiFeatureToggle" aria-expanded="true">
                                <div class="btn-collapse-icon border bg-light icon-btn rounded-circle text-dark fs-20">
                                    <i class="tio-chevron-right"></i>
                                </div>
                                <span class="font-weight-bold text-start">{{ translate('AI_Feature_Toggle') }} </span>
                            </button>
                        </div>

                        <div class="collapse mt-3" id="collapseAiFeatureToggle">
                            <div class="card card-body">
                                <p class="fs-12">
                                    {{ translate('Use_this_switch_to_turn_AI_features_on_or_off_for_your_platform.') }}
                                </p>
                                <ul class="fs-12 mb-0">
                                    <li>
                                        {{ translate('When_ON') }}: {{ translate('AI_tools_like_content_and_image_generation_will_work.') }}
                                    </li>
                                    <li>
                                        {{ translate('When_OFF') }}: {{ translate('all_AI_features_will_stop_working_until_you_turn_it_back_on.') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 p-sm-4 bg-light rounded mb-3 mb-sm-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between overflow-hidden">
                            <button class="btn-collapse d-flex gap-3 align-items-center bg-transparent border-0 p-0 collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseTip" aria-expanded="true">
                                <div class="btn-collapse-icon border bg-light icon-btn rounded-circle text-dark fs-20">
                                    <i class="tio-chevron-right"></i>
                                </div>
                                <span class="font-weight-bold text-start">{{ translate('Tip') }} </span>
                            </button>
                        </div>

                        <div class="collapse mt-3" id="collapseTip">
                            <div class="card card-body">
                                <p class="fs-12 mb-0">
                                    {{ translate('you_need_to_enter_the_correct_api_details_so_the_AI_tools_(like_text_or_image_generation)_can_work_without_errors.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="aiConfigStatusModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="text-center mb-5">
                            <img id="img" src="{{ asset('public/assets/admin/svg/components/map-icon.svg') }}" alt="Unchecked Icon" class="mb-5">
                            <h4 id="title">{{ translate('Are You Sure') }}?</h4>
                            <p id="subtitle">{{ translate('By Turning On the Google Maps you need to setup following setting to get the map work properly.') }}</p>
                        </div>
                        <div class="btn--container justify-content-center my-4">
                            <button class="btn btn-secondary h--45px min-w-120px" id="cancelButton">{{ translate('Cancel') }}</button>
                            <button class="btn btn--primary h--45px min-w-120px" id="confirmBtn">{{ translate('Ok') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $(document).ready(function () {
            $('.ai-configuration-status-change-alert').on('click', function (e) {
                e.preventDefault();
                const checkbox = $(this);
                const currentState = checkbox.prop('checked');
                const  $aiConfigStatusModal = $('#aiConfigStatusModal');
                $aiConfigStatusModal.find('#img').attr('src', currentState ? checkbox.data('status-on-image') : checkbox.data('status-off-image'));
                $aiConfigStatusModal.find('#title').text(currentState ? checkbox.data('status-on-title') : checkbox.data('status-off-title'));
                $aiConfigStatusModal.find('#subtitle').text(currentState ? checkbox.data('status-on-subtitle') : checkbox.data('status-off-subtitle'));
                $aiConfigStatusModal.find('#cancelButton').text(checkbox.data('cancel-btn-text'));
                $aiConfigStatusModal.find('#confirmBtn').text(checkbox.data('confirm-btn-text'));
                $('#aiConfigStatusModal').modal('show');

                $aiConfigStatusModal.find('#confirmBtn').off('click').on('click', function () {
                    checkbox.prop('checked', currentState).trigger('change');
                    $aiConfigStatusModal.modal('hide');
                });

                $aiConfigStatusModal.find('#cancelButton').off('click').on('click', function () {
                    $aiConfigStatusModal.modal('hide');
                });
            });
        })
    </script>
@endpush
