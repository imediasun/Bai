
<div class="credit" data-id="{{ $credit->id }}">

    <div class="head">

        @include ('partials.bank_rating_on_listing', ['product' => $credit])
        <!-- end .l-->
        <div class="c">
            <div class="name">
                <a href="">{{ $credit->name_ru}}</a>
            </div>
{{--            {{dd($credit->currency)}}--}}
            <!-- end .name-->
            <ul class="info">
                <li class="credit_rate"><strong>{{ empty($credit->percent) ? $credit->percent_rate : $credit->percent }}%</strong> ставка в год</li>
                <li class="payment_per_month"><strong>{{ $credit->ppm }}</strong> {{ $credit->currency }} в месяц</li>
                <li class="overpay"><strong>-{{ $credit->overpay }}</strong> переплата</li>
            </ul>
            <!-- end .info-->
        </div>
        <!-- end .c-->
        <div class="r">
            {{--<a id="to_compare_{{ $credit->id }}" class="to_compare {% if credit.id in app.session.get('kredity') %}active{% endif %}" onclick="comparationListToggle({{ $credit->id }})">--}}
            <a id="to_compare_{{ $credit->id }}" class="to_compare" onclick="comparationListToggle({{ $credit->id }}, 'credit')">
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
    <div class="in" data-id="{{ $credit->id }}">
        <ul class="benefits_checks">
            <li><span class="ic ic_check active"></span> <strong>Сумма:</strong> @if(!empty($credit->amount)) {{ $credit->amount }}{{ $credit->currency }}@else{{'без ограничений'}}@endif</li>
            <li><span class="ic ic_check active"></span> <strong>Срок:</strong> {{ $credit->period }} месяцев</li>
            <li><span class="ic ic_check active"></span> <strong>Процентная ставка:</strong> от {{ $credit->percent != null ? $credit->percent : $credit->percent_rate  }}%</li>
            <li><span class="ic ic_check active"></span> <strong>Срок рассмотрения:</strong> {{ $credit->time_for_consideration }} дней</li>
            <li><span class="ic ic_check active"></span> <strong>Возраст заемщика:</strong>  {{ $credit->age == 18 ? 'от 18 лет' : 'от 23 до 63 лет'}} </li>
            <li><span class="ic ic_check active"></span> <strong>ГЭСВ:</strong> {{ $credit->gesv }}%</li>
            <li><span class="ic ic_check active"></span> <strong>Досрочное погашение:</strong> {{ $credit->have_early_repayment ? 'есть' : 'нет' }}</li>
            <li><span class="ic ic_check active"></span> <strong>Подтверждение дохода:</strong> {{ $credit->income_confirmation}}</li>
            <li><span class="ic ic_check active"></span> <strong>Обеспечение:</strong> {{ $credit->credit_security }}</li>
            <li><span class="ic ic_check active"></span> <strong>Схема погашения:</strong> {{ $credit->repayment_structure == 'diff' ? 'дифференцированная' : 'аннуитетная' }}</li>
            <li><span class="ic ic_check active"></span> <strong>Минимальный официальных доход:</strong> @if(!empty($credit->minimum_income)) {{ $credit->minimum_income }}{{ ' ₸' }}@else{{ '60 000 ₸' }}@endif</li>
            <li><span class="ic ic_check active"></span> <strong>Общий стаж работы:</strong> от {{ $credit->occupational_life or 3 }} месяцев</li>
        </ul>
        <div class="actions">
            <div><a onclick="comparationListToggle({{ $credit->id }})" class="to_compare2" href="#"><span class="ic ic_scale white"></span><span class="opt_link">Сравнить</span></a></div>
            <a class="btn btn_round btn_orange get_online" href="{{ $credit->online_url or '#' }}"><span>Оформить кредит онлайн</span></a>
            <div><a href="#"><span>Условия</span> <i class="arrow">→</i></a></div>
        </div>
    </div>

    <!-- end .in-->
</div>

