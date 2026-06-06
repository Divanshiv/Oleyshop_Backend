@extends('layouts.admin.app')

@section('title', translate('Add new sub category'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/category.png') }}" class="w--24"
                        alt="{{ translate('category') }}">
                </span>
                <span>
                    {{ translate('sub_category_setup') }}
                </span>
            </h1>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.category.store') }}" method="post" id="sub_category_form">
                    @csrf

                    @php
                        $languages = Helpers::get_business_settings('language');
                        $defaultLanguage = Helpers::get_default_language();
                    @endphp

                    @if ($languages && array_key_exists('code', $languages[0]))
                        <ul class="nav nav-tabs mb-4 d-inline-flex">
                            @foreach ($languages as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{ $lang['default'] == true ? 'active' : '' }}"
                                        href="#"
                                        id="{{ $lang['code'] }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang['code']) . '(' . strtoupper($lang['code']) . ')' }}</a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="bg-light rounded p-4">
                            <div class="row g-4">
                                @foreach ($languages as $lang)
                                    <div class="col-sm-6 {{ $lang['default'] == false ? 'd-none' : '' }} lang_form"
                                        id="{{ $lang['code'] }}-form">
                                        <label class="form-label"
                                            for="exampleFormControlInput1">{{ translate('sub_category') }}
                                            {{ translate('name') }} ({{ strtoupper($lang['code']) }})
                                            @if ($lang['code'] == 'en')
                                                <span class="input-label-secondary text-danger">*</span>
                                            @endif
                                        </label>

                                        <input type="text" name="name[]" class="form-control" maxlength="255"
                                            placeholder="{{ translate('New Sub Category') }}"
                                            @if ($lang['status'] == true) oninvalid="document.getElementById('{{ $lang['code'] }}-link').click()" @endif>

                                        @if ($lang['code'] == 'en')
                                            <span class="error-text d-flex justify-content-end fs-12px text-danger"
                                                data-error="name.0"></span>
                                        @endif
                                    </div>

                                    <input type="hidden" name="lang[]" value="{{ $lang['code'] }}">
                                @endforeach
                                @else
                                    <div class="col-sm-6 lang_form" id="{{ $defaultLanguage }}-form">
                                        <label class="form-label" for="exampleFormControlInput1">{{ translate('sub_category') }}
                                            {{ translate('name') }}({{ strtoupper($defaultLanguage) }})</label>
                                        <input type="text" name="name[]" class="form-control"
                                            placeholder="{{ translate('New Sub Category') }}">
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $defaultLanguage }}">
                                @endif

                                <input name="position" value="1" hidden>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label" for="exampleFormControlSelect1">{{ translate('main') }}
                                            {{ translate('category') }}<span class="input-label-secondary text-danger">*</span></label>

                                        <select id="exampleFormControlSelect1" name="parent_id" class="form-control" required>
                                            @foreach ($mainCategories as $category)
                                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="mt-20">
                            <div class="btn--container justify-content-end">
                                <a href="" class="btn btn--reset min-w-120px">{{ translate('reset') }}</a>
                                <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                            </div>
                        </div>
                </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card--header order-top">
            <div class="d-flex gap-2 align-items-center">
                <h5 class="mb-0"> {{ translate('Sub Category Table') }}
                    <span class="badge badge-soft-dark rounded-pill fs-10 ml-1">{{ $categories->total() }}</span>
                </h5>
            </div>

            <div class="d-flex flex-sm-nowrap flex-wrap gap-sm-3 gap-3">
                <form action="{{ request()->url() }}" method="GET">
                    @foreach (request()->except('search', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="input-group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control h-30 min-w-180px"
                            placeholder="{{ translate('Search by Sub Category') }}" aria-label="Search"
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
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="bg-card-common">
                    <tr>
                        <th>{{ translate('#') }}</th>
                        <th>{{ translate('main') }} {{ translate('category') }}</th>
                        <th>{{ translate('sub_category') }}</th>
                        <th class="text-center">{{ translate('Total Products') }}</th>
                        <th>{{ translate('status') }}</th>
                        <th class="text-center">{{ translate('action') }}</th>
                    </tr>

                </thead>

                <tbody id="set-rows">
                    @foreach ($categories as $key => $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $key }}</td>
                            <td>
                                @if($category->parent)
                                    <span class="d-block font-size-sm text-body max-w--160px min-w-120px line--limit-1 text-nowrap">
                                        {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger font-size-sm">
                                        {{ translate('Category Deleted') }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="d-block font-size-sm text-body max-w--160px min-w-120px line--limit-1 text-nowrap">
                                    {{ $category['name'] }}
                                </span>
                            </td>

                            <td class="text-center">
                                {{ $category->products_count }}
                            </td>

                            <td>
                                <label class="toggle-switch">

                                    <input type="checkbox"
                                           class="toggle-switch-input category-status-toggle"
                                           id="stocksCheckbox{{ $category->id }}"

                                           data-id="{{ $category->id }}"
                                           data-parent-exists="{{ $category->parent ? 1 : 0 }}"
                                           data-route="{{ route('admin.sub-category.status', [$category->id, $category->status ? 0 : 1]) }}"
                                           data-current-status="{{ $category->status }}"

                                        {{ $category->status ? 'checked' : '' }}>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>

                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn" href="{{ route('admin.category.edit', [$category['id']]) }}">
                                        <i class="tio-edit"></i>
                                    </a>

                                    <a href="javascript:void(0)"
                                       class="action-btn btn--danger btn-outline-danger subcat-delete-btn"
                                       data-id="{{ $category->id }}"
                                       data-products="{{ $category->products_count }}"
                                       data-parent="{{ $category->parent_id }}"
                                       data-delete-route="{{ route('admin.sub-category.delete', $category->id) }}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (count($categories) == 0)
            <div class="text-center p-4">
                <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                    alt="{{ translate('image') }}">
                <p class="mb-0">{{ translate('No_data_to_show') }}</p>
            </div>
        @endif

        <div class="">
            {!! $categories->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
        </div>
    </div>

        <div class="modal fade" id="switchParentModal" tabindex="-1">
            <div class="modal-dialog max-w-500px modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header p-2">
                        <h5 class="modal-title">{{ translate('Parent Category Required') }}</h5>
                        <button type="button" class="btn p-1" data-dismiss="modal">
                            <i class="tio-clear"></i>
                        </button>
                    </div>

                    <form action="{{ route('admin.sub-category.switch-parent') }}" id="switchParentForm" method="POST">
                        @csrf

                        <div class="modal-body text-center">
                            <p class="text-danger mb-3">
                                {{ translate('Main category was deleted. Please assign a new category before changing status.') }}
                            </p>

                            <input type="hidden" name="status" id="switch_status">
                            <input type="hidden" name="category_id" id="switch_category_id">

                            <div class="form-group text-start">
                                <label>{{ translate('Select Category') }}</label>
                                <select name="parent_id" class="form-control" required>
                                    <option value="">{{ translate('Select category') }}</option>
                                    @foreach($mainCategories as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn--reset" data-dismiss="modal">
                                {{ translate('Cancel') }}
                            </button>
                            <button type="submit" class="btn btn--primary">
                                {{ translate('Update') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

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
                            <h4 class="mb-3 text-dark fs-18 font-weight-semibold" id="statusModalTitle"> {{ translate('Are you sure you want to turn off this sub category?') }}</h4>
                            <p class="mb-0 text-gray fs-14" id="statusModalDesc"> {{ translate('Once disabled, the Sub-category and all its products will no longer be visible on the website/app.') }}</p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-5 justify-content-center gap-3">
                        <button type="button" class="btn btn--reset min-w-120px" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn--primary min-w-120px" id="confirmStatusChange">Yes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Delete Form (GLOBAL) -->

        <form id="subCategoryDeleteForm" method="POST" style="display:none;">
            @csrf
            @method('DELETE')

            <input type="hidden" name="shift_to_parent" id="shift_to_parent" value="0">
            <input type="hidden" name="shift_category_id" id="shift_category_id">
            <input type="hidden" name="shift_main_category_id" id="shift_main_category_id">
        </form>

        <!-- MODAL 1: Simple Delete (No Product) -->
        <div class="modal fade" id="category_delete_modal" tabindex="-1">
            <div class="modal-dialog max-w-500px modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h1 class="modal-title fs-5"></h1>
                        <button type="button" class="btn p-1 bg-soft-secondary w-35px h-35 rounded-full"
                                data-dismiss="modal">
                            <i class="tio-clear fs-20"></i>
                        </button>
                    </div>

                    <div class="modal-body pt-2">
                        <div class="modal-content-box text-center">
                            <div class="mb-4">
                                <img src="{{ asset('public/assets/admin/img/delete.png') }}">
                            </div>

                            <h4 class="mb-3 text-dark fs-18 font-weight-semibold">
                                {{ translate('Are you sure to delete this sub category?') }}
                            </h4>

                            <p class="mb-0 text-gray fs-14">
                                {{ translate('If once you delete this sub category, you will lost this sub category data permanently.') }}
                            </p>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pb-5 justify-content-center gap-3">
                        <button type="button" class="btn btn--reset min-w-120px" data-dismiss="modal">
                            {{ translate('No') }}
                        </button>
                        <button type="button" id="confirm_simple_delete" class="btn btn-danger min-w-120px">
                            {{ translate('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL 2: Has Product (Shift to Parent OR Open Shift Modal) -->
        <div class="modal fade" id="shift_delete_modal" tabindex="-1">
            <div class="modal-dialog max-w-500px modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h1 class="modal-title fs-5"></h1>
                        <button type="button" class="btn p-1 bg-soft-secondary w-35px h-35 rounded-full"
                                data-dismiss="modal">
                            <i class="tio-clear fs-20"></i>
                        </button>
                    </div>

                    <div class="modal-body pt-2">
                        <div class="modal-content-box text-center">
                            <div class="mb-4">
                                <img src="{{ asset('public/assets/admin/img/delete.png') }}">
                            </div>

                            <h4 class="mb-3 text-dark fs-18 font-weight-semibold">
                                {{ translate('Are you sure to delete this sub category?') }}
                            </h4>

                            <p class="mb-0 text-gray fs-14">
                                {{ translate('This sub category has') }}
                                <strong id="product_count_text">0 {{ translate('Products') }}.</strong>
                                {{ translate('those will be shifted to the main category. If you want to shift those products to another sub category click below.') }}
                            </p>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pb-4 justify-content-center gap-3">
                        <button type="button" class="btn btn--reset min-w-120px" data-dismiss="modal">
                            {{ translate('No') }}
                        </button>
                        <button type="button" id="confirm_shift_parent" class="btn btn-danger min-w-120px">
                            {{ translate('Shift & Delete') }}
                        </button>
                    </div>

                    <div class="pb-5 mb-2 text-center">
                        <button type="button" id="open_shift_modal2"
                                class="btn p-0 text-theme fs-14 font-weight-semibold text-underline">
                            {{ translate('Shift to another sub category') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL 3: Shift to Another Subcategory -->
        <div class="modal fade" id="shift_delete_modal2" tabindex="-1">
            <div class="modal-dialog max-w-500px modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h1 class="modal-title fs-5"></h1>
                        <button type="button" class="btn p-1 bg-soft-secondary w-35px h-35 rounded-full"
                                data-dismiss="modal">
                            <i class="tio-clear fs-20"></i>
                        </button>
                    </div>

                    <div class="modal-body pt-2">
                        <div class="modal-content-box text-center px-lg-2">
                            <div class="mb-4">
                                <img src="{{ asset('public/assets/admin/img/delete.png') }}">
                            </div>

                            <h4 class="mb-3 text-dark fs-18 font-weight-semibold">
                                {{ translate('Are you sure to delete this sub category?') }}
                            </h4>

                            <p class="mb-0 text-gray fs-14">
                                {{ translate('This sub category has') }}
                                <strong id="product_count_text_modal3">0 {{ translate('Products') }}.</strong>
                                {{ translate('Please shifting them to another category before deleting.') }}
                            </p>

                            <div class="d-flex flex-sm-nowrap flex-wrap mt-4 gap-4 pt-xl-1">
                                <div class="form-group mb-0 w-100">
                                    <label class="input-label text-start mb-10px">
                                        {{ translate('Choose Category') }} <span class="input-label-secondary text-danger">*</span>
                                    </label>
                                    <select id="choose_category" class="form-control h-40 py-2">
                                        <option value="">{{ translate('Select Category') }}</option>
                                        @foreach($mainCategories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-0 w-100">
                                    <label class="input-label text-start mb-10px">
                                        {{ translate('Choose Sub Category') }}
                                    </label>
                                    <select id="choose_sub_category" class="form-control h-40 py-2">
                                        <option value="">{{ translate('Select Sub Category') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pb-4 justify-content-center gap-3">
                        <button type="button" class="btn btn--reset min-w-120px" data-dismiss="modal">
                            {{ translate('No') }}
                        </button>
                        <button type="button" id="confirm_shift_other" class="btn btn-danger min-w-120px">
                            {{ translate('Shift & Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

@endsection

@push('script_2')
    <script>
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

        submitByAjax('#sub_category_form', {
            hasEditors: false,
            languages: @json($languages ?? []),
            successMessage: '{{ translate('Sub category added successfully!') }}',
            redirectUrl: '{{ route('admin.category.add-sub-category') }}'
        });
    </script>

            <script>
                let pendingRoute = null;
                let pendingCheckbox = null;

                $(document).on('change', '.category-status-toggle', function () {

                    const parentExists = Number($(this).data('parent-exists'));
                    const categoryId   = $(this).data('id');
                    const route        = $(this).data('route');
                    const isChecked    = $(this).is(':checked');

                    // rollback UI until confirmed
                    $(this).prop('checked', !isChecked);

                    pendingRoute = route;
                    pendingCheckbox = this;

                    if (parentExists === 0) {
                        // parent deleted → switch parent modal
                        $('#switch_category_id').val(categoryId);
                        $('#switch_status').val(isChecked ? 1 : 0);
                        $('#switchParentModal').modal('show');
                        return;
                    }

                    // parent exists → normal status modal
                    updateStatusModalText(isChecked);
                    $('#turnOnOff_modal').modal('show');
                });

                function updateStatusModalText(isTurningOn) {
                    if (isTurningOn) {
                        $('#statusModalTitle').text("{{ translate('Turn on this category?') }}");
                        $('#statusModalDesc').text("{{ translate('This category and its products will be visible on the website/app.') }}");
                        $('#confirmStatusChange')
                            .removeClass('btn--danger')
                            .addClass('btn--primary')
                            .text('Turn On');
                    } else {
                        $('#statusModalTitle').text("{{ translate('Turn off this category?') }}");
                        $('#statusModalDesc').text("{{ translate('Once disabled, the category and all its products will no longer be visible.') }}");
                        $('#confirmStatusChange')
                            .removeClass('btn--primary')
                            .addClass('btn--danger')
                            .text('Turn Off');
                    }
                }

                $('#confirmStatusChange').on('click', function () {
                    if (!pendingRoute) return;

                    window.location.href = pendingRoute;
                });
            </script>

            <script>
                let deleteSubCategoryId = null;
                let deleteRoute = null;
                let parentCategoryId = null;
                let productCount = 0;

                // Open correct modal based on product count
                $(document).on('click', '.subcat-delete-btn', function () {
                    deleteSubCategoryId = $(this).data('id');
                    productCount = parseInt($(this).data('products')) || 0;
                    parentCategoryId = $(this).data('parent');

                    deleteRoute = "{{ route('admin.sub-category.delete', ':id') }}".replace(':id', deleteSubCategoryId);

                    if (productCount === 0) {
                        $('#category_delete_modal').modal('show');
                    } else {
                        $('#product_count_text').text(productCount + ' Products.');
                        $('#product_count_text_modal3').text(productCount + ' Products.');
                        $('#shift_delete_modal').modal('show');
                    }
                });

                $('#confirm_simple_delete').on('click', function () {
                    submitDeleteForm(0, null);
                });

                $('#confirm_shift_parent').on('click', function () {
                    submitDeleteForm(1, null);
                });

                $('#open_shift_modal2').on('click', function () {
                    $('#shift_delete_modal').modal('hide');
                    $('#shift_delete_modal2').modal('show');
                });


                // Load subcategories when category changes
                $('#choose_category').on('change', function () {
                    let categoryId = $(this).val();

                    if (!categoryId) {
                        $('#choose_sub_category')
                            .html('<option value="">Select Sub Category</option>')
                            .prop('disabled', true);
                        return;
                    }

                    $('#choose_sub_category')
                        .html('<option value="">Loading...</option>')
                        .prop('disabled', true);

                    $.get("{{ route('admin.category.children', ':id') }}".replace(':id', categoryId), function (data) {

                        // If NO subcategories exist
                        if (!data || data.length === 0) {
                            $('#choose_sub_category')
                                .html('<option value="">No sub category available (will shift to main category)</option>')
                                .prop('disabled', true);
                            return;
                        }

                        let options = '<option value="">Select Sub Category</option>';

                        data.forEach(function (sub) {
                            if (sub.id != deleteSubCategoryId) {
                                options += `<option value="${sub.id}">${sub.name}</option>`;
                            }
                        });

                        $('#choose_sub_category')
                            .html(options)
                            .prop('disabled', false);
                    });
                });



                // Shift to selected subcategory
                $('#confirm_shift_other').on('click', function () {

                    let mainCategoryId = $('#choose_category').val();
                    let subCategoryId  = $('#choose_sub_category').val();
                    let subDisabled    = $('#choose_sub_category').prop('disabled');

                    if (!mainCategoryId) {
                        toastr.warning('Please select a category');
                        return;
                    }

                    // Category has NO subcategories
                    if (subDisabled || !subCategoryId) {
                        submitDeleteForm(0, null, mainCategoryId);
                        return;
                    }

                    // shift to selected subcategory
                    submitDeleteForm(0, subCategoryId, mainCategoryId);
                });



                // Final delete form submission
                function submitDeleteForm(shiftToParent, shiftCategoryId = null, mainCategoryId = null) {
                    $('#shift_to_parent').val(shiftToParent);

                    if (shiftCategoryId) {
                        $('#shift_category_id').val(shiftCategoryId);
                        $('#shift_main_category_id').val(mainCategoryId);
                    } else {
                        // If no subcategory selected, still pass main category
                        $('#shift_category_id').val('');
                        $('#shift_main_category_id').val(mainCategoryId);
                    }

                    $('#subCategoryDeleteForm').attr('action', deleteRoute);
                    $('#subCategoryDeleteForm').submit();
                }


            </script>


    @endpush
