@extends('layouts.admin.app')

@section('title', translate('Add new category'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/category.png') }}" class="w--24"
                        alt="{{ translate('category') }}">
                </span>
                <span>
                    {{ translate('category_setup') }}
                </span>
            </h1>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.category.store') }}" method="post" enctype="multipart/form-data"
                    id="category_form">
                    @csrf

                    @php
                        $languages = Helpers::get_business_settings('language');
                        $defaultLanguage = Helpers::get_default_language();
                    @endphp

                    <div class="row justify-content-between g-4">
                        <div class="col-xxl-8 col-xl-8 col-lg-8 col-sm-7">
                            <div class="bg-light rounded p-4 h-100">
                                <div class="mb-sm-5 mb-4">
                                    @if ($languages && array_key_exists('code', $languages[0]))
                                        <ul class="nav nav-tabs d-inline-flex">
                                            @foreach ($languages as $lang)
                                                <li class="nav-item">
                                                    <a class="nav-link lang_link {{ $lang['default'] == true ? 'active' : '' }}"
                                                        href="#"
                                                        id="{{ $lang['code'] }}-link">{{ Helpers::get_language_name($lang['code']) . '(' . strtoupper($lang['code']) . ')' }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                </div>

                                @foreach ($languages as $lang)
                                    <div class="{{ $lang['default'] == false ? 'd-none' : '' }} lang_form"
                                        id="{{ $lang['code'] }}-form">
                                        <label class="form-label" for="exampleFormControlInput1">{{ translate('category') }}
                                            {{ translate('name') }}
                                            ({{ strtoupper($lang['code']) }})
                                            @if ($lang['code'] == 'en')
                                                <span class="input-label-secondary text-danger">*</span>
                                            @endif
                                        </label>

                                        <input type="text" name="name[]" class="form-control"
                                            placeholder="{{ translate('Ex: Size') }}" maxlength="255"
                                            @if ($lang['status'] == true) oninvalid="document.getElementById('{{ $lang['code'] }}-link').click()" @endif>

                                        @if ($lang['code'] == 'en')
                                            <span class="error-text d-flex justify-content-end fs-12px text-danger"
                                                data-error="name.0"></span>
                                        @endif
                                    </div>

                                    <input type="hidden" name="lang[]" value="{{ $lang['code'] }}">
                                @endforeach
                            @else
                                <div class="lang_form col-sm-6" id="{{ $defaultLanguage }}-form">
                                    <label class="form-label" for="default_category_name">{{ translate('category') }}
                                        {{ translate('name') }}
                                        ({{ strtoupper($defaultLanguage) }})</label>

                                    <input type="text" name="name[]" id="default_category_name" class="form-control"
                                        maxlength="255" placeholder="{{ translate('New Category') }}">
                                </div>

                                <input type="hidden" name="lang[]" value="{{ $defaultLanguage }}">
                                @endif

                                <input name="position" value="0" hidden>
                            </div>
                        </div>

                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-sm-5">
                            <div class="bg-light rounded p-xxl-4 p-3">
                                <div class="d-flex flex-column justify-content-center h-100">
                                    <h5 class="text-center mb-3 text--title text-capitalize">
                                        {{ translate('Upload  Image') }}
                                    </h5>

                                    <label class="upload--vertical ratio-2-2 max-w-100 w-auto h-200">
                                        <input type="file" name="image" id="customFileEg1"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               hidden
                                               data-maxFileSize="{{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}">
                                        <img id="viewer" class="img-viewer"
                                             src="{{ asset('public/assets/admin/img/add-image.png') }}"
                                             alt="{{ translate('banner image') }}" />
                                    </label>
                                </div>

                                <p class="fs-10 m-0 text-center mt-3">
                                    {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} : Max {{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}
                                    <span class="text-dark font-semibold">(1:1)</span>
                                </p>

                                <span class="error-text d-flex justify-content-center fs-12px text-danger"
                                    data-error="image"></span>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card--header order-top">
                <div class="d-flex gap-2 align-items-center">
                    <h5 class="mb-0"> {{ translate('Category Table') }}
                        <span class="badge badge-soft-dark rounded-pill fs-10 ml-1">{{ $categories->total() }}</span>
                    </h5>
                </div>

                <div class="d-flex flex-sm-nowrap flex-wrap gap-sm-3 gap-3">
                    <form action="{{ request()->url() }}" method="GET">
                        @foreach (request()->except('search', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                                placeholder="{{ translate('Search by Category') }}" aria-label="Search"
                                value="{{ $search }}" autocomplete="off">

                            <div class="input-group-append h-30">
                                <button type="submit" class="input-group-text title-bg3 p-2 text-white">
                                    <i class="tio-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table
                    class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="bg-card-common">
                        <tr>
                            <th>{{ translate('#') }}</th>
                            <th>{{ translate('category_image') }}</th>
                            <th class="text-center">{{ translate('Total Products') }}</th>
                            <th>{{ translate('status') }}</th>
                            <th>{{ translate('priority') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($categories as $key => $category)
                            <tr>
                                <td>{{ $categories->firstItem() + $key }}</td>
                                <td class="w-260px">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="thumb">
                                            <img src="{{ $category->imageFullPath }}" class="img--50"
                                                alt="{{ translate('category') }}">
                                        </div>
                                        <span class="d-block font-size-sm text-dark line--limit-1 fs-14 text-break max-w-250 min-w-160px text-body text-trim-50">
                                            {{ $category['name'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ $category->products_count }}
                                </td>
                                <td>

                                    <label class="toggle-switch">
                                        <input type="checkbox" class="toggle-switch-input change-category-status"
                                            id="stocksCheckbox{{ $category->id }}"
                                            data-route="{{ route('admin.category.status', [$category->id, $category->status ? 0 : 1]) }}"
                                            data-message="{{ $category->status ? translate('you_want_to_disable_this_category') : translate('you_want_to_active_this_category') }}"
                                            {{ $category->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>

                                </td>
                                <td>
                                    <div class="max-85">
                                        <select name="priority" class="custom-select"
                                            onchange="location.href='{{ route('admin.category.priority', ['id' => $category['id'], 'priority' => '']) }}' + this.value">
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}"
                                                    {{ $category->priority == $i ? 'selected' : '' }}>
                                                    {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn"
                                            href="{{ route('admin.category.edit', [$category['id']]) }}">
                                            <i class="tio-edit"></i></a>
                                        <a class="action-btn btn--danger btn-outline-danger category-delete-btn" href="javascript:"
                                            data-id="category-{{ $category['id'] }}"
                                            data-category-id="{{ $category->id }}"
                                            data-products="{{ $category->products_count }}"
                                            data-delete-route="{{ route('admin.category.delete', $category->id) }}"
                                            data-message="{{ translate('Want to delete this') }}?">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $categories->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>

            @if (count($categories) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                        alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                </div>
            @endif
        </div>


        <!-- Direct delete form -->
        <form id="category-delete-form" method="POST">
            @csrf
            @method('delete')

            <div class="modal fade" id="category_delete_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog max-w-500px modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header p-2">
                            <h1 class="modal-title fs-5"></h1>
                            <button type="button"
                                    class="btn p-1 bg-soft-secondary w-35px h-35 rounded-full min-w-35px d-center"
                                    data-dismiss="modal">
                                <i class="tio-clear fs-20"></i>
                            </button>
                        </div>

                        <div class="modal-body pt-2">
                            <div class="modal-content-box text-center">
                                <div class="mb-4">
                                    <img src="{{ asset('public/assets/admin/img/delete.png') }}" alt="{{ translate('delete') }}">
                                </div>

                                <h4 class="mb-3 text-dark fs-18 font-weight-semibold">
                                    {{ translate('Are you sure to delete this category?') }}
                                </h4>

                                <p class="mb-0 text-gray fs-14">
                                    {{ translate('If you delete this category, the data will be removed permanently.') }}
                                </p>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pb-5 justify-content-center gap-3">
                            <button type="button" class="btn btn--reset min-w-120px" data-dismiss="modal">
                                {{ translate('No') }}
                            </button>

                            <button type="button" class="btn btn-danger min-w-120px" id="confirmDirectDelete">
                                {{ translate('Delete') }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>

        <!-- Shift & delete form -->
        <form id="category-shift-delete-form" method="POST">
            @csrf
            @method('delete')

            <!-- Hidden input for selected shift category -->
            <input type="hidden" name="shift_category_id" id="shift_category_id">

            <div class="modal fade" id="shiftcategory_delete_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog max-w-500px modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header p-2">
                            <h1 class="modal-title fs-5"></h1>
                            <button type="button"
                                    class="btn p-1 bg-soft-secondary w-35px h-35 rounded-full min-w-35px d-center"
                                    data-dismiss="modal">
                                <i class="tio-clear fs-20"></i>
                            </button>
                        </div>

                        <div class="modal-body pt-2">
                            <div class="modal-content-box text-center">

                                <div class="mb-4">
                                    <img src="{{ asset('public/assets/admin/img/delete.png') }}" alt="{{ translate('delete') }}">
                                </div>

                                <h4 class="mb-3 text-dark fs-18 font-weight-semibold">
                                    {{ translate('Are you sure to delete this category?') }}
                                </h4>

                                <p class="mb-3 text-gray fs-14">
                                    {{ translate('This category has') }}
                                    <strong><span id="categoryProductCount">0</span> {{ translate('products') }}</strong>.
                                    {{ translate('Please shift those products to another category before deleting.') }}
                                </p>

                                <div class="form-group mb-0 text-start">
                                    <label class="input-label mb-10px">{{ translate('Choose Category') }}</label>
                                    <select id="shiftCategorySelect" class="form-control h-40 py-2">
                                        <option value="">{{ translate('Select category') }}</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer border-0 pb-5 justify-content-center gap-3">
                            <button type="button" class="btn btn--reset min-w-120px" data-dismiss="modal">
                                {{ translate('No') }}
                            </button>

                            <button type="button" class="btn btn-danger min-w-120px" id="confirmShiftDelete">
                                {{ translate('Shift & Delete') }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>

        <div class="modal fade" id="turnOnOff_modal" tabindex="-1" aria-labelledby="turnOnOff_modalLabel" aria-hidden="true">
            <div class="modal-dialog max-w-500px modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h1 class="modal-title fs-5"></h1>
                        <button type="button" class="btn p-1 bg-soft-secondary w-35px h-35 rounded-full min-w-35px d-center" data-dismiss="modal" aria-label="Close">
                            <i class="tio-clear fs-20"></i>
                        </button>
                    </div>
                    <div class="modal-body pt-2">
                        <div class="modal-content-box text-center">
                            <div class="mb-4">
                                <img src="{{ asset('public/assets/admin/img/warning-icon-theme.png') }}"
                                     alt="{{ translate('warning') }}">
                            </div>
                            <h4 id="statusModalTitle" class="mb-3 text-dark fs-18 font-weight-semibold">
                                {{ translate('Turn off this category?') }}
                            </h4>
                            <p id="statusModalDesc" class="mb-0 text-gray fs-14">
                                {{ translate('Once disabled, the category and all its products will no longer be visible on the website/app.') }}
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-5 justify-content-center gap-3">
                        <button type="button" class="btn btn--reset min-w-120px" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn--primary min-w-120px" id="confirmStatusChange">Turn Off</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/category.js') }}"></script>
    <script>
        "use strict";

        $(".lang_link").click(function(e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{ $defaultLanguage }}') {
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });

        submitByAjax('#category_form', {
            hasEditors: false,
            languages: @json($languages ?? []),
            successMessage: '{{ translate('Category added successfully!') }}',
            redirectUrl: '{{ route('admin.category.add') }}'
        });
    </script>

    <script>
        let statusChangeRoute = '';
        let lastCheckbox = null;

        $(document).on('change', '.change-category-status', function (e) {
            e.preventDefault();

            lastCheckbox = $(this);
            statusChangeRoute = lastCheckbox.data('route');

            // revert toggle until confirmation
            lastCheckbox.prop('checked', !lastCheckbox.prop('checked'));

            const isCurrentlyOn = lastCheckbox.is(':checked');
            const isTurningOff = isCurrentlyOn;

            if (isTurningOff) {
                $('#statusModalTitle').text('Turn off this category?');
                $('#statusModalDesc').text(
                    'Once disabled, the category and all its products will no longer be visible on the website/app.'
                );

                $('#confirmStatusChange')
                    .removeClass('btn--primary')
                    .addClass('btn--danger')
                    .text('Turn Off');
            } else {
                $('#statusModalTitle').text('Turn on this category?');
                $('#statusModalDesc').text(
                    'This category and its products will become visible on the website/app.'
                );

                $('#confirmStatusChange')
                    .removeClass('btn--danger')
                    .addClass('btn--primary')
                    .text('Turn On');
            }

            $('#turnOnOff_modal').modal('show');
        });

        $('#confirmStatusChange').on('click', function () {
            if (statusChangeRoute) {
                window.location.href = statusChangeRoute;
            }
        });

        $('#turnOnOff_modal').on('hidden.bs.modal', function () {
            statusChangeRoute = '';
            lastCheckbox = null;
        });

        let deleteRoute = '';
        const shiftCategoryOptionsTemplate = $('#shiftCategorySelect').html();

        $(document).on('click', '.category-delete-btn', function () {
            const productCount = parseInt($(this).data('products'));
            const currentCategoryId = String($(this).data('category-id'));
            deleteRoute = $(this).data('delete-route');

            if (productCount > 0) {
                $('#categoryProductCount').html(productCount);
                $('#shiftCategorySelect').html(shiftCategoryOptionsTemplate).val('');
                $('#shiftCategorySelect option[value="' + currentCategoryId + '"]').remove();
                $('#category-shift-delete-form').attr('action', deleteRoute);
                $('#shiftcategory_delete_modal').modal('show');
            } else {
                $('#category-delete-form').attr('action', deleteRoute);
                $('#category_delete_modal').modal('show');
            }
        });

        // Direct delete
        $('#confirmDirectDelete').on('click', function () {
            $('#category-delete-form').submit();
        });

        // Shift & delete
        $('#confirmShiftDelete').on('click', function () {
            const shiftCategoryId = $('#shiftCategorySelect').val();

            if (!shiftCategoryId) {
                toastr.warning('Please select a category to shift products');
                return;
            }

            $('#shift_category_id').val(shiftCategoryId);
            $('#category-shift-delete-form').submit();
        });

        $('#shiftcategory_delete_modal').on('hidden.bs.modal', function () {
            $('#shiftCategorySelect').html(shiftCategoryOptionsTemplate).val('');
            $('#shift_category_id').val('');
        });

    </script>



@endpush
