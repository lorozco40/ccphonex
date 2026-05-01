var preguntarwasig = null;
var preguntarnuewa = null;
var walinksenabled = true;
var idWaCtaActiva  = 0;
var wacons         = {
    veralerta: false,
    comunicar: true,
    waminid: 0,
    activarCto: function(cid, wac, eti) {
        if (cid>0 && cid == wactas[idWaCtaActiva].idContactoActivo) {
            toastmsg("Contacto activo.", "danger");
            $("#watext"+idWaCtaActiva).focus();
            return false;
        }
        wacons.limpiarwa();
        $("#wacontactname"+idWaCtaActiva).text(eti);
        $("#wasaaa"+idWaCtaActiva+" .wawac, #enviarWaMediaForm .wawac").val(wac);
        if (cid != "0") { // 0 es de masivos
            wacons.traer_data(cid);
        } else {
            wacons.traer_masivos();
        }
    },
    buscarcontacto: function() {
        $("#wabuscres"+idWaCtaActiva).html("");
        $.post(site_url+"whatsapp/buscacontacto",
        {abuscar: $("#wabusc"+idWaCtaActiva).val(), wid:idWaCtaActiva},function(data){
            $("#wabuscres"+idWaCtaActiva).html(data);
        },'json')
        .fail(function() {
            toastmsg("Error de red, revisa tu conexión e intenta nuévamente.", "danger");
        });
    },
    traerAsig: function() {
        if (wacons.comunicar == true) {
            wacons.comunicar = false;
            $.post(site_url+'whatsapp/traer_asign', function(res) {
                wacons.veralerta = false;
                res.forEach(function(row) {
                    if ('undefined' != typeof(wactas[row.id_wacta]) && wactas[row.id_wacta].idsAsigned.includes(row.id) === false) {
                        wactas[row.id_wacta].idsAsigned.push(row.id);
                        $("#w"+row.id_wacta+"ac"+row.id).remove();
                        html = '<p class="wacontact wapendi" id="w'+row.id_wacta+'ac'+row.id+'" data-wac="'+row.account+'"><a class="waactivate" data-id="'+
                            row.id+'" data-sid="'+row.sid+'" data-wac="'+row.account+'" href="#">'+row.name+'</a></p>';
                        $("#wacontactos"+row.id_wacta).prepend(html).animate({ scrollTop: 0 }, 1000);
                        wacons.veralerta = true;
                        if (row.id == wactas[idWaCtaActiva].idContactoActivo) {
                            $("#wasaaa"+idWaCtaActiva+" .wasid, #enviarWaMediaForm .wasid").val(row.sid); // Asegurar que viene en row
                            $("#wasaaa"+idWaCtaActiva+" .btn").prop("disabled", false);
                            $("#transferlist"+idWaCtaActiva).val(0).prop("disabled", false);
                            if (typeof 'undefined' === wactas[idWaCtaActiva].encuesta || !wactas[idWaCtaActiva].encuesta) {
                                $("#wasaaa"+idWaCtaActiva+" .waencytermbtn").prop("disabled", true);
                            }
                        }
                    }
                });
                if (wacons.veralerta) {
                    notifyMe({msg:'Tienes un Whasapp'});
                    toastmsg("Tienes un WhatsApp.");
                    $("[href='#whatsapp']").append('<span class="badge wabadge">1</span>');
                    $.each(wactas, function(i, cta){
                        if(cta.idsAsigned.length>0) {
                            $("#whatsapptab .nav-link[data-id="+i+"]").append('<span class="badge wabadge">1</span>');
                        }
                    });
                    wacons.veralerta = false;
                }
                wacons.comunicar = true;
            },'json')
            .fail(function() {
                wacons.comunicar = true;
                toastmsg("Error de red, revisa tu conexión e intenta nuévamente.", "danger");
            });
        }
    },
    traer_nuewa: function() {
        if (wacons.comunicar == true) {
            wacons.comunicar = false;
            if (wactas[idWaCtaActiva].idContactoActivo > 0) {
                $.post(site_url+'whatsapp/traer_nuewa', {wid:idWaCtaActiva, cid:wactas[idWaCtaActiva].idContactoActivo, lastid:$("#wasaaa"+idWaCtaActiva+" .walastid").val()}, function(res) {
                    if (res.conver) {
                        $("#wamsgs"+idWaCtaActiva).append(res.conver).animate({ scrollTop: ($("#wamsgs"+idWaCtaActiva).prop("scrollHeight") + 100)}, 1000);
                    }
                    if (res.lastid) {
                        $("#wasaaa"+idWaCtaActiva+" .walastid").val(res.lastid);
                    }
                    wacons.comunicar = true;
                },'json')
                .fail(function() {
                    toastmsg("Error de red, revisa tu conexión.", "danger");
                    wacons.comunicar = true;
                });
            } else {
                clearInterval(preguntarnuewa);
                preguntarnuewa = null;
                wacons.comunicar = true;
            }
        }
    },
    traer_data: function(cid, toid = false) {
        wacons.detenerwa();
        $.ajax({
            type: 'POST',
            cache: false,
            url: site_url+'whatsapp/traer_data',
            data: {wid:idWaCtaActiva, cid:cid, toid:toid},
            dataType: 'json',
        })
        .done(function(res){
            if (res.error) {
                toastmsg(res.error, "danger");
                if (!toid) {
                    wacons.limpiarwa();
                }
            } else {
                wactas[idWaCtaActiva].idContactoActivo = cid;
                wacons.waminid = res.waminid;
                $("#wasaaa"+idWaCtaActiva+" .wacid, #enviarWaMediaForm .wacid").val(cid);
                $("#wasaaa"+idWaCtaActiva+" .wasid, #enviarWaMediaForm .wasid").val(res.sid);
                if( toid === false ) {
                    $("#wasaaa"+idWaCtaActiva+" .walastid, #enviarWaMediaForm .walastid").val(res.lastid);
                }
                if (res.lastid == 0) {
                    toastmsg("Sin mensajes para mostrar");
                    $("#wa-cargar-mas"+idWaCtaActiva).addClass("d-none");
                } else {
                    if (toid) {
                        $("#wamsgs"+idWaCtaActiva).prepend(res.conver).animate({ scrollTop: 0}, 1000);
                    } else {
                        $("#wamsgs"+idWaCtaActiva).html(res.conver).animate({ scrollTop: ($("#wamsgs"+idWaCtaActiva).prop("scrollHeight") + 100)}, 1000);
                    }
                    $("#wa-cargar-mas"+idWaCtaActiva).removeClass("d-none");
                }
                $("#watext"+idWaCtaActiva).val("").prop("disabled", false).focus();
                $("#wasaaa"+idWaCtaActiva+" .btn:not(.wabuscbtn)").prop("disabled", false);
                if (wactas[idWaCtaActiva].idsAsigned.includes(''+cid) === false) {
                    $("#wasaaa"+idWaCtaActiva+" .waencytermbtn, #wasaaa"+idWaCtaActiva+" .waterminabtn, #wasaaa"+idWaCtaActiva+" .watransferir").prop("disabled", true);
                    $("#transferlist"+idWaCtaActiva).val(0).prop("disabled", true);
                } else {
                    $("#transferlist"+idWaCtaActiva).val(0).prop("disabled", false);
                }
                if (typeof 'undefined' === wactas[idWaCtaActiva].encuesta || !wactas[idWaCtaActiva].encuesta) {
                    $("#wasaaa"+idWaCtaActiva+" .waencytermbtn").prop("disabled", true);
                }
                if (window.innerWidth > 992) {
                    window.scrollTo(0, 0);
                } else {
                    window.scrollTo(0, $("#lostabos").offset().top);
                }
                $("#w"+idWaCtaActiva+"ac"+cid).addClass("active");
            }
            wacons.reanudarwa();
        })
        .fail(function(data) {
            toastmsg("Error de red, revisa tu conexión e intenta nuévamente.", "danger");
            wacons.reanudarwa();
        });
    },
    traer_masivos: function() {
        wacons.detenerwa();
        $.post(site_url+'whatsapp/traer_masivos', {wid: idWaCtaActiva}, function(res) {
            if (res.error) {
                toastmsg(res.error, "danger");
            } else if (res.conver=="") {
                toastmsg("Sin mensajes para mostrar", "danger");
                $("#wasaaa"+idWaCtaActiva+" .wacid, #wasaaa"+idWaCtaActiva+" .wasid, #enviarWaMediaForm .wacid, #enviarWaMediaForm .wasid").val("0");
                $("#watext"+idWaCtaActiva).val("").prop("disabled", false).focus();
                if (window.innerWidth > 992) {
                    window.scrollTo(0, 0);
                } else {
                    window.scrollTo(0, $("#lostabos").offset().top);
                }
            } else {
                $("#wamsgs"+idWaCtaActiva).html(res.conver).animate({ scrollTop: $("#wamsgs").prop("scrollHeight")}, 1000);
                $("#wasaaa"+idWaCtaActiva+" .wacid, #wasaaa"+idWaCtaActiva+" .wasid, #enviarWaMediaForm .wacid, #enviarWaMediaForm .wasid").val("0");
                $("#watext"+idWaCtaActiva).val("").prop("disabled", false).focus();
                if (window.innerWidth > 992) {
                    window.scrollTo(0, 0);
                } else {
                    window.scrollTo(0, $("#lostabos").offset().top);
                }
            }
            wacons.reanudarwa();
        },'json')
        .fail(function() {
            toastmsg("Error de red, revisa tu conexión e intenta nuévamente.", "danger");
            wacons.reanudarwa();
        });
    },
    detenerwa: function() {
        walinksenabled = false;
        clearInterval(preguntarnuewa);
        preguntarnuewa = null;
        $("#watext"+idWaCtaActiva).val('Trabajando ...').prop("disabled", true);
        $("#wasaaa"+idWaCtaActiva+" .btn:not(.wabuscbtn)").prop("disabled", true);
        $("#emojiskinon").css({"display":"none"});
    },
    reanudarwa: function() {
        if (!preguntarwasig) { preguntarwasig = setInterval(function(){ wacons.traerAsig() }, 5000); }
        if (!preguntarnuewa && wactas[idWaCtaActiva].idContactoActivo > 0) {
            preguntarnuewa = setInterval(function(){ wacons.traer_nuewa() }, 3000);
        }
        walinksenabled = true;
    },
    limpiarwa: function() {
        wacons.detenerwa();
        wactas[idWaCtaActiva].idContactoActivo = "";
        $(".wacontact.active").removeClass("active");
        $("#watext"+idWaCtaActiva).val("").prop("disabled", true);
        $("#wamsgs"+idWaCtaActiva).html("");
        $(".wacid, .wasid, .wawac, .walastid").val("");
        $("#wacontactname"+idWaCtaActiva).text("Nombre de usuario");
        $("#transferlist"+idWaCtaActiva).val(0).prop("disabled", true);
        $("#wa-cargar-mas"+idWaCtaActiva).addClass("d-none");
        walinksenabled = true;
    },
    terminawases: function(enc = false) { // Debe haber un contacto activo para usar -> terminar sesión
        let sid = $("#wasaaa"+idWaCtaActiva+" .wasid").val(); // sólo existe un hidden con esa clase bajo ese id
        let cid = $("#wasaaa"+idWaCtaActiva+" .wacid").val();
        if (enc && !wactas[idWaCtaActiva].encuesta) {
            toastmsg("No hay una encuesta asignada", "danger");
        } else if (typeof 'undefined' === sid || sid == "" || sid == "0"|| sid == null || !wactas[idWaCtaActiva].idsAsigned.includes(''+cid)) {
            toastmsg("No hay una sesión activa", "danger");
        } else {
            wacons.detenerwa();
            let url = (enc) ? "whatsapp/encyterm" : "whatsapp/terminases";
            $.post(site_url+url,{cid: cid, sid: sid, wid: idWaCtaActiva}, function(data){
                if (data.error) {
                    toastmsg(data.error, "danger");
                } else {
                    $("#w"+idWaCtaActiva+"ac"+wactas[idWaCtaActiva].idContactoActivo).removeClass("wapendi");
                    $("#w"+idWaCtaActiva+"ac"+wactas[idWaCtaActiva].idContactoActivo+" a").prop("data-sid", 0);
                    wacons.limpiarwa();
                    toastmsg("Conversación finalizada, gracias.", "success");
                    let index = wactas[idWaCtaActiva].idsAsigned.indexOf(''+cid);
                    if (index > -1) {
                        wactas[idWaCtaActiva].idsAsigned.splice(index, 1);
                    }
                    let tot = 0;
                    $.each(wactas, function(i, cta){
                        asiestacta = cta.idsAsigned.length;
                        tot += asiestacta;
                        if(asiestacta == 0) {
                            $("#whatsapptab .nav-link[data-id="+i+"] .wabadge").remove();
                        }
                    });
                    if (tot == 0) {
                        $(".nav-link[href=#whatsapp] .wabadge").remove();
                    }
                }
                wacons.reanudarwa();
            },"json")
            .fail(function() {
                toastmsg("Error de red, revisa tu conexión e intenta nuévamente.", "danger");
                wacons.reanudarwa();
            });
        }
    }
}

