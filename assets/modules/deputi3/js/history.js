var map;

function initialize() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 5,
        center: new google.maps.LatLng(-3.162, 113.730),
        mapTypeId: google.maps.MapTypeId.TERRAIN
    });
}

function getTitle(point) {
    return 'IAQ : '+point.iaq;
}

function getCircle(point) {
    var iaq = parseInt(point.iaq);
    var colortemp = getColor('iaq', iaq);
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

// array of reference of all markers    
var markers = [];

// Sets the map on all markers in the array.
function setMapOnAll(m) {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(m);
    }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
    // loop through all markers, close its infobox
    $.each(markers, function (index, marker) {
        marker.infobox.close();
    });
    setMapOnAll(null);
}

// Deletes all markers in the array by removing references to them.   
function deleteMarkers() {
    clearMarkers();
    markers = [];
}

// Render pie
function renderPie() {
    $('.easy-pie-chart.percentage').each(function () {
        console.log('renderpie');
        $(this).easyPieChart({
            barColor: $(this).data('color'),
            trackColor: '#EEEEEE',
            scaleColor: false,
            lineCap: 'butt',
            lineWidth: 12,
            animate: false,
            size: 100
        }).css('color', $(this).data('color'));
    });
}
function rgbToHex(r, g, b) {
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
}
function getColor(param, val) {
    var r, g, b;
    if (param === 'iaq') {
        //0     -> 122,229,45
        //51    -> 248,252,12
        //101   -> 242,189,46
        //151   -> 211,33,33
        //>201   -> 121,5,175
        if (val < 51) {
            r = 122 + (val / 50) * (248 - 122);
            g = 229 + (val / 50) * (252 - 229);
            b = 45 + (val / 50) * (12 - 45);
        } else if (val < 101) {
            val = val - 50;
            r = 248 + (val / 50) * (242 - 248);
            g = 252 + (val / 50) * (189 - 252);
            b = 12 + (val / 50) * (46 - 12);
        } else if (val < 151) {
            val = val - 100;
            r = 242 + (val / 50) * (211 - 242);
            g = 189 + (val / 50) * (33 - 189);
            b = 46 + (val / 50) * (33 - 46);
        } else if (val < 201) {
            val = val - 150;
            r = 211 + (val / 50) * (121 - 211);
            g = 33 + (val / 50) * (5 - 33);
            b = 33 + (val / 50) * (175 - 33);
        } else {
            r = 121;
            g = 5;
            b = 175;
        }
    }
    return rgbToHex(Math.round(r), Math.round(g), Math.round(b));
}

function getRemark(param, val) {
    if (param === 'iaq') {
        if (val < 51)
            return 'Good';
        if (val < 101)
            return 'Moderate';
        if (val < 151)
            return 'Unhealthy for sensitive group';
        if (val < 201)
            return 'Unhealthy';
        return 'Very unhealthy';

    }
}

function createInfobox(point) {
    var $ele = $(document.getElementById('infobox-main').cloneNode(true));
    $ele.find('#iaq-remark').html(getRemark('iaq', point.iaq));
    $ele.find('#iaq-value').html(point.iaq);
    $ele.find('#co2-value').html(point.co2);
    $ele.find('#pm25-value').html(point.pm25);
    $ele.find('#pm10-value').html(point.pm10);
    $ele.find('#tvoc-value').html(point.tvoc);
    $ele.find('#hum-value').html(point.humidity);
    $ele.find('#temp-value').html((point.temperature - 4000) / 100);
    $ele.find('#iaq-pie').data('color', getColor('iaq', point.iaq));
    return $ele.get(0);
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
        content: createInfobox(point),
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
    });
    //done all, add marker to array
    markers.push(marker);
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
        $('#date-timepicker-to').data("DateTimePicker").minDate(
                e.date);
        updateCookies();
        updatePoints();
    });
    $("#date-timepicker-to").on("dp.change", function (e) {
        $('#date-timepicker-from').data("DateTimePicker").maxDate(
                e.date);
        updateCookies();
        updatePoints();
    });

    function updateCookies() {}

    function updatePoints() {
        //flush previous markers
        deleteMarkers();
        var device = $('#device').val();
        var from = $('#date-timepicker-from').data('DateTimePicker')
                .date();
        var to = $('#date-timepicker-to').data('DateTimePicker').date();
        if (from != null && to != null) {
            var url = base_url + 'index.php/device/get_points/' +
                    device + '/' + from + '/' + to;
            $.getJSON(url, function (points) {
                $.each(points, function (i, rec) {
                    addMarker(rec);
                });
                var markerCluster = new MarkerClusterer(map, markers);
            });
        }
    }
});