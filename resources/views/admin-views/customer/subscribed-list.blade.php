@extends('layouts.admin.app')

@section('title', translate('Subscribed List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/employee.png') }}" class="w--20"
                        alt="{{ translate('employee') }}">
                </span>
                <span>
                    {{ translate('Subscribed Customers') }} <span
                        class="badge badge-soft-primary ml-2 badge-pill">{{ $newsletters->total() }}</span>
                </span>
            </h1>
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header flex-wrap gap-2 border-0">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                                    placeholder="{{ translate('Search by email') }}" aria-label="Search"
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
                                    data-hs-unfold-content-animation-in="slideInUp"
                                    data-hs-unfold-content-animation-out="fadeOut" style="animation-duration: 300ms;">
                                    <span class="dropdown-header">{{ translate('Download Options') }}</span>
                                    <a id="export-excel" class="dropdown-item"
                                        href="{{ route('admin.customer.subscribed_emails_export', ['search' => $search]) }}">
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
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ translate('#') }}</th>
                                    <th>{{ translate('email') }}</th>
                                    <th>{{ translate('subscribed_at') }}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @foreach ($newsletters as $key => $newsletter)
                                    <tr>
                                        <td>
                                            {{ $newsletters->firstitem() + $key }}
                                        </td>
                                        <td>
                                            <a
                                                href="mailto:{{ $newsletter['email'] }}?subject={{ translate('Mail from ') . Helpers::get_business_settings('restaurant_name') }}">{{ $newsletter['email'] }}</a>
                                        </td>
                                        <td>{{ date('d M Y h:m A ' . config('timeformat'), strtotime($newsletter->created_at)) }}
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    <div>
                        {!! $newsletters->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
                    </div>

                    @if (count($newsletters) == 0)
                        <div class="text-center p-4">
                            <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                                alt="{{ translate('image') }}">
                            <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
