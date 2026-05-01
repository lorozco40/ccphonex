$(document).ready(function() {
    if (uid != 0) {
        setInterval(ping, 15000);
    }
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle=confirmation]').confirmation({rootSelector: '[data-toggle=confirmation]'});1
    $(document).on('mousedown', 'select[readonly]', function(e) {
        e.preventDefault();
        this.blur();
    });
});

function toastmsg(msg, tipo = 'info', tit = 'Assertive') {
    $(".disp-toast.hide").remove();
    let nuevtoast = $('#toastplantilla').clone();
    nuevtoast.attr('id', '');
    nuevtoast.addClass('disp-toast');
    nuevtoast.find('.mr-auto').text(tit);
    nuevtoast.find('.toast-body').html(msg);
    let tiposdisp = ['danger', 'success', 'info', 'warning', 'primary', 'secondary', 'dark', 'light'];
    if (!tiposdisp.includes(tipo)) {
        tipo = 'info';
    }
    nuevtoast.find('.tipotoast').addClass('text-'+tipo).addClass('bg-'+tipo);
    nuevtoast.find('.text-muted').text(moment().format('HH:mm:ss'));
    $('#toastzone').append(nuevtoast);
    nuevtoast.toast('show');
}

$('#licenciaModal').on('show.bs.modal', function (e) {
    $.post(site_url+"ajax/licinfo", function(data){
        if (!data) {
            toastmsg("Error comunicando con el servidor.", "danger");
        }
        $("#lica").text(data.ensis);
        $("#licd").text(data.dispo);
    },"json")
    .fail(function() {
        if (typeof res.responseJSON.error !== "undefined") {
            toastmsg(res.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión.", "danger");
        }
    });
});

$(document).on("click", ".form-select option", function(e){
    //Se agrega esta condicion para validar si la opcion esta seleccionada, en caso de que sea una des-seleccion no se hara nada.
    //Ya que cada ves que se le daba click a un option se agregaba selected aun que fue una deseleccion.
    //Este cambio esta en observacion.
    if( $(this).prop("selected") ){
        if ($(this).prop("selected",true)) {
            let padre = $(this).parents(".form-select");
            let valor = $(this).val();
            if (valor == "" || valor == null || valor == 0 || valor == '0' || valor == false) {
                padre.val("");
            } else {
                padre.children("option").first().prop("selected", false);
            }
        }
    }
});

$(document).on("click", ".sele", function() {
    $(".sele").removeClass("disabled");
    $(this).addClass("disabled");
});

$(document).on("click", ".cerrarpastilla", function(){
    $(this).closest(".pastilla").remove();
});

$(document).on("click", ".closeparentdiv", function(){
    $(this).closest("div").remove();
});

$(document).on("click", ".verotro", function(e){
    e.preventDefault();
    that = $(this);
    that.addClass("d-none");
    $(that.data("otro")).attr("type", "text").focus();
    setTimeout(function(){
        that.removeClass("d-none");
        $(that.data("otro")).attr("type", "hidden");
    }, 5000);
});

$(document).on('submit', '#misdatosform', function(e){
    e.preventDefault();
    $('#spinnerModal').modal('show');
    let data = $('#misdatosform').serialize();
    $.ajax({
        url: site_url + 'usuarios/misdatos',
        type: 'POST',
        data: data,
        success: function(data){
            $('#spinnerModal').modal('show');
            if(data == 'ok'){
                $('#misDatosModal').modal('hide');
                if ($('#misdatosform input[name="pass"]').val() != '') {
                    toastmsg('Datos actualizados, por favor inicia sesión nuevamente!', 'success');
                    setTimeout(function(){
                        location.href = site_url + 'acceso/logout';
                    }, 3000);
                } else {
                    toastmsg('Datos actualizados, recarga la página o espera un momento por favor!', 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 3000);
                }
            } else {
                toastmsg(data, 'danger');
            }
        },
        fail: function(){
            $('#spinnerModal').modal('hide');
            toastmsg('Error de red, verifica tu conexión.', 'danger');
        }
    });
});

$(document).on("submit", ".delform", function(e){
    if(!confirm("Seguro deseas borrar el registro?")){
        e.preventDefault();
    }
});

$(document).on("change", ".ajaxdep", function(){
    var adcampo = $(this).data("descencampo");
    var adcol = $(this).data("descencol");
    var adesteval = $(this).val();
    var adelhijo = $(this).data("descen");
    $('#'+adelhijo).html('<option val="">-- Elige --</option>').attr("disabled", true);
    $.post(site_url+'ajax/des_dep', {campo: adcampo, col:adcol, esteval: adesteval}, function(data){
        $('#'+adelhijo).html(data).attr("disabled", false);
    },'json');
});

$(document).on("change", ".showform", function(){
    var adshowform = $(this).data("showform");
    if (adshowform == $(this).val()) {
        $(".sfdes").css("display", "flex");
        $(".sfdes input, .sfdes select").prop("required", true);
    }
});

$(document).on("keydown", "body", function(e){
    var keycode = (e.keyCode ? e.keyCode : (e.which ? e.which : e.key));
    if(keycode == 27) {
        $("#chatinterno_body, #emojiskinon").css({"display":"none"});
        $(".modal").not("#spinnerModal").modal("hide");
        $(".toast").toast("hide");
        $(".alert").alert('close');
    }
});

$(document).on('click', '.dropdown-menu a.dropdown-toggle', function(e) {
    if (!$(this).next().hasClass('show')) {
        $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
    }
    var $subMenu = $(this).next(".dropdown-menu");
    $subMenu.toggleClass('show');

    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
        $('.dropdown-submenu .show').removeClass("show");
    });
    return false;
});

