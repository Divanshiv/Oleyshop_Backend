@extends('layouts.admin.app')

@section('title', translate('Expense Report'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="media align-items-center">
                <img src="{{ asset('public/assets/admin/img/expense_report.png') }}" class="w--20" alt="">
                <div class="media-body pl-3">
                    <h1 class="page-header-title mb-1">{{ translate('expense') }} {{ translate('report') }}</h1>
                </div>
            </div>
        </div>

        <div>
            <div class="card mb-3">
                <div class="card-body">
                    <form class="w-100">
                        <h4 class="mb-3">{{ translate('Filter_Data') }}</h4>

                        <div class="row g-3 g-sm-4 g-md-3 g-lg-4 align-items-end">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label class="input-label" for="end_range">{{ translate('Date Range') }}</label>
                                <select class="form-control __form-control" name="date_type" id="date_type">
                                    <option value="this_year" {{ $dateType == 'this_year' ? 'selected' : '' }}>
                                        {{ translate('This Year') }}
                                    </option>
                                    <option value="this_month" {{ $dateType == 'this_month' ? 'selected' : '' }}>
                                        {{ translate('This Month') }}
                                    </option>
                                    <option value="this_week" {{ $dateType == 'this_week' ? 'selected' : '' }}>
                                        {{ translate('This Week') }}
                                    </option>
                                    <option value="custom_date" {{ $dateType == 'custom_date' ? 'selected' : '' }}>
                                        {{ translate('Custom Date') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3" id="start_date_div">
                                <label class="input-label" for="start_date">{{ translate('Start Date') }}</label>
                                <input type="text" id="start_date" name="start_date" value="{{ $startDate }}"
                                    class="js-flatpickr form-control flatpickr-custom min-h-45px"
                                    placeholder="{{ translate('yy-mm-dd') }}"
                                    data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3" id="end_date_div">
                                <label class="input-label" for="end_date">{{ translate('End Date') }}</label>
                                <input type="text" id="end_date" name="end_date" value="{{ $endDate }}"
                                    class="js-flatpickr form-control flatpickr-custom min-h-45px"
                                    placeholder="{{ translate('yy-mm-dd') }}"
                                    data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 __btn-row">
                                <button type="submit" id="show_filter_data"
                                    class="btn w-100 btn--primary min-h-45px">{{ translate('show data') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3">
                        <div class="flex-grow-1 color_card">
                            <div class="info">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="tio-trending-up"></i>
                                    <h4 class="mb-0 fs-24">{{ \App\CentralLogics\Helpers::set_symbol($totalExpense) }}</h4>
                                </div>
                                <div class="subtext"><span>{{ translate('Total_Expense') }}</span></div>
                            </div>
                            <div class="circle">
                                <img src="{{ asset('/public/assets/admin/img/expense.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="flex-grow-1 color_card warning">
                            <div class="info">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="tio-trending-up"></i>
                                    <h4 class="mb-0 fs-24">{{ \App\CentralLogics\Helpers::set_symbol($extraDiscount) }}
                                    </h4>
                                </div>
                                <div class="subtext">{{ translate('Extra Discount') }}</div>
                            </div>
                            <div class="circle">
                                <img src="{{ asset('/public/assets/admin/img/free-delivery.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="flex-grow-1 color_card success">
                            <div class="info">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="tio-trending-up"></i>
                                    <h4 class="mb-0 fs-24">{{ \App\CentralLogics\Helpers::set_symbol($freeDelivery) }}</h4>
                                </div>
                                <div class="subtext">{{ translate('Free_Delivery') }}</div>
                            </div>
                            <div class="circle">
                                <img src="{{ asset('/public/assets/admin/img/free-delivery.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="flex-grow-1 color_card info">
                            <div class="info">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="tio-trending-up"></i>
                                    <h4 class="mb-0 fs-24">{{ \App\CentralLogics\Helpers::set_symbol($couponDiscount) }}
                                    </h4>
                                </div>
                                <div class="subtext"><span>{{ translate('Coupon_Discount') }}</span></div>
                            </div>
                            <div class="circle">
                                <img src="{{ asset('/public/assets/admin/img/coupon-discount.svg') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="center-chart-area">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <img width="20" src="{{ asset('/public/assets/admin/img/order-statistics.png') }}"
                                alt="">
                            <h3 class="mb-0">{{ translate('Expense Statistics') }}</h3>
                        </div>
                        <canvas id="updatingData" class="store-center-chart"
                            data-hs-chartjs-options='{
                                                        "type": "bar",
                                                        "data": {
                                                            "labels": [{{ '"' . implode('","', array_keys($expenseTransactionChart['discount_amount'])) . '"' }}],
                                                            "datasets": [{
                                                                "label": "{{ \App\CentralLogics\translate('total_expense_amount') }}",
                                                                "data": [{{ '"' . implode('","', array_values($expenseTransactionChart['discount_amount'])) . '"' }}],
                                                                "backgroundColor": "#a2ceee",
                                                                "hoverBackgroundColor": "#0177cd",
                                                                "borderColor": "#a2ceee"
                                                            }]
                                                        },
                                                        "options": {
                                                            "scales": {
                                                                "yAxes": [{
                                                                    "gridLines": {
                                                                        "color": "#e7eaf3",
                                                                        "drawBorder": false,
                                                                        "zeroLineColor": "#e7eaf3"
                                                                    },
                                                                    "ticks": {
                                                                        "beginAtZero": true,
                                                                        "fontSize": 12,
                                                                        "fontColor": "#97a4af",
                                                                        "fontFamily": "Open Sans, sans-serif",
                                                                        "padding": 5,
                                                                        "postfix": " $"
                                                                    }
                                                                }],
                                                                "xAxes": [{
                                                                    "gridLines": {
                                                                        "display": false,
                                                                        "drawBorder": false
                                                                    },
                                                                    "ticks": {
                                                                        "fontSize": 12,
                                                                        "fontColor": "#97a4af",
                                                                        "fontFamily": "Open Sans, sans-serif",
                                                                        "padding": 5
                                                                    },
                                                                    "categoryPercentage": 0.3,
                                                                    "maxBarThickness": "10"
                                                                }]
                                                            },
                                                            "cornerRadius": 5,
                                                            "tooltips": {
                                                                "prefix": " ",
                                                                "hasIndicator": true,
                                                                "mode": "index",
                                                                "intersect": false
                                                            },
                                                            "hover": {
                                                                "mode": "nearest",
                                                                "intersect": true
                                                            }
                                                        }
                                                    }'>
                        </canvas>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card--header order-top">
                    <div class="d-flex gap-2 align-items-center">
                        <h5 class="mb-0"> {{ translate('Total Transactions') }}
                            <span
                                class="badge badge-soft-dark rounded-pill fs-10 ml-1">{{ $expenseTransactionsTable->total() }}</span>
                        </h5>
                    </div>

                    <div class="d-flex flex-sm-nowrap flex-wrap gap-sm-3 gap-3">
                        <div class="d-flex flex-sm-nowrap flex-wrap gap-sm-3 gap-3">
                            <form action="{{ request()->url() }}" method="GET">
                                @foreach (request()->except('search', 'page') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach

                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                                        placeholder="{{ translate('Search by order id') }}" aria-label="Search"
                                        value="{{ $search }}" autocomplete="off">

                                    <div class="input-group-append h-30">
                                        <button type="submit" class="input-group-text title-bg3 p-2 text-white">
                                            <i class="tio-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div>
                            <select name="expense_type" id="expense_type" class="form-control h-30 py-1">
                                <option value="" selected>{{ translate('expense_Type') }}</option>
                                <option value="free_delivery" {{ $expenseType == 'free_delivery' ? 'selected' : '' }}>
                                    {{ translate('Free_Delivery') }}
                                </option>
                                <option value="extra_discount" {{ $expenseType == 'extra_discount' ? 'selected' : '' }}>
                                    {{ translate('Extra_Discount') }}
                                </option>
                                <option value="coupon_discount" {{ $expenseType == 'coupon_discount' ? 'selected' : '' }}>
                                    {{ translate('Coupon_Discount') }}
                                </option>
                            </select>
                        </div>

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
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('download') }}
                                    {{ translate('options') }}</span>
                                <a id="export-excel" class="dropdown-item"
                                    href="{{ route('admin.report.expense.export.excel', ['search' => $search, 'date_type' => $dateType, 'start_date' => $startDate, 'end_date' => $endDate, 'expenseType' => $expenseType]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                        alt="{{ translate('excel') }}">
                                    {{ translate('excel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table
                        class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('SL') }}</th>
                                <th>{{ translate('Order ID') }}</th>
                                <th>{{ translate('Order Date') }}</th>
                                <th>{{ translate('Expense Amount') }}</th>
                                <th>{{ translate('Expense Type') }}</th>
                                <th class="text-center">{{ translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenseTransactionsTable as $key => $transaction)
                                <tr>
                                    <td>{{ $expenseTransactionsTable->firstItem() + $key }}</td>
                                    <td>
                                        <a
                                            href="{{ route('admin.orders.details', ['id' => $transaction['id']]) }}">{{ $transaction->id }}</a>
                                    </td>
                                    <td>
                                        <div>{{ $transaction->created_at->format('d M Y') }}</div>
                                        <div>{{ $transaction->created_at->format('h:i A') }}</div>
                                    </td>
                                    <?php
                                    $expense_amount = 0;
                                    if ($transaction->coupon_discount_amount > 0) {
                                        $expense_amount = $transaction->coupon_discount_amount;
                                    } elseif ($transaction->extra_discount > 0) {
                                        $expense_amount = $transaction->extra_discount;
                                    } elseif ($transaction->free_delivery_amount > 0) {
                                        $expense_amount = $transaction->free_delivery_amount;
                                    }
                                    ?>
                                    <td>{{ \App\CentralLogics\Helpers::set_symbol($expense_amount) }}</td>
                                    <td class="text-capitalize">
                                        @if (isset($transaction->coupon->coupon_type))
                                            <span class="badge badge-soft-info">
                                                {{ translate($transaction?->coupon->coupon_type) }}
                                            </span>
                                        @elseif($transaction->free_delivery_amount > 0)
                                            <span class="badge badge-soft-success">
                                                {{ translate('Free_Delivery') }}
                                            </span>
                                        @elseif($transaction->extra_discount > 0)
                                            <span class="badge badge-soft-warning">
                                                {{ translate('Extra_Discount') }}
                                            </span>
                                        @else
                                            <span class="badge badge-soft-danger">
                                                {{ translate('Coupon Deleted') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a class="action-btn btn-outline-primary-2" target="_blank"
                                                href="{{ route('admin.orders.generate-invoice', [$transaction['id']]) }}">
                                                <i class="tio-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="">
                    {!! $expenseTransactionsTable->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
                </div>

                @if (count($expenseTransactionsTable) == 0)
                    <div class="text-center p-4">
                        <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                            alt="{{ translate('Image Description') }}">
                        <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/Chart.min.js"></script>
    <script src="{{ asset('public/assets/admin') }}/js/chart.js.extensions/chartjs-extensions.js"></script>
    <script src="{{ asset('public/assets/admin') }}/js/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js">
    </script>
    <script src="{{ asset('public/assets/admin/js/flatpicker.js') }}"></script>
    <script>
        "use strict";

        $(document).ready(function() {
            $('input').addClass('form-control');
        });

        Chart.plugins.unregister(ChartDataLabels);

        $('.js-chart').each(function() {
            $.HSCore.components.HSChartJS.init($(this));
        });

        var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{ url('/') }}/admin/store/get-stores',
                data: function(params) {
                    return {
                        q: params.term,
                        @if (isset($zone))
                            zone_ids: [{{ $zone->id }}],
                        @endif
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#expense_type').on('change', function() {
            let expenseType = this.value;

            // Get the current URL
            let currentUrl = new URL(window.location.href);

            // Update the query parameter 'expense_type' with the selected value
            currentUrl.searchParams.set('expense_type', expenseType);

            // Redirect to the new URL (this will reload the page)
            window.location.href = currentUrl;
        });
    </script>
@endpush
