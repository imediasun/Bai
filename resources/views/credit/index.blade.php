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
                                <div class="in" style="height: auto;">
                                    <div class="main_indent">
                                        <div class="form_row form_row_autowidth">
                                            <div class="form_el">
                                                <div class="label" >Срок рассмотрения</div>
                                                <div class="input">
                                                    <select name="prop[time_for_consideration]" id="time_for_consideration" class="prop_filter curselect curselect_small">
                                                        <option value="none">Не важно</option>
                                                        <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 1){{ 'selected="selected"' }}@endif value="1">в день обращения</option>
                                                        <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 3){{ 'selected="selected"' }}@endif value="3">до 3 дней</option>
                                                        <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 7){{ 'selected="selected"' }}@endif value="7">до 7 дней</option>
                                                        <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 14){{ 'selected="selected"' }}@endif value="14">до 14 дней</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Возраст</div>
                                                <div class="input input_big">
                                                    <select name="calc[age]" id="credit_age" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        <option @if(Request::has('age') && Request::input('age') == '18'){{ 'selected' }}@endif value="18">от 18 лет</option>
                                                        <option @if(Request::has('age') && Request::input('age') == '23-63'){{ 'selected' }}@endif value="23-63">от 23 до 63 лет</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Подтверждение дохода</div>
                                                <div class="input input_big">
                                                    <select name="calc[income_confirmation]" id="credit_income_confirmation" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        <option @if(Request::has('income_confirmation') && Request::input('income_confirmation') == '0' ) selected @endif value="0">Без подтверждения доходов</option>
                                                        <option @if(Request::has('income_confirmation') && Request::input('income_confirmation') == '1' ) selected @endif value="1">С подтверждением доходов</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Обеспечение</div>
                                                <div class="input input_big">
                                                    <select name="calc[credit_security]" id="credit_security" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        <option @if(Request::has('credit_security') && Request::input('credit_security') == 'none' ) selected @endif value="none">без залога и поручительства</option>
                                                        <option @if(Request::has('credit_security') && Request::input('credit_security') == 'guarantor' ) selected @endif value="guarantor">поручитель</option>
                                                        <option @if(Request::has('credit_security') && Request::input('credit_security') == 'deposit' ) selected @endif value="deposit">залог - депозит</option>
                                                        <option @if(Request::has('credit_security') && Request::input('credit_security') == 'immovables_current' ) selected @endif value="immovables_current">залог - имеющееся недвижимость</option>
                                                        <option @if(Request::has('credit_security') && Request::input('credit_security') == 'immovables_bying' ) selected @endif value="immovables_bying">залог - приобретемая недвижимость</option>
                                                        <option @if(Request::has('credit_security') && Request::input('credit_security') == 'auto_current' ) selected @endif value="auto_current">залог - имеющееся авто</option>
                                                        <option @if(Request::has('credit_security') && Request::input('credit_security') == 'auto_buying' ) selected @endif value="auto_buying">залог - приобретаемое авто</option>
                                                        <option @if(Request::has('credit_security') && Request::input('credit_security') == 'money' ) selected @endif value="money">залог - денежные средства</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Регистрация</div>
                                                <div class="input input_big">
                                                    <select name="calc[registration]" id="registration" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        <option @if(Request::has('registration') && Request::input('registration') == 'none'){{ 'selected' }}@endif value="none">не важно</option>
                                                        <option @if(Request::has('registration') && Request::input('registration') == 'const'){{ 'selected' }}@endif value="const">постоянная</option>
                                                        <option @if(Request::has('registration') && Request::input('registration') == 'const_in_area'){{ 'selected' }}@endif value="const_in_area">постоянная в районе обращения</option>
                                                        <option @if(Request::has('registration') && Request::input('registration') == 'temp'){{ 'selected' }}@endif value="temp">временная</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Цель кредита</div>
                                                <div class="input input_big">
                                                    <select name="calc[credit_goal]" id="credit_goal" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        <option @if(Request::has('credit_goal') && Request::input('credit_goal') == 'none'){{ 'selected' }}@endif value="none">не важно</option>
                                                        <option @if(Request::has('credit_goal') && Request::input('credit_goal') == 'any'){{ 'selected' }}@endif value="any">любая</option>
                                                        <option @if(Request::has('credit_goal') && Request::input('credit_goal') == 'emergency_needs'){{ 'selected' }}@endif value="emergency_needs">неотложные нужды</option>
                                                        <option @if(Request::has('credit_goal') && Request::input('credit_goal') == 'just_money'){{ 'selected' }}@endif value="just_money">просто деньги</option>
                                                        <option @if(Request::has('credit_goal') && Request::input('credit_goal') == 'goods'){{ 'selected' }}@endif value="goods">товары</option>
                                                        <option @if(Request::has('credit_goal') && Request::input('credit_goal') == 'business'){{ 'selected' }}@endif value="business">бизнес</option>
                                                        <option @if(Request::has('credit_goal') && Request::input('credit_goal') == 'refinancing'){{ 'selected' }}@endif value="refinancing">рефинансирование</option>
                                                        <option @if(Request::has('credit_goal') && Request::input('credit_goal') == 'medication'){{ 'selected' }}@endif value="medication">лечение</option>
                                                        <option @if(Request::has('credit_goal') && Request::input('credit_goal') == 'education'){{ 'selected' }}@endif value="education">образование</option>
                                                        <option @if(Request::has('credit_goal') && Request::input('credit_goal') == 'traveling'){{ 'selected' }}@endif value="traveling">путешествие</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Оформление кредита</div>
                                                <div class="input input_big">
                                                    <select name="calc[credit_formalization]" id="credit_formalization" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        <option @if(Request::has('credit_formalization') && Request::input('credit_formalization') == 'online'){{ 'selected="selected"' }}@endif value="online">онлайн заявка</option>
                                                        <option @if(Request::has('credit_formalization') && Request::input('credit_formalization') == 'office'){{ 'selected="selected"' }}@endif value="office">в отделении банка</option>
                                                        <option @if(Request::has('credit_formalization') && Request::input('credit_formalization') == 'both'){{ 'selected="selected"' }}@endif value="both">в отделений банка и онлайн заявка</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Схема погашения</div>
                                                <div class="input input_big">
                                                    <select name="calc[repayment_structure]" id="repayment_structure" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        <option @if(Request::has('repayment_structure') && Request::input('repayment_structure') == 'ann' ) selected @endif value="ann">аннуитетная</option>
                                                        <option @if(Request::has('repayment_structure') && Request::input('repayment_structure') == 'diff' ) selected @endif value="diff">дифференцированная</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Гражданство</div>
                                                <div class="input input_big">
                                                    <select name="calc[have_citizenship]" id="have_citizenship" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        <option @if(Request::has('have_citizenship') && Request::input('have_citizenship') == 1 ) selected @endif value="1">резидент</option>
                                                        <option @if(Request::has('have_citizenship') && Request::input('have_citizenship') == 0 ) selected @endif value="0">не резидент</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Досрочное погашение</div>
                                                <div class="input input_big">
                                                    <select name="calc[have_early_repayment]" id="repayment_structure" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        <option @if(Request::has('have_early_repayment') && Request::input('have_early_repayment') == 1 ) selected @endif value="1">есть</option>
                                                        <option @if(Request::has('have_early_repayment') && Request::input('have_early_repayment') == 0 ) selected @endif value="0">нет</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Способ получения</div>
                                                <div class="input input_big">
                                                    <select name="calc[receive_mode]" id="receive_mode" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        <option @if(Request::has('receive_mode') && Request::input('receive_mode') == 'cash'){{ 'selected' }}@endif value="cash">наличными</option>
                                                        <option @if(Request::has('receive_mode') && Request::input('receive_mode') == 'bank_card'){{ 'selected' }}@endif value="bank_card">на банковскую карту</option>
                                                        <option @if(Request::has('receive_mode') && Request::input('receive_mode') == 'bank_account'){{ 'selected' }}@endif value="bank_account">на банковский счет</option>
                                                        <option @if(Request::has('receive_mode') && Request::input('receive_mode') == 'none'){{ 'selected' }}@endif value="none">не требуется</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form_el">
                                                <div class="label">Зарплатный проект</div>
                                                <div class="input input_big">
                                                    <select name="calc[income_project]" id="income_project" class="curselect prop_filter">
                                                        <option value="none">Не важно</option>
                                                        @foreach(\App\Bank::where('parent_id', null)->get() as $bank)
                                                            <option @if(Request::has('income_project') && Request::input('income_project') == $bank->id){{ 'selected' }}@endif value="{{ $bank->id }}">{{ $bank->name_ru }}</option>
                                                        @endforeach
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
//                                    var data = $('#filter_form').serializeArray();
                                    var data = $('#filter_form').serialize();