$(document).on("submit", ".waform", function(e){
    e.preventDefault();
    let datos = $(this).serialize();
    $("#watext"+idWaCtaActiva).val("").prop("disabled", false).focus();
    $.post(site_url+"whatsapp/enviar", datos, function(data) {
        if (data.error) {
            toastmsg(data.error, "danger");
        }
    },'json')
    .fail(function() {
        toastmsg("Error de red, revisa tu conexión e intenta nuévamente.", "danger");
    });
});

$(document).on('keypress', ".emojikinon-con", function(e) {
    if(e.which == 13 && !e.shiftKey) {
        e.preventDefault();
        $(this).parents('.waform').submit();
    }
});

$(document).on("submit", "#enviarWaMediaForm", function(e) {
    e.preventDefault();
    let data = $("#enviarWaMediaForm").serializewafile();
    $("#spinnerModal").modal("show");
    $.ajax({
        type: 'POST',
        method: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        url: site_url+'whatsapp/enviar_wamedia',
        data: data,
        dataType: 'json'
    })
    .done(function(data){
        if (data.error) {
            toastmsg(data.error, "danger");
        } else {
            $('#modalWaFile').modal('hide');
            toastmsg("Enviado", "success");
        }
        if (data.sid) $("#wasaaa"+idWaCtaActiva+" .wasid, #enviarWaMediaForm .wasid").val(data.sid);
        $("#enviarWaMediaForm input[type='file']").val(""); // Reset al elemento file del formulario
        $("#spinnerModal").modal("hide");
    })
    .fail(function() {
        toastmsg("Error de red, revisa tu conexión e intenta nuévamente.", "danger");
        $("#spinnerModal").modal("hide");
    });
});

