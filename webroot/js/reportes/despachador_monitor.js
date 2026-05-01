var postdata;
var traerdata;
$(document).ready(function() {
    traerdatos(true);
    traerdata = setInterval(function(){traerdatos()}, 5000);
});
$(document).on("change", "#id_desp", function() {
    traerdatos(true);
});

function traerdatos(spinner = false) {
    $("#totales").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $("#tipi").html("");
    if ($("#id_desp").val() == '' || $("#id_desp").val() == null) {
        $("#totales").html("<tr><td>Sin datos con esos parámetros.</td></tr>");
    } else {
        if(spinner) $("#spinnerModal").modal("show");
        $.post(site_url+"despachador/monitor_data", {id_desp: $("#id_desp").val()}, function(data) {
            postdata = data;
            $("#di-tipo").text(data.disp.autodial);
            $("#di-camp").text(data.disp.campana);
            $("#di-mult").text(data.disp.multi);
            if (data.disp.running==0) {
                $("#despStatus").removeClass("card-success").addClass("card-danger");
                $("#despStatus h5").text("Detenido");
            } else {
                $("#despStatus").removeClass("card-danger").addClass("card-success");
                $("#despStatus h5").text("Activo");
            }
            $("#despAgentesOcupados p").text(data.ocu);
            $("#despAgentesLibres p").text(data.desocu);
            $("#despAgentesLoged p").text(data.loged);
            $("#da-totreg, #pr-da-totreg").text(data.data.totreg);
            $("#da-fin").text(data.data.cerradas);
            $("#pr-da-fin").css("width", (data.data.cerradas/data.data.totreg)*100+'%');
            $("#da-nue").text(data.data.sintocar);
            $("#pr-da-nue").css("width", (data.data.sintocar/data.data.totreg)*100+'%');
            $("#da-despl").text(data.data.despliegues);
            $("#pr-da-despl").css("width", (data.data.despliegues/data.data.totreg)*100+'%');
            $("#da-par").text(data.data.parcial);
            $("#pr-da-par").css("width", (data.data.parcial/data.data.totreg)*100+'%');
            $("#da-open").text(data.data.abiertas);
            $("#pr-da-open").css("width", (data.data.abiertas/data.data.totreg)*100+'%');
            html = '';
            data.tipi.forEach(function(row){
                eti = (row.qualif == '') ? 'Sin tipificar' : row.qualif;
                wid = row.total / data.data.totreg * 100 + '%';
                html += '<div class="table-row borrable">' +
                    '<div class="table-cell">'+row.total+'</div>' +
                    '<div class="table-cell">'+eti+'</div>' +
                    '<div class="table-cell">' +
                        '<div class="progress">' +
                            '<div class="progress-bar" id="pr-ti-no" role="progressbar" aria-valuenow="'+wid+'" aria-valuemin="0" aria-valuemax="100" style="width:'+wid+'"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            $("#ti-table").html(html);
            html = '';
            if (typeof data.user !== 'undefined' && data.user.length > 0) {
                data.user.forEach(function(row){
                    html += "<div class='table-row borrable'><div class='table-cell' style='color:" + row.color + "'>";
                    switch (row.estatus) {
                        case 'acw':
                            html += '<i class="fas fa-table"></i></div>';
                            break;
                        case "comida":
                            html += '<i class="fas fa-utensils"></i></div>';
                            break;
                        case "retro":
                            html += '<i class="fas fa-chalkboard"></i></div>';
                            break;
                        case "sanitario":
                            html += '<i class="fas fa-toilet-paper"></i></div>';
                            break;
                        case "break":
                            html += '<i class="fas fa-coffee"></i></div>';
                            break;
                        case "Disponible":
                            html += '<i class="fas fa-user" ></i></div>';
                            break;
                        case "En llamada":
                            html += '<i class="fas fa-phone"></i></div>';
                            break;
                        default:
                            html += '<i class="fas fa-user-alt-slash"></i></div>';
                    }
                    html += '<div class="table-cell">' + row.agente +
                        '</div><div class="table-cell">' + row.ext +
                        '</div><div class="table-cell">' + row.estatus +
                        '</div><div class="table-cell">' + row.act +
                        '</div><div class="table-cell">' + row.acttime +
                        '</div></div>';
                });
            }
            $("#tablagentes .borrable").remove();
            $("#tablagentes").append(html);
            if (typeof data.cola !== 'undefined') {
                $("#co-encola").text(data.cola.wait);
                $("#despEnCola").css('display', 'flex');
            } else {
                $("#despEnCola").css('display', 'none');
            }
            html = '<ul>';
            data.cond.forEach(function(row){
                accion = (row.accion == '1') ? ', Iniciar' : ((row.accion=='2') ? ', Detener' : '');
                camcond = (row.camcond != '') ? ', '+row.camcond : '';
                html += "<li>" + row.hora + accion + camcond + "</li>";
            });
            html += '</ul>';
            $("#di-cond").html(html);
            $("#spinnerModal").modal("hide");
        }, "json")
        .fail(function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    }
};
