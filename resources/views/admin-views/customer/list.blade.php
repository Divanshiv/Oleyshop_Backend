@extends('layouts.admin.app')

@section('title', translate('Customer List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/employee.png') }}" class="w--20"
                        alt="{{ translate('customer') }}">
                </span>
                <span>
                    {{ translate('customers list') }} <span
                        class="badge badge-soft-primary ml-2 badge-pill">{{ $customers->total() }}</span>
                </span>
            </h1>
        </div>

        <div class="card">
            <div class="card-header flex-wrap gap-2 border-0">
                <form action="{{ request()->url() }}" method="GET">
                    @foreach (request()->except('search', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="input-group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                            placeholder="{{ translate('Search by Name or Phone or Email') }}" aria-label="Search"
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
                                href="{{ route('admin.customer.export', ['search' => $search]) }}">
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
                        <tr class="word-nobreak">
                            <th>
                                {{ translate('#') }}
                            </th>
                            <th class="table-column-pl-0">{{ translate('customer name') }}</th>
                            <th>{{ translate('contact info') }}</th>
                            <th class="text-center">{{ translate('Total Orders') }}</th>
                            <th class="text-center">{{ translate('Total Order Amount') }}</th>
                            <th class="text-center">{{ translate('status') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="set-rows">
                        @foreach ($customers as $key => $customer)
                            <tr>
                                <td>
                                    {{ $customers->firstItem() + $key }}
                                </td>
                                <td class="table-column-pl-0">
                                    <a href="{{ route('admin.customer.view', [$customer['id']]) }}"
                                        class="product-list-media">
                                        <img class="rounded-full" src="{{ $customer->imageFullPath }}"
                                            alt="{{ translate('customer') }}">
                                        <div class="table--media-body">
                                            <h5 class="title m-0">
                                                {{ $customer['f_name'] . ' ' . $customer['l_name'] }}
                                            </h5>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <h5 class="m-0">
                                        <a href="mailto:{{ $customer['email'] }}">{{ $customer['email'] }}</a>
                                    </h5>
                                    <div>
                                        <a href="Tel:{{ $customer['phone'] }}">{{ $customer['phone'] }}</a>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <a href="{{ route('admin.customer.view', [$customer['id']]) }}">
                                            <span class="badge badge-soft-info py-2 px-3 font-medium">
                                                {{ $customer->orders->count() }}
                                            </span>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        {{ Helpers::set_symbol(\App\User::total_order_amount($customer->id)) }}
                                    </div>
                                </td>
                                <td>
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox" class="toggle-switch-input status-change-alert"
                                            id="stocksCheckbox{{ $customer->id }}"
                                            data-route="{{ route('admin.customer.status', [$customer->id, $customer->is_block ? 0 : 1]) }}"
                                            data-message="{{ $customer->is_block ? translate('you_want_to_change_the_status_for_this_customer') : translate('you_want_to_change_the_status_for_this_customer') }}"
                                            {{ $customer->is_block ? '' : 'checked' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn" href="{{ route('admin.customer.view', [$customer['id']]) }}">
                                            <i class="tio-invisible"></i>
                                        </a>
                                        <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                            data-id="customer-{{ $customer['id'] }}"
                                            data-message="{{ translate('Want to remove this customer') }}?">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{ route('admin.customer.delete', [$customer['id']]) }}"
                                            method="post" id="customer-{{ $customer['id'] }}">
                                            @csrf @method('delete')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $customers->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>

            @if (count($customers) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                        alt="{{ translate('Image Description') }}">
                    <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
