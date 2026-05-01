var min = $("#min").val();
var max = $("#max").val();
var campana = $("#campanas option:selected").val();
var chartarea;

$(document).ready(function() {
    google.charts.load('current', {packages: ['corechart']});
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
    });
    traedatos();
});

$("#min, #max, #campanas").on("change", function() {
    min = $("#min").val();
    max = $("#max").val();
    campana = $("#campanas option:selected").val();
    traedatos();
});
//setInterval(function(){traedatos();traedatosout()}, 3000);
function traedatos() {
    $("#areachart").html("<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>");
    $.post(site_url+"reportes/data", {min: min, max: max, campana: campana, reporte: 'compara30'}, function(data) {
        $("#areachart").html('');
            google.charts.setOnLoadCallback(function() {drawChart(data) });
    },"json");
}

function drawChart(data) {

    var datos = [['Hora', 'Abandonadas', 'Atendidas']];
    for (var key in data.area) {
        aba = (data.area[key].aba) ? data.area[key].aba : 0;
        ate = (data.area[key].ate) ? data.area[key].ate : 0;
        datos.push([key, parseInt(aba), parseInt(ate)]);
    }

    var datosarea = google.visualization.arrayToDataTable(datos);

    var view = new google.visualization.DataView (datosarea);
    view.setColumns(
        [   0,
            1,{
                calc: "stringify",
                sourceColumn: 1,
                type: "string",
                role: "annotation" },
            2,{
                calc: "stringify",
                sourceColumn: 2,
                type: "string",
                role: "annotation" },
        ]);

    var options = {
        chartArea: {width: '90%'},
        height: 500,
        isStacked: true,
        hAxis: {title: 'Medias horas'},
        colors: ['#FF0000', '#2F4FA1'],
        legend: {position: 'bottom'},
        areaOpacity: 0.7,
        vAxis: {title: 'Llamadas', minValue: 0, }
    };

    var areachart = document.getElementById('areachart');
    chartarea = new google.visualization.AreaChart(areachart);

    google.visualization.events.addListener(chartarea, 'ready', function () {
        areachart.innerHTML = '<img id="chartarea" src="' +chartarea.getImageURI()+ '">';
    });

    chartarea.draw(view, options);
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
    pdf.text(60, 23, 'Comparativo llamadas cada media hora');

    pdf.addImage(chartarea.getImageURI(),15, 40);
    pdf.save('compmediashoras.pdf');

})
