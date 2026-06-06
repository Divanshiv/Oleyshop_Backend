@extends('layouts.admin.app')

@section('title', translate('Product List'))

@php
    use App\CentralLogics\Helpers;
@endphp

@section('content')
    <div class="content container-fluid product-list-page">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/products.png') }}" class="w--24" alt="">
                </span>
                <span>
                    {{ translate('product List') }}
                    <span class="badge badge-soft-secondary">{{ $products->total() }}</span>
                </span>
            </h1>
        </div>

        <div class="card">
            <div class="card--header order-top">
                <div class="d-flex gap-2 align-items-center">
                    <form action="{{ request()->url() }}" method="GET">
                        @foreach (request()->except('search', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control"
                                placeholder="{{ translate('Search_by_ID_or_Name') }}" aria-label="Search"
                                value="{{ $search }}" autocomplete="off">

                            <div class="input-group-append">
                                <button type="submit" class="input-group-text p-2">
                                    <i class="tio-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="d-flex flex-sm-nowrap flex-wrap gap-sm-3 gap-3">
                    <div class="hs-unfold">
                        <a class="js-hs-unfold-invoker export_btn h-30 text-dark btn btn-sm dropdown-toggle min-height-30"
                            href="javascript:;"
                            data-hs-unfold-options="{
                                    &quot;target&quot;: &quot;#usersExportDropdown&quot;,
                                    &quot;type&quot;: &quot;css-animation&quot;
                                }"
                            data-hs-unfold-target="#usersExportDropdown" data-hs-unfold-invoker="">
                            <i class="tio-download-to title-clr3 top-02"></i>
                            {{ translate('export') }}
                            <i class="tio-down-ui fs-10 title-clr3"></i>
                        </a>

                        <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right hs-unfold-content-initialized hs-unfold-css-animation animated hs-unfold-hidden"
                            data-hs-target-height="98.7188" data-hs-unfold-content=""
                            data-hs-unfold-content-animation-in="slideInUp" data-hs-unfold-content-animation-out="fadeOut"
                            style="animation-duration: 300ms;">
                            <span class="dropdown-header">{{ translate('Download Options') }}</span>
                            <a id="export-excel" class="dropdown-item"
                                href="{{ route('admin.product.bulk-export', ['search' => $search]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                    alt="Image Description">
                                Excel
                            </a>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('admin.product.add-new') }}"
                            class="btn btn-primary min-height-30 py-1 h-30 fs-12px"><i class="tio-add"></i>
                            {{ translate('add new product') }}
                        </a>
                    </div>

                    <div>
                        <a href="{{ route('admin.product.limited-stock') }}"
                            class="btn btn--primary-2 min-height-30 h-30 fs-12px px-3">{{ translate('limited stocks') }}</a>
                    </div>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('Product Name') }}</th>
                            <th>{{ translate('Selling price') }}</th>
                            <th class="text-center">{{ translate('Total sale') }}</th>
                            <th class="">{{ translate('Quantity') }}</th>
                            <th class="text-center">{{ translate('Show in daily needs') }}</th>
                            <th class="text-center">{{ translate('Featured') }}</th>
                            <th class="text-center">{{ translate('Status') }}</th>
                            <th class="text-center">{{ translate('Action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($products as $key => $product)
                            @php
                                $variationsArray = json_decode($product->variations, true);
                                $variationCount = is_array($variationsArray) ? count($variationsArray) : 0;
                            @endphp

                            <tr class="position-relative table-custom-tr">
                                <td class="text-dark pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    {{ $products->firstItem() + $key }}
                                </td>

                                <td class="pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn p-0 collapse-btn"
                                            @if ($variationCount == 0) disabled @endif>
                                            <span
                                                class="rounded-circle icon @if ($variationCount == 0) bg-white @endif">
                                                <i
                                                    class="tio-chevron-down top-02 @if ($variationCount == 0) d-none @endif"></i>
                                            </span>
                                        </button>

                                        <a href="{{ route('admin.product.view', [$product['id']]) }}"
                                            class="product-list-media">
                                            @if (!empty(json_decode($product['image'], true)))
                                                <img src="{{ $product->identityImageFullPath[0] }}">
                                            @else
                                                <img src="{{ asset('public/assets/admin/img/400x400/img2.jpg') }}">
                                            @endif

                                            <span class="name min-w-180px">
                                                <h6 class="line--limit-1 mb-0">
                                                    {{ \Illuminate\Support\Str::limit($product['name'], 20, $end = '...') }}
                                                </h6>

                                                @if ($variationCount > 0)
                                                    <span class="fs-12px d-block text-gray">
                                                        {{ $variationCount }} {{ translate('Variants') }}
                                                    </span>
                                                @endif
                                            </span>
                                        </a>
                                    </div>
                                </td>

                                <td class="text-dark pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    <div class="max-85">
                                        {{ Helpers::set_symbol($product['price']) }}
                                    </div>
                                </td>

                                <td class="text-dark text-center">
                                    {{ $product->total_sold }}
                                </td>

                                <td class="text-dark pt-1 pb-3">
                                    <div class="d-flex align-items-center product-quantity">
                                        {{ $product->total_stock }}

                                        <button class="btn py-0 px-2 fs-18" id="{{ $product->id }}"
                                            onclick="update_quantity({{ $product->id }})" type="button"
                                            data-toggle="modal" data-target="#update-quantity"
                                            title="{{ translate('update_quantity') }}">
                                            <i class="tio-add-circle c1"></i>
                                        </button>
                                    </div>
                                </td>

                                <td class="text-dark pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    <div class="text-center">
                                        <label class="switch my-0">
                                            <input type="checkbox" class="status"
                                                onchange="daily_needs('{{ $product['id'] }}','{{ $product->daily_needs == 1 ? 0 : 1 }}')"
                                                id="{{ $product['id'] }}"
                                                {{ $product->daily_needs == 1 ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </td>

                                <td class="pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox"
                                            onclick="featured_status_change_alert('{{ route('admin.product.feature', [$product->id, $product->is_featured ? 0 : 1]) }}', '{{ $product->is_featured ? translate('want to remove from featured product') : translate('want to add in featured product') }}', event)"
                                            class="toggle-switch-input" id="stocksCheckbox{{ $product->id }}"
                                            {{ $product->is_featured ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>

                                <td class="pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox"
                                            onclick="status_change_alert('{{ route('admin.product.status', [$product->id, $product->status ? 0 : 1]) }}', '{{ $product->status ? translate('you want to disable this product') : translate('you want to active this product') }}', event)"
                                            class="toggle-switch-input" id="stocksCheckbox{{ $product->id }}"
                                            {{ $product->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>

                                <td class="pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn" href="{{ route('admin.product.view', [$product['id']]) }}">
                                            <i class="tio-visible-outlined"></i>
                                        </a>

                                        <a class="action-btn" href="{{ route('admin.product.edit', [$product['id']]) }}">
                                            <i class="tio-edit"></i>
                                        </a>

                                        <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('product-{{ $product['id'] }}','{{ translate('Want to delete this') }}')">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>

                                    <form action="{{ route('admin.product.delete', [$product['id']]) }}" method="post"
                                        id="product-{{ $product['id'] }}">
                                        @csrf @method('delete')
                                    </form>
                                </td>
                            </tr>

                            <!-- This Table Tr hide and collapse -->
                            <tr class="table-collapse-body d-none">
                                <td class="table-item-data collapse-content bg-light" colspan="100%">
                                    <div class="py-3 table-data-items-space">
                                        <div class="row g-3">
                                            @foreach ($variationsArray as $variation)
                                                <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                                    <div class="rounded p-2 border bg-white d-flex align-items-center gap-2"
                                                        style="border: 1px solid red">

                                                        @if (!empty(json_decode($product['image'], true)))
                                                            <img width="40" height="40" class="rounded"
                                                                src="{{ $product->identityImageFullPath[0] }}"
                                                                alt="{{ translate('product image') }}">
                                                        @else
                                                            <img width="40" height="40" class="rounded"
                                                                src="{{ asset('public/assets/admin/img/400x400/img2.jpg') }}"
                                                                alt="{{ translate('product image') }}">
                                                        @endif

                                                        <div class="d-flex flex-column">
                                                            <div class="item d-flex flex-wrap">
                                                                <span class="fs-12px d-block text-gray">
                                                                    {{ translate('Variant') }}&nbsp;:
                                                                </span>

                                                                <span class="fs-12px d-block text-dark">
                                                                    {{ $variation['type'] }}
                                                                </span>
                                                            </div>

                                                            <div class="cont d-flex flex-wrap">
                                                                <div class="item d-flex align-items-center ">
                                                                    <span class="fs-12px d-block text-gray">
                                                                        {{ translate('Stock') }} :
                                                                    </span>

                                                                    <span class="fs-12px d-block text-dark">
                                                                        {{ $variation['stock'] }}
                                                                    </span>
                                                                </div>

                                                                <div class="item d-flex align-items-center ">
                                                                    <span class="fs-12px d-block text-gray">
                                                                        {{ translate('Price') }} :
                                                                    </span>
                                                                    <span class="fs-12px d-block text-dark">
                                                                        {{ Helpers::set_symbol($variation['price']) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <!-- End -->
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $products->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>

            @if (count($products) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                        alt="Image Description">
                    <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Update Product Quantity Modal -->
    <div class="modal fade" id="update-quantity" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.product.update-quantity') }}" method="post">
                    <div class="modal-body p-0">
                        @csrf
                        <div class="rest-part"></div>
                        <div class="card-body pt-0">
                            <div class="btn--container justify-content-end">
                                <button type="button" class="btn btn--danger text-white" data-dismiss="modal"
                                    aria-label="Close">
                                    {{ translate('close') }}
                                </button>
                                <button class="btn btn--primary" type="submit">{{ translate('submit') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ translate('Are you sure?') }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#107980',
                cancelButtonText: '{{ translate('No') }}',
                confirmButtonText: '{{ translate('Yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
    </script>

    <script>
        function featured_status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ translate('Are you sure?') }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#107980',
                cancelButtonText: '{{ translate('No') }}',
                confirmButtonText: '{{ translate('Yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
    </script>

    <script>
        function daily_needs(id, status) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.product.daily-needs') }}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function() {
                    toastr.success('{{ translate('Daily need status updated successfully') }}');
                }
            });
        }
    </script>

    <script>
        function update_quantity(val) {
            $.get({
                url: '{{ url('/') }}/admin/product/get-variations?id=' + val,
                dataType: 'json',
                success: function(data) {
                    $('.rest-part').empty().html(data.view);
                },
            });
        }

        function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="qty_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if (qty_elements.length > 0) {

                $('input[name="total_stock"]').attr("readonly", true);
                $('input[name="total_stock"]').val(total_qty);
            } else {
                $('input[name="total_stock"]').attr("readonly", false);
            }
        }
    </script>
@endpush
