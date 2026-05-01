const chatiman = {
    meses: Array('nada', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'),
    to: "",
    up: {},
    act: "",
    formatoFecha: "DD-MM-yy hh:mm:ss",
    enviarmsg: () => {
        if(chatiman.to === "") {
            toastmsg("No has elegido destino." + chatiman.to, "danger");
            return false;
        }
        if(chatiman.to === "0" && chatiman.up["emd"] === 0) {
            toastmsg("No puedes enviar mensajes de difusión. Consulta con tu administrador.", "danger");
            return false;
        }
        if(chatiman.up["ems"] === 0 && wsUsers[chatiman.to].perfil != 'agente') { // perfil_destinatario supervisor
            toastmsg("No puedes enviar mensajes a un supervisor. Consulta con tu administrador.", "danger");
            return false;
        }
        if(chatiman.up["emu"] === 0 && wsUsers[chatiman.to].perfil == 'agente'){ // perfil_destinatario agente
            toastmsg("No puedes enviar mensajes a otros usuarios. Consulta con tu administrador.", "danger");
            return false;
        }
        powsockfun.send({a: "chat", to: chatiman.to, msg: $("#chi_nuevomensaje").val()});
        $("#chi_nuevomensaje").val('');

        return true;
    },
    getChat: data => {
        if (chatiman.act == "more" && data.ents.length < 6) {
            $("#l_messages_"+chatiman.to).html("Inicio de la conversación").removeClass("last_messages");
        }
        data.ents.forEach(item => {
            chatiman.armaChiGlobo(item);
        });
        chatiman.act = "";
    },
    armaChiGlobo: (data, history='current') => {
        if(data.mensaje.length > 1 ) {
            let direccion = (data.id_usuario_emite == agente.id) ? 'saliente' : 'entrante';
            let dirclass = (direccion=='saliente') ? 'justify-content-end' : 'justify-content-start';
            let output = "<div class='row "+dirclass+" chi_message' data-id='"+data.id+"'>";
            let nombre = (direccion=='saliente') ? 'Tú' : wsUsers[data.id_usuario_emite].nombre;
            output+="       <div class='col-8 chimessage chi-"+direccion+"'>";
            output+="           <div class=''><b>"+nombre+" :</b></div>";
            output+="           <div class='chimsgbody'>"+data.mensaje+"</div>";
            output+="           <div class='row justify-content-between chimsgfecha'><i class='col-auto'>"+
                moment(data.fecha_envio).format(chatiman.formatoFecha)+"</i><i class='col-auto' id='ack_"+data.id+"'>oo</i></div>";
            output+="       </div>";
            output+= "</div>";
            let idMsg = (direccion == 'entrante') ? data.id_usuario_emite : data.id_usuario_recibe;
            if( data.id_usuario_emite == 0 || data.id_usuario_recibe == 0 ) {
                idMsg = 0;
            }
            // Llega sólo, nuevo mensaje
            if(chatiman.act == "") {
                $("#chi_messages_uid_"+idMsg).append(output).data('mid', data.id);
                chatiman.scrollspace('chi_messages_uid_'+idMsg);
                if (chatiman.to==idMsg && $("#chatinterno_body").css('display') == "block") {
                    powsockfun.send({a:"read",id:idMsg});
                } else {
                    $("#uid_"+idMsg).addClass('newChat');
                    $("#nuevochatinterno").css({"display":"block"});
                    toastmsg('Tienes un nuevo mensaje de chat de '+wsUsers[data.id_usuario_emite].nombre);
                    notifyMe({msg:'Tienes un nuevo mensaje de '+wsUsers[data.id_usuario_emite].nombre,tit:'Chat'});
                }
            } else { // historia o conversación inicial
                $("#chi_messages_uid_"+idMsg).prepend(output).data('mid', data.id);
                if (chatiman.act == "ini") {
                    chatiman.scrollspace('chi_messages_uid_'+idMsg);
                } else { // chatiman.act == "more"
                    chatiman.scrollspace('chi_messages_uid_'+idMsg, "top");
                }
            }
            $("#l_messages_"+chatiman.to).prependTo($("#chi_messages_uid_"+idMsg));
        } else {
            return false;
        }
    },
    scrollspace: (obj, dir = "bot") => {
        var objDiv = document.getElementById(obj);
        if (dir == "bot") {
            objDiv.scrollTop = objDiv.scrollHeight;
        } else {
            objDiv.scrollTop = 0;
        }
    },
    update_users: () => {
        newChat="";
        selectedUser="";
        if(undefined!==$("#uid_0").attr('class') && $("#uid_0").attr('class').indexOf('newChat') > 0 ) {
            newChat = " newChat ";
        }
        if(undefined!==$("#chiu_0").attr('class') && $("#chiu_0").attr('class').indexOf('active') > 0 ) {
            selectedUser = " active ";
        }
        var html = "<li class='chi_liuser "+selectedUser+"' id='chiu_0' data-uid='0'><div class='chi_user chi_user_difusion "+newChat+"' id='uid_0'>Difusión</div></li>";
        var html_offline = "";
        chatiman.up = chatiman.traduce_permisos(agente.perci);
        if(chatiman.up["pc"] == 1) {
            $("#chatinterno_container").css({"display": "block"});
            Object.keys(wsUsers).forEach(function(key) {
                if(wsUsers[key].id != agente.id) {
                    wsUsers[key].chatiperm = chatiman.traduce_permisos(wsUsers[key].perci);
                    if(wsUsers[key].perfil=='supervisor' || wsUsers[key].perfil=='superior' || wsUsers[key].perfil=='admin') {
                        sup_class = " chi_liuser-supervisor";
                        sup = "<span class='bsuper'><i class='fas fa-star'></i></span>";
                    } else {
                        sup_class = " chi_liuser-agente";
                        sup = "";
                    }
                    perfil_class = (wsUsers[key].perfil=='agente') ? 'agente' : 'supervisor';
                    newChat="";
                    selectedUser="";
                    if(undefined!==$("#uid_"+key).attr('class') && $("#uid_"+key).attr('class').indexOf('newChat') > 0 ) {
                        newChat = " newChat ";
                    }
                    if(undefined!==$("#chiu_"+key).attr('class') && $("#chiu_"+key).attr('class').indexOf('active') > 0 ) {
                        selectedUser = " active ";
                    }
                    if(wsUsers[key].online && wsUsers[key].sid != "" && wsUsers[key].chatiperm["pc"] == '1'  ) {
                        html += "<li class='chi_liuser"+sup_class+selectedUser+"' id='chiu_"+wsUsers[key].id+"' data-uid='"+wsUsers[key].id+"'>";
                        html += "<div class='user_online_status chat_online'></div>";
                        html += "<div class='chi_user "+perfil_class+newChat+"' id='uid_"+wsUsers[key].id+"'>";
                        if( (wsUsers[key].nombre).length > 22 ) {
                            html += (wsUsers[key].nombre).substring(0,22)+'...';
                        } else {
                            html += wsUsers[key].nombre;
                        }
                        html += "</div>"+sup;
                        html += "</li>";
                    } else {
                        html_offline += "<li class='chi_liuser"+sup_class+selectedUser+"' id='chiu_"+wsUsers[key].id+"' data-uid='"+wsUsers[key].id+"'>";
                        html_offline += "<div class='user_online_status'></div>";
                        html_offline += "<div class='chi_user "+perfil_class+newChat+"' id='uid_"+wsUsers[key].id+"'>";
                        if( (wsUsers[key].nombre).length > 22 ) {
                            html_offline += (wsUsers[key].nombre).substring(0,22)+'...';
                        } else {
                            html_offline += wsUsers[key].nombre;
                        }
                        html_offline += "</div>"+sup;
                        html_offline += "</li>";
                    }
                    if ($("#chi_messages_uid_"+key).length == 0) {
                        $("#chi_messages_container").append("<div class='chi_messages' id='chi_messages_uid_"+key+"' data-mid='0'><div class='last_messages' id='l_messages_"+key+"'>Mensajes anteriores</div></div>");
                    }
                }
            });
        } else {
            $("#chatinterno_container").css({"display": "none"});
        }
        $("#chi_userlist").html(html+html_offline);
    },
    traduce_permisos: permisos => {
        permisos = ('undefined' === typeof permisos || permisos.length != 9) ? '0,0,0,0,0' : permisos;
        permisos = permisos.split(",");
        output = new Array();
        indices = new Array("pc","emd","ems","emu","rmu");
        for(h=0; h<permisos.length; h++){
            output[indices[h]]=permisos[h];
        };
        return output;
    }    
}

$(document).on("click", ".openchatinterno, #chatinterno_body .close", function(){
    $("#chatinterno_body").toggle().css("display");
    if (chatiman.to != "" && $("#uid_"+chatiman.to).hasClass("newChat")) {
        powsockfun.send({a:"read",id:chatiman.to})
        $("#uid_"+chatiman.to).removeClass("newChat");
        $("#nuevochatinterno").css({"display":"none"});
        chatiman.scrollspace('chi_messages_uid_'+chatiman.to);
    }
});
$(document).on("click", ".last_messages", function(){
    chatiman.act = "more";
    let mid = $("#chi_messages_uid_"+chatiman.to).data('mid');
    powsockfun.send({a:'conv', to: chatiman.to, mid: mid});
});
$(document).on("click", ".chi_liuser", function(){
    chatiman.to = $(this).data('uid');
    let mid = $("#chi_messages_uid_"+chatiman.to).data('mid');
    if (mid == 0) {
        chatiman.act = "ini";
        powsockfun.send({a: 'conv', to: chatiman.to});
    }
    $(".chi_messages").css({"display": "none"});
    $("#chi_messages_uid_"+chatiman.to).css({"display": "block"});
    $(".chi_liuser").removeClass("active");
    if ($("#uid_"+chatiman.to).hasClass("newChat")) {
        powsockfun.send({a:"read",id:chatiman.to})
        $("#uid_"+chatiman.to).removeClass("newChat");
    }
    $("#chiu_"+chatiman.to).addClass("active");
    $("#nuevochatinterno").css({"display":"none"});
    chatiman.scrollspace('chi_messages_uid_'+chatiman.to);
});
$(document).on("click", "#chi_enviarmensaje",function(e) {
    e.preventDefault();
    chatiman.enviarmsg();
});
$(document).on('keydown', '#chi_nuevomensaje', function(e){
    var keycode = (e.keyCode ? e.keyCode : (e.which ? e.which : e.key));
    if(keycode == 13 && !e.shiftKey ){
        e.preventDefault();
        chatiman.enviarmsg();
    }
});
$(document).ready(function(){
    if(agente) {
        chatiman.up = chatiman.traduce_permisos(agente.perci);
        if(chatiman.up["pc"] == 1) {
            $("#chatinterno_container").css({"display": "block"});
        }
    }
});
