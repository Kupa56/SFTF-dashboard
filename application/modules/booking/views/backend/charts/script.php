
<script src="<?=  adminAssets("plugins/chartjs/Chart.js")?>"></script>
<script>


    $(function () {
        /* ChartJS
         * -------
         * Here we will create a few charts using ChartJS
         */

        //--------------
        //- AREA CHART -
        //--------------


        // Get context with jQuery - using jQuery's .get() method.
        var areaChartCanvas = $('#areaChartOrders').get(0).getContext('2d');
        // This will get the first returned node in the jQuery collection.
        var areaChart       = new Chart(areaChartCanvas);

    <?php

            $array = array();
            foreach ($result as $key => $v){
                $array[] = $key;
            }
        ?>

        var label_list = <?=json_encode($array,JSON_OBJECT_AS_ARRAY)?>;

        // visits.reverse();
        var areaChartData = {
            labels  : label_list,
            datasets: [
                {
                    label               : 'xxxxx',
                    fillColor           : '#ff7701',
                    strokeColor         : '#ff7701',
                    pointColor          : '#ff7701',
                    pointStrokeColor    : '#ff7701',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: '#fff',
                    data                : <?=json_encode(($result))?>
                },
            ]
        };

        var areaChartOptions = {
            // Boolean - If we should show the scale at all
            showScale               : true,
            // Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines      : true,
            // String - Colour of the grid lines
            scaleGridLineColor      : 'rgba(0,0,0,.05)',
            // Number - Width of the grid lines
            scaleGridLineWidth      : 1,
            // Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            // Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines  : true,
            // Boolean - Whether the line is curved between points
            bezierCurve             : true,
            // Number - Tension of the bezier curve between points
            bezierCurveTension      : 0.3,
            // Boolean - Whether to show a dot for each point
            pointDot                : true,
            // Number - Radius of each point dot in pixels
            pointDotRadius          : 2,
            // Number - Pixel width of point dot stroke
            pointDotStrokeWidth     : 2,
            // Number - amount extra to add to the radius to cater for hit detection outside the drawn point
            pointHitDetectionRadius : 20,
            // Boolean - Whether to show a stroke for datasets
            datasetStroke           : true,
            // Number - Pixel width of dataset stroke
            datasetStrokeWidth      : 1.5,
            // Boolean - Whether to fill the dataset with a color
            datasetFill             : false,
            // String - A legend template
            legendTemplate          : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<datasets.length; i++){%><li><span style=\'background-color:<%=datasets[i].lineColor%>\'></span><%=datasets[i].label%></li><%}%></ul>',
            // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            maintainAspectRatio     : true,
            // Boolean - whether to make the chart responsive to window resizing
            responsive              : true,
            animation: true
        };

        //Create the line chart
        areaChart.Line(areaChartData, areaChartOptions);

    })
</script>