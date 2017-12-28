{{--{% set routePrefix = app.request.attributes.get('routePrefix')|default('') %}--}}
{{--{% set routePrefix = routePrefix ? routePrefix ~ '_' : '' %}--}}
{{--{% set currentCity = city_manager.getCity() %}--}}
<header class="header">
    <div class="container aside_hold">
        <div class="main_content">
            <div class="main_indent">
                <div class="header_menu_toggle">
                    <div class="toggle_city"> <span class="opt_link">{{ $shared['city']['name_ru'] }}</span>
                        <div class="ic ic_select white"></div>
                    </div>
                    <!-- end .toggle_city-->
                </div>
                <div class="round_link link_menu_place"></div>
                <label class="round_link border link_menu" for="show_aside_menu"> <span></span> <span></span> <span></span><em class="ic ic_cross white"></em></label>

                <!-- end .header_menu_toggle-->
                <div class="logo"><a href="/"><img src="{{ asset('img/logo.svg')}}" width="262" height="78" alt="bai.kz"/></a></div>
                <!-- end .logo-->
                <div class="search">
                    <input type="text" placeholder="Поиск по сайту">
                    <button type="submit"><span class="ic ic_search white"></span></button>
                </div>
                <!-- end .search-->
                <a class="round_link link_lock" href="#"><span class="ic ic_lock"></span><span class="ic ic_lock white"></span></a><!-- end .round_link-->

            </div>
            <!-- end .main_indent-->
        </div>
        <!-- end .main_content-->
        <div class="aside">
            <div class="header_menu_toggle">
                <div class="round_link border toggle_menu">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <!-- end .toggle_menu-->
            </div>
            <!-- end .header_menu_toggle-->
        </div>
        <!-- end .aside-->
        <nav class="header_menu menu">
            <div class="main_indent">
                <ul class="main">
                    <li><strong>Кредиты</strong>
                        <ul>
                            <li><a href="#">Потребительские</a></li>
                        </ul>
                    </li>
                    <li><strong>Ипотека</strong>
                        <ul>
                            <li><a href="#">Без первоначального залога</a></li>
                        </ul>
                    </li>
                    <li><strong>Депозиты</strong>
                        <ul>
                            <li><a href="#">Выгодные депозиты</a></li>
                        </ul>
                    </li>
                    <li><strong>Курсы валют</strong>
                        <ul>
                            {{--{#<li><a href="#">Добавить обменник</a></li>#}--}}
                            <li><a href="#">Курсы в вашем городе</a></li>
                        </ul>
                    </li>
                    <li><strong>Автокредиты</strong>
                        <ul>
                            <li><a href="#">На новую машину</a></li>
                        </ul>
                    </li>
                    <li><strong>Микрозаймы</strong>
                        <ul>
                            <li><a href="#">Терпимые</a></li>
                        </ul>
                    </li>
                    <li><strong>Карты</strong>
                        <ul>
                            <li><a href="#">Дебетовые</a></li>
                            <li><a href="#">Кредитные</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- end .main_indent-->
        </nav>
        {{--{#{% include 'top-menu.html.twig' %}#}--}}
        <!-- end .header_menu-->
        <nav class="header_menu city">
            <div class="main_indent">
                {{--<ul class="main">--}}
                    {{--{% set cities = app_tools.getAllCities() %}--}}
                    {{--{% set oldLetter = cities[0].name|slice(0,1)|upper %}--}}
                    {{--<li>--}}
                        {{--<div class="page_header_location_list_h">{{ oldLetter }}</div>--}}
                        {{--<ul>--}}
                            {{--{% for city in app_tools.getAllCities() %}--}}
                            {{--{% set cityLetter = city.name|slice(0,1)|upper %}--}}
                            {{--{% if oldLetter != cityLetter %}--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                        {{--<div class="page_header_location_list_h">{{ cityLetter }}</div>--}}
                        {{--<ul>--}}
                            {{--{% set oldLetter = cityLetter %}--}}
                            {{--{% endif %}--}}
                            {{--{#<li><a href="{% if routePrefix != '' %}{{ path(routePrefix ~ 'page', {'altName': city.altName}) }}{% else %}#{% endif %}" class="city-link" data-city-name="{{ city.altName }}">{{ city.name|capitalize }}</a></li>#}--}}
                            {{--{% endfor %}--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            </div>

            <!-- end .main_indent-->
        </nav>
        {{--{% if routePrefix == '' %}--}}
            {{--<script type="text/javascript">--}}
                {{--$('.city-link').on('click', function() {--}}
                    {{--var city = $(this).data('city-name');--}}
                    {{--$.ajax({--}}
                        {{--url: Routing.generate('ajax_set_city'),--}}
                        {{--async: false,--}}
                        {{--cache: false,--}}
                        {{--type: "POST",--}}
                        {{--dataType: "json",--}}
                        {{--data: {--}}
                            {{--city: city,--}}
                            {{--route: '{{ app.request.attributes.get('_route') }}',--}}
                            {{--routeParams: '{{ app.request.attributes.get('_route_params')|json_encode() }}'--}}
                        {{--},--}}
                        {{--error: function (jqXHR, textStatus, errorThrown) {--}}
                            {{--alert(textStatus + ': ' + errorThrown);--}}
                        {{--}--}}
                    {{--}).done(function (response) {--}}
                        {{--window.location.href = response.href;--}}
                    {{--});--}}
                {{--});--}}
            {{--</script>--}}
        {{--{% endif %}--}}
        <!-- end .header_menu-->
    </div>
    <!-- end .container-->
</header>
