var preguntarfb = null;
var selecteduser = "";
var currentuser = "";
var meses = Array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
var users = "";
var openconversation = "";
var init_fb;
var veralertafb = false;
$(document).ready(function(){
    if (agente.permisoSec.includes('facebook')) {
        if (!preguntarfb) {
            preguntarfb = setInterval(function(){ traer_asignacionfb() }, 10000);
        }
    }
    initFB();
    setTimeout(get_usuarios, 2500);
    var oc = setInterval(getNuevos, 6000);
	$("#fbfinish").click(function(){
		if(confirm("¿estás seguro que deseas terminar esta conversación?")){
			usr = currentuser.replace('fbc-','');
			ids = usr.split('-');
			fb_usr = ids[1];
			fb_con = ids[0];
            openconversation="";
			$.ajax({
				url:'facebook/release',
				method:'post',
				data: {fb_id: fb_usr},
				success:function(t) {
					$(".wrapperfbuserclick").removeClass("wrapperfbuserclick").addClass("wrapperfbuser");
					limpiarCampo("fbtext");
					$("#fbtext").attr({"disabled":true});
					$("#fbmsgs").html("");
				}
			});
		}
	});
    $("#fbsrch").on("keyup", function(e){
        buscar();
    });

    $("#fbsendmessage").click(function(){
        if (currentuser.length < 1) {
            return false;
        }
        msg = $("#fbtext").val();
        enviarMensaje(currentuser, msg);
        limpiarCampo("fbtext");
        scrollspace('fbmsgs');
        return false;
    });
    $("#fbtext").on("focus", function(){
        $(this).keypress(function(e) {
            var keycode = (e.keyCode ? e.keyCode : (e.which ? e.which : e.key));
            if(keycode == 13) {
                msg = $(this).val();
                enviarMensaje(currentuser, msg);
                limpiarCampo("fbtext");
                scrollspace('fbmsgs');
                return false;
            }
        });
    });
});
$("#sendFBmedia").click(function(e){
    e.preventDefault();
    usr = currentuser.replace('fbc-','');
    ids = usr.split('-');
    fb_usr = ids[1];
    fb_con = ids[0];
    $("#fb_contact").val(fb_usr);
    var data = $("#enviarFbMediaForm").serializefbfile();
    for (var p of data) {
        console.log(p);
    }
    $.ajax({
        url: "facebook/sendmedia",
        type: 'POST',
        method: "POST",
        cache: false,
        processData: false,
        contentType: false,
        dataType: 'json',
        data: data,
        success: function(t) {
            //console.log(t);
        }
    })
    .done(function(resp){
        if (resp.status == 'error') {
            toastmsg(resp.msg, "danger");
        }
    })
    .fail(function(resp) {
        toastmsg("El servidor no ha contestado, revisa la información.", "danger");
    });
});
function traer_asignacionfb() {
    $.ajax({
        url: "facebook/traer_asignfb",
        method: "post",
        async: false,
        success: function(t) {
            obj = $.parseJSON(t);
            if(obj.length < 1) {
                return false;
            }
            c = obj[0].c;
            id_user = obj[0].id_user;
            if(c>0) {
                veralertafb=true;
            }
            if (veralertafb) {
                toastmsg("Tienes un facebook.");
                $("[href='#facebook']").append('<span class="badge fbbadge">1</span>');
                veralertafb = false;
            }
            $(".wrapperfbuser").each(function(i, j){
                id = $(this).attr('id');
                if( id.indexOf( id_user ) >-1 ) {
                    $("#"+id).addClass('wrapperfbusernewmessage');
                }
            });
        }
    });
}

