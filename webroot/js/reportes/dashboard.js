var fijo = $("#campanas option:selected").val();

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
    $.post(site_url+"reportes/dashini", {campana: fijo}, function(data) {
        atepor = (data.dona.atendidas!=0) ? Math.round((data.dona.atendidas/data.dona.total)*100) : 0;
        abapor = (data.dona.abandonadas!=0) ? Math.round((data.dona.abandonadas/data.dona.total)*100) : 0;
        ateopor = (data.donaout.atendidas!=0) ? Math.round((data.donaout.atendidas/data.donaout.total)*100) : 0;
        abaopor = (data.donaout.abandonadas!=0) ? Math.round((data.donaout.abandonadas/data.donaout.total)*100) : 0;
        $("#donut-example, #area-example").html("");
        $("#donut-example-out, #area-example-out").html("");
        $("#dashindicadorate").html(data.dona.atendidas+" <small><small>("+atepor+"%)</small></small>");
        $("#dashindicadoraba").html(data.dona.abandonadas+" <small><small>("+abapor+"%)</small></small>");
        $("#dashindicador").html(data.dona.total);
        $("#dashindicadorateout").html(data.donaout.atendidas+" <small><small>("+ateopor+"%)</small></small>");
        $("#dashindicadorabaout").html(data.donaout.abandonadas+" <small><small>("+abaopor+"%)</small></small>");
        $("#dashindicadorout").html(data.donaout.total);
        html = "<table class='table table-borderless table-striped table-hover text-center'><thead><tr><th>Cola</th><th>Agentes</th><th>Agentes en llamada</th><th>Llamadas en cola</th></tr></thead><tbody>";
        encola = 0;
        Object.keys(data.colas).forEach(function(key) {
            encola += parseInt(data.colas[key].wait);
            if (undefined === data.colas[key].members) {
                agentes = 0;
            } else {
                agentes = Object.keys(data.colas[key].members).length;
            }
            html += "<tr><td>"+key+"</td>";
            html += "<td>"+agentes+"</td>";
            html += "<td>"+data.colas[key].enllamada+"</td>";
            html += "<td>"+data.colas[key].wait+"</td></tr>";
        });
        $("#colas").html(html+"</tbody></table>");
        $("#contestadas").html(data.colas.answered);
        $("#espera").html(data.colas.wait);
        $("#tres").html(data.colas.hanged);
        $("#wait").text(encola);

        if (data.dona.total >0 || data.area.total >0) {
            google.charts.setOnLoadCallback(function() {drawChartPieInb(data.dona), drawChartAreaInb(data)
            });
        } else {
            $("#donut-example, #area-example").html("");
        }
        if (data.donaout.total >0 || data.areaout.total >0) {
            google.charts.setOnLoadCallback(function() {drawChartPieOut(data.donaout), drawChartAreaOut(data)
            });
        } else {
            $("#donut-example-out, #area-example-out").html("");
        }
    },"json");
}

function drawChartPieInb(data) {

    var piedata = google.visualization.arrayToDataTable([
        ['Rubro', 'Porcentaje'],
        ['Atendidas', parseInt(data.atendidas)],
        ['Abandono', parseInt(data.abandonadas)]
    ]);

    var pieoptions = {
        pieHole: 0.4,
        backgroundColor: {fill: 'transparent'},
        colors: ['#adca56', '#ff0012'],
        legend: {position: 'bottom', alignment: 'center', textStyle: {bold: 'true'}},
        chartArea: {top: 0, width: '100%', height: '85%'},
        tooltip: { isHtml: true },
    };

    var chartpieinb = new google.visualization.PieChart(document.getElementById('donut-example'));

    chartpieinb.draw(piedata, pieoptions);
}

function drawChartPieOut(data) {

    var piedata = google.visualization.arrayToDataTable([
        ['Rubro', 'Porcentaje'],
        ['Exitosas', parseInt(data.atendidas)],
        ['Fallidas', parseInt(data.abandonadas)]
    ]);

    var pieoptions = {
        pieHole: 0.4,
        backgroundColor: {fill: 'transparent'},
        colors: ['#adca56', '#ff0012'],
        legend: {position: 'bottom', alignment: 'center', textStyle: {bold: 'true'}},
        chartArea: {top: 0, width: '100%', height: '85%'},
        tooltip: { isHtml: true },
    };

    var chartpieout = new google.visualization.PieChart(document.getElementById('donut-example-out'));

    chartpieout.draw(piedata, pieoptions);
}

function drawChartAreaInb(data) {

    var datos = [['Hora', 'Abandono', 'Atendidas']];
    for (var key in data.area) {
        aba = (data.area[key].aba) ? data.area[key].aba : 0;
        ate = (data.area[key].ate) ? data.area[key].ate : 0;
        datos.push([key, parseInt(aba), parseInt(ate)]);
    }

    var datosarea = google.visualization.arrayToDataTable(datos);

    var view = new google.visualization.DataView (datosarea);

    var options = {
        chartArea: {top: 5, width: '90%', height: '80%'},
        backgroundColor: {fill: 'transparent'},
        colors: ['#ff0012', '#adca56'],
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

     var chartareainb = new google.visualization.AreaChart(document.getElementById('area-example'));

    chartareainb.draw(view, options);
}

function drawChartAreaOut(data) {

    var datos = [['Hora', 'Fallidas', 'Finalizadas']];
    for (var key in data.areaout) {
        abaout = (data.areaout[key].abaout) ? data.areaout[key].abaout : 0;
        ateout = (data.areaout[key].ateout) ? data.areaout[key].ateout : 0;
        datos.push([key, parseInt(abaout), parseInt(ateout)]);
    }

    var datosarea = google.visualization.arrayToDataTable(datos);

    var view = new google.visualization.DataView (datosarea);

    var options = {
        chartArea: {top: 5, width: '90%', height: '80%'},
        backgroundColor: {fill: 'transparent'},
        colors: ['#ff0012', '#adca56'],
        legend: 'none',
        areaOpacity: 0.8,
        isStacked: true,
        pointSize: 3,
        dataOpacity: 0.7,
        series: {
            0: {pointShape: 'circle'},
            1: {pointShape: 'circle'},
        },
        tooltip: { isHtml: true, showColorCode: true },
    };

     var chartareaout = new google.visualization.AreaChart(document.getElementById('area-example-out'));

    chartareaout.draw(view, options);
}
