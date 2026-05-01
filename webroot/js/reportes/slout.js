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

$("#min, #max, #tipo, #campana").on("change", function() {
    traerdatos();
});

function traerdatos() {
    $("#slout").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    var min = $("#min").val();
    var max = $("#max").val();
    var tipo = $("#tipo option:selected").val();
    $.post(site_url+"sl/sloutdata", $("#slaform").serialize(), function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#slout").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>Hora</div><div class='table-cell'>Llamadas</div><div class='table-cell'>Exitosas</div>"+
                "<div class='table-cell'>Abandono</div><div class='table-cell'>Tiempo ocupación</div><div class='table-cell'>AHT</div>"+
                "<div class='table-cell'>Llamada más larga</div><div class='table-cell'>Agentes por intervalo</div></div>";

                data.data.forEach(function(row){
                    html += "<div class='table-row'>"+
                        "<div class='table-cell'>"+row.hora+"</div>"+
                        "<div class='table-cell'>"+row.llamadas+"</div>"+
                        "<div class='table-cell'>"+row.exito+"</div>"+
                        "<div class='table-cell'>"+row.abandono+"</div>"+
                        "<div class='table-cell'>"+row.ocupacion+"</div>"+
                        "<div class='table-cell'>"+row.aht+"</div>"+
                        "<div class='table-cell'>"+row.larga+"</div>"+
                        "<div class='table-cell'>"+row.opers+"</div></div>\n";
                });
                html += "<div class='table-row' style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;'>"+
                "<div class='table-cell'>Total"+"</div>"+
                "<div class='table-cell'>"+data.tot[0].llamadas+"</div>"+
                "<div class='table-cell'>"+data.tot[0].exito+"</div>"+
                "<div class='table-cell'>"+data.tot[0].abandono+"</div>"+
                "<div class='table-cell'>"+data.tot[0].ocupacion+"</div>"+
                "<div class='table-cell'>"+data.tot[0].aht+"</div>"+
                "<div class='table-cell'>"+data.tot[0].larga+"</div>"+
                "<div class='table-cell'>"+data.tot[0].opers+"</div></div>\n";

                $("#leyend").html("<div class='row'><div class='col'><ul>"+
                "<li>AHT (tiempo de respuesta): Tiempo utilizado para completar una tarea determinada.</li></ul></div></div>");
            }
        $("#slout").html(html);
    },"json");
};
