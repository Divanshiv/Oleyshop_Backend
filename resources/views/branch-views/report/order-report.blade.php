@php
    use App\CentralLogics\Helpers;
    ini_set('memory_limit', '-1');
@endphp

@extends('layouts.branch.app')

@section('title', translate('Order Report'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin/vendor/swiper/swiper-bundle.min.css') }}" />
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="media align-items-center">
                <img class="w--20" src="{{ asset('public/assets/admin') }}/img/sale-report.png" alt="Image Description">
                <div class="media-body pl-3">
                    <h1 class="page-header-title mb-1">{{ translate('order') }} {{ translate('report') }}</h1>
                </div>
            </div>
        </div>

        <div>
            <div class="card mb-4">
                <div class="card-body">
                    <h3>{{ translate('Filter Data') }}</h3>

                    <form id="form_id" class="w-100">
                        <div class="row g-3 g-sm-4 g-md-3 g-lg-4 mt-2">

                            <div class="col-lg-4 col-md-4 col-sm-6">
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

                            <div class="col-sm-6 col-md-4 col-lg-4">
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

                            <div class="col-sm-6 col-md-4 col-lg-4">
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
                                    <a href="{{ route('admin.report.sale-report') }}"
                                        class="btn btn--reset min-w-120px">{{ translate('Reset') }}</a>

                                    <button type="submit"
                                        class="btn btn--primary min-w-120px">{{ translate('Filter') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="swiper saleOverView_slide">
                    <div class="swiper-wrapper">

                        @foreach ($finalCounts as $key => $status)
                            <div class="swiper-slide">
                                <div class="card card-sm {{ $status['style']['bg'] }} border-0 shadow-none">
                                    <div class="card-body py-5 px-xxl-5">
                                        <div class="row g-2">
                                            <div class="col">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <h4 class="mb-1">{{ translate($key) }}</h4>
                                                        <span class="text-info {{ $status['style']['text_class'] }}">
                                                            <i class="tio-trending-up"></i> {{ $status['count'] ?? 0 }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-auto">
                                                <div class="js-circle"
                                                    data-hs-circles-options='{
                                                "value": {{ $status['percentage'] ?? 0 }},
                                                "maxValue": 100,
                                                "duration": 2000,
                                                "isViewportInit": true,
                                                "colors": {{ json_encode($status['style']['circle_colors']) }},
                                                "radius": 30,
                                                "width": 3,
                                                "fgStrokeLinecap": "round",
                                                "textFontSize": 12,
                                                "additionalText": "%",
                                                "textClass": "circle-custom-text",
                                                "textColor": "{{ $status['style']['text_color'] }}"
                                                }'>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">

                    <h4 class="card-subtitle text-capitalize mb-0 text-dark"><span
                            class="mr-sm-2">{{ translate('total_Delivired_Orders') }}</span> :
                        <span class="h3 ml-sm-2 title-clr3">{{ $finalCounts['delivered']['count'] }}</span>
                    </h4>
                </div>

                <div class="table-order-data d-flex align-items-center flex-wrap">
                    <div class="table-data-badge d-flex align-items-center gap-2">
                        <div class="dot-custom style-1"></div>
                        <h5 class="text-dark fs-14 font-medium m-0">{{ translate('Total Order') }}</h5>
                    </div>
                    <div class="table-data-badge d-flex align-items-center gap-2">
                        <div class="dot-custom style-2"></div>
                        <h5 class="text-dark fs-14 font-medium m-0">VAT / Tax</h5>
                    </div>
                    <div class="table-data-badge d-flex align-items-center gap-2">
                        <div class="dot-custom style-3"></div>
                        <h5 class="text-dark fs-14 font-medium m-0">Delivery Charge</h5>
                    </div>
                </div>
            </div>

            <div class="card-body p-3">
                <div class="chart-container">
                    <canvas id="orderChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex-wrap border-0">
                <h3 class="m-0">{{ translate('Delivered Order List') }}</h3>

                <div class="d-flex gap-3">
                    <form action="{{ request()->url() }}" method="GET">
                        @foreach (request()->except('search', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                                placeholder="{{ translate('Search by Order Id') }}" aria-label="Search"
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
                            data-hs-unfold-content-animation-in="slideInUp" data-hs-unfold-content-animation-out="fadeOut"
                            style="animation-duration: 300ms;">
                            <span class="dropdown-header">Download
                                Options</span>
                            <a id="export-excel" class="dropdown-item"
                                href="{{ route('branch.report.export-order-report', ['date_range' => $dateRange, 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}">
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
                            <th>{{ translate('#') }}</th>
                            <th>{{ translate('Order ID') }}</th>
                            <th>{{ translate('Date') }}</th>
                            <th>{{ translate('Order Amount') }} ({{ Helpers::currency_symbol() }})</th>
                            <th>{{ translate('Discount Given') }} ({{ Helpers::currency_symbol() }})</th>
                            <th>{{ translate('Delivery Charge') }} ({{ Helpers::currency_symbol() }})</th>
                            <th>{{ translate('Charge On Weight') }} ({{ Helpers::currency_symbol() }})</th>
                            <th>{{ translate('Vat/Tax') }} ({{ Helpers::currency_symbol() }})</th>
                            <th>{{ translate('Total Order Amount') }} ({{ Helpers::currency_symbol() }})</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($orders as $key => $order)
                            <tr>
                                <td class="text-dark">
                                    {{ $orders->firstItem() + $key }}
                                </td>

                                <td class="text-dark">
                                    #{{ $order->id }}
                                </td>

                                <td class="text-dark">
                                    {{ $order->created_at->format('d M Y') }} <br>
                                    {{ $order->created_at->format('h:i A') }}
                                </td>

                                <td class="text-dark">
                                    {{ Helpers::set_symbol($order->details->sum(fn($item) => $item->price * $item->quantity)) }}
                                </td>

                                <td class="text-dark">
                                    Product Discount :
                                    {{ Helpers::set_symbol($order->details->sum(fn($item) => $item->discount_on_product * $item->quantity)) }}
                                    <br>
                                    Coupon Discount : {{ Helpers::set_symbol($order->coupon_discount_amount) }} <br>
                                    @if ($order->extra_discount > 0)
                                        Extra Discount : {{ Helpers::set_symbol($order->extra_discount) }}
                                    @endif
                                </td>

                                <td class="text-dark">
                                    {{ Helpers::set_symbol($order->delivery_charge) }}
                                </td>

                                <td class="text-dark">
                                    {{ Helpers::set_symbol($order->weight_charge_amount) }}
                                </td>

                                <td class="text-dark">
                                    {{ Helpers::set_symbol($order->total_tax_amount) }}
                                </td>

                                <td class="text-dark">
                                    {{ Helpers::set_symbol($order->order_amount) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div>
                    {!! $orders->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
                </div>

                @if (count($orders) === 0)
                    <div class="text-center p-4">
                        <img class="mb-3 w-120px" src="{{ asset('public/assets/admin/svg/illustrations/sorry.svg') }}"
                            alt="{{ translate('Image Description') }}">
                        <p class="mb-0">{{ translate('No data to show') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
@endpush

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('public/assets/admin') }}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="{{ asset('public/assets/admin') }}/js/hs.chartjs-matrix.js"></script>
    <script src="{{ asset('public/assets/admin/js/flatpicker.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/order-report.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/swiper-bundle.min.js') }}"></script>
    <script href="{{ asset('public/assets/admin/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script>
        let swiper = new Swiper(".saleOverView_slide", {
            slidesPerView: 3,
            spaceBetween: 10,
            breakpoints: {
                '1': {
                    slidesPerView: 1.5,
                    spaceBetween: 15,
                },
                '575': {
                    slidesPerView: 2.5,
                    spaceBetween: 15,
                },
                '767': {
                    slidesPerView: 3.5,
                    spaceBetween: 18,
                },
                '1199': {
                    slidesPerView: 3.5,
                    spaceBetween: 18,
                },
                '1399': {
                    slidesPerView: 4.5,
                    spaceBetween: 18,
                },
                '1600': {
                    slidesPerView: 4.5,
                    spaceBetween: 24,
                },
            },
        });
        const sliderEl = document.querySelector(".saleOverView_slide");
        sliderEl.addEventListener("wheel", function(e) {
            e.preventDefault();
            e.deltaY > 0 ? swiper.slideNext() : swiper.slidePrev();
        }, { passive: false });
    </script>

    <script>
        const ctx = document.getElementById('orderChart').getContext('2d');
        const labels = ['0', ...@json($labels).map(l => l.replace(/"/g, ''))];
        const totalOrders = [0, ...@json($totalOrders).map(v => v ? parseFloat(v) : 0)];
        const taxData = [0, ...@json($taxData).map(v => v ? parseFloat(v) : 0)];
        const deliveryChargeData = [0, ...@json($deliveryChargeData).map(v => v ? parseFloat(v) : 0)];

        function formatCurrency(value, symbol = "{{ Helpers::currency_symbol() }}") {
            if (value >= 1_000_000_000) return symbol + (value / 1_000_000_000).toFixed(1) + 'B';
            if (value >= 1_000_000) return symbol + (value / 1_000_000).toFixed(1) + 'M';
            if (value >= 1_000) return symbol + (value / 1_000).toFixed(1) + 'K';
            return symbol + value.toFixed(2);
        }

        const orderChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: "{{ translate('Total Order') }}",
                        data: totalOrders,
                        borderColor: '#10798080',
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false,
                        lineTension: 0.4
                    },
                    {
                        label: "{{ translate('VAT / Tax') }}",
                        data: taxData,
                        borderColor: 'transparent',
                        borderWidth: 0,
                        pointRadius: 0,
                        fill: false,
                        lineTension: 0.4
                    },
                    {
                        label: "{{ translate('Delivery Charge') }}",
                        data: deliveryChargeData,
                        borderColor: 'transparent',
                        borderWidth: 0,
                        pointRadius: 0,
                        fill: false,
                        lineTension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    enabled: true,
                    mode: 'index',
                    intersect: false,
                    backgroundColor: '#1e1e1e',
                    titleFontColor: '#ffffff',
                    bodyFontColor: '#ffffff',
                    cornerRadius: 10,
                    bodySpacing: 8,
                    xPadding: 10,
                    yPadding: 10,
                    displayColors: true,
                    custom: function(tooltipModel) {
                        if (!tooltipModel || !tooltipModel.body) return;

                        tooltipModel.labelColors = [{
                                borderColor: '#14cc60',
                                backgroundColor: '#14cc60'
                            },
                            {
                                borderColor: '#ff5f5f',
                                backgroundColor: '#ff5f5f'
                            },
                            {
                                borderColor: '#ffb21d',
                                backgroundColor: '#ffb21d'
                            }
                        ];
                        tooltipModel.body.forEach(function(bodyItem, i) {
                            if (bodyItem && bodyItem.lines) {
                                tooltipModel.body[i].lines = bodyItem.lines.map(function(line, index) {
                                    const matches = line.match(/([-+]?[0-9]*\.?[0-9]+)/);
                                    if (matches) {
                                        const num = parseFloat(matches[0]);
                                        return line.replace(matches[0], formatCurrency(num));
                                    }
                                    return line;
                                });
                            }
                        });
                    }
                },

                legend: {
                    display: false
                },

                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        },
                        gridLines: {
                            drawBorder: false,
                            borderDash: [5, 5],
                            color: '#B4D0E080'
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            drawBorder: false,
                            color: '#B4D0E080'
                        }
                    }]
                },

                hover: {
                    mode: null
                }
            }
        });
    </script>

    <script>
        $('#form_id').on('submit', function(e) {
            if ($('#date_range').val() === '{{ CUSTOM_DATE }}') {
                if (!$('#start_date').val() || !$('#end_date').val()) {
                    toastr.error('Please select start and end dates.');
                    e.preventDefault();
                }
            }
        });
    </script>
@endpush
