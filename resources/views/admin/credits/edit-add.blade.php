@extends('voyager::master')

@section('page_title', __('voyager.generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('css')
    <style>
        .panel .mce-panel {
            border-left-color: #fff;
            border-right-color: #fff;
        }

        .panel .mce-toolbar,
        .panel .mce-statusbar {
            padding-left: 20px;
        }

        .panel .mce-edit-area,
        .panel .mce-edit-area iframe,
        .panel .mce-edit-area iframe html {
            padding: 0 10px;
            min-height: 350px;
        }

        .mce-content-body {
            color: #555;
            font-size: 14px;
        }

        .panel.is-fullscreen .mce-statusbar {
            position: absolute;
            bottom: 0;
            width: 100%;
            z-index: 200000;
        }

        .panel.is-fullscreen .mce-tinymce {
            height:100%;
        }

        .panel.is-fullscreen .mce-edit-area,
        .panel.is-fullscreen .mce-edit-area iframe,
        .panel.is-fullscreen .mce-edit-area iframe html {
            height: 100%;
            position: absolute;
            width: 99%;
            overflow-y: scroll;
            overflow-x: hidden;
            min-height: 100%;
        }
    </style>
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager.generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
    </h1>
    {{--@include('voyager::multilingual.language-selector')--}}
@stop

{{--@include('layouts.voyager.input-tabs', ['title' => 'Page title', 'name' => 'page_title', 'panel_action' => true])--}}
{{--@include('layouts.voyager.input-tabs', ['title' => 'Page heading', 'name' => 'page_heading', 'panel_action' => true])--}}
{{--@include('layouts.voyager.text-area-tabs', ['title' => 'Meta description', 'name' => 'meta_description', 'is_richbox' => false, 'panel_action' => true])--}}
{{--@include('layouts.voyager.text-area-tabs', ['title' => 'Meta keywords', 'name' => 'meta_keywords', 'is_richbox' => false, 'panel_action' => true])--}}
{{--@include('admin.controls.input-select', [--}}
    {{--'title' => 'Цель кредита',--}}
    {{--'name' => 'credit_goal',--}}
    {{--'is_select2' => true,--}}
    {{--'options' => [--}}
        {{--['name' => 'любая','value' => 'any',],--}}
        {{--['name' => 'неотложные нужды','value' => 'emergency_needs',],--}}
        {{--['name' => 'просто деньги','value' => 'just_money',],--}}
        {{--['name' => 'товары','value' => 'goods',],--}}
        {{--['name' => 'бизнес','value' => 'business',],--}}
        {{--['name' => 'рефинансирование','value' => 'refinancing',],--}}
        {{--['name' => 'лечение','value' => 'medication',],--}}
        {{--['name' => 'образование','value' => 'education',],--}}
        {{--['name' => 'путешествие','value' => 'traveling',],--}}
    {{--]--}}
{{--])--}}

@section('content')
    <div class="page-content container-fluid">

        <form class="form-edit-add" role="form" action="@if(isset($dataTypeContent->id)){{ route('voyager.credits.update', $dataTypeContent->id) }}@else{{ route('voyager.credits.store') }}@endif" method="POST" enctype="multipart/form-data">
        {{--<form class="form-edit-add" role="form" action="@if(isset($dataTypeContent->id)){{ route('tst', $dataTypeContent->id) }}@else{{ route('voyager.credits.store') }}@endif" method="POST" enctype="multipart/form-data">--}}
            <!-- PUT Method if we are editing -->
            @if(isset($dataTypeContent->id))
                {{ method_field("PUT") }}
            @endif
            {{ csrf_field() }}

            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#main_tab_common" aria-expanded="true">Общая информация</a></li>
                <li><a data-toggle="tab" href="#main_tab_calcs_fees">Условия и комиссии (для расчетов)</a></li>
                <li><a data-toggle="tab" href="#main_tab_other">Другие условия</a></li>
                <li><a data-toggle="tab" href="#main_tab_custom_options">Кастомные условия</a></li>
            </ul>
            <div class="tab-content">
                <div id="main_tab_common" class="tab-pane fade active in">
                    <div class="row">
                        <div class="col-md-8">
                        <!-- ### BANK ### -->
                            <div class="panel">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="panel-heading">
                                    <h3 class="panel-title"><i class="icon wb-book"></i> Банк</h3>
                                    <div class="panel-actions">
                                        <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="name">Выберите банк</label>
                                        <select class="select2" name="bank">
                                            @foreach($banks as $bank)
                                                <option value="{{$bank->id}}" @if(isset($dataTypeContent->bank_id) && $dataTypeContent->bank_id == $bank->id){{ 'selected="selected"' }}@endif>{{ $bank->name_ru }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div><!-- .panel -->

                            <!-- ### CREDIT NAME ### -->
                            @include('admin.controls.input-tabs', ['title' => 'Название кредита', 'name' => 'name', 'panel_action' => true, 'need_slug' => true])


                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab_short_description" aria-expanded="true">Краткое описание</a></li>
                                <li><a data-toggle="tab" href="#tab_description">Описание</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab_short_description" class="tab-pane fade active in">
                                    <!-- ### CREDIT SHORT DESCRIPTION ### -->
                                    @include('admin.controls.text-area-tabs', ['title' => 'Краткое описание', 'name' => 'short_description', 'is_richbox' => true, 'panel_action' => true])
                                </div>
                                <div id="tab_description" class="tab-pane fade">
                                    <!-- ### CREDIT DESCRIPTION ### -->
                                    @include('admin.controls.text-area-tabs', ['title' => 'Описание', 'name' => 'description', 'is_richbox' => true, 'panel_action' => true])
                                </div>
                            </div>

                            {{--<button class="btn btn-danger btn-add-props">+ доп. свойства</button>--}}


                        </div>
                        <div class="col-md-4">
                            <!-- ### DETAILS ### -->
                            <div class="panel panel panel-bordered panel-warning">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><i class="icon wb-clipboard"></i> Детали</h3>
                                    <div class="panel-actions">
                                        <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="name">Онлайн URL</label>
                                        <input type="text" class="form-control" id="online_url" name="online_url" placeholder="URL">
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Акция</label>
                                        <input type="text" class="form-control" id="promo" name="promo" placeholder="promo">
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Сортировка</label>
                                        <input type="text" class="form-control" id="sort_order" name="sort_order" placeholder="" value="@if(isset($dataTypeContent->sort_order)){{ $dataTypeContent->sort_order }}@else{{ 10 }}@endif">
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="1" class="checkbox" name="is_approved" @if(isset($dataTypeContent->is_approved) && $dataTypeContent->is_approved){{ 'checked=checked' }}@endif>
                                                Одобрен
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ### SEO CONTENT ### -->

                            <!-- ### H1 ### -->
                            @include('admin.controls.text-area-tabs', ['title' => 'Тег H1', 'name' => 'h1', 'is_richbox' => false, 'panel_action' => true])

                            <!-- ### Meta title ### -->
                            @include('admin.controls.text-area-tabs', ['title' => 'Meta description', 'name' => 'meta_description', 'is_richbox' => false, 'panel_action' => true])

                        </div>
                    </div>
                </div>
                <div id="main_tab_calcs_fees" class="tab-pane fade">
                    <div class="row">
                        <div class="col-md-12">
                            @if(isset($dataTypeContent->id) && count($dataTypeContent->props) > 0)
                                @foreach($dataTypeContent->props as $prop)
                                    @php session(['props_cnt' => $prop->id]); @endphp
                                    @include('admin.credits.add.add_product_props', ['val' => $prop->id])
                                @endforeach
                            @endif
                            <button class="btn btn-danger btn-add-props">+ доп. свойства</button>
                        </div>
                    </div>
                </div>
                <div id="main_tab_other" class="tab-pane fade">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Другие условия (не участвуют в калькуляции)</h3>
                                    <div class="panel-actions">
                                        <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="credit_props">Минимальный доход</label>
                                                <input type="text" class="form-control" name="minimum_income" placeholder="в тенге" value="@if(isset($dataTypeContent->minimum_income)){{ $dataTypeContent->minimum_income }}@endif">
                                            </div>
                                            <div class="form-group">
                                                <label for="minimum_income_comment">Комментарий (Минимальный доход)</label>
                                                <textarea name="minimum_income_comment" class="form-control" id="minimum_income_comment">
                                                    @if(isset($dataTypeContent->minimum_income_comment)){{ $dataTypeContent->minimum_income_comment }}@endif
                                                </textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="credit_props">Общий стаж работы</label>
                                                <input type="text" class="form-control" name="occupational_life" placeholder="в месяцах" value="@if(isset($dataTypeContent->occupational_life)){{ $dataTypeContent->occupational_life }}@endif">
                                            </div>
                                            <div class="form-group">
                                                <label for="occupational_life_comment">Комментарий (Общий стаж работы)</label>
                                                <textarea name="occupational_life_comment" class="form-control" id="occupational_life_comment">
                                                    @if(isset($dataTypeContent->occupational_life_comment)){{ $dataTypeContent->occupational_life_comment }}@endif
                                                </textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="credit_props">Стаж на текущем месте работы</label>
                                                <input type="text" class="form-control" name="occupational_current" placeholder="в месяцах" value="@if(isset($dataTypeContent->occupational_current)){{ $dataTypeContent->occupational_current }}@endif">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="credit_props">ГЭСВ</label>
                                                <input type="text" class="form-control" name="gesv" placeholder="в %" value="@if(isset($dataTypeContent->gesv)){{ $dataTypeContent->gesv }}@endif">
                                            </div>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="gesv_comment">Комментарий (ГЭСВ)</label>
                                                <textarea name="gesv_comment" class="form-control" id="gesv_comment">
                                                    @if(isset($dataTypeContent->gesv_comment)){{ $dataTypeContent->gesv_comment }}@endif
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="credit_props">Страхование</label>
                                                <select name="insurance" class="form-control">
                                                    <option @if(isset($dataTypeContent->insurance) && $dataTypeContent->insurance == 'voidable') selected @endif value="one_time_percent">не обязательно</option>
                                                    <option @if(isset($dataTypeContent->insurance) && $dataTypeContent->insurance == 'one_time_percent') selected @endif value="one_time_percent">разово в % от кредита</option>
                                                    <option @if(isset($dataTypeContent->insurance) && $dataTypeContent->insurance == 'one_time_amount') selected @endif value="one_time_amount">разово в сумме</option>
                                                    <option @if(isset($dataTypeContent->insurance) && $dataTypeContent->insurance == 'not_less_then_amount') selected @endif value="not_less_then_amount">не менее</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="credit_props">Страхование (комментарий)</label>
                                                <input type="text" class="form-control" name="insurance_input" placeholder="" value="@if(isset($dataTypeContent->insurance_input)){{ $dataTypeContent->insurance_input }}@endif">

                                                {{--<textarea name="insurance_input" class="form-control" id="insurance_input" cols="30" rows="10">@if(isset($dataTypeContent->insurance_input)){{ $dataTypeContent->insurance_input }}@endif</textarea>--}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @include('admin.controls.text-area-tabs', ['title' => 'Способ погашения', 'name' => 'method_of_repayment', 'is_richbox' => true, 'panel_action' => true])
                            @include('admin.controls.text-area-tabs', ['title' => 'Документы', 'name' => 'docs', 'is_richbox' => true, 'panel_action' => true])
                            @include('admin.controls.text-area-tabs', ['title' => 'Прочие требования', 'name' => 'other_claims', 'is_richbox' => true, 'panel_action' => true])
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel panel-bordered panel-info">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><i class="icon wb-clipboard"></i> Логические параметры</h3>
                                    <div class="panel-actions">
                                        <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input value="1" type="checkbox" @if(isset($dataTypeContent->have_constant_income) && $dataTypeContent->have_constant_income){{ 'checked=checked' }}@endif class="checkbox" name="have_constant_income">
                                                Постоянный доход
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input value="1" type="checkbox" @if(isset($dataTypeContent->have_mobile_phone) && $dataTypeContent->have_mobile_phone){{ 'checked=checked' }}@endif class="checkbox" name="have_mobile_phone">
                                                Наличие мобильного телефона
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input value="1" type="checkbox" @if(isset($dataTypeContent->have_work_phone) && $dataTypeContent->have_work_phone){{ 'checked=checked' }}@endif class="checkbox" name="have_work_phone">
                                                Наличие рабочего телефона
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input value="1" type="checkbox" @if(isset($dataTypeContent->have_early_repayment) && $dataTypeContent->have_early_repayment){{ 'checked=checked' }}@endif class="checkbox" name="have_early_repayment">
                                                Досрочное погашение
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="have_early_repayment_comment">Комментарий (Досрочное погашение)</label>
                                        <textarea name="have_early_repayment_comment" class="form-control" id="have_early_repayment_comment">
                                            @if(isset($dataTypeContent->have_early_repayment_comment)){{ $dataTypeContent->have_early_repayment_comment }}@endif
                                        </textarea>
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input value="1" type="checkbox" @if(isset($dataTypeContent->have_prolongation) && $dataTypeContent->have_prolongation){{ 'checked=checked' }}@endif class="checkbox" name="have_prolongation">
                                                Пролонгация
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input value="1" type="checkbox" @if(isset($dataTypeContent->have_citizenship) && $dataTypeContent->have_citizenship){{ 'checked=checked' }}@endif class="checkbox" name="have_citizenship">
                                                Резидент (гражданство)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel panel-bordered panel-success">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><i class="icon wb-clipboard"></i> Селекты</h3>
                                    <div class="panel-actions">
                                        <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="credit_props">Категория заемщика</label>
                                        <select name="debtor_category" class="select2">
                                            <option value="none" @if(isset($dataTypeContent->debtor_category) && empty($dataTypeContent->debtor_category)){{ 'selected' }}@endif>не имеет значения</option>
                                            <option value="employee" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'employee'){{ 'selected' }}@endif>работник по найму</option>
                                            <option value="one_man_business" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'one_man_business'){{ 'selected' }}@endif>индивидуальные предприниматели</option>
                                            <option value="business_owners" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'business_owners'){{ 'selected' }}@endif>владельцы и совладельцы бизнеса</option>
                                            <option value="civil_service" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'civil_service'){{ 'selected' }}@endif>работники бюджетной сферы / госслужащие</option>
                                            <option value="farm_owners" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'farm_owners'){{ 'selected' }}@endif>владельцы личных подсобных хозяйств</option>
                                            <option value="military" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'military'){{ 'selected' }}@endif>военнослужащие</option>
                                            <option value="law_enforcements" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'law_enforcements'){{ 'selected' }}@endif>работники правоохранительных органов</option>
                                            <option value="lawyers" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'lawyers'){{ 'selected' }}@endif>адокаты / нотариусы</option>
                                            <option value="family" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'family'){{ 'selected' }}@endif>семья</option>
                                            <option value="new_family" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'new_family'){{ 'selected' }}@endif>молодая семья</option>
                                            <option value="pensioner" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'pensioner'){{ 'selected' }}@endif>пенсионеры</option>
                                            <option value="social_program" @if(isset($dataTypeContent->debtor_category) && $dataTypeContent->debtor_category == 'social_program'){{ 'selected' }}@endif>соц программа</option>
                                        </select>
                                    </div>

                                    {{--todo: привести все селекты к такому виду--}}
                                    @include('admin.controls.input-select', [
                                        'title' => 'Цель кредита',
                                        'name' => 'credit_goal',
                                        'is_select2' => true,
                                        'options' => [
                                            ['name' => 'любая','value' => 'any',],
                                            ['name' => 'неотложные нужды','value' => 'emergency_needs',],
                                            ['name' => 'просто деньги','value' => 'just_money',],
                                            ['name' => 'товары','value' => 'goods',],
                                            ['name' => 'бизнес','value' => 'business',],
                                            ['name' => 'рефинансирование','value' => 'refinancing',],
                                            ['name' => 'лечение','value' => 'medication',],
                                            ['name' => 'образование','value' => 'education',],
                                            ['name' => 'путешествие','value' => 'traveling',],
                                        ]
                                    ])


                                    <div class="form-group">
                                        <label for="credit_props">Способ получения</label>
                                        <select name="receive_mode[]" class="select2" multiple="multiple">
                                            <option value="">не имеет значения</option>
                                            @if(isset($dataTypeContent->receive_mode) && json_decode($dataTypeContent->receive_mode) != null)
                                                <option @if(in_array('cash',json_decode($dataTypeContent->receive_mode))){{ 'selected' }}@endif value="cash">наличными</option>
                                                <option @if(in_array('bank_card',json_decode($dataTypeContent->receive_mode))){{ 'selected' }}@endif value="bank_card">на банковскую карту</option>
                                                <option @if(in_array('bank_account',json_decode($dataTypeContent->receive_mode))){{ 'selected' }}@endif value="bank_account">на банковский счет</option>
                                                <option @if(in_array('none', json_decode($dataTypeContent->receive_mode))){{ 'selected' }}@endif value="none">не требуется</option>
                                            @else
                                                <option value="cash">наличными</option>
                                                <option value="bank_card">на банковскую карту</option>
                                                <option value="bank_account">на банковский счет</option>
                                                <option value="none">не требуется</option>
                                            @endif

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="credit_props">Регистрация</label>
                                        <select name="registration" class="select2">
                                            <option @if(isset($dataTypeContent->registration) && $dataTypeContent->registration == 'const'){{ 'selected' }}@endif value="const">постоянная</option>
                                            <option @if(isset($dataTypeContent->registration) && $dataTypeContent->registration == 'const_in_area'){{ 'selected' }}@endif value="const_in_area">постоянная в районе обращения</option>
                                            <option @if(isset($dataTypeContent->registration) && $dataTypeContent->registration == 'temp'){{ 'selected' }}@endif value="temp">временная</option>
                                            <option @if(isset($dataTypeContent->registration) && $dataTypeContent->registration == 'none'){{ 'selected' }}@endif value="none">не требуется</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="credit_props">Срок рассмотрения</label>
                                        <select name="time_for_consideration" class="select2">
                                            <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 1){{ 'selected="selected"' }}@endif value="1">в день обращения</option>
                                            <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 3){{ 'selected="selected"' }}@endif value="3">до 3 дней</option>
                                            <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 7){{ 'selected="selected"' }}@endif value="7">до 7 дней</option>
                                            <option @if(isset($dataTypeContent->time_for_consideration) && $dataTypeContent->time_for_consideration == 14){{ 'selected="selected"' }}@endif value="14">до 14 дней</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="time_for_consideration_comment">Комментарий (срок рассмотрения)</label>
                                        <textarea name="time_for_consideration_comment" class="form-control" id="time_for_consideration_comment">
                                            @if(isset($dataTypeContent->time_for_consideration_comment)){{ $dataTypeContent->time_for_consideration_comment }}@endif
                                        </textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="credit_props">Кредитная история</label>
                                        <select name="credit_history" class="select2">
                                            <option @if(isset($dataTypeContent->credit_history) && $dataTypeContent->credit_history == 'positive'){{ 'selected="selected"' }}@endif value="positive">положительная кредитная история</option>
                                            <option @if(isset($dataTypeContent->credit_history) && $dataTypeContent->credit_history == 'negative'){{ 'selected="selected"' }}@endif value="negative">отрицательная кредитная история</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="credit_props">Оформление кредита</label>
                                        <select name="credit_formalization" class="select2">
                                            <option @if(isset($dataTypeContent->credit_formalization) && $dataTypeContent->credit_formalization == 'online'){{ 'selected="selected"' }}@endif value="online">онлайн заявка</option>
                                            <option @if(isset($dataTypeContent->credit_formalization) && $dataTypeContent->credit_formalization == 'office'){{ 'selected="selected"' }}@endif value="office">в отделении банка</option>
                                            <option @if(isset($dataTypeContent->credit_formalization) && $dataTypeContent->credit_formalization == 'both'){{ 'selected="selected"' }}@endif value="both">в отделений банка и онлайн заявка</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="main_tab_custom_options" class="tab-pane fade">
                    <div class="row">
                        <div class="col-md-12">
                            @if(isset($dataTypeContent->custom_props) && count($dataTypeContent->custom_props) > 0)
                                @foreach($dataTypeContent->custom_props as $custom_prop)
                                    {{--@php session(['custom_props_cnt' => $custom_prop->id]); @endphp--}}
                                    @include('admin.credits.add.add_product_custom_options', ['val' => $custom_prop->id])
                                @endforeach
                            @endif
                            <button class="btn btn-danger btn-add-props-custom">+ кастомное свойство</button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary pull-right">
                @if(isset($dataTypeContent->id)){{ __('voyager.post.update') }}@else <i class="icon wb-plus-circle"></i> {{ __('voyager.post.new') }} @endif
            </button>
        </form>

        <iframe id="form_target" name="form_target" style="display:none"></iframe>

    </div>
