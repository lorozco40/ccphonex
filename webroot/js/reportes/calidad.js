var pag = $("#pag").val();
var min = $("#min").val();
var max = $("#max").val();
var tipeval = $("#tipeval option:selected").val();
var tipo = $("#tipo option:selected").val();
var estagente = $("#agentes option:selected").val();
var chartbar;

$(document).on("change", "#min, #max, #tipo, #tipeval, #agentes", function() {
    pag = 0;
    min = $("#min").val();
    max = $("#max").val();
    tipeval = $("#tipeval option:selected").val();
    tipo = $("#tipo option:selected").val();
    estagente = $("#agentes option:selected").val();
    datoscalidad();
});

$(document).ready(function(){
    pag = 0;
    datoscalidad();
    google.charts.load('current', {packages: ['corechart', 'bar']});
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
    });
});

$(document).on("click", ".page-link", function(e) {
    e.preventDefault();
    pag = ($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG;
    datoscalidad();
});

function datoscalidad() {
    $("#repocalidad").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-pulse'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $(".parad-none").addClass("d-none").removeClass('parad-none');
    $.post(site_url+"calidad/listcalidad", {min: min, max: max, page: pag, tipo: tipo, tipeval: tipeval, agente: estagente }, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (!data.data || data.data.length == 0) {
            $("#repocalidad").html(html);
        } else {
            var html = "<div class='table table-striped'><div class='table-header-group'>";
            data.campos.forEach(function(row){
                html += "<div class='table-cell'>"+row.name+"</div>";
            });
            html += "</div>";
            data.data.forEach(function(row){
                html += "<div class='table-row'>";
                data.campos.forEach(function(dato){
                    html += "<div class='table-cell'>"+row[dato.name]+"</div>";
                });
                html += "</div>";
            });
            html += "<div class='table-row' style='background-color: #2b2b2b; color: #ffffff;'>"+
                "<div class='table-cell'></div><div class='table-cell'></div><div class='table-cell'></div><div class='table-cell'></div>"+
                "<div class='table-cell'></div><div class='table-cell'></div><div class='table-cell'>Efectividad en <strong>"+data.cuenta+"</strong> llamadas</div>";
            data.campostot.forEach(function(row, index){
                if (index == 0) {
                    html += "<div style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;' class='table-cell'>"+(data.tot[0][row.name]/data.cuenta).toFixed(2)+"%</div>";
                } else if (index == 1) {
                    html += "<div style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;' class='table-cell'>"+(100-(data.tot[0][row.name]/data.cuenta)).toFixed(2)+"%</div>";
                } else {
                    html += "<div style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;' class='table-cell'>"+((data.tot[0][row.name]/data.cuenta)*100).toFixed(2)+"%</div>";
                }
            });
            html += "</div>";

            mos = ((pag+REGS_POR_PAG)>data.cuenta) ? data.cuenta : pag+REGS_POR_PAG;
            $("#inireg").text(pag+1);
            $("#finreg").text(mos);
            $("#totreg").text(data.cuenta);
            $("#pagination").html(data.pagination);
            $(".d-none").addClass("parad-none").removeClass('d-none');
            google.charts.setOnLoadCallback(function() { drawStacked(data.graf) });
        }
        $("#repocalidad").html(html);
        $("#bar_chart").html("");
    }, "json");

}

function drawStacked(datos) {
console.log(datos);
    var data = google.visualization.arrayToDataTable(datos);

    var options = {
        height: 550,
        isStacked: 'percent',
        legend: {position: 'bottom'},
        colors: ['#1E60A2', '#639C1E'], // '#1E60A2' azul, '#639C1E' verde,'#A2701E' naranja, 'A21E2E' rojo
        hAxis: {title: 'Agentes'},
        vAxis: {minValue: 0,
                ticks: [0, .1, .2, .3, .4, .6, .8, 1],
                title: '% Efectividad vs Faltante'}
    };

    var bar_chart = document.getElementById('bar_chart');
    chartbar = new google.visualization.ColumnChart(bar_chart);

    google.visualization.events.addListener(chartbar, 'ready', function () {
        bar_chart.innerHTML = '<img id="chartbar" src="' +chartbar.getImageURI()+ '">';
    });

    chartbar.draw(data, options);
}

$(document).on("click", "#pdfbtn", function(e) {
    e.preventDefault();
    var pdf = new jsPDF({
        orientation: 'landscape'
    });

    pdf.setLineWidth(1.0);
    pdf.line(20, 30, 275, 30);
    pdf.line(20, 190, 275, 190);

    pdf.setFontSize(30);
    pdf.text(90, 25, 'Efectividad por agente');

    pdf.addImage(chartbar.getImageURI(), 3, 30);
    pdf.save('grafcalidad.pdf');

});
