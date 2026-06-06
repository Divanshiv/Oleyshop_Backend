@extends('layouts.admin.app')

@section('title', translate('Offline Payment'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center">
                <img width="20" class="avatar-img" src="{{ asset('public/assets/admin/img/icons/business_setup2.png') }}"
                    alt="{{ translate('business_setup') }}">
                <span class="page-header-title ml-2 mb-0">
                    {{ translate('Offline Payment Method Setup') }}
                </span>
            </h2>
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
                                placeholder="{{ translate('Search_by_method_name') }}" aria-label="Search"
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
                    <div>
                        <a href="{{ route('admin.business-settings.web-app.third-party.offline-payment.add') }}"
                           class="align-items-center btn btn-primary d-flex fs-12px gap-10px h-30 py-1"><i class="tio-add"></i>
                            {{ translate('Add New Method') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table
                    class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('Payment Method Name') }}</th>
                            <th>{{ translate('Payment Info') }}</th>
                            <th>{{ translate('Required Info from Customer') }}</th>
                            <th class="text-center">{{ translate('status') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($methods as $key => $method)
                            <tr>
                                <td>{{ $methods->firstitem() + $key }}</td>
                                <td>
                                    <div class="max-w300 text-wrap">
                                        {{ $method['method_name'] }}
                                    </div>
                                </td>
                                <td>
                                    @foreach ($method['method_fields'] as $key => $fields)
                                        <span class="border border-white max-w300 text-wrap text-left">
                                            {{ $fields['field_name'] }} :
                                            {{ translate($fields['field_data']) }}
                                        </span><br />
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($method['method_informations'] as $key => $informations)
                                        <span class="border border-white max-w300 text-wrap text-left">
                                            {{ translate($informations['information_name']) }} |
                                        </span>
                                    @endforeach
                                    <div class="max-w300 text-wrap">
                                        {{ translate('Payment note') }}
                                    </div>
                                </td>
                                <td>
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox" class="toggle-switch-input status-change-alert"
                                            id="stocksCheckbox{{ $method->id }}"
                                            data-route="{{ route('admin.business-settings.web-app.third-party.offline-payment.status', [$method->id, $method->status ? 0 : 1]) }}"
                                            data-message="{{ $method->status ? translate('you_want_to_disable_this_method') : translate('you_want_to_active_this_method') }}?"
                                            {{ $method->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn"
                                            href="{{ route('admin.business-settings.web-app.third-party.offline-payment.edit', [$method['id']]) }}">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <button class="action-btn btn--danger btn-outline-danger delete-method"
                                            data-id="{{ $method->id }}">
                                            <i class="tio-delete-outlined"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $methods->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>

            @if (count($methods) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                        alt="{{ translate('Image') }}">
                    <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";

        $('.delete-method').on('click', function() {
            let id = $(this).data('id');
            deleteItem(id)
        })

        function deleteItem(id) {
            Swal.fire({
                title: '{{ translate('Are you sure') }}?',
                text: "{{ translate('You will not be able to revert this') }}!",
                showCancelButton: true,
                confirmButtonColor: '#FC6A57',
                cancelButtonColor: '#EA295E',
                confirmButtonText: '{{ translate('Yes, delete it') }}!'
            }).then((result) => {
                if (result.value) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.business-settings.web-app.third-party.offline-payment.delete') }}",
                        method: 'POST',
                        data: {
                            id: id,
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function() {
                            toastr.success('{{ translate('Removed successfully') }}');
                            location.reload();
                        }
                    });
                }
            })
        }
    </script>
@endpush
