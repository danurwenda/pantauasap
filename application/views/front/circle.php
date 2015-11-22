
<style>
    html, body, #map { margin: 0; padding: 0; height: 100%; }
    #map{height:90vh;}
</style>
<script
    src="https://maps.googleapis.com/maps/api/js?libraries=visualization">
</script>
<script>
    var map;

    function initialize() {
        var mapOptions = {
            zoom: 5,
            center: {lat: -3.162, lng: 113.730},
            mapTypeId: google.maps.MapTypeId.TERRAIN
        };

        map = new google.maps.Map(document.getElementById('map'),
                mapOptions);

        // Create a <script> tag and set the USGS URL as the source.
        var script = document.createElement('script');
        script.src = 'http://118.97.100.60/asap/index.php/Vizu/showLatestGeoJson';
        //script.src = 'http://yuns-macbook-pro.local/~yunhariadi/wisdome/resources/tmp/test.json';
        //script.src = 'http://yuns-macbook-pro.local/~yunhariadi/wisdome/resources/tmp/earthquake.json';
        document.getElementsByTagName('head')[0].appendChild(script);

        map.data.setStyle(function (feature) {
            var iaq = feature.getProperty('iaq');
            return {
                icon: getCircle(iaq)

            };
        });
    }

    function getCircle(iaq) {
        var colortemp = 'black';
        if (iaq > 100) {
            colortemp = 'red';
        } else if (iaq > 50) {
            colortemp = 'yellow';
        } else {
            colortemp = 'green';
        }
        var circle = {
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: colortemp,
            fillOpacity: .5,
            scale: Math.abs(iaq) / 2,
            strokeColor: 'red',
            strokeWeight: .5
        };
        return circle;
    }

    function eqfeed_callback(results) {
        map.data.addGeoJson(results);
    }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>

<div id="map"></div>