function initFB(){
    $.ajax({
        url: 'facebook/FBdata',
        async: true,
        success: function(t) {
            if(t==false) {
                init_fb=false;
            } else {
                init_fb=true;
            }
        }
    });
}
function get_usuarios() {
    if(!init_fb) {
        return false;
    }
	$.ajax({
		url: 'facebook/getusers',
		method: 'post',
		async: false,
		success: function(t){
			users = t;
		}
	});
    list_users(users);
    accionesusuarios();
}
function accionesusuarios(){
    $(".wrapperfbuser").unbind('click').click(function(){
        currentuser = $(this).attr('id');
        usr = currentuser.replace('fbc-','');
        ids = usr.split('-');
        fb_usr = ids[1];
        fb_con = ids[0];
        $.ajax({
            url: 'facebook/ismyuser',
            method: 'post',
            data: {usr_id: fb_usr},
            success: function(t) {
                if(t=='true') {
                    get_conversation(currentuser);
                } else {
                    toastmsg("esta conversación está asignada a otro agente.", "danger");
                }
            }
        });
    });
}
function get_conversation(id) {
    if(currentuser==selecteduser) {
        //return false;
    }
    $(".wrapperfbuserclick").removeClass('wrapperfbusernewmessage');
    $(".wrapperfbuserclick").removeClass("wrapperfbuserclick").removeClass('wrapperfbusernewmessage').addClass("wrapperfbuser");
    $("#"+id).removeClass("wrapperfbuser").addClass("wrapperfbuserclick");
    toggleDisabled("fbtext");
    currentuser = id;
    usr = currentuser.replace('fbc-','');
    ids = usr.split('-');
    fb_usr = ids[1];
    fb_con = ids[0];
    openconversation = fb_con;
    $("#fbmsgs").html("");
    $.ajax({
        url: 'facebook/usermensajes',
        data: {usr: fb_con},
        method: 'post',
        success: function(t) {
            obj = $.parseJSON(t);
            $.each(obj, function(k, l){
                mensaje = l.message;
                url = l.url;
                type = (l.type).toLowerCase();
                fbtype = (l.fbtype).toLowerCase();
                datetime = l.datetime_message;
                globo = armaGlobo(mensaje, url, type, fbtype, datetime);
                imprimeglobo(globo);
                scrollspace('fbmsgs');
                $(".fbbadge").remove();
            });
        }
    });
}
function toggleDisabled(obj) {
    status = $("#"+obj).attr("disabled");
    if(status) {
        $("#"+obj).attr({"disabled":false});
    } else {
        $("#"+obj).attr({"disabled":true});
    }
}
function enviarMensaje(usr, msg) {
	usr = usr.replace('fbc-','');
	ids = usr.split('-');
	fb_usr = ids[1];
	fb_con = ids[0];
	$.ajax({
		url:'facebook/send',
		method: 'post',
		data: {fb_id:fb_usr, conv: fb_con, message: msg},
		success: function(t) {
			FBdata();
            getNuevos();
		}
	});
}
function limpiarCampo(obj) {
    $("#"+obj).val($.trim(""));
}
function armaGlobo(msg, url, direccion, fbtype, datetime) {
    row = (direccion=='saliente')?'fb-justify-content-end':'';
    if( msg.length > 1 || url.length > 0 ) {
        output = "<div class='row "+row+"'>";
        output+="       <div class='fbmessage fb-"+direccion+"'>";
        if(msg.length>0) {
            output+="           <div class='fbmsgbody'>"+msg+"</div>";
        }
        if(url.length>0) {
            if( fbtype.indexOf("image")>=0 ) {
                output+="           <div class='fbmsgbody'><a href='"+url+"' target='_blank'><img src='"+url+"' style='max-width: 230px' /></a></div>";
            }
            if( fbtype.indexOf("video")>=0 ) {
                nurl = url.substring(0, url.lastIndexOf("/") ) + "/thumbs/"+url.substring(url.lastIndexOf("/")+1, url.lastIndexOf(".") )+".jpg";
                output+="           <div class='fbmsgbody'><a href='"+url+"' target='_blank'><img src='"+nurl+"' style='max-width: 230px' /></a></div>";
            }
            if( fbtype.indexOf("applicatio")>=0 ) {
                output+="           <div class='fbmsgbody'><a href='"+url+"' target='_blank'>"+url.substring(url.lastIndexOf("/")+1, 1000)+"</a></div>";
            }
            if( fbtype.indexOf("audio")>=0 ) {
                output+="           <div class='fbmsgbody'><audio controls><source src='"+url+"' type='"+fbtype+"'></audio></div>";
            }
        }
        output+="           <div class='fbmsgfecha'><i>"+format_fecha(datetime, direccion)+"</i></div>";
        output+="       </div>";
        output+= "</div>";
        return output;
    } else {
        return false;
    }
}
function imprimeglobo(globo) {
    if(globo) {
        $("#fbmsgs").append(globo);
    }
}
function scrollspace(obj) {
    var objDiv = document.getElementById(obj);
    objDiv.scrollTop = objDiv.scrollHeight;
}
function format_fecha(obj_fecha, direccion) {
    pieces1 = obj_fecha.split(" ");
    f = pieces1[0].split("-");
    h = pieces1[1].split(":");
    txt = (direccion=='entrante')?txt='Recibido':txt='Enviado';
    return txt+" a las " + h[0] + ":" + h[1] + ", el " + f[2] + " de " + meses[parseInt(f[1])-1] + " del " + f[0];
}
function list_users(obj){
	output = "";
	usrs = $.parseJSON(obj);
	$.each(usrs, function(i, j){
		id = j.id;
		conversation_id = j.conversation_id;
		name = j.name;
        output+=armarlistado(conversation_id, id, name);
	});
	$("#fbusersviewport").html(output);
}
function armarlistado(cid, fid, nombre) {
    output = "";
    output+="<div id='fbc-"+cid+"-"+fid+"' class='wrapperfbuser'>";
    output+="   <a class='fbactivate' href='#'>"+nombre+"</a>";
    output+="</div>";
    return output;
}
function FBdata() {
    $.ajax({
        url: 'facebook/FBdata'
    });
}
function getNuevos() {
    if(!init_fb) {
        return false;
    }
    id = openconversation;
    if(id.length<=0) {
        return true;
    }
    $.ajax({
        url: 'facebook/getNuevos',
        method: 'post',
        data: {id_con: id},
        success: function(t) {
            obj = $.parseJSON(t);
            mensajes = obj["mensajes"];
            usuarios = obj["usuarios"];
            output="";
            n_usr=0;
            $.each(usuarios, function(i, j){
                id_ue = $("#fbc-"+j.fb_conversation_id+"-"+j.fb_id).length;
                if(id_ue < 1) {
                    output+=armarlistado(j.fb_conversation_id, j.fb_id, j.name);
                }
                n_usr++;
            });
            if(n_usr>0) {
                $("#fbusersviewport").append(output);
                accionesusuarios();
            }
            $.each(mensajes, function(k,l){
                mensaje = l.message;
                url = l.url;
                type = (l.type).toLowerCase();
                fbtype = (l.fbtype).toLowerCase();
                datetime = l.datetime_message;
                globo = armaGlobo(mensaje, url, type, fbtype, datetime);
                imprimeglobo(globo);
                scrollspace('fbmsgs');
            });
        }
    });
}
function buscar() {
    aguja = $("#fbsrch").val().toUpperCase();
    $(".fbactivate").each(function(i,j){
        pajar = $(this).text().toUpperCase();
        id = $(this).parent().attr('id');
        busqueda=pajar.indexOf(aguja);
        if(busqueda<0){
            $("#"+id).css({'display':'none'});
        } else {
            $("#"+id).css({'display':'block'});
        }
    });
}
(function($) {
    $.fn.serializefbfile = function() {
        var obj = $(this);
        /* ADD FILE TO PARAM AJAX */
        var formData = new FormData();
        $.each( $(obj).find("input[type='file']"), function(i, tag){
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        var params = $(obj).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        return formData;
    };
})(jQuery);
