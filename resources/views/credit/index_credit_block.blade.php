
<div class="credit" data-id="{{ $credit->id }}">

    <div class="head">

        @include ('partials.bank_rating_on_listing', ['product' => $credit])
        <!-- end .l-->
        <div class="c">
            <div class="name">
                <a href="">{{ $credit->name_ru}}</a>
            </div>
            <!-- end .name-->
            <ul class="info">
                <li class="credit_rate"><strong>{{ $credit->percent or $credit->props()->where('percent_rate', '!=', null)->min('percent_rate') }}%</strong> ставка по кредиту</li>
                <li class="payment_per_month"><strong>{{ $credit->ppm }}</strong> тенге в месяц</li>
                <li class="overpay"><strong>{{ $credit->overpay }}</strong> переплата</li>
            </ul>
            <!-- end .info-->
        </div>
        <!-- end .c-->
        <div class="r">
            <a id="to_compare_{{ $credit->id }}" class="to_compare {% if credit.id in app.session.get('kredity') %}active{% endif %}" onclick="comparationListToggle({{ $credit->id }})">
                <span class="ic ic_scale"></span>
                <span class="ic ic_scale dark"></span>
                <span class="ic ic_scale active"></span>
            </a><!-- end .to_compare-->
            <div class="toggle_open"> <span class="ic ic_arrow_thin"></span> <span class="ic ic_arrow_thin dark"></span> <span class="ic ic_arrow_thin active"></span> </div>
            <!-- end .toggle_open-->
        </div>
        <!-- end .r-->
    </div>
    <!-- end .head-->
    <div class="in" data-id="{{ $credit->id }}"></div>
    <!-- end .in-->
</div>
