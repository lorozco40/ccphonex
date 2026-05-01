$(document).ready( () => {
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
        onSelect: ()=>{
            cw.filterChange('')
        }
    });
    $(document).on("click", ".evalpoint", function(){
        var prev = parseInt($("#evaltotal").text());
        var este = parseInt($(this).val());
        if ($(this).is(":checked")) {
            var total = prev + este;
        } else {
            var total = prev - este;
        }
        $("#evaltotal").text(total);
    });
    cw.start();
})
const cw = {
    cuentas: [],
    agentes: [],
    contacto: {
        id: '',
        account: '',
        name: '',
    },
    filtros: {
        evento: '',
        min: '',
        max: '',
        id_campaign: '',
        id_agente: '',
        id_wc: '',
    },
    current_session: 0,
    start: () => {
        cw.filterChange('id_campaign');
        cw.resetFormEQM()
    },
    maskTel: (number) => {
        // Máscara: +X (XXX)-XXX-XXXX
        let numberMask = '';
        const expSoloNumeros = /^\d{10,14}$/;
        if( expSoloNumeros.test(number) ) 
            numberMask = number.replace(/(\d{1,3})(\d{3})(\d{3})(\d{4})$/, '+$1 ($2)-$3-$4');

        return numberMask;
    },
    handleWabusc: (e) => {
        const keycode = e.keyCode || e.which;
        if (keycode == 13) {
            cw.filterChange('');
        }
    },
    filterChange: (evento = '') => {
        cw.filtros = {
            evento      : evento,
            min         : $("#min").val(),
            max         : $("#max").val(),
            id_campaign : $("#id_campaign").val(),
            id_agente   : $("#id_agente").val(),
            id_wc       : $("#id_wc").val(),
            bus         : $("#wabusc").val(),
        };
        $.post(site_url+"calidad/wa_filter",cw.filtros,
        function(resp){
            cw.filtros.id_wc = resp['id_wc'];
            cw.filtros.id_agente = resp['id_agente'];
            cw.renderCuentas(resp['cuentas'], resp['id_wc']);
            cw.renderAgentes(resp['agentes'], resp['id_agente']);
            cw.renderContactos(resp['contactos']);
            // Reniciamos el apartado de conversacion
            cw.restartConversation();
            cw.getConversation()
            if( evento=='id_wc' || evento=='id_campaign' ) {
                // Obtenemos el registro de la cuenta seleccionada
                const rowTemp = cw.cuentas.find(itemTemp => itemTemp.id === cw.filtros.id_wc);
                $("#number_account").html( cw.maskTel(rowTemp.cuenta) );
            }
        },'json').fail( (err) => cw.ajaxFail(err) );
    },
    restartConversation: () => {
        cw.current_session = 0;
        $("#wamsgs").html('');
        cw.contacto = {
            id: '',
            account: '',
            name: '',
        };
    },
    getConversationBefore: (id, account, name) => {
        const elemento = '#item_contacto_'+id;
        $('#wacontactos').find('*').removeClass('border');
        $(elemento).addClass('border');
        cw.current_session = 0;
        cw.contacto.id = id;
        cw.contacto.account = account;
        cw.contacto.name = name;
        cw.getConversation();
    },
    getConversation: () => {
        if( cw.contacto.id == '' ) {
            $("#wacontactname").html(`Nombre de usuario`)
        } else {
            $("#wacontactname").html(`${cw.contacto.name} (${cw.contacto.account})`)
        }
        // Intentamos traer la conversacion, siempre y cuando se tengan los datos correctos
        if( cw.filtros.min != 0 && cw.filtros.max != 0 && cw.filtros.id_agente != 0 && cw.filtros.id_wc != 0 && cw.contacto.id != 0 ) {
            // Consultamos la conversacion
            const data = {
                min         : cw.filtros.min,
                max         : cw.filtros.max,
                id_agente   : cw.id_agente,
                id_wc       : cw.id_wc,
                id_contacto : cw.contacto.id,
                current_session : cw.current_session,
            }
            let wid = cw.id_wc;
            let cid = cw.contacto.id;
            let toid = false;
            $.post(site_url+"calidad/wa_conversation",data, (res) => {
                if (res.error) {
                    toastmsg(res.error, "danger");
                    $("#wacontactname").text('Nombre de usuario');
                } else {
                    if (res.lastid == 0) {
                        toastmsg("Sin mensajes para mostrar");
                    } else {
                        if (toid) {
                            $("#wamsgs").prepend(res.conver).animate({ scrollTop: 0}, 1000);
                        } else {
                            if( cw.current_session == 0 ) {
                                $("#wamsgs").html(res.conver);
                            } else {
                                $("#wamsgs").prepend(res.conver).animate({ scrollTop: 0}, 1000);
                            }
                            cw.current_session = res.current_session;
                        }
                    }
                }
            },'json').fail( (err) => cw.ajaxFail(err) );
        }
    },
    calificarMsg: (id) => {
        cw.resetFormEQM()
        $('#card_eval_msg').show();
        $("#form_eqm input[name=id_whatsapp_entry]").val(id);
        $('#temp_msg').html( $('#msg_'+id).html() );
    },
    calificarSes: (id_campaign, id_session) => {
        // Obtenemos los campos del formulario a calificar
        $.post(site_url+'calidad/wa_traercampos', {'id_campaign': id_campaign}, function(resp) {
            $("#evaltotal").text(0)
            $("#waCalidadModal").modal('show')
            const cedula_name = (resp.info == null) ? "-" : resp.info.name
            $("#cedula_name").html(cedula_name);
            if ( resp.data == false ) {
                salida = "No hay formulario activo para ésta campaña.";
            } else {
                salida = "";
                resp.data.forEach(function(fila){
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
                $("#eval_id").val(id_session);
            }
            $("#calidadbody").html(salida);
        },'json')
        .fail((err) => cw.ajaxFail(err));
    },
    resetFormEQM: () => {
        $('#card_eval_msg').hide();
        $("#form_eqm").trigger("reset");
        $("#form_eqm :input[name=id]").val(0);
        $('#temp_msg').html('');
    },
    saveECM:() => {
        let sRating = $('#form_eqm input[name="rating"]:checked')
        const rating = ( typeof( sRating.val() ) === 'undefined' ) ? 0 : sRating.val();
        const data = {
            id: $('#form_eqm input[name=id]').val(),
            id_whatsapp_entry: $('#form_eqm input[name=id_whatsapp_entry]').val(),
            comment: $('#form_eqm :input[name=comment]').val(),
            rating: rating
        }
        $.post(site_url+'calidad/wa_save_ecm', data, function(res) {
            if (res.success) {
                toastmsg(res.msg, "success");
                cw.resetFormEQM();
                $("#row-cmsg-"+data.id_whatsapp_entry).html(res.c_rating);
            } else {
                toastmsg(res.msg, "danger");
            }
        },'json')
        .fail((err) => cw.ajaxFail(err));
    },
    saveECS: () => {
        const data =  $('#eval_form').serialize();
        const id_session = $('#eval_id').val();
        $.post(site_url+'calidad/wa_save_ecs', data, function(res) {
            if (res.success) {
                toastmsg(res.msg, "success");
                $("#waCalidadModal").modal('hide')
                $("#btn-cses-"+id_session).html(res.html);
            } else {
                toastmsg(res.msg, "danger");
            }
        },'json')
        .fail((err) => cw.ajaxFail(err));
    },
    load_more: () => { // boton Cargar anteriores
        if( cw.current_session == -1 ) {
            toastmsg('Sin mensajes para mostrar', "success");
        } else {
            cw.getConversation();
        }
    },
    renderCuentas: (cuentas, id_wc) => {
        if( cuentas !== false ) {
            cw.cuentas = [];
            options = ``;
            cuentas.map( (cuenta) => {
                cw.cuentas.push(cuenta);
                options += `<option value="${cuenta.id}" data-cuenta="${cuenta.cuenta}">${cuenta.nombre}</option>`;
            });
            $("#id_wc").html(options);
        }
        cw.id_wc = id_wc;
        $("#id_wc").val(id_wc);
    },
    renderAgentes: (agentes, id_agente) => {
        if( agentes !== false ) {
            options = `<option value="">-Seleccione-</option>`;
            agentes.map( (agente) => {
                options += `
                <option value="${agente.id}">${agente.nombre}</option>
                `;
            });
            $("#id_agente").html(options);
        }
        cw.id_agente = id_agente;
        $("#id_agente").val(id_agente);
    },
    renderContactos: (contactos) => {
        let html = '';
        if( contactos.rows.length == 0 ) {
            html = `<p>${contactos.msg}</p>`;
        }
        contactos.rows.map( (row) => {
            html += `
            <p class="wacontact" data-contacto="0">
                <a id="item_contacto_${row.id_contact}" class="waactivate border-primary rounded-pill px-2" onclick="cw.getConversationBefore('${row.id_contact}', '${row.account}', '${row.name}')" href="#">${row.name}</a>
            </p>
            `;
        });
        $("#wacontactos").html(html);
    },
    ajaxFail:(err) => {
        console.log('Error: '+err)
        toastmsg("Error de red, revisa tu conexión e intenta nuévamente.", "danger");
    }
}