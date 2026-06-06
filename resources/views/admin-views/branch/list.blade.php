@extends('layouts.admin.app')

@section('title', translate('branch List'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/add_branch.png') }}" class="w--20"
                        alt="{{ translate('branch') }}">
                </span>
                <span>
                    {{ translate('branch List') }} <span class="badge badge-soft-secondary">{{ $branches->total() }}</span>
                </span>
            </h1>
        </div>

        <div class="card">
            <div class="card--header order-top">
                <div class="d-flex gap-2 align-items-center">
                    <form action="{{ request()->url() }}" method="GET">
                        @foreach (request()->except('search', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control"
                                placeholder="{{ translate('Search by Name') }}" aria-label="Search"
                                value="{{ $search }}" autocomplete="off">

                            <div class="input-group-append">
                                <button type="submit" class="input-group-text p-2">
                                    <i class="tio-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="d-flex flex-sm-nowrap flex-wrap gap-sm-3 gap-3">
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table
                    class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{ translate('#') }}</th>
                            <th class="border-0">{{ translate('branch name') }}</th>
                            <th class="border-0">{{ translate('branch type') }}</th>
                            <th class="border-0">{{ translate('contact info') }}</th>
                            <th class="border-0">{{ translate('Delivery Charge Type') }}</th>
                            <th class="border-0">{{ translate('status') }}</th>
                            <th class="border-0 text-center">{{ translate('action') }}</th>
                        </tr>

                    </thead>

                    <tbody>
                        @foreach ($branches as $key => $branch)
                            <tr>
                                <td>{{ $branches->firstItem() + $key }}</td>
                                <td>
                                    <div class="short-media">
                                        <img src="{{ $branch->imageFullPath }}">
                                        <div class="text-cont">
                                            <span class="d-block font-size-sm text-body text-trim-50">
                                                {{ $branch['name'] }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($branch['id'] == 1)
                                        <span>{{ translate('main') }} </span>
                                    @else
                                        <span>{{ translate('sub Branch') }} </span>
                                    @endif
                                </td>
                                <td>
                                    <h5 class="m-0">
                                        <a href="mailto:{{ $branch['email'] }}">{{ $branch['email'] }}</a>
                                    </h5>
                                    <div>
                                        <a href="Tel:{{ $branch['phone'] }}">{{ $branch['phone'] }}</a>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-soft-success">
                                        {{ $branch?->delivery_charge_setup?->delivery_charge_type }} </span>
                                </td>
                                <td>
                                    @if ($branch['id'] != 1)
                                        <label class="toggle-switch">
                                            <input type="checkbox" class="toggle-switch-input status-change-alert"
                                                id="stocksCheckbox{{ $branch->id }}"
                                                data-route="{{ route('admin.branch.status', [$branch->id, $branch->status ? 0 : 1]) }}"
                                                data-message="{{ $branch->status ? translate('you_want_to_disable_this_branch') : translate('you_want_to_active_this_branch') }}"
                                                {{ $branch->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn" target="_blank"
                                            href="{{ route('admin.business-settings.store.delivery-fee-setup') }}">
                                            <i class="tio-settings"></i>
                                        </a>
                                        <a class="action-btn" href="{{ route('admin.branch.edit', [$branch['id']]) }}"><i
                                                class="tio-edit"></i>
                                        </a>
                                        @if ($branch['id'] != 1)
                                            <a class="action-btn btn--danger btn-outline-danger form-alert"
                                                href="javascript:" data-id="branch-{{ $branch['id'] }}"
                                                data-message="{{ translate('Want to delete this') }}">
                                                <i class="tio-delete-outlined"></i>
                                            </a>
                                        @endif
                                    </div>
                                    <form action="{{ route('admin.branch.delete', [$branch['id']]) }}" method="post"
                                        id="branch-{{ $branch['id'] }}">
                                        @csrf @method('delete')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $branches->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>

            @if (count($branches) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{ asset('public/assets/admin') }}/svg/illustrations/sorry.svg"
                        alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
