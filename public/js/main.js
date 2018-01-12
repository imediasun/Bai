$(document).ready(function () {

    allInit();
});


function allInit() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function if_mobile() {
        if ($(window).width() < 760) return true;
    }

    function if_tab() {
        if ($(window).width() < 1001) return true;
    }

    if (!if_mobile()) {
        $('.bank_address_units').jScrollPane();
    }

    $('#calculate_form').submit(function () {
        return false;
    });

    $('.bank_address_unit').click(function () {
        // TODO Позиционирование на карте
        $(this).addClass('current').siblings().removeClass('current')
    });

    // tinysort('div.offers_list>.offer_unit', {selector: 'span.payment'});
    // tinysort('div.credits>.credit', {selector: 'span.payment'});
    $('a.sort_links').on('click', function(e) {
        e.preventDefault();

        $('a.sort_links').each(function () {
            $( this ).removeClass( "active" );
        });

        var page_type = $('#source_page').attr('product_type');

        $(this).addClass('active');
        // $('.sort_links').addClass('active');

        // tinysort('div.offers_list>.offer_unit', {selector: 'span.rate'});
        // tinysort('div.offers_list>.offer_unit', {selector: 'span.payment'});
        // if(page_type == 'credits' || page_type == 'mortgages' || page_type == 'autocredits'){
        //
        // }

        var target_id = $(this).data('class-toggle');
        if(target_id == 'less_overpay'){
            tinysort('div.credits>.credit', {selector: 'li.overpay>strong'});
        }
        if(target_id == 'less_payment'){
            tinysort('div.credits>.credit', {selector: 'li.payment_per_month>strong'});
        }
        if(target_id == 'less_rate'){
            tinysort('div.credits>.credit', {selector: 'li.credit_rate>strong'});
        }
        if(target_id == 'dep_less_rate'){
            tinysort('div.credits>.credit', {selector: 'li.dep_rate>strong'});
        }
        if(target_id == 'dep_less_period'){
            tinysort('div.credits>.credit', {selector: 'li.dep_term>strong'});
        }
        if(target_id == 'dep_cap_more'){
            tinysort('div.credits>.credit', {selector: 'li.capitalization>strong', order: 'desc' });
        }
    });

    $('.link_show_branch').click(function (e) {
        // // Check if target is a link
        // if($(e.target).hasClass('link_bank_page')) {
        //     return;
        // }
        //
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
                    console.dir(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(textStatus + ': ' + errorThrown);
                }
            });
        }

         $(this).find('th').width($(this).find('th').width() + 10);
         $(this).next('tr').find('.bank_address_toggle').toggleClass('v').slideToggle(function () {
             if ($(this).hasClass("v")) {
                 //.container.fitToViewport()
                 //$(this).css('display','table');
                 if (!if_mobile()) {
                     $('.bank_address_units').jScrollPane();
                     var pane = $(this).find($('.bank_address_units'));
                     pane.css('height', pane.parents('.bank_address_addresses').height() - pane.siblings('.address_units_hint').height())
                     var api = pane.data('jsp');
                     api.reinitialise();
                 }
                 //$(this).parents('tr').prev('tr').find('th').css('width','');

             }
         });
    });

    $('.reviews_slider > .hold').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        adaptiveHeight: true
    });

    $('.fast_select_slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1
    });
    $('.bank_services').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [{
            breakpoint: 480,
            settings: {
                variableWidth: true,
                slidesToShow: 1
            }
        }
        ]
    });

    $('.bank_cards').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [{
            breakpoint: 1016,
            settings: {
                variableWidth: true,
                slidesToShow: 2
            }
        }, {
            breakpoint: 768,
            settings: {
                variableWidth: true,
                slidesToShow: 1
            }
        }
        ]
    });

    $('.bidding_slider').slick({
        slidesToShow: 2,
        slidesToScroll: 1,
        variableWidth: true,
        infinite: false,
        responsive: [{
            breakpoint: 660,
            settings: {
                slidesToShow: 1
            }
        }
        ]
    });

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

    $('select').select2({
        minimumResultsForSearch: 20
    });
    initSlider();

    $('.tablist, .bidding_slider, .tabs_small, .map_places_list, .tabs_tags').each(function () {
        $(this).tabs();
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
    initMapWidth();


    //get credit online
    var modal_form = null;
    write_credit_params();
    //open modal
    $('.get_online').click(function (e) {
        e.preventDefault();
        storageData = {};
        write_credit_params();
        storageData.logo = $(this).parents().find('.credit.open').find('.image > img').attr('src');
        storageData.time = $(this).parents().find('.credit.open').find('.time').text();
        storageData.credit_id = $(this).parents().find('.credit.open').data('id');
        storageData.overpay = $(this).parents().find('.credit.open').find('.overpay > strong').text();
        load_credit_step1();
    });

	//close modal
	$('.close').click(function(e){
        $('.modal').hide(1000, function () {
            //$('.modal_container').html('');
            $('#compare_list').hide();
            $('.modal').show();
        });
        e.preventDefault();
	});

	//write form data
	$('#credit_period').change(function () {
        storageData.term = $(this).val();
        storageData.term_human = $(this).find("option:selected").text();
    });

	$('#amount_input').change(function () {
        storageData.amount = $(this).val();
        var insert_data = $(this).val().split(" ").join('');
        change_data(insert_data);
    });

    $('#amount_input').keyup(function () {
        cutFor3(this);
    });

	$('.currency').click(function () {
        storageData.currency = $(this).val();
        storageData.currency_symbol = $(this).parent().find('span').text();
    });
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

function cutFor3(input)
{
    res = $(input).val();
    res = res.replace(/\s/g, '');
    cntr = 1;
    res1 = '';
    for (j = res.toString().length - 1; j >= 0; j --) {
        res1 = res.toString().substr(j, 1) + res1;
        if (cntr >= 3 && j != 0) {
            res1 = ' ' + res1;
            cntr = 0;
        }
        cntr ++;
    }
    $(input).val(res1);
}

function load_credit_step1() {
    $.ajax({
        url: '/ajax/credit-online/credits/1',
        async: true,
        cache: false,
        type: "POST",
        dataType: "json",
        data: storageData,
        success: function (response) {
            $('.modal_container').html(response.html);

            $('.modal').show(1000);
            $('html,body').animate({scrollTop: $('.modal').offset().top});

            console.dir(storageData);
            // localStorage["storageData"] = JSON.stringify(storageData);
        },
        error: function (jqXHR) {
            console.dir(jqXHR);
        }
    });
}

function load_credit_step2(step1) {
    $.ajax({
        url: '/ajax/credit-online/credits/1',
        async: true,
        cache: false,
        type: "POST",
        dataType: "json",
        data: storageData,
        success: function (response) {
            step1.html(response);
        },
        error: function (jqXHR) {
            console.dir(jqXHR);
        }
    });
}

function load_credit_step3(step2) {
    $.ajax({
        url: Routing.generate('credit_ajax_step3'),
        async: true,
        cache: false,
        type: "POST",
        dataType: "json",
        data: storageData,
        success: function (response) {
            step2.html(response);
        },
        error: function (jqXHR) {
            console.dir(jqXHR);
        }
    });
}

function credit_sendmail_bank() {
    $.ajax({
        url: Routing.generate('credit_sendmail_bank'),
        async: true,
        cache: false,
        type: "POST",
        dataType: "json",
        data: storageData,
        success: function (response) {
            console.dir('email is sent');
            console.dir(response);
        },
        error: function (jqXHR) {
            console.dir(jqXHR);
        }
    });
}

var storageData = {};
function write_credit_params() {
    storageData.amount = $('#amount_input').val();
    storageData.product_type = $('#source_page').attr('product_type');
    storageData.term = $('#credit_period').val();
    storageData.term_human = $('#credit_period').find("option:selected").text();
    storageData.currency = $('.currency:checked').parent().find('input').val();
    storageData.currency_symbol = $('.currency:checked').parent().find('span').text();

    // localStorage["credit_data"] = JSON.stringify(storageData);
}

function initSlider() {
	jQuery('.slider_hold').parent().each(function () {
		var hold = jQuery(this);
		var slid = hold.find('.ui-slider');
		var box = hold.find('input:text');
		var points = slid.data('points').split(',');
		var step = 100000;
		slideFN = function (e, ui) {
            var count = Math.ceil((ui.value == 0 ? 1 : ui.value) / step);
            var percent = step + (ui.value - step * count);
            var val = Math.round(points[count - 1] / 1 + percent * (points[count] / 1 - points[count - 1] / 1) / step);

            try{
                $('#amount_input').attr('value',val);
                change_data(val);
            }catch (ex){

            }

            $(document).ready(function () {
                try {
                    changeSlider();
                }
                catch (e) {
                }
            });

            var credit_page_span = $('#credit_span_amount');
            if(credit_page_span){
                credit_page_span.text(val.toFixed(2));
			}

			box.val(val.addSpace());
		};

		slid.slider({
			range: "min",
			min: 0,
			step: 0.1,
			max: step * (points.length - 1),
			slide: slideFN
		});

		slideFN(null, {
			value: slid.slider("value")
		});

		box.bind('keyup', function () {
			var val = box.val().replace(/\s+/g, '');
			if (!isNaN(val) && (val >= points[0] / 1 && val <= points[points.length - 1] / 1)) {
				var count = 1;
				for (var i = 0; i < points.length - 1; i++) {
					if (val >= points[i] / 1 && val < points[i + 1] / 1) count = i;
				}
				if (val == points[points.length - 1] / 1) count = points.length - 2;
				slid.slider("value", (val - points[count] / 1) / (points[count + 1] / 1 - points[count] / 1) * step + step * count);
			}
			if (!isNaN(val) && val < points[0] / 1) {
				slid.slider("value", 0);
			}
			if (!isNaN(val) && val > points[points.length - 1] / 1) {
				slid.slider("value", step * (points.length - 1));
			}
		}).bind('blur', function () {
			//console.log(slid.slider("value"));
			slideFN(null, {
				value: slid.slider("value")
			});
		});
	});
}

Number.prototype.addSpace = function () {
	var temp = '';
	for (var i = this.toString().length - 1; i >= 0; i--) {
		temp = this.toString().charAt(i) + temp;
		if (!((this.toString().length - i) % 3) && i != 0) temp = ' ' + temp;
	};
	return temp;
}

function initHeaderMenu() {
	$('.header').each(function () {
		var hold = $(this);
		var options = {
			queue: true,
			duration: 200,
			easing: 'linear'
		};
		var linkMenu = hold.find('.toggle_menu');
		var linkLang = hold.find('.toggle_city');
		var menuNav = hold.find('.menu');
		var menuCity = hold.find('.city');

		var _closeMenu = function () {
			menuNav.removeClass('open');
			menuNav.slideUp(options);
			linkMenu.parent().removeClass('active');
		};

		var _showMenu = function () {
			menuNav.addClass('open');
			menuNav.slideDown(options);
			linkMenu.parent().addClass('active');
		};

		var _closeCity = function () {
			menuCity.removeClass('open');
			menuCity.slideUp(options);
			linkLang.parent().removeClass('active');
		};

		var _showCity = function () {
			menuCity.addClass('open');
			menuCity.slideDown(options);
			linkLang.parent().addClass('active');
		};

		_closeMenu();
		_closeCity();

		linkMenu.click(function () {
			if (menuNav.hasClass('open')) {
				_closeMenu();
			} else {
				_closeCity();
				_showMenu();
			}
			return false;
		});

		linkLang.click(function () {
			if (menuCity.hasClass('open')) {
				_closeCity();
			} else {
				_closeMenu();
				_showCity();
			}
			return false;
		});
	});
}

function initQuestion() {
	$('.question').each(function () {
		var hold = $(this);
		var options = {
			queue: true,
			duration: 200,
			easing: 'linear'
		};
		var link = hold.find('.toggle_show > span');
		var short = hold.find('.short');
		var full = hold.find('.full');
		var answer = hold.find('.answer');

		answer.css({
			"height": "auto"
		});
		answer.hide();

		full.css({
			"height": "auto"
		});
		full.hide();

		link.click(function () {
			hold.toggleClass('open');
			answer.slideToggle(options);
			short.slideToggle(options);
			full.slideToggle(options);
		});
	});
}

function initBankInfo() {
	$('.bank_info_section').each(function () {
		var hold = $(this);
		var options = {
			queue: true,
			duration: 200,
			easing: 'linear'
		};
		var link = hold.find('.bank_info_head');
		var text = hold.find('.bank_info_in');

		text.css({
			"height": "auto"
		});
		text.hide();

        hold.toggleClass('open');
        text.slideToggle(options);

        link.click(function () {
			hold.toggleClass('close');
			text.slideToggle(options);
		});
	});
}

function initBankCourse() {
	$('.bank_course').each(function () {
		var hold = $(this);
		var options = {
			queue: true,
			duration: 200,
			easing: 'linear'
		};
		var link = hold.find('.toggle');
		var text = hold.find('> .in');

		link.click(function () {
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
}

function initReview() {
	$('.review').each(function () {
		var hold = $(this);
		var options = {
			queue: true,
			duration: 200,
			easing: 'linear'
		};
		var link = hold.find('.toggle_answer > span');
		var answer = hold.find('.answer_form');

		answer.css({
			"height": "auto"
		});
		answer.hide();


		link.click(function () {
			hold.toggleClass('open');
			answer.slideToggle(options);
		});
	});
}

function initCredit() {
	$('.credit').each(function () {
		var hold = $(this);
		var options = {
			queue: true,
			duration: 200,
			easing: 'linear'
		};
		// var link = hold.find('.toggle_open');
		var link = hold.find('.c');
		var link_credit = $(hold);
		var details = hold.find('.in');

        details.css({
			"height": "auto"
		});
        details.hide();

        link_credit.click(function () {

            if (!$(details).children().length) {
                $.ajax({
                    url: Routing.generate('credit_ajax_get_details', {id: $(details).data('id')}),
                    cache: false,
                    type: "POST",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        // TODO Show some kind of Loading
                    },
                    success: function (response) {
                        details.html(response);
                    }
                });

            }

            // hold.toggleClass('open');
            // details.slideToggle(options);

        });

        link.click(function () {
            hold.toggleClass('open');
            details.slideToggle(options);
        });


	});
}

function initFormHidden() {
    $('.form_hidden').each(function () {
        var hold = $(this);
        var options = {
            queue: false,
            duration: 200,
            easing: 'linear'
        };
        var linkHold = hold.find('.form_toggle');
        var link = hold.find('.form_toggle .btn, .link_hide > span');
        var text = hold.find('.form_in');

        text.css({
            "height": "auto"
        });
        text.hide();

        link.click(function () {
            hold.toggleClass('open');
            linkHold.slideToggle(options);
            text.slideToggle(options);
        });
    });
}

function initFormMoreHidden() {
	$('.form_additional').each(function () {
		var hold = $(this);
		var options = {
			queue: false,
			duration: 200,
			easing: 'linear'
		};
		var link = hold.find('.toggle > div');
		var text = hold.find('.in');

		text.css({
			"height": "auto"
		});
		// text.hide();

		link.click(function () {
			hold.toggleClass('open');
			text.slideToggle(options);
		});
	});
}

function initRate() {
    $('.stars_rate').each(function () {
        var hold = $(this);
        var bg = hold.find('> div');
        var _radio = hold.find("input:radio");
        var text = hold.parent().next('.rate_text');
        var label = hold.find('label');

        var val1 = hold.find('input.rate1');
        var val2 = hold.find('input.rate2');
        var val3 = hold.find('input.rate3');
        var val4 = hold.find('input.rate4');
        var val5 = hold.find('input.rate5');

        /*var text1 = 'Очень плохо';
        var text2 = 'Так себе';
        var text3 = 'Нормально';
        var text4 = 'Хорошо';
        var text5 = 'Очень хорошо';*/

        var text1 = 'Плохо';
        var text2 = 'Плохо';
        var text3 = 'Плохо';
        var text4 = 'Хорошо';
        var text5 = 'Отлично';

        label.hover(function(){
            $(this).click();
        });

        var _check = function () {
            if (val1.prop('checked')) {
                bg.css({
                    'width': '20%'
                });
                text.html(text1);
            } else if (val2.prop('checked')) {
                bg.css({
                    'width': '40%'
                });
                text.html(text2);
            } else if (val3.prop('checked')) {
                bg.css({
                    'width': '60%'
                });
                text.html(text3);
            } else if (val4.prop('checked')) {
                bg.css({
                    'width': '80%'
                });
                text.html(text4);
            } else if (val5.prop('checked')) {
                bg.css({
                    'width': '100%'
                });
                text.html(text5);
            }
        };

        _check();

        _radio.change(function () {
            _check();
        });
    });
}

function initFaq() {
	$('.faq_el').each(function () {
		var hold = $(this);
		var options = {
			queue: true,
			duration: 200,
			easing: 'linear'
		};
		var link = hold.find('.head');
		var text = hold.find('.in');

		text.css({
			"height": "auto"
		});
		text.hide();


		link.click(function () {
			hold.toggleClass('open');
			text.slideToggle(options);
		});
	});
}

function initProgressWidth() {
	$('.graph').each(function () {
		var hold = $(this);
		var check = hold.find('.graph_pos');
		var line = hold.find('.graph_line');

		var _resize = function () {
			if (check.is(':visible')) {
				var h = hold.height();
				line.css({
					'width': h
				});
			} else {
				line.css({
					'width': 'auto'
				});
			}
		};

		_resize();
		$(window).resize(function () {
			_resize();
		});
	});
}

function initWidgetPage() {
	$('.widget_container').each(function () {
		var hold = $(this);
		var vert = hold.find('#setVert');
		var hor = hold.find('#setHor');
		var wid = hold.find('#setWidth');
		var vertClass = 'widget_container_vertical';
		var horClass = 'widget_container_horizontal';
		var horWidth = '900';
		var vertWidth = '238';

		var _check = function () {
			if (vert.is(':checked')) {
				hold.addClass(vertClass);
				hold.removeClass(horClass);
				wid.val(vertWidth);
			}

			if (hor.is(':checked')) {
				hold.addClass(horClass);
				hold.removeClass(vertClass);
				wid.val(horWidth);
			}
		};

		_check();
		vert.change(function () {
			_check();
		});
		hor.change(function () {
			_check();
		});
	});
}


function initPlaceInfo() {
	$('.place_info').each(function () {
		var hold = $(this);
		var options = {
			queue: true,
			duration: 200,
			easing: 'linear'
		};
		var linkHold = hold.find('.place_contacts_show');
		var link = linkHold.find('.btn');
		var text = hold.find('.place_contacts_hide');
		
		link.click(function(){
			text.slideToggle(options);
			linkHold.slideToggle(options);
		});
	});
}

/**
 * jQuery tabs min v1.0.0
 * Copyright (c) 2011 JetCoders
 * email: yuriy.shpak@jetcoders.com
 * www: JetCoders.com
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 **/

jQuery.fn.tabs = function (options) {
	return new Tabs(this.get(0), options);
};

function Tabs(context, options) {
	this.init(context, options);
}
Tabs.prototype = {
	options: {},
	init: function (context, options) {
		this.options = jQuery.extend({
			listOfTabs: 'a.tab',
			active: 'active',
			event: 'click'
		}, options || {});
		this.btn = jQuery(context).find(this.options.listOfTabs);
		this.last = this.btn.index(this.btn.filter('.' + this.options.active));
		if (this.last == -1) this.last = 0;
		this.btn.removeClass(this.options.active).eq(this.last).addClass(this.options.active);
		var _this = this;
		this.btn.each(function (i) {
			jQuery($(this).attr('href')).addClass('hidden-tab');
			if (_this.last == i) jQuery($(this).attr('href')).addClass('visible');
			//else jQuery($(this).attr('href')).hide();
		});
		this.initEvent(this, this.btn);
	},
	initEvent: function ($this, el) {
		el.bind(this.options.event, function () {
			if ($this.last != el.index(jQuery(this))) $this.changeTab(el.index(jQuery(this)));
			return false;
		});
	},
	changeTab: function (ind) {
		jQuery(this.btn.eq(this.last).attr('href')).removeClass('visible');
		jQuery(this.btn.eq(ind).attr('href')).addClass('visible');
		this.btn.eq(this.last).removeClass(this.options.active);
		this.btn.eq(ind).addClass(this.options.active);
		this.last = ind;
	}
}


//------compare----------
var comparison_count = 0;
var compare_slug;
function createComparison(count, slug) {
    compare_slug = slug;
	if(count){
        $('#compare_bar').show();
        comparison_count = count;
        $('.loans_count').text(count);

    }
}

$(document).ready(function () {
    $('#compare_button').on('click', showCompareList);
});

function dropComparison() {
    $('.to_compare').removeClass('active');
    comparison_count = 0;
    $('#compare_bar').hide();
    $.get('/' + compare_slug + '/compare/dropCompare');
}
function showCompareList() {
    compare_slug = 'credit';
    $.get('/ajax/' + compare_slug + '/compare/compareList',function (data) {
        $('#compare_table').html(data.html);
        $('#compare_table').ready(function () {
            $('#compare_list').show();
        });
    });
}
function comparationListToggle(id, product ){

    var insert_data = $('#amount_input').val().split(" ").join('');

    var amount = insert_data;
    console.dir(amount);
    var period  = $('#credit_period').val();
    var currency = $('.currency:checked').val();
    $.post('/ajax/compare/toggleCompare', {
        product: product,
        id: id,
        amount: amount,
        period:period,
        currency:currency
    }, function (data) {
            $('#compare_bar').show();
            if(data.action == 'add'){
                $('#to_compare_'+id).addClass('active');
                comparison_count++;
            }
            if(data.action == 'remove'){
                $('#to_compare_'+id).removeClass('active');
                comparison_count--;
            }
            $('.loans_count').text(comparison_count);
    });
}

function removeFromCompareList(id) {
    comparationListToggle(id)
    $('.compare_list_item_'+id).remove();
}
//------end compare------