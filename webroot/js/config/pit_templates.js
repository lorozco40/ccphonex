if( $("#hiddenCampanaPIT").length > 0 ) recargarPlantillasPIT( $("#hiddenCampanaPIT").val() );

$("#selectCampanaPIT").change(function() {
    var idCampaign = $(this).val();
    recargarPlantillasPIT(idCampaign);
}).change();

$(document).on("click", "#btnNuevaPlantillaPIT", function() {
    $("#spinnerModal").modal("show");
    $("#plantillaModalLabelPIT").text("Nueva Plantilla");
    $("#selectCampanasModalPIT").val('');
    $("#textModalPIT").val('');
    $("#textAreaModalPIT").val('');
    $("#idModalPIT").val('');
    $("#accionModalPIT").val("nuevo");
    $("#plantillaModalPIT").modal("show");
    $("#spinnerModal").modal("hide");
});

$(document).on("click", "#btnActualizarPlantillaPIT", function() {
    var idPlantilla = $("#selectPlantillaPIT").val();

    if( idPlantilla != "" ) {
        $("#spinnerModal").modal("show");
        $.post(site_url+'pit/buscarPlantilla', {
            id: idPlantilla
        }, function(respuesta) {
            $("#plantillaModalLabelPIT").text("Actualizar Plantilla");
            $("#selectCampanasModalPIT").val(respuesta.id_campaign);
            $("#textModalPIT").val(respuesta.name);
            $("#textAreaModalPIT").val(respuesta.valor);
            $("#idModalPIT").val(respuesta.id);
            $("#accionModalPIT").val("actualizar");
            $("#plantillaModalPIT").modal("show");
        }, 'json')
        .fail(function() {
            toastmsg("Algo ocurrió al buscar los datos de la plantilla.", "danger");
        });
        $("#spinnerModal").modal("hide");
    }else{
        toastmsg("Seleccione la plantilla.", "danger");
    }
});

$(document).on("click", "#btnEliminarTemplatePIT", function() {
    var idPlantilla = $("#selectPlantillaPIT").val();
    var texto = $("#selectPlantillaPIT option:selected").text();
    if( idPlantilla != "" ) {
        if( confirm("Esta seguro de eliminar esta plantilla?\n\""+texto+"\"") ) {

            $("#spinnerModal").modal("show");
            $.post(site_url+'pit/pborrar', {
                id : idPlantilla
            }, function(respuesta) {
                toastmsg(respuesta.action, respuesta.msg);
                let Id = $("#selectCampanaPIT").length > 0 ? "#selectCampanaPIT" : "#hiddenCampanaPIT";
                let IdCampaign = $(Id).val();
                recargarPlantillasPIT(IdCampaign);
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

$(document).on("click", "#btnGuardarPlantillaPIT", function() {
    var id                = $.trim($("#idModalPIT").val());
    let IdCampanaSelector = $("#selectCampanaPIT").length > 0 ? "#selectCampanaPIT" : "#hiddenCampanaPIT";
    var idCampana         = $.trim($(IdCampanaSelector).val());
    var textPIT           = $.trim($("#textModalPIT").val());
    var textAreaPIT       = $.trim($("#textAreaModalPIT").val());
    var accionPIT         = $.trim($("#accionModalPIT").val());

    if( idCampana != "" && textPIT != "" && textAreaPIT != "" ) {
        $("#spinnerModal").modal("show");
        let ruta = accionPIT == "nuevo" ? "pnueva" : "pactu";
        $.post(site_url+'pit/'+ruta, {
            id         : id,
            id_campaign: idCampana,
            name       : textPIT,
            valor      : textAreaPIT,
            accion     : accionPIT
        }, function(respuesta) {
            if (respuesta.action == "info") {
                // SI EXITE EL SELECTE DE CAMPAÑAS O SI ES UN INPIT HIDDEN
                let IdPlantilla = $("#selectPlantillaPIT").val();
                recargarPlantillasPIT(idCampana, IdPlantilla);
                if( accionPIT == "nuevo" ){
                    $("#selectCampanasModalPIT").val('');
                    $("#textModalPIT").val('');
                    $("#textAreaModalPIT").val('');
                }else{
                    $("#plantillaModalPIT").modal("hide");
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

function recargarPlantillasPIT(idCampaign, idPlantilla="") {
    if( idCampaign != "" ) {
        $("#spinnerModal").modal("show");
        $.post(site_url+'pit/buscarPlantilla', {
            id_campaign: idCampaign
        }, function(respuesta) {
            var option = "";
            if (respuesta.length != 1 ) option = "<option value=''>-Seleccione-</option>"
            respuesta.forEach(element => {
                option += "<option value='"+element.id+"'>"+element.name+"</option>";
            });
            $("#selectPlantillaPIT").html(option);
            if( idPlantilla != "" ) $("#selectPlantillaPIT").val(idPlantilla);
        }, 'json')
        .fail(function() {
            toastmsg("Algo ocurrió al buscar las plantilla de la campaña.", "danger");
        });
        $("#spinnerModal").modal("hide");
    }else{
        $("#selectPlantillaPIT").empty();
        $("#selectPlantillaPIT").html("<option value=''>-Seleccione-</option>");
    }
}