//                                    var data = new FormData(('#filter_form'));
//                                    console.dir(data);
                                    var block = '<div class="loading_cover"></div>';

                                    $.ajax({
                                        url: '/ajax/credit-calc',
                                        cache: false,
                                        type: "GET",
                                        dataType: "json",
                                        data: {
                                            data: data
                                        },
                                        beforeSend: function( xhr ) {
                                            $('.credits').addClass('cover_hold');
                                            $('.credits').append(block);
                                        },
                                        success: function (response) {
                                            $('#offers_list').html(response.html);
                                            console.dir(response);
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
                        {{--{% for key, credit in credits %}--}}
                        {{--{% include 'credit/index_credit_block.html.twig' %}--}}
                        {{--{% endfor %}--}}
                    </div>
                    <div class="info_block">
                        <div class="pic"></div>
                        Данный расчет погашения кредита является предварительным. При обращении
                        в&nbsp;отделение Банка, Вам будет предоставлен точный расчет.
                    </div>
                    @else
                    @include('partials.nothing_found')
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
                        @include('partials.aside_box_fast_filter', ['item' => 'CREDIT'])
                    </div>
                    <!-- end .fast_select-->
                </div>
                <!-- end .side_block-->
            </div>
            <!-- end .side_r-->
        </div>
        <!-- end .raside_hold-->
        <div class="reviews_slider main_indent">
            @include('partials.aside_box_bank_reviews')
        </div>
        <!-- end .reviews_slider-->
    </div>
@endsection