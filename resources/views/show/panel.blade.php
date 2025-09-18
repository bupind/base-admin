<div class="card border-{{ $style }}" @if ($style!= 'none')style="border-top:2px solid;" @endif>
    <div class="card-header with-border">
        <div class="d-flex w-100 align-items-center">
            <div class="btn-group">
                {!! $tools !!}
            </div>
        </div>
    </div>
    <div class="form-horizontal">
        <div class="card-body">
            <div class="row">
                @foreach($fields as $field)
                    {!! $field->render() !!}
                @endforeach
            </div>
        </div>
    </div>
</div>
