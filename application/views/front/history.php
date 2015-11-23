<?php
echo css_asset('bootstrap-datetimepicker.css', 'ace');
echo js_asset('date-time/moment.js', 'ace');
echo js_asset('date-time/bs3-dtp4.js', 'ace');
?>
<style>
    html, body, #map { margin: 0; padding: 0; height: 100%; }
    #map{height:90vh;}
    .ace-settings-box.open{overflow: visible;}
    .infobox-wrapper {
        display:none;
    }
    #infobox {
        border:2px solid black;
        margin-top: 8px;
        background:#333;
        color:#FFF;
        font-family:Arial, Helvetica, sans-serif;
        font-size:12px;
        padding: .5em 1em;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        text-shadow:0 -1px #000000;
        -webkit-box-shadow: 0 0  8px #000;
        box-shadow: 0 0 8px #000;
    }
</style>
<script
    src="https://maps.googleapis.com/maps/api/js?libraries=visualization">
</script>
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/src/infobox.js"></script>
<script>
        var map;

        function initialize() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 5,
                center: new google.maps.LatLng(-3.162, 113.730),
                mapTypeId: google.maps.MapTypeId.TERRAIN
            });
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
            for (var i = 0; i < markers.length; i++) {
                markers[i].infobox.close();               
            }
        }
        // Deletes all markers in the array by removing references to them.   
        function deleteMarkers() {
            clearMarkers();
            markers = [];
        }


        function createMarker(latLng, html, data) {
            var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                icon: getCircle(data),
                title: 'cupu'
            });

            markers.push(marker);

            marker['infobox'] = new InfoBox({
                content: document.getElementById("infobox"),
                disableAutoPan: false,
                maxWidth: 150,
                pixelOffset: new google.maps.Size(-140, 0),
                zIndex: null,
                boxStyle: {
                    background: "url('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/examples/tipbox.gif') no-repeat",
                    opacity: 0.75,
                    width: "280px"
                },
                closeBoxMargin: "12px 4px 2px 2px",
                closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
                infoBoxClearance: new google.maps.Size(1, 1)
            });

            google.maps.event.addListener(marker, 'click', function () {
                this['infobox'].open(map, this);

                // reference clicked marker
                var curMarker = this;
                // loop through all markers
                $.each(markers, function (index, marker) {
                    // if marker is not the clicked marker, close the marker
                    if (marker !== curMarker) {
                        marker.infobox.close();
                    }
                });
            });

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
            $('#params').change(function () {
                updateCookies();
                updatePoints();
            });
            $('#device').change(function () {
                updateCookies();
                updatePoints();
            });
            $('#date-timepicker-from').on("dp.change", function (e) {
                $('#date-timepicker-to').data("DateTimePicker").minDate(e.date);
                updateCookies();
                updatePoints();
            });
            $("#date-timepicker-to").on("dp.change", function (e) {
                $('#date-timepicker-from').data("DateTimePicker").maxDate(e.date);
                updateCookies();
                updatePoints();
            });

            function updateCookies() {

            }

            function updatePoints() {
                deleteMarkers();
                var device = $('#device').val();
                var from = $('#date-timepicker-from').data('DateTimePicker').date();
                var to = $('#date-timepicker-to').data('DateTimePicker').date();
                if (from != null && to != null) {
                    var url = base_url + 'index.php/device/get_points/' + device + '/' + from + '/' + to;
                    $.getJSON(url, function (points) {
                        $.each(points, function (i, v) {
                            var latLng = new google.maps.LatLng(v.lat, v.lon);
                            var contentString = '<a href="http://118.97.100.60/asap/index.php/chart/'
                                    + v.sensor_id + '" target="_blank">Device#'
                                    + v.sensor_id + '</a><br/>iAQ : ' + v.iaq + '<br/>pm10 :' + v.pm10 + '<br/>Recorded :<br/>' + v.recorded_timestamp;
                            createMarker(latLng, contentString, v.iaq);
                        });
                    });
                }
            }
        });

</script>
<!-- #section:settings.box -->
<div class="ace-settings-container" id="ace-settings-container">
    <div class="btn btn-app btn-xs btn-warning ace-settings-btn open" id="ace-settings-btn">
        <i class="ace-icon fa fa-cog bigger-130"></i>
    </div>

    <div class="ace-settings-box clearfix open" id="ace-settings-box">
        <div class="row" style="width:312px;">
            <div class="widget-box transparent">
                <div class="widget-header widget-header-flat">
                    <h4 class="widget-title lighter">
                        <!--<i class="ace-icon fa fa-star orange"></i>-->
                        Parameter
                    </h4>

                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <select class="form-control" id="params">
                            <option value="iaq">IAQ</option>
                            <option value="tvoc">TVOC</option>
                            <option value="co2">CO<sub>2</sub></option>
                            <option value="pm25">PM2.5</option>
                            <option value="pm10">PM10</option>
                            <option value="temp">Temperature</option>
                            <option value="hum">Humidity</option>                                                    
                        </select>
                    </div><!-- /.widget-main -->
                </div><!-- /.widget-body -->
            </div>
            <div class="widget-box transparent">
                <div class="widget-header widget-header-flat">
                    <h4 class="widget-title lighter">
                        <!--<i class="ace-icon fa fa-star orange"></i>-->
                        Device Source
                    </h4>

                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <select class="form-control" id="device">
                            <option value="0">All</option>
                            <?php
                            foreach ($devices as $d) {
                                $did = $d->sensor_id;
                                ?>
                                <option value="<?php echo $did; ?>"><?php echo $did; ?></option>
                                <?php
                            }
                            ?>                          
                        </select>
                    </div><!-- /.widget-main -->
                </div><!-- /.widget-body -->
            </div>
            <div class="widget-box transparent">
                <div class="widget-header widget-header-flat">
                    <h4 class="widget-title lighter">
                        <!--<i class="ace-icon fa fa-star orange"></i>-->
                        Time Range
                    </h4>

                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <label for="date-timepicker-from">From</label>
                        <div class="input-group">
                            <input id="date-timepicker-from" type="text" class="form-control">
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o bigger-110"></i>
                            </span>
                        </div>

                        <label for="date-timepicker-to">To</label>
                        <div class="input-group">
                            <input id="date-timepicker-to" type="text" class="form-control">
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o bigger-110"></i>
                            </span>
                        </div>
                    </div><!-- /.widget-main -->
                </div><!-- /.widget-body -->
            </div>
        </div>
    </div><!-- /.ace-settings-box -->
</div>
<div class="infobox-wrapper">
    <div id="infobox">
        The contents of your info box. It's very easy to create and customize.
    </div>
</div>
<div id="map"></div>
