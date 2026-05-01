var min = $("#min").val();
var max = $("#max").val();
var campana = $("#campana").val();
var chartbar;

$(document).ready(function() {
    google.charts.load('current', {packages: ['corechart', 'bar']});
    traerdatosporcolas();
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
    });
});

$("#min, #max, #campana").on("change", function() {
    min = $("#min").val();
    max = $("#max").val();
    campana = $("#campana").val();
    traerdatosporcolas();
});

function traerdatosporcolas() {
    $("#chart_div").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $.post(site_url+"reportes/listarporcolasgra", {min: min, max: max, campana: campana}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.length == '' || data.length == null) {
            $("#chart_div").html(html);
        } else {
            google.charts.setOnLoadCallback(function() {drawBasic(data) });
        }
    },"json");
}

function drawBasic(data) {

    var datos = new google.visualization.DataTable();
    datos.addColumn('string', 'Cola');
    datos.addColumn('number', 'Exitosas');
    datos.addColumn('number', 'Abandono');
    data.forEach(function (row) {
        datos.addRow([String(row.cola), parseInt(row.exito), parseInt(row.abandono)]);
    });

    var view = new google.visualization.DataView (datos);
    view.setColumns([0,
        1,{calc: "stringify", sourceColumn: 1, type: "string", role: "annotation" },
        2,{calc: "stringify", sourceColumn: 2, type: "string", role: "annotation" }
    ]);

    var options = {
        chartArea: {width: '85%'},
        legend: {position: 'bottom'},
        colors: ['#adca56', '#ff0012'],
        height: 550,
        bar: {groupWidth: "70%"},
        hAxis: {title: 'Campañas', minValue: 0, titleTextStyle:{bold:true}},
        vAxis: {title: 'Llamadas por campaña', titleTextStyle:{bold:true}}
    };

    var chart_div = document.getElementById('chart_div');
    chartbar = new google.visualization.ColumnChart(chart_div);

    google.visualization.events.addListener(chartbar, 'ready', function() {
        chart_div.innerHTML = '<img id="chartbar" src="'+chartbar.getImageURI()+'">';
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
    pdf.text(100, 25, 'Análisis por campaña');

    pdf.addImage(chartbar.getImageURI(), 10, 30);
    pdf.save('analisiscampana.pdf');

})
