var pag = $("#pag").val();
var min = $("#min").val();
var max = $("#max").val();
var agentes = $("#agentes option:selected").val();

$(document).ready(function() {
    pag = 0;
    videodetalle();
});

$(document).on("change", "#min, #max, #agentes", function(){
    pag = 0;
    min = $("#min").val();
    max = $("#max").val();
    agentes = $("#agentes option:selected").val();
    videodetalle();
});

$(document).on("click", ".page-link", function(e) {
    e.preventDefault();
    pag = ($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG;
    videodetalle();
});

function videodetalle() {
    $("#video_detalle").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $(".parad-none").addClass("d-none").removeClass('parad-none');
    $.post(site_url+"videollamada/videollamada_detalle", {min: min, max: max, agentes: agentes, page: pag}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#video_detalle").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>Fecha</div><div class='table-cell'>Agente</div><div class='table-cell'>Ip</div>"+
                "<div class='table-cell'>Espera</div><div class='table-cell'>Inicio</div><div class='table-cell'>Fin</div>"+
                "<div class='table-cell'>Duración</div><div class='table-cell'>Estatus</div></div>";

                data.data.forEach(function(row) {

                    html += "<div class='table-row'>"+
                    "<div class='table-cell'>"+row.datetime_entry_queue+"</div>"+
                    "<div class='table-cell'>"+row.agente+"</div>"+
                    "<div class='table-cell'>"+row.callerid+"</div>"+
                    "<div class='table-cell'>"+row.duration_wait+"</div>"+
                    "<div class='table-cell'>"+row.datetime_init+"</div>"+
                    "<div class='table-cell'>"+row.datetime_end+"</div>"+
                    "<div class='table-cell'>"+row.duration+"</div>"+
                    "<div class='table-cell'>"+row.status+"</div></div>\n";
                })

                mos = ((pag+REGS_POR_PAG)>data.cuenta) ? data.cuenta : pag+REGS_POR_PAG;
                $("#inireg").text(pag+1);
                $("#finreg").text(mos);
                $("#totreg").text(data.cuenta);
                $("#pagination").html(data.pagination);
                $(".d-none").addClass("parad-none").removeClass('d-none');
            }
        $("#video_detalle").html(html);
    },"json");
};
