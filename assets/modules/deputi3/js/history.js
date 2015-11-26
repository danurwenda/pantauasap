var map, markerCluster;
// array of reference of all markers
var markers = [];

function initialize() {
    var cookieZoom = ace.cookie.get('history-zoom')
            , cookieCenterLat = ace.cookie.get('history-center-lat')
            , cookieCenterLon = ace.cookie.get('history-center-lon');
    map = new google.maps.Map(document.getElementById('map'), {
        zoom:
                cookieZoom != undefined ? parseFloat(cookieZoom) :
                5,
        center:
                (cookieCenterLat != undefined && cookieCenterLon != undefined) ? new google.maps.LatLng(parseFloat(cookieCenterLat), parseFloat(cookieCenterLon)) :
                new google.maps.LatLng(-3.162, 113.730),
        mapTypeId: google.maps.MapTypeId.TERRAIN
    });
    map.addListener('zoom_changed', function () {
        ace.cookie.set('history-zoom', map.getZoom(), 604800)
        console.log(map.getZoom())
    });
    map.addListener('center_changed', function () {
        ace.cookie.set('history-center-lat', map.getCenter().lat(), 604800)
        ace.cookie.set('history-center-lon', map.getCenter().lng(), 604800)
    });

    updatePoints();
}

function getCircle(point) {
    var iaq = parseInt(point.iaq);
    var colortemp = getColor($('#sensorparam').val(), point);
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
    if (markerCluster != undefined)
        markerCluster.clearMarkers();
}

function getRemark(paramnum, pointdata) {
    var val = getParamValue(paramnum, pointdata);
    return 'Remark';
}

/**
 * Create infoBox depends on selected parameter
 * @param {type} point
 * @returns {unresolved}
 */
function createInfoboxMain(point) {
    var paramnum = $('#sensorparam').val(),
            $ele = $(document.getElementById('infobox-main').cloneNode(true));
    // header
    $ele.find('#main-title').html(getTitle(paramnum));
    $ele.find('#main-value').html(getParamValue(paramnum, point));
    $ele.find('#main-pie').data('color', getColor(paramnum, point));
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
google.maps.event.addDomListener(window, 'load', initialize);

function updateCookies() {
    ace.cookie.set('history-param', $('#sensorparam').val(), 604800);
    ace.cookie.set('history-device', $('#device').val(), 604800);
    ace.cookie.set('history-time-from', $('#date-timepicker-from').data('DateTimePicker').date(), 604800);
    ace.cookie.set('history-time-to', $('#date-timepicker-to').data('DateTimePicker').date(), 604800);
}

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
            markerCluster = new MarkerClusterer(map, markers,{maxZoom:18});
        });
    }
}
/**
 * Apply background style to .legend and add invisible li's to it.
 * @returns {undefined}
 */
function updateLegend() {
    var selectedParam = $('#sensorparam').val(),
            th = thresholds[selectedParam],
            color = colors[selectedParam],
            bg = '',
            thRange = th[th.length - 1] - th[0]
            ;
    var $ul = $('.legend');
    //clear ul
    $ul.empty();
    for (var i = 0; i < th.length; i++) {
        //compute percentage
        if (i === 0) {
            bg = color[i] + ' 0%';
        } else {
            var percent = (th[i] - th[0]) * 100 / thRange;
            bg = bg + ', ' + color[i] + ' ' + percent + '%';
        }
        //create li inside .legend
        //the number of li's created is equal to th.length-1
        if (i < th.length - 1) {
            var $li = $('<li></li>');//create new li
            //give class based on parity
            if (i % 2 === 0) {
                //even index means odd'th element in the list
                $li.addClass('odd');
            }
            //insert label
            $li.append('<span class="start">' + th[i] + '</span>');
            //give class on first/last li
            if (i === 0) {
                //lowest level
                $li.addClass('lowest');
            } else if (i === th.length - 2) {
                //highest level
                $li.addClass('highest');
                //add an .end
                $li.append('<span class="end">' + th[i + 1] + '</span>');
            }
            //compute the length of the li compared to the ul
            var percentLength = 100 * (th[i + 1] - th[i]) / thRange;
            //put the style
            $li.width(percentLength + '%');
            //add to ul
            $ul.append($li);
        }
    }
    //apply background linear gradient css
    $ul.css('background', 'linear-gradient(to right,' + bg + ')');
}

jQuery(function ($) {
    var cookieFrom = ace.cookie.get('history-time-from'),
            cookieTo = ace.cookie.get('history-time-to'),
            cookieParam = ace.cookie.get('history-param'),
            cookieDevice = ace.cookie.get('history-device');
    $('#date-timepicker-from').datetimepicker({
        defaultDate: cookieFrom != undefined ? cookieFrom : Date.now() - 30 * 24 * 3600 * 1000
    }).next().on(ace.click_event, function () {
        $(this).prev().focus();
    });
    $('#date-timepicker-to').datetimepicker({
        defaultDate: cookieTo != undefined ? cookieTo : Date.now()
    }).next().on(ace.click_event, function () {
        $(this).prev().focus();
    });
    if (cookieParam != undefined) {
        $('#sensorparam').val(cookieParam);
    }
    updateLegend();
    $('#sensorparam').change(function () {
        updateCookies();
        updatePoints();
        updateLegend();
    });
    if (cookieDevice != undefined) {
        $('#device').val(cookieDevice);
    }
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
});