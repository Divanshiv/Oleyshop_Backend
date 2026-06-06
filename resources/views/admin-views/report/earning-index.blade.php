@extends('layouts.admin.app')

@section('title', translate('Earning Report'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="media align-items-center mb-2">
                <div class="d-flex gap-2 align-items-center">
                    <img src="{{asset('public/assets/admin/img/image-4.png')}}" class="w--20" alt="">
                    <h1 class="mb-0">{{translate('earning')}} {{translate('report')}} {{translate('overview')}}</h1>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form action="{{url()->current()}}" method="GET">
                    <div class="row g-3">
                        <div class="col-12">
                            <div>
                                <label class="form-label mb-0 font-semibold">{{translate('show')}} {{translate('data')}} {{translate('by')}} {{translate('date')}}{{translate('range')}}</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="input-label">{{translate('start')}} {{translate('date')}}</label>
                            <label class="input-date">
                                <input type="text" name="from" id="from_date" value="{{ request()->get('from') }}"
                                       class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ translate('dd/mm/yy') }}" required>
                            </label>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="input-label">{{translate('end')}} {{translate('date')}}</label>
                            <label class="input-date">
                                <input type="text" name="to" id="to_date" value="{{ request()->get('to') }}"
                                       class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ translate('dd/mm/yy') }}" required>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="input-label d-none d-md-block">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn--primary min-h-45px btn-block">{{translate('show')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap g-2 g-lg-3">
                    <div class="flex-grow-1">
                        <div class="card card-sm bg--1 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="media">
                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('earning')}}</h4>
                                                <span class="text-warning">
                                                <i class="tio-trending-up"></i> {{ Helpers::set_symbol(round(abs($totalEarning))) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                                "value": {{$totalSold > 0 ? round(((abs($totalEarning)) / $totalSold) * 100) : 0}},
                                                "maxValue": 100,
                                                "duration": 2000,
                                                "isViewportInit": true,
                                                "colors": ["#0096ff40", "#0096ff90"],
                                                "radius": 25,
                                                "width": 3,
                                                "fgStrokeLinecap": "round",
                                                "textFontSize": 14,
                                                "additionalText": "%",
                                                "textClass": "circle-custom-text",
                                                "textColor": "#0096ff"
                                            }'>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="card card-sm bg--2 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="media">
                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('sold')}}</h4>
                                                <span class="text-success">
                                                <i class="tio-trending-up"></i> {{ Helpers::set_symbol(round(abs($totalSold))) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                                "value": {{$totalSold > 0 ? 100 : 0}},
                                                "maxValue": 100,
                                                "duration": 2000,
                                                "isViewportInit": true,
                                                "colors": ["#00800040", "green"],
                                                "radius": 25,
                                                "width": 3,
                                                "fgStrokeLinecap": "round",
                                                "textFontSize": 14,
                                                "additionalText": "%",
                                                "textClass": "circle-custom-text",
                                                "textColor": "green"
                                            }'>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="card card-sm bg--3 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="media">
                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('tax')}}</h4>
                                                <span class="text-danger">
                                                <i class="tio-trending-up"></i> {{ Helpers::set_symbol(round(abs($totalTax))) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                                "value": {{$totalSold > 0 ? round(((abs($totalTax)) / $totalSold) * 100) : 0}},
                                                "maxValue": 100,
                                                "duration": 2000,
                                                "isViewportInit": true,
                                                "colors": ["#f83b3b40", "#f83b3b"],
                                                "radius": 25,
                                                "width": 3,
                                                "fgStrokeLinecap": "round",
                                                "textFontSize": 14,
                                                "additionalText": "%",
                                                "textClass": "circle-custom-text",
                                                "textColor": "#f83b3b"
                                                }'>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="card card-sm bg--4 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="media">
                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('delivery')}} {{translate('charge') }}</h4>
                                                <span class="text-warning">
                                                <i class="tio-trending-up"></i> {{ Helpers::set_symbol(round(abs($totalDeliveryCharge))) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                                "value": {{$totalDeliveryCharge > 0 ? round(((abs($totalDeliveryCharge)) / $totalSold) * 100) : 0}},
                                                "maxValue": 100,
                                                "duration": 2000,
                                                "isViewportInit": true,
                                                "colors": ["#ec9a3c40", "#ec9a3c"],
                                                "radius": 25,
                                                "width": 3,
                                                "fgStrokeLinecap": "round",
                                                "textFontSize": 14,
                                                "additionalText": "%",
                                                "textClass": "circle-custom-text",
                                                "textColor": "#ec9a3c"
                                            }'>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img width="20" src="{{asset('/public/assets/admin/img/order-statistics.png')}}" alt="">
                    <h3 class="mb-0">{{ translate('Total sale of ') }} {{date('Y')}} :<span class="text-primary"> {{ Helpers::set_symbol($thisYearTotalSold) }}</span></h3>
                </div>

                <div class="chartjs-custom h-400">
                    <canvas id="gradient-line-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{asset('public/assets/admin/js/flatpicker.js')}}"></script>
    <script src="{{asset('public/assets/admin/js/earning.js')}}"></script>

    <script>
        (function ($) {
            "use strict";

            let canvas = document.getElementById('gradient-line-chart');
            let ctx = canvas.getContext('2d');
            let myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
                    datasets: [{
                        label: "Total Order",
                        lineTension: .5,
                        borderColor: "#00C9A7",
                        pointBorderColor: "#00C9A7",
                        pointBackgroundColor: "#00C9A7",
                        fill: false,
                        backgroundColor: "rgba(82, 220, 79, .04)",
                        borderWidth: 3,
                        data: [{{ $sold[1]}}, {{$sold[2]}}, {{$sold[3]}}, {{$sold[4]}}, {{$sold[5]}}, {{$sold[6]}}, {{$sold[7]}}, {{$sold[8]}}, {{$sold[9]}}, {{$sold[10]}}, {{$sold[11]}}, {{$sold[12]}}]
                    }, {
                        label: "Total Tax",
                        lineTension: .5,
                        borderColor: "#ED4C78",
                        pointBorderColor: "#ED4C78",
                        pointBackgroundColor: "#ED4C78",
                        fill: false,
                        backgroundColor: "rgba(50, 131, 249, .04)",
                        borderWidth: 3,
                        data: [{{$tax[1]}}, {{$tax[2]}}, {{$tax[3]}}, {{$tax[4]}}, {{$tax[5]}}, {{$tax[6]}}, {{$tax[7]}}, {{$tax[8]}}, {{$tax[9]}}, {{$tax[10]}}, {{$tax[11]}}, {{$tax[12]}}]

                    }, {
                        label: "Total Delivery Charge",
                        lineTension: .5,
                        borderColor: "#F5A200",
                        pointBorderColor: "#F5A200",
                        pointBackgroundColor: "#F5A200",
                        fill: false,
                        backgroundColor: "rgba(50, 131, 249, .04)",
                        borderWidth: 3,
                        data: [{{$deliveryCharge[1]}}, {{$deliveryCharge[2]}}, {{$deliveryCharge[3]}}, {{$deliveryCharge[4]}}, {{$deliveryCharge[5]}}, {{$deliveryCharge[6]}}, {{$deliveryCharge[7]}}, {{$deliveryCharge[8]}}, {{$deliveryCharge[9]}}, {{$deliveryCharge[10]}}, {{$deliveryCharge[11]}}, {{$deliveryCharge[12]}}]

        }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        position: "top",
                        align: "end",
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            }
                        },
                    }
                }
            });
        })(jQuery);
    </script>

    <script>
        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('{{ translate("Invalid date range!") }}', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        })
    </script>
@endpush
