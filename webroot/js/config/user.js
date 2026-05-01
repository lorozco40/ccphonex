var pag = 0;
var reg = 0;
var rpp = 20;
var obregs = {};
var bus = '';

$(document).ready(function(){
    getpag();
    $(document).on("click", "#nuser", function(){
        $("#btnPass").show();
        $("#formuser").trigger("reset");
        $("#formuser input[name=id]").val(0);
        $("#formuser #caminput").show();
        $("#formuser #actinput").hide();
        $("#userModal").modal("show");
    });
    $(document).on("click", ".eduser", function(){
        var id = $(this).data("id");
        $("#btnPass").hide();
        $("#formuser #caminput").hide();
        $("#formuser #actinput").show();
        Object.entries(obregs[id]).forEach(([i, val]) => {
            $("#formuser input[type=text][name="+i+"]").val(val);
            $("#formuser input[type=hidden][name="+i+"]").val(val);
            $("#formuser input[type=email][name="+i+"]").val(val);
            $("#formuser select[name="+i+"]").val(val);
            cheko = (val=='0') ? false : true;
            $("#formuser input[type=checkbox][name="+i+"]").prop('checked',cheko);
        });
        $("#formuser input[name=pass]").val("");
        $("#userModal").modal("show");
    });
    $(document).on("click", ".peruser", function(){
        $("#spinnerModal").modal("show");
    });
    $(document).on('submit', "#buser", function(e){
        e.preventDefault();
        bus = $("#buser input[type=text]").val();
        pag = 0;
        getpag();
    });
    $(document).on("click", ".deslog", function(e){
        e.preventDefault();
        $("#spinnerModal").modal("show");
        $.post(site_url+'usuarios/desloguear', {id:$(this).data('id')}, function(data){
            $("#spinnerModal").modal("hide");
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                toastmsg(data, "success");
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
    })
    $(document).on("submit", "#formuser", function(e){
        e.preventDefault();
        data = new FormData();
        data.append("id", $("#formuser [name=id]").val());
        data.append("name", $("#formuser [name=name]").val());
        data.append("last", $("#formuser [name=last]").val());
        data.append("user", $("#formuser [name=user]").val());
        data.append("pass", $("#formuser [name=pass]").val());
        data.append("perfil", $("#formuser [name=perfil]").val());
        data.append("campana", campanas_txt());
        data.append("extension", $("#formuser [name=extension]").val());
        //Enviamos el valor de active solo si esta marcado
        if($("#formuser [name=active]").is(':checked')){
            data.append("active", 'on');
        }
        guardar(data);
    });
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        pag = $(this).data('pag');
        getpag();
    });
    $(document).on("change", "#elirpp", function(){
        pag = 0;
        rpp = $(this).val();
        getpag();
    });
    $(document).on("change", "select[name=cam]", function(){
        pag = 0;
        getpag();
    });
});

function campanas_txt() {
    let select = document.getElementById('campanas');
    let valoresSeleccionados = [];
    // Iterar sobre todas las opciones y agregar las seleccionadas a un array
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].selected) {
            valoresSeleccionados.push(select.options[i].value);
        }
    }
    let cadenaValores = valoresSeleccionados.join(',');

    return cadenaValores;
}

function guardar(data) {
    $("#spinnerModal").modal("show");
    changeToPasword("inputPass", "iconPass");
    $.ajax({
        url: site_url+'usuarios/guardar',
        type: 'POST',
        processData:false,
        contentType:false,
        data: data,
    })
    .done(function(data){
        $("#spinnerModal").modal("hide");
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            $("#userModal").modal("hide");
            toastmsg(data, "success");
            getpag();
        }
    })
    .fail(function(data) {
        $("#spinnerModal").modal("hide");
        if (typeof data.responseJSON.error !== "undefined") {
            toastmsg(data.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}

function mostrarContrasena(idInput = '', idIcon = '') {
    const input = $("#" + idInput);
    const icon = $("#" + idIcon);
    input.attr("type", "text");
    icon.removeClass("fa-eye-slash");
    icon.addClass("fa-eye");
    setTimeout(() => {
        changeToPasword(idInput, idIcon);
    }, 3000);
}

function changeToPasword(idInput = '', idIcon = '') {
    const input = $("#" + idInput);
    const icon = $("#" + idIcon);
    input.attr("type", "password");
    icon.removeClass("fa-eye");
    icon.addClass("fa-eye-slash");
}

function getpag(cual = 0) {
    $("#spinnerModal").modal("show");
    pag = (cual != 0) ? cual : pag;
    cam = $("select[name=cam]").val();
    $.get(site_url+'usuarios/lista', {pag: pag, lim: rpp, bus: bus, cam: cam}, function(data) {
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            $("#liuser .borrable").remove();
            reg = data.regs;
            var html = "";
            if (data.data && data.data.length>0) {
                data.data.forEach((row,key) => {
                    obregs[row.id] = row;
                    boton = "dark";
                    able  = " disabled";
                    if (row.uid!=0) {
                        boton = "success";
                        if (agente.permiso.includes('usuarios/desloguear')) able  = "";
                    }
                    active = (row.active==1) ? 'Si' : 'No';
                    html += "<div class='table-row borrable'>" +
                        "<div class='table-cell'><button data-id='"+row.id+"' type='button' class='btn btn-"+boton+" deslog'"+able+">&nbsp;</button></div>" +
                        "<div class='table-cell'>"+row.extension+"</div>" +
                        "<div class='table-cell'>"+row.nombre+"</div>" +
                        "<div class='table-cell'>"+row.perfil+"</div>" +
                        "<div class='table-cell'>"+row.user+"</div>" +
                        "<div class='table-cell wrapable'>"+camnames(row.campanas, cams)+"</div>" +
                        "<div class='table-cell'>"+active+"</div>" +
                        "<div class='table-cell'>";
                            if(agente.permiso.includes('usuarios/actualizar')) {
                                html += "<button type='button' class='btn btn-dark eduser' data-id='"+row.id+"'>Editar</button>";
                            }
                    html += "</div><div class='table-cell'>";
                    if(agente.permiso.includes('usuarios/permisos')) {
                        html += "<a href='"+site_url+"usuarios/permisos/"+row.id+"' class='btn btn-primary peruser' data-id='"+row.id+"'>Permisos</a>";
                    }
                    html += "</div></div>";
                });
                $("#liuser").append(html);
            }
            paginacion(pag, reg, rpp, data.data.length);
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
}

function camnames(ids, cams) {
    var arrids = ids.split(",");
    var arrnam = arrids.map(function(idcam, i){
        return cams[idcam];
    });
    arrnam = arrnam.filter(function(elem){
        return elem !== undefined;
    })
    return arrnam.join(", ");
}
