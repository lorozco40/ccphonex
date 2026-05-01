var fijo = $("#campanas option:selected").val();
var chartareainbA;
var chartareainbB;

$(document).ready(function(){
    google.charts.load('current', {'packages':['corechart']});
    traedatos();
    setInterval(function(){
        traedatos();
    }, 3000);
});

$(document).on("change", "#campanas", function() {
    fijo = $("#campanas option:selected").val();
    traedatos();
});

function traedatos() {
    var fijo = $("#campanas option:selected").val();
    $.post(site_url+"norma035/dashboard", {campana: fijo, min: $("#min").val(), max: $("#max").val() }, function(data) {
        google.charts.setOnLoadCallback(function() {drawChartAreaInb(data)});
    },"json");

    $.post(site_url+"norma035/dashdiasemana", {campana: fijo, min: $("#min").val(), max: $("#max").val() }, function(data) {
        google.charts.setOnLoadCallback(function() {drawChartAreaDias(data, 'area-week')});
    },"json");
}

function drawChartAreaInb(data) {
    var total=0;
    var datos = [['Hora', 'Atendidas']];
    for (var key in data.area) {
        ate = (data.area[key].ate) ? data.area[key].ate : 0;
        datos.push([key, parseInt(ate)]);
        total+=parseInt(ate);
    }
    var datosarea = google.visualization.arrayToDataTable(datos);
    var view = new google.visualization.DataView (datosarea);
    var options = {
        chartArea: {top: 5, width: '90%', height: '80%'},
        backgroundColor: {fill: 'transparent'},
        colors: ['#305ca7'],
        legend: 'none',
        areaOpacity: 0.8,
        isStacked: true,
        pointSize: 3,
        dataOpacity: 0.7,
        series: {
            0:{pointShape: 'circle'},
            1:{pointShape: 'circle'},
        },
        tooltip: { isHtml: true, showColorCode: true},
    };
    chartareainbA = new google.visualization.AreaChart(document.getElementById('area-example'));
    chartareainbA.draw(view, options);
    $("#area-example_total").html(total);
}

function drawChartAreaDias(data) {
    var total=0;
    var datos = [['Hora', 'Atendidas']];
    for (var key in data.area) {
        ate = (data.area[key].ate) ? data.area[key].ate : 0;
        datos.push([key, parseInt(ate)]);
        total+=parseInt(ate);
    }
    var datosarea = google.visualization.arrayToDataTable(datos);
    var view = new google.visualization.DataView (datosarea);
    var options = {
        chartArea: {top: 5, width: '90%', height: '80%'},
        backgroundColor: {fill: 'transparent'},
        colors: ['#b553a6'],
        legend: 'none',
        areaOpacity: 0.8,
        isStacked: true,
        pointSize: 3,
        dataOpacity: 0.7,
        series: {
            0:{pointShape: 'circle'},
            1:{pointShape: 'circle'},
        },
        tooltip: { isHtml: true, showColorCode: true},
    };
    chartareainbB = new google.visualization.AreaChart(document.getElementById('area-week'));
    chartareainbB.draw(view, options);
    $("#area-week_total").html(total);
}
$(document).on("click", "#pdfchart", function(e) {
    e.preventDefault();
    var pdf = new jsPDF({
        orientation: 'landscape'
    })

    pdf.setLineWidth(1.0);
    pdf.line(20, 30, 275, 30);
    pdf.line(20, 190, 275, 190);

    pdf.setFontSize(30);
    pdf.text(70, 25, 'Reporte de NOM 035');
    pdf.addImage(chartareainbA.getImageURI(), 3, 60);
    pdf.addImage(chartareainbB.getImageURI(), 3, 130);
    pdf.save('reportenom035.pdf');

})
