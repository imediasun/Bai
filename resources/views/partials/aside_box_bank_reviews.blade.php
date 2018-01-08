{{--{% if reviews is not defined %}--}}
    {{--{% set reviews = app_tools.getReviews() %}--}}
{{--{% endif %}--}}
{{--{% if reviews is not empty %}--}}
    <div class="h2">Отзывы о банках</div>
    <div class="hold">
        @foreach(\App\Review::getReviews()->get() as $review)
            <div class="el">
                <div class="message review2">
                    <div class="wrap top">
                        <div class="wrap_in">
                            <div class="big">{{ $review->bank->name_ru }}</div>
                            <!-- end .big-->
                            <div class="head2">
                                <div class="link"><a href="#">{{ $review->bank->name_ru }}</a> <i class="arrow">→</i> </div>
                                <!-- end .link-->
                                <div class="stars medium">
                                    <div style="width: 80%"></div>
                                </div>
                                <!-- end .stars-->
                            </div>
                            <!-- end .head2-->
                            <div class="text">{{ $review->review }}</div>
                            <!-- end .text-->
                        </div>
                        <!-- end .wrap_in-->
                    </div>
                    <!-- end .top-->
                </div>
                <!-- end .review2-->
            </div>
        @endforeach
    </div>

    <div class="btns">
        <a href="#" class="btn btn_sq btn_sq_small btn_green btn_border"><span>Все отзывы</span></a>
        <a href="#" class="btn btn_sq btn_sq_small btn_green"><span>Оставить отзыв</span></a>
    </div>

{{--{% endif %}--}}
