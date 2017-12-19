var changeSlider;
var yandexMap;
var inputval = 'nal';
function ajaxCource(altName) {

    if  (inputval == 'nal'){
        console.log('nal');

        var param = {
            'currency': $('#currency').val(),
            'count': nospace($('#count').val()),
            'direction': $('#direction').val()
        };

        $.ajax({
            url: '/ajax/courseCalculator/'+altName+'/',
            cache: false,
            type: "POST",
            // dataType: "json",
            data: param,
            beforeSend: function( xhr ) {
                $('#onlist').addClass('cover_hold');
                $('.loading_cover').css({'display':'block'});
            },
            success: function (response) {
                $('#onlist').removeClass('cover_hold');

                $('#onlist').html(response);
                $('#onlist').ready(function () {

                    reloadPage();

                });
                // console.dir(response);
            },

        });

        // $.post('/ajax/courseCalculator/'+altName)
        //     .done(function(data) {
        //         $('#onlist').html(data);
        //         $('#onlist').ready(function () {
        //
        //             reloadPage();
        //         });
        //
        //         // $(function () {
        //         //     $('.loading_cover').css({'display':'none'});
        //         //     console.log(111);
        //         // });
        //
        //     });

    }else{
        getNotNalList();
    }



}
function nospace(str) {
    var RegEx=/\s/g;

    return str.replace(RegEx,"");

}

function createCalculate(altName) {
    var tm;
    $('#currency').change(function () {
        ajaxCource(altName);
    });

    changeSlider = function () {
        clearTimeout(tm);
        tm = setTimeout(function () {
            ajaxCource(altName);
        }, 400);
    };
    $('#count').change('blur', function () {
        changeSlider();
    });
    $('#direction').change(function () {
        ajaxCource(altName);
    });

    $('input[name="nal"]').change(function () {

        inputval = $(this).val();
        if($(this).val() == 'nal'){
            ajaxCource(altName);
        }else {
            getNotNalList();
        }
    });
    $('#mob_place .close').on('click', function () {
        $('#mob_place').hide();
    })

    $('.loading_cover').css({'display':'none'});

}

function reloadPage() {

    $('.offices_list_slider .list_places').slick({
        vertical: true,
        verticalSwiping: true,
        dots: false,
        arrows: false,
        asNavFor: '.places_slider',
        focusOnSelect: true,
        infinite: false
    });

    $('.places_slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        swipe: false,
        swipeToSlide: false,
        dots: false,
        arrows: false,
        fade: true,
        asNavFor: '.offices_list_slider .list_places',
        infinite: false
    });

    $('.today_courses_slider ul').slick({
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite: false,
        responsive: [{
            breakpoint: 768,
            settings: {
                slidesToShow: 2
            }
        },{
            breakpoint: 512,
            settings: {
                slidesToShow: 1
            }
        }
        ]
    });

    initHeaderMenu();
    initQuestion();
    initReview();
    initCredit();
    initFormHidden();
    initFormMoreHidden();
    initRate();
    initFaq();
    initBankInfo();
    initBankCourse();
    initProgressWidth();
    initWidgetPage();
    initPlaceInfo();
    slideClick();
    getMVInfo();
    initMapWidth();

    $('.loading_cover').css({'display':'none'});

}

function initMapWidth () {
    $('.raside_hold_map').each(function(){
        var hold = $(this);
        var map = hold.find('> .map_place');
        var box = hold.find('> .side_l');

        var _resize = function () {
            map.css({
                width: $(window).width() - (box.offset().left + box.outerWidth())
            });
        }
        _resize();

        $(window).bind('resize', _resize);
    });
}

function slideClick() {
    $('.slick-slider').on('click', '.slick-slide', function () {
        var rate = $(this).data('rateid');
        $.get('/ajax/getBank/' +  $(this).data('bankid'), function (data) {
            var res = JSON.parse(data);
            $('#place_info_'+rate+' .address').text(res.address);
            $('#place_info_'+rate+' .phone').text(res.phone);
            $('#place_info_'+rate+' .description').text(res.description);


            $('#mob_plase_address').text(res.address);
            $('#mob_plase_tel').text(res.phone);
            $('#mob_plase_decs').text(res.description);
            mapSearch(res.city + ' ' + res.address);
            $('#mob_place').show();
        });
    });
}

$(document).ready(function () {
    slideClick();
    $('#mob_map').on('click', function () {
        if(!yandexMap || yandexMap.container._parentElement.id != 'map_place1')
        yandexMap = new ymaps.Map('map_place1', {
            center: [76.953352, 43.236529],
            zoom: 9
        });
    })

    $('#currency').change(function () {
        $('#dinamica_link').text('Динамика '+$(this).val());
    });
});
if(screen.width > 1019){
    ymaps.ready(mapInit);
}
function mapInit() {
    if(!yandexMap){
        var heigth = document.body.clientHeight;
        if (heigth > $('.side_l').height()){
            heigth = $('.side_l').height();
        }
        $('#map_place').height(heigth);
        $('#map_place').stick_in_parent()
            .on("sticky_kit:stick", function(e) {
                $(this).css({
                    'left': 'auto',
                    'right': '0'
                })
            })
            .on("sticky_kit:unbottom", function(e) {
                $(this).css({
                    'left': 'auto',
                    'right': '0'
                })
            })
            .on("sticky_kit:unstick", function(e) {
                $(this).css({
                    'left': '',
                    'right': ''
                })
            })
            .on("sticky_kit:bottom", function(e) {
                $(this).css({
                    'left': '',
                    'right': ''
                })
            })
        ;
        yandexMap = new ymaps.Map('map_place', {
            center: [76.953352, 43.236529],
            zoom: 9
        });
    }
}
function mapSearch(address) {
    ymaps.geocode(address, {
        results: 1
    }).then(function (res) {
        var firstGeoObject = res.geoObjects.get(0),
            coords = firstGeoObject.geometry.getCoordinates(),
            bounds = firstGeoObject.properties.get('boundedBy');

        firstGeoObject.options.set('preset', 'islands#darkBlueDotIconWithCaption');
        firstGeoObject.properties.set('iconCaption', firstGeoObject.getAddressLine());

        yandexMap.geoObjects.add(firstGeoObject);
        yandexMap.setBounds(bounds, {
            checkZoomRange: true
        });
    });
}
function getNotNalList() {
    var param = {
        'currency': $('#currency').val(),
        'count': nospace($('#count').val()),
        'direction': $('#direction').val()
    };
    $.post('/ajax/notnallist/', param)
        .done(function(data) {
            $('#onlist').html(data);
            $('#onlist').ready(function () {

                reloadPage();
            });

            // $(function () {
            //     $('.loading_cover').css({'display':'none'});
            //     console.log(111);
            // });

        });
}
function getMVInfo() {
    $('select[name="filial"]').change(function () {
        var  temp = $(this).val().split('_');
        $.get('/ajax/getBank/' +  temp[1], function (data) {
            var res = JSON.parse(data);
            console.dir(res);
            $('#place_info_'+temp[0]+' .address_value').text(res.address);
            $('#place_info_'+temp[0]+' .tell_value').text(res.phone);
            $('#place_info_'+temp[0]+' .desc_value').text(res.description);
        });
    })
}