$(document).on("click", ".gentoken", function(e){
    e.preventDefault();
    var target = $(this).data('target');
    var uid = $(this).data('uid');
    data = '';
    if (uid) {
        var data = {'uid':uid};
    }
    $.post(site_url+'usuarios/gentoken', data, function(res){
        if (res.error) {
            toastmsg(res.error, 'danger');
        } else {
            $("input[name="+target+"]").val(res);
        }
    });
});

$(document).on("click", ".llamarcliente", function(){
    let numero = $(this).data("numero").toString();
    $("#numDisplay").val(pnx.formatPhone(numero, 10));
    $(window).scrollTop(0);
    setTimeout(function () {
        ctxSip.phoneCallButtonPressed();
    }, 1000);
});
$(document).on("click", ".smsacliente", function(){
  let numero = $(this).data("numero").toString();
  numero = numero.replace(/\D/g,'').slice(0,10);
  $("a[href=#sms]").click();
  $("#smsform input[name=input_num]").val(numero);
  $("#smsform textarea[name=input_msg]").focus();
});
$(document).on("click", ".emailacliente", function(){
    email = $(this).data("email").replace(/\s/g,'');
    $("a[href=#emailconsolax]").click();
    $("#emnuevo").click();
    setTimeout(function () {
        $("#emForm textarea[name=to]").val(email);
        $("#emForm input[name=subject]").focus();
    }, 500);
});

$(document).on("keydown", "input.page-input", function(e){
    // las variables pag y rpp son forzosas (lo aarelaré depués para soportar múltiples paginadores en una sola url)
    // Establecer la función getpag (data-fun cuando sea diferente), cuando se quiera utilizar la paginación personalizada
    // El input solo dirigirá a la página requerida con la tecla <ENTER>
    var keycode = (e.keyCode ? e.keyCode : (e.which ? e.which : e.key));
    if(keycode == 13) {
        fun = ($(this).data('fun')) ? $(this).data('fun') : 'getpag';
        e.preventDefault();
        prepag = ($(this).val()!='') ? parseInt($(this).val()) : 1;
        rpp = (typeof $("#elirpp").val() !== 'undefined') ? parseInt($("#elirpp").val()) : 20;
        quiero = prepag * rpp - rpp;
        eval(fun+"("+quiero+")");
    }
});

function paginacion(pag = 0, reg = 0, rpp = 20, cta = 0, des = 'paginacion', fun = 'getpag') {
    // pag: el número de registro con el que empieza la página actual, ejemplo: 20 (página 2 con 20 registros por página default)
    // reg: el número total de registros
    // rpp: Registros por página
    // cta: el número de registros de ésta página
    // des: el id destino de la paginación, default es paginacion
    pag = parseInt(pag);
    reg = parseInt(reg);
    rpp = parseInt(rpp);
    cta = parseInt(cta);
    if (reg == 0 || cta == 0) {
        html = "No hay registros para mostrar";
    } else {
        html = '<p>Mostrando registros <strong style="color: #0fa7ff;">' +
            (pag+1) + '</strong> a <strong style="color: #0fa7ff;">' + (pag+cta) +
            '</strong> de <strong style="color: #0fa7ff;">' + reg + '</strong></p>';
        if(reg > rpp) {
            totp = Math.ceil(reg/rpp);
            act  = (pag/rpp)+1;
            html += '<nav aria-label="Paginación"><ul class="pagination">';
            if (pag > 0) {
                ant = pag-rpp;
                html += '<li class="page-item">' +
                '<a class="page-link" href="#" aria-label="Previous" data-pag="'+ant+'">' +
                '<span aria-hidden="true">&laquo;</span>' +
                '<span class="sr-only">Previous</span>' +
                '</a>' +
                '</li>' +
                '<li class="page-item"><a class="page-link" href="#" data-pag="0">1</a></li>' +
                '<li class="page-item"><span class="page-input"> ... </span></li>';
            } else {
                html += '<li class="page-item disabled">' +
                '<a class="page-link" href="#" aria-label="Previous" tabindex="-1">' +
                '<span aria-hidden="true">&laquo;</span>' +
                '<span class="sr-only">Previous</span>' +
                '</a>' +
                '</li>';
            }
            html += '<li class="page-item"><input class="page-input" id="emctaspl" data-fun="'+fun+'" value="'+act+'"></li>';
            if (totp > act) {
                html += '<li class="page-item"><span class="page-input"> ... </span></li>' +
                '<li class="page-item"><a class="page-link" href="#" data-pag="'+((totp-1)*rpp)+'">'+totp+'</a></li>' +
                '<li class="page-item">' +
                '<a class="page-link" href="#" aria-label="Next" data-pag="'+(pag+rpp)+'">' +
                '<span aria-hidden="true">&raquo;</span>' +
                '<span class="sr-only">Next</span>' +
                '</a>' +
                '</li>';
            } else {
                html += '<li class="page-item disabled">' +
                '<a class="page-link" href="#" aria-label="Next" tabindex="-1">' +
                '<span aria-hidden="true">&raquo;</span>' +
                '<span class="sr-only">Next</span>' +
                '</a>' +
                '</li>';
            }
            html += '</ul></nav>';
        }
    }
    $("#"+des).html(html);
}

