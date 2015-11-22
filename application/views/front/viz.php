<!-- start viz -->    
<style>
    html, body, #map { margin: 0; padding: 0; height: 100%; }
    #map{height:90vh;}
</style>
<?php
echo css_asset('bootstrap-datetimepicker.css', 'ace');
echo js_asset('date-time/moment.js', 'ace');
echo js_asset('date-time/bs3-dtp4.js', 'ace');
?>
<script
    src="https://maps.googleapis.com/maps/api/js?libraries=visualization">
</script>
<script>
    var map;

    function initialize() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: new google.maps.LatLng(-3.162, 113.730),
            mapTypeId: google.maps.MapTypeId.TERRAIN
        });

        var script = document.createElement('script');
        script.src = 'http://118.97.100.60/asap/index.php/Vizu/showLatestGeoJson';
        //script.src = 'http://yuns-macbook-pro.local/~yunhariadi/wisdome/index.php/Vizu/showLatestGeoJson';
        document.getElementsByTagName('head')[0].appendChild(script);
    }

    function getCircle(iaq) {
        var colortemp = 'black';
        if (iaq > 150) {
            colortemp = 'black';
        } else if (iaq > 100) {
            colortemp = 'red';
        } else if (iaq > 50) {
            colortemp = 'yellow';
        } else {
            colortemp = 'green';
        }
        var circle = {
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: colortemp,
            fillOpacity: .6,
            scale: Math.abs(iaq) / 4,
            strokeColor: 'white',
            strokeWeight: .5
        };
        return circle;
    }

    function createMarker(latLng, html, data) {
        var newmarker = new google.maps.Marker({
            position: latLng,
            map: map,
            icon: getCircle(data),
            title: html
        });

        newmarker['infowindow'] = new google.maps.InfoWindow({
            content: html
        });

        google.maps.event.addListener(newmarker, 'click', function () {
            this['infowindow'].open(map, this);
        });

    }

    window.eqfeed_callback = function (results) {
        for (var i = 0; i < results.features.length; i++) {
            var coords = results.features[i].geometry.coordinates;
            var latLng = new google.maps.LatLng(coords[1], coords[0]);
            var diaq = results.features[i].properties.iaq;
            var iddevice = results.features[i].properties.id;
            var contentString = '<a href="http://118.97.100.60/asap/index.php/chart/' + iddevice + '" target="_blank">Device#' + iddevice + '</a></br> \n\
                                iAQ : ' + results.features[i].properties.iaq + ' pm10 :' + results.features[i].properties.pm10 + '</br>Recorded :</br>' + results.features[i].properties.recorded + '</br><a href="index.php/Peta/history/' + iddevice + '" >History#' + iddevice + '</a></br> ';
            createMarker(latLng, contentString, diaq);
        }
    }
    google.maps.event.addDomListener(window, 'load', initialize)
    jQuery(function ($) {
        $('#date-timepicker-from').datetimepicker({
            defaultDate:Date.now()-7000*24*3600
        }).next().on(ace.click_event, function () {
            $(this).prev().focus();
        });
        $('#date-timepicker-to').datetimepicker({
            defaultDate:Date.now()
        }).next().on(ace.click_event, function () {
            $(this).prev().focus();
        });
        $('#date-timepicker-from').on("dp.change", function (e) {
            $('#date-timepicker-to').data("DateTimePicker").minDate(e.date);
            updateAverage();
        });
        $("#date-timepicker-to").on("dp.change", function (e) {
            $('#date-timepicker-from').data("DateTimePicker").maxDate(e.date);
            updateAverage();
        });
        updateAverage();

        function updateAverage() {
            var from = $('#date-timepicker-from').data('DateTimePicker').date();
            var to = $('#date-timepicker-to').data('DateTimePicker').date();
            if (from != null && to != null) {
                $.getJSON('http://118.97.100.60/asap/index.php/device/get_average/' + from + '/' + to, function (data) {
                    var $panel = $('#average-panel');
                    $panel.find('#num').html(data.num);
                    if (data.num > 0) {
                        $panel.find('#temp').html(data.temp);
                        $panel.find('#hum').html(data.hum);
                        $panel.find('#pm10').html(data.pm10);
                        $panel.find('#pm25').html(data.pm25);
                        $panel.find('#iaq').html(data.iaq);
                        $panel.find('#tvoc').html(data.tvoc);
                        $panel.find('#co2').html(data.co2);
                    } else {
                        $panel.find('#temp').html('-');
                        $panel.find('#hum').html('-');
                        $panel.find('#pm10').html('-');
                        $panel.find('#pm25').html('-');
                        $panel.find('#iaq').html('-');
                        $panel.find('#tvoc').html('-');
                        $panel.find('#co2').html('-');
                    }
                });
            }
        }
    });
</script>
<!-- #section:settings.box -->
<div class="ace-settings-container" id="ace-settings-container">
    <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
        <i class="ace-icon fa fa-cog bigger-130"></i>
    </div>

    <div class="ace-settings-box clearfix" id="ace-settings-box" style="overflow: visible;">
        <div class="widget-box">
            <div class="widget-header">
                <h4 class="widget-title">Average measurement</h4>
            </div>

            <div class="widget-body">
                <div class="widget-main" id="average-panel"> 

                    <label for="date-timepicker-from">From</label>

                    <!-- #section:plugins/date-time.datetimepicker -->
                    <div class="input-group">
                        <input id="date-timepicker-from" type="text" class="form-control">
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o bigger-110"></i>
                        </span>
                    </div>

                    <label for="date-timepicker-to">To</label>
                    <!-- #section:plugins/date-time.datetimepicker -->
                    <div class="input-group">
                        <input id="date-timepicker-to" type="text" class="form-control">
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o bigger-110"></i>
                        </span>
                    </div>
                    <!-- /section:plugins/date-time.datetimepicker -->
                    <div class="width-100 input-group">
                        <div class="pull-left width-50">
                            <div># sampling</div>
                            <div>Avg IAQ</div>
                            <div>Avg TVOC</div>
                            <div>Avg CO2</div>
                            <div>Avg PM2.5</div>
                            <div>Avg PM10</div>
                            <div>Avg temp</div>
                            <div>Avg humidity</div>
                        </div><!-- /.pull-left -->

                        <div class="pull-left width-50">
                            <div><span id="num"></span></div>
                            <div><span id="iaq"></span></div>
                            <div><span id="tvoc"></span></div>
                            <div><span id="co2"></span></div>
                            <div><span id="pm25"></span></div>
                            <div><span id="pm10"></span></div>
                            <div><span id="temp"></span></div>
                            <div><span id="hum"></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.ace-settings-box -->
</div><!-- /.ace-settings-container -->

<!-- /section:settings.box -->
<div id="map"></div>
