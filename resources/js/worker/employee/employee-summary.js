(function ($) {
    $.employeeSummaryScatterPlotsChart = function (options) {
        var settings = $.extend(
            {
                data: null,
                renderTo: null,
                tickInterval: 1 * 3600 * 1000, // 3 hours
                pointInterval: 3600 * 1000, // one day (in milisec.)
            },
            options
        );

        var dates = Object.keys(settings.data).map(function (key, item) {
            var date = key.split("-");
            var min = new Date(date[0], date[1] - 1, date[2], 0, 0, 0, 1);
            var max = new Date(date[0], date[1] - 1, date[2], 23, 59, 59);
            return {
                min: min.getTime(),
                max: max.getTime(),
            };
        });

        var xAxis = dates.map(function (date, key) {
            var labelEnabled = key == 0 ? true : false;
            return {
                type: "datetime",
                tickInterval: settings.tickInterval,
                min: date.min,
                max: date.max,
                startOnTick: false,
                lineWidth: 0,
                minorGridLineWidth: 0,
                lineColor: "transparent",
                labels: {
                    enabled: labelEnabled,
                },
                minorTickLength: 0,
                tickLength: 0,
            };
        });

        var colors = [
            "rgba(223, 83, 83, .5)",
            "rgba(235, 146, 52, .5)",
            "rgba(137, 235, 52, .5)",
            "rgba(52, 235, 174, .5)",
            "rgba(52, 220, 235, .5)",
            "rgba(52, 128, 235, .5)",
            "rgba(162, 52, 235, .5)",
        ];
        var series = Object.keys(settings.data).map(function (key, index) {
            var color = colors[index];
            var date = key;
            var items = settings.data[key];
            var data = items.map(function (item, index) {
                var date = new Date(item.created_at);
                return [
                    date.getTime(),
                    parseFloat(item.value),
                    {
                        y: date.getTime(),
                        x: parseFloat(item.value),
                        dateTime: date,
                    },
                ];
            });

            return {
                pointInterval: settings.pointInterval,
                name: date,
                color: color,
                xAxis: index,
                data: data,
                marker: {
                    symbol: "circle",
                    fillColor: color,
                    radius: 5,
                },
            };
        });

        Highcharts.setOptions({
            time: {
                timezoneOffset: -(7 * 60),
            },
        });

        chart = new Highcharts.Chart({
            chart: {
                renderTo: settings.renderTo,
                type: "scatter",
                zoomType: "xy",
            },
            title: {
                text: "Performance per hour",
            },
            tooltip: {
                formatter: function () {
                    return (
                        "<b>" +
                        this.series.name +
                        "</b><br/>" +
                        "<b>Time:</b> " +
                        Highcharts.dateFormat("%H:%M:%S", new Date(this.x)) +
                        "<br />" +
                        "<b>x:</b> " +
                        this.y +
                        ""
                    );
                },
            },
            subtitle: {
                text: "last 7 days",
            },
            xAxis: xAxis,
            yAxis: {
                title: {
                    text: "จำนวนที่ทำได้ - Total",
                },
            },
            series: series,
        });
    };
})(jQuery);
