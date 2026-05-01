const repodinpag = {
    pag: 0,
    rpp: 20,
    reporte: 'repodin',
    modelo: 'repodin',
}
const repodin = {
    getData: () => {
        $("#spinnerModal").modal("show");
        $.post(site_url+'reportes/data', repodinpag, function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                var html = "No hay reportes para mostrar";
                if (data.data.length > 0) {
                    html = `<div class="table-header-group">
                        <div class="table-cell">Nombre</div>
                        <div class="table-cel">Formulario</div>
                        <div class="table-cel">Campo eje</div>
                        <div class="table-cel">Creado por</div>
                        <div class="table-cel">Desde</div>
                        <div class="table-cel">Activo</div>
                        <div class="table-cel">Acciones</div>
                    </div>`;
                    data.data.forEach(el => {
                        html += `<div class="table-row borrable">
                            <input type="hidden" name="id" value="${el.id}" />
                            <div class="table-cell">${el.name}</div>
                            <div class="table-cell">${el.form}</div>
                            <div class="table-cell">${el.indexf}</div>
                            <div class="table-cell">${el.creador}</div>
                            <div class="table-cell">${el.created_when}</div>
                            <div class="table-cell">${el.active}</div>
                            <div class="table-cell">
                                <button class="btn btn-primary">
                            </div>
                        </div>`
                    });
                }
                $("#contentList").html(html);
            }
        })
        .fail(function(data) {
            console.log(data);
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    getFormFields: function(fid) {
        $("#spinnerModal").modal("show");
        $.post(site_url+'repodin/formfields', {fid}, function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                var html = "<option>Error en la definición de campos del formulario</option>";
                if (data.length > 0) {
                    html = "";
                    console.log(data);
                    data.forEach(el => {
                        html += `<option value="${el.slug}">${el.nombre}</div>`
                    });
                }
                $("select[name=filtro],select[name=paraver").html(html);
            }
        })
        .fail(function(data) {
            console.log(data);
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    }
}

$(document).ready(function(){
    repodin.getData();
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        repodinpag.pag = $(this).data('pag');
        repodin.getData();
    });
    $(document).on("change", "#elirpp", function(){
        repodinpag.pag = 0;
        repodinpag.rpp = $(this).val();
        repodin.getData();
    });
    $(document).on("click", "#newrd", function(){
        var form = $("select[name=form]").val();
        repodin.getFormFields(form);
    });
    $(document).on("change","select[name=form]",function(){
        repodin.getFormFields($(this).val());
    });
});
