@extends('layouts.admin.app')

@section('title', translate('Sale Report'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="media align-items-center">
                <img class="w--20" src="{{ asset('public/assets/admin') }}/img/sale-report.png" alt="Image Description">
                <div class="media-body pl-3">
                    <h1 class="page-header-title mb-1">{{ translate('sale') }} {{ translate('report') }}</h1>
                </div>
            </div>
        </div>

        <div>
            <div class="card mb-4">
                <div class="card-body">
                    <h3>{{ translate('Filter Data') }}</h3>

                    <form id="sale_report_form_id" class="w-100">
                        <div class="row g-3 g-sm-4 g-md-3 g-lg-4 mt-2">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label for="branch_id" class="input-label">{{ translate('Select Branch') }}</label>
                                <select class="custom-select custom-select-sm text-capitalize min-h-45px" name="branch_id"
                                    id="branch_id">
                                    <option disabled selected>--- {{ translate('select') }} {{ translate('branch') }} ---
                                    </option>
                                    <option value="all" {{ is_null($branchId) || $branchId == 'all' ? 'selected' : '' }}>
                                        {{ translate('all') }} {{ translate('branch') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch['id'] }}"
                                            {{ $branch['id'] == $branchId ? 'selected' : '' }}>{{ $branch['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label for="date_range" class="input-label">{{ translate('Date Range') }}</label>

                                <select id="date_range" name="date_range" class="custom-select">
                                    <option value="{{ ALL_TIME }}"
                                        {{ (request('date_range') ?? ALL_TIME) == ALL_TIME ? 'selected' : '' }}>
                                        {{ translate(ALL_TIME) }}</option>
                                    <option value="{{ THIS_YEAR }}"
                                        {{ request('date_range') == THIS_YEAR ? 'selected' : '' }}>
                                        {{ translate(THIS_YEAR) }}</option>
                                    <option value="{{ THIS_MONTH }}"
                                        {{ request('date_range') == THIS_MONTH ? 'selected' : '' }}>
                                        {{ translate(THIS_MONTH) }}</option>
                                    <option value="{{ THIS_WEEK }}"
                                        {{ request('date_range') == THIS_WEEK ? 'selected' : '' }}>
                                        {{ translate(THIS_WEEK) }}</option>
                                    <option value="{{ CUSTOM_DATE }}"
                                        {{ request('date_range') == CUSTOM_DATE ? 'selected' : '' }}>
                                        {{ translate(CUSTOM_DATE) }}</option>
                                </select>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label class="input-label" for="start_date">{{ translate('Start Date') }}</label>
                                <div class="input-date-group">
                                    <label class="input-date">
                                        <input type="text" id="start_date" name="start_date" value="{{ $startDate }}"
                                            class="js-flatpickr form-control flatpickr-custom min-h-45px"
                                            placeholder="{{ translate('yy-mm-dd') }}"
                                            data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                                    </label>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label class="input-label" for="end_date">{{ translate('End Date') }}</label>
                                <div class="input-date-group">
                                    <label class="input-date">
                                        <input type="text" id="end_date" name="end_date" value="{{ $endDate }}"
                                            class="js-flatpickr form-control flatpickr-custom min-h-45px"
                                            placeholder="{{ translate('yy-mm-dd') }}"
                                            data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                                    </label>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <a href="{{ route('admin.report.sale-report') }}" id=""
                                        class="btn btn--reset min-w-120px">{{ translate('Reset') }}</a>

                                    <button type="submit" id="show_filter_data"
                                        class="btn btn--primary min-w-120px">{{ translate('Filter') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="report--data">
                        <div class="row g-xxl-4 g-3">
                            <div class="col-sm-6 col-lg-4">
                                <div class="order--card color_card pink h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="card-title m-0 text-dark text-success mb-2" id="order_count">
                                                {{ $totalOrders }}
                                            </span>

                                            <h6
                                                class="card-subtitle fs-12px font-medium d-flex justify-content-between m-0 align-items-center">
                                                <span>{{ translate('total orders') }}</span>
                                            </h6>
                                        </div>

                                        <div
                                            class="icon w-50px h-50px bg-white rounded-pill d-flex align-items-center justify-contnet-center">
                                            <img src="{{ asset('public/assets/admin/img/order-bag.svg') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="order--card color_card warning h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="card-title m-0 text-dark text-success mb-2" id="item_count">
                                                {{ $totalQuantity }}
                                            </span>

                                            <h6
                                                class="card-subtitle fs-12px font-medium d-flex justify-content-between m-0 align-items-center">
                                                <span>{{ translate('total item sold') }}</span>
                                            </h6>
                                        </div>

                                        <div
                                            class="icon w-50px h-50px bg-white rounded-pill d-flex align-items-center justify-contnet-center">
                                            <img src="{{ asset('public/assets/admin/img/order-item.svg') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="order--card color_card success h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="card-title m-0 text-dark text-success mb-2" id="order_amount">
                                                {{ Helpers::set_symbol($totalAmount) }}
                                            </span>

                                            <h6
                                                class="card-subtitle fs-12px font-medium d-flex justify-content-between m-0 align-items-center">
                                                <span>{{ translate('total sale amount') }}</span>
                                            </h6>
                                        </div>

                                        <div
                                            class="icon w-50px h-50px bg-white rounded-pill d-flex align-items-center justify-contnet-center">
                                            <img src="{{ asset('public/assets/admin/img/order-amount.svg') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card--header order-top">
                    <div class="d-flex gap-2 align-items-center">
                        <h3 class="mb-0"> {{ translate('Product List') }}</h3>
                    </div>

                    <div class="d-flex flex-sm-nowrap flex-wrap gap-sm-3  gap-3">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                                    placeholder="{{ translate('Search by Product id,name') }}" aria-label="Search"
                                    value="{{ $search }}" autocomplete="off">



                                <div class="input-group-append h-30">
                                    <button type="submit" class="input-group-text title-bg3 p-2 text-white">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker export_btn h-30 text-dark btn btn-sm dropdown-toggle min-height-30"
                                href="javascript:;"
                                data-hs-unfold-options="{
                                    &quot;target&quot;: &quot;#usersExportDropdown&quot;,
                                    &quot;type&quot;: &quot;css-animation&quot;
                                }"
                                data-hs-unfold-target="#usersExportDropdown" data-hs-unfold-invoker="">
                                <i class="tio-download-to title-clr3 top-02"></i>
                                Export
                                <i class="tio-down-ui fs-10 title-clr3"></i>
                            </a>

                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right hs-unfold-content-initialized hs-unfold-css-animation animated hs-unfold-hidden"
                                data-hs-target-height="98.7188" data-hs-unfold-content=""
                                data-hs-unfold-content-animation-in="slideInUp"
                                data-hs-unfold-content-animation-out="fadeOut" style="animation-duration: 300ms;">
                                <span class="dropdown-header">Download
                                    Options</span>
                                <a id="export-excel" class="dropdown-item"
                                    href="{{ route('admin.report.export-sale-report', ['branch_id' => $branchId, 'date_range' => $dateRange, 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                        alt="Image Description">
                                    Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" id="set-rows">
                    <table
                        class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ translate('#') }} </th>
                                <th>{{ translate('product info') }}</th>
                                <th class="text-center">{{ translate('Total Sale Qty') }}</th>
                                <th>{{ translate('Total sale Amount ($)') }}</th>
                                <th>{{ translate('Average Sale Price ($)') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($products as $key => $detail)
                                @php
                                    $product_info = json_decode($detail->product_details, true);
                                @endphp

                                <tr>
                                    <td class="text-dark">
                                        {{ $products->firstItem() + $key }}
                                    </td>

                                    <td>
                                        @if ($detail->product)
                                            <a href="{{ route('admin.product.view', [$detail->product_id]) }}"
                                                target="_blank" class="product-list-media">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img
                                                        src="{{ !empty(json_decode($detail->product['image'], true))
                                                            ? $detail->product->identityImageFullPath[0]
                                                            : asset('public/assets/admin/img/160x160/2.png') }}">

                                                    <h5 class="m-0">
                                                        {{ $detail->product->name }}
                                                    </h5>
                                                </div>
                                            </a>
                                        @else
                                            <span
                                                class="product-list-media d-inline-flex align-items-center gap-3 text-muted"
                                                data-toggle="tooltip" data-placement="top"
                                                title="This product has been deleted">
                                                <img src="{{ asset('public/assets/admin/img/160x160/2.png') }}">
                                                <h5 class="m-0">
                                                    {{ $product_info['name'] ?? 'Unknown Product' }}
                                                </h5>
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-dark text-center">
                                        {{ $detail['total_quantity'] }}
                                    </td>

                                    <td class="text-dark">
                                        {{ Helpers::set_symbol($detail['total_amount']) }}
                                    </td>

                                    <td class="text-dark">
                                        {{ Helpers::set_symbol($detail['avg_price']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div>
                        {!! $products->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
                    </div>

                    @if (count($products) === 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-120px"
                                src="{{ asset('public/assets/admin/svg/illustrations/sorry.svg') }}"
                                alt="{{ translate('Image Description') }}">
                            <p class="mb-0">{{ translate('No data to show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/flatpicker.js') }}"></script>

    <script>
        $('#sale_report_form_id').on('submit', function(e) {
            if ($('#date_range').val() === '{{ CUSTOM_DATE }}') {
                if (!$('#start_date').val() || !$('#end_date').val()) {
                    toastr.error('Please select start and end dates.');
                    e.preventDefault();
                }
            }
        });
    </script>
@endpush
