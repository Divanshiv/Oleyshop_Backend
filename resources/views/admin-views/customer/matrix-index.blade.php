@extends('layouts.admin.app')

@section('title', translate('Matrix Management'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/employee.png') }}" class="w--20"
                        alt="{{ translate('matrix') }}">
                </span>
                <span>
                    {{ translate('matrix_management') }} <span
                        class="badge badge-soft-primary ml-2 badge-pill">{{ $customers->total() }}</span>
                </span>
            </h1>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap gap-2 border-0 align-items-center justify-content-between">
                <form action="{{ request()->url() }}" method="GET">
                    @foreach (request()->except('search', 'matrix_level_filter', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="d-flex gap-2 align-items-center flex-wrap">
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

                        <select name="matrix_level_filter" class="form-control h-30 min-w-120 py-1" onchange="this.form.submit()">
                            <option value="">{{ translate('All Levels') }}</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->level }}" {{ request('matrix_level_filter') == $level->level ? 'selected' : '' }}>
                                    {{ $level->position_name }} (Lv.{{ $level->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.customer.matrix.settings') }}">
                    <i class="tio-settings"></i>
                    {{ translate('Settings') }}
                </a>
            </div>

            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr class="word-nobreak">
                            <th>{{ translate('#') }}</th>
                            <th class="table-column-pl-0">{{ translate('customer name') }}</th>
                            <th class="text-center">{{ translate('Referral Code') }}</th>
                            <th class="text-center">{{ translate('Matrix Level') }}</th>
                            <th class="text-center">{{ translate('Position') }}</th>
                            <th class="text-center">{{ translate('Team Members') }}</th>
                            <th class="text-center">{{ translate('Direct Referrals') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="set-rows">
                        @foreach ($customers as $key => $customer)
                            @php
                                $matrixMember = $customer->matrixMember;
                                $directCount = $matrixMember ? $matrixMember->children_count : 0;
                            @endphp
                            <tr>
                                <td>{{ $customers->firstItem() + $key }}</td>
                                <td class="table-column-pl-0">
                                    <a href="{{ route('admin.customer.view', [$customer['id']]) }}"
                                        class="product-list-media">
                                        <img class="rounded-full" src="{{ $customer->imageFullPath }}"
                                            alt="{{ translate('customer') }}" width="40" height="40">
                                        <div class="table--media-body">
                                            <h5 class="title m-0">
                                                {{ $customer['f_name'] . ' ' . $customer['l_name'] }}
                                            </h5>
                                        </div>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-soft-info py-2 px-3">
                                        {{ $customer['referral_code'] ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($customer->matrix_level > 0)
                                        <span class="badge badge-primary py-2 px-3">
                                            Lv.{{ $customer->matrix_level }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-secondary py-2 px-3">
                                            {{ translate('None') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold">
                                        {{ $customer['matrix_position'] ?? translate('—') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-soft-success py-2 px-3">
                                        {{ $customer['total_team_members'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-soft-warning py-2 px-3">
                                        {{ $directCount }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn btn--primary btn-outline-primary"
                                           href="{{ route('admin.customer.matrix.tree', [$customer['id']]) }}"
                                           title="{{ translate('View Tree') }}">
                                            <i class="tio-node-multiple-outlined"></i>
                                        </a>
                                        <a class="action-btn"
                                           href="{{ route('admin.customer.view', [$customer['id']]) }}"
                                           title="{{ translate('view') }}">
                                            <i class="tio-invisible"></i>
                                        </a>
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
