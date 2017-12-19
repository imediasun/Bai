function plotDynamics(days)
{
    var history = historyData,
        options = {
            grid: {
                hoverable: true,
                show: true,
                borderColor: "#000",
                borderWidth: 0,
                margin: 5,
                axisMargin: 5
            },
            series: {
                color: '#6f7992',
                shadowSize: 0,
                lines: {
                    show: true,
                    lineWidth: 2
                },
                points: {
                    show: true,
                    lineWidth: 2,
                    fill: true,
                    radius: 2,
                    fillColor: "#6f7992"
                }
            },
            xaxis: {
                mode: "time",
                minTickSize: [1, "day"],
                timeformat: "%e %b",
                monthNames: ["янв", "фев", "мрт", "апр", "май", "июн", "июл", "авг", "сен", "окт", "нбр", "дек"]                                            },
            yaxis: {
                autoscaleMargin: 0.1
            }
        },
        data = [];

    history = history.slice(0, days).reverse();

    for (var i = 0; i < history.length; i++) {
        var newElement = [];
        newElement[0] = history[i]['microtime']+10000;
        newElement[1] = history[i]['value'];
        data[i] = newElement;
    }


    currPlot = $.plot($("#graph_cube_plot"), [
        {
            data: data,
            points: {
                show: true,
                lineWidth: 2,
                fill: true,
                radius: 0.7,
                fillColor: "#6f7992"
            }
        },
        {
            data: data.slice(data.length - 1), points: {symbol: "circle", radius: 3}
        }
    ], options);

    legends = $("#dynamicsGraph .legendLabel");
}

function updateLegend()
{
    updateLegendTimeout = null;
    var pos = latestPosition;
    var axes = currPlot.getAxes();
    if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max || pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) {
        return;
    }

    var i, j, dataset = currPlot.getData();
    for (i = 0; i < dataset.length; ++i) {
        var series = dataset[i];
        // Find the nearest points, x-wise
        for (j = 0; j < series.data.length; ++j) {
            if (series.data[j][0] > pos.x) {
                break;
            }
        }
        // Now Interpolate
        var y, p1 = series.data[j - 1], p2 = series.data[j];

        if (p1 == null) {
            y = p2[1];
        } else if (p2 == null) {
            y = p1[1];
        } else {
            y = p1[1] + (p2[1] - p1[1]) * (pos.x - p1[0]) / (p2[0] - p1[0]);
        }

        monthNames = ["янв", "фев", "мрт", "апр", "май", "июн", "июл", "авг", "сен", "окт", "нбр", "дек"];
        dateObj = new Date(Math.round(pos.x));
        var date = dateObj.getDate() + ' ' + monthNames[dateObj.getMonth()] + ' ' + dateObj.getFullYear();
        legends.eq(i).text(date + ' - ' + Number(y).toFixed(2));
    }
}

function showDynamics(date)
{
    var history = historyData;
    history = history.reverse();
    date = date.substr(6,4)+'-'+date.substr(3,2)+'-'+date.substr(0,2);
    var data = [], counter = 0;
    for (var i=0; i<history.length; i++) {
        if (history[i]['date'] == date) {
            data[counter] = history[i-1];
            counter++;
        }
        if (counter > 0 && counter <= 7) {
            data[counter] = history[i];
            counter++;
        }
    }
    renderDynamicsBlock(data);
}

function renderDynamicsBlock(data)
{
    var html = '', diff = 0, color = '', date = '';
    for (var i=1; i<=7; i++) {
        diff = data[i]['value']-data[i-1]['value'];
        diff = diff.toFixed(2);
        if (diff == 0) {
            color = 'cgray';
        } else if (diff > 0) {
            color = 'cred';
            diff = '+'+diff;
        } else if (diff < 0) {
            color = 'cgreen';
        }
        date = data[i]['date'].substr(8,2)+'.'+data[i]['date'].substr(5,2)+'.'+data[i]['date'].substr(0,4);
        html = html+'<tr><th>'+date+'</th><td>'+data[i]['value']+'</td><td><span class="m_neg '+color+'">'+diff+'</span></td></tr>';
    }
    $('#dynamics_data').html(html);
}