$(document).ready(function() {
    traerdatos();
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
    traerdatos();
});

function traerdatos() {
    $("#acumulado").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    var min = $("#min").val();
    var max = $("#max").val();
    $.post(site_url+"sl/acum_mensual_data", $("#acumform").serialize(), function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#acumulado").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>Fecha</div><div class='table-cell'>Campaña</div><div class='table-cell'>ASA</div>"+
                "<div class='table-cell'>ABA</div><div class='table-cell'>Atendidas</div><div class='table-cell'>Por agente</div>"+
                "<div class='table-cell'>AHT</div><div class='table-cell'>Prom ACW</div><div class='table-cell'>Abandonadas</div>"+
                "<div class='table-cell'>Salientes</div><div class='table-cell'>Prom Salientes</div><div class='table-cell'>Prom entrantes</div>"+
                "<div class='table-cell'>% constestadas</div><div class='table-cell'>Aba < 3's</div><div class='table-cell'>SL("+data.data[0].seg+"'s)</div></div>";

                data.data.forEach(function(row){
                    html += "<div class='table-row'>"+
                        "<div class='table-cell'>"+row.fecha+"</div>"+
                        "<div class='table-cell'>"+row.campana+"</div>"+
                        "<div class='table-cell'>"+row.promresp+"</div>"+
                        "<div class='table-cell'>"+row.promaban+"</div>"+
                        "<div class='table-cell'>"+row.llamadas+"</div>"+
                        "<div class='table-cell'>"+row.ateporage+"</div>"+
                        "<div class='table-cell'>"+row.promllam+"</div>"+
                        "<div class='table-cell'>"+row.promacw+"</div>"+
                        "<div class='table-cell'>"+row.llamaban+"</div>"+
                        "<div class='table-cell'>"+row.llamsalida+"</div>"+
                        "<div class='table-cell'>"+row.promllamsal+"</div>"+
                        "<div class='table-cell'>"+row.promllament+"</div>"+
                        "<div class='table-cell'>"+row.porcontes+"%</div>"+
                        "<div class='table-cell'>"+row.llamaba3seg+"</div>"+
                        "<div class='table-cell'>"+row.nivserv+"%</div></div>\n";
                });
                html += "<div class='table-row' style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;'>"+
                "<div class='table-cell'></div>"+
                "<div class='table-cell'>Total"+"</div>"+
                "<div class='table-cell'>"+data.tot.promresp+"</div>"+
                "<div class='table-cell'>"+data.tot.promaban+"</div>"+
                "<div class='table-cell'>"+data.tot.llamadas+"</div>"+
                "<div class='table-cell'>"+data.tot.ateporage+"</div>"+
                "<div class='table-cell'>"+data.tot.promllam+"</div>"+
                "<div class='table-cell'>"+data.tot.promacw+"</div>"+
                "<div class='table-cell'>"+data.tot.llamaban+"</div>"+
                "<div class='table-cell'>"+data.tot.llamsalida+"</div>"+
                "<div class='table-cell'>"+data.tot.promllamsal+"</div>"+
                "<div class='table-cell'>"+data.tot.promllament+"</div>"+
                "<div class='table-cell'>"+data.tot.porcontes+"%</div>"+
                "<div class='table-cell'>"+data.tot.llamaba3seg+"</div>"+
                "<div class='table-cell'>"+data.tot.nivserv+"%</div></div>\n";

                $("#leyend").html("<div class='row'><div class='col'><ul>"+
                "<li>ASA (tiempo medio de atención): Tiempo medio en segundos, utilizado para responder la llamada.</li>"+
                "<li>ABA (razón de abandono): Porcentaje de llamadas abandonadas mientras esperaban recibir atención.</li>"+
                "<li>AHT (tiempo de respuesta): Tiempo utilizado para completar una tarea determinada.</li>"+
                "<li>SL  (factor del tiempo de servicio): Porcentaje de llamadas respondidas en un plazo de <strong>"+data.data[0].seg+"</strong> segundos.</li></ul></div></div>");
            }
        $("#acumulado").html(html);
    },"json");
};
