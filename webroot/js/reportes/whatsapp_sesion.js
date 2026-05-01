let repwases = {
    rpp: 20,
    dndc: ['id', 'id_campaign'], // Columnas que no quiero mostrar, Default no quiero el id
    cedulas: {},
    cedulas_info: {},
    getpag: function(cual = 0) {
        $("#spinnerModal").modal("show");
        $("#repo, #paginacion").html("");
        $("#pag").val(cual);
        postdata = $("#repoform").serialize();
        postdata += "&rpp="+this.rpp;
        $.post(site_url+'reportes/data', postdata, function(data) {
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                if (data.data && data.data.length>0) {
                    let html = "<div class='table table-striped'><div class='table-header-group'>";
                    data.campos.forEach(function(row, i){
                        if (!repwases.dndc.includes(row.toLowerCase())) {
                            html += "<div class='table-cell'>"+data.tits[i]+"</div>\n";
                        }
                    });
                    html += "</div>";
                    data.data.forEach(function(row,key) {
                        html += "<div class='table-row'>\n";
                        data.campos.forEach(function(row2){
                            if (!repwases.dndc.includes(row2.toLowerCase())) {
                                let wrapclass = ("undefined" != typeof wrapable && wrapable.includes(row2.toLowerCase())) ? " wrapable" : "";
                                html += "<div class='table-cell" + wrapclass + "'>"+row[row2]+"</div>\n";
                            }
                        })
                        html += "</div>\n";
                    });
                    if(data.totales !== false && data.totales !== undefined){
                        html += "<div class='table-row totales'>\n";
                        for (key in data.totales) {
                            if (typeof data.totales[key] !== 'function') {
                                html += "<div class='table-cell font-weight-bold'>"+data.totales[key]+"</div>\n";
                            }
                        }
                        html += "</div>\n";
                    }
                    $("#repo").html(html);
                }
                paginacion(data.pag, data.cuenta, data.rpp, data.data.length, "paginacion", "repwases.getpag");
            }
            $("#spinnerModal").modal("hide");
        },"json")
        .fail(function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    getCedula: function(cam, id) {
        $("#spinnerModal").modal("show");
        $.post(site_url+'calidad/traercedulawhats', {cam: cam}, function(data) {
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
                setTimeout(function(){
                    $("#evalModal").modal("hide");
                }, 400);
            } else {
                repwases.cedulas[cam] = data.fields;
                repwases.cedulas_info[cam] = data.quality;
                repwases.showCedula(cam, id);
            }
            $("#spinnerModal").modal("hide");
        },"json")
        .fail(function(data) {
            $("#spinnerModal").modal("hide");
            setTimeout(function(){
                $("#evalModal").modal("hide");
            }, 400);
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    showCedula: function(cam, id) {
        if (typeof this.cedulas[cam] == 'undefined') {
            salida = "No hay formulario activo para ésta campaña.";
        } else {
            salida = "";
            $("#quality_name").html(this.cedulas_info[cam].name);
            this.cedulas[cam].forEach(function(fila){
                if (fila.question == 'Comentario') {
                    salida += '<tr>'+
                        '<td>'+fila.question+'</td>'+
                        '<td colspan="2">'+
                            '<textarea name="'+fila.id+'" class="form-control" maxlength="600" rows="4"></textarea>'+
                        '</td>'+
                    '</tr>';
                } else {
                    salida += '<tr>'+
                        '<td>'+fila.question+'</td>'+
                        '<td>'+fila.weight+'</td>'+
                        '<td>'+
                            '<div class="custom-control custom-checkbox">'+
                                '<input type="checkbox" class="custom-control-input evalpoint" value="'+fila.weight+
                                    '" id="customCheck'+fila.id+'" name="'+fila.id+'">'+
                                '<label class="custom-control-label" for="customCheck'+fila.id+'"></label>'+
                            '</div>'+
                        '</td>'+
                    '</tr>';
                }
            });
            $("#eval_id").val(id);
        }
        $("#calidadbody").html(salida);
        this.getSesion(id);
    },
    getSesion: (sid) => {
        $("#spinnerModal").modal("show");
        $.post(site_url+'whatsapp/traersesconv', {sid}, function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                $("#wamsgs").html(data.conver);
                $("#m_agente").html(data.session.agente);
                $("#m_numero").html(data.session.numero);
                $("#m_fecha").html(data.session.fecha+'<br/>'+data.session.inicio);
            }
        },"json")
        .fail(function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    }
}

$(document).ready(function() {
    repwases.getpag();
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
        onSelect: function(){
            repwases.getpag();
        }
    });
    $(document).on("change", "select", function() {
        if($(this).attr("id")=="elirpp") {
            repwases.rpp = $("#elirpp").val();
        }
        repwases.getpag();
    });
    $(document).on("keydown", "input.nosend", function(e){
        let keycode = (e.keyCode ? e.keyCode : (e.which ? e.which : e.key));
        if(keycode == 13) {
            e.preventDefault();
            repwases.getpag();
        }
    });
    $(document).on("click", ".page-link", function(e) {
        e.preventDefault();
        repwases.getpag($(this).data("pag"));
    });
    $(document).on("click", ".lanzamodal", function(){
        $("#evaltotal").text("0");
        let id  = $(this).data("id");
        let cam = $(this).data("cam");
        let cto = $(this).data("cto");
        $("#wacontactname").html(cto);
        $("#wasesid").html(id);
        $("#wamsgs").html("");
        if (typeof repwases.cedulas[cam] == 'undefined') {
            repwases.getCedula(cam, id);
        } else {
            repwases.showCedula(cam, id);
        }
    });
    $(document).on("click", ".evalpoint", function(){
        let total = parseInt($("#evaltotal").text());
        let este = parseInt($(this).val());
        if ($(this).is(":checked")) {
            total += este;
        } else {
            total -= este;
        }
        $("#evaltotal").text(total);
    });
});
