      <div class="credit_form2">
        <form>
          <div class="t">
            <div class="h1">Заполните форму для получения кредита</div>
            <div class="steps">
              <div class="step active">
                <div class="num">1</div>
                <div>Персональные<br>данные</div>
              </div>
              <!-- end .step-->
              <div class="arr active"></div>
              <div class="step active">
                <div class="num">2</div>
                <div>Дополнительная<br>информация</div>
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
              <div class="label">У вас есть действующий кредит?</div>
              <div class="main_checks main_checks_big">
                <label>
                  <input type="radio" checked value="1" name="iscredit" class="outtaHere iscredit">
                  <span>Да, есть</span></label>
                <label>
                  <input type="radio" value="0" name="iscredit" class="outtaHere iscredit">
                  <span>Нет</span></label>
              </div>
              <!-- end .main_checks-->
              
              <div class="label">У вас есть пенсионные отчисления?</div>
              <div class="main_checks main_checks_big">
                <label>
                  <input type="radio" value="1" checked name="pens" class="outtaHere pens">
                  <span>Да, есть</span></label>
                <label>
                  <input type="radio" value="0" name="pens" class="outtaHere pens">
                  <span>Нет</span></label>
              </div>
              <!-- end .main_checks-->
              
              <div class="label">Размер вашей заработной платы</div>
              <div class="input_num">
                <div class="input input_big">
                  <input type="text" name="salary" id="salary" placeholder="250 000">
                </div>
                <!-- end .input-->
                <div class="text">тенге</div>
                <!-- end .text--> 
              </div>
              <!-- end .input_with_info-->
              <div class="label">На карту какого банка вы получаете зарплату?</div>
              <div class="input input_big">
                <select name="banks" id="banks">
                    {% for bank in banks %}
                      <option value="{{ bank.altName }}">{{ bank.name }}</option>
                    {% endfor %}
                </select>
              </div>
              <!-- end .input-->
              <div class="check">
                <label>
                  <input type="checkbox" name="i_agree_checkbox" class="outtaHere">
                  <span class="checkbox"><span class="ic ic_check dark"></span></span><span>Я ознакомился и согласен<br>с <a href="#">условиями передачи информации</a></span></label>
              </div>
            </div>
            <!-- end .l-->
            <div class="r">
              <div class="logo_hold"><a href="#"><img src="{{ credit.bank_logo }}" width="238" height="398" alt=""/></a></div>
              <!-- end .logo_hold-->
              <ul>
                <li>
                  <div class="grey">Сумма кредита</div>
                  {{ credit.amount }}{{ credit.currency_symbol }}
                </li>
                <li>
                  <div class="grey">Срок кредита</div>
                  {{ credit.term }}
                </li>
                <li>
                  <div class="grey">Переплата</div>
                  {{ credit.overpay }}{{ credit.currency_symbol }}
                </li>
                <li>
                  <div class="grey">Срок рассмотрения</div>
                  {{ credit.time }}
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
            <button class="btn btn_round btn_orange" id="form2_submit" type="submit"><span>Следующий шаг</span></button>
          </div>
          <!-- end .b-->
        </form>
      </div>
      <!-- end .credit_form-->
      <div class="close"><span class="ic ic_cross_big white"></span></div>

<script>
  $(function () {
      $('#form2_submit').click(function (e) {
          e.preventDefault();
          storageData.credit_exists = $('.iscredit:checked').val();
          storageData.pension_exists = $('.pens:checked').val();
          storageData.salary = $('#salary').val();
          storageData.bank = $('#banks > option:selected').val();
          storageData.bank_human = $('#banks > option:selected').text();
          console.dir(storageData);
          load_credit_step3($('.credit_form2'));
//          credit_sendmail_bank();
      });

      //close modal
      $('.close').click(function(e){
          e.preventDefault();
          $('.modal').hide(1000, function () {
              $('.modal_container').html('');
          });
      });
  });
</script>
