<div class="modal" style="display: none;">
    <div class="fader"></div>
    <div class="modal_in">
        <div class="credit_form">
            <form method="post" id="step1_form">
                <div class="t">
                    <div class="h1">Заполните форму для получения кредита</div>
                    <div class="steps">
                        <div class="step active">
                            <div class="num">1</div>
                            <div>Персональные<br>данные
                            </div>
                        </div>
                        <!-- end .step-->
                        <div class="arr"></div>
                        <div class="step">
                            <div class="num">2</div>
                            <div>Дополнительная<br>информация
                            </div>
                        </div>
                        <!-- end .step-->
                        <div class="arr"></div>
                        <div class="step">
                            <div class="num">3</div>
                            <div>Звонок<br>оператора</div>
                        </div>
                        <!-- end .step-->
                    </div>
                    <!-- end .steps-->
                </div>
                <!-- end .t-->
                <div class="c">
                    <div class="l">
                        <div class="label">Ваше ФИО <span class="red">*</span></div>
                        <p>
                            <div class="input input_big">
                                <input type="text" name="firstname" id="firstname" placeholder="Имя" value="">
                            </div>
                        </p>
                        <p>
                            <div class="input input_big">
                                <input type="text" name="lastname" id="lastname" placeholder="Фамилия" value="">
                            </div>
                        </p>
                        <p>
                            <div class="input input_big">
                                <input type="text" name="middlename" id="middlename" placeholder="Отчество" value="">
                            </div>
                        </p>

                        <!-- end .input-->
                        <div class="label">Город <span class="red">*</span></div>
                        <div class="input_with_info">
                            <div class="input input_big">
                                <select name="city" id="city">
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name_ru }}</option>
                                    @endforeach
                                </select>
                                {{--<span class="select2 select2-container select2-container--default" dir="ltr" style="width: 375px;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-t5la-container"><span class="select2-selection__rendered" id="select2-t5la-container" title="Северо-Западный автономный округ">Северо-Западный автономный округ</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>--}}
                            </div>
                        </div>
                        <!-- end .input-->
                        <div class="label">Телефон <span class="red">*</span></div>
                        <div class="input_with_info">
                            <div class="input input_big">
                                <input type="text" name="phone" id="phone" placeholder="+7 777 777 77 77">
                            </div>
                            <!-- end .input-->
                            <div class="text">Мобильный или домашний с кодом города</div>
                            <!-- end .text-->
                        </div>
                        <!-- end .input_with_info-->
                        <div class="label">Электронная почта <span class="red">*</span></div>
                        <div class="input_with_info">
                            <div class="input input_big">
                                <input type="text" name="email" id="email" placeholder="ivanov@mail.ru">
                            </div>
                            <!-- end .input-->
                            <div class="text">На нее придет ответ от&nbsp;банка</div>
                            <!-- end .text-->
                        </div>
                        <!-- end .input_with_info-->
                        <div class="label">ИНН <span class="red">*</span></div>
                        <div class="input_with_info">
                            <div class="input input_big">
                                <input type="text" name="iin" id="iin" maxlength="12" placeholder="839 578 929 185">
                            </div>
                            <!-- end .input-->
                            <div class="text">12 знаков</div>
                            <!-- end .text-->
                        </div>
                        <!-- end .input_with_info-->
                    </div>
                    <!-- end .l-->
                    <div class="r">
                        <div class="logo_hold"><a href="#"><img id="step1_bank_img" src="{{ $credit['bank_logo'] }}" width="238" height="398" alt=""></a></div>
                        <!-- end .logo_hold-->
                        <ul>
                            <li>
                                <div class="grey">Сумма кредита</div>
                                <span id="modal_amount">{{ $credit['amount'] }}{{ $credit['currency_symbol'] }}</span>
                            </li>
                            <li>
                                <div class="grey">Срок кредита</div>
                                <span id="modal_term">{{ $credit['term_human'] }}</span>
                            </li>
                            <li>
                                <div class="grey">Переплата</div>
                                <span id="modal_overpay">{{ $credit['overpay'] }}{{ $credit['currency_symbol'] }}</span>
                            </li>
                            <li>
                                <div class="grey">Срок рассмотрения</div>
                                <span id="modal_time">{{ $credit['time'] }}</span>
                            </li>
                            <li class="full">
                                <div class="grey">Подтверждение дохода</div>
                                Не требуется
                            </li>
                        </ul>
                    </div>
                    <!-- end .r-->
                </div>
                <!-- end .c-->
                <div class="b">
                    <button id="form1_submit" class="btn btn_round btn_orange" type="submit"><span>Следующий шаг</span></button>
                </div>
                <!-- end .b-->
            </form>
        </div>
        <!-- end .credit_form-->
        <div class="close modal-close"><span class="ic ic_cross_big white"></span></div>
    </div>
    <!-- end .modal_in-->
</div>
<script>
    $('#form1_submit').click(function (e) {
        e.preventDefault();
        storageData.firstname = $('#firstname').val();
        storageData.lastname = $('#lastname').val();
        storageData.middlename = $('#middlename').val();
        storageData.phone = $('#phone').val();
        storageData.city = $('#city').val();
        storageData.email = $('#email').val();
        storageData.iin = $('#iin').val();

        load_credit_step2($('.credit_form'));
    });

    //close modal
    $('.close').click(function(e){
        e.preventDefault();
        $('.modal').hide(1000, function () {
            $('.modal_container').html('');
        });
    });
</script>