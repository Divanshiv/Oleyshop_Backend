@extends('layouts.admin.app')

@section('title', translate('Add new banner'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/banner.png') }}" class="w--20"
                        alt="{{ translate('banner') }}">
                </span>
                <span>
                    {{ translate('banner setup') }}
                </span>
            </h1>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('admin.banner.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('title') }}</label>
                                        <input type="text" name="title" value="{{ old('title') }}"
                                            class="form-control" placeholder="{{ translate('New banner') }}" maxlength="255"
                                            required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{ translate('item') }}
                                            {{ translate('type') }}<span class="input-label-secondary">*</span></label>
                                        <!-- Add an empty placeholder so initial state can be 'none selected' -->
                                        <select name="item_type" class="form-control show-item" required>
                                            <option value="">{{ translate('select type') }}</option>
                                            <option value="product" {{ old('item_type') == 'product' ? 'selected' : '' }}>{{ translate('product') }}</option>
                                            <option value="category" {{ old('item_type') == 'category' ? 'selected' : '' }}>{{ translate('category') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0" id="type-product">
                                        <label class="input-label" for="exampleFormControlSelect1">{{ translate('product') }} <span class="input-label-secondary">*</span></label>
                                        <select name="product_id" class="form-control js-select2-custom">
                                            <option value="">{{ translate('select product') }}</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product['id'] }}" {{ old('product_id') == $product['id'] ? 'selected' : '' }}>{{ $product['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-0" id="type-category" style="display: none">
                                        <label class="input-label"
                                            for="exampleFormControlSelect1">{{ translate('category') }} <span
                                                class="input-label-secondary">*</span></label>
                                        <select name="category_id" class="form-control js-select2-custom">
                                            <option value="">{{ translate('select category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category['id'] }}" {{ old('category_id') == $category['id'] ? 'selected' : '' }}>{{ $category['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column justify-content-center h-100">
                                <h5 class="text-center mb-3 text--title text-capitalize">
                                    {{ translate('banner') }} {{ translate('image') }}
                                    <small class="text-danger">*</small>
                                </h5>
                                <label class="upload--vertical">
                                    <input type="file" name="image" id="customFileEg1" class=""
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           hidden
                                           data-maxFileSize="{{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}">
                                    <img id="viewer" src="{{ asset('public/assets/admin/img/upload-vertical.png') }}"
                                        alt="{{ translate('banner image') }}" />
                                </label>
                                <p class="fs-10 m-0 text-center mt-3">
                                    {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} : Max {{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}
                                    <span class="text-dark font-semibold">(2:1)</span>
                                </p>
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
                    <h5 class="mb-0"> {{ translate('Banner List') }}
                        <span class="badge badge-soft-dark rounded-pill fs-10 ml-1">{{ $banners->total() }}</span>
                    </h5>
                </div>

                <div class="d-flex flex-sm-nowrap flex-wrap gap-sm-3 gap-3">
                    <form action="{{ request()->url() }}" method="GET">
                        @foreach (request()->except('search', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                                placeholder="{{ translate('Search by ID or Name') }}" aria-label="Search"
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
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{ translate('#') }}</th>
                            <th class="border-0">{{ translate('banner image') }}</th>
                            <th class="border-0">{{ translate('title') }}</th>
                            <th class="border-0">{{ translate('banner type') }}</th>
                            <th class="text-center border-0">{{ translate('status') }}</th>
                            <th class="text-center border-0">{{ translate('action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($banners as $key => $banner)
                            <tr>
                                <td>{{ $banners->firstItem() + $key }}</td>
                                <td>
                                    <div>
                                        <img class="img-vertical-150" src="{{ $banner->imageFullPath }}"
                                            alt="{{ translate('banner image') }}">
                                    </div>
                                </td>
                                <td>
                                    <span class="d-block font-size-sm text-body text-trim-25">
                                        {{ $banner['title'] }}
                                    </span>
                                </td>
                                <td>
                                    @if ($banner['product_id'])
                                        {{ translate('Product') }} :
                                        {{ $banner->product ? $banner->product->name : '' }}
                                    @elseif($banner['category_id'])
                                        {{ translate('Category') }} :
                                        {{ $banner->category ? $banner->category->name : '' }}
                                    @endif
                                </td>
                                <td>
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox" class="toggle-switch-input status-change-alert"
                                            id="stocksCheckbox{{ $banner->id }}"
                                            data-route="{{ route('admin.banner.status', [$banner->id, $banner->status ? 0 : 1]) }}"
                                            data-message="{{ $banner->status ? translate('you_want_to_disable_this_banner') : translate('you_want_to_active_this_banner') }}"
                                            {{ $banner->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn" href="{{ route('admin.banner.edit', [$banner['id']]) }}"><i
                                                class="tio-edit"></i></a>
                                        <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                            data-id="banner-{{ $banner['id'] }}"
                                            data-message="{{ translate('Want to delete this') }}">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                    <form action="{{ route('admin.banner.delete', [$banner['id']]) }}" method="post"
                                        id="banner-{{ $banner['id'] }}">
                                        @csrf @method('delete')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $banners->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>

            @if (count($banners) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                        alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/banner.js') }}"></script>
    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
            toastr.error("{{ $error }}");
            @endforeach
        </script>
    @endif

    <script>
        $(document).ready(function() {
            const $form = $('form');
            const $itemType = $form.find('select[name="item_type"]');
            const $productGroup = $("#type-product");
            const $categoryGroup = $("#type-category");
            const $productSelect = $form.find('select[name="product_id"]');
            const $categorySelect = $form.find('select[name="category_id"]');

            function updateItemTypeView() {
                const val = $itemType.val();

                if (!val) {
                    // If no explicit item_type but a product/category was previously selected via old(), use it
                    if ($productSelect.val() && $productSelect.val() !== '') {
                        $itemType.val('product');
                    } else if ($categorySelect.val() && $categorySelect.val() !== '') {
                        $itemType.val('category');
                    }
                }

                const current = $itemType.val();

                if (current === 'product') {
                    $productGroup.show();
                    $categoryGroup.hide();
                    $productSelect.attr('required', true);
                    $categorySelect.removeAttr('required');
                } else if (current === 'category') {
                    $productGroup.hide();
                    $categoryGroup.show();
                    $categorySelect.attr('required', true);
                    $productSelect.removeAttr('required');
                } else {
                    // nothing selected: hide both and clear required
                    $productGroup.hide();
                    $categoryGroup.hide();
                    $productSelect.removeAttr('required');
                    $categorySelect.removeAttr('required');
                }
            }

            // initialize on load
            updateItemTypeView();

            // when item type changes
            $itemType.on('change', function() {
                updateItemTypeView();
                // reset selects when switching type to avoid stale selection
                if ($(this).val() === 'product') {
                    $categorySelect.prop('selectedIndex', 0).trigger('change');
                } else if ($(this).val() === 'category') {
                    $productSelect.prop('selectedIndex', 0).trigger('change');
                }
            });

            $('form').on('reset', function() {
                // Reset item type to placeholder and update view
                const $form = $(this);
                const $itemTypeInner = $form.find('select[name="item_type"]');
                $itemTypeInner.prop('selectedIndex', 0).trigger('change');

                // Reset product & category selects
                $form.find('select[name="product_id"]').prop('selectedIndex', 0).trigger('change');
                $form.find('select[name="category_id"]').prop('selectedIndex', 0).trigger('change');
                updateItemTypeView();
            });
        });
    </script>
@endpush
