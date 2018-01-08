{{--{% set fastFilters = app_tools.getFastFilters(item) %}--}}
{{--{% if fastFilters %}--}}

    <div class="h4">Быстрый подбор</div>
    <ul>
        @foreach (CommonHelper::getFastFilters($item) as $fastFilter )
{{--            <li><a href="{{ path(item|lower ~ '_page', {'altName': fastFilter.altName}) }}" class="opt_link">{{ fastFilter.name }}</a></li>--}}
            <li><a href="{{ $fastFilter->url }}" class="opt_link">{{ $fastFilter->name_ru }}</a></li>
        @endforeach
    </ul>
{{--{% endif %}--}}