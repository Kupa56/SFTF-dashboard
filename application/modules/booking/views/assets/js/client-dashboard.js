// LINE CHART
(function(){
    "use strict";

    let dashboardDropdown =  $('.dashboard .dashboard-dropdown');

    /*
    * Count All bookings
    */

    let dashboardAnalyticsDataAll = {};

    $( "dashboard-analytics[dashboard-analytics-status=all]").each(function( index ) {
        let line = $(this).attr("dashboard-analytics-status");
        dashboardAnalyticsDataAll[line] = {};
        lineFunc(dashboardAnalyticsDataAll,line);
    }).promise().done(function () {
        chartLineFunc(dashboardAnalyticsDataAll);
    });

    /*
    * Count Pending bookings
    */

    let dashboardAnalyticsDataPending = {};

    $( "dashboard-analytics[dashboard-analytics-status=pending]").each(function( index ) {
        let line = $(this).attr("dashboard-analytics-status");
        dashboardAnalyticsDataPending[line] = {};
        lineFunc(dashboardAnalyticsDataPending,line);
    }).promise().done(function () {
        console.log(dashboardAnalyticsDataPending);
        parseData(dashboardAnalyticsDataPending,"pending");
    });

    /*
    * Count Canceled bookings
    */

    let dashboardAnalyticsDataCanceled = {};

    $( "dashboard-analytics[dashboard-analytics-status=canceled]").each(function( index ) {
        let line = $(this).attr("dashboard-analytics-status");
        dashboardAnalyticsDataCanceled[line] = {};
        lineFunc(dashboardAnalyticsDataCanceled,line);
    }).promise().done(function () {
        parseData(dashboardAnalyticsDataCanceled,"canceled");
    });

    /*
    * Count Confirmed bookings
    */

    let dashboardAnalyticsDataConfirmed = {};

    $( "dashboard-analytics[dashboard-analytics-status=confirmed]").each(function( index ) {
        let line = $(this).attr("dashboard-analytics-status");
        dashboardAnalyticsDataConfirmed[line] = {};
        lineFunc(dashboardAnalyticsDataConfirmed,line);
    }).promise().done(function () {
        parseData(dashboardAnalyticsDataConfirmed,"confirmed");
    });


    function lineFunc (dashboardAnalyticsData,line) {

        $( "dashboard-analytics[dashboard-analytics-status="+line+"] item").each(function( index ) {

            let itemkey = $(this).attr("data-key");
            let itemId = $(this).attr("data-id");
            let itemLabel = $(this).attr("data-label");
            let itemData = JSON.parse($(this).text());

            dashboardAnalyticsData[line][itemId] = {
                'key': itemkey,
                'label': itemLabel,
                'data': itemData
            };

        }).promise().done(function () {

        });
    }


    function parseData(data,status) {

        let myLinesStatusData = [];

        for (let key in data){
            for (let keyStatus in data[key]){
                let object = data[key][keyStatus];
                myLinesStatusData[keyStatus] = 0;
                for (let index in data[key][keyStatus].data){
                    let count = data[key][keyStatus].data[index];
                    myLinesStatusData[keyStatus] = myLinesStatusData[keyStatus]+count
                }
            }
        }



        $(".counters div[data-dashboard-status="+status+"]" +
            " h3[data-dashboard-count]").text(myLinesStatusData[parseInt(dashboardDropdown.val())]);

        //change the result by time
        dashboardDropdown.on('change',function () {
            let val = $(this).val();
            $(".counters div[data-dashboard-status="+status+"]" +
                " h3[data-dashboard-count]").text(myLinesStatusData[val]);
        });


    }



    function chartLineFunc (data) {

       let myLinesData = [];

        for (let key in data){
            for (let keyStatus in data[key]){
                let object = data[key][keyStatus];
                myLinesData[keyStatus] = [];
                for (let index in data[key][keyStatus].data){
                    let count = data[key][keyStatus].data[index];
                    myLinesData[keyStatus].push({
                        y: index, item1: count
                    })
                }
            }
        }

        //init chart line
        let lineChart = new Morris.Line({
            element: 'line-chart',
            resize: true,
            data: myLinesData[parseInt(dashboardDropdown.val())],
            xkey: 'y',
            ykeys: ['item1'],
            labels: [dashboardDropdown.attr('data-label')],
            lineColors: ['#3c8dbc'],
            hideHover: 'auto'
        });

        //disable overlay
        $('.reservation-dashboard .overlay').addClass("hidden");


        //change the result by time
        dashboardDropdown.on('change',function () {
            let val = $(this).val();
            lineChart.setData(myLinesData[val]);
        });

        //$( "dashboard-analytics[dashboard-analytics-status]").remove();

    }

})();