var review_page = 1;

$(document).ready(function () {
    filterInit();
    $('#review_form').submit(function () {

        var data = $(this).serializeArray();

        $.post('/otzivy/review-process/', data, function () {
            var url = '';

            if($('#bank_select').val() && $('#bank_select').val() != 'null' ){
                url += '/' + $('#bank_select').val();
            }
            if($('#category_select').val() && $('#category_select').val() != 'null'){
                url += '/' + $('#category_select').val();
            }
            if($('#city_select').val() && $('#city_select').val() != 'null'){
                url += '/' + $('#city_select').val();
            }
            window.location.pathname = '/otzivy'+url+'/';
        });

        return false;
    });
    initInteractiveFilter();
});

function filterInit() {
    $('.filter_form_select').change(function () {

        var url = '/otzivy/filter/' + getFilter();
        if($(this).val() != 'null'){
            $.get(url, function (data) {
                $('#bank_ratings').html(data);
                $('#pagination_page').show();
                review_page = 1;
                initReview();
            });
        }
    })
}

function getFilter() {
    var url = '';

    if($('#bank_select').val()){
        url += '/' + $('#bank_select').val();
    }else{ url += '/null'}
    if($('#category_select').val()){
        url += '/' + $('#category_select').val();
    }else{ url += '/null'}
    if($('#city_select').val()){
        url += '/' + $('#city_select').val();
    }else{ url += '/null'}
    if($('#rate_select').val()){
        url += '/' + $('#rate_select').val();
    }else{ url += '/null'}

    return url;
}



function pagination() {
    var formData = $('#search_form').serializeArray();
    review_page++;
    formData.push({
        'name': 'page',
        'value': review_page
    });
    $.post('/otzivy/review-pagination/', formData, function (data) {
        $('#bank_ratings').html(data);
        initReview();
    });
}


function initInteractiveFilter() {

    var bank = $('#bank_select');
    var category = $('#category_select');
    var city = $('#city_select');

    bank.change(function () {
        if ((!category.val() || category.val() == 'null') && bank.val() != 'null' ){
            $.get('/otzivy/get-product-options/'+$(this).val(), function (data) {
                category.html(data);
                category.select2('destroy');
                category.select2();
            })
        }else{
            $.get('/otzivy/get-bank-options/null/', function (data) {
                bank.html(data);
                bank.select2('destroy');
                bank.select2();
            })
        }
        if (!city.val() || city.val() == 'null'){
            $.get('/otzivy/get-city-options/'+$(this).val()+'/', function (data) {
                city.html(data);
                city.select2('destroy');
                city.select2();
            })
        }
    });

    category.change(function () {
        if(!bank.val() || bank.val() == 'null'){
            console.log('bank');
            $.get('/otzivy/get-bank-options/'+$(this).val()+'/', function (data) {
                bank.html(data);
                bank.select2('destroy');
                bank.select2();
            })
        }else{
            $.get('/otzivy/get-product-options/null/', function (data) {
                category.html(data);
                category.select2('destroy');
                category.select2();
            })
        }
    });

    city.change(function () {
        if((!bank.val() || bank.val() == 'null') && category.val() != 'null'){
            console.log('bank');
            $.get('/otzivy/get-bank-options/null/', function (data) {
                bank.html(data);
                bank.select2('destroy');
                bank.select2();
            })
        }else{
            $.get('/otzivy/get-city-options/null/', function (data) {
                city.html(data);
                city.select2('destroy');
                city.select2();
            })
        }
    });
}