$(document).on("click", ".waterminabtn", function(){
    wacons.terminawases();
});

$(document).on("click", ".waencytermbtn", function(){
    wacons.terminawases(true);
});

$(document).on("click", ".watransferir", function(){
    let cid = wactas[idWaCtaActiva].idContactoActivo
    let sid = $("#wasaaa"+idWaCtaActiva+" .wasid").val();
    let nag = $("#transferlist"+idWaCtaActiva).val();
    if(nag < 1 ) { // Nuevo Agente
        toastmsg("Debes seleccionar a quien transferir.", "danger");
    } else if (sid == 0 || sid == "" || sid == null) {
        toastmsg("No es una sesión activa", "danger");
        wacons.limpiarwa();
    } else {
        wacons.detenerwa();
        $.post(site_url+"whatsapp/transferir", {sid:sid, nag:nag, cid:cid}, function(data){
            if(data.error) {
                toastmsg(data.error, "danger");
            } else {
                toastmsg("Chat transferido.", "success");
                wacons.limpiarwa();
            }
            wacons.reanudarwa();
        },'json')
        .fail(function() {
            toastmsg("Error de red, revisa tu conexión e intenta nuévamente.", "danger");
            wacons.reanudarwa();
        });
    }
});

$(document).on("click", ".waactivate", function(e){
    e.preventDefault();
    if (!walinksenabled) {
        toastmsg("Espera a que termine de cargar por favor.", "danger");
        return false;
    }
    wacons.activarCto($(this).data("id"), $(this).data("wac"), $(this).text()+' ('+ $(this).data("wac") +')');
    $("#wabuscres"+idWaCtaActiva).html('');
});
$(document).on('keypress', ".wabusc", function(e) {
    if(e.which == 13) {
        e.preventDefault();
        wacons.buscarcontacto();
    }
});
$(document).on("click", ".wabuscbtn", function(){
    wacons.buscarcontacto();
});
$(document).on("click", ".wa-cargar-mas", function(e){
    e.preventDefault();
    if (wacons.waminid>1) {
        wacons.traer_data(wactas[idWaCtaActiva].idContactoActivo, wacons.waminid);
    } else {
        toastmsg("Sin mensajes para mostrar");
        $("#wa-cargar-mas"+idWaCtaActiva).addClass("d-none");
    }
});
$(document).on("click", "#wa-pills-tab .nav-link", function() {
    wacons.limpiarwa();
    idWaCtaActiva = $(this).data("id");
    $("#enviarWaMediaForm .wawid").val(idWaCtaActiva);
    wacons.reanudarwa();
});
$(document).on("click", ".verenmodal", function(e) {
    e.preventDefault();
    var tipo = $(this).data("tipo");
    var liga = $(this).prop("href");
    var tit = $(this).prop("title");
    var html = "<img src='" + liga + "' style='max-width:100%;' />";
    if (tipo == "audio") {
        html = new Audio(liga);
        html.controls = true;
        html.autoplay = true;
    }
    $("#mediaModal .modal-title").html(tit);
    $("#mediaModal .modal-body").html(html);
    $("#mediaModal").modal("show");
});

// emojiskinon
$(document).on("click", ".emojikinon-con", function(){
    $("#emojiskinon").css({"display":"none"});
});
$(document).on("click", ".emojikinon-btn", function(){
    if ($(this).prop("disabled")) {
        return false;
    } else {
        $("#emojiskinon").toggle();
    }
});
$(document).on("click", "#emojiskinon span", function(){
    target = $("#whatsapptab .tab-pane.show .emojikinon-con");
    cont = target.val() + $(this).text();
    target.val(cont).focus();
});
// Fin EmojisKinon

$(document).ready(function(){
    idWaCtaActiva = parseInt($("#enviarWaMediaForm .wawid").val());
    if (agente.permisoSec.includes('whatsapp')) {
        if (!preguntarwasig) {
            preguntarwasig = setInterval(function(){ wacons.traerAsig() }, 5000);
        }
    }
});

(function($) {
$.fn.serializewafile = function() {
    var obj = $(this);
    /* ADD FILE TO PARAM AJAX */
    var formData = new FormData();
    $.each($(obj).find("input[type='file']"), function(i, tag) {
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
