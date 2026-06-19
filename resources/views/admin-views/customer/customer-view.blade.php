@extends('layouts.admin.app')

@section('title', translate('Customer Details'))

@section('content')
    <div class="content container-fluid">
        <div class="d-print-none pb-2">
            <div class="page-header border-bottom">
                <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/employee.png')}}" class="w--20" alt="{{ translate('customer') }}">
                </span>
                    <span class="page-header-title pt-2">
                        {{translate('customer_Details')}}
                    </span>
                </h1>
            </div>
        </div>

        <div class="d-print-none pb-2">
            <div class="row align-items-center">
                <div class="col-auto mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('customer')}} {{translate('id')}} #{{$customer['id']}}</h1>
                    <span class="d-block">
                        <i class="tio-date-range"></i> {{translate('joined_at')}} : {{date('d M Y '.config('timeformat'),strtotime($customer['created_at']))}}
                    </span>
                </div>

                <div class="col-auto ml-auto">
                    @if(!empty($previousCustomer))
                        <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle mr-1"
                           href="{{route('admin.customer.view', $previousCustomer)}}"
                           data-toggle="tooltip" data-placement="top" title="{{ translate('Previous customer') }}">
                            <i class="tio-arrow-backward"></i>
                        </a>
                    @endif
                    @if(!empty($nextCustomer))
                            <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle"
                               href="{{route('admin.customer.view', $nextCustomer)}}" data-toggle="tooltip"
                               data-placement="top" title="{{ translate('Next customer') }}">
                                <i class="tio-arrow-forward"></i>
                            </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="row mb-2 g-2">


            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="resturant-card bg--2">
                    <img class="resturant-icon" src="{{asset('/public/assets/admin/img/dashboard/1.png')}}" alt="{{ translate('image') }}">
                    <div class="for-card-text font-weight-bold  text-uppercase mb-1">{{translate('wallet')}} {{translate('balance')}}</div>
                    <div class="for-card-count">{{ number_format($customer->wallet_balance??0) }}</div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="resturant-card bg--3">
                    <img class="resturant-icon" src="{{asset('/public/assets/admin/img/dashboard/1.png')}}" alt="{{ translate('image') }}">
                    <div class="for-card-text font-weight-bold text-uppercase mb-1">{{ translate('point_value_balance') }}</div>
                    <div class="for-card-count">{{ number_format($customer->total_point_value??0) }}</div>
                </div>
            </div>

        </div>


        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="card">
                    <div class="card-header">
                        <div class="card--header">
                        <h5 class="card-title">{{ translate('Order List') }} <span class="badge badge-soft-secondary">{{ count($orders) }}</span></h5>
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search"
                                           class="form-control"
                                           placeholder="{{translate('Search by Order Id or Order Amount')}}" aria-label="Search"
                                           value="{{$search}}" autocomplete="off">

                                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">

                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text">
                                            {{ translate('search') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <h5 class="card-header-title">
                        </h5>
                    </div>
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('#')}}</th>
                                <th class="text-center">{{translate('order')}} {{translate('id')}}</th>
                                <th class="text-center">{{translate('order')}} {{translate('date')}}</th>
                                <th class="text-center">{{translate('total amount')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $key=>$order)
                                <tr>
                                    <td>{{$orders->firstItem()+$key}}</td>
                                    <td class=" text-center">
                                        <a href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                    </td>
                                    <td class="text-center">{{  $order['date'] ? \Carbon\Carbon::parse($order['date'])->format('d, M Y') : \Carbon\Carbon::parse($order['created_at'])->format('d, M Y') }}</td>
                                    <td class="text-center">{{ Helpers::set_symbol($order['order_amount']) }}</td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="action-btn"
                                                href="{{route('admin.orders.details',['id'=>$order['id']])}}"><i
                                                    class="tio-invisible"></i></a>
                                            <a class="action-btn btn--primary btn-outline-primary" target="_blank"
                                                href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                                <i class="tio-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="">
                        {!! $orders->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
                    </div>

                    @if(count($orders)==0)
                        <div class="text-center p-4">
                            <img class="w-120px mb-3" src="{{asset('public/assets/admin')}}/svg/illustrations/sorry.svg" alt="{{ translate('image') }}">
                            <p class="mb-0">{{ translate('No_data_to_show')}}</p>
                        </div>
                    @endif
                </div>
            </div>



            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>
                                @if($customer)
                                    {{$customer['f_name'].' '.$customer['l_name']}}
                                    @else
                                    {{ translate('customer') }}
                                @endif
                            </span>
                        </h4>
                    </div>

                    @if($customer)
                        <div class="card-body">
                            <div class="media align-items-center customer--information-single" href="javascript:">
                                <div class="avatar avatar-circle">
                                    <img
                                        class="avatar-img"
                                        src="{{$customer->imageFullPath}}"
                                        alt="{{ translate('customer') }}">
                                </div>
                                <div class="media-body">
                                    <ul class="list-unstyled m-0">
                                        <li class="pb-1">
                                            <i class="tio-email mr-2"></i>
                                            <a href="mailto:{{$customer['email']}}">{{$customer['email']}}</a>
                                        </li>
                                        <li class="pb-1">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            <a href="Tel:{{$customer['phone']}}">{{$customer['phone']}}</a>
                                        </li>
                                        <li class="pb-1">
                                            <i class="tio-shopping-basket-outlined mr-2"></i>
                                            {{$customer->orders->count()}} {{translate('orders')}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5>{{translate('contact')}} {{translate('info')}}</h5>
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#passwordModal">
                                    <i class="tio-lock"></i> {{translate('password')}}
                                </button>
                            </div>
                            @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                            @foreach($customer->addresses as $address)
                                <ul class="list-unstyled list-unstyled-py-2">
                                    @if($address['contact_person_number'])
                                        <li>
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            {{$address['contact_person_number']}}
                                        </li>
                                    @endif
                                    <li class="quick--address-bar">
                                        <div class="quick-icon badge-soft-secondary">
                                            <i class="tio-home"></i>
                                        </div>
                                        <div class="info">
                                            <h6>{{ translate($address['address_type'])}}</h6>
                                            @if($googleMapStatus && $address['latitude'] && $address['longitude'])
                                                <a target="_blank" href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}" class="text--title">
                                                    {{$address['address']}}
                                                </a>
                                            @else
                                                <p>{{$address['address']}}</p>
                                            @endif
                                        </div>
                                    </li>
                                </ul>
                            @endforeach

                        </div>
                @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Password Change Modal --}}
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.customer.password-update') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $customer->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translate('Change Customer Password') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new-password">{{ translate('New Password') }}</label>
                            <div class="input-group">
                                <input type="text" name="password" id="new-password" class="form-control"
                                       minlength="6" required
                                       placeholder="{{ translate('Enter or generate new password') }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-secondary" id="generate-password-btn"
                                            title="{{ translate('Generate password') }}">
                                        <i class="tio-refresh"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">{{ translate('Minimum 6 characters. Copy this password and share with the customer.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        document.getElementById('generate-password-btn')?.addEventListener('click', function() {
            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$&_';
            let password = '';
            for (let i = 0; i < 10; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            document.getElementById('new-password').value = password;
        });
    </script>
@endpush
