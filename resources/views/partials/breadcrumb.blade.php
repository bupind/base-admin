<nav aria-label="breadcrumb" class="breadcrumb-nav small ms-1">
    @if ($breadcrumb)
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ admin_url('/') }}">{{__('Dashboard')}}</a></li>
            @foreach($breadcrumb as $item)
                @if($loop->last)
                    <li class="breadcrumb-item active">
                        {{ $item['text'] }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        @if (\Illuminate\Support\Arr::has($item, 'url'))
                            <a href="{{ admin_url(\Illuminate\Support\Arr::get($item, 'url')) }}">
                                {{ $item['text'] }}
                            </a>
                        @else
                            {{ $item['text'] }}
                        @endif
                    </li>
                @endif
            @endforeach
        </ol>
    @elseif(config('backend.enable_default_breadcrumb'))
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ admin_url('/') }}">{{__('Dashboard')}}</a></li>
            @for($i = 2; $i <= count(Request::segments()); $i++)
                <li class="breadcrumb-item">
                    <a href="{{ admin_url(implode('/',array_slice(Request::segments(),1,$i-1))) }}">
                        {{ucfirst(Request::segment($i))}}
                    </a>
                </li>
            @endfor
        </ol>
    @endif
</nav>
