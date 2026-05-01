var pag = $("#pag").val();
var min = $("#min").val();
var max = $("#max").val();
var agentes = $("#agentes option:selected").val();
var contacto = $("#contacto option:selected").val();

$(document).ready(function() {
    pag = 0;
    facebook_detalle();
});

$(document).on("change", "#min, #max, #agentes, #contacto", function(){
    pag = 0;
    min = $("#min").val();
    max = $("#max").val();
    agentes = $("#agentes option:selected").val();
    contacto = $("#contacto option:selected").val();
    facebook_detalle();
});

$(document).on("click", ".page-link", function(e) {
    e.preventDefault();
    pag = ($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG;
    facebook_detalle();
});

function facebook_detalle() {
    $("#facebook").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $(".parad-none").addClass("d-none").removeClass('parad-none');
    $.post(site_url+"facebook/facebook_detalle", {min: min, max: max, agentes: agentes, contacto: contacto, page: pag}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#facebook").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>Recepción</div><div class='table-cell'>Agente</div><div class='table-cell'>FB id</div>"+
                "<div class='table-cell'>Remitente</div><div class='table-cell'>Mensaje</div><div class='table-cell'>Tipo</div>"+
                "<div class='table-cell'>Dirección</div><div class='table-cell'>Estatus</div></div>";

                data.data.forEach(function(row) {
                    html += "<div class='table-row'>"+
                    "<div class='table-cell'>"+row.recepcion+"</div>"+
                    "<div class='table-cell'>"+row.agente+"</div>"+
                    "<div class='table-cell'>"+row.numero+"</div>"+
                    "<div class='table-cell'>"+row.remitente+"</div>"+
                    "<div class='table-cell'>"+row.mensaje+"</div>"+
                    "<div class='table-cell'>"+row.fbtype+"</div>"+
                    "<div class='table-cell'>"+row.type+"</div>"+
                    "<div class='table-cell'>"+row.status+"</div></div>\n";
                })

                mos = ((pag+REGS_POR_PAG)>data.cuenta) ? data.cuenta : pag+REGS_POR_PAG;
                $("#inireg").text(pag+1);
                $("#finreg").text(mos);
                $("#totreg").text(data.cuenta);
                $("#pagination").html(data.pagination);
                $(".d-none").addClass("parad-none").removeClass('d-none');
            }
        $("#facebook").html(html);
    },"json");
};
