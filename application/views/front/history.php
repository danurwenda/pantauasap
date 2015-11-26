<?php
echo css_asset('bootstrap-datetimepicker.css', 'ace');
echo js_asset('date-time/moment.js', 'ace');
echo js_asset('date-time/bs3-dtp4.js', 'ace');
echo js_asset('jquery.easypiechart.js', 'ace');
echo js_asset('jsclusterer/src/markerclusterer_compiled.js', 'deputi3');
?>
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
echo js_asset('mapsettings.js', 'deputi3');
echo js_asset('history.js', 'deputi3');
?>
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
                        <select class="form-control" id="sensorparam">
                            <option value="0">IAQ</option>
                            <option value="1">TVOC</option>
                            <option value="2">CO<sub>2</sub></option>
                            <option value="3">PM2.5</option>
                            <option value="4">PM10</option>
                            <option value="5">Temperature</option>
                            <option value="6">Humidity</option>
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
            <div class="widget-box transparent">
                <div class="widget-header widget-header-flat">
                    <h4 class="widget-title lighter">
                        <!--<i class="ace-icon fa fa-star orange"></i>-->
                        Legend
                    </h4>

                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <style>
                    .legend {
                        margin: 0;
                        margin-top:20px;
                        margin-bottom: 20px;
                        padding: 20px 0 0;
                        clear: both;
                        font-size: 12px;
                        font-weight: normal;
                        color: #000;
                        height: 20px;
                    }
                    .legend li {
                        display: block;
                        float: left;
                        position: relative;
                        height: 20px;
                    }
                    .legend li span {
                        white-space: nowrap;
                        color: #858585;
                    }
                    .legend li .start {
                        position: absolute;
                        top: -40px;
                        left: -10px;
                    }
                    .legend .odd .start {
                        bottom: 0px;
                        top: auto;
                    }
                    .legend .lowest .start {
                        left: 0;
                    }
                    .legend .highest .end {
                        position:absolute;
                        right: 0;
                    }
                    .legend .odd .end{
                        top:-40px;
                    }
                </style>
                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <ul class="legend">
                        </ul>
                    </div><!-- /.widget-main -->
                </div><!-- /.widget-body -->
            </div>
        </div>
    </div><!-- /.ace-settings-box -->
</div>

<div id="map"></div>
