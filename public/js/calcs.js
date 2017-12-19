// $(function () {

    var credit_listing_data = {};
    function init_credit_listing() {
        var credit_rates = $('.credit_rate');
        var payment_per_month = $('.payment_per_month');
        var overpay = $('.overpay');
        $.each(credit_rates, function (index, value) {
            credit_listing_data[index] = {
                rate:parseFloat($(value).text()),
                payment_per_month:parseFloat($(payment_per_month).eq(index).text()),
                overpay:parseInt($(overpay).eq(index).text()),
            };
        });
        // console.dir(credit_children_data);
    }

    init_credit_listing();

    function filterByParam(run_func, params) {
        run_func.apply(null, params);
    }

    function runAjax(url, params, successFunc) {
        $.ajax({
            url: Routing.generate(url),
            async: true,
            cache: false,
            type: "POST",
            dataType: "json",
            data: params,
            success: function (response) {
                successFunc.apply(null, [response]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.dir(jqXHR);
            }
        });
    }

    function resultAjax(responce) {
        if(responce.amount.length != 0){
            $('input[name=percent]').val(responce.amount[0].po_name);
        }
    }

    function resultFilter(responce) {
        $('#offers_list').html(responce);
    }

    $('input.autonum').on('change', function () {

    });

    function getPercentFromNumber(number, numberFrom) {
        return number*100/numberFrom;
    }

    $('input[name=refill]').on('change keyup', function () {
        var dep_refill = parseInt($(this).val());
        $('span[itemprop=refill_rate]').text(dep_refill);
    });
    
    function getFormattedDatePeriod(endPeriod) {
        var d = new Date();
        var result = [];
        for(var i = 0; i < endPeriod; i++){
            var delimiter = '.';
            var zero_based_date = d.getDate();
            var zero_based_month = d.getMonth() + 1;
            if (zero_based_date < 10){
                zero_based_date = "0" + zero_based_date;
            }
            if (zero_based_month < 10){
                zero_based_month = "0" + zero_based_month;
            }
            var formatted = [zero_based_date, zero_based_month, d.getFullYear()].join(delimiter);
            result[i] = formatted;
            d.setMonth(d.getMonth() + 1);
        }
        return result;
    }

    function calculate_monthly_percent(amount, percent) {
        return Math.round(amount * percent / 100 / 12);
    }

    function create_credit_scheme(periods, amount, percent, type, initial_fee) {
        var origin_amount = amount;
        var result = [{
            'index': 1,
            'date_time': new Date(),
            'amount':amount,
            'amount_remain' : 0,
            'percentages' : 0,
            'estimated_payment' : 0,
        }];
        var credit_result = calculate_credit(periods, amount, percent, type, initial_fee);
        var percents = 0;
        for (var i = 0; i < periods; i++) {
            var percents_new = calculate_monthly_percent(amount, percent);
            percents = percents + percents_new;
            if (i == 0) {
                result.push = {
                        'index': 1 + i,
                    'date_time': credit_result['dates'][i],
                    'amount': amount,
                    'amount_remain': calculate_monthly_percent(amount, percent),
                    'percentages': credit_result['ppm'][i] - calculate_monthly_percent(amount, percent),
                    'estimated_payment': credit_result['ppm'][i],
                };
                amount = amount - (credit_result['ppm'][i] - calculate_monthly_percent(amount, percent));
            } else {
                result.push = {
                    'index': 1 + i,
                    'date_time': credit_result['dates'][i],
                    'amount': amount,
                    'amount_remain': calculate_monthly_percent(amount, percent),
                    'percentages': credit_result['ppm'][i] - calculate_monthly_percent(amount, percent),
                    'estimated_payment': credit_result['ppm'][i],
                };
                amount = amount - (credit_result['ppm'][i] - calculate_monthly_percent(amount, percent));
            }
    
        }
        result[0]['origin_amount'] = origin_amount;
        result[0]['percents'] = percents;
        result[0]['overpay'] = percents + origin_amount;
    
        return result;
    }

    function calculate_credit(periods, amount, percent, type, initial_fee){
        if(initial_fee != 0){
            amount = amount - initial_fee;
        }
        var percentPerMonth = percent;
        var dates = getFormattedDatePeriod(periods);

        if (percent == 0 || percent == null){
            return {
                'ppm': amount/periods,
                'procentAmount':0,
                'month':periods,
                'dates':dates
            };
        }

        //для аннуитетного
        if (type == 1) {

            var ppm = amount * percentPerMonth / (1 - 1 / Math.pow((1 + percentPerMonth), periods));
            var ppm_arr = [];
            for (var i = 1; i <= periods; i++) {
                ppm_arr[i] = Math.round(ppm);
            }
            var procentAmount = ppm * periods - amount;
            return {
                'ppm':ppm_arr,
                'procentAmount':Math.round(procentAmount),
                'month':periods,
                'dates':dates
            };
        }

        //для дифференцированного
        else {

            var procentAmount = Math.round(amount * percentPerMonth * (periods + 1) / 2);
            var ppm = [];
            for (var i = 1; i <= periods; i++) {
                ppm[i] = Math.round(amount / periods + amount * (periods - i + 1) * percentPerMonth / periods);
            }
            return {
                'ppm':ppm,
                'procentAmount':procentAmount,
                'month':periods,
                'dates':dates
            };
        }
    }

    function calculate_monthly_percent_dep(amount, percent, renewal) {
        var percent = Math.round(amount * percent / 100 / 12);
        var result = {
            'percent' : percent,
            'withPercent': percent + amount + renewal,
            'amount':  percent + amount - percent,
            'capitalize': percent,
            'renewal':renewal
        };
        return result;
    }
    
    function depositInterestCalc(options) {
        var percentPerMonth = options.percent / 12 / 100;
        var procentAmount = 0;
        var ppm = options.amount * percentPerMonth;
        var rows = [];
        var total_refill = 0;
        var total_percent = 0;
        for (var i = 0; i < options.period; i++) {
            //если без капитализации,
            if (!options.capitalization){
                procentAmount += options.amount * percentPerMonth;
                if(i%options.refill_rate == 0 && i+1 != options.period) {
                    options.amount += options.refill;
                }
                //если капитализация присутствует
            }else{
                if((i+1)%options.refill_rate == 0){
                    rows[i] = calculate_monthly_percent_dep(options.amount, options.percent, options.refill);
                }
                else{
                    rows[i] = calculate_monthly_percent_dep(options.amount, options.percent, 0);
                }
                options.amount = rows[i].withPercent;
            }
            total_refill += rows[i].renewal;
            total_percent += rows[i].percent;

        }
        return {
            'rows':rows,
            'procentAmount': Math.round(procentAmount),
            'month':options.period,
            'amount':options.amount,
            'total_amount': options.amount,
            'total_refill': total_refill,
            'total_percent': total_percent,
        };
    }

    function change_data(amount) {
        var product_type = $('#source_page').attr('product_type');
        var product_id = $('#source_page').attr('product_id');
        var route = '';
        var term_val = $('select#credit_period').val();
        var city = $('#city').val();
        // var amount =
        var storageData = {
            'amount': amount,
            'term_val': term_val,
            'product_type':product_type,
        };

        switch (product_type){
            case 'deposit': route = 'credits_ajax_amount';

                var deposit_period = $('#deposit_period').val();
                var dep_rate = parseFloat($('span[itemprop=annualPercentageRate]').text());
                var amount_span = $('span[itemprop=amount]').text();
                var percent = dep_rate/100/deposit_period;
                var dep_refill = parseInt($('#refill').val());
                var options = {
                    'amount': amount,
                    'refill_rate': 1,
                    'percent': dep_rate,
                    'capitalization': true,
                    'period': deposit_period,
                    'refill': dep_refill,
                };
                var interestRate = depositInterestCalc(options);
                $('#deposit_scheme').html('');
                var d = new Date();

                for(var i in interestRate.rows){
                    var delimiter = '.';
                    var zero_based_date = d.getDate();
                    var zero_based_month = d.getMonth() + 1;
                    if (zero_based_date < 10){
                        zero_based_date = "0" + zero_based_date;
                    }
                    if (zero_based_month < 10){
                        zero_based_month = "0" + zero_based_month;
                    }
                    var formatted = [zero_based_date, zero_based_month, d.getFullYear()].join(delimiter);
                    var row_begin = '<tr>';
                    var row_end = '<tr/>';
                    var column = row_begin + '<td>' + formatted + '<td/>';
                    column += '<td>' + interestRate.rows[i].percent + ' ₸<td/>';
                    column += '<td>' + interestRate.rows[i].renewal + ' ₸<td/>';
                    column += '<td>' + interestRate.rows[i].withPercent + ' ₸<td/> + row_end';
                    $('#deposit_scheme').append(column);
                    d.setMonth(d.getMonth() + 1);
                }

                $('span[itemprop=amount]').text(amount);
                $('span[itemprop=refill_rate]').text(dep_refill);
                $('span[itemprop=interestRate]').text(interestRate.amount);
                $('#percent_total_result').text(interestRate.total_percent + ' ₸');
                $('#refill_total_result').text(interestRate.total_refill + ' ₸');
                $('#amount_total_result').text(interestRate.total_amount + ' ₸');

                break;

            case 'deposits': route = 'deposits_ajax_amount';
                var deposit_period = $('#credit_period').val();
                var capitalization = $('.capitalization');
                var dep_rate = $('.dep_rate');
                var credits = $('.credit').find('.dep_rate');
                // console.dir(deposit_period);

                $.each(credits, function (index, value) {
                    var percent = parseFloat($(value).text())/100;
                    var cap_new = amount*percent/12*deposit_period;
                    var cap_text = "<strong>+" + cap_new.toFixed(2) + "</strong> капитализация";
                    $(value).parent().find('.capitalization').html(cap_text);
                });
                break;

            case 'credit': route = '';
                var credit_period = $('#credit_period').val();
                var percent = parseFloat($('span[itemprop=annualPercentageRate]').text())/100/credit_period;
                var percent_raw = parseFloat($('span[itemprop=annualPercentageRate]').text());
                var payment_per_month = (amount*percent/(1-1/Math.pow((1+percent), credit_period)));
                var need_return = payment_per_month*credit_period;
                var overpay = need_return - amount;
                var amount_percent = getPercentFromNumber(overpay, amount);
                var overpay_percent = 100 - amount_percent;

                $('#payment_per_month').text(payment_per_month.toFixed(2));
                $('#need_return').text(need_return.toFixed(2));
                $('#overpay').text(overpay.toFixed(2));
                $('#mount_count').text(credit_period);
                $('#amount_percent').css('width', overpay_percent + '%');
                $('#overpay_percent').css({'width': amount_percent + '%', 'left': overpay_percent + '%'});

                var credit_table_data = calculate_credit(credit_period, amount, percent, 1, 0);
                credit_period++;
                var dates = getFormattedDatePeriod(credit_period);

                $('#credit_scheme').html('');
                var remain = null;
                var amount_ = amount;
                var arr_amount = [];

                for(var i = 1; i < dates.length; i++){

                    var row_begin = '<tr>';
                    var row_end = '<tr/>';
                    remain = amount - calculate_monthly_percent(amount_, percent_raw);
                    var percentages = calculate_monthly_percent(amount_, percent_raw);

                    var estimated_payment = credit_table_data['ppm'][i];
                    amount_ = amount_ - (credit_table_data['ppm'][i] - calculate_monthly_percent(amount_, percent_raw));

                    if(i == 1)
                        arr_amount[i] = amount;
                    else
                        arr_amount[i] = amount_;

                    var column = row_begin + '<td>' + dates[i] + '<td/>';       //дата расчета
                    column += '<td>' + arr_amount[i] + ' ₸<td/>';                      //Остаток задолженности
                    column += '<td>' + percentages + ' ₸<td/>';                 //Начисленные проценты
                    column += '<td>' + amount_ + ' ₸<td/>';                     //Основной долг
                    column += '<td>' + estimated_payment + ' ₸<td/> + row_end'; //Сумма на конец периода
                    $('#credit_scheme').append(column);
                }
                $('span[itemprop=amount]').text(amount);

                break;

            case 'credits': route = 'credits_ajax_amount';
                var credit_period = $('#credit_period').val();
                var payment_per_month = $('.payment_per_month');
                var overpay = $('.overpay');

                $.each(credit_listing_data, function (index, value) {
                    var percent = credit_listing_data[index].rate/100/credit_period;
                    var new_payment_per_month = amount*percent/(1-1/Math.pow((1+percent), credit_period));
                    var new_payment_per_month_text = "<strong>" + new_payment_per_month.toFixed(2) + "</strong> тенге в месяц";
                    var new_overpay = new_payment_per_month*credit_period - amount;
                    var new_overpay_text = "<strong>-" + new_overpay.toFixed(2) + "</strong> переплата";

                    $(payment_per_month).eq(index).html(new_payment_per_month_text);
                    $(overpay).eq(index).html(new_overpay_text);

                });

                break;

            case 'mortgage': route = 'credits_ajax_amount';
                var credit_period = $('#credit_period').val();
                var percent = parseFloat($('span[itemprop=annualPercentageRate]').text())/100/credit_period;
                var percent_raw = parseFloat($('span[itemprop=annualPercentageRate]').text());
                var payment_per_month = (amount*percent/(1-1/Math.pow((1+percent), credit_period)));
                var need_return = payment_per_month*credit_period;
                var overpay = need_return - amount;
                var amount_percent = getPercentFromNumber(overpay, amount);
                var overpay_percent = 100 - amount_percent;

                if ($('#fee_amount').is(':checked')) {
                    var fee = $('#fee').val().split(" ").join('');
                }else{
                    var fee = amount * $('#fee').val().split(" ").join('') / 100;
                }

                $('#payment_per_month').text(payment_per_month.toFixed(2));
                $('#need_return').text(need_return.toFixed(2));
                $('#overpay').text(overpay.toFixed(2));
                $('#mount_count').text(credit_period);
                $('#amount_percent').css('width', overpay_percent + '%');
                $('#overpay_percent').css({'width': amount_percent + '%', 'left': overpay_percent + '%'});

                var credit_table_data = calculate_credit(credit_period, amount, percent, 1, 0);
                credit_period++;
                var dates = getFormattedDatePeriod(credit_period);

                $('#credit_scheme').html('');

                var remain = null;
                var amount_ = amount;
                var arr_amount = [];

                for(var i = 1; i < dates.length; i++){

                    var row_begin = '<tr>';
                    var row_end = '<tr/>';
                    remain = amount - calculate_monthly_percent(amount_, percent_raw);
                    var percentages = calculate_monthly_percent(amount_, percent_raw);
                    var estimated_payment = credit_table_data['ppm'][i];
                    amount_ = amount_ - (credit_table_data['ppm'][i] - calculate_monthly_percent(amount_, percent_raw));

                    if(i == 1)
                        arr_amount[i] = amount;
                    else
                        arr_amount[i] = amount_;

                    var column = row_begin + '<td>' + dates[i] + '<td/>';       //дата расчета
                    column += '<td>' + arr_amount[i] + ' ₸<td/>';                      //Остаток задолженности
                    column += '<td>' + percentages + ' ₸<td/>';                 //Начисленные проценты
                    column += '<td>' + amount_ + ' ₸<td/>';                     //Основной долг
                    column += '<td>' + estimated_payment + ' ₸<td/> + row_end'; //Сумма на конец периода

                    $('#credit_scheme').append(column);
                }

                break;

            case 'mortgages': route = 'mortgages_ajax_amount';
                var credit_period = $('#credit_period').val();
                var payment_per_month = $('.payment_per_month');
                var overpay = $('.overpay');

                if ($('#fee_amount').is(':checked')) {
                    var fee = $('#fee').val().split(" ").join('');
                }else{
                    var fee = amount * $('#fee').val().split(" ").join('') / 100;
                }

                $.each(credit_listing_data, function (index, value) {
                    amount = amount - fee;
                    var percent = credit_listing_data[index].rate/100/credit_period;
                    var new_payment_per_month = amount*percent/(1-1/Math.pow((1+percent), credit_period));
                    var new_payment_per_month_text = "<strong>" + new_payment_per_month.toFixed(2) + "</strong> тенге в месяц";
                    var new_overpay = new_payment_per_month*credit_period - amount;
                    var new_overpay_text = "<strong>-" + new_overpay.toFixed(2) + "</strong> переплата";

                    $(payment_per_month).eq(index).html(new_payment_per_month_text);
                    $(overpay).eq(index).html(new_overpay_text);
                });

                break;

            case 'autocredit': route = 'credits_ajax_amount';
                var credit_period = $('#credit_period').val();
                var percent = parseFloat($('span[itemprop=annualPercentageRate]').text())/100/credit_period;
                var percent_raw = parseFloat($('span[itemprop=annualPercentageRate]').text());
                var payment_per_month = (amount*percent/(1-1/Math.pow((1+percent), credit_period)));
                var need_return = payment_per_month*credit_period;
                var overpay = need_return - amount;
                var amount_percent = getPercentFromNumber(overpay, amount);
                var overpay_percent = 100 - amount_percent;

                if ($('#fee_amount').is(':checked')) {
                    var fee = $('#fee').val().split(" ").join('');
                }else{
                    var fee = amount * $('#fee').val().split(" ").join('') / 100;
                }

                $('#payment_per_month').text(payment_per_month.toFixed(2));
                $('#need_return').text(need_return.toFixed(2));
                $('#overpay').text(overpay.toFixed(2));
                $('#mount_count').text(credit_period);
                $('#amount_percent').css('width', overpay_percent + '%');
                $('#overpay_percent').css({'width': amount_percent + '%', 'left': overpay_percent + '%'});

                // var calculated = calculate_credit(credit_period, amount, 18, 1, fee);
                var credit_table_data = calculate_credit(credit_period, amount, percent, 1, 0);
                credit_period++;
                var dates = getFormattedDatePeriod(credit_period);

                $('#credit_scheme').html('');

                var remain = null;
                var amount_ = amount;
                var arr_amount = [];

                for(var i = 1; i < dates.length; i++){

                    var row_begin = '<tr>';
                    var row_end = '<tr/>';
                    remain = amount - calculate_monthly_percent(amount_, percent_raw);
                    var percentages = calculate_monthly_percent(amount_, percent_raw);
                    var estimated_payment = credit_table_data['ppm'][i];
                    amount_ = amount_ - (credit_table_data['ppm'][i] - calculate_monthly_percent(amount_, percent_raw));

                    if(i == 1)
                        arr_amount[i] = amount;
                    else
                        arr_amount[i] = amount_;

                    var column = row_begin + '<td>' + dates[i] + '<td/>';       //дата расчета
                    column += '<td>' + arr_amount[i] + ' ₸<td/>';                      //Остаток задолженности
                    column += '<td>' + percentages + ' ₸<td/>';                 //Начисленные проценты
                    column += '<td>' + amount_ + ' ₸<td/>';                     //Основной долг
                    column += '<td>' + estimated_payment + ' ₸<td/> + row_end'; //Сумма на конец периода
                    $('#credit_scheme').append(column);
                }

                break;

            case 'autocredits': route = 'autocredits_ajax_amount';
                var credit_period = $('#credit_period').val();
                var payment_per_month = $('.payment_per_month');
                var overpay = $('.overpay');
                var fee = $('#fee').val().split(" ").join('');

                $.each(credit_listing_data, function (index, value) {
                    amount = amount - fee;
                    var percent = credit_listing_data[index].rate/100/credit_period;
                    var new_payment_per_month = amount*percent/(1-1/Math.pow((1+percent), credit_period));
                    var new_payment_per_month_text = "<strong>" + new_payment_per_month.toFixed(2) + "</strong> тенге в месяц";
                    var new_overpay = new_payment_per_month*credit_period - amount;
                    var new_overpay_text = "<strong>-" + new_overpay.toFixed(2) + "</strong> переплата";

                    $(payment_per_month).eq(index).html(new_payment_per_month_text);
                    $(overpay).eq(index).html(new_overpay_text);
                });
                break;

            case 'loan': route = 'credits_ajax_amount';
                var credit_period = $('#credit_period').val();
                var payment_per_month = $('.payment_per_month');
                var overpay = $('.overpay');

                $.each(credit_listing_data, function (index, value) {
                    var percent = credit_listing_data[index].rate/100/credit_period;
                    var new_payment_per_month = amount*percent/(1-1/Math.pow((1+percent), credit_period));
                    var new_payment_per_month_text = "<strong>" + new_payment_per_month.toFixed(2) + "</strong> тенге в месяц";
                    var new_overpay = new_payment_per_month*credit_period - amount;
                    var new_overpay_text = "<strong>" + new_overpay.toFixed(2) + "</strong> переплата";

                    $(payment_per_month).eq(index).html(new_payment_per_month_text);
                    $(overpay).eq(index).html(new_overpay_text);
                });

                break;

            case 'loans': route = 'loans_ajax_amount';



                break;

            case 'credit_card': route = 'credits_ajax_amount';
                break;

            case 'credit_cards': route = 'credits_ajax_amount';
                break;
        }
    }

    $('#credit_fee_type ').on('change', function () {
        getFee();
        return false;
    });

    $('#credit_fee').on('keyup', function () {
        getFee();
        return false;
    });
// });