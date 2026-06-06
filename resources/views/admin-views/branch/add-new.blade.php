@extends('layouts.admin.app')

@section('title', translate('Add new branch'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/add_branch.png')}}" class="w--20" alt="{{ translate('branch') }}">
                </span>
                <span>
                    {{translate('add New Branch')}}
                </span>
            </h1>
        </div>
        <div class="row g-3">
            <div class="col-sm-12">
                <form action="{{route('admin.branch.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="tio-user"></i>
                                        {{translate('branch information')}}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div class="form-group mb-0">
                                                        <label for="name" class="input-label">{{translate('branch_name')}}</label>
                                                        <input type="text" name="name" id="name" class="form-control" placeholder="{{ translate('Ex: xyz branch') }}" value="{{ old('name') }}" maxlength="255" required>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group mb-0">
                                                        <label for="address" class="input-label" for="">{{translate('address')}}</label>
                                                        <textarea type="text" name="address" id="address" class="form-control h--90px" placeholder="{{translate('Ex: 666/668 DOHS Mirpur, Dhaka, Bangladesh')}}" value="{{ old('address') }}" required>{{ old('address') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="d-flex flex-column justify-content-center h-100">
                                                <div class="text-center mb-3 text--title">
                                                    {{translate('Branch Image')}}
                                                    <small class="text-danger">*</small>
                                                </div>
                                                <label class="upload--squire">
                                                    <input type="file" name="image" id="customFileEg1" class=""
                                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                                           hidden
                                                           data-maxFileSize="{{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}">
                                                    <img id="viewer" src="{{asset('public/assets/admin/img/upload-vertical.png')}}" alt="{{ translate('branch image') }}"/>
                                                </label>
                                                <p class="fs-10 m-0 text-center mt-3">
                                                    {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} : Max {{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}
                                                    <span class="text-dark font-semibold">(1:1)</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-4">
                                            <div class="row g-3">
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="form-group mb-0">
                                                        <label for="phone" class="input-label">{{translate('phone')}}</label>
                                                        <input type="phone" name="phone" id="phone" class="form-control" value="{{ old('phone') }}"
                                                               maxlength="255" placeholder="{{ translate('EX : +09853834') }}"
                                                               required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="form-group mb-0">
                                                        <label for="email" class="input-label">{{translate('email')}}</label>
                                                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                                                               maxlength="255" placeholder="{{ translate('EX : example@example.com') }}"
                                                               required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="form-group mb-0">
                                                        <label for="password" class="input-label">{{translate('password')}}</label>

                                                        <div class="input-group input-group-merge">
                                                            <input type="password" class="js-toggle-password form-control form-control-lg"
                                                                   name="password" id="password"
                                                                   placeholder="{{ translate('8+ characters required') }}"
                                                                   aria-label="8+ characters required"
                                                                   data-hs-toggle-password-options='{
                                                                "target": "#changePassTarget",
                                                            "defaultClass": "tio-hidden-outlined",
                                                            "showClass": "tio-visible-outlined",
                                                            "classChangeTarget": "#changePassIcon"
                                                            }'>
                                                                <div id="changePassTarget" class="input-group-append">
                                                                <a class="input-group-text" href="javascript:">
                                                                    <i id="changePassIcon" class="tio-visible-outlined"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                        @if($googleMapStatus)
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="tio-poi"></i>
                                            {{translate('branch location')}}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <div class="form-group mb-0">
                                                            <label class="form-label text-capitalize" for="latitude">{{ translate('latitude') }}
                                                                <i class="tio-info-outined"
                                                                   data-toggle="tooltip"
                                                                   data-placement="top"
                                                                   title="{{ translate('click_on_the_map_select_your_default_location') }}">
                                                                </i>
                                                            </label>
                                                            <input type="text" id="latitude" name="latitude" class="form-control"
                                                                   placeholder="{{ translate('Ex:') }} 23.8118428"
                                                                   value="{{ old('latitude') }}" required readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group mb-0">
                                                            <label class="form-label text-capitalize" for="longitude">{{ translate('longitude') }}
                                                                <i class="tio-info-outined"
                                                                   data-toggle="tooltip"
                                                                   data-placement="top"
                                                                   title="{{ translate('click_on_the_map_select_your_default_location') }}">
                                                                </i>
                                                            </label>
                                                            <input type="text" step="0.1" name="longitude" class="form-control"
                                                                   placeholder="{{ translate('Ex:') }} 90.356331" id="longitude"
                                                                   value="{{ old('longitude') }}" required readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group mb-0">
                                                            <label class="input-label">
                                                                {{translate('coverage (km)')}}
                                                                <i class="tio-info-outined"
                                                                   data-toggle="tooltip"
                                                                   data-placement="top"
                                                                   title="{{ translate('This value is the radius from your branch location, and customer can order inside  the circle calculated by this radius. The coverage area value must be less or equal than 1000.') }}">
                                                                </i>
                                                            </label>
                                                            <input type="number" name="coverage" min="1" max="1000" class="form-control" placeholder="{{ translate('Ex : 3') }}" value="{{ old('coverage') }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6" id="location_map_div">
                                                <input id="pac-input" class="controls rounded" data-toggle="tooltip"
                                                       data-placement="right"
                                                       data-original-title="{{ translate('search_your_location_here') }}"
                                                       type="text" placeholder="{{ translate('search_here') }}" />
                                                <div id="location_map_canvas" class="overflow-hidden rounded h-100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>




    </div>

@endsection

@push('script_2')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ Helpers::get_business_settings('map_api_client_key') }}&libraries=places&v=3.45.8"></script>
    <script src="{{ asset('public/assets/admin/js/branch.js') }}"></script>

    <script>
        $(document).on('ready', function () {
            $('.js-toggle-password').each(function () {
                new HSTogglePassword(this).init()
            });
        });
    </script>
@endpush
