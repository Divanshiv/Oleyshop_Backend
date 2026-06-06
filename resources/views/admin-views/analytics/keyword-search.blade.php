@extends('layouts.admin.app')

@section('title', translate('Keyword_Search_Analytics'))

@section('content')
    <div class="content overflow-hidden container-fluid ">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/analytics_logo.png') }}" class="w--20"
                        alt="{{ translate('analytics') }}">
                </span>
                <span>
                    {{ translate('Keyword_Search_Analytics') }}
                </span>
            </h1>
        </div>

        <div class="row gy-3">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-lg-nowrap flex-wrap justify-content-between gap-2 align-items-center">
                            <h4 class="m-0">{{ translate('Trending_Keywords') }}</h4>
                            <div class="select-wrap d-flex flex-wrap gap-10 max-w-150px w-100">
                                <select class="form-control select2-custom-height js-select2-custom trending-keywords__select" name="date_range">
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
                        @if (count($graphData['count']) < 1 && count($graphData['keyword']) < 1)
                            <div class="text-center py-4 h-100 w-100 d-center">
                                <div class="text-center">
                                    <img class="mb-2" width="65" src="{{ asset('public/assets/admin/img/no-data--found.png') }}" alt="{{ translate('logo') }}">
                                    <div class="w-100">
                                        {{ translate('No Data Found') }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="apex_radial-bar-chart-custom">
                                <div id="apex_radial-bar-chart"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
                            <h4 class="m-0">{{ translate('category_Wise_Search_Volume') }}</h4>
                            <div class="select-wrap d-flex flex-wrap gap-10 min-w-180px">
                                <select class="form-control select2-custom-height js-select2-custom w-100 zone-search-volume__select"
                                    id="date-range" name="date_range_2">
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

                        <div class="mt-3">
                            <div class="row gy-3">
                                <div class="col-lg-5">
                                    <div
                                        class="bg-light search-value--box rounded-10 d-flex justify-content-center align-items-center p-3">
                                        <div class="text-center">
                                            <img class="mb-2" width="50"
                                                src="{{ asset('public/assets/admin/img/analytics_logo.png') }}"
                                                alt="{{ translate('analytics_logo') }}">
                                            <h2 class="mb-2">{{ $searchedKeywordCount }}</h2>
                                            <p>{{ translate('Total Search Volume') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="max-h320-auto">
                                        <ul class="common-list max-h-315-with-scrolling after-none gap-4 d-flex flex-column list-unstyled">
                                            @forelse ($categoryWiseVolumes as $item)
                                                <li>
                                                    <div class="mb-2 d-flex align-items-center justify-content-between gap-10 flex-wrap">
                                                        <span class="zone-name">{{ $item->category ? $item->category?->name : translate('category Deleted') }}</span>
                                                        <span class="booking-count">{{ number_format(($item['count'] * 100) / $total, 2) }}%</span>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ ($item['count'] * 100) / $total }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">

                                                        </div>
                                                    </div>
                                                </li>
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
                            placeholder="{{ translate('Search by keyword') }}" aria-label="Search"
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
                                href="{{ route('admin.analytics.keyword.export.excel', ['search' => $search]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                    alt="Image Description">
                                {{ translate('Excel') }}
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
                            <th>{{ translate('Keyword') }}</th>
                            <th class="text-center">{{ translate('Search Volume') }}</th>
                            <th class="text-center">{{ translate('Related Categories') }}</th>
                            <th class="text-center">{{ translate('Related Products') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($searchedTableData as $key => $item)
                            <tr>
                                <td>{{ $searchedTableData->firstitem() + $key }}</td>
                                <td>{{ $item->keyword ?? '' }}</td>
                                <td class="text-center">{{ $item->volume_count ?? '' }}</td>
                                <td class="text-center">
                                    <a href="#" data-toggle="tooltip" data-html="true" data-placement="right"
                                        title="
                                        <?php
                                        $categories = json_decode($item->searched_category);
                                        foreach ($categories as $category) {
                                            echo $category->category?->name . '<br>';
                                        }

                                        ?>">
                                        {{ $item->searched_category_count }}</a>

                                </td>
                                <td class="text-center">{{ $item->searched_product_count }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="">
                {!! $searchedTableData->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if (count($searchedTableData) == 0)
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

@endsection

@push('script_2')
    <script src="{{ asset('/public/assets/admin/js/apex-charts/apexcharts.js') }}"></script>

    <script>
        const seriesData = @json(array_values($graphData['count']));
        const labelsData = @json(array_values($graphData['keyword']));

        const options = {
            series: seriesData,
            chart: {
                height: 350,
                type: 'radialBar'
            },
            stroke: {
                lineCap: 'round',
                width: 2
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 270,
                    hollow: {
                        margin: 10,
                        size: '37%'
                    },
                    track: {
                        background: '#EAEAEA',
                        strokeWidth: '100%',
                        margin: 12
                    },
                    dataLabels: {
                        name: {
                            fontSize: '16px'
                        },
                        value: {
                            fontSize: '14px'
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function () {
                                return {{ array_sum($graphData['count']) }};
                            }
                        }
                    }
                }
            },
            labels: labelsData,
            colors: ['#286CD1', '#FFC700', '#A2CEEE', '#79CCA5', '#FFB16D'],
            legend: {
                show: true,
                floating: false,
                fontSize: '12px',
                position: 'bottom',
                horizontalAlign: 'center',
                offsetY: -10,
                itemMargin: {
                    horizontal: 5,
                    vertical: 10
                },
                labels: {
                    colors: '#6A727A',
                    useSeriesColors: false
                },
                markers: {
                    size: 4,
                    width: 4,
                    height: 4,
                    radius: 10,
                },
                formatter: function (_, opts) {
                    return labelsData[opts.seriesIndex] + ": " + opts.w.globals.series[opts.seriesIndex];
                }
            }
        };

        new ApexCharts(document.querySelector("#apex_radial-bar-chart"), options).render();
    </script>


    <script>
        $(".trending-keywords__select").on('change', function() {
            if (this.value !== "") location.href = "{{ route('admin.analytics.keyword-search') }}" +
                '?date_range=' + this.value + '&date_range_2=' +
                '{{ $queryParams['date_range_2'] ?? 'all_time' }}';
        });
        $(".zone-search-volume__select").on('change', function() {
            if (this.value !== "") location.href = "{{ route('admin.analytics.keyword-search') }}" +
                '?date_range=' + '{{ $queryParams['date_range'] ?? 'all_time' }}' + '&date_range_2=' + this.value;
        });
    </script>
@endpush
