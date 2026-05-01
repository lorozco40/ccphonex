const formbo = { // Form Basic Object
    fid: 0, // ID del formulario activo o seleccionado
    pag: 0, // Página actual
    rpp: 10, // Registros por página
    getData: function () {
        $("#spinnerModal").modal("show");
        $.post(site_url + "form/lista", {pag: formbo.pag, rpp: formbo.rpp}, function (data) {
            let html = "";
            data.data.map(reg => {
                html += "<form class='row borrable'><input type='hidden' name='id' value='" +
                    reg.id + "'><input type='hidden' name='type' value='0'>" +
                    "<input type='hidden' name='active' value='0'><div class='table-cell'>" +
                    "<select class='form-control' name='campaign'>";
                let camsel = "";
                data.campanas.map(cam => {
                    let estesel = (cam.id == reg.campaign) ? " selected:selected" : "";
                    camsel += "<option value='" + cam.id + "'" + estesel + ">" + cam.name + "</option>";
                });
                html += camsel;
                html += "</select></div><div class='table-cell'>" +
                    "<input class='form-control' name='name' value='" + reg.name + "'></div>" +
                    "<div class='table-cell'><input type='checkbox' name='type' value='1' class='form-control check'" +
                    (reg.type == 1) ? " checked:checked" : "" + " /></div>" +
                    "<div class='table-cell'><input type='checkbox' name='active' value='1' class='form-control check'" +
                    (reg.active == 1) ? " checked:checked" : "" + " /></div>" +
                    "<div class='table-cell'><button class='btn btn-info fupd'>Actualizar</button>" +
                    "<a class='btn btn-secondary ml-3' href='" + sit_url + "form/campos/" + reg.id + "'>Detalle</a>" +
                    "<button class='btn btn-danger ml-3 fdel'>Eliminar</button>" +
                    "</div></form>";
            })
            $("#formContent").append(html);
            paginacion(data.pag, data.tot, data.rpp, data.regs);
            $("#spinnerModal").modal("hide");
        }, "json")
        .fail(function(data){
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    update: function (data) {
        $("#spinnerModal").modal("show");
        $.post(site_url + "form/actualiza", {data}, function (data) {
            formbo.pag = 0;
            getData();
            $("#spinnerModal").modal("hide");
            toastmsg(data.msg, data.tipo);
        })
        .fail(function(data){
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    delete: function () {
        $("#spinnerModal").modal("show");
        $.post(site_url + "form/eliminar", {fid: formbo.fid}, function (data) {
            formbo.pag = 0;
            getData();
            $("#spinnerModal").modal("hide");
            toastmsg(data.msg, data.tipo);
        })
        .fail(function(data){
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
};

$(document).ready(function () {
    formbo.getData();
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        formbo.pag = $(this).data('pag');
        getData();
    });
    $(document).on("click", ".fupd", function(e) {
        e.preventDefault();
        let data = $(this).closest("form").serialize();
        formbo.update(data);
    });
    $(document).on("click", ".fdel", function(e) {
        e.preventDefault();
        formbo.fid = $(this).data("fid");
        formbo.delete();
    });
});
