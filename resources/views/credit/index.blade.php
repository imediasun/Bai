@extends('layouts.app')
@section('content')
    @include('partials.page_title', ['product_type' => 'Кредит', 'product_title' => 'Кредиты'])
    <div class="bg_wrap bg_grey">
        <div class="raside_hold">
            <div class="side_l">
                <div class="form form_filter main_indent">
                    <div class="form_in">
                        <form class="filter_form" name="filter_form" id="filter_form" method="post">
                            <div class="form_row form_money">
                                <div class="form_el">
                                    <div class="label">Стоимость кредита</div>
                                    <div class="input input_big">
                                        <input name="calc[tot]" id="amount_input" type="text" value="200 000" class="prop_filter">
                                    </div>
                                </div>
                                <!-- end .form_el-->
                                <div class="form_el">
                                    <div class="form_row_inner">
                                        <div class="form_el input_checks">
                                            <label>
                                                <input type="radio" value="kzt" checked name="calc[curr]" class="prop_filter outtaHere cC currency">
                                                <span>₸</span>
                                            </label>
                                            <label>
                                                <input type="radio" value="usd" name="calc[curr]" class="prop_filter outtaHere cC currency">
                                                <span>$</span>
                                            </label>
                                            <label>
                                                <input type="radio" value="eur" name="calc[curr]" class="prop_filter outtaHere cC currency">
                                                <span>€</span>
                                            </label>
                                        </div>
                                        <!-- end .form_el-->

                                        <div class="form_el">
                                            <div class="label">Срок кредита</div>
                                            <div class="input input_big">
                                                <select name="calc[period]" id="credit_period" class="curselect prop_filter">
                                                    @foreach($credit_period as $key => $period)
                                                        <option value="{{ $key}}" {{ $key == 12 ? 'selected' : '' }}>{{ $period }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <!-- end .form_el-->
                                    </div>
                                    <!-- end .form_row_inner-->
                                </div>
                                <!-- end .form_el-->
                            </div>
                            <div class="form_additional">
                                <div class="toggle">
                                    <div><span class="opt_link">Дополнительные данные: возраст, справки, залог, поручитель</span>
                                        <div class="ic_place"><span class="ic ic_arrow white"></span><span class="ic ic_arrow yellow"></span></div>
                                    </div>
                                </div>
                                <!-- end .toggle-->
                                <div class="in">
                                    <div class="main_indent">
                                        <div class="form_row form_row_autowidth">
                                            <div class="form_el">
                                                <div class="label" >Срок рассмотрения</div>
                                                <div class="input">
                                                    <select name="prop[time_for_consideration]" id="time_for_consideration" class="prop_filter curselect curselect_small">
                                                        <option value="">Не важно</option>
                                                        <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 1){{ 'selected="selected"' }}@endif value="1">в день обращения</option>
                                                        <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 3){{ 'selected="selected"' }}@endif value="3">до 3 дней</option>
                                                        <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 7){{ 'selected="selected"' }}@endif value="7">до 7 дней</option>
                                                        <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 14){{ 'selected="selected"' }}@endif value="14">до 14 дней</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- end .main_indent-->

                                </div>

                                <!-- end .in-->
                            </div>
                            <!-- end .form_additional-->
                        </form>
                        <script type="text/javascript">
                            $(function () {
                                $('.prop_filter').on('change', function () {
                                    var data = $('#filter_form').serializeArray();
                                    var block = '<div class="loading_cover"></div>';

                                    $.ajax({
                                        url: Routing.generate('credit_ajax_get_filtered'),
                                        cache: false,
                                        type: "POST",
                                        dataType: "json",
                                        data: {
                                            data: data
                                        },
                                        beforeSend: function( xhr ) {
                                            $('.credits').addClass('cover_hold');
                                            $('.credits').append(block);
                                        },
                                        success: function (response) {
                                            $('#offers_list').html(response);
                                            initCredit();
                                        }
                                    });
                                });
                            });
                        </script>

                    </div>
                    <!-- end .form_in-->
                </div>

                <!-- end .form-->
                @if(isset($credits) && count($credits) > 0)
                    <div class="sort main_indent">
                        <div class="in">
                            <div class="text">Показывать первыми:</div>
                            <a class="link sort_links" data-class-toggle="less_overpay" href="#"><span class="opt_link">Переплата меньше</span></a>
                            <a class="link active sort_links" data-class-toggle="less_payment" href="#"><span class="opt_link">Платеж меньше</span></a>
                            <a class="link sort_links" data-class-toggle="less_rate" href="#"><span class="opt_link">Ставка меньше</span></a></div>
                        <!-- end .in-->
                    </div>
                    <!-- end .sort-->
                    <div class="credits offers_list" id="offers_list">
                        @foreach($credits as $key => $credit)
                            @include('credit.index_credit_block')
                        @endforeach
                        {% for key, credit in credits %}
                        {% include 'credit/index_credit_block.html.twig' %}
                        {% endfor %}
                    </div>
                    <div class="info_block">
                        <div class="pic"></div>
                        Данный расчет погашения кредита является предварительным. При обращении
                        в&nbsp;отделение Банка, Вам будет предоставлен точный расчет.
                    </div>
                    @else
                    @include('common.nothing_found')
                @endif

                @isset($uniqueSeoRecords->fullDescription)
                    <div class="info_block">{{ $uniqueSeoRecords->fullDescription }}</div>
                @endisset
            </div>
            <!-- end .side_l-->
            <div class="side_r">
                <!-- end .side_block-->
                <div class="side_block">
                    <div class="fast_select">
                        @include('common.aside_box_fast_filter', ['item' => 'CREDIT'])
                    </div>
                    <!-- end .fast_select-->
                </div>
                <!-- end .side_block-->
            </div>
            <!-- end .side_r-->
        </div>
        <!-- end .raside_hold-->
        <div class="reviews_slider main_indent">
            @include('common.aside_box_bank_reviews')
        </div>
        <!-- end .reviews_slider-->
    </div>
@endsection