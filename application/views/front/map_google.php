
<link rel="stylesheet" href="http://openlayers.org/en/master/css/ol.css" type="text/css">
<style type="text/css">
    body {
        width: 100%;
        height: 500px;
        position: relative;
    }
    #map {
        width: 100%;
        height: 400px;
    }
    div.fill {
        width: 100%;
        height: 100%;
    }
</style>
<script src="http://maps.google.com/maps/api/js?v=3&amp;sensor=false"></script>
<!--<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>-->

<div id="map" class="map">
    <div id="gmap" class="fill"></div>
    <div id="olmap" class="fill"></div><div id="popup"></div>
</div>
<script src="http://openlayers.org/en/master/build/ol.js" type="text/javascript"></script>
<script type="text/javascript">
    $.getJSON('http://118.97.100.60/asap/index.php/vizu/showLatestID/1', function (data) {
        var gmap = new google.maps.Map(document.getElementById('gmap'), {
            disableDefaultUI: true,
            keyboardShortcuts: false,
            draggable: false,
            disableDoubleClickZoom: true,
            scrollwheel: false,
            streetViewControl: false,
            mapTypeId: google.maps.MapTypeId.SATELLITE
        });

        var view = new ol.View({
            // make sure the view doesn't go beyond the 22 zoom levels of Google Maps
            maxZoom: 21
        });
        view.on('change:center', function () {
            var center = ol.proj.transform(view.getCenter(), 'EPSG:3857', 'EPSG:4326');
            gmap.setCenter(new google.maps.LatLng(center[1], center[0]));
        });
        view.on('change:resolution', function () {
            gmap.setZoom(view.getZoom());
        });

        function iconChange(data) {
            var chtml = ' ';
            if (data > 100) {
                chtml = 'http://118.97.100.60/asap/resources/v3.10.0/examples/data/circle_red.png';
            } else {
                chtml = 'http://118.97.100.60/asap/resources/v3.10.0/examples/data/circle_green.png';
            }
            return chtml;
        }
        ;

        var iconFeature = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.transform([data.locY, data.locX], 'EPSG:4326', 'EPSG:3857')),
            sensorid: data.sensorid,
            iaq: data.iaq,
            co2: data.co2,
            pm10: data.pm10
        });



        var iconStyle = new ol.style.Style({
            image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                anchor: [0.5, 46],
                //anchor: [],
                anchorXUnits: 'fraction',
                anchorYUnits: 'pixels',
                opacity: 0.75,
                //src: '../resources/v3.10.0/examples/data/icon.png'
                src: iconChange(data.iaq)
            }))
        });

        iconFeature.setStyle(iconStyle);

        var vectorSource = new ol.source.Vector({
            features: [iconFeature]
        });

        var vector = new ol.layer.Vector({
            source: vectorSource
        });

        var olMapDiv = document.getElementById('olmap');
        var map = new ol.Map({
            layers: [vector],
            interactions: ol.interaction.defaults({
                altShiftDragRotate: false,
                dragPan: false,
                rotate: false
            }).extend([new ol.interaction.DragPan({kinetic: null})]),
            target: olMapDiv,
            view: view
        });
        view.setCenter([0, 0]);
        view.setZoom(2);

        var element = document.getElementById('popup');
        var popup = new ol.Overlay({
            element: element,
            positioning: 'bottom-center',
            stopEvent: false
        });
        map.addOverlay(popup);

//     map.on('click', function(evt) {
//      var feature = map.forEachFeatureAtPixel(evt.pixel,
//          function(feature, layer) {
//            return feature;
//          });
//      if (feature) {
//        popup.setPosition(evt.coordinate);
//        $(element).popover({
//          'placement': 'top',
//          'html': true,
//          'content': 'sensorID:' +feature.get('sensorid')+'  IAQ:' +feature.get('iaq')+' CO2:'+feature.get('co2')+' pm10:'+feature.get('pm10')
//        });
//        $(element).popover('show');
//      } else {
//        $(element).popover('destroy');
//      }
//    });

        olMapDiv.parentNode.removeChild(olMapDiv);
        gmap.controls[google.maps.ControlPosition.TOP_LEFT].push(olMapDiv);
    });


</script>
