require("./bootstrap");
require("./worker/employee/employee-summary.js");
require("./highcharts.js");

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});
