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

$("#min, #max, #campana, #agentes, #tipo").on("change", function() {
    traerdatos();
});

function traerdatos() {
    $("#slin").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    var min = $("#min").val();
    var max = $("#max").val();
    var campana = $("#campana option:selected").val();
    var agentes = $("#agentes option:selected").val();
    var tipo = $("#tipo option:selected").val();
    $.post(site_url+"sl/slindata", $("#slaform").serialize(), function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#slin").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>Hora</div><div class='table-cell'>SL("+data.data[0].seg+"'s)</div><div class='table-cell'>ABA</div>"+
                "<div class='table-cell'>ASA</div><div class='table-cell'>AHT</div><div class='table-cell'>Recibidas</div>"+
                "<div class='table-cell'>Atendidas</div><div class='table-cell'>Abandonadas</div><div class='table-cell'>Llamada más larga</div>"+
                "<div class='table-cell'>Agentes por intervalo</div></div>";

                data.data.forEach(function(row){
                    html += "<div class='table-row'>"+
                    "<div class='table-cell'>"+row.hora+"</div>"+
                    "<div class='table-cell'>"+row.tsf+"%</div>"+
                    "<div class='table-cell'>"+row.aba+"%</div>"+
                    "<div class='table-cell'>"+row.asa+"</div>"+
                    "<div class='table-cell'>"+row.tat+"</div>"+
                    "<div class='table-cell'>"+row.llamadas+"</div>"+
                    "<div class='table-cell'>"+row.atendidas+"</div>"+
                    "<div class='table-cell'>"+row.abandonadas+"</div>"+
                    "<div class='table-cell'>"+row.larga+"</div>"+
                    "<div class='table-cell'>"+row.opers+"</div></div>\n";
                });

                html += "<div class='table-row' style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;'>"+
                "<div class='table-cell'>Total</div>"+
                "<div class='table-cell'>"+data.tot[0].tsf+"%</div>"+
                "<div class='table-cell'>"+data.tot[0].aba+"%</div>"+
                "<div class='table-cell'>"+data.tot[0].asa+"</div>"+
                "<div class='table-cell'>"+data.tot[0].tat+"</div>"+
                "<div class='table-cell'>"+data.tot[0].llamadas+"</div>"+
                "<div class='table-cell'>"+data.tot[0].atendidas+"</div>"+
                "<div class='table-cell'>"+data.tot[0].abandonadas+"</div>"+
                "<div class='table-cell'>"+data.tot[0].larga+"</div>"+
                "<div class='table-cell'>"+data.tot[0].opers+"</div></div>\n";

                $("#leyend").html("<div class='row'><div class='col'><ul>"+
                "<li>SL  (factor del tiempo de servicio): Porcentaje de llamadas respondidas en un plazo de <strong>"+data.data[0].seg+"</strong> segundos.</li>"+
                "<li>ABA (razón de abandono): Porcentaje de llamadas abandonadas mientras esperaban recibir atención.</li>"+
                "<li>ASA (tiempo medio de atención): Tiempo medio en segundos, utilizado para responder la llamada.</li>"+
                "<li>AHT (tiempo de respuesta): Tiempo utilizado para completar una tarea determinada.</li></ul></div></div>");
            }
        $("#slin").html(html);
    },"json");
};
