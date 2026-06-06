@extends('layouts.admin.app')

@section('title', translate('Limited Stocks'))

@section('content')
    <div class="content container-fluid product-list-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/products.png') }}" class="w--24" alt="">
                </span>
                <span>
                    {{ translate('Limited Stocks') }}
                    <span class="badge badge-soft-secondary">{{ $products->total() }}</span>
                </span>
            </h1>
            <p class="d-flex">{{ translate('the_products_are_shown_in_this_list,_which_quantity_is_below') }}
                {{ $stockLimit }}</p>
        </div>

        <div class="card">
            <div class="card--header order-top">
                <div class="d-flex gap-2 align-items-center">
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
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('#') }}</th>
                            <th>{{ translate('product_name') }}</th>
                            <th>{{ translate('selling_price') }}</th>
                            <th class="">{{ translate('quantity') }}</th>
                            <th class="text-center">{{ translate('status') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                        @foreach ($products as $key => $product)
                            <tr>
                                <td class="pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    {{ $products->firstItem() + $key }}</td>
                                <td class="pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    <a href="{{ route('admin.product.view', [$product['id']]) }}"
                                        class="product-list-media">
                                        @if (!empty(json_decode($product['image'], true)))
                                            <img src="{{ $product->identityImageFullPath[0] }}">
                                        @else
                                            <img src="{{ asset('public/assets/admin/img/400x400/img2.jpg') }}">
                                        @endif
                                        <h6 class="name line--limit-2">
                                            {{ \Illuminate\Support\Str::limit($product['name'], 20, $end = '...') }}
                                        </h6>
                                    </a>
                                </td>
                                <td class="pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    <div class="max-85 text-right">
                                        {{ Helpers::set_symbol($product['price']) }}
                                    </div>
                                </td>
                                <td class="pt-1 pb-3">
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
                                <td class="pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox"
                                            onclick="status_change_alert('{{ route('admin.product.status', [$product->id, $product->status ? 0 : 1]) }}', '{{ $product->status ? translate('you_want_to_disable_this_product') : translate('you_want_to_active_this_product') }}', event)"
                                            class="toggle-switch-input" id="stocksCheckbox{{ $product->id }}"
                                            {{ $product->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td class="pt-1 pb-3  {{ $key == 0 ? 'pt-4' : '' }}">
                                    <!-- Dropdown -->
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn" href="{{ route('admin.product.edit', [$product['id']]) }}">
                                            <i class="tio-edit"></i></a>
                                        <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('product-{{ $product['id'] }}','{{ translate('Want to delete this') }}')">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                    <form action="{{ route('admin.product.delete', [$product['id']]) }}" method="post"
                                        id="product-{{ $product['id'] }}">
                                        @csrf @method('delete')
                                    </form>
                                    <!-- End Dropdown -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

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
            <!-- End Table -->
        </div>
    </div>

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
