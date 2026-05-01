if( $("#hiddenCampanaSMS").length > 0 ) recargarPlantillasSMS( $("#hiddenCampanaSMS").val() );

$("#selectCampanaSMS").change(function() {
    var idCampaign = $(this).val();
    recargarPlantillasSMS(idCampaign);
}).change();

$(document).on("click", "#btnNuevaPlantillaSMS", function() {
    $("#spinnerModal").modal("show");
    $("#plantillaModalLabelSMS").text("Nueva Plantilla");
    $("#selectCampanasModalSMS").val('');
    $("#textModalSMS").val('');
    $("#textAreaModalSMS").val('');
    $("#idModalSMS").val('');
    $("#accionModalSMS").val("nuevo");
    $("#plantillaModalSMS").modal("show");
    $("#spinnerModal").modal("hide");
});

$(document).on("click", "#btnActualizarPlantillaSMS", function() {
    var idPlantilla = $("#selectPlantillaSMS").val();

    if( idPlantilla != "" ) {
        $("#spinnerModal").modal("show");
        $.post(site_url+'sms/buscarPlantilla', {
            id: idPlantilla
        }, function(respuesta) {
            $("#plantillaModalLabelSMS").text("Actualizar Plantilla");
            $("#selectCampanasModalSMS").val(respuesta.id_campaign);
            $("#textModalSMS").val(respuesta.name);
            $("#textAreaModalSMS").val(respuesta.valor);
            $("#idModalSMS").val(respuesta.id);
            $("#accionModalSMS").val("actualizar");
            $("#plantillaModalSMS").modal("show");
        }, 'json')
        .fail(function() {
            toastmsg("Algo ocurrió al buscar los datos de la plantilla.", "danger");
        });
        $("#spinnerModal").modal("hide");
    }else{
        toastmsg("Seleccione la plantilla.", "danger");
    }
});

$(document).on("click", "#btnEliminarTemplateSMS", function() {
    var idPlantilla = $("#selectPlantillaSMS").val();
    var texto = $("#selectPlantillaSMS option:selected").text();
    if( idPlantilla != "" ) {
        if( confirm("Esta seguro de eliminar esta plantilla?\n\""+texto+"\"") ) {

            $("#spinnerModal").modal("show");
            $.post(site_url+'sms/pborrar', {
                id : idPlantilla
            }, function(respuesta) {
                toastmsg(respuesta.action, respuesta.msg);
                let Id = $("#selectCampanaSMS").length > 0 ? "#selectCampanaSMS" : "#hiddenCampanaSMS";
                let IdCampaign = $(Id).val();
                recargarPlantillasSMS(IdCampaign);
            }, 'json')
            .fail(function() {
                toastmsg("Algo ocurrió al eliminar la plantilla.", "danger");
            });
            $("#spinnerModal").modal("hide");
        }
    }else{
        toastmsg("Seleccione la plantilla.", "danger");
    }
});

$(document).on("click", "#btnGuardarPlantillaSMS", function() {
    var id                = $.trim($("#idModalSMS").val());
    let IdCampanaSelector = $("#selectCampanaSMS").length > 0 ? "#selectCampanaSMS" : "#hiddenCampanaSMS";
    var idCampana         = $.trim($(IdCampanaSelector).val());
    var textSMS           = $.trim($("#textModalSMS").val());
    var textAreaSMS       = $.trim($("#textAreaModalSMS").val());
    var accionSMS         = $.trim($("#accionModalSMS").val());

    if( idCampana != "" && textSMS != "" && textAreaSMS != "" ) {
        $("#spinnerModal").modal("show");
        let ruta = accionSMS == "nuevo" ? "pnueva" : "pactu";
        $.post(site_url+'sms/'+ruta, {
            id         : id,
            id_campaign: idCampana,
            name       : textSMS,
            valor      : textAreaSMS,
            accion     : accionSMS
        }, function(respuesta) {
            if (respuesta.action == "info") {
                // SI EXITE EL SELECTE DE CAMPAÑAS O SI ES UN INPIT HIDDEN
                let IdPlantilla = $("#selectPlantillaSMS").val();
                recargarPlantillasSMS(idCampana, IdPlantilla);
                if( accionSMS == "nuevo" ){
                    $("#selectCampanasModalSMS").val('');
                    $("#textModalSMS").val('');
                    $("#textAreaModalSMS").val('');
                }else{
                    $("#plantillaModalSMS").modal("hide");
                }
            }
            toastmsg(respuesta.action, respuesta.msg);
        }, 'json')
        .fail(function() {
            toastmsg("Algo ocurrió al guardar la plantilla.", "danger");
        });
        $("#spinnerModal").modal("hide");
    } else {
        toastmsg("Faltan por llenar campos.", "danger");
    }
});

function recargarPlantillasSMS(idCampaign, idPlantilla="") {
    if( idCampaign != "" ) {
        $("#spinnerModal").modal("show");
        $.post(site_url+'sms/buscarPlantilla', {
            id_campaign: idCampaign
        }, function(respuesta) {
            var option = "";
            if (respuesta.length != 1 ) option = "<option value=''>-Seleccione-</option>"
            respuesta.forEach(element => {
                option += "<option value='"+element.id+"'>"+element.name+"</option>";
            });
            $("#selectPlantillaSMS").html(option);
            if( idPlantilla != "" ) $("#selectPlantillaSMS").val(idPlantilla);
        }, 'json')
        .fail(function() {
            toastmsg("Algo ocurrió al buscar las plantilla de la campaña.", "danger");
        });
        $("#spinnerModal").modal("hide");
    }else{
        $("#selectPlantillaSMS").empty();
        $("#selectPlantillaSMS").html("<option value=''>-Seleccione-</option>");
    }
}
