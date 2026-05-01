let idEncAct = 0;

$(document).on("click", ".relis", function() {
    let rid = $(this).data("id");
    if (rid != idEncAct) {
        idEncAct = rid;
        $("#nurctv input[name=rid]").val(rid);
        traerctv(rid);
    }
    $("#warareModal").modal("show");
});

$(document).on("submit", "#nurctv", function(e) { // Actualizar Ractivo
    e.preventDefault();
    let data = $(this).serialize();
    locreq("warate/saverctv", data);
});

$(document).on("click", ".updrctv", function() {
    let data = $(this).closest("form").serialize();
    locreq("warate/saverctv", data);
});

$(document).on("click", ".darctv", function() {
    let data = $(this).closest("form").serializeArray();
    for (index = 0; index < data.length; ++index) {
        if (data[index].name == "tipo") {
            data[index].value = "0";
            break;
        }
    }
    data = jQuery.param(data);
    locreq("warate/saverctv", data);
});

function locreq(url, data) {
    $("#spinnerModal").modal("show");
    $.post(site_url+url, data, function(data) {
        if (data.error) {
            toastmsg(data.error, "danger");
        } else {
            toastmsg(data.msg);
        }
        traerctv(idEncAct);
    });
}

function traerctv(rid) {
    $("#spinnerModal").modal("show");
    $("#tbrctv .borrable").remove();
    $("#nurctv").trigger("reset");
    $.post(site_url+"warate/getrctv", {rid}, function (data) {
        let html = "";
        data.data.map(function(r,i) {
            let estetipo = (r.tipo == 1) ? "Numérico" : ((r.tipo == 2) ? "Texto" : "Desactivado");
            let reporte = (r.reporte == 1) ? 'checked' : '';
            if (estetipo == "Desactivado") {
                html += "<div class='table-row borrable'><div class='table-cell'>" +
                    estetipo + "</div><div class='table-cell text-center'>" +
                    "<input type='checkbox' class='form-control ' disabled/></div><div class='table-cell'>" +
                    r.reactivo + "</div><div class='table-cell'></div></div>";
            } else {
                html += "<form class='table-row borrable'>" +
                    "<div class='table-cell'>" +
                    "<input type='hidden' name='id' value='" + r.id + "' />" +
                    "<input type='hidden' name='tipo' value='" + r.tipo + "' />" +
                    "<input type='hidden' name='rid' value='" + r.id_wr + "' />" +
                    estetipo + "</div>" +
                    "<div class='table-cell'>" +
                    "<input type='checkbox' name='reporte' class='form-control mb-n3' "+reporte+" /></div>" +
                    "<div class='table-cell'>"+
                    "<textarea class='form-control' name='reactivo'>" + r.reactivo +
                    "</textarea></div><div class='table-cell'>" +
                    "<button type='button' class='btn btn-primary mx-2 updrctv' " +
                    "data-toggle='tooltip' title='Actualizar'>" +
                    "<li class='fa fa-save'></li></button>" +
                    "<button type='button' class='btn btn-danger ml-2 darctv' " +
                    "data-toggle='tooltip' title='Desactivar'>" +
                    "<li class='fa fa-trash'></li></button></div></form>";
            }
        });
        $("#tbrctv").append(html);
        $("#spinnerModal").modal("hide");
        $('[data-toggle="tooltip"]').tooltip();
    }, "json");
   
}
