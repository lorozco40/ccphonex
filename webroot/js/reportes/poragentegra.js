var min = $("#min").val();
var max = $("#max").val();
var estagente = $("#agentes option:selected").val();
var chartbar;

$(document).ready(function() {
    google.charts.load('current', {packages: ['corechart', 'bar']});
    traerdatosporagente();
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
    });
});

$("#min, #max, #agentes").on("change", function() {
    min = $("#min").val();
    max = $("#max").val();
    estagente = $("#agentes option:selected").val();
    traerdatosporagente();
});

function traerdatosporagente() {
    $("#chart_div").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $.post(site_url+"reportes/listarporagentegra", {min: min, max: max, agente: estagente}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.length == '' || data.length == null) {
            $("#chart_div").html(html);
        } else {
            google.charts.setOnLoadCallback(function() {drawBasic(data) });
        }
    }, "json");
}

function drawBasic(data) {

    var datos = new google.visualization.DataTable();
    datos.addColumn('string', 'Agente');
    datos.addColumn('number', 'Entrante');
    datos.addColumn('number', 'Saliente');
    data.forEach(function(row) {
        datos.addRow([
            String(row.agente),
            parseInt(row.entrante),
            parseInt(row.saliente)
        ]);

    });

    var view = new google.visualization.DataView (datos);
    view.setColumns([0,
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
        chartArea: {width: '45%'},
        height: 550,
        legend: {position: 'bottom'},
        colors: ['#1E60A2', '#639C1E'], // '#1E60A2' azul, '#639C1E' verde,'#A2701E' naranja, 'A21E2E' rojo
        hAxis: {title: 'Llamadas totales', minValue: 0},
        vAxis: {title: 'Agentes'}
    };

    var chart_div = document.getElementById('chart_div');
    chartbar = new google.visualization.BarChart(chart_div);

    google.visualization.events.addListener(chartbar, 'ready', function () {
        chart_div.innerHTML = '<img id="chartbar" src="' +chartbar.getImageURI()+ '">';
    });

    chartbar.draw(view, options);
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
    pdf.text(70, 25, 'Distribución llamadas por agente');

    pdf.addImage(chartbar.getImageURI(), 3, 30);
    pdf.save('llamadasagente.pdf');

})
