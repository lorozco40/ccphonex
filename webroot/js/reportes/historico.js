var min = $("#min").val();
var max = $("#max").val();
var fijo = $("#campanas option:selected").val();

var hora_inicio_selected = "00:00:00";
var hora_fin_selected    = "23:59:59";

$(document).ready(function(){
    google.charts.load('current', {'packages':['corechart']});
    traedatos();
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
    });

});

$(document).on("change", "#min, #max, #campanas", function() {
    min = $.trim( $("#min").val() );
    max = $.trim( $("#max").val() );
    fijo = $.trim( $("#campanas option:selected").val() );
    if( fijo.length > 0 ){
        buscar_horas();
    }
});

$(document).on("change", "#hora_inicio, #hora_fin", function() {

    hora_inicio_selected = $.trim( $("#hora_inicio").val() );
    hora_fin_selected    = $.trim( $("#hora_fin").val() );

    hora_inicio_value = ( hora_inicio_selected.length > 0 ) ? parseInt(hora_inicio_selected.split(":")[0]) : 100;
    hora_fin_value    = ( hora_fin_selected.length > 0 ) ? parseInt(hora_fin_selected.split(":")[0]) : 100;

    if( hora_inicio_value >= hora_fin_value ){
        limpiarGraficasDatos();
        toastmsg("La hora de inicio no puede ser mayor o igual a la hora de término", "danger");
        return false;
    }

    traedatos();
});

function traedatos() {
    $.post(site_url+"reportes/data", {min: min, max: max, hora_inicio: hora_inicio_selected, hora_fin: hora_fin_selected, campana: fijo, reporte: 'historico'}, function(data) {
        if( data.success ){
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
        } else{
            limpiarGraficasDatos();
            toastmsg(data.error, "danger");
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
        chartArea: { top: 0, width: '100%', height: '85%'},
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

function buscar_horas() {
    var cid = $("#campanas").val();
    $.post(site_url+'reportes/buscarHorasCampana', {
        cid: cid,
    }, function(respuesta) {
        if( respuesta.success ){
            armarSelectHoras(respuesta.data.inicio, respuesta.data.fin );
        } else {
            toastmsg(respuesta.msg, "danger");
        }
    }, 'json')
    .fail(function(e) {
        // console.log(e);
        location.reload();
    });
}

function armarSelectHoras( inicio, fin ) {
    hora_inicio = inicio;
    hora_fin = fin;

    arregloHoraInicio = hora_inicio.split(":");
    hora_inicio = parseInt(arregloHoraInicio[0]);

    arregloHoraFin = hora_fin.split(":");
    hora = parseInt(arregloHoraFin[0]);
    minuto = parseInt(arregloHoraFin[1]);
    horamax = false;

    if ( minuto > 0 ) hora++;

    hora_fin = hora;

    optionInicio = "";
    for (let index = hora_inicio; index <= hora_fin; index++) {
        text = index + ":00";
        text = text.padStart(5,"0");
        value = text + ":00";

        selected = index == hora_inicio ? "selected" : "";

        // NO INCLUYE EL 24 CUANDO HORA_FIN = 23:59
        if ( index != 24 ) optionInicio += "<option value='"+value+"' "+selected+" >"+text+"</option>";
    }

    optionFin = "";
    for (let index = hora_inicio; index <= hora_fin; index++) {
        text = index + ":00";
        text = text.padStart(5,"0");
        value = text + ":00";

        selected = index == hora_fin ? "selected" : "";

        // CUANDO INDEX SEA 24 EN VEZ DE COLOCAR EL 24 SE COLOCARÁ 23:59
        if ( index != 24 ) optionFin += "<option value='"+value+"' "+selected+" >"+text+"</option>";
        else optionFin += "<option value='23:59:59' selected>23:59</option>";
    }

    $("#hora_inicio").empty().html(optionInicio);
    $("#hora_fin").empty().html(optionFin).change();
}

function limpiarGraficasDatos() {
    $("#dashindicador").html("0");
    $("#dashindicadoraba, #dashindicadorate").html("0 <small><small>(0%)</small></small>");
    $("#dashindicadorout").html("0");
    $("#dashindicadorabaout, #dashindicadorateout").html("0 <small><small>(0%)</small></small>");
    $("#donut-example, #area-example").empty();
    $("#donut-example-out, #area-example-out").empty();
}
