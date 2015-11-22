Highcharts.setOptions({
    lang: {
        loading: "Memuat...",
        months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
        shortMonths: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
        weekdays: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
        numericSymbols: ["rb", "jt", "M", "T", "P", "E"],
        noData: "Tidak ada data",
        decimalPoint: ".",
        resetZoom: "Reset zoom",
        resetZoomTitle: "Reset zoom level 1:1",
        thousandsSep: ",",
        eventButtonTitle: 'Show/Hide Events',
        expandButtonTitle: 'Expand'
    }
});
function expand4Chart() {
    var chartContainer = $(this.container).parent();
    var isFullscreen = chartContainer.hasClass('fullscreen');
    if (isFullscreen) {
        //show both columns
        $('.chart').show().parent().show();
        //shrink this        
        chartContainer.removeClass('fullscreen');
        //reflow chart
        this.reflow();
    } else {
        //hide both columns
        $('.chart').hide().parent().hide();
        //expand this        
        chartContainer.parent().show();
        chartContainer.show().addClass('fullscreen');
        //reflow chart
        this.reflow();
    }
}
function getViewportWidth() {
    // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
    var viewportwidth;
    if (typeof window.innerWidth != 'undefined')
    {
        viewportwidth = window.innerWidth;
    }

    // IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)

    else if (typeof document.documentElement != 'undefined'
            && typeof document.documentElement.clientWidth !=
            'undefined' && document.documentElement.clientWidth != 0)
    {
        viewportwidth = document.documentElement.clientWidth;
    }

    // older versions of IE

    else
    {
        viewportwidth = document.getElementsByTagName('body')[0].clientWidth;
    }
    return viewportwidth;
}
function getViewportHeight() {
    var viewportheight;
// the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight

    if (typeof window.innerWidth != 'undefined')
    {
        viewportheight = window.innerHeight;
    }

    // IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)

    else if (typeof document.documentElement != 'undefined'
            && typeof document.documentElement.clientWidth !=
            'undefined' && document.documentElement.clientWidth != 0)
    {
        viewportheight = document.documentElement.clientHeight;
    }

    // older versions of IE

    else
    {
        viewportheight = document.getElementsByTagName('body')[0].clientHeight;
    }
    return viewportheight;
}