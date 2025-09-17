@include("backend::form._header")

        <input type="text" id="{{$id}}" name="{{$name}}" value="{{$value}}" class="form-control" readonly {!! $attributes !!} />

@include("backend::form._footer")
