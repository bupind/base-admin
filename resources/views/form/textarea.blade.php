@include("backend::form._header")

	<textarea name="{{$name}}" class="form-control {{$class}}" rows="{{ $rows }}" placeholder="{{ $placeholder }}" {!! $attributes !!} >{{ old($column, $value) }}</textarea>

	{!! $append !!}

@include("backend::form._footer")
