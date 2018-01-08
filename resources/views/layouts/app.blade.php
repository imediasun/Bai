{{--{% set currentRoute = app.request.attributes.get('_route') %}--}}
{{--{% set currentPath = path(app.request.attributes.get('_route'), app.request.attributes.get('_route_params')) %}--}}
{{--{% set seoRecord = getSeoRecord() %}--}}
{{--{% set uniqueSeoRecords = getUniqueSeoRecords() %}--}}
{{--{% set res = app_tools.links(currentPath)  %}--}}
{{--{% set current_city = city_manager.getCity() %}--}}

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<html lang="{{app()->getLocale()}}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
{{--    <title>{{ $seoRecord->headerTitle }}</title>--}}
    {{--{#<meta name="description" content="{{ seoRecord.metaDescription }}">#}--}}
    @yield('seo_meta')
    <meta property="og:locale" content="ru_RU" />
    <meta property="og:type" content="website" />
    {{--{#<meta property="og:title" content="{{ seoRecord.headerTitle }}" />#}--}}
    {{--{#<meta property="og:description" content="{{ seoRecord.metaDescription }}" />#}--}}

    <meta name="viewport" content="width=device-width">

    @include('partials.css')
    @include('partials.js-head')
    {{--    {#<link rel="stylesheet" href="{{ asset('css/main.css') }}">#}--}}

    {{--{% block head_javascripts %}{% endblock %}--}}
</head>
<body class="{% block page_class %}{% endblock %}">
    <input type="checkbox" class="outtaHere" id="show_aside_menu">
{{--{% block body %}{% endblock %}--}}
{{--@yield('body')--}}
    <div class="mainwrap">
    <div class="mainbg_l"></div>

    {{--{% include 'common/header.html.twig' %}--}}
    @include('partials.header')
    <div style="display: none;" id="compare_bar" class="filter_info">
        <div class="container">
            <div class="l"><span class="ic ic_scale_big"></span>Выбрано<div class="show_mob"></div> <span class="loans_count"></span> кредита</div>
            <!-- end .l-->
            <a id="compare_button" class="btn btn_round btn_round_small btn_border_green"><span >Сравнить</span></a>
            <div class="r" onclick="dropComparison()"> <span class="clear opt_link">Сбросить</span> </div>
            <!-- end .r-->
        </div>
        <!-- end .container-->
    </div>
    <div class="wrapper">
        <div class="container aside_hold">
            <div class="main_content">
                @yield('content')
                @include('partials.page_footer')
            </div>
            @include('partials.sidebar')
        </div>
    </div>
    @include('partials.footer')
    <div class="modal_container"></div>
    <div id="compare_list" style="display: none;">
        <div class="fader"></div>
        <div class="modal">
            <div class="modal_in">
                <div class="compare">
                    <div class="h1">Сравнение <span class="loans_count"></span> кредитов</div>
                    <div id="compare_table" class="compare_table"></div>
                </div>
                <div id="compar_close_button" class="close"><span class="ic ic_cross_big"></span></div>
            </div>
        </div>
    </div>
        @include('partials.js')

</div>
{{--@include('city_popup')--}}
{{--{% include 'common/city_popup.html.twig' %}--}}
{{--{# Footer JavaScripts Block #}--}}
{{--{% block footer_javascripts %}{% endblock %}--}}

</body>
</html>