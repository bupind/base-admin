<div class="card">
    <div class="card-header with-border">
        {!! $form->renderTools() !!}
    </div>
    {!! $form->open() !!}
        <div class="card-body">
            @if(!$tabObj->isEmpty())
                @include('backend::form.tab', compact('tabObj'))
            @else
                <div class="row">
                    @if($form->hasRows())
                        @foreach($form->getRows() as $row)
                            {!! $row->render() !!}
                        @endforeach
                    @else
                        @foreach($layout->columns() as $column)
                            @foreach($column->fields() as $field)
                                {!! $field->render() !!}
                            @endforeach
                        @endforeach
                    @endif
                </div>
            @endif
        </div>


    <footer class="navbar form-footer navbar-light bg-white py-3 px-4 @if (!empty($fixedFooter))shadow fixed-bottom @endif">
        <div class="row">
            {{ csrf_field() }}
            <div class="col-md-{{$width['label']}}"></div>
            <div class="col-md-{{$width['field']}} d-flex align-items-center ">
                <div class="flex-grow-1 ">

                </div>
                <div class="btn-group">
                    {!! $form->renderFooter() !!}
                </div>
            </div>
        </div>
    </footer>


    @foreach($form->getHiddenFields() as $field)
        {!! $field->render() !!}
    @endforeach
    {!! $form->close() !!}

</div>
