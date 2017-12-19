$(document).ready(function () {

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
		}]
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
		}]
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
		}]
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
		}, {
			breakpoint: 512,
			settings: {
				slidesToShow: 1
			}
		}]
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
});

function initSlider() {
	jQuery('.slider_hold').parent().each(function () {
		var hold = jQuery(this);
		var slid = hold.find('.ui-slider');
		var box = hold.find('input:text');
		var points = slid.data('points').split(',');
		var step = 100000;
		var slideFN = function (e, ui) {
			var count = Math.ceil((ui.value == 0 ? 1 : ui.value) / step);
			var percent = step + (ui.value - step * count);
			var val = Math.round(points[count - 1] / 1 + percent * (points[count] / 1 - points[count - 1] / 1) / step);

			box.val(val.addSpace());
		};

		slid.slider({
			range: "min",
			value: 0,
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
			console.log(slid.slider("value"));
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

		link.click(function () {
			hold.toggleClass('open');
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
		var link = hold.find('.toggle_open');
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
		text.hide();

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

		var text1 = 'Очень плохо';
		var text2 = 'Так себе';
		var text3 = 'Нормально';
		var text4 = 'Хорошо';
		var text5 = 'Очень хорошо';
		
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

		link.click(function () {
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
