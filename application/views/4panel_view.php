<html>
    <head>
        <title>Real time data</title>
        <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    </head>
    <body>
        <div class="row">
            <div class="col-sm-6 chart-col">
                <div class="top-left chart" id="TL"></div>
                <div class="bottom-left chart" id="BL"></div>
            </div>
            <div class="col-sm-6 chart-col">
                <div class="top-right chart" id="TR"></div>
                <div class="bottom-right chart" id="BR"></div>
            </div>
        </div>
        <?php
        echo js_asset('highcharts/highcharts.js', 'deputi3');
        echo js_asset('highcharts/exporting.js', 'deputi3');
        echo js_asset('highcharts/highcharts.indo.js', 'deputi3');
        ?>
        <script type="text/javascript">
            var chart; // global
            /**
             * Request data from the server, add it to the graph and set a timeout 
             * to request again
             */
            function requestData() {
                $.ajax({
                    url: '<?php echo site_url('device/get_data/1'); ?>',
                    success: function (point) {
                        var series = chart.series[0],
                                shift = series.data.length > 20; // shift if the series is 
                        // longer than 20

                        // add the point
                        chart.series[0].addPoint(point, true, shift);

                        // call it again after one second
                        setTimeout(requestData, 1000);
                    },
                    cache: false
                });
            }
            $(document).ready(function () {
                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'TL',
                        defaultSeriesType: 'spline',
                        events: {
                            load: requestData
                        }
                    },
                    title: {
                        text: 'Live random data'
                    },
                    xAxis: {
                        type: 'datetime',
                        tickPixelInterval: 150,
                        maxZoom: 20 * 1000
                    },
                    yAxis: {
                        minPadding: 0.2,
                        maxPadding: 0.2,
                        title: {
                            text: 'Value',
                            margin: 80
                        }
                    },
                    series: [{
                            name: 'Random data',
                            data: []
                        }]
                });
            });
        </script>
        <?php ?>
    </body>
</html>