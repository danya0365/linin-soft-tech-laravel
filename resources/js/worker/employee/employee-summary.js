$(function () {
    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'employee-plot-chart',
            type: 'scatter',
            zoomType: 'xy'
        },
        title: {
            text: 'Title'
        },
        subtitle: {
            text: 'Subtitle'
        },
        xAxis: [{
            type: "datetime",
            tickInterval: 3 * 3600 * 1000, // 3 hours
            min: Date.UTC(2013, 3, 5, 0, 0, 0, 1),
            max: Date.UTC(2013, 3, 5, 23, 59, 59),
            startOnTick: false,
            lineWidth: 0,
            minorGridLineWidth: 0,
            lineColor: 'transparent',
            labels: {
               enabled: false
            },
            minorTickLength: 0,
            tickLength: 0
        }, {
            type: "datetime",
            tickInterval: 3 * 3600 * 1000, // 3 hours
            min: Date.UTC(2013, 3, 4, 0, 0, 0, 1),
            max: Date.UTC(2013, 3, 4, 23, 59, 59),
            startOnTick: false,
            lineWidth: 0,
            minorGridLineWidth: 0,
            lineColor: 'transparent',
            labels: {
               enabled: false
            },
            minorTickLength: 0,
            tickLength: 0
        }, {
            type: "datetime",
            tickInterval: 3 * 3600 * 1000, // 3 hours
            min: Date.UTC(2013, 3, 3, 0, 0, 0, 1),
            max: Date.UTC(2013, 3, 3, 23, 59, 59),
            startOnTick: false, 
            lineWidth: 0,
            minorGridLineWidth: 0,
            lineColor: 'transparent',
            labels: {
               enabled: false
            },
            minorTickLength: 0,
            tickLength: 0
        },{
            type: "datetime",
            tickInterval: 3 * 3600 * 1000, // 3 hours
            min: Date.UTC(2013, 3, 2, 0, 0, 0, 1),
            max: Date.UTC(2013, 3, 2, 23, 59, 59),
            startOnTick: false
        },],
        yAxis: {
            title: {
                text: 'Glucose'
            }
        },
        series: [{
            pointInterval: 3600 * 1000, // one day (in milisec.)
            name: '05. marca',
            color: 'rgba(223, 83, 83, .5)',
            xAxis: 0,
            data: [
                [Date.UTC(2013, 3, 5, 10), 100],
                [Date.UTC(2013, 3, 5, 11, 30), 120],
                [Date.UTC(2013, 3, 5, 12, 15), 130],
                [Date.UTC(2013, 3, 5, 14, 45), 160],
                [Date.UTC(2013, 3, 5, 16, 1), 130],
            ],
            marker: {
                symbol: "circle",
                fillColor: "rgba(143,30,200,.5)",
                radius: 5
            }
        },
        {
            pointInterval: 3600 * 1000, // one day (in milisec.)
            name: '4. marca',
            color: 'rgba(223, 83, 83, .5)',
            xAxis: 1,
            data: [
                [Date.UTC(2013, 3, 4, 10, 9), 100],
                [Date.UTC(2013, 3, 4, 11, 11), 120],
                [Date.UTC(2013, 3, 4, 12, 21), 130],
                [Date.UTC(2013, 3, 4, 14, 41), 160],
                [Date.UTC(2013, 3, 4, 16), 80]
            ],
            marker: {
                symbol: "circle",
                fillColor: "rgba(143,30,200,.5)",
                radius: 5
            }
        }, 
        {
            pointInterval: 3600 * 1000, // one day (in milisec.)
            name: '3. marca',
            color: 'rgba(223, 83, 83, .5)',
            xAxis: 2,
            data: [
                [Date.UTC(2013, 3, 3, 8, 11), 120],
                [Date.UTC(2013, 3, 3, 9, 13), 130],
                [Date.UTC(2013, 3, 3, 17, 31), 160],
                [Date.UTC(2013, 3, 3, 18, 20), 130]
            ],
            marker: {
                symbol: "circle",
                fillColor: "rgba(143,30,200,.5)",
                radius: 5
            }
        },
        {
            pointInterval: 3600 * 1000, // one day (in milisec.)
            name: '2. marca',
            color: 'rgba(223, 83, 83, .5)',
            xAxis: 3,
            data: [
                [Date.UTC(2013, 3, 2, 7, 16), 100],
                [Date.UTC(2013, 3, 2, 9, 30), 120],
                [Date.UTC(2013, 3, 2, 11, 34), 130],
                [Date.UTC(2013, 3, 2, 17, 41), 160],
                [Date.UTC(2013, 3, 2, 20, 53), 130]
            ],
            marker: {
                symbol: "circle",
                fillColor: "rgba(143,30,200,.5)",
                radius: 5
            }
        }]
    });
});