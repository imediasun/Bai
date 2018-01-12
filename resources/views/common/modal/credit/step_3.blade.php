      <div class="credit_form3">
        <div class="t">
          <div class="h1">Заполните форму для получения кредита</div>
          <div class="steps">
            <div class="step active">
              <div class="num">1</div>
              <div>Персональные<br>
              данные</div></div>
            <!-- end .step-->
            <div class="arr active"></div>
            <div class="step active">
              <div class="num">2</div>
              <div>Дополнительная<br>
              информация</div></div>
            <!-- end .step-->
            <div class="arr active"></div>
            <div class="step active">
              <div class="num">3</div>
              <div>Звонок<br>
              оператора</div></div>
            <!-- end .step--> 
          </div>
          <!-- end .steps--> 
        </div>
        <!-- end .t-->
        <div class="c c_done"> <img src="/img/check_done.png" width="256" height="256" alt=""/>
          <div class="big">Заявка отправлена в банк</div>
          Сотрудники банка обзванивают клиентов с&nbsp;09:00 до&nbsp;21:00.<br>
          Обычно, это происходит в&nbsp;течение 30&nbsp;минут. </div>
        <!-- end .c-->
        <div class="b b_done"> <span class="btn btn_sq btn_sq_small btn_green"><span>Закрыть</span></span> </div>
        <!-- end .b--> 
      </div>
      <!-- end .credit_form-->
      <div class="close"><span class="ic ic_cross_big white"></span></div>
      <script>
          $(function () {
              $('.b_done').click(function () {
                  $('.modal').hide(1000, function () {
                      $('.modal').remove();
                      $('.modal_container').html('')
                      storageData = {};
                  });
              });

              //close modal
              $('.close').click(function(e){
                  e.preventDefault();
                  $('.modal').hide(1000, function () {
                      $('.modal_container').html('');
                      storageData = {};
                  });
              });
          })
      </script>