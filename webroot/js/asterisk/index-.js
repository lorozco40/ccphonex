var paginacion = {
    pag: 0,
    reg: 0,
    rpp: 20,
    bus: ''
}

var buscar = document.getElementById("buscar");
var rediBuscador = document.getElementById("rediBuscador");
buscar.addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        paginacion.pag = 0;
        traerdata();
    }
});
rediBuscador.addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        rediTabla();
    }
});

$(document).ready(function() {
    traerdata();
    $(document).on("change", "#elirpp", function(){
        paginacion.rpp = $(this).val();
        traerdata();
    });
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        paginacion.pag = $(this).data('pag');
        traerdata();
    });
});

function motivo_display() {
    let display = $("#active").prop( "checked");
    if( display == false ) {
        $(".motivo_display").show();
    }
    else {
        $(".motivo_display").hide();
    }
}

function traerdata(nuval = 0) {
    let cuenta = 0;
    let selected = '';
    paginacion.pag = (nuval != 0) ? (nuval-1)*paginacion.rpp : paginacion.pag;
    paginacion.bus = $("#buscar").val();
    $("#spinnerModal").modal("show");
    $.post(site_url+"pit/traerdata", { bus: paginacion.bus, pag: paginacion.pag, rpp: paginacion.rpp}, function(data) {
        var html = "<div class='table-row'><div class='table-cell text-warning'> No se encontro ningún registro.</td></tr>";
        if (!data.data || data.data.length == 0) {
            $("#tablaPit .table-row").remove();
            $("#paginacion").html("");
            pag=0;
            $("#tablaPit").append(html);
        } else {
            cuenta = 0;
            html = '';
            reg = data.reg;
            data.data.forEach((row,key) => {
                cuenta++;
                activo = (row.active=='1') ? 'Si' : 'No';
                html += `<div class='table-row'>
                    <div class='table-cell'>`+row.pin+`</div>
                    <div class='table-cell'>`+row.phone+`</div>
                    <div class='table-cell'>`+row.nombre+`</div>
                    <div class='table-cell'>`+activo+`</div>
                    <div class='table-cell'>
                        <select id="select_user_pit_`+row.id+`" name="id_user" class="form-control" onchange="actualizar_usuario(`+row.id+`)">`;
                            html += '<option value="0">-- Seleccione --</option>'
                            data.usuarios_paginacion.forEach((item,key) => {
                                selected = (row.id_user == item.id) ? 'selected' : '';
                                html += `<option value="`+item.id+`" `+selected+`>`+item.name+`</option>`;
                            });
                html += `</select>
                    </div>
                    <div class='table-cell'>
                        <button type='button' class='btn btn-dark editar' onclick="editar(`+row.id+`)" data-id='`+row.id+`'>Editar</button>`;

                    if( data.perfil == 'admin' ) {
                        html += ` <button type='button' class='btn btn-dark' onclick="eliminar(`+row.id+`)">Eliminar</button>`;
                    }

                html +=`</div>
                </div>`;
            });
            $("#tablaPit .table-row").remove();
            $("#tablaPit").append(html);
            paginacion(paginacion.pag, data.reg, paginacion.rpp, cuenta, 'paginacion', 'traerdata');
        }
        $("#spinnerModal").modal("hide");
    },"json")
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

function editar(id) {
    let active_check = false;
    //obtenemos el registro
    $.post(site_url+"pit/editar", { id: id}, function(data) {
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            if( data.active == 1 ) active_check = true;
            //Seteamos los valores
            $( "#active" ).prop( "checked", active_check );
            $("#id").val(data.id);
            $("#pin").val(data.pin);
            $("#phone").val(data.phone);
            $("#name").val(data.name);
            $("#last").val(data.last);
            $("#motivo").val(data.motivo);

            $("#redi_id").val(data.redi_id);
            $("#redi_nombre").val(data.redi_nombre);
            $("#redi_pin").val(data.redi_pin);
            $("#redi_vigencia").val(data.redi_vigencia);

            motivo_display();
            redireccionadoControl();
            //Mostramos el modal
            $("#modal_pit").modal('show');
        }
        $("#spinnerModal").modal("hide");
    },"json")
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

function eliminar(id) {
    if( confirm("Esta seguro de eliminar este registro?") ) {
        $.post(site_url+"pit/eliminar", { id: id}, function(data) {
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                toastmsg(data, "success");
                traerdata();
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

function nuevo() {
    //Seteamos los valores
    $( "#active" ).prop( "checked", true );
    $("#id").val(0);
    $("#pin").val('');
    $("#phone").val('');
    $("#name").val('');
    $("#last").val('');
    $("#motivo").val('');
    motivo_display();
    redireccionadoControl();
    //Mostramos el modal
    $("#modal_pit").modal('show');
}

function guardar() {
    let data = new FormData(document.getElementById("form_pit"));
    let rediPin = $("#rediPin").val();
    data.append('rediPin', rediPin)
    $.ajax({
        url: site_url+'pit/guardar',
        data: data,
		processData:false,
		contentType:false,
		type: 'POST',
    })
    .done(function(data) {
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                $("#id").val(0);
                $("#pin").val('');
                $("#phone").val('');
                $("#name").val('');
                $("#modal_pit").modal('hide');
                toastmsg(data, "success");
                traerdata();
            }
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

function guardadoMasivoModal() {
    $("#form_pit_file").trigger("reset");
    $("#pitfile-modal").modal('show');
}

function guardadoMasivo() {
    var data = new FormData(document.getElementById("form_pit_file"));
    $.ajax({
        url: site_url+'pit/guardarArchivo',
        data: data,
        processData:false,
        contentType:false,
        type: 'POST',
        success: function (data)
        {
            if(typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                $("#pitfile-modal").modal("hide");
                toastmsg(data);
                traerdata();
            }
        }
    });
}

function actualizar_usuario(id) {
    let id_user = $("#select_user_pit_"+id).val();
    $.post(site_url+"pit/actualizar_usuario", { id: id, id_user: id_user  }, function(data) {
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            toastmsg(data, "success");
            traerdata();
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

//Se activa cuando se abre el modal de catalog pit
function redireccionadoControl() {
    let id = $("#id").val();
    let redi_pin = $("#redi_pin").val();
    $("#rediPanel").hide();
    $("#rediBuscador").val('');
    $("#rediPin").val('');
    $("#rediNombre").val('');
    $("#rediVigencia").val('');
    $("#id_pit_catalog_redirect").val('');

    //ocultamos todo
    $(".rediControl").hide();
    $(".rediLeyenda").hide();
    $(".rediExistente").hide();
    $(".rediPanel").hide();
    if( id == 0 ) {

    }

    if ( id != 0 && redi_pin != '') { //significa que se esta editando y hay un redirecionamiento
        $(".rediLeyenda").show();
        $(".rediExistente").show();
    }

    if ( id != 0 && redi_pin == '') { //significa que se esta editando y no hay un redirecionamiento
        $(".rediControl").show();
        //$(".rediLeyenda").show();
        //$(".rediPanel").show();
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
        toastmsg('Escriba la información a buscar!', "danger");
    } else {
        $("#spinnerModal").modal("show");
        $.post(site_url+'pit/buscar_nombre', {
            pin: pin,
            id: id,
        }, function(resp) {
            $("#spinnerModal").modal("hide");
            if( resp.success === true ){
                $("#nombre_pit").addClass("is-valid").removeClass("is-invalid");
                $("#rediNombre").val(resp.data.name);
                $("#input_id_pit_catalog").val(resp.data.id);
                $("#rediPin").val(resp.data.pin);
                $("#id_pit_catalog_redirect").val(resp.data.id);
                $("#input_msg_pit").attr("disabled",false);
                $("#btn_enviarsms_pit").attr("disabled",false);
                $("#limpiasmspit").attr("disabled",false);
            } else if( resp.success === false) {
                $("#input_id_pit_catalog").val('');
                $("#id_pit_catalog_redirect").val('');
                $("#rediPin").val('');
                $("#rediNombre").val('');
                $("#rediVigencia").val('');
                $("#nombre_pit").addClass("is-invalid").removeClass("is-valid");
                $("#nombre_pit").val(resp.msg);
                toastmsg(resp.msg, "danger");
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
                    if( row.bus_id == 1 ) {
                        html+= `
                        <tr>
                            <td><span class="text-`+row.class+` mx-2" role="button" onclick="seleccionar_id(`+row.id+`)">`+row.pin+`</span></td>
                            <td>`+row.name+`</td>
                        </tr>`;
                    }
                    else {
                        html+= `
                        <tr>
                            <td><span class="text-`+row.class+` mx-2" role="button" onclick="seleccionar_id(`+row.id+`)">`+row.pin+`</span></td>
                            <td>`+row.name+`</td>
                        </tr>`;
                    }
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

function seleccionar_id(id) {
    $('#input_id_pit_catalog').val(id);
    rediTabla(id);
}

function seleccionar_pin(pin) {
    $('#rediBuscador').val(pin);
    rediTabla();
}




function eliminarRedirect() {
    const id = $("#id").val();
    const redi_id = $("#redi_id").val();
    if( confirm("Esta seguro de eliminar el redireccionamiento?") ) {
        $.post(site_url+"pit/eliminar_redirect", { id: redi_id  }, function(data) {
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                toastmsg(data, "success");
                editar(id);
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