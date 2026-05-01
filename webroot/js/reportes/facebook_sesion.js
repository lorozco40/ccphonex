var pag = $("#pag").val();
var min = $("#min").val();
var max = $("#max").val();
var agentes = $("#agentes option:selected").val();
var contacto = $("#contacto option:selected").val();

$(document).ready(function() {
    pag = 0;
    facebook_sesion();
});

$(document).on("change", "#min, #max, #agentes, #contacto", function(){
    pag = 0;
    min = $("#min").val();
    max = $("#max").val();
    agentes = $("#agentes option:selected").val();
    contacto = $("#contacto option:selected").val();
    facebook_sesion();
});

$(document).on("click", ".page-link", function(e) {
    e.preventDefault();
    pag = ($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG;
    facebook_sesion();
});

function facebook_sesion() {
    $("#fbsesion").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $(".parad-none").addClass("d-none").removeClass('parad-none');
    $.post(site_url+"facebook/facebook_sesion", {min: min, max: max, agentes: agentes, contacto: contacto, page: pag}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#fbsesion").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>FB id</div><div class='table-cell'>Contacto</div><div class='table-cell'>Agente</div>"+
                "<div class='table-cell'>Asignación</div><div class='table-cell'>Espera</div><div class='table-cell'>Inicio</div>"+
                "<div class='table-cell'>Termino</div><div class='table-cell'>Duración</div><div class='table-cell'>Mensajes</div></div>";

                data.data.forEach(function(row) {
                    html += "<div class='table-row'>"+
                    "<div class='table-cell'>"+row.fb_id+"</div>"+
                    "<div class='table-cell'>"+row.name+"</div>"+
                    "<div class='table-cell'>"+row.agente+"</div>"+
                    "<div class='table-cell'>"+row.fechasig+"</div>"+
                    "<div class='table-cell'>"+row.promespera+"</div>"+
                    "<div class='table-cell'>"+row.fechstart+"</div>"+
                    "<div class='table-cell'>"+row.fechend+"</div>"+
                    "<div class='table-cell'>"+row.promduration+"</div>"+
                    "<div class='table-cell'>"+row.mensajes+"</div></div>\n";
                })

                html += "<div class='table-row' style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;'>"+
                "<div class='table-cell'></div><div class='table-cell'></div>"+
                "<div class='table-cell'></div><div class='table-cell'>Promedio</div>"+
                "<div class='table-cell'>"+data.total[0].promespera+"</div>"+
                "<div class='table-cell'></div><div class='table-cell'></div>"+
                "<div class='table-cell'>"+data.total[0].promduration+"</div>"+
                "<div class='table-cell'>"+data.total[0].mensajes+"</div></div>\n";

                mos = ((pag+REGS_POR_PAG)>data.cuenta) ? data.cuenta : pag+REGS_POR_PAG;
                $("#inireg").text(pag+1);
                $("#finreg").text(mos);
                $("#totreg").text(data.cuenta);
                $("#pagination").html(data.pagination);
                $(".d-none").addClass("parad-none").removeClass('d-none');

            }
        $("#fbsesion").html(html);
    },"json");
};
