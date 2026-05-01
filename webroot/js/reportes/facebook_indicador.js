var pag = $("#pag").val();
var min = $("#min").val();
var max = $("#max").val();
var agentes = $("#agentes option:selected").val();
var contacto = $("#contacto option:selected").val();

$(document).ready(function() {
    pag = 0;
    facebook_indicador();
});

$(document).on("change", "#min, #max, #agentes, #contacto", function(){
    pag = 0;
    min = $("#min").val();
    max = $("#max").val();
    agentes = $("#agentes option:selected").val();
    contacto = $("#contacto option:selected").val();
    facebook_indicador();
});

$(document).on("click", ".page-link", function(e) {
    e.preventDefault();
    pag = ($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG;
    facebook_indicador();
});

function facebook_indicador() {
    $("#facebookind").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $(".parad-none").addClass("d-none").removeClass('parad-none');
    $.post(site_url+"facebook/facebook_indicador", {min: min, max: max, agentes: agentes, contacto: contacto, page: pag}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#facebookind").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>Fecha</div><div class='table-cell'>Agente</div><div class='table-cell'>Contacto</div>"+
                "<div class='table-cell'>Mensajes</div><div class='table-cell'>Entrante</div><div class='table-cell'>Saliente</div></div>";

                data.data.forEach(function(row) {
                    html += "<div class='table-row'>"+
                    "<div class='table-cell'>"+row.fecha+"</div>"+
                    "<div class='table-cell'>"+row.agente+"</div>"+
                    "<div class='table-cell'>"+row.contacto+"</div>"+
                    "<div class='table-cell'>"+row.mensaje+"</div>"+
                    "<div class='table-cell'>"+row.entrante+"</div>"+
                    "<div class='table-cell'>"+row.saliente+"</div></div>\n";
                });

                html += "<div class='table-row' style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;'>"+
                "<div class='table-cell'></div><div class='table-cell'></div>"+
                "<div class='table-cell'>Total</div>"+
                "<div class='table-cell'>"+data.total[0].mensaje+"</div>"+
                "<div class='table-cell'>"+data.total[0].entrante+"</div>"+
                "<div class='table-cell'>"+data.total[0].saliente+"</div></div>\n";

                mos = ((pag+REGS_POR_PAG)>data.cuenta) ? data.cuenta : pag+REGS_POR_PAG;
                $("#inireg").text(pag+1);
                $("#finreg").text(mos);
                $("#totreg").text(data.cuenta);
                $("#pagination").html(data.pagination);
                $(".d-none").addClass("parad-none").removeClass('d-none');
            }
        $("#facebookind").html(html);
    },"json");
};
