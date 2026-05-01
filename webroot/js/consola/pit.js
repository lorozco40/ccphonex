let pitpermit = 160;

$(document).ready(function() {
    $("#input_msg_pit").keyup(function() {
        let usado = $(this).val().length;
        let resto = pitpermit - usado;
        $("#restopit").html(resto);
        if (resto == 0) {
            toastmsg("Limite alcanzado, ya no quedan caracteres.", "danger");
        }
    });
});

$(document).on("click", "#buscar_nombre", function(e){
    e.preventDefault();
    buscar_nombre();
});

$(document).on("keyup", "#input_pin", function(e){
    let code = (e.keyCode ? e.keyCode : e.which);
    if ( code==13 ) {
        buscar_nombre();
    }else{
        $("#input_msg_pit").attr("disabled",true);
        $("#btn_enviarsms_pit").attr("disabled",true);
        $("#limpiasmspit").attr("disabled",true);
        $("#nombre_pit").val("");
        $("#nombre_pit").removeClass("is-invalid is-valid");
        $("#alert_aviso").hide();
        $("#mpc_btn").hide();
    }
});

$(document).on("click", "#limpiasmspit", function(e) {
    e.preventDefault();
    $("#input_msg_pit").val("");
    $("#input_msg_pit").keyup();
});

$(document).on("click", "#btn_enviarsms_pit", function(e) {
    e.preventDefault();
    let pin = $.trim($('#input_pin').val());
    let id_pit_catalog = $('#input_id_pit_catalog').val();
    let msg = $.trim($('#input_msg_pit').val());
    if ( pin.length == "" || msg.length == "" ) {
        if( pin.length == "" ) toastmsg('Escriba un PIN!!', );
        else toastmsg('Escriba algún mensaje!!', );
    } else {
        $("#spinnerModal").modal("show");
        $.post(site_url+'pit/enviar', {
            pin: pin,
            id_pit_catalog: id_pit_catalog,
            msg: msg,
        }, function(respuesta) {
            $("#spinnerModal").modal("hide");
            toastmsg(respuesta.msg, respuesta.action);
        }, 'json')
        .fail(function() {
            $("#spinnerModal").modal("hide");
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        });
    }
});

$(document).on("click", "#pNuevaPit", function() {
    $("#spinnerModal").modal("show");
    $.post(site_url+'pit/pnueva', {
        valor: $('#nuevavalorpit').val()
    }, function(respuesta) {
        $("#spinnerModal").modal("hide");
        if (respuesta.action == "info") {
            $("#listaplantillapit").append('<div class="pastilla" data-id="'+respuesta.id+'">'+
                '<div class="cerrarpastilla pBorrarPit"><i class="fas fa-times"></i></div>'+
                '<div class="form-group">'+
                    '<textarea id="ptextPit'+respuesta.id+'" class="form-control">'+$('#nuevavalorpit').val()+'</textarea>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col"><button class="btn btn-secondary pUsarPit">Usar</button></div>'+
                    '<div class="col text-right"><button class="btn btn-info pActuPit">Actualizar</button></div>'+
                '</div>'+
            '</div>');
        }
        toastmsg(respuesta.msg, respuesta.action);
    }, 'json')
    .fail(function() {
        $("#spinnerModal").modal("show");
        location.reload();
    });
});

$(document).on("click", ".pActuPit", function() {
    let id = $(this).closest(".pastilla").attr("data-id");
    $("#spinnerModal").modal("show");
    $.post(site_url+'pit/pactu', {
        id:	   id,
        valor: $('#ptextPit'+id).val()
    }, function(respuesta) {
        $("#spinnerModal").modal("hide");
        toastmsg(respuesta.msg, respuesta.action);
    }, 'json')
    .fail(function() {
        $("#spinnerModal").modal("hide");
        location.reload();
    });
});

$(document).on("click", ".pBorrarPit", function() {
    let id = $(this).closest(".pastilla").attr("data-id");
    $("#spinnerModal").modal("show");
    $.post(site_url+'pit/pborrar', {
        id : id
    }, function(respuesta) {
        $("#spinnerModal").modal("hide");
        toastmsg(respuesta.msg, respuesta.action);
    }, 'json')
    .fail(function() {
        $("#spinnerModal").modal("hide");
        location.reload();
    });
});

$(document).on("click", ".pUsarPit", function() {
    let id = $(this).closest(".pastilla").attr("data-id");
    $("#input_msg_pit").val($("#ptextPit"+id).val());
    $("#input_msg_pit").keyup();
});

