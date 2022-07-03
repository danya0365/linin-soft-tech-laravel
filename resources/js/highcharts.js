(function ($) {
    function energyPieChart(targetId, dataChart) {
        var data = [];

        for (const [key, value] of Object.entries(dataChart)) {
            if (data.length == 0) {
                data.push({
                    name: value.name,
                    y: parseFloat(value.value),
                    sliced: true,
                    selected: true,
                });
            } else {
                data.push({
                    name: value.name,
                    y: parseFloat(value.value),
                });
            }
        }

        var series = [
            {
                name: "พลังงาน",
                colorByPoint: true,
                data: data,
            },
        ];

        Highcharts.chart(targetId, {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: "pie",
            },
            title: {
                text: "พลังงานที่ใช้ (บาท)",
            },
            tooltip: {
                pointFormat: "{series.name}: <b>{point.y} บาท</b>",
            },
            accessibility: {
                point: {
                    valueSuffix: "%",
                },
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: "pointer",
                    dataLabels: {
                        enabled: true,
                        format: "<b>{point.name}</b>: {point.percentage:.1f} %",
                    },
                },
            },
            series: series,
        });
    }

    $.energyPieChart = function (options) {
        var settings = $.extend(
            {
                data: null,
                renderTo: null,
            },
            options
        );

        energyPieChart(settings.renderTo, settings.data);
    };
})(jQuery);

(function ($) {
    function salesChart(
        targetId,
        categories,
        expenseYearSummary,
        incomeYearSummary,
        profitYearSummary
    ) {
        var expenseData = [];
        for (const [key, value] of Object.entries(expenseYearSummary)) {
            expenseData.push(parseFloat(value.total_amount));
        }
        var expenses = {
            name: "ต้นทุน",
            data: expenseData,
        };

        var incomeData = [];
        for (const [key, value] of Object.entries(incomeYearSummary)) {
            incomeData.push(parseFloat(value.total_amount));
        }
        var incomes = {
            name: "ยอดขาย",
            data: incomeData,
        };

        var profitData = [];
        for (const [key, value] of Object.entries(profitYearSummary)) {
            profitData.push(parseFloat(value.total_amount));
        }
        var profits = {
            name: "กำไร",
            data: profitData,
        };

        var series = [incomes, expenses, profits];

        Highcharts.chart(targetId, {
            chart: {
                type: "column",
            },
            credits: {
                enabled: false,
            },
            title: {
                text: "ต้นทุน, ยอดขาย, กำไร",
            },
            subtitle: {
                text: "",
            },
            xAxis: {
                categories: categories,
                crosshair: true,
            },
            yAxis: {
                title: {
                    text: "จำนวนเงิน (บาท)",
                },
                labels: {
                    formatter: function () {
                        return (
                            Math.round(this.value * 100) / 100
                        ).toLocaleString();
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

    $.salesChart = function (options) {
        var settings = $.extend(
            {
                data: null,
                renderTo: null,
            },
            options
        );

        salesChart(
            settings.renderTo,
            settings.data.monthYearTitles,
            settings.data.expenseYearSummary,
            settings.data.incomeYearSummary,
            settings.data.profitYearSummary
        );
    };
})(jQuery);

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
                title: {
                    text: "จำนวนเงิน (บาท)",
                },
                labels: {
                    formatter: function () {
                        return (
                            Math.round(this.value * 100) / 100
                        ).toLocaleString();
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
            },
            yAxis: {
                title: {
                    text: "จำนวนเงิน (บาท)",
                },
                labels: {
                    formatter: function () {
                        return (
                            Math.round(this.value * 100) / 100
                        ).toLocaleString();
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

(function ($) {
    function incomeDaysChart(targetId, categories, seriesData) {
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
                text: "ยอดขาย 7 วันล่าสุด",
            },
            subtitle: {
                text: "",
            },
            xAxis: {
                categories: categories,
                crosshair: true,
            },
            yAxis: {
                title: {
                    text: "จำนวนเงิน (บาท)",
                },
                labels: {
                    formatter: function () {
                        return (
                            Math.round(this.value * 100) / 100
                        ).toLocaleString();
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

    $.incomeDaysChart = function (options) {
        var settings = $.extend(
            {
                data: null,
                renderTo: null,
            },
            options
        );

        incomeDaysChart(
            settings.renderTo,
            settings.data.titles,
            settings.data.data
        );
    };
})(jQuery);

(function ($) {
    function salesLatestDaysChart(targetId, titleText, categories, seriesData) {
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
            credits: {
                enabled: false,
            },
            title: {
                text: titleText,
            },
            subtitle: {
                text: "",
            },
            xAxis: {
                categories: categories,
                crosshair: true,
            },
            yAxis: {
                title: {
                    text: "จำนวนเงิน (บาท)",
                },
                labels: {
                    formatter: function () {
                        return (
                            Math.round(this.value * 100) / 100
                        ).toLocaleString();
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

    $.salesLatestDaysChart = function (options) {
        var settings = $.extend(
            {
                data: null,
                renderTo: null,
                title: "",
            },
            options
        );

        salesLatestDaysChart(
            settings.renderTo,
            settings.title,
            settings.data.titles,
            settings.data.data
        );
    };
})(jQuery);
