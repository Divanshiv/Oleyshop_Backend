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
        @if(!empty($node['position']))
            <div>
                <span class="node-badge pos-badge">{{ translate('Pos') }} {{ $node['position'] }}</span>
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

            {{-- Fill empty slots up to 4 positions --}}
            @for($i = count($node['children']) + 1; $i <= 4; $i++)
                <div class="matrix-child-wrapper">
                    <div class="matrix-empty-slot">
                        <i class="tio-add"></i>
                        <div>{{ translate('Empty Slot') }} {{ $i }}</div>
                    </div>
                </div>
            @endfor
        </div>
    @endif
</div>
