const achat = {
    idSesActiva: null,
    sesiones: {},
    msgs: {},
    msgsarea: document.getElementById('achat-msgs'),
    acsendbtn: document.getElementById('achat-send-btn'),
    sessarea: document.getElementById('achat-sesiones'),
    inputext: document.getElementById('achat-input'),
    acfin: document.getElementById('achat-ir'),
    acfinbtn: document.getElementById('achat-ir-btn'),
    procesamsg: msg => {
        console.log("llegó mensaje achat");
        console.log(msg);
        switch (msg.b) {
            case 'acciones':
                let trans = true;
                let lasac = "<option value=\"0\">Agradecer y terminar</option>";
                for (let i = 0; i < msg.acciones.length; i++) {
                    if (msg.acciones[i].id == 1) {
                        lasac += "<option value=\"1\">Enviar a encuesta</option>";
                    } else {
                        lasac += `<option value="${msg.acciones[i].id}">${msg.acciones[i].nombre}</option>`;
                    }
                    if (trans) {
                        lasac += "<optgroup label=\"Transferir a\">";
                        trans = false;
                    }
                }
                lasac += "</optgroup>";
                achat.acfin.innerHTML = lasac;
                break;
            case 'ses':
                achat.sesiones[msg.ses.id] = msg.ses;
                achat.llegaChat(msg.ses);
                break;
            case 'previo':
                if (undefined === achat.sesiones[msg.ses.id]) {
                    achat.sesiones[msg.ses.id] = msg.ses;
                    achat.llegaChat(msg.ses);
                    if (msg.ses.entries) {
                        achat.msgs[msg.ses.id] = "";
                        msg.ses.entries.forEach(entry => {
                            if (entry.type == 'Entrante') {
                                achat.msgs[msg.ses.id] += "<div class=\"row achat-msg-in mt-2\">" + entry.message + "</div>";
                            } else {
                                achat.msgs[msg.ses.id] += "<div class=\"row achat-msg-out mt-2\">" + entry.message + "</div>";
                            }
                        });
                    }
                }
                break;
            case 'chat':
                achat.msgs[msg.acsid] += "<div class=\"row achat-msg-in mt-2\">" + msg.msg + "</div>";
                if (achat.idSesActiva == msg.acsid) {
                    achat.msgsarea.innerHTML = achat.msgs[msg.acsid];
                    achat.msgsarea.scrollTop = achat.msgsarea.scrollHeight;
                }
                break;
            case 'fin':
                delete achat.sesiones[msg.acsid];
                delete achat.msgs[msg.acsid];
                document.getElementById(`chtbtn-${msg.acsid}`).remove();
                if (achat.idSesActiva == msg.acsid && Object.keys(achat.sesiones).length == 0) {
                    achat.inputext.value = "";
                    achat.inputext.disabled = true;
                    achat.acsendbtn.disabled = true;
                    achat.acfin.disabled = true;
                    achat.acfin.value = "0";
                    achat.acfinbtn.disabled = true;
                    achat.msgsarea.innerHTML = "";
                    achat.idSesActiva = null;
                    $(".nav-link[href=#chat] .badge").remove();
                } else {
                    achat.activaChat(Object.keys(achat.sesiones)[0]);
                }
                break;
            case 'error':
                console.log("Error: " + msg.msg);
                break;
            default:
                console.log("Mensaje de error o desconocido recibido");
                console.log(msg);
        }
    },
    llegaChat: ses => {
        achat.msgs[ses.id] = "<p>Conversación entrante:</p>";
        let chat = document.createElement('div');
        chat.className = "achat-act-btn row mt-2";
        chat.setAttribute("id", `chtbtn-${ses.id}`);
        chat.setAttribute("data-sid", ses.id);
        let horachat = new Date(ses.start).toLocaleTimeString().split(' ')[0];
        chat.innerHTML = `<div class="col">
                <button class="achat-sesion btn btn-secondary w-100">${ses.id} ${horachat}</button>
            </div>
        </div>`;
        achat.sessarea.innerHTML += chat.outerHTML;
        notifyMe({msg:'Tienes un Assertive chat'});
        toastmsg("Tienes un Assertive chat");
        if (Object.keys(achat.sesiones).length == 1) {
            $(".nav-link[href=#chat]").append('<span class="badge">1</span>');
        }
    },
    activaChat: id => {
        let botonactivo = document.querySelector('.achat-sesion.btn-primary');
        if (botonactivo != null) {
            botonactivo.classList.remove('btn-primary');
            botonactivo.classList.add('btn-secondary');
            botonactivo.disabled = false;
        }
        let boton = document.getElementById(`chtbtn-${id}`).querySelector('.achat-sesion');
        boton.classList.remove('btn-secondary');
        boton.classList.add('btn-primary');
        boton.disabled = true;

        achat.idSesActiva = id;
        achat.msgsarea.innerHTML = achat.msgs[id];
        powsockfun.send({a: 'iniciar', acsid: achat.idSesActiva});
        achat.inputext.disabled = false;
        achat.acsendbtn.disabled = false;
        achat.acfin.disabled = false;
        achat.acfinbtn.disabled = false;
    },
    enviaChat: () => {
        if (achat.inputext.value == "") {
            return;
        }
        let msg = achat.inputext.value;
        achat.msgs[achat.idSesActiva] += "<div class=\"row achat-msg-out mt-2\">" + msg + "</div>";
        achat.msgsarea.innerHTML = achat.msgs[achat.idSesActiva];
        powsockfun.send({a: 'chat', dir: 'o', acsid: achat.idSesActiva, msg: msg});
        achat.inputext.value = "";
        achat.msgsarea.scrollTop = achat.msgsarea.scrollHeight
    },
    iraChat: () => {
        powsockfun.send({a: 'terminar', acsid: achat.idSesActiva, msg: achat.acfin.value});
        delete achat.sesiones[achat.idSesActiva];
        delete achat.msgs[achat.idSesActiva];
        document.getElementById(`chtbtn-${achat.idSesActiva}`).remove();
        if (Object.keys(achat.sesiones).length == 0) {
            achat.idSesActiva = null;
            achat.inputext.value = "";
            achat.inputext.disabled = true;
            achat.acsendbtn.disabled = true;
            achat.acfin.disabled = true;
            achat.acfin.value = "0";
            achat.acfinbtn.disabled = true;
            achat.msgsarea.innerHTML = "";
            $(".nav-link[href=#chat] .badge").remove();
        } else {
            achat.activaChat(Object.keys(achat.sesiones)[0]);
        }
    },
}

$(document).ready(() => {
    $(document).on('click', '.achat-act-btn', function() {
        achat.activaChat($(this).data('sid'));
    });
    $(document).on('click', '#achat-send-btn', function() {
        achat.enviaChat();
    });
    $(document).on('click', '#achat-ir-btn', function() {
        achat.iraChat();
    });
});
