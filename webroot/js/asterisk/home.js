deshabilitar();

$(document).on("change", "#config", function(){
    var config = $(this).val();
    deshabilitar();
    $("#texto").val("");
    $("#import").val("");
    $("#label-texto").text("");
    if ( config != "" ) {
        $("#spinnerModal").modal("show");
        $("#label-texto").text(config);
        $.post(site_url+"asterisk/traerConfig", { config: config }, function(data) {
            $("#spinnerModal").modal("hide");
            if( data.success ) {
                document.getElementById("texto").disabled = false;
                document.getElementById("btn-guardar").disabled = false;
                $("#texto").val(data.texto);
                if( data.include.length > 0 ){
                    let includes = data.include;
                    document.getElementById("import").disabled = false;
                    document.getElementById("import").innerHTML = "<option value=''>-- Seleccione --</option>";
                    for(var i in includes){
                        document.getElementById("import").innerHTML += "<option value='"+includes[i]+"'>"+includes[i]+"</option>";
                    }
                }
            } else {
                toastmsg(data.mensaje, "danger");
            }
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
});

$(document).on("change", "#import", function(){
    var config = $(this).val();
    deshabilitar();
    $("#texto").val("");
    $("#label-texto").text("");
    if ( config != "" ) {
        $("#spinnerModal").modal("show");
        $("#label-texto").text(config);
        $.post(site_url+"asterisk/traerConfig", { config: config }, function(data) {
            $("#spinnerModal").modal("hide");
            if( data.success ) {
                document.getElementById("texto").disabled = false;
                document.getElementById("btn-guardar").disabled = false;
                document.getElementById("import").disabled = false;
                $("#texto").val(data.texto);
            } else toastmsg(data.mensaje, "danger");
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
    }else{
        $("#config").change();
    }
});

$(document).on("click", "#btn-guardar", function(){
    var config    = "";
    var hazChange = false;
    // var config = $("#import").val() == "" ? $("#config").val() : $("#import").val();
    if( $("#import").val() == "" ){
        config = $("#config").val();
        hazChange = true;
    }else{
        config = $("#import").val();
        hazChange = false;
    }

    var texto = $("#texto").val();
    if ( config != "" ) {
        $("#spinnerModal").modal("show");
        $.post(site_url+"asterisk/guardarConfig", { config: config, texto: texto }, function(data) {
            $("#spinnerModal").modal("hide");
            if( data.success ) {
                toastmsg(data.mensaje, "success");
                document.getElementById("btn-reload").disabled = false;
                if( hazChange ) $("#config").change();
            } else toastmsg(data.mensaje, "danger");
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
    }else{
        toastmsg("Escriba en la caja de texto.", "danger");
    }
});

$(document).on("click", "#btn-reload", function(){
    $("#spinnerModal").modal("show");
    $.get(site_url+"asterisk/cargarConfig", function(data) {
        console.log(data);
        $("#spinnerModal").modal("hide");
        if( data.success ) {
            toastmsg(data.mensaje, "success");
            document.getElementById("btn-reload").disabled = false;
        } else toastmsg(data.mensaje, "danger");
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
});

function deshabilitar() {
    document.getElementById("texto").disabled = true;
    document.getElementById("import").disabled = true;
    document.getElementById("btn-guardar").disabled = true;
    document.getElementById("btn-reload").disabled = true;
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