@stop

@section('javascript')
    <script>
        $('document').ready(function () {

            $('[data-translit-source]').bind('change click keyup', function (e) {
                var el = $(e.target),
                    id = el.data('translitTarget');
                $.post(
                    '/admin/ajax/translit',
                    {
                        'text': el.val()
                    },
                    function (data) {
                        $('#' + id).val(data);
                    }
                );
            });

            $('.btn-delete_prop').on('click', function (e) {
                e.preventDefault();

                var panel = $(this).parents('.panel');
                if (!confirm('Удалить блок?')) return;
                delete_prop_button($(this).data('id'), panel);
            });

            $('.btn-add-props-custom').on('click', function (e) {
                e.preventDefault();

                add_prop_custom_button(this);
            });

            $('.btn-del-props-custom').on('click', function (e) {
                e.preventDefault();

                var panel = $(this).parents('.panel');
                if (!confirm('Удалить блок?')) return;
                delete_custom_prop_button($(this).data('id'), panel);
            });

            function add_prop_custom_button(btn) {
                var data = {
                    url: '/admin/ajax/get-custom-prop-block'
                };

                $.ajax({
                    url: data.url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    method: 'post',
                    success:function (response) {
                        $(btn).before(response);
                    },
                    error:function (error) {
                        console.dir(error);
                    }
                });
            }

            function delete_custom_prop_button(id, block) {
                var data = {
                    url: '/admin/ajax/del-custom-prop-block',
                    id: id,
                    product: 'credit',
                };

                $.ajax({
                    url: data.url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    method: 'post',

                    error:function (error) {
                        console.dir(error);
                    }
                });
                block.remove();
            }

            function delete_prop_button(id, block) {
                var data = {
                    url: '/admin/ajax/del-prop-block',
                    id: id,
                    product: 'credit',
                };

                $.ajax({
                    url: data.url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    method: 'post',

                    error:function (error) {
                        console.dir(error);
                    }
                });
                block.remove();
            }

            function add_fee_button(btn) {
                var data = {
                    url: '/admin/ajax/get-fee-block'
                };

                $.ajax({
                    url: data.url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    method: 'post',
                    success:function (response) {
                        $(btn).before(response);
                    },
                    error:function (error) {
                        console.dir(error);
                    }
                });
            }

            $('.btn-add-fees').on('click', function (e) {
                e.preventDefault();

                add_fee_button(this);
            });


            $('.btn-add-props').on('click', function (e) {
                e.preventDefault();
                add_prop_button(this);
            });

            function add_prop_button(btn) {
                var data = {
                    url: '/admin/ajax/get-prop-block'
                };

                $.ajax({
                    url: data.url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    method: 'post',
                    success:function (response) {
                        $(btn).before(response);
                    },
                    error:function (error) {
                        console.dir(error);
                    }
                });
            }

            function delete_fee_button(id, block) {
                var data = {
                    url: '/admin/ajax/del-fee-block',
                    id: id,
                    product: 'credit',
                };

                $.ajax({
                    url: data.url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    method: 'post',

                    error:function (error) {
                        console.dir(error);
                    }
                });
                block.remove();
            }

            $('.btn-delete-fee').on('click', function (e) {
                e.preventDefault();

                var panel = $(this).parents('.panel-fee');
                if (!confirm('Удалить блок?')) return;
                delete_fee_button($(this).data('id'), panel);
            })

        });
    </script>
@stop
