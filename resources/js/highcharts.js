(function ($) {
    function energyDaysChart(targetId, categories, seriesData) {
        var series = seriesData.map(function (item, index) {
            var name = item.name;
            var data = item.data.map(function (item, index) {
                return parseFloat(item);
            });
            return {
                name: name,
                data: data,
            };
        });

        Highcharts.chart(targetId, {
            chart: {
                type: "column",
            },
            title: {
                text: "ยอดการใช้พลังงาน 7 วันล่าสุด",
            },
            subtitle: {
                text: "",
            },
            xAxis: {
                categories: categories,
                crosshair: true,
            },
            yAxis: {
                min: 0,
                title: {
                    text: "จำนวนเงิน (บาท)",
                },
                labels: {
                    formatter: function () {
                        return this.value;
                    },
                },
            },
            tooltip: {
                headerFormat:
                    '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat:
                    '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} บาท</b></td></tr>',
                footerFormat: "</table>",
                shared: true,
                useHTML: true,
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                },
            },
            series: series,
        });
    }

    $.energyDaysChart = function (options) {
        var settings = $.extend(
            {
                data: null,
                renderTo: null,
                tickInterval: 1 * 3600 * 1000, // 1 hours
                pointInterval: 3600 * 1000, // one day (in milisec.)
            },
            options
        );

        energyDaysChart(
            settings.renderTo,
            settings.data.titles,
            settings.data.data
        );
    };
})(jQuery);

(function ($) {
    function expenseDaysChart(targetId, categories, seriesData) {
        var series = seriesData.map(function (item, index) {
            var name = item.name;
            var data = item.data.map(function (item, index) {
                return parseFloat(item);
            });
            return {
                name: name,
                data: data,
            };
        });

        Highcharts.chart(targetId, {
            chart: {
                type: "column",
            },
            title: {
                text: "ยอดต้นทุน 7 วันล่าสุด",
            },
            subtitle: {
                text: "",
            },
            xAxis: {
                categories: categories,
                crosshair: true,
                labels: {
                    formatter: function () {
                        return this.value;
                    },
                },
            },
            yAxis: {
                min: 0,
                title: {
                    text: "จำนวนเงิน (บาท)",
                },
                labels: {
                    formatter: function () {
                        return this.value;
                    },
                },
            },
            tooltip: {
                headerFormat:
                    '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat:
                    '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} บาท</b></td></tr>',
                footerFormat: "</table>",
                shared: true,
                useHTML: true,
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                },
            },
            series: series,
        });
    }

    $.expenseDaysChart = function (options) {
        var settings = $.extend(
            {
                data: null,
                renderTo: null,
            },
            options
        );

        expenseDaysChart(
            settings.renderTo,
            settings.data.titles,
            settings.data.data
        );
    };
})(jQuery);
