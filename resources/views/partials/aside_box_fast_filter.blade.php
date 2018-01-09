@php
    $fastFilters = CommonHelper::getFastFilters($item);
@endphp
{{--{% if fastFilters %}--}}
@if(isset($fastFilters) && !empty($fastFilters))
    <div class="h4">Быстрый подбор</div>
    <ul>
        @foreach ($fastFilters as $fastFilter )
{{--            <li><a href="{{ path(item|lower ~ '_page', {'altName': fastFilter.altName}) }}" class="opt_link">{{ fastFilter.name }}</a></li>--}}
            <li><a href="/kredity/{{ $fastFilter->alt_name_ru }}" class="opt_link">{{ $fastFilter->name_ru }}</a></li>
        @endforeach
    </ul>
@endif