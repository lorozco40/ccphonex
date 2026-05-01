var pag = 0;
var reg = 0;
var rpp = 20;
var obregs = {};

$(document).ready(function(){
    getpag();
    $(document).on("click", "#nuemcta", function(){
        $("#emctaform").trigger("reset");
        $("#emctaform input[name=id]").val(0);
        $("#nombre-modal-cuenta").text("");
        $("#delete_firma").val(0);
        visibility_img(false);
        $("#emctaModal").modal("show");
    });
    // Validamos cada vez que ocurra un cambio en el input de la firma
    $('#signature_img').on('change', function() {
        let id = $("#emctaform :input[name=id]").val();
        if(id != 0) {
            if( obregs[id]['signature_img'] ) {
                $("#delete_firma").val(1);
            }
        }
    });
    $(document).on("click", ".edemcta", function(){
        var id = $(this).data("id");
        var name = obregs[id]["nombre"];
        name = name.length > 0 ? "- " + name + " -" : "";
        $("#nombre-modal-cuenta").text(name);
        $("#delete_firma").val(0);
        $("#emctaform").trigger("reset");
        Object.entries(obregs[id]).forEach(([i, val]) => {
            if( i == 'signature_img' ) {
                if( val != '' ) {
                    visibility_img(true);
                    $("#signature-img-ui").html(val)
                } else {
                    visibility_img(false);
                    $("#signature-img-ui").html('')
                }
            } else {
                $("#emctaform :input[name="+i+"]").val(val);
            }
        });
        $("#emctaModal").modal("show");
    });
    $(document).on("click", ".abrir-crm-modal", function(){
        var id         = $(this).data("id");
        var idform     = obregs[id]["in_tipo"];//$(this).data("id-formulario");
        var cid        = obregs[id]["id_campaign"];//$(this).data("cid");
        var name       = obregs[id]["nombre"];//$(this).data("name");
        var parametros = "cid="+cid+"&id="+id;
        buscarFormularios(parametros, idform);

        $("#nombre-modal-crm").text(name);
        Object.entries(obregs[id]).forEach(([i, val]) => {
            $("#crmform input[name="+i+"]").val(val);
            $("#crmform select[name="+i+"]").val(val);
        });

        $("#SeleccionarCrmModal").modal("show");
    });
    $(document).on("click", ".desemcta", function(){
        let data = new FormData()
        data.append("id", $(this).data("id"));
        data.append("uid", agente.id);
        data.append("key", agente.token);
        data.append("activa", 0);
        savecta(data, "PUT");
    });
    $(document).on("click", ".actemcta", function(){
        let data = new FormData()
        data.append("id", $(this).data("id"));
        data.append("uid", agente.id);
        data.append("key", agente.token);
        data.append("activa", 1);
        savecta(data, "PUT");
    });
    $(document).on("submit", "#emctaform", function(e){
        e.preventDefault();
        let data = new FormData(document.getElementById("emctaform"))
        let metodo = (data.get('id') == 0) ? "POST" : "PUT";
        data.append("key", agente.token);
        data.append("uid", agente.id);
        savecta(data, metodo);
    });
    $(document).on("click", ".elimcta", function(){
        let id = $(this).data("id");
        savecta(data);
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
    $(document).on("submit", "#crmform", function(e){
        e.preventDefault();
        asignarFormulario($("#crmform").serialize());
    });
});

function delete_img() {
    $("#delete_firma").val(1);
    visibility_img( false );
}

function visibility_img(ui = false) {
    if( ui === true ) {
        $(".img_ui").show();
        $(".img_input").hide();
    } else {
        $(".img_input").show();
        $(".img_ui").hide();
    }

}

function savecta(data, metodo = 'POST') {
    $("#spinnerModal").modal("show");
    $.ajax({
        url: 'https://'+bago_url+'/email/cuenta',
        type: metodo,
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,
    })
    .done(function(resp){
        if( metodo == "PUT" ) {
            if( data.get('email') == null && data.get('activa') == 1 ) {
                toastmsg("Cuenta activada", "success");
            }
            else if( data.get('email') == null && data.get('activa') == 0 ) {
                toastmsg("Cuenta desactivada", "success");
            } else {
                toastmsg("Cuenta actualizada", "success");
            }
        }
        if( metodo == "POST" ) {
            toastmsg("Cuenta creada", "success");
        }
        $("#emctaModal").modal("hide");
        getpag();
    })
    .fail(function(error) {
        $("#spinnerModal").modal("hide");
        if (typeof error.responseJSON.error !== "undefined") {
            toastmsg(error.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}

function getpag(cual = 0) {
    let uses = {
        1: "CRM Remitente",
        2: "Modulo Email"
    };
    pag = (cual != 0) ? cual : pag;
    $("#spinnerModal").modal("show");
    $.get('https://'+bago_url+'/email/cuenta', {pag: pag, lim: rpp, uid: agente.id, key: agente.token}, function(data) {
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            $("#TablaEmCtas .borrable").remove();
            reg = data.regs;
            var html = "";
            var cuenta = 0;
            data.data.forEach((row,key) => {
                obregs[row.id] = row;
                cuenta++;
                clase = (row.activa) ? "desemcta" : "actemcta";
                if (row.activa) {
                    activa = 'Si';
                    btndice = 'Desactivar';
                } else {
                    activa = 'No'
                    btndice = 'Activar';
                }
                html += "<div class='table-row borrable'>" +
                    "<div class='table-cell'>"+row.email+"</div>" +
                    "<div class='table-cell'>"+row.nombre+"</div>" +
                    "<div class='table-cell'>"+uses[row.use]+"</div>" +
                    "<div class='table-cell'>"+row.tipo+"</div>" +
                    "<div class='table-cell'>"+campanas[row.id_campaign]+"</div>" +
                    "<div class='table-cell'>"+row.in_servidor+":"+row.in_puerto+"</div>" +
                    "<div class='table-cell'>"+row.out_servidor+":"+row.out_puerto+"</div>" +
                    "<div class='table-cell'>"+activa+"</div>" +
                    "<div class='table-cell'>" +
                        "<button type='button' class='btn btn-dark "+clase+"' data-id='"+row.id+"'>"+btndice+"</button>" +
                    "</div>" +
                    "<div class='table-cell'><button type='button' class='btn btn-dark edemcta' data-id='"+row.id+"'>Editar</button></div>" +
                "</div>";
            });
            $("#TablaEmCtas").append(html);
            paginacion(pag, reg, rpp, cuenta);
        }
        $("#spinnerModal").modal("hide");
    },"json")
    .fail(function(data) {
        $("#spinnerModal").modal("hide");
        if (typeof data.responseJSON.error !== "undefined") {
            toastmsg(data.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}

function buscarFormularios(data, idform) {
    $("#spinnerModal").modal("show");
    $.ajax({
        url: site_url+'form/getbycam',
        type: 'POST',
        data: data,
        dataType: 'json',
    })
    .done(function(respuesta){
        if (typeof respuesta.error !== 'undefined') {
            $("#spinnerModal").modal("hide");
            toastmsg(respuesta.error, "danger");
        } else {
            $("#spinnerModal").modal("hide");
            html = "<option value="+''+">-- Elige --</option>";
            selected = "";
            respuesta.forEach(element => {
                selected = idform == element.id ? "selected" : "";
                html += "<option value='"+ element.id +"' "+selected+" >"+element.name+"</option>";
            });
            $(".in_tipo").empty();
            $(".in_tipo").append(html);
            buscarIntipoCuenta(data);
        }
    })
    .fail(function(respuesta) {
        $("#spinnerModal").modal("hide");
        if (typeof respuesta.responseJSON.error !== "undefined") {
            toastmsg(respuesta.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}

function asignarFormulario(data) {
    $("#spinnerModal").modal("show");
    $.ajax({
        url: site_url+'email/asignarFormulario',
        type: 'POST',
        data: data,
        dataType: 'json',
    })
    .done(function(data){
        $("#spinnerModal").modal("hide");
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            if( data.status == "Ok" ){
                $("#SeleccionarCrmModal").modal("hide");
                toastmsg(data.msg, "success");
                getpag();
            }else{
                toastmsg(data.msg, "danger");
            }
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

function buscarIntipoCuenta(data) {
    $('#crmform select[name="in_tipo"]').val("");
    $("#spinnerModal").modal("show");
    $.ajax({
        url: site_url+'email/buscarInTipoCuenta',
        type: 'POST',
        data: data,
        dataType: 'json',
    })
    .done(function(respuesta){
        if (typeof respuesta.error !== 'undefined') {
            $("#spinnerModal").modal("hide");
            toastmsg(respuesta.error, "danger");
        } else {
            $("#spinnerModal").modal("hide");
            $('#crmform select[name="in_tipo"]').val(respuesta.id_form);
            texto = $('#crmform select[name="in_tipo"]').find('option[value=' + respuesta.id_form + ']').text();
        }
    })
    .fail(function(respuesta) {
        $("#spinnerModal").modal("hide");
        if (typeof respuesta.responseJSON.error !== "undefined") {
            toastmsg(respuesta.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}
