@props([
    'node' => [],
    'isRoot' => false,
])

<div class="matrix-node">
    <a href="{{ route('admin.customer.view', ['user_id' => $node['id']]) }}"
       class="matrix-node-card {{ $isRoot ? 'is-root' : '' }}"
       style="text-decoration: none; color: inherit; display: block;">
        <div class="node-name">{{ $node['name'] }}</div>
        <div class="node-meta">{{ $node['phone'] }}</div>
        <div class="node-meta" style="font-size:10px; color:#6b7a8d;">
            {{ translate('ID') }}: {{ $node['id'] }} | {{ translate('Ref') }}: {{ $node['referral_code'] }}
        </div>
        @if(!empty($node['matrix_position']) && $node['matrix_position'] !== '—')
            <div>
                <span class="node-badge level-badge">{{ $node['matrix_position'] }}</span>
            </div>
        @endif
        @if(!empty($node['position']))
            <div>
                <span class="node-badge pos-badge">{{ translate('Pos') }} {{ $node['position'] }}</span>
            </div>
        @endif
        @if(!$isRoot && !empty($node['parent_name']))
            <div class="node-meta" style="margin-top: 2px;">
                {{ translate('Under') }}: {{ $node['parent_name'] }} @if(!empty($node['parent_matrix_position'])){{ $node['parent_matrix_position'] }}@endif
            </div>
        @endif
        <div class="node-meta" style="margin-top: 2px;">
            {{ translate('Depth') }}: {{ $node['depth'] }}
            @if(!empty($node['is_member']) && $node['is_member'])
                <span class="badge badge-success ml-1" style="font-size:9px;">{{ translate('Member') }}</span>
            @else
                <span class="badge badge-secondary ml-1" style="font-size:9px;">{{ translate('Non-Member') }}</span>
            @endif
        </div>
    </a>

    @if(!empty($node['children']) && count($node['children']) > 0)
        <div class="matrix-children">
            @foreach($node['children'] as $child)
                <div class="matrix-child-wrapper">
                    @include('admin-views.customer._matrix-tree-node', ['node' => $child, 'isRoot' => false])
                </div>
            @endforeach
        </div>
    @endif
</div>
