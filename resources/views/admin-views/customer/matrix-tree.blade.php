@extends('layouts.admin.app')

@section('title', translate('Matrix Tree'))

@push('css_or_js')
    <style>
        .matrix-tree-container {
            overflow-x: auto;
            padding: 20px 0;
        }
        .matrix-tree {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: max-content;
        }
        .matrix-node {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .matrix-node-card {
            background: #fff;
            border: 2px solid #e7eaf3;
            border-radius: 8px;
            padding: 12px 20px;
            min-width: 180px;
            text-align: center;
            position: relative;
            transition: all 0.2s;
            cursor: pointer;
        }
        .matrix-node-card:hover {
            border-color: #377dff;
            box-shadow: 0 4px 12px rgba(55,125,255,0.15);
        }
        .matrix-node-card.is-root {
            border-color: #377dff;
            background: #f5f8ff;
        }
        .matrix-node-card .node-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .matrix-node-card .node-meta {
            font-size: 11px;
            color: #8c98a4;
        }
        .matrix-node-card .node-badge {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 4px;
        }
        .matrix-node-card .node-badge.level-badge {
            background: #e8faff;
            color: #00c9a7;
        }
        .matrix-node-card .node-badge.pos-badge {
            background: #fff4e5;
            color: #ff8f00;
        }
        .matrix-children {
            display: flex;
            gap: 24px;
            margin-top: 30px;
            position: relative;
        }
        .matrix-children::before {
            content: '';
            position: absolute;
            top: -15px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: #e7eaf3;
        }
        .matrix-child-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .matrix-child-wrapper::before {
            content: '';
            position: absolute;
            top: -15px;
            width: 2px;
            height: 15px;
            background: #e7eaf3;
        }
        .matrix-child-wrapper:first-child::before {
            left: 50%;
        }
        .matrix-child-wrapper:last-child::before {
            left: 50%;
        }
        .matrix-child-wrapper:not(:first-child):not(:last-child)::before {
            left: 50%;
        }
        .team-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .team-summary .info-card {
            background: #fff;
            border: 1px solid #e7eaf3;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }
        .team-summary .info-card .info-value {
            font-size: 24px;
            font-weight: 700;
            color: #1e2022;
        }
        .team-summary .info-card .info-label {
            font-size: 12px;
            color: #8c98a4;
            margin-top: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/employee.png') }}" class="w--20"
                        alt="{{ translate('matrix') }}">
                </span>
                <span>{{ translate('Matrix Tree') }}: {{ $user['f_name'] . ' ' . $user['l_name'] }}</span>
            </h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.customer.matrix.index') }}"
                   class="btn btn-sm btn-secondary">
                    <i class="tio-arrow-backward"></i>
                    {{ translate('Back to Matrix') }}
                </a>
            </div>
        </div>

        {{-- Team Summary --}}
        <div class="team-summary">
            <div class="info-card">
                <div class="info-value">{{ $status['total_team_members'] ?? 0 }}</div>
                <div class="info-label">{{ translate('Total Team Members') }}</div>
            </div>
            <div class="info-card">
                <div class="info-value">{{ $user['matrix_level'] ?? 0 }}</div>
                <div class="info-label">{{ translate('Current Level') }}</div>
            </div>
            <div class="info-card">
                <div class="info-value">{{ $user['matrix_position'] ?? translate('—') }}</div>
                <div class="info-label">{{ translate('Position') }}</div>
            </div>
            <div class="info-card">
                <div class="info-value">{{ is_countable($children) ? count($children) : 0 }}</div>
                <div class="info-label">{{ translate('Direct Referrals') }}</div>
            </div>
        </div>

        {{-- Level Progress --}}
        @if(count($levels) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">{{ translate('Level Progression') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">{{ translate('Level') }}</th>
                                    <th>{{ translate('Position') }}</th>
                                    <th class="text-center">{{ translate('Required Members') }}</th>
                                    <th class="text-center">{{ translate('Incentive') }}</th>
                                    <th class="text-center">{{ translate('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($levels as $level)
                                    @php
                                        $earned = $user->matrix_level >= $level->level;
                                        $current = $user->matrix_level == $level->level;
                                    @endphp
                                    <tr class="{{ $current ? 'table-active' : '' }}">
                                        <td class="text-center font-weight-bold">Lv.{{ $level->level }}</td>
                                        <td>{{ $level->position_name }}</td>
                                        <td class="text-center">{{ $level->required_members }}</td>
                                        <td class="text-center">{{ \App\CentralLogics\Helpers::set_symbol($level->incentive_amount) }}</td>
                                        <td class="text-center">
                                            @if($earned)
                                                <span class="badge badge-success">{{ translate('Achieved') }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ translate('Locked') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tree Visualization --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ translate('Referral Tree') }}</h5>
                <form method="GET" class="d-flex gap-2 align-items-center">
                    <label class="mb-0 text-muted small">{{ translate('Depth') }}:</label>
                    <select name="depth" class="form-control form-control-sm h-30 py-1" style="width: auto;" onchange="this.form.submit()">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $depth == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            <div class="card-body matrix-tree-container">
                @if(!empty($tree))
                    <div class="matrix-tree">
                        @include('admin-views.customer._matrix-tree-node', ['node' => $tree, 'isRoot' => true])
                    </div>
                @else
                    <div class="text-center p-4">
                        <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                            alt="{{ translate('Image Description') }}">
                        <p class="mb-0">{{ translate('This user is not part of any matrix tree yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
