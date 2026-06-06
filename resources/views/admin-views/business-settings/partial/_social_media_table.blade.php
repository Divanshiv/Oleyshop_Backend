@foreach ($socialMedia as $key => $medium)
    <tr>
        <td class="column_name" data-column_name="sl" data-id="{{ $medium->id }}">{{ $key + 1 }}</td>
        <td class="column_name" data-column_name="name" data-id="{{ $medium->id }}">{{ $medium->name }}</td>
        <td class="column_name" data-column_name="slug" data-id="{{ $medium->id }}">{{ $medium->link }}</td>
        <td class="column_name" data-column_name="status" data-id="{{ $medium->id }}">
            <label class="toggle-switch toggle-switch-sm">
                <input type="checkbox" class="toggle-switch-input status" id="{{ $medium->id }}" {{ $medium->status == 1 ? 'checked' : '' }}>
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </td>
        <td><a type="button" class="action-btn edit mx-auto" id="{{ $medium->id }}"><i class="tio-edit"></i></a>
        </td>
    </tr>
@endforeach
