<div class="main_head main_indent">
{{--    {{ wo_render_breadcrumbs({separator: '/', separatorClass: 'sep'}) }}--}}
    {{--{% if product_type == 'credit_cards' %} {#TODO [nkl90]: избавиться от этого#}--}}
    <div class="h1 title_tabs">
        <a class="tab active" href="#"><span class="opt_link">Кредитные карты</span></a>
        <a class="tab" href="#"><span class="opt_link">Дебетовые карты</span></a>
    </div>
    {{--{% elseif product_type == 'debet_cards' %}--}}
    {{--<div class="h1 title_tabs"> <a class="tab" href="{{ path('creditcard_page', {'altName': city.altName}) }}"><span class="opt_link">Кредитные карты</span></a> <a class="tab active" href="#"><span class="opt_link">Дебетовые карты</span></a> </div>--}}
    {{--{% endif %}--}}

    <header class="page_title">
        <div class="page_title_in page_title_bank">
            <h1 class="h1">
                @if(isset($entity->h1))
                    {{$entity->h1}}
                @else
                    @if(isset($shared['seo_record']['title']))
                        {{$shared['seo_record']['title']}}
                    @else
                        {{ $product_title }} @if(isset($shared['city'])) в {{ $shared['city'] }} @endif
                    @endif

                @endif
            </h1>
        </div>
    </header>
    @if(isset($entity->shortDescription) && $entity->shortDescription != null)
        <div class="text">{{ $entity->shortDescription }}</div>
    @else
        @if(isset($seoRecord->description))
            <div class="text">{{ $seoRecord->description }}</div>
        @endif
    @endif

    <!-- end .text-->
</div>
<input type="hidden" id="city" value="@isset($city){{ $city->altName }}@endisset">
<meta id="source_page" product_type="{{ $product_type }}" product_id="">