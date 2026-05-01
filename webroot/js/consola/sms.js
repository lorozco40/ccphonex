var permit = 160;

$(document).ready(function() {
    $("#input_msg").keyup(function() {
        usado = $(this).val().length;
        resto = permit - usado;
        $("#resto").html(resto);
        if (resto == 0) {
            toastmsg("Limite alcanzado, ya no quedan caracteres.", "danger");
        }
    });
});

$(document).on("click", "#btn_traeplatinlla", function(){
    traeplantilla();
});

$(document).on("click", "#limpiasms", function(e) {
    e.preventDefault();
    $("#input_msg").val("");
    $("#input_msg").keyup();
});

$(document).on("click", "#btn_enviarsms", function(e) {
    e.preventDefault();
    let num = $('#input_num').val();
    let msg = $('#input_msg').val();
    if (num.length != 10 || msg.length <3) {
        toastmsg('Por favor llena todos los campos!', "danger");
    } else {
        $.post(site_url+'sms/enviar', {
            num: num,
            msg: msg,
        }, function(respuesta) {
            toastmsg(respuesta.msg, respuesta.action);
        }, 'json')
        .fail(function() {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        });
    }
});

$(document).on("click", "#btnNuevaPlantillaSMS", function() {
    $("#spinnerModal").modal("show");
    $("#plantillaModalLabelSMS").text("Nueva Plantilla");
    $("select #selectCampanasModalSMS").val('');
    /*
        *ESPECIFICO EL SELECT POR QUE EL INPUT HIDDEN TAMBIEN SE LLAMA ASI
        *COLOCO EL VALOR DEL SELECT EN EL PRIMERO (-SELECCIONE-) CUANDO TIENE VARIAS CAMPAÑAS ASIGNADAS EL USUARIO
        *SI ES UN INPUT HIIDEN NO ES NECESARIO REINICIAR EL VALOR PUESTO SOLO TIENE UNA CAMPAÑA ASIGNADA
    */
    $("#textAreaModalSMS").val('');
    $("#nameModalSMS").val('');
    $("#plantillaModalSMS").modal("show");
    $("#spinnerModal").modal("hide");
});

$(document).on("click", ".btnActualizarPlantillaSMS", function() {
    var idPlantilla = $(this).data("id");
    var textAreaSMS = $("#textArea"+idPlantilla+"SMS").val();

    if( idPlantilla != "" ) {
        $("#spinnerModal").modal("show");
        $.post(site_url+'sms/pactu', {
            id:	   idPlantilla,
            valor: textAreaSMS
        }, function(respuesta) {
            toastmsg(respuesta.msg, respuesta.action);
        }, 'json')
        .fail(function() {
            toastmsg("Algo ocurrió al actualizar la plantilla.", "danger");
        });
        $("#spinnerModal").modal("hide");
    }else{
        toastmsg("Seleccione la plantilla.", "danger");
    }
});

$(document).on("click", ".btnUsarPlantillaSMS", function() {
    var idPlantilla = $(this).data("id");
    var textAreaSMS = $("#textArea"+idPlantilla+"SMS").val();
    let n = textAreaSMS.length;
    if( n > permit ) 
        textAreaSMS = textAreaSMS.substring(0, permit);
    $("#input_msg").val(textAreaSMS);
    $("#input_msg").keyup();
});

$(document).on("click", ".btnEliminarTemplateSMS", function() {
    var idPlantilla = $(this).data("id");
    var textAreaSMS = $("#textArea"+idPlantilla+"SMS").val();
    if( confirm("Esta seguro de eliminar esta plantilla?\n\""+textAreaSMS+"\"") ) {

        $("#spinnerModal").modal("show");
        $.post(site_url+'sms/pborrar', {
            id : idPlantilla
        }, function(respuesta) {
            toastmsg(respuesta.msg, respuesta.action);
            $("#"+idPlantilla+"PlantillaSMS").remove();
        }, 'json')
        .fail(function() {
            toastmsg("Algo ocurrió al eliminar la plantilla.", "danger");
        });
        $("#spinnerModal").modal("hide");
    }
});

$(document).on("click", "#btnGuardarPlantillaSMS", function() {
    var idCampana   = $.trim($("#selectCampanasModalSMS").val());
    var textAreaSMS = $.trim($("#textAreaModalSMS").val());
    var nameSMS     = $.trim($("#nameModalSMS").val());

    if( idCampana != "" && textAreaSMS != "" && nameSMS != "" ) {
        $("#spinnerModal").modal("show");
        $.post(site_url+'sms/pnueva', {
            id_campaign: idCampana,
            valor: textAreaSMS,
            name: nameSMS
        }, function(respuesta) {
            if (respuesta.action == "info") agregaPlantillaHtmlSMS(respuesta.id);
            toastmsg(respuesta.msg, respuesta.action);
            $("select #selectCampanasModalSMS").val('');
            $("#textAreaModalSMS").val('');
        }, 'json')
        .fail(function() {

            toastmsg("Algo ocurrió al guardar la plantilla.", "danger");
        });
        $("#spinnerModal").modal("hide");
    } else {
        toastmsg("Faltan por llenar campos.", "danger");
    }
});

function traeplantilla() {
    toastmsg("Debes elegir una plantilla.", "danger");
}

function agregaPlantillaHtmlSMS(id) {
    let name     = $("#nameModalSMS").val();
    let textArea = $("#textAreaModalSMS").val();
    let html = '<div id="'+id+'PlantillaSMS" class="row mb-4">' +
                '<div class="col-6 col-sm-9">' +
                    '<label for="textArea'+id+'SMS">'+name+'</label>' +
                    '<textarea id="textArea'+id+'SMS" class="form-control" rows="4">'+textArea+'</textarea>' +
                '</div>' +
                '<div class="col-6 col-sm-3 d-flex align-items-center justify-content-center" >' +
                    '<button type="button" class="btn btn-dark ml-2 btnUsarPlantillaSMS" title="Usar" data-id="'+id+'"><i class="fas fa-hand-pointer"></i></button>' +
                    '<button type="button" class="btn btn-success ml-2 btnActualizarPlantillaSMS" title="Guardar" data-id="'+id+'"><i class="fas fa-save"></i></button>' +
                    '<button type="button" class="btn btn-danger ml-2 btnEliminarTemplateSMS" title="Eliminar" data-id="'+id+'"><i class="fas fa-trash"></i></button>' +
                '</div>' +
            '</div>';
    $("#divPlantillasSMS").append(html);
    if( $("#NoHayPlantillasSMS").length > 0 ) $("#NoHayPlantillasSMS").remove();
}
