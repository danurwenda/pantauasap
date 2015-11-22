<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html>
<head>
<title>AsapPantau</title>
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="http://openlayers.org/en/v3.10.0/css/ol.css" type="text/css">
<script src="http://openlayers.org/en/v3.10.0/build/ol.js"></script>

</head>
<body>
<div class="container-fluid">

<div class="row-fluid">
  <div class="span12">
    <div id="map" class="map"><div id="popup"></div></div>
  </div>
</div>

</div>
<script>
    
$.getJSON('http://118.97.100.60/asap/index.php/vizu/showLatestID/1', function (data) {
    // Create the chart

function iconChange(data){
        var chtml=' ';
        if(data>100){
            chtml = 'http://118.97.100.60/asap/resources/v3.10.0/examples/data/circle_red.png';
        }else{
            chtml = 'http://118.97.100.60/asap/resources/v3.10.0/examples/data/circle_green.png';
        }
        return chtml;
    };
    console.log( data.co2);
    var iconFeature = new ol.Feature({
      geometry: new ol.geom.Point(ol.proj.transform([data.locX,data.locY], 'EPSG:4326', 'EPSG:3857')),
      sensorid: data.sensorid,
      iaq:data.iaq,
      co2:data.co2,
      pm10:data.pm10,
      recorded:data.recorded
    });

    var iconStyle = new ol.style.Style({
      image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
        anchor: [0.5, 46],
        //anchor: [],
        anchorXUnits: 'fraction',
        anchorYUnits: 'pixels',
        opacity: 0.75,
        src: iconChange(data.iaq)
      }))
    });

    iconFeature.setStyle(iconStyle);

    var vectorSource = new ol.source.Vector({
      features: [iconFeature]
    });

    var vectorLayer = new ol.layer.Vector({
      source: vectorSource
    });

    var rasterLayer = new ol.layer.Tile({
      source: new ol.source.BingMaps({
key: 'Ak-dzM4wZjSqTlzveKz5u0d4IQ4bRzVI309GxmkgSVr1ewS6iPSrOvOKhA-CJlm3',
      imagerySet: 'Aerial'
       })
    });

    var map = new ol.Map({
      layers: [rasterLayer, vectorLayer],
      controls: ol.control.defaults({
        attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
          collapsible: false
        })
      }),
      target: 'map',
      view: new ol.View({
        center: ol.proj.fromLonLat([113.73055555555555, -3.1625]),
        //center: ol.proj.fromLonLat([0,0]),
        //center : [0,0],
        zoom: 5
      })
    });

    var element = document.getElementById('popup');
    var popup = new ol.Overlay({
      element: element,
      positioning: 'bottom-center',
      stopEvent: false
    });
    map.addOverlay(popup);

    // display popup on click
    map.on('click', function(evt) {
      var feature = map.forEachFeatureAtPixel(evt.pixel,
          function(feature, layer) {
            return feature;
          });
      if (feature) {
        popup.setPosition(evt.coordinate);
        $(element).popover({
          'placement': 'top',
          'html': true,
          'content': '<a href="http://118.97.100.60/asap/index.php/demo/chart/1" target="_blank">AirMentor</a>' +feature.get('sensorid')+'  IAQ:' +feature.get('iaq')+' CO2:'+feature.get('co2')+' pm10:'+feature.get('pm10')+' record:'+feature.get('recorded')
        });
        $(element).popover('show');
      } else {
        $(element).popover('destroy');
      }
    });


});
    



</script>
</body>
</html>

