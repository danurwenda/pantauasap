
<!DOCTYPE html>
<html>
  <head>
    <style>
      html, body, #map { margin: 0; padding: 0; height: 100%; }
    </style>
    <script
      src="https://maps.googleapis.com/maps/api/js?libraries=visualization">
    </script>
    <script>
      var map;

      function initialize() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 5,
          center: new google.maps.LatLng(-3.162,113.730),
          mapTypeId: google.maps.MapTypeId.TERRAIN
        });

        var script = document.createElement('script');
        script.src = 'http://118.97.100.60/asap/index.php/Vizu/showLatestGeoJson';
       // script.src = 'http://yuns-macbook-pro.local/~yunhariadi/wisdome/index.php/Vizu/showLatestGeoJson';
        document.getElementsByTagName('head')[0].appendChild(script);
      }
  
        function getCircle(iaq) {
            var colortemp = 'black';
            if(iaq>150){
                colortemp = 'black';
            }else if(iaq>100){
                colortemp = 'red';
            }else if(iaq>50){
                colortemp = 'yellow';
            }else{
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
        
        function createMarker(latLng, html,data) {
            var newmarker = new google.maps.Marker({
                position: latLng,
                map: map,
                icon: getCircle(data),
                title: html
            });

            newmarker['infowindow'] = new google.maps.InfoWindow({
                    content: html
                });

            google.maps.event.addListener(newmarker, 'click', function() {
                this['infowindow'].open(map, this);
            });
        
        }
      
      window.eqfeed_callback = function(results) {
        for (var i = 0; i < results.features.length; i++) {
          var coords = results.features[i].geometry.coordinates;
          var latLng = new google.maps.LatLng(coords[1],coords[0]);
          var diaq = results.features[i].properties.iaq;
          var iddevice = results.features[i].properties.id;
          var contentString = '<a href="http://118.97.100.60/asap/index.php/chart/'+iddevice+'" target="_blank">Device#'+iddevice+'</a></br> \n\
                                iAQ : '+results.features[i].properties.iaq+' pm10 :'+results.features[i].properties.pm10+'</br>Recorded :</br>'+results.features[i].properties.recorded ;
            createMarker(latLng, contentString,diaq);
        }
      }
      google.maps.event.addDomListener(window, 'load', initialize)
    </script>
  </head>
  <body>
    <div id="map"></div>
  </body>
</html>
