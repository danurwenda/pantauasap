<!-- start viz -->    
<style>
    html, body, #map { margin: 0; padding: 0; height: 100%; }
    #map{height:90vh;}
    .ace-settings-box.open{overflow: visible;}
    .infobox-wrapper {
        display:none;
    }
    #infobox-main {
        border:2px solid #CCCCCC;
        background:rgb(40,64,78);
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
    #infobox-main{
        max-width: 400px;
    }
    #infobox-main .infobox{
        width:120px;
        background-color: #efc67a;
    }
    #infobox-main .infobox-data{
        min-width: 0;
    }
    .percentage > .infobox-data-number{
        font-size:22px;
    }
</style>
<div class="infobox-wrapper">
    <div id="infobox-main">
        <div class="row" style="margin-bottom:10px">
            <div class="col-xs-4 center" style="margin-top:30px">
                <span style="font-size: 21px" id="main-title"></span>
            </div>
            <div class="col-xs-4 center">
                <div class="easy-pie-chart percentage" data-percent="100" id="main-pie">
                    <span class="percent infobox-data-number" id="main-value"></span>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="row" id="main-remark"></div>
                <div class="row bottom" id="device-source">
                    <a href="#">
                    <button class="btn btn-xs btn-info btn-round">
                        <i class="ace-icon fa fa-signal bigger-110"></i>
                        Device #<span id="device-id"></span>
                    </button>
                    </a>
                </div>
            </div>
        </div>
        <div class="infobox-container" id="infobox2">
            <!-- #section:pages/dashboard.infobox -->
            <div class="infobox">
                <div class="infobox-data">
                    <span class="infobox-data-number" id="value"></span>
                    <div class="infobox-content">CO<sub>2</sub></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script
    src="https://maps.googleapis.com/maps/api/js?libraries=visualization">
</script>
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/src/infobox.js"></script>
<?php
echo css_asset('bootstrap-datetimepicker.css', 'ace');
echo js_asset('date-time/moment.js', 'ace');
echo js_asset('date-time/bs3-dtp4.js', 'ace');
echo js_asset('jquery.easypiechart.js', 'ace');
echo js_asset('mapsettings.js', 'deputi3');
?>

<script>
        var map;
        // array of reference of all markers
        var markers = [];
        function initialize() {
            var cookieZoom = ace.cookie.get('latest-zoom')
                    , cookieCenterLat = ace.cookie.get('latest-center-lat')
                    , cookieCenterLon = ace.cookie.get('latest-center-lon');

            map = new google.maps.Map(document.getElementById('map'), {
                zoom:
                        cookieZoom != undefined ? parseFloat(cookieZoom) :
                        5,
                center:
                        (cookieCenterLat != undefined && cookieCenterLon != undefined) ? new google.maps.LatLng(parseFloat(cookieCenterLat), parseFloat(cookieCenterLon)) :
                        new google.maps.LatLng(-2.54, 117.24),
                mapTypeId: google.maps.MapTypeId.TERRAIN
            });

            map.addListener('zoom_changed', function () {
                ace.cookie.set('latest-zoom', map.getZoom(), 604800)
            });
            map.addListener('center_changed', function () {
                ace.cookie.set('latest-center-lat', map.getCenter().lat(), 604800)
                ace.cookie.set('latest-center-lon', map.getCenter().lng(), 604800)
            });

            loadLastPoints();
        }

        function loadLastPoints() {
            var url = base_url + 'index.php/device/get_last_point';
            $.getJSON(url, function (points) {
                $.each(points, function (i, r) {
                    addMarker(r)
                })
            })
        }
        function getCircle(point) {
            var iaq = parseInt(point.iaq);
            var colortemp = getColor(0, point);
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
        function addMarker(point) {
            //create marker
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(point.lat, point.lon),
                map: map,
                icon: getCircle(point),
                title: getTitle(point)
            });

            //create infobox
            var ib = new InfoBox({
                content: createInfoboxMain(point),
                alignBottom: true, //infobox di utaranya marker
                pixelOffset: new google.maps.Size(-200, 0),
                maxWidth: 600
            });

            google.maps.event.addListener(ib, 'domready', function () {
                renderPie()
            })

            marker.infobox = ib;

            //add event handler
            google.maps.event.addListener(marker, 'click', function () {
                this.infobox.open(map, this);
//                renderPie();
                // reference clicked marker
                var curMarker = this;
                // close all other markers
                // loop through all markers
                $.each(markers, function (i, marker) {
                    // if marker is not the clicked marker, close the marker
                    if (marker !== curMarker) {
                        marker.infobox.close();
                    }
                });
                map.panTo(marker.getPosition());
            });
            //done all, add marker to array
            markers.push(marker);
        }

        function createInfoboxMain(point) {
            var paramnum = 0,
                    $ele = $(document.getElementById('infobox-main').cloneNode(true)),
                    chartUrl=base_url+'index.php/chart/' + point.sensor_id;
            // header
            $ele.find('#main-title').html(getTitle(paramnum));
            $ele.find('#main-value').html(getParamValue(paramnum, point));
            $ele.find('#main-pie').data('color', getColor(paramnum, point));
            $ele.find('#device-source a').attr('href',chartUrl).attr('target','_blank')
            .find('#device-id').html(point.sensor_id);
            $ele.find('#main-remark').html(moment(point.recorded_timestamp).format('D MMM YYYY, hh:mm:ss'));
            // another param
            var infobox = $ele.find('.infobox').detach();
            for (var i = 0; i < params.length; i++) {
                if (i != paramnum) {
                    //add to infobox-container
                    var clone = infobox.clone();
                    clone.find('.infobox-content').html(getTitle(i));
                    clone.find('.infobox-data-number').html(getParamValue(i, point));
                    //insert
                    $ele.find('.infobox-container').append(clone);
                }
            }
            return $ele.get(0);
        }

        google.maps.event.addDomListener(window, 'load', initialize)
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
                    $.getJSON(base_url + 'index.php/device/get_average/' + from + '/' + to, function (data) {
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

    <div class="ace-settings-box clearfix" id="ace-settings-box">
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
