@extends('layouts.admin.app')

@section('title', translate('Matrix Settings'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/employee.png') }}" class="w--20"
                        alt="{{ translate('matrix') }}">
                </span>
                <span>
                    {{ translate('matrix_settings') }}
                </span>
            </h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title d-flex align-items-center">
                    <span class="card-header-icon mb-1 mr-2">
                        <i class="tio-settings"></i>
                    </span>
                    <span>{{ translate('Company Root Settings') }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <i class="tio-info-outined mr-1"></i>
                    {{ translate('New members who register without a referral code will be automatically placed under the company root user in the matrix tree.') }}
                </div>

                <form action="{{ route('admin.customer.matrix.settings') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="input-label" for="company_root_id">
                            {{ translate('Company Root User') }}
                        </label>
                        <select name="company_root_id" id="company_root_id" class="form-control js-select2-custom" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $companyRootId ? 'selected' : '' }}>
                                    {{ $user->f_name }} {{ $user->l_name }} ({{ $user->phone ?? $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            {{ translate('Currently:') }}
                            <strong>
                                @if($companyRoot)
                                    {{ $companyRoot->f_name }} {{ $companyRoot->l_name }} (ID: {{ $companyRoot->id }})
                                @else
                                    {{ translate('User not found') }}
                                @endif
                            </strong>
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="tio-save mr-1"></i>
                        {{ translate('Save') }}
                    </button>

                    <a class="btn btn-secondary" href="{{ route('admin.customer.matrix.index') }}">
                        {{ translate('Back to Matrix Management') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection
