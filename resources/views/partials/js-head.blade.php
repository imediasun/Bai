@yield('scripts_head')

<script src="https://yastatic.net/share2/share.js" async="async"></script>
<script src="{{ asset('js/vendor/jquery-1.11.3.min.js') }}"></script>
<!-- JS Routing -->
{{--<script src="{{ asset('js/router.js') }}"></script>--}}
<script src="{{ asset('js/jquery.cookie.js') }}"></script>{#mg#}
{{--<script src="{{ path('fos_js_routing_js', { callback: 'fos.Router.setData' }) }}"></script>--}}
<script type="text/javascript" src="{{ asset('js/vendor/chart.js') }}"></script>

<script src="{{ asset('js/vendor/modernizr-2.8.3.min.js') }}"></script>
<script src="https://api-maps.yandex.ru/2.1/?lang={{ yandex_map_locale }}" type="text/javascript"></script>