$(document).on("click", "#btnNuevaPlantillaPIT", function() {
    $("#spinnerModal").modal("show");
    $("#plantillaModalLabelPIT").text("Nueva Plantilla");
    $("select #selectCampanasModalPIT").val('');
    /*
        *ESPECIFICO EL SELECT POR QUE EL INPUT HIDDEN TAMBIEN SE LLAMA ASI
        *COLOCO EL VALOR DEL SELECT EN EL PRIMERO (-SELECCIONE-) CUANDO TIENE VARIAS CAMPAÑAS ASIGNADAS EL USUARIO
        *SI ES UN INPUT HIIDEN NO ES NECESARIO REINICIAR EL VALOR PUESTO SOLO TIENE UNA CAMPAÑA ASIGNADA
    */
    $("#textAreaModalPIT").val('');
    $("#nameModalPIT").val('');
    $("#plantillaModalPIT").modal("show");
    $("#spinnerModal").modal("hide");
});

$(document).on("click", ".btnActualizarPlantillaPIT", function() {
    let idPlantilla = $(this).data("id");
    let textAreaPIT = $("#textArea"+idPlantilla+"PIT").val();

    if( idPlantilla != "" ) {
        $("#spinnerModal").modal("show");
        $.post(site_url+'pit/pactu', {
            id:	   idPlantilla,
            valor: textAreaPIT
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

$(document).on("click", ".btnUsarPlantillaPIT", function() {
    let idPlantilla = $(this).data("id");
    let textAreaPIT = $("#textArea"+idPlantilla+"PIT").val();
    let n = textAreaPIT.length;
    if( n > pitpermit ) 
        textAreaPIT = textAreaPIT.substring(0, pitpermit);
    $("#input_msg_pit").val(textAreaPIT);
    $("#input_msg_pit").keyup();
});

$(document).on("click", ".btnEliminarTemplatePIT", function() {
    let idPlantilla = $(this).data("id");
    let textAreaPIT = $("#textArea"+idPlantilla+"PIT").val();
    if( confirm("Esta seguro de eliminar esta plantilla?\n\""+textAreaPIT+"\"") ) {

        $("#spinnerModal").modal("show");
        $.post(site_url+'pit/pborrar', {
            id : idPlantilla
        }, function(respuesta) {
            toastmsg(respuesta.msg, respuesta.action);
            $("#"+idPlantilla+"PlantillaPIT").remove();
        }, 'json')
        .fail(function() {
            toastmsg("Algo ocurrió al eliminar la plantilla.", "danger");
        });
        $("#spinnerModal").modal("hide");
    }
});

$(document).on("click", "#btnGuardarPlantillaPIT", function() {
    let idCampana   = $.trim($("#selectCampanasModalPIT").val());
    let textAreaPIT = $.trim($("#textAreaModalPIT").val());
    let namePIT     = $.trim($("#nameModalPIT").val());

    if( idCampana != "" && textAreaPIT != "" && namePIT != "") {
        $("#spinnerModal").modal("show");
        $.post(site_url+'pit/pnueva', {
            id_campaign: idCampana,
            valor: textAreaPIT,
            name: namePIT
        }, function(respuesta) {
            if (respuesta.action == "info") agregaPlantillaHtmlPIT(respuesta.id);
            toastmsg(respuesta.msg, respuesta.action);
            $("select #selectCampanasModalPIT").val('');
            $("#nameModalPIT").val('');
            $("#textAreaModalPIT").val('');
        }, 'json')
        .fail(function() {
            toastmsg("Algo ocurrió al guardar la plantilla.", "danger");
        });
        $("#spinnerModal").modal("hide");
    } else {
        toastmsg("Faltan por llenar campos.", "danger");
    }
});

function agregaPlantillaHtmlPIT(id) {
    let name     = $("#nameModalPIT").val();
    let textArea = $("#textAreaModalPIT").val();
    let html = '<div id="'+id+'PlantillaPIT" class="row mb-4">' +
                    '<div class="col-6 col-sm-9">' +
                        '<label for="textArea'+id+'PIT">'+name+'</label>' +
                        '<textarea id="textArea'+id+'PIT" class="form-control" rows="4">'+textArea+'</textarea>' +
                    '</div>' +
                    '<div class="col-6 col-sm-3 d-flex align-items-center justify-content-center" >' +
                        '<button type="button" class="btn btn-dark ml-2 btnUsarPlantillaPIT" title="Usar" data-id="'+id+'"><i class="fas fa-hand-pointer"></i></button>' +
                        '<button type="button" class="btn btn-success ml-2 btnActualizarPlantillaPIT" title="Guardar" data-id="'+id+'"><i class="fas fa-save"></i></button>' +
                        '<button type="button" class="btn btn-danger ml-2 btnEliminarTemplatePIT" title="Eliminar" data-id="'+id+'"><i class="fas fa-trash"></i></button>' +
                    '</div>' +
                '</div>';
    $("#divPlantillasPIT").append(html);
    if( $("#NoHayPlantillasPIT").length > 0 ) $("#NoHayPlantillasPIT").remove();
}

function buscar_nombre(id = 0) {
    $("#tabla_contacto_pit").html('');
    let pin = $.trim($('#input_pin').val());
    if ( pin.length == "" ) {
        toastmsg('Escriba una Clave o Nombre!', );
    } else {
        $("#spinnerModal").modal("show");
        $.post(site_url+'pit/buscar_nombre', {
            pin: pin,
            id: id,
        }, function(resp) {
            $("#spinnerModal").modal("hide");
            if( resp.success === true ){
                $("#nombre_pit").addClass("is-valid").removeClass("is-invalid");
                $("#mpc_btn").show();
                $("#nombre_pit").val(resp.data.name);
                $("#input_id_pit_catalog").val(resp.data.id);
                $("#input_msg_pit").attr("disabled",false);
                $("#btn_enviarsms_pit").attr("disabled",false);
                $("#limpiasmspit").attr("disabled",false);
                if( resp.data.aviso != null && resp.data.aviso != '' ) {
                    $("#text-aviso").html(resp.data.aviso);
                    $("#alert_aviso").show();
                }
                else {
                    $("#alert_aviso").hide();
                }
            } else if( resp.success === false) {
                $("#input_id_pit_catalog").val('');
                $("#nombre_pit").addClass("is-invalid").removeClass("is-valid");
                $("#mpc_btn").hide();
                $("#btn_enviarsms_pit").attr("disabled",true);
                $("#nombre_pit").val(resp.msg);
                toastmsg(resp.msg, );
            } else if ( resp.success == 'tabla' ) {
                html = `<hr/><table>
                <thead>
                    <tr>
                        <th class="text-center">Mostrando los 10 primeros resultados</th>
                    </tr>
                    <tr>
                        <th>Clave</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody>`;
                resp.data.map(function(row, i) {
                    html+= `<tr>
                        <td><span class="text-`+row.class+` mx-2" role="button" onclick="seleccionar_id(`+row.id+`)">`+row.pin+`</span></td>
                        <td>`+row.name+`</td>
                    </tr>`;
                })
                html += "</tbody></table><hr/>";
                $("#tabla_contacto_pit").html(html);
            }
        }, 'json')
        .fail(function(e) {
            $("#spinnerModal").modal("hide");
            console.log(e);
        });
    }
}

function seleccionar_pin(pin) {
    $('#input_pin').val(pin);
    buscar_nombre();
}

function seleccionar_id(id) {
    $('#input_id_pit_catalgo').val(id);
    buscar_nombre(id);
}

//obtenemos la informacion del usuario de PIT
function edit_contact_pit() {
    $("#spinnerModal").modal("show");
    $.post(site_url+'pit/editar', {
        id: $("#input_id_pit_catalog").val(),
    }, function(resp) {
            $("#spinnerModal").modal("hide");
            if( resp.hasOwnProperty('error') ){
                toastmsg(resp.error, );
            } else {
                $("#mpc").modal("show");
                $("#mpc_id").val(resp.id);
                $("#mpc_pin").val(resp.pin);
                $("#mpc_phone").val(resp.phone);
                $("#mpc_name").val(resp.name+' '+resp.last);
                $("#mpc_aviso").val(resp.aviso);
                $("#redi_id").val(resp.redi_id);
                $("#redi_nombre").val(resp.redi_nombre);
                $("#redi_pin").val(resp.redi_pin);
                $("#redi_vigencia").val(resp.redi_vigencia);
                $("#redi_vigencia_hora").val(resp.redi_vigencia_hora);
                redireccionadoControl();
            }
        },
    'json')
    .fail(function(e) {
        $("#spinnerModal").modal("hide");
        console.log(e);
    });
}

let rediBuscador = document.getElementById("rediBuscador");
rediBuscador.addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        rediTabla();
    }
});

//Apartado de redireccionamiento PIT
function redireccionadoControl() {
    let redi_pin = $("#redi_pin").val();
    $("#rediPanel").hide();
    $("#rediBuscador").val('');
    $("#rediPin").val('');
    $("#rediNombre").val('');
    $("#rediVigencia").val('');
    $("#rediVigenciaHora").val('');
    $("#id_pit_catalog_redirect").val('');
    $("#rediVigencia").attr("disabled",true);
    $("#rediVigenciaHora").attr("disabled",true);
    $(".rediControl").hide();
    $(".rediLeyenda").hide();
    $(".rediExistente").hide();
    $(".rediPanel").hide();

    if (  redi_pin != '') { //significa que se esta editando y hay un redirecionamiento
        $(".rediLeyenda").show();
        $(".rediExistente").show();
    }

    if (  redi_pin == '') { //significa que se esta editando y no hay un redirecionamiento
        $(".rediControl").show();
    }
}
//Muestra el panel de redireccionado
function rediPanel() {
    $(".rediControl").hide();
    $(".rediPanel").show();
    $(".rediLeyenda").show();
}

function rediTabla(id = 0) {
    $("#rediTablaContactoPit").html('');
    let pin = $.trim($('#rediBuscador').val());
    if ( pin.length == "" ) {
        toastmsg('Escriba la información a buscar!', );
    } else {
        $("#spinnerModal").modal("show");
        $.post(site_url+'pit/buscar_nombre', {
            pin: pin,
            id: id,
        }, function(resp) {
            $("#spinnerModal").modal("hide");
            if( resp.success === true ){
                $("#rediNombre").addClass("is-valid").removeClass("is-invalid");
                $("#rediNombre").val(resp.data.name);
                $("#mpc_input_id_pit_catalog").val(resp.data.id);
                $("#rediPin").val(resp.data.pin);
                $("#id_pit_catalog_redirect").val(resp.data.id);
                $("#rediVigencia").attr("disabled",false);
                $("#rediVigenciaHora").attr("disabled",false);
            } else if( resp.success === false) {
                $("#mpc_input_id_pit_catalog").val('');
                $("#id_pit_catalog_redirect").val('');
                $("#rediPin").val('');
                $("#rediNombre").val('');
                $("#rediVigencia").val('');
                $("#rediNombre").addClass("is-invalid").removeClass("is-valid");
                $("#rediNombre").val(resp.msg);
                toastmsg(resp.msg, );
                $("#rediVigencia").attr("disabled",true);
                $("#rediVigenciaHora").attr("disabled",true);
            } else if ( resp.success == 'tabla' ) {
                html = `<hr/><table>
                <thead>
                    <tr>
                        <th class="text-center">Mostrando los 10 primeros resultados</th>
                    </tr>
                    <tr>
                        <th>CLAVE</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody>`;
                resp.data.map(function(row, i) {
                    html+= `<tr>
                        <td><span class="text-`+row.class+` mx-2" role="button" onclick="mpc_seleccionar_id(`+row.id+`)">`+row.pin+`</span></td>
                        <td>`+row.name+`</td>
                    </tr>`;
                })
                html += "</tbody></table><hr/>";
                $("#rediTablaContactoPit").html(html);
            }
        }, 'json')
        .fail(function(e) {
            $("#spinnerModal").modal("hide");
            console.log(e);
        });
    }
}

function mpc_seleccionar_id(id) {
    $('#mpc_input_id_pit_catalog').val(id);
    rediTabla(id);
}

function eliminarRedirect() {
    let id_pit_catalog = $("#input_id_pit_catalog").val();
    const redi_id = $("#redi_id").val();
    if( confirm("Esta seguro de eliminar el redireccionamiento?") ) {
        $.post(site_url+"pit/eliminar_redirect", { id: redi_id  }, function(data) {
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                toastmsg(data, "success");
                edit_contact_pit()
                seleccionar_id(id_pit_catalog);
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
}

function mpc_guardar() {
    $("#spinnerModal").modal("show");
    let data = new FormData(document.getElementById("form_pit"));
    let rediPin = $("#rediPin").val();
    let id_pit_catalog = $("#input_id_pit_catalog").val();
    data.append('rediPin', rediPin)
    $.ajax({
        url: site_url+'pit/pmc_guardar',
        data: data,
		processData:false,
		contentType:false,
		type: 'POST',
    })
    .done(function(data) {
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            $("#mpc_id").val(0);
            $("#mpc_phone").val('');
            $("#mpc_name").val('');
            $("#modal_pit").modal('hide');
            $("#mpc").modal("hide");
            toastmsg(data, "success");
            seleccionar_id(id_pit_catalog);
        }
        $("#spinnerModal").modal("hide");
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