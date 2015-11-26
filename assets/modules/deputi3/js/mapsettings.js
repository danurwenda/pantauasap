var params = [
    'iaq',
    'tvoc',
    'co2',
    'pm25',
    'pm10',
    'temperature',
    'humidity'
], displayname = [
    'IAQ (index)',
    'TVOC (PPB)',
    'CO<sub>2</sub> (PPM)',
    'PM2.5 (μg/m<sup>3</sup>)',
    'PM10  (μg/m<sup>3</sup>)',
    'Temp (℃)',
    'Humidity (%)'
],
        thresholds = [
            [0, 50, 100, 150, 200], //iaq
            [0, 200, 500, 1000, 1500], //tvoc
            [0, 800, 1100, 2500], //co2
            [0, 20, 40, 60, 150], //pm2.5
            [0, 60, 150, 250], //pm10
            [-20, -10, 15, 20, 28, 30, 35], //temperature
            [0, 20, 30, 40, 60, 80, 90]      //humidity
        ], colors = [
    ['#7ae52d', '#f8fc0c', '#f2bd2e', '#d32121', '#7905af'], //iaq
    ['#7ae52d', '#f8fc0c', '#f2bd2e', '#d32121', '#7905af'], //tvoc
    ['#7ae52d', '#f8fc0c', '#f2bd2e', '#d32121'], //co2
    ['#7ae52d', '#f8fc0c', '#f2bd2e', '#d32121', '#7905af'], //pm2.5
    ['#7ae52d', '#f8fc0c', '#f2bd2e', '#d32121'], //pm10
    ['#d32121', '#f2bd2e', '#f8fc0c', '#7ae52d', '#f8fc0c', '#f2bd2e', '#d32121'], //temp
    ['#d32121', '#f2bd2e', '#f8fc0c', '#7ae52d', '#f8fc0c', '#f2bd2e', '#d32121']//hum
];


function getTitle(paramnum) {
    return displayname[paramnum]
}

function getParamValue(paramnum, point) {
    return point[params[paramnum]]
}

function getColor(param, point) {
    var r, g, b,
            val = getParamValue(param, point),
            th = thresholds[param],
            cls = colors[param];
    for (var i = 0; i < th.length; i++) {
        if (val <= th[i]) {
            //we found it
            //the color lies between cls[i-1](if any) and cls[i]
            if (i === 0) {
                return cls[0];
            } else {
                var bot = hexToRgb(cls[i - 1]);
                var top = hexToRgb(cls[i]);
                var gap = th[i] - th[i - 1];
                val = val - th[i - 1];
                //somewhere between
                r = bot.r + (val / gap) * (top.r - bot.r);
                g = bot.g + (val / gap) * (top.g - bot.g);
                b = bot.b + (val / gap) * (top.b - bot.b);
                return rgbToHex(Math.round(r), Math.round(g), Math.round(b));
            }
        }
    }
    return cls[cls.length - 1];
}
function rgbToHex(r, g, b) {
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
}
function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}
// Render pie
function renderPie() {
    $('.easy-pie-chart.percentage').each(function () {
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