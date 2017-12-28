{{--{% set routePrefix = app.request.attributes.get('routePrefix')|default('') %}--}}
{{--{% set routePrefix = routePrefix ? routePrefix ~ '_' : '' %}--}}
{{--{% set currentCity = city_manager.getCity() %}--}}
<nav class="aside">
    <div class="side_menu">
        <div class="in">
            {{--<a class="link {% if app.request.get('_route') == 'credit_page' %} active{% endif %}" href="{{ path('credit_page', {'altName': currentCity['altName']}) }}"><img src="{{ asset('img/pics/cash.png') }}" width="92" height="92" alt=""/>Кредиты</a><!-- end .link-->--}}
            {{--<a class="link {% if app.request.get('_route') == 'creditcard_page' %} active{% endif %}" href="{{ path('creditcard_page', {'altName': currentCity['altName']}) }}"><img src="{{ asset('img/pics/card.png') }}" width="92" height="92" alt=""/>Карты</a><!-- end .link-->--}}
            {{--<a class="link {% if app.request.get('_route') == 'mortgage_page' %} active{% endif %}" href="{{ path('mortgage_page', {'altName': currentCity['altName']}) }}"><img src="{{ asset('img/pics/house.png') }}" width="92" height="92" alt=""/>Ипотека</a><!-- end .link-->--}}
            {{--<a class="link {% if app.request.get('_route') == 'auto_credit_page' %} active{% endif %}" href="{{ path('auto_credit_page', {'altName': currentCity['altName']}) }}"><img src="{{ asset('img/pics/car.png') }}" width="92" height="92" alt=""/>Автокредит</a><!-- end .link-->--}}
            {{--<a class="link {% if app.request.get('_route') == 'deposit_page' %} active{% endif %}" href="{{ path('deposit_page', {'altName': currentCity['altName']}) }}"><img src="{{ asset('img/pics/safe.png') }}" width="92" height="92" alt=""/>Депозиты</a><!-- end .link-->--}}
            {{--<a class="link {% if app.request.get('_route') == 'loan_page' %} active{% endif %}" href="{{ path('loan_page', {'altName': currentCity['altName']}) }}"><img src="{{ asset('img/pics/wallet.png') }}" width="92" height="92" alt=""/>Микрозайм</a><!-- end .link-->--}}
            {{--<a class="link {% if app.request.get('_route') == 'exchange_rates_page' %} active{% endif %}" href="{{ path('exchange_rates_page', {'altName': currentCity['altName']}) }}"><img src="{{ asset('img/pics/exchange.png') }}" width="92" height="92" alt=""/>Курс валют</a><!-- end .link-->--}}
            {{--<a class="link {% if app.request.get('_route') == 'bank_page' %} active{% endif %}" href="{{ path('bank_page_without_city', {'altName': currentCity['altName']}) }}"><img src="{{ asset('img/pics/bank.png') }}" width="92" height="92" alt=""/>Банки</a><!-- end .link-->--}}
        </div>
    </div>
    <!-- end .side_menu-->
</nav>