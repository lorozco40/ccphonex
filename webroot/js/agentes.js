var campana;
var campananombre = '';
var selcamps = [];

$(document).ready(function(){
    traerdatos();
    setInterval(traerdatos,3000);
    campana = $("#campana").val();
    selcamps = campana.split(',');
    google.charts.load("current", {packages:["corechart"]});
});
$(document).on("change", "#campana", function(){
    campana = $("#campana").val();
    selcamps = campana.split(',');
    campananombre = ($("#campana option:selected").text() == 'Todas ...') ? '' : $("#campana option:selected").text();
});
function traerdatos() {
    $.post(site_url+"agentes/status", {campana:$("#campana").val()} ,function(data) {
        $("#agentes > .table-row").css({"display":"none"});
        data.status.forEach(function(row) {
            var pasar = "No";
            var estascamps = row.campanas.split(',');
            var inter = intersect(selcamps, estascamps);
            if (inter.length > 0) {
                var pasar = "Si";
            }
            if (row.estatus == "Desconectado" || pasar == "No") {
                $("#fila"+row.id).css("display", "none");
            } else {
                $("#fila"+row.id).css("display", "table-row");
                var ico = '<i class="fas fa-user-alt-slash"></i>';
                if (row.statgraf == "acw") {
                    ico = '<i class="fas fa-table"></i>';
                }
                if (row.statgraf == "comida") {
                    ico = '<i class="fas fa-utensils"></i>';
                }
                if (row.statgraf == "retro") {
                    ico = '<i class="fas fa-chalkboard"></i>';
                }
                if (row.statgraf == "sanitario") {
                    ico = '<i class="fas fa-toilet-paper"></i>';
                }
                if (row.statgraf == "break") {
                    ico = '<i class="fas fa-coffee"></i>';
                }
                if (row.statgraf == "Disponible" || row.statgraf == "Otro") {
                    ico = '<i class="fas fa-user" ></i>';
                }
                if (row.statgraf == "En llamada" || row.statgraf == "Llamando") {
                    ico = '<i class="fas fa-phone"></i>';
                }

                $("#stat"+row.id).text(row.estatus);
                $("#act"+row.id).text(row.act);
                $("#time"+row.id).text(row.acttime);
                $("#ico"+row.id).html(ico).css("color", row.color);
            }
        });
        $("#longw").text("0"+data.colas.longestwait);
        $("#encolam").text(data.colas.wait);
        $("#llamrm").text(data.estad.rec);
        $("#porsl").text(data.estad.porsl+"%");
        $("#llamam").text(data.estad.aba);
        $("#promrm").text(data.estad.avgwait);
        $("#promlm").text(data.estad.avgdura);
        $("#promam").text(data.estad.avgaba);
        google.charts.setOnLoadCallback(function() { drawChart(data.graf, data.colores) });
    },"json");
}
function drawChart(arra, colores) {
    var data = google.visualization.arrayToDataTable(arra);
    var options = {
        title: 'Estatus',
        titleTextStyle:{fontSize: 16, bold: 'true'},
        is3D: true,
        chartArea:{left:20,top:45,width:'100%',height:'100%'},
        legend: {textStyle: {fontSize: 14, bold: 'true'}},
        pieSliceTextStyle: {fontSize: 18, bold: 'true'},
        pieSliceText: 'value',
        slices:{    1: {offset: 0.07},
                    2: {offset: 0.07},
                    3: {offset: 0.07},
                    4: {offset: 0.07},
                    5: {offset: 0.07},
                    6: {offset: 0.07},
                    7: {offset: 0.07},
                    8: {offset: 0.07},
                    9: {offset: 0.07}
                },
        // pieStartAngle: -100,
        colors: colores
    };

    var chart = new google.visualization.PieChart(document.getElementById('grafica'));
    chart.draw(data, options);
}

function intersect(a, b) {
    var t;
    if (b.length > a.length) t = b, b = a, a = t; // indexOf to loop over shorter
    return a.filter(function (e) {
        return b.indexOf(e) > -1;
    }).filter(function (e, i, c) { // extra step to remove duplicates
        return c.indexOf(e) === i;
    });
}
