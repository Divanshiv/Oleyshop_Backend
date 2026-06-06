@extends('layouts.admin.app')

@section('title', translate('Customer_Search_Analytics'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/analytics_logo.png') }}" class="w--20"
                        alt="{{ translate('analytics') }}">
                </span>
                <span>
                    {{ translate('Customer_Search_Analytics') }}
                </span>
            </h1>
        </div>

        <div class="row gy-3">
            <div class="col-lg-6 col-xl-7">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between gap-3 mb-20">
                            <div class="">
                                <h4 class="mb-1">{{ translate('Top 5 Customer') }}</h4>
                                <p class="fs-12 mb-0">{{ translate('According to search volume') }}</p>
                            </div>
                            <div class="select-wrap d-flex flex-wrap gap-10 min-w-180px">
                                <select class="form-control select2-custom-height js-select2-custom min-w180 h-30 top-customers__select">
                                    <option value="today"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'today' ? 'selected' : '' }}>
                                        {{ translate('Today') }}</option>
                                    <option value="all_time"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'all_time' ? 'selected' : '' }}>
                                        {{ translate('All_Time') }}</option>
                                    <option value="this_week"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'this_week' ? 'selected' : '' }}>
                                        {{ translate('This_Week') }}</option>
                                    <option value="last_week"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'last_week' ? 'selected' : '' }}>
                                        {{ translate('Last_Week') }}</option>
                                    <option value="this_month"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'this_month' ? 'selected' : '' }}>
                                        {{ translate('This_Month') }}</option>
                                    <option value="last_month"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'last_month' ? 'selected' : '' }}>
                                        {{ translate('Last_Month') }}</option>
                                    <option value="last_15_days"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'last_15_days' ? 'selected' : '' }}>
                                        {{ translate('Last_15_Days') }}</option>
                                    <option value="this_year"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'this_year' ? 'selected' : '' }}>
                                        {{ translate('This_Year') }}</option>
                                    <option value="last_year"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'last_year' ? 'selected' : '' }}>
                                        {{ translate('Last_Year') }}</option>
                                    <option value="last_6_month"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'last_6_month' ? 'selected' : '' }}>
                                        {{ translate('Last_6_Month') }}</option>
                                    <option value="this_year_1st_quarter"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'this_year_1st_quarter' ? 'selected' : '' }}>
                                        {{ translate('This_Year_1st_Quarter') }}</option>
                                    <option value="this_year_2nd_quarter"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'this_year_2nd_quarter' ? 'selected' : '' }}>
                                        {{ translate('This_Year_2nd_Quarter') }}</option>
                                    <option value="this_year_3rd_quarter"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'this_year_3rd_quarter' ? 'selected' : '' }}>
                                        {{ translate('This_Year_3rd_Quarter') }}</option>
                                    <option value="this_year_4th_quarter"
                                        {{ array_key_exists('date_range', $queryParams) && $queryParams['date_range'] == 'this_year_4th_quarter' ? 'selected' : '' }}>
                                        {{ translate('this_year_4th_quarter') }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="">
                                @if (count($graphData['search_volume']) < 1 && count($graphData['top_customers']) < 1)
                                    <div class="text-center py-5 h-100 w-100 d-center">
                                        <div class="text-center py-5 mt-lg-5">
                                            <img class="mb-2" width="65" src="{{ asset('public/assets/admin/img/no-data--found.png') }}" alt="{{ translate('logo') }}">
                                            <div class="w-100">
                                                {{ translate('No Data Found') }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div id="apex_donut-chart"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-5">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between gap-3">
                            <div class="">
                                <h4 class="mb-1">{{ translate('Top Products') }}</h4>
                                <p class="fs-12 m-0">{{ translate('According to search volume') }}</p>
                            </div>
                            <div class="select-wrap d-flex flex-wrap gap-10 min-w-180px">
                                <select class="form-control select2-custom-height js-select2-custom min-w180 h-30 top-services__select">
                                    <option value="today"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'today' ? 'selected' : '' }}>
                                        {{ translate('Today') }}</option>
                                    <option value="all_time"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'all_time' ? 'selected' : '' }}>
                                        {{ translate('All_Time') }}</option>
                                    <option value="this_week"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'this_week' ? 'selected' : '' }}>
                                        {{ translate('This_Week') }}</option>
                                    <option value="last_week"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'last_week' ? 'selected' : '' }}>
                                        {{ translate('Last_Week') }}</option>
                                    <option value="this_month"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'this_month' ? 'selected' : '' }}>
                                        {{ translate('This_Month') }}</option>
                                    <option value="last_month"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'last_month' ? 'selected' : '' }}>
                                        {{ translate('Last_Month') }}</option>
                                    <option value="last_15_days"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'last_15_days' ? 'selected' : '' }}>
                                        {{ translate('Last_15_Days') }}</option>
                                    <option value="this_year"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'this_year' ? 'selected' : '' }}>
                                        {{ translate('This_Year') }}</option>
                                    <option value="last_year"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'last_year' ? 'selected' : '' }}>
                                        {{ translate('Last_Year') }}</option>
                                    <option value="last_6_month"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'last_6_month' ? 'selected' : '' }}>
                                        {{ translate('Last_6_Month') }}</option>
                                    <option value="this_year_1st_quarter"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'this_year_1st_quarter' ? 'selected' : '' }}>
                                        {{ translate('This_Year_1st_Quarter') }}</option>
                                    <option value="this_year_2nd_quarter"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'this_year_2nd_quarter' ? 'selected' : '' }}>
                                        {{ translate('This_Year_2nd_Quarter') }}</option>
                                    <option value="this_year_3rd_quarter"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'this_year_3rd_quarter' ? 'selected' : '' }}>
                                        {{ translate('This_Year_3rd_Quarter') }}</option>
                                    <option value="this_year_4th_quarter"
                                        {{ array_key_exists('date_range_2', $queryParams) && $queryParams['date_range_2'] == 'this_year_4th_quarter' ? 'selected' : '' }}>
                                        {{ translate('this_year_4th_quarter') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-20">
                            <div class="">
                                <ul class="common-list max-h-390-with-scrolling after-none gap-4 d-flex flex-column list-unstyled">
                                    @forelse($topProducts as $item)
                                        @if ($item->product)
                                            <li>
                                                <div
                                                    class="mb-2 d-flex align-items-center justify-content-between gap-10 flex-wrap">
                                                    <span class="zone-name">{{ $item->product->name }}</span>
                                                    <span
                                                        class="booking-count">{{ number_format(($item['count'] * 100) / $total) }}%</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ ($item['count'] * 100) / $total }}%"
                                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </li>
                                        @endif
                                    @empty
                                        <li>
                                            <div class="text-center py-5 h-100 w-100 d-center">
                                                <div class="text-center py-5 mt-lg-5">
                                                    <img class="mb-2" width="65" src="{{ asset('public/assets/admin/img/no-data--found.png') }}" alt="{{ translate('logo') }}">
                                                    <div class="w-100">
                                                        {{ translate('No Data Found') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header flex-wrap gap-2 border-0">
                <form action="{{ request()->url() }}" method="GET">
                    @foreach (request()->except('search', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="input-group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                            placeholder="{{ translate('Search by customer info') }}" aria-label="Search"
                            value="{{ $search }}" autocomplete="off">

                        <div class="input-group-append h-30">
                            <button type="submit" class="input-group-text title-bg3 p-2 text-white">
                                <i class="tio-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="d-flex align-items-center gap-3">
                    <div class="hs-unfold">
                        <a class="js-hs-unfold-invoker export_btn h-30 text-dark btn btn-sm dropdown-toggle min-height-30"
                            href="javascript:;"
                            data-hs-unfold-options="{
                                    &quot;target&quot;: &quot;#usersExportDropdown3&quot;,
                                    &quot;type&quot;: &quot;css-animation&quot;
                                }"
                            data-hs-unfold-target="#usersExportDropdown" data-hs-unfold-invoker="">
                            <i class="tio-download-to title-clr3 top-02"></i>
                            Export
                            <i class="tio-down-ui fs-10 title-clr3"></i>
                        </a>

                        <div id="usersExportDropdown3"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right hs-unfold-content-initialized hs-unfold-css-animation animated hs-unfold-hidden"
                            data-hs-target-height="98.7188" data-hs-unfold-content=""
                            data-hs-unfold-content-animation-in="slideInUp" data-hs-unfold-content-animation-out="fadeOut"
                            style="animation-duration: 300ms;">
                            <span class="dropdown-header">{{ translate('Download Options') }}</span>
                            <a id="export-excel" class="dropdown-item"
                                href="{{ route('admin.analytics.customer.export.excel', ['search' => $search]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                    alt="Image Description">
                                Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table
                    class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="bg-card-common">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('Customer') }}</th>
                            <th class="text-center text-nowrap">{{ translate('Search') }} {{ translate('Volume') }}</th>
                            <th class="text-center text-nowrap">{{ translate('Related') }} {{ translate('Categories') }}</th>
                            <th class="text-center text-nowrap">{{ translate('Related') }} {{ translate('Products') }}</th>
                            <th class="text-center text-nowrap">{{ translate('Times Product') }} {{ translate('Visited') }}</th>
                            <th class="text-center text-nowrap">{{ translate('Total') }} {{ translate('Orders') }}</th>
                            <th class="text-center text-nowrap">{{ translate('Action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($customersData as $key => $value)
                            <tr>
                                <td>{{ $customersData->firstitem() + $key }}</td>
                                <td>
                                    <div class="media align-items-center gap-3 max-content">
                                        <div class="avatar avatar-lg">
                                            <a href="{{ $value->customer ? route('admin.customer.view', [$value->customer['id']]) : '#' }}"
                                                class="product-list-media">
                                                <img class="rounded-full"
                                                    src="{{ $value->customer->imageFullPath ?? asset('public/assets/admin/img/160x160/2.png') }}"
                                                    alt="{{ translate('customer') }}">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="title m-0 line--limit-1 text-wrap text-break max-w-250 min-w-160px">
                                                {{ $value->customer ? $value->customer['f_name'] . ' ' . $value->customer['l_name'] : 'Customer Deleted' }}
                                            </h5>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $value->search_volume ?? 0 }}</td>
                                <td class="text-center">
                                    <a href="#" data-toggle="tooltip" data-html="true" data-placement="right"
                                        title="
                                        <?php
                                        if (isset($value->related_category)) {
                                            $categories = json_decode($value->related_category);
                                            foreach ($categories as $category) {
                                                echo $category->category ? $category->category->name . '<br>' : '';
                                            }
                                        }
                                        ?>">
                                        {{ $value->related_category_count ?? 0 }}
                                    </a>
                                </td>
                                <td class="text-center">{{ $value->related_product_count ?? 0 }}</td>
                                <td class="text-center">{{ $value->visited_products_count ?? 0 }}</td>
                                <td class="text-center">{{ $value->orders_count ?? 0 }}</td>
                                <td>
                                    <div class="table-actions d-flex justify-content-center">
                                        <button class="action-btn" id="customer-{{ $value->user_id }}"
                                            type="button" data-toggle="modal"
                                            data-target="#customer-view-{{ $value->user_id }}"
                                            title="{{ translate('view') }}">
                                            <i class="tio-visible"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="">
                {!! $customersData->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>

            @if (count($customersData) == 0)
                <div class="text-center py-4 h-100 w-100 d-center">
                    <div class="text-center pb-5">
                        <img class="mb-2" width="65" src="{{ asset('public/assets/admin/img/no-data--found.png') }}" alt="{{ translate('logo') }}">
                        <div class="w-100">
                            {{ translate('No Data Found') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @foreach ($customersData as $key => $customer)
        <div class="modal fade" id="customer-view-{{ $customer->user_id }}" tabindex="-1">

            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $customer->Customer ? $customer->Customer['f_name'] . ' ' . $customer->Customer['l_name'] : 'Customer Deleted' }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="rest-part">
                        </div>
                        <div class="card-body pt-0">
                            <div>
                                <p><strong>{{ translate('Search Volume') }}:</strong> {{ $customer->search_volume }}</p>
                                <p><strong>{{ translate('Related Categories') }}:</strong>
                                    {{ $customer->related_category_count }}</p>
                                <ul>
                                    <?php
                                    if (isset($customer->related_category)) {
                                        $categories = json_decode($customer->related_category);
                                        foreach ($categories as $cat) {
                                            echo $cat->category ? $cat->category->name . '<br>' : '';
                                        }
                                    }
                                    ?>
                                </ul>
                                <p><strong>{{ translate('Related Products') }}:</strong>
                                    {{ $customer->related_product_count }}</p>
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="button" class="btn btn--danger text-white" data-dismiss="modal"
                                    aria-label="Close">
                                    {{ translate('close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('script_2')
    <script src="{{ asset('/public/assets/admin/js/apex-charts/apexcharts.js') }}"></script>

    <script>
        "use strict";

        var options = {
            series: @json($graphData['search_volume']),
            chart: {
                type: 'donut',
                width: "100%",
                height: 400
            },
            labels: @json(count($graphData['top_customers']) > 0 ? $graphData['top_customers'] : ''),
            legend: {
                show: true,
                floating: false,
                fontSize: '14px',
                position: 'right',
                horizontalAlign: 'center',
                offsetY: -10,
                itemMargin: {
                    horizontal: 5,
                    vertical: 10
                },
            },
            responsive: [{
                breakpoint: 1400,
                options: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#apex_donut-chart"), options);
        chart.render();
    </script>

    <script>
        $(".top-customers__select").on('change', function() {
            if (this.value !== "") location.href = "{{ route('admin.analytics.customer-search') }}" +
                '?date_range=' + this.value + '&date_range_2=' +
                '{{ $queryParams['date_range_2'] ?? 'all_time' }}';
        });
        $(".top-services__select").on('change', function() {
            if (this.value !== "") location.href = "{{ route('admin.analytics.customer-search') }}" +
                '?date_range=' + '{{ $queryParams['date_range'] ?? 'all_time' }}' + '&date_range_2=' + this.value;
        });
    </script>
@endpush
