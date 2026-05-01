var min = $("#min").val();
var max = $("#max").val();

$(document).ready(function() {
    videoindicador();
});

$(document).on("change", "#min, #max", function(){
    min = $("#min").val();
    max = $("#max").val();
    videoindicador();
});

function videoindicador() {
    $("#video_indicador").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $.post(site_url+"videollamada/videollamada_indicador", {min: min, max: max}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#video_indicador").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>Fecha</div><div class='table-cell'>Agente</div><div class='table-cell'>Videollamadas</div>"+
                "<div class='table-cell'>Terminadas</div><div class='table-cell'>Abandonadas</div><div class='table-cell'>Promedio de espera</div>"+
                "<div class='table-cell'>Mas larga</div></div>";

                data.data.forEach(function(row) {

                    html += "<div class='table-row'>"+
                    "<div class='table-cell'>"+row.fecha+"</div>"+
                    "<div class='table-cell'>"+row.agente+"</div>"+
                    "<div class='table-cell'>"+row.videollamada+"</div>"+
                    "<div class='table-cell'>"+row.terminada+"</div>"+
                    "<div class='table-cell'>"+row.abandonada+"</div>"+
                    "<div class='table-cell'>"+row.promedio_espera+"</div>"+
                    "<div class='table-cell'>"+row.mas_larga+"</div></div>\n";
                })
                html += "<div class='table-row' style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;'>"+
                "<div class='table-cell'></div>"+
                "<div class='table-cell'>Total</div>"+
                "<div class='table-cell'>"+data.tot.videollamada+"</div>"+
                "<div class='table-cell'>"+data.tot.terminada+"</div>"+
                "<div class='table-cell'>"+data.tot.abandonada+"</div>"+
                "<div class='table-cell'>"+data.tot.promedio_espera+"</div>"+
                "<div class='table-cell'>"+data.tot.mas_larga+"</div></div>\n";
            }
        $("#video_indicador").html(html);
    },"json");
};
