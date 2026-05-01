var pag = $("#pag").val();
var min = $("#min").val();
var max = $("#max").val();
var agentes = $("#agentes option:selected").val();

$(document).ready(function() {
    pag = 0;
    datostwitter();
});

$(document).on("change", "#min, #max, #agentes", function(){
    pag = 0;
    min = $("#min").val();
    max = $("#max").val();
    agentes = $("#agentes option:selected").val();
    datostwitter();
});

$(document).on("click", ".page-link", function(e) {
    e.preventDefault();
    pag = ($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG;
    datostwitter();
});

function datostwitter() {
    $("#twitter").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $(".parad-none").addClass("d-none").removeClass('parad-none');
    $.post(site_url+"twitter/datostwitter", {min: min, max: max, agentes: agentes, page: pag}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#twitter").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>Recepción</div><div class='table-cell'>Agente</div><div class='table-cell'>Remitente</div>"+
                "<div class='table-cell'>Lugar</div><div class='table-cell'>Mensaje</div><div class='table-cell'>Asignación</div>"+
                "<div class='table-cell'>Inicio</div><div class='table-cell'>Espera</div><div class='table-cell'>Respuesta</div>"+
                "<div class='table-cell'>Duración</div></div>";

                data.data.forEach(function(row) {
                    html += "<div class='table-row'>"+
                    "<div class='table-cell'>"+row.fechRecep+"</div>"+
                    "<div class='table-cell'>"+row.agente+"</div>"+
                    "<div class='table-cell'>"+row.remitente+"</div>"+
                    "<div class='table-cell'>"+row.lugarenvio+"</div>"+
                    "<div class='table-cell'>"+row.mensaje+"</div>"+
                    "<div class='table-cell'>"+row.fechAsign+"</div>"+
                    "<div class='table-cell'>"+row.fechStart+"</div>"+
                    "<div class='table-cell'>"+row.espera+"</div>"+
                    "<div class='table-cell'>"+row.fechResp+"</div>"+
                    "<div class='table-cell'>"+row.duracion+"</div></div>\n";
                })
                
                mos = ((pag+REGS_POR_PAG)>data.cuenta) ? data.cuenta : pag+REGS_POR_PAG;
                $("#inireg").text(pag+1);
                $("#finreg").text(mos);
                $("#totreg").text(data.cuenta);
                $("#pagination").html(data.pagination);
                $(".d-none").addClass("parad-none").removeClass('d-none');
            }
        $("#twitter").html(html);
    },"json");
};