(function($) {
$.fn.serializefiles = function() {
    var obj = $(this);
    /* ADD FILE TO PARAM AJAX */
    var formData = new FormData();
    $.each($(obj).find("input[type='file']"), function(i, tag) {
        $.each($(tag)[0].files, function(i, file) {
            formData.append(tag.name, file);
        });
    });
    var params = $(obj).serializeArray();
    // params.find(item => item.name === 'body').value = CKEDITOR.instances.input_email_body.getData();
    $.each(params, function (i, val) {
        formData.append(val.name, val.value);
    });
    return formData;
};
})(jQuery);

function ping() {
    $.post(site_url+"debug/ping", function(data){
        if (data.msg) {
            toastmsg(data.msg, 'danger');
        } else if (data===0) {
            location.reload();
        }
    },"json")
    .fail(function(){
        location.reload();
    });
}

function ValidateEmail(inputText) {
    if(inputText.match(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/)) {
        return true;
    } else {
        toastmsg("Ese no es un email válido!", "danger");
        return false;
    }
}

function notifyMe(data = {msg:"Bienvenido a Assertive", tit:"Assertive Business!"})  {
    tit = (undefined !== data.tit) ? data.tit : "Assertive Business!";
    if(!("Notification" in window)) {
        toastmsg("Este navegador no soporta notificaciones de escritorio", "danger");
    } else if (Notification.permission === "granted") {
        var options = {
            body: data.msg,
            icon: site_url + "assets/img/logo.png",
            dir : "ltr",
        };
        var notification = new Notification(tit, options);
    } else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function (permission) {
            if  (!('permission' in Notification))  {
                Notification.permission = permission;
            }
            if  (permission === "granted")  {
                var  options = {
                    body: data.msg,
		            icon: site_url+"assets/img/logo.png",
		            dir : "ltr",
                };
                var notification = new Notification(tit, options);
                notification.onclick = function() {
                    window.focus();
                    this.close();
                }
            }
        });
    }
}

function upd_user_status(sec, val) {
    $.post(site_url+"ajax/upduserstatus", {sec:sec,val:val}, function(res){
        if (res.msg) {
            toastmsg(res.msg, res.tipo); // bootstrap classes: danger, success, default info
        }
    },"json")
    .fail(function(){
        toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        // location.reload();
    });
}

function validarNumerico(event) {
    // Obtén el valor actual del campo de entrada
    /*
     * agregar asi inputElement.addEventListener('keyup', validarNumerico)
     * o
     * <input onkeyup="validarNumerico(event)">
     * */
    const valor = event.target.value;

    // Utiliza una expresión regular para verificar si es un número válido
    if (/^[0-9]*$/.test(valor)) {
        // Si es numérico, no hagas nada
        return
    } else {
        // Si no es numérico, elimina los caracteres no numéricos
        event.target.value = valor.replace(/[^0-9]/g, '');
    }
}

function validarNumeroConDecimales(event) {
    /*
     * agregar asi inputElement.addEventListener('keyup', validarNumeroConDecimales)
     * o
     * <input onkeyup="validarNumeroConDecimales(event)">
     * */
    const valor = event.target.value;

    // Utiliza una expresión regular para permitir números enteros o con hasta dos decimales
    if (/^\d+(\.\d{0,2})?$/.test(valor)) {
        // Si es un número válido, no hagas nada
        return
    } else {
        // Si no es válido, elimina los caracteres no numéricos y excesivos
        event.target.value = valor.replace(/[^\d.]/g, ''); // Elimina caracteres no numéricos
        const partes = valor.split('.');

        if (partes.length > 1) {
            event.target.value = partes[0] + '.' + partes[1].slice(0, 2); // Limita a dos decimales
        }
    }
}
