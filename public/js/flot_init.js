$(document).ready(function () {
    initHightcharts();
    bindConvertor();
});


function initHightcharts() {
    var url = window.location.pathname.replace('kursy', 'ajax');
    $.getJSON( url, function( data ) {

        for (var i = 0; i < data.length; i++){
            data[i][0] = new Date(data[i][0]).getTime();
        }

        Highcharts.stockChart('placeholder_div', {
            rangeSelector: {
                selected: 1,
                inputEnabled: !1,
                buttonTheme: { visibility: "hidden" },
                labelStyle: { visibility: "hidden" }
            },

            title: {
                text: ''
            },

            tooltip: {
                backgroundColor: "#ccc",
                borderRadius: 2,
                borderWidth: 0,
                shadow: !0,
                useHTML: !0,
                style: {
                    padding: "6px", color: "#373d42"
                },
                formatter: function () {
                    return formatDate(this.x) + ' - ' + this.y.toFixed(2)
                }
            },
            navigation: {
                buttonOptions: {
                    enabled: false
                },
            },
            credits: {
                enabled: false
            },
            navigator: {
                maskFill: 'rgba(98,194,0, 0.5)',
                margin: 18,
                height: 65,
                series: {
                    color: 'green',
                    fillColor: 'white',
                    lineColor: 'green'
                }
            },
            scrollbar: {
                enabled: false
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    second: '%Y-%m-%d<br/>%H:%M:%S',
                    minute: '%Y-%m-%d<br/>%H:%M',
                    hour: '%Y-%m-%d<br/>%H:%M',
                    day: '%Y<br/>%m-%d',
                    week: '%Y<br/>%m-%d',
                    month: '%Y-%m',
                    year: '%Y'
                },
                plotLines: [{
                    color: '#FF0000',
                    width: 2,
                }],
                crosshair: {
                    width: 1,
                    color: 'red'
                }
            },
            series: [{
                name: ' ',
                data: data,
                color: '#62c200',
                shadow: true,
                tooltip: {
                    valueDecimals: 2
                }
            }]
        });
    }).fail(function() { initHightcharts() });

}

function bindConvertor() {
    $('#top_cur_input').bind('keyup', function () {
        topCalculateForm();
    });
    $('#bott_cur_input').bind('keyup', function () {
        bottCalculateForm()
    });

    $('#bott_cur_select').change(function () {
        bottCalculateForm();
    });

    $('#top_cur_select').change(function () {
        topCalculateForm();
    });
}

function formatDate(date) {
    date = new Date(date);
    var month = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
    var dd = date.getDate();
    if (dd < 10) dd = '0' + dd;

    var mm = month[date.getMonth(   )];
    var yy = date.getFullYear() % 100;
    if (yy < 10) yy = '0' + yy;

    return dd + ' ' + mm + ' ' + yy;
}

function topCalculateForm() {
    var topval = $('#top_cur_input').val().replace(' ','');
    var topCurRate = $('#top_cur_select').val();
    var botCurRate = $('#bott_cur_select').val();

    var tempValue = ((topval*1)*(topCurRate*1)/(botCurRate*1)).toFixed(2);
    $('#bott_cur_input').val(tempValue);
    $( "#bott_cur_slider" ).slider( "value", tempValue );

}

function bottCalculateForm() {
    var bottval = $('#bott_cur_input').val().replace(' ','');
    var topCurRate = $('#top_cur_select').val();
    var botCurRate = $('#bott_cur_select').val();

    var tempValue = ((bottval*1)*(botCurRate*1)/(topCurRate*1)).toFixed(2);
    $('#top_cur_input').val(tempValue);
    $( "#top_cur_slider" ).slider( "value", tempValue );
}