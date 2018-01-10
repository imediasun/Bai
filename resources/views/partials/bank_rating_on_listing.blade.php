@if (isset($product->bank) && !empty($product->bank))
<div class="l">
    <div class="image"><img src="{{ $product->bank->logo }}" width="133" height="85" alt=""/> </div>
    <!-- end .image-->
    <div class="cover"> @if ($product->online_url)<a class="btn btn_round btn_round_small btn_border_orange" href="{{ $product->online_url}}"><span>Оформить</span></a>@endif </div>
    <!-- end .cover-->
    <div class="rating">
        <div class="stars">
            @php $rate = $product->bank->rating_clients @endphp
            @php $ratePercent = $rate*100/5 @endphp
            <div style="width: {{ $ratePercent }}%;"></div>
        </div>
        <!-- end .stars-->
        <span>{{ $rate }}0 </span>
        <div class="rating_popup">
            <div class="rating_popup_row rating_popup_row_head">
                <div class="el">
                    <div class="small">Общая оценка</div>
                     @if ($rate > 1 && $rate < 2.5)
                        <div class="big1">Очень плохо</div>
                    @elseif ($rate > 2.5 && $rate < 3.5)
                        <div class="big1">Средне</div>
                    @elseif ($rate > 3.5 && $rate < 4.5)
                        <div class="big1">Хорошо</div>
                    @elseif ($rate > 4)
                        <div class="big1">Отлично</div>
                    @elseif ($rate == 0.00)
                        <div class="big1">Нет оценок</div>
                    @endif
                </div>
                <!-- end .el-->
                <div class="el">
                    <div class="stars medium">
                        <div style="width: {{ $ratePercent }}%"></div>
                    </div>
                    <!-- end .stars-->
                    {{ $rate }}
                </div>
                <!-- end .el-->
            </div>
            <!-- end .rating_popup_row-->
            <div class="rating_popup_row">
                <div class="el">
                    <div class="big2">Рейтинг клиентов @if ($rate == 0.00)(нет оценок)@endif</div>
                    <div class="stars medium">
                        <div style="width: {{ $ratePercent }}%"></div>
                    </div>
                    <!-- end .stars-->
                    <a href="#">{{ $rate }}</a></div>
                <!-- end .el-->
                {{--{#<div class="el">--}}
                    {{--<div class="big2">По активам</div>--}}
                    {{--<div class="stars medium">--}}
                        {{--{% set rating = product.bank.ratingAssets %}--}}
                        {{--<div style="width: {{ rating*100/5 }}%"></div>--}}
                    {{--</div>--}}
                    {{--<!-- end .stars-->--}}
                    {{--<a href="#">{{ rating }}</a>--}}
                {{--</div>#}--}}
                <!-- end .el-->
            </div>
            <!-- end .rating_popup_row-->
            {{--{#<div class="rating_popup_row">--}}
                {{--<div class="el">--}}
                    {{--<div class="big2">По прибыли</div>--}}
                    {{--<div class="stars medium">--}}
                        {{--{% set rating = product.bank.ratingProfit %}--}}
                        {{--<div style="width: {{ rating*100/5 }}%"></div>--}}
                    {{--</div>--}}
                    {{--<!-- end .stars-->--}}
                    {{--<a href="#">{{ rating }}</a></div>--}}
                {{--<!-- end .el-->--}}
                {{--<div class="el">--}}
                    {{--<div class="big2">По депозитам</div>--}}
                    {{--<div class="stars medium">--}}
                        {{--{% set rating = product.bank.ratingDeposit %}--}}
                        {{--<div style="width: {{ rating*100/5 }}%"></div>--}}
                    {{--</div>--}}
                    {{--<!-- end .stars-->--}}
                    {{--<a href="#">{{ rating }}</a></div>--}}
                {{--<!-- end .el-->--}}
            {{--</div>#}--}}
            {{--<!-- end .rating_popup_row-->--}}
            {{--{#<div class="rating_popup_row">#}--}}
            {{--{#<div class="el">#}--}}
            {{--{#<div class="big2">По банкоматам</div>#}--}}
            {{--{#<div class="stars medium">#}--}}
            {{--{#<div style="width: 100%"></div>#}--}}
            {{--{#</div>#}--}}
            {{--{#<!-- end .stars-->#}--}}
            {{--{#<a href="#">3561</a></div>#}--}}
            {{--{#<!-- end .el-->#}--}}
            {{--{#<div class="el">#}--}}
            {{--{#<div class="big2">По отзывам</div>#}--}}
            {{--{#<div class="stars medium">#}--}}
            {{--{#<div style="width: 100%"></div>#}--}}
            {{--{#</div>#}--}}
            {{--{#<!-- end .stars-->#}--}}
            {{--{#<a href="#">278</a></div>#}--}}
            {{--{#<!-- end .el-->#}--}}
            {{--{#</div>#}--}}
            <!-- end .rating_popup_row-->
        </div>
        <!-- end .rating_popup-->
    </div>
    <!-- end .rating-->
</div>
@endif