@extends('layouts.admin.app')

@section('title', translate('Member Management'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/employee.png') }}" class="w--20"
                        alt="{{ translate('member') }}">
                </span>
                <span>
                    {{ translate('members') }} <span
                        class="badge badge-soft-primary ml-2 badge-pill">{{ $customers->total() }}</span>
                </span>
            </h1>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap gap-2 border-0 align-items-center justify-content-between">
                <form action="{{ request()->url() }}" method="GET" class="mb-0">
                    @foreach (request()->except('search', 'member_filter', 'page') as $key => $value)
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

                        <select name="member_filter" class="form-control h-30 min-w-120 py-1 text-center" style="text-align-last: center;" onchange="this.form.submit()">
                            <option value="">{{ translate('All Members') }}</option>
                            <option value="active" {{ request('member_filter') == 'active' ? 'selected' : '' }}>
                                {{ translate('Active Members') }}
                            </option>
                            <option value="inactive" {{ request('member_filter') == 'inactive' ? 'selected' : '' }}>
                                {{ translate('Inactive Members') }}
                            </option>
                        </select>
                    </div>
                </form>

                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#transferModal">
                        <i class="tio-exchange"></i>
                        {{ translate('Transfer Points') }}
                    </button>
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#milestoneModal">
                        <i class="tio-settings-outlined"></i>
                        {{ translate('Milestone Settings') }}
                    </button>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr class="word-nobreak">
                            <th>{{ translate('#') }}</th>
                            <th class="table-column-pl-0">{{ translate('customer name') }}</th>
                            <th>{{ translate('contact info') }}</th>
                            <th class="text-center">{{ translate('Total Orders') }}</th>
                            <th class="text-center">{{ translate('Point Value') }}</th>
                            <th class="text-center">{{ translate('Progress') }}</th>
                            <th class="text-center">{{ translate('Member Status') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="set-rows">
                        @foreach ($customers as $key => $customer)
                            @php
                                if ($customer->is_member) {
                                    $progress = 100;
                                    $remaining = 0;
                                } else {
                                    $progress = min(100, ($customer->total_point_value / max($memberMilestone, 1)) * 100);
                                    $remaining = max(0, $memberMilestone - $customer->total_point_value);
                                }
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
                                        <span class="badge badge-soft-info py-2 px-3 font-medium">
                                            {{ $customer->orders->count() }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <span class="font-weight-bold">
                                            {{ number_format($customer->total_point_value ?? 0) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center" style="min-width: 120px;">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">{{ round($progress, 1) }}%</small>
                                            <small class="text-muted">{{ number_format($remaining) }} left</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar
                                                @if($progress >= 100) bg-success
                                                @elseif($progress >= 50) bg-info
                                                @else bg-warning
                                                @endif"
                                                role="progressbar"
                                                style="width: {{ $progress }}%"
                                                aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        @if($customer->is_member)
                                            <span class="badge badge-success py-2 px-3">
                                                <i class="tio-checkmark-circle"></i>
                                                {{ translate('Active Member') }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary py-2 px-3">
                                                <i class="tio-time"></i>
                                                {{ translate('Not Member') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn"
                                           href="{{ route('admin.customer.view', [$customer['id']]) }}"
                                           title="{{ translate('view') }}">
                                            <i class="tio-invisible"></i>
                                        </a>
                                        <a class="action-btn btn--primary btn-outline-primary"
                                           href="javascript:"
                                           onclick="toggleMemberStatus({{ $customer['id'] }})"
                                           title="{{ $customer->is_member ? translate('Deactivate Member') : translate('Activate Member') }}">
                                            <i class="tio-{{ $customer->is_member ? 'block' : 'checkmark-circle' }}"></i>
                                        </a>
                                        <form id="toggle-member-form-{{ $customer['id'] }}"
                                              action="{{ route('admin.customer.members.toggle-status', [$customer['id']]) }}"
                                              method="post">
                                            @csrf
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

    {{-- Milestone Settings Modal --}}
    <div class="modal fade" id="milestoneModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.customer.members.update-milestone') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translate('Member Milestone Settings') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ translate('Points Required for Membership') }}</label>
                            <input type="number" name="milestone_points" class="form-control"
                                value="{{ $memberMilestone }}" min="1" required>
                            <small class="text-muted">
                                {{ translate('Customers who accumulate this many points will become active members.') }}
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            {{ translate('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ translate('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Transfer Points Modal --}}
    <div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.customer.members.transfer-points') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translate('Transfer Points') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ translate('From (Sender)') }}</label>
                            <select name="from_user_id" class="form-control js-select2-customer" required>
                                <option value="">{{ translate('-- Select Sender --') }}</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">
                                        #{{ $c->id }} {{ $c->f_name }} {{ $c->l_name }} ({{ number_format($c->total_point_value ?? 0) }} pts)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ translate('To (Receiver)') }}</label>
                            <select name="to_user_id" class="form-control js-select2-customer" required>
                                <option value="">{{ translate('-- Select Receiver --') }}</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">
                                        #{{ $c->id }} {{ $c->f_name }} {{ $c->l_name }} ({{ number_format($c->total_point_value ?? 0) }} pts)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ translate('Points to Transfer') }}</label>
                            <input type="number" name="amount" class="form-control" min="1" required
                                placeholder="{{ translate('Enter amount') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            {{ translate('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="tio-exchange"></i> {{ translate('Transfer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        // Initialize select2 for customer dropdowns in transfer modal
        $(document).ready(function () {
            $('.js-select2-customer').select2({
                width: '100%',
                dropdownParent: $('#transferModal')
            });
        });

        function toggleMemberStatus(id) {
            event.preventDefault();
            Swal.fire({
                title: '{{ translate("Are you sure?") }}',
                text: '{{ translate("You want to change the member status for this customer") }}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#fc544b',
                confirmButtonText: '{{ translate("Yes") }}',
                cancelButtonText: '{{ translate("No") }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    document.getElementById('toggle-member-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
