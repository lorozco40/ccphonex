var pag = $("#pag").val();
var min = $("#min").val();
var max = $("#max").val();
var agentes = $("#agentes option:selected").val();

$(document).ready(function() {
    pag = 0;
    datostwitterind();
});

$(document).on("change", "#min, #max, #agentes", function(){
    pag = 0;
    min = $("#min").val();
    max = $("#max").val();
    agentes = $("#agentes option:selected").val();
    datostwitterind();
});

$(document).on("click", ".page-link", function(e) {
    e.preventDefault();
    pag = ($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG;
    datostwitterind();
});

function datostwitterind() {
    $("#twitterind").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $(".parad-none").addClass("d-none").removeClass('parad-none');
    $.post(site_url+"twitter/datostwitterind", {min: min, max: max, agentes: agentes, page: pag}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#twitterind").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>Fecha</div><div class='table-cell'>Agente</div><div class='table-cell'>Recibidos</div>"+
                "<div class='table-cell'>Respondidos</div><div class='table-cell'>Sin responder</div><div class='table-cell'>AHT Twitter</div>"+
                "<div class='table-cell'>Promedio de espera</div><div class='table-cell'>Espera más larga</div></div>";

                data.data.forEach(function(row) {
                    html += "<div class='table-row'>"+
                    "<div class='table-cell'>"+row.fecha+"</div>"+
                    "<div class='table-cell'>"+row.agente+"</div>"+
                    "<div class='table-cell'>"+row.recibidos+"</div>"+
                    "<div class='table-cell'>"+row.respondidos+"</div>"+
                    "<div class='table-cell'>"+row.sinresponder+"</div>"+
                    "<div class='table-cell'>"+row.aht+"</div>"+
                    "<div class='table-cell'>"+row.promespera+"</div>"+
                    "<div class='table-cell'>"+row.masespera+"</div></div>\n";
                });
                html += "<div class='table-row' style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;'>"+
                "<div class='table-cell'></div><div class='table-cell'>Total</div>"+
                "<div class='table-cell'>"+data.total[0].recibidos+"</div>"+
                "<div class='table-cell'>"+data.total[0].respondidos+"</div>"+
                "<div class='table-cell'>"+data.total[0].sinresponder+"</div>"+
                "<div class='table-cell'>"+data.total[0].aht+"</div>"+
                "<div class='table-cell'>"+data.total[0].promespera+"</div>"+
                "<div class='table-cell'>"+data.total[0].masespera+"</div></div>\n";

                $("#leyend").html("<div class='row'><div class='col'><ul>"+
                "<li>AHT (tiempo de respuesta): Tiempo utilizado para completar una tarea determinada.</li></ul></div></div>");

                mos = ((pag+REGS_POR_PAG)>data.cuenta) ? data.cuenta : pag+REGS_POR_PAG;
                $("#inireg").text(pag+1);
                $("#finreg").text(mos);
                $("#totreg").text(data.cuenta);
                $("#pagination").html(data.pagination);
                $(".d-none").addClass("parad-none").removeClass('d-none');
            }
        $("#twitterind").html(html);
    },"json");
};
