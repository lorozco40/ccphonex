var desp = {
    id: $('#id_desp').val(),
    traer_form : function(cual = "", unlock_reg = "") {
        if (agente.estado == 'disponible' && $("#leform").html() == '') {
            $('#despastilla').addClass('d-none');
            return new Promise((resolve, reject) => {
                let ahora = new Date();
                ahora.setTime(ahora.getTime() + (1*60*60*1000));
                $.post(site_url+'despachador/traer_despachador', {id_desp: desp.id, id_reg: cual, unlock_reg: unlock_reg}, function(data) {
                    if (data.desp !== null) {
                        desp.id = data.desp.id;
                        $("#despachador").html(data.form);
                        $("#numDisplay").val(data.phone);
                        filah = "<h4>Historial de llamadas</h4><table class='table table-striped'><tr><th>Fecha</th><th>Agente</th><th>Tipificacion</th><th>Comentario</th>";
                        data.histo.forEach(function(row) {
                            val = Object.keys(row)[0];
                            filah += "<tr><td>"+row.saved_when+
                            "</td><td>"+row.nombre+
                            "</td><td>"+row[val]+
                            "</td><td>"+row.com+
                            "</td></tr>\n";
                        });
                        $("#disp_histo").html(filah);
                        $("#busdespres").html('');
                        $("#busdespval").val('');
                        if (data.desp.autodial == 'progresivo') {
                            $(".btnCall").click();
                        }
                        $(".postponedate").val(toIsoString(ahora));
                        $("#despastilla").removeClass('d-none');
                        resolve('Formulario cargado');
                    } else {
                        desp.id = "";
                        setTimeout(function() {
                            desp.traer_form();
                        }, 15000);
                        resolve('No hay registros');
                    }
                },'json')
                .fail(function() {
                    reject('Error de comunicación');
                    setTimeout(function() {
                        desp.traer_form();
                    }, 15000);
                });
            });
        } else {
            setTimeout(function() {
                desp.traer_form();
            }, 15000);
        }
    },
    guardar_form: function(accion) {
        return new Promise((resolve, reject) => {
            var campos = '';
            var valido = true;
            $('#desp_data .form-control').filter('[required]:visible').each(function() {
                if ($(this).val() == '' || $(this).val() == '-- Elige --') {
                    campos += ' ' + $(this).attr('name') + ',';
                    valido = false;
                }
            });
            if (valido) {
                $("#spinnerModal").modal("show");
                notFound="";
                $("input[type='checkbox']").each(function(i, j){
                    if( $(j).is(':checked') ) {
                        $(j).attr('value','1');
                    } else {
                        $(j).attr('value', '0');
                        notFound+='&'+$(j).attr('name')+'=0';
                    }
                });
                data = $('#desp_data').serialize() + '&accion='+accion + notFound;
                $.post(site_url+'despachador/actualiza_registro', data, function(data){
                    $("#spinnerModal").modal("hide");
                    if (data == '1') {
                        toastmsg('Registro actualizado!', "success");
                        resolve('Registro actualizado!');
                    } else {
                        toastmsg('Error de comunicación, consulta con tu administrador.', "danger");
                        reject('Error de comunicación');
                    }
                },'json')
                .fail(function() {
                    $("#spinnerModal").modal("hide");
                    toastmsg('Error de comunicación, consulta con tu administrador.', "danger");
                    reject('Error de comunicación');
                });
            } else {
                $("#spinnerModal").modal("hide");
                toastmsg('El(Los) campo(s): "'+campos+'" es(son) REQUERIDO(S).', "danger");
                reject("Requeridos faltantes");
            }
        });
    },
    buscar: function(texto) {
        if (desp.id != "") {
            $("#spinnerModal").modal("show");
            $.post(site_url+'despachador/buscar', {id_desp: desp.id, buscar: texto}, function(data) {
                $("#busdespres").html(data);
            },'json');
            $("#spinnerModal").modal("hide");
        }
    },
    postponer: function() {
        return new Promise((resolve, reject) => {
            $("#spinnerModal").modal("show");
            $.post(site_url+'despachador/postponer',
                {id_desp: desp.id, id_reg: desp_data.id.value, nfecha: desp_data.postponedate.value},
                function(data) {
                $("#spinnerModal").modal("hide");
                if ('undefined' !== typeof data.error) {
                    toastmsg(data.error, "danger");
                    resolve(data.error);
                } else {
                    toastmsg(data.msg, "success");
                    resolve(data.msg);
                }
            },'json')
            .fail(function() {
                $("#spinnerModal").modal("hide");
                toastmsg('Error de comunicación, consulta con tu administrador.', "danger");
                reject('Error de comunicación');
            });
        });
    },
};

function toIsoString(date) {
    const pad = function(num) {
        return (num < 10 ? '0' : '') + num;
    };

    return date.getFullYear() +
        '-' + pad(date.getMonth() + 1) +
        '-' + pad(date.getDate()) +
        'T' + pad(date.getHours()) +
        ':' + pad(date.getMinutes()) +
        ':' + pad(date.getSeconds());
}

$(document).ready(function(){
    desp.traer_form();
    $(document).on('submit', "#busdespform", function(e){
        e.preventDefault();
        desp.buscar($("#busdespval").val());
    });
    $(document).on('click', ".despreg", function(e){
        e.preventDefault();
        const unlock_reg = $("#desp_data input[name=id]").val();
        desp.traer_form($(this).data('id'), unlock_reg);
    });
    $(document).on('click', "#d_saltar_btn", function(e){
        e.preventDefault();
        const unlock_reg = $("#desp_data input[name=id]").val();
        desp.traer_form('', unlock_reg);
    });
    $(document).on('click', "#d_parcial_btn, #d_final_btn", function(e){
        e.preventDefault();
        desp.guardar_form($(this).attr('name')).then(resultado => {
            desp.traer_form();
        }).catch(error => {
            console.error("Guardado de formulario rechazado:", error);
        });
    });
    $(document).on('click', "#d_postpone", function(e){
        e.preventDefault();
        desp.guardar_form('parcial')
        .then(desp.postponer)
        .then(() => {
            desp.traer_form();
        })
        .catch(err => {
            console.log(err);
        });
    });
    $(document).on('keyup', "#busdespval", function(e){
        $("#busdespres").empty();
    });
});
