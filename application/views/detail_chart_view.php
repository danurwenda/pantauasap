<?php
echo js_asset('highcharts/highstock.js', 'deputi3');
echo js_asset('highcharts/exporting.js', 'deputi3');
echo js_asset('highcharts/highcharts.indo.js', 'deputi3');
?>
<script type="text/javascript">

    $(function () {
    var data = [
<?php echo $detailchart['dataJS']; ?>
    ],
            title = '<?php echo $indikator->indikator_name; ?>',
            unit = '<?php echo $indikator->unit; ?>',
            formatter = <?php echo $detailchart['formatterJS']; ?>;
            $('#detail-chart').highcharts('StockChart', {
    rangeSelector: {
    selected: 1
    },
            title: {
            text: title
            },
            xAxis: {
            labels: {
            style: {
            fontSize: '16px'
            }
            }
            },
            yAxis: {
            labels: {
            style: {
            fontWeight: 'bold',
                    fontSize: '20px'
            }
            },
                    title: {
                    text: unit
                    }
            },
            exporting: {
            buttons: {
            eventButton: {
            _titleKey: 'eventButtonTitle', onclick: function () {
            var eventSeries = this.series[1];
                    if (eventSeries.visible) {
            eventSeries.hide();
            } else {
            eventSeries.show();
            }
            },
                    symbol: 'url(<?php echo image_asset_url('flaghc.png', 'deputi3') ?>)'
            },
                    contextButton: {
                    enabled: false
                    }
            }
            },
            credits: {
            enabled: false
            },
            tooltip: {shared: false},
            plotOptions: {
            line: {
            marker: {
            enabled: true,
                    symbol: 'circle',
                    radius: 4,
                    states: {
                    hover: {
                    enabled: true
                    }
                    }
            }
            }
            },
            series: [{
            color:'#0000FF',
                    name: title,
                    data: data,
                    id: 'dataseries',
                    tooltip: {
                    headerFormat: '',
                            pointFormatter: formatter
                    }
<?php if (isset($threshold)) { ?>
                , zones: [
                {
                value: <?php echo $threshold->forecast; ?>,
                        color: '#0000FF'
                }, {
                value: <?php echo json_decode($threshold->threshold_vals); ?>,
                        color: '#FBDD17'
                }, {
                color: '#FF0000'//most severe color
                }
                ],
<?php } ?>
            }, {
            //by default it's hidden
            visible: false,
                    // the event marker flags
                    y: - 50,
                    type: 'flags',
                    name: 'Events',
                    data: [
<?php foreach ($events as $e) { ?>
                        {
                        x: <?php echo sqlDateToUTC($e->event_date); ?>,
                                title: '<?php echo $e->title; ?>',
                                text: '<?php echo $e->description; ?>'
                        },
<?php } ?>
                    ],
                    onSeries: 'dataseries'
            }]
    });
    });
</script>    
<div id="detail-chart" style="margin: 0 auto;min-height: 540px"></div>