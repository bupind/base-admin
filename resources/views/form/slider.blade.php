@include("backend::form._header")

    <input type="range" class="{{$class}} form-range" name="{{$name}}" data-from="{{ old($column, $value) }}" {!! $attributes !!} />

@include("backend::form._footer")
