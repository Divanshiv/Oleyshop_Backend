@extends('layouts.admin.app')

@section('title', translate('employee role'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/employee.png') }}" class="w--24"
                        alt="{{ translate('employee') }}">
                </span>
                <span>
                    {{ translate('Employee Role Setup') }}
                </span>
            </h1>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form id="submit-create-role" method="post" action="{{ route('admin.custom-role.store') }}">
                    @csrf
                    <div class="max-w-500px">
                        <div class="form-group">
                            <label class="form-label">{{ translate('role_name') }}</label>
                            <input type="text" name="name" class="form-control" id="name"
                                aria-describedby="emailHelp" placeholder="{{ translate('Ex') }} : {{ translate('Store') }}"
                                required>
                        </div>
                    </div>

                    <div class="d-flex">
                        <h5 class="input-label m-0 text-capitalize">{{ translate('module_permission') }} : </h5>
                        <div class="check-item pb-0 w-auto">
                            <input type="checkbox" id="select_all">
                            <label class="title-color mb-0 pl-2" for="select_all">{{ translate('select_all') }}</label>
                        </div>
                    </div>

                    <div class="check--item-wrapper">
                        @foreach (MANAGEMENT_SECTION as $section)
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="{{ $section }}"
                                        class="form-check-input module-permission" id="{{ $section }}">
                                    <label class="form-check-label"
                                        for="{{ $section }}">{{ translate($section) }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="btn--container justify-content-end mt-4">
                        <button type="reset" class="btn btn--reset">{{ translate('reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card--header order-top">
                <div class="d-flex gap-2 align-items-center">
                    <h5 class="mb-0"> {{ translate('employee_roles_table') }}
                        <span class="badge badge-soft-dark rounded-pill fs-10 ml-1">{{ $adminRoles->total() }}</span>
                    </h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex flex-sm-nowrap flex-wrap gap-sm-3 gap-3">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                                    placeholder="{{ translate('Search by role name') }}" aria-label="Search"
                                    value="{{ $search }}" autocomplete="off">

                                <div class="input-group-append h-30">
                                    <button type="submit" class="input-group-text title-bg3 p-2 text-white">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

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
                                href="{{ route('admin.custom-role.export', ['search' => $search]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                    alt="Image Description">
                                Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table
                    class="table table-hover table-border table-thead-bordered table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('role_name') }}</th>
                            <th>{{ translate('modules') }}</th>
                            <th class="text-center">{{ translate('status') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($adminRoles as $k => $role)
                            <tr>
                                <td>{{ $adminRoles->firstItem() + $k }}</td>
                                <td>{{ $role['name'] }}</td>
                                <td class="text-capitalize">
                                    <div class="max-w-300px">
                                        @if ($role['module_access'] != null)
                                            @php($comma = '')
                                            @foreach ((array) json_decode($role['module_access']) as $module)
                                                {{ $comma }}{{ translate(str_replace('_', ' ', $module)) }}
                                                @php($comma = ', ')
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox"
                                            data-route="{{ route('admin.custom-role.status', [$role->id, $role->status ? 0 : 1]) }}"
                                            data-message="{{ $role->status ? translate('you_want_to_disable_this_role') : translate('you_want_to_active_this_role') }}"
                                            class="toggle-switch-input status-change-alert"
                                            id="stocksCheckbox{{ $role->id }}" {{ $role->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a href="{{ route('admin.custom-role.update', [$role['id']]) }}"
                                            class="action-btn" title="{{ translate('Edit') }}">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                            data-id="role-{{ $role['id'] }}"
                                            data-message="{{ translate('Want to delete this role') }}?">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{ route('admin.custom-role.delete', [$role['id']]) }}"
                                            method="post" id="role-{{ $role['id'] }}">
                                            @csrf @method('delete')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="">
                    {!! $adminRoles->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
                </div>

                @if (count($adminRoles) === 0)
                    <div class="text-center p-4">
                        <img class="mb-3 width-7rem" src="{{ asset('public/assets/admin/svg/illustrations/sorry.svg') }}"
                            alt="{{ translate('image') }}">
                        <p class="mb-0">{{ translate('No data to show') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/custom-role.js') }}"></script>
@endpush
