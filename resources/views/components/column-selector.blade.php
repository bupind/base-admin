<div class="dropdown btn-group grid-column-selector dropdown" id="grid-column-selector" data-defaults='{{ implode(",",$defaults) }}'>
    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="icon-table"></i>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">

        @foreach($columns as $key => $label)
        @php
        if (empty($visible)) {
            $checked = 'checked';
        } else {
            $checked = in_array($key, $visible) ? 'checked' : '';
        }
        @endphp

        <li>
            <label class="dropdown-item" for="column-select-{{ $key }}">
                <input type="checkbox" class="form-check-input column-selector" id="column-select-{{ $key }}" value="{{ $key }}" {{ $checked }}/>{{ $label }}
            </label>
        </li>
        @endforeach

        <li><hr class="dropdown-divider"></li>
        <li class="text-right">
            <button class="btn btn-sm btn-light column-select-all" onclick="backend.grid.columns.all()">{{ __('backend.all') }}</button>&nbsp;&nbsp;
            <button class="btn btn-sm btn-primary column-select-submit" onclick="backend.grid.columns.submit()">{{ __('backend.submit') }}</button>
        </li>
    </ul>
</div>
