@extends('layouts.admin.app')

@section('title', translate('Add new delivery-man'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/employee.png')}}" class="w--24" alt="{{ translate('deliveryman') }}">
                </span>
                <span>
                    {{translate('add new deliveryman')}}
                </span>
            </h1>
        </div>

        <form action="{{route('admin.delivery-man.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="card-header-icon">
                            <i class="tio-user"></i>
                        </span> {{translate('General Information')}}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="input-label" for="exampleFormControlInput1">{{translate('First Name')}}</label>
                                            <input type="text" name="f_name" value="{{ old('f_name') }}" class="form-control"
                                                   placeholder="{{translate('Ex : First Name')}}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="input-label" for="exampleFormControlInput1">{{translate('Last Name')}}</label>
                                            <input type="text" name="l_name" value="{{ old('l_name') }}" class="form-control"
                                                   placeholder="{{translate('Ex : Last Name')}}" required>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('phone')}}</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control"
                                               placeholder="{{ translate('Ex : 017********') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('branch')}}</label>
                                        <select name="branch_id" class="form-control">
                                            <option value="0">{{translate('all')}}</option>
                                            @foreach(\App\Model\Branch::all() as $branch)
                                                <option value="{{$branch['id']}}">{{$branch['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('identity')}} {{translate('type')}}</label>
                                        <select name="identity_type" class="form-control">
                                            <option value="passport">{{translate('passport')}}</option>
                                            <option value="driving_license">{{translate('driving')}} {{translate('license')}}</option>
                                            <option value="nid">{{translate('nid')}}</option>
                                            <option value="restaurant_id">{{translate('store Id')}} {{translate('id')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('identity')}} {{translate('number')}}</label>
                                        <input type="text" name="identity_number" class="form-control" value="{{ old('identity_number') }}"
                                                placeholder="{{ translate('Ex : DH-23434-LS') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="bg-light rounded p-xxl-4 p-3 mb-3">
                                <div class="d-flex flex-column justify-content-center h-100">
                                    <h5 class="text-center mb-3 text--title text-capitalize">
                                        {{ translate('Deliveryman Image') }} <span class="text-danger">*</span>
                                    </h5>

                                    <label class="upload--vertical ratio-2-2 max-w-100 w-auto h-200">
                                        <input type="file" name="image" id="customFileEg1"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               hidden
                                               data-maxFileSize="{{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}"
                                               required>
                                        <img id="viewer" class="img-viewer"
                                             src="{{ asset('public/assets/admin/img/add-image.png') }}"
                                             alt="{{ translate('image') }}" />
                                    </label>
                                </div>

                                <p class="fs-10 m-0 text-center mt-3">
                                    {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} : Max {{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}
                                    <span class="text-dark font-semibold">(1:1)</span>
                                </p>

                                <span class="error-text d-flex justify-content-center fs-12px text-danger"
                                      data-error="image"></span>
                            </div>

                            <div>
                                <label class="form-label d-block mb-2">
                                    {{ translate('Identity Image') }}
                                </label>
                                <div class="product--coba">
                                    <div class="row g-2" id="coba"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="card-header-icon">
                            <i class="tio-user"></i>
                        </span> {{translate('Account Information')}}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 col-sm-6">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('email')}}</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="{{ translate('Ex : ex@example.com') }}"
                                    required>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('password')}}</label>
                            <input type="text" name="password" class="form-control" placeholder="{{ translate('7+ Characters') }}" required>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('Confirm password')}}</label>
                            <input type="text" name="password_confirmation" class="form-control" placeholder="{{ translate('7+ Characters') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn--container justify-content-end mt-3">
                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
            </div>
        </form>
    </div>
@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function () {
            readURL(this);
        });
    </script>

    <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>

    <script type="text/javascript">

        let maxSizeReadable = "{{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}";
        let maxFileSize = 2 * 1024 * 1024;

        if (maxSizeReadable.toLowerCase().includes('mb')) {
            maxFileSize = parseFloat(maxSizeReadable) * 1024 * 1024;
        } else if (maxSizeReadable.toLowerCase().includes('kb')) {
            maxFileSize = parseFloat(maxSizeReadable) * 1024;
        }

        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 2,
                rowHeight: '140px',
                groupClassName: 'two__item',
                maxFileSize: maxFileSize,

                placeholderImage: {
                    image: '{{asset('public/assets/admin/img/upload-vertical.png')}}',
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{ translate("Please only input png|jpg|jpeg|gif|webp type file") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function () {
                    toastr.error('File size must be less than ' + maxSizeReadable);
                }
            });
        });
    </script>
@endpush
