@if(in_array('reset', $buttons))
    <button type="reset" class="btn btn-warning">{{ trans('backend.reset') }}</button>
@endif
@if(in_array('submit', $buttons))
    <button type="submit" class="btn btn-primary">{{ trans('backend.submit') }}</button>
@endif
