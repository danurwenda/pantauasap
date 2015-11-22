<?php
echo css_asset('bootstrap-datetimepicker.css', 'ace');
echo js_asset('date-time/moment.js', 'ace');
echo js_asset('date-time/bs3-dtp4.js', 'ace');
?>
<style>
    html, body, #map { margin: 0; padding: 0; height: 100%; }
    #map{height:90vh;}
</style>
<script
    src="https://maps.googleapis.com/maps/api/js?libraries=visualization">
</script>
<script>
    var id = <?php echo $id; ?>,
            map;

    function initialize() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: new google.maps.LatLng(-3.162, 113.730),
            mapTypeId: google.maps.MapTypeId.TERRAIN
        });

//        var script = document.createElement('script');
//        script.src = 'http://118.97.100.60/asap/index.php/Vizu/showHistoryID/' + id;
//        // script.src = 'http://yuns-macbook-pro.local/~yunhariadi/wisdome/index.php/Vizu/showHistoryID/'+id;
//        document.getElementsByTagName('head')[0].appendChild(script);

    }

    function getCircle(iaq) {
        iaq = parseInt(iaq);
        var colortemp = 'black';
        if (iaq > 200) {
            colortemp = '#800080';
        } else if (iaq > 150) {
            colortemp = '#FF4500';
        } else if (iaq > 100) {
            colortemp = '#FFA500';
        } else if (iaq > 50) {
            colortemp = '#FFFF00';
        } else {
            colortemp = '#9ACD32';
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
    var markers = [];
    // Sets the map on all markers in the array.
    function setMapOnAll(m) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(m);
        }
    }
    // Removes the markers from the map, but keeps them in the array.
    function clearMarkers() {
        setMapOnAll(null);
    }
    // Deletes all markers in the array by removing references to them.   
    function deleteMarkers() {
        clearMarkers();
        markers = [];
    }

    function createMarker(latLng, html, data) {
        var newmarker = new google.maps.Marker({
            position: latLng,
            map: map,
            icon: getCircle(data),
            title: html
        });

        markers.push(newmarker);

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
            //var iddevice = results.features[i].properties.id;
            var contentString = '<a href="http://118.97.100.60/asap/index.php/chart/' + id + '" target="_blank">Device#' + id + '</a></br> \n\
                            iAQ : ' + results.features[i].properties.iaq + ' pm10 :' + results.features[i].properties.pm10 + '</br>Recorded :</br>' + results.features[i].properties.recorded;
            createMarker(latLng, contentString, diaq);
        }
    }
    google.maps.event.addDomListener(window, 'load', initialize);
    jQuery(function ($) {
        $('#date-timepicker-from').datetimepicker({
            defaultDate: Date.now() - 7000 * 24 * 3600
        }).next().on(ace.click_event, function () {
            $(this).prev().focus();
        });
        $('#date-timepicker-to').datetimepicker({
            defaultDate: Date.now()
        }).next().on(ace.click_event, function () {
            $(this).prev().focus();
        });
        $('#device').change(function () {
            updatePoints();
        });
        $('#date-timepicker-from').on("dp.change", function (e) {
            $('#date-timepicker-to').data("DateTimePicker").minDate(e.date);
            updatePoints();
        });
        $("#date-timepicker-to").on("dp.change", function (e) {
            $('#date-timepicker-from').data("DateTimePicker").maxDate(e.date);
            updatePoints();
        });

        function updatePoints() {
            deleteMarkers();
            var device = $('#device').val();
            var from = $('#date-timepicker-from').data('DateTimePicker').date();
            var to = $('#date-timepicker-to').data('DateTimePicker').date();
            if (from != null && to != null) {
                $.getJSON('http://118.97.100.60/asap/index.php/device/get_points/' + device + '/' + from + '/' + to, function (points) {
                    $.each(points, function (i, v) {
                        var latLng = new google.maps.LatLng(v.lat,v.lon);
//                        console.log(latLng.lat());
                        var contentString = '<a href="http://118.97.100.60/asap/index.php/chart/'
                                + device + '" target="_blank">Device#'
                                + device + '</a><br/>iAQ : ' + v.iaq + '<br/>pm10 :' + v.pm10 + '<br/>Recorded :<br/>' + v.recorded_timestamp;
                        createMarker(latLng, contentString, v.iaq);
                    });
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
                <h4 class="widget-title">History</h4>
            </div>

            <div class="widget-body">
                <div class="widget-main" id="average-panel"> 
                    <label for="date-timepicker-to">Device</label>
                    <!-- #section:plugins/date-time.datetimepicker -->
                    <div class="input-group">
                        <select class="form-control" id="device">
                            <option value=""></option>
                            <option value="1">1</option>                            
                        </select>

                        <span class="input-group-addon">
                            <i class="fa fa-mobile bigger-110"></i>
                        </span>
                    </div>
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
                </div>
            </div>
        </div>
    </div><!-- /.ace-settings-box -->
</div><!-- /.ace-settings-container -->

<!-- /section:settings.box -->

<div id="map"></div>
