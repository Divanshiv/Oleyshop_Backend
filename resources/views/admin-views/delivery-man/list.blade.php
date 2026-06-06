@extends('layouts.admin.app')

@section('title', translate('Deliveryman List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/employee.png') }}" class="w--24"
                        alt="{{ translate('deliveryman') }}">
                </span>
                <span>
                    {{ translate('deliveryman') }} {{ translate('list') }}
                </span>
                <span class="badge badge-soft-info badge-pill">{{ $deliverymen->total() }}</span>
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
                                    data-hs-unfold-content-animation-in="slideInUp"
                                    data-hs-unfold-content-animation-out="fadeOut" style="animation-duration: 300ms;">
                                    <span class="dropdown-header">{{ translate('Download Options') }}</span>
                                    <a id="export-excel" class="dropdown-item"
                                        href="{{ route('admin.delivery-man.export', ['search' => $search]) }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                            alt="Image Description">
                                        Excel
                                    </a>
                                </div>
                            </div>

                            <div>
                                <a href="{{ route('admin.delivery-man.add') }}"
                                    class="btn btn-primary min-height-30 py-1 h-30 fs-12px"><i class="tio-add"></i>
                                    {{ translate('Add Deliveryman') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive datatable-custom">
                        <table
                            class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ translate('#') }}</th>
                                    <th>{{ translate('name') }}</th>
                                    <th>{{ translate('Contact Info') }}</th>
                                    <th>{{ translate('Total Orders') }}</th>
                                    <th class="text-center">{{ translate('Status') }}</th>
                                    <th class="text-center">{{ translate('action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @foreach ($deliverymen as $key => $deliveryman)
                                    <tr>
                                        <td>{{ $deliverymen->firstItem() + $key }}</td>
                                        <td>
                                            <a class="table--media"
                                                href="{{ route('admin.delivery-man.preview', $deliveryman['id']) }}">
                                                <img class="rounded-full" src="{{ $deliveryman->imageFullPath }}"
                                                    alt="{{ translate('deliveryman') }}">
                                                <div class="table--media-body">
                                                    <h5 class="title">
                                                        {{ $deliveryman['f_name'] }} {{ $deliveryman['l_name'] }}
                                                    </h5>
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <h5 class="m-0">
                                                <a href="mailto:{{ $deliveryman['email'] }}"
                                                    class="text-hover">{{ $deliveryman['email'] }}</a>
                                            </h5>
                                            <div>
                                                <a href="tel:{{ $deliveryman['phone'] }}"
                                                    class="text-hover">{{ $deliveryman['phone'] }}</a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-info py-2 px-3 font-bold">
                                                {{ $deliveryman->orders->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <label class="toggle-switch my-0">
                                                <input type="checkbox"
                                                    data-route="{{ route('admin.delivery-man.status', [$deliveryman->id, $deliveryman->is_active ? 0 : 1]) }}"
                                                    data-message="{{ $deliveryman->is_active ? translate('you_want_to_disable_this_deliveryman') : translate('you_want_to_active_this_deliveryman') }}"
                                                    class="toggle-switch-input status-change-alert"
                                                    id="stocksCheckbox{{ $deliveryman->id }}"
                                                    {{ $deliveryman->is_active ? 'checked' : '' }}>
                                                <span class="toggle-switch-label mx-auto text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="btn--container justify-content-center">
                                                <a class="action-btn"
                                                    href="{{ route('admin.delivery-man.edit', [$deliveryman['id']]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="action-btn btn--danger btn-outline-danger form-alert"
                                                    href="javascript:" data-id="delivery-man-{{ $deliveryman['id'] }}"
                                                    data-message="{{ translate('Want to remove this deliveryman') }}?">
                                                    <i class="tio-delete-outlined"></i>
                                                </a>
                                                <form
                                                    action="{{ route('admin.delivery-man.delete', [$deliveryman['id']]) }}"
                                                    method="post" id="delivery-man-{{ $deliveryman['id'] }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="">
                            {!! $deliverymen->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
                        </div>

                        @if (count($deliverymen) == 0)
                            <div class="text-center p-4">
                                <img class="w-120px mb-3"
                                    src="{{ asset('public/assets/admin') }}/svg/illustrations/sorry.svg"
                                    alt="{{ translate('image') }}">
                                <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
