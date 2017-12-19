var not_nal_page =0;
var nal_page = 0;

function slideClick() {
    $('.slick-slider').on('click', '.slick-slide', function () {
        $.get('/ajax/getBank/' +  $(this).data('bankid'), function (data) {
            var res = JSON.parse(data);

            $('#place_info .address').text(res.address);
            $('#place_info .phone').text(res.phone);
            $('#place_info .description').text(res.description);


            $('#onmap .address').text(res.address);
            $('#onmap .phone').text(res.phone);
            $('#onmap .description').text(res.description);
            mapSearch(res.city + ' ' + res.address);
            $('#mob_place').show();
        });
    });
}

$(function () {
   slideClick();
    $('#no_cash_add').on('click', function () {
        var city = $(this).data('city');
        var bank = $(this).data('bank');
        not_nal_page++;
        $.get('/ajax/no-cash-bank/'+city+'/'+bank+'/'+not_nal_page, function (data) {
            $('#not_nal_list').append(data);
        })

    });

    $('#cash_add').on('click', function () {
        var city = $(this).data('city');
        var bank = $(this).data('bank');
        nal_page++;
        $.get('/ajax/cash-bank/'+city+'/'+bank+'/'+nal_page, function (data) {
            $('#nal_list').append(data);
            addActionToNewItem(data)
        })
    });
    $('#map_plase_close').click(function () {
        $('#map_place_info').hide();
    });
    $('.slick-slide').click(function () {
        $('#map_place_info').show();
    });

    initConvert();
});

function addActionToNewItem(data) {
    $('#nal_list .link_show_branch').unbind( "click" );
    $('#nal_list .bank_course').each(function () {
        var hold = $(this);
        var options = {
            queue: true,
            duration: 200,
            easing: 'linear'
        };
        var link = hold.find('.toggle');
        var text = hold.find('> .in');

        link.click(function () {
            console.log('link');
            if (hold.hasClass('open')) {
                hold.removeClass('open');
                text.slideUp(options);
            } else {
                text.css({
                    "height": "auto"
                });
                text.hide();
                hold.addClass('open');
                text.slideDown(options);
            }
        });
    });
    $('#nal_list .link_show_branch').click(function (e) {

        e.preventDefault();

        var altName = $(this).data('alt-name'),
            rateType = $(this).data('rate-type'),
            branchCount = $(this).data('branch-count'),
            branchContainer = $('#address_tr_' + altName);

        if (branchCount && branchContainer.html()) {
            $.ajax({
                url: Routing.generate('bank_ajax_get_branches'),
                async: true,
                cache: false,
                type: "POST",
                dataType: "json",
                data: {
                    altName: altName,
                    rateType: rateType
                },
                success:function (response) {
                    branchContainer.html(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(textStatus + ': ' + errorThrown);
                }
            });
        }

    });

}

function initConvert() {
    convertRate('#nal_form');
    convertRate('#beznal_form');
}

function convertRate(selector){

   var firstField = $(selector + ' .amount_in');
   var firstSelect = $(selector + ' .curr_in');
   var secondField = $(selector + ' .amount_out');
   var secondSelect = $(selector + ' .curr_out');
   var transfer = $(selector + ' .ic_data_transfer');

   firstField.keyup(function () {
      var value = ($(this).val()*1)
                *(firstSelect.val()*1)
                /(secondSelect.val()*1);
      secondField.val(value.toFixed(2));
   });

    firstSelect.change(function () {
        firstField.keyup();
    });
    secondSelect.change(function () {
        firstField.keyup();
    });

    transfer.click(function () {
       var temp = firstSelect.val();

       firstSelect.find('option').each(function () {
           $(this).attr('selected', false);
           if($(this).val() == secondSelect.val()){
               $(this).attr('selected', true);
               firstField.keyup();
           }
       });

        secondSelect.find('option').each(function () {
            $(this).attr('selected', false);
            if($(this).val() == temp){
                $(this).attr('selected', true);
                firstField.keyup();
            }
        });
        firstSelect.select2('destroy');
        secondSelect.select2('destroy');


        firstSelect.select2();
        secondSelect.select2();
    });
    firstField.keyup();
}