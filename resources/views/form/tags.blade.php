@include("backend::form._header")

    <input class="form-control {{$class}}" name="{{$name}}[]" data-placeholder="{{ $placeholder }}" {!! $attributes !!} value="{{implode(",",$value)}}" />

@include("backend::form._footer")
