@if (count($breadcrumbs))



@endif

<nav class="path">
    @foreach ($breadcrumbs as $breadcrumb)

        @if ($breadcrumb->url && !$loop->last)
            <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
            <span class="sep">/</span>
        @else
            <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
        @endif

    @endforeach
    {{--<a href="/">Главная</a>--}}
    {{--<span class="sep">/</span>--}}
    {{--<a href="credit_index">Кредиты</a>--}}
</nav>

{{--<nav>--}}
    {{--<div class="nav-wrapper">--}}
        {{--<div class="col s12">--}}
            {{--@foreach ($breadcrumbs as $breadcrumb)--}}

                {{--@if ($breadcrumb->url && !$loop->last)--}}
                    {{--<a href="{{ $breadcrumb->url }}" class="breadcrumb">{{ $breadcrumb->title }}</a>--}}
                {{--@else--}}
                    {{--<span class="breadcrumb">{{ $breadcrumb->title }}</span>--}}
                {{--@endif--}}

            {{--@endforeach--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</nav>--}}