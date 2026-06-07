@props([
    'node' => [],
    'isRoot' => false,
])

<div class="matrix-node">
    <div class="matrix-node-card {{ $isRoot ? 'is-root' : '' }}">
        <div class="node-name">{{ $node['name'] }}</div>
        <div class="node-meta">{{ $node['phone'] }}</div>
        @if(!empty($node['position']))
            <div>
                <span class="node-badge pos-badge">{{ translate('Pos') }} {{ $node['position'] }}</span>
            </div>
        @endif
        <div class="node-meta" style="margin-top: 2px;">
            {{ translate('Depth') }}: {{ $node['depth'] }}
        </div>
    </div>

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
