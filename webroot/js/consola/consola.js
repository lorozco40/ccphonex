var centesimas = 0;
var segundos = 0;
var minutos = 0;
var horas = 0;
var checkconfbridge;
var controla;
var traecolas; // variable para intervalo de traer colas
var autofinacw = null; // auto terminar descanso ACW en agente.acw o lads[lad][acw] segundos
// Searchable result
var pag = 0;
var reg = 0;
var rpp = 20;

var pnx = {
    activas: 0,
    lads:[], // Llamadas data
    lad:null, // Llamada actual lads id
    required: 0,
    setUniqueid: function(uniqueid) {
        $("#uniqueid, #cuniqueid, input[name=uniqueid], .uniqueid").text(uniqueid);
        $("#uniqueid, #cuniqueid, input[name=uniqueid], .uniqueid").val(uniqueid);
    },
    traeForm: function(cid, fid, id) {
        if (agente.permisoSec.includes('qualif')) {
            if ($("#leform").html()=="") {
                $("#leform").html("<i class='fas fa-spinner fa-2x fa-pulse'><span class='sr-only'>Cargando ...</span></i>");
                $.post(site_url+"consola/formulario", {cid:cid, fid: fid, id:id}, function(data){
                    if (data.status == 'error') {
                        toastmsg(data.msg, "danger");
                        $("#leform .fa-spinner").remove();
                        $("#campanasal input[name=ticketid]").focus();
                    } else {
                        $("#tmpformdel").show();
                        $("#leform").html(data.form);
                        crm_tbr.init(fid, id);
                        crm_tbr.form_fields = data.form_fields_tbr;
                        if ($("#cuniqueid").text() != "") {
                            pnx.setUniqueid($("#cuniqueid").text());
                        } else if (data.registro.uniqueid) {
                            pnx.setUniqueid(data.registro.uniqueid);
                        } else {
                            pnx.requiereUniqueid("");
                        }
                        ftr.getAll();
                        pnx.completeDep();
                        switch_view_forms('formulario');
                    }
                },"json")
                .fail(function(){
                    toastmsg("Error de comunicación.", "danger");
                    $("#leform .fa-spinner").remove();
                });
            } else {
                toastmsg("Ya hay un formulario visible.", "warning", "Pendiente");
            }
        } else {
            toastmsg("No tienes permiso para usar esta función.", "danger");
        }
    },
    completeDep: () => {
        $("select[data-depend='1']").each(function(){
            let adcampo = $(this).data("descencampo");
            let elem1 = $(this);
            let elem2 = $("select[data-depend='2'][data-descencampo='"+adcampo+"']");
            let elem3 = $("select[data-depend='3'][data-descencampo='"+adcampo+"']");
            let elem4 = $("select[data-depend='4'][data-descencampo='"+adcampo+"']");
            if (elem2 && elem2.data('presel')!='') {
                let elem1val = elem1.data('presel');
                elem2.html('<option val="">-- Elige --</option>').attr("disabled", true);
                $.post(site_url+'ajax/des_dep', {campo: adcampo, col:2, esteval: elem1val}, function(data){
                    elem2.html(data).attr("disabled", false);
                    elem2.val(elem2.data('presel'));
                },'json');
            }
            if (elem3 && elem3.data('presel')!='') {
                elem3.html('<option val="">-- Elige --</option>').attr("disabled", true);
                $.post(site_url+'ajax/des_dep', {campo: adcampo, col:3, esteval: elem2.data('presel')}, function(data){
                    elem3.html(data).attr("disabled", false);
                    elem3.val(elem3.data('presel'));
                },'json');
            }
            if (elem4 && elem4.data('presel')!='') {
                elem4.html('<option val="">-- Elige --</option>').attr("disabled", true);
                $.post(site_url+'ajax/des_dep', {campo: adcampo, col:4, esteval: elem3.data('presel')}, function(data){
                    elem4.html(data).attr("disabled", false);
                    elem4.val(elem4.data('presel'));
                },'json');
            }
        });
    },
    suenaLlamada: function(cid) {
        if (pnx.activas==0) {
            $.post(site_url+"consola/datosLlamada", {cid:cid}, function(data){
                notifyMe({msg:'Llamada entrante de '+pnx.formatPhone(data.number), tit:"Llamada"});
                pnx.lads.push(data);
                pnx.lad = pnx.lads.length - 1;
                $("#cnombre").text(cid);
                $("#cnumero").text(pnx.formatPhone(data.number));
                $("#ccampana").text(data.campaign.name);
                if (data.uniqueid) {
                    pnx.setUniqueid(data.uniqueid);
                } else {
                    pnx.requiereUniqueid(cid);
                }
                $("#cscript").text(data.script);
                if (agente.permisoSec.includes('qualif') && data.form) {
                    $("#tmpformdel").show();
                    $("#leform").html(data.form);
                    switch_view_forms('formulario');
                    if (undefined !== data.histo) {
                        $("#disp_histo").html(data.histo);
                    }
                }
                if (data.agenda.length > 0) {
                    $("#agendainfo").html(data.agenda);
                }
            },"json");
        }
    },
    entraLlamada: function() {
        pnx.activas+=1;
        activity_set("En llamada");
    },
    saleLlamada: function(cid) {
        activity_set("En llamada, llamando a " + cid);
        if (pnx.activas==0) {
            $.post(site_url+"consola/datosLlamadaSal", {formIdCam: $("#formIdCam").val(), cid: cid}, function(data){
                pnx.lads.push(data);
                pnx.lad = pnx.lads.length - 1;
                $("#cnombre").text(data.name);
                $("#cnumero").text(pnx.formatPhone(cid));
                $("#ccampana").text(data.campaign.name);
                if (data.uniqueid) {
                    pnx.setUniqueid(data.uniqueid);
                } else {
                    pnx.requiereUniqueid(cid);
                }
                $("#cscript").text(data.script);
                if(agente.permisoSec.includes('qualif') && data.form) {
                    $("#tmpformdel").show();
                    $("#leform").html(data.form);
                    switch_view_forms('formulario');
                }
            },"json");
        }
        pnx.activas+=1;
    },
    requiereUniqueid: function(cid) {
        if (typeof tap === 'undefined') {
            $.post(site_url+"ajax/traecuid", {cid: cid}, function(data){
                if (data.uniqueid) {
                    pnx.setUniqueid(data.uniqueid);
                } else {
                    if (pnx.required<3) {
                        pnx.required += 1;
                        setTimeout(function(){
                            pnx.requiereUniqueid(cid);
                        }, 3000);
                    } else {
                        pnx.required = 0;
                    }
                }
            });
        }
    },
    terminaLlamada: function(fail = false) {
        if (pnx.activas>0) {
            pnx.activas-=1;
        }
        if (pnx.activas==0) {
            $("#cnombre").text("");
            $("#cnumero").text("");
            $("#ccampana").text("");
            $("#cscript").text("");
            $("#agendainfo").text("");
            activity_unset("noreactiva");
            let tiempo = null;
            if (fail == false) {
                if ('undefined' !== agente.acw && parseInt(agente.acw) > 0) {
                    tiempo = agente.acw;
                } else if ('undefined' !== pnx.lads[pnx.lad] && parseInt(pnx.lads[pnx.lad].campaign.acw) > 0) {
                    tiempo = pnx.lads[pnx.lad].campaign.acw;
                }
            }
            if (tiempo != null) {
                activity_set("Descanso, acw", tiempo);
            } else {
                sipSiMolestar();
                agente.estado = "disponible";
            }
            pnx.lads.splice(pnx.lad, 1);
        }
    },
    answer: function(sessid) {
        if (agente.permisoSec.includes('autoanswer')) {
            setTimeout(function(){
                ctxSip.phoneCallButtonPressed(sessid);
            }, 500);
        }
    },
    formatPhone : function(phone, lim = false) {
        var num;
        if (phone.indexOf('@')) {
            num =  phone.split('@')[0];
        } else {
            num = phone;
        }
        num = num.toString().replace(/[^0-9]/g, '');
        if (lim) {
            num = num.substring(0,lim);
        }
        if (num.length === 10) {
            return '(' + num.substr(0, 3) + ') ' + num.substr(3, 3) + '-' + num.substr(6,4);
        } else if (num.length === 11) {
            return '(' + num.substr(0, 3) + ') ' + num.substr(3, 4) + '-' + num.substr(7,4);
        } else {
            return num;
        }
    },
    confBridge : function(room) {
        if (!checkconfbridge) {
            checkconfbridge = setInterval(function() { docheckconfbridge(room) }, 2000);
        }
    },
    logerror : function(tipo) {
        $.post(site_url+"ajax/logerror", {error: tipo, id_user: agente.id, extension: agente.exten});
    }
}

function docheckconfbridge(room) {
    $.post(site_url+"consola/confbridge", {room: room}, function(data){
        $("#confadmin").html(data).css("display", "block");
    },'json');
}

$(document).ready(function(){
    $.post(site_url+"consola/user_activity", { activity: "Entra consola", status: '1' });
    visibility_search_forms();
    telefono = (typeof telefono !== 'undefined') ? telefono : '';
    if (telefono != '') { $("#numDisplay").val(pnx.formatPhone(telefono, 10)); }
    $(document).on("change", "#formIdCam", function(){
        $.post(site_url+"form/getbycam", {cid: $(this).val()}, function(data){
            if (typeof data.error === 'undefined') {
                var html = "";
                data.forEach(function(fila) {
                    html += "<option value='"+fila.id+"'>"+fila.name+"</option>";
                });
                $("#formIdForm").html(html);
                visibility_search_forms();
            } else {
                toastmsg(data.error, "danger");
            }
        })
    });
    $(document).on("change", "#formIdForm", function(){
        visibility_search_forms();
    });
    $(document).on("click", "#closeconfadmin", function() {
        clearInterval(checkconfbridge);
        checkconfbridge = null;
        $("#confadmin").html("").css("display", "none");
        $("#iniConf").attr("disabled", false);
    });
    $(document).on("click", ".cbhangbtn", function(){
        var room = $(this).data("room");
        var chan = $(this).data("chan");
        $.post(site_url+"consola/confbridgehang", {room: room, chan: chan});
        $(this).off("click");
    });
    $(document).on("click", "#tmpform", function(ev){
        ev.preventDefault();
        show_form_helpdesk($("#campanasal input[name=ticketid]").val())
    });
    $(document).on('keypress', '#campanasal input[name=ticketid]', function(e){
        var keycode = (e.keyCode ? e.keyCode : (e.which ? e.which : e.key));
        $("#search_form_text").val('');
        switch_view_forms('busqueda');
        if(keycode == 13) {
            show_form_helpdesk($("#campanasal input[name=ticketid]").val())
        }
    });
    $(document).on("click", "#tmpformdel", function(ev){
        ev.preventDefault();
        switch_view_forms('busqueda');
    });
    // Paginación:
    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();
        pag = $(this).data('pag');
        search_form();
    });
    $(document).on("change", "#elirpp", function () {
        pag = 0;
        rpp = $(this).val();
        search_form();
    });
    $(document).on("click", "#setDescanso", function() {
        activity_set("Descanso, "+$('#breakList option:selected').val());
    });
    $(document).on("click", "#estadoagente .fa-angle-double-right", function(){
        $("#estadoagente").css({"width":"30px","top":"50px","right":"0","left":"unset"});
        $("#estadoagente .col-11").css("display", "none");
        $("#estadoagente .fa-angle-double-right").css("display", "none");
        $("#estadoagente .fa-angle-double-left").css("display", "inline-flex");
    });
    $(document).on("click", "#estadoagente .fa-angle-double-left", function(){
        $("#estadoagente").css({"width":"350px"});
        $("#estadoagente .col-11").css("display", "block");
        $("#estadoagente .fa-angle-double-right").css("display", "inline-flex");
        $("#estadoagente .fa-angle-double-left").css("display", "none");
    });
    $(document).on("click", "#unsetDescanso", function() {
        activity_unset();
        $("#leform").html("");
    });
    $(document).on("submit", ".call_form", function(e){
        e.preventDefault();
        $.post(site_url+'ajax/save_form', $(this).serialize(), function(data){
            if (data.status=='ok') {
                $("#leform").find('button').attr('disabled', 'true');
                $("#idform").val('');
                toastmsg("Registro guardado con éxito.", "success");
                $("#leform").html("");
                switch_view_forms('busqueda')
            } else {
                toastmsg("Falló al guardar el registro, verifica tu información.", "danger");
            }
        });
    });
    $(window).on('beforeunload', function(){
        $.post(site_url+"consola/user_activity", { activity: "Sale consola", status: '0' });
    });
    $("#estadoagente, #confadmin").draggable({ containment: "html", scroll: false });
    if (agente.queues.length > 0) {
        $("#encola").draggable({ containment: "html", scroll: false });
        traecolas = setInterval(function(){
            $.post("consola/getcolas", function(data){
                if (!data) {
                    clearInterval(traecolas);
                    $("#encola").hide();
                } else {
                    $("#encola").show();
                    agente.queues.forEach(function(row) {
                        if (data[row] !== undefined) {
                            $("#c"+row).text(data[row].wait);
                        } else {
                            $("#c"+row).text(0);
                        }
                    });
                }
            },"json");
        },3000);
    } else {
        $("#encola").hide();
    }
});

function search_form_eval(keycode) {
    $("#campanasal input[name=ticketid]").val('');
    if(keycode == 13) {
        search_form();
        switch_view_forms('busqueda');
    }
}

// Validamos si el formulario tiene habilitada la opcion de busquedas
function visibility_search_forms() {
    $(".col-searchform").hide();
    let fid = $("#formIdForm").val();
    $.post(site_url+"consola/vsearchforms", {fid: fid}, function(data){
        if( data == true ) {
            $(".col-searchform").show();
        }
    })
}


// Busca un formulario apartir de columnas searchable
function search_form() {
    if (typeof tap === 'undefined') {
        $("#campanasal input[name=ticketid]").val('');
        switch_view_forms('busqueda');
        let cid = $("#formIdCam").val();
        let fid = $("#formIdForm").val();
        let bus  = $("#search_form_text").val();
        //Validamos que haya parametros de busqueda
        if(typeof bus !== 'undefined' && bus.length == 0 ) {
            toastmsg("Debes escribir al menos un carácter de búsqueda.", "danger");
            return false;
        }
        if ($("#leform").html()=="") {
            $("#leform").html("<i class='fas fa-spinner fa-2x fa-pulse'><span class='sr-only'>Cargando ...</span></i>");
            $.post(site_url+"consola/formsearch", {cid:cid, fid:fid, pag:pag, reg:reg, rpp:rpp, bus:bus}, function(data){
                $("#leform .fa-spinner").remove();
                if (data.success === false) {
                    toastmsg(data.msg, "danger");
                } else {
                    let html = '';
                    // THEAD
                    html += `<div class="table">
                    <div class="table-header-group">
                        <div class="table-row">`;
                        data.head.map( item => {
                            html += `<div class="table-cell">${ item }</div>`;
                        })
                        if( data.head.length > 0 ) {
                            html += `<div class="table-cell">Acción</div>`;
                        }
                    html += `
                        </div>
                    </div>
                    `;
                    // TBODY
                    data.rows.map(row => {
                        html += `<div class="table-row">`;
                        data.head.map( item => {
                            html += `<div class="table-cell">${ row[item] }</div>`;
                        });
                        if( data.rows.length > 0 ) {
                            html += `
                                <div class="table-cell">
                                    <button type="button" class="btn btn-info" onclick="show_form_helpdesk(${row.ID})">
                                        ver
                                    </button>
                                </div>`;
                        }
                        html += `</div>`
                    });
                    html += `</div>`
                    $("#list-forms").html(html);
                    pag = data.pag;
                    tot = data.tot;
                    rpp = data.rpp;
                    paginacion(data.pag, data.tot, data.rpp, data.rows.length);
                }
            },"json")
            .fail(function(){
                toastmsg("Error de comunicación.", "danger");
                $("#leform .fa-spinner").remove();
            });
        } else {
            toastmsg("Ya hay un formulario visible.", "warning", "Pendiente");
        }
    }
}

function show_form_helpdesk(tid) {
    let cid     = $("#formIdCam").val();
    let fid     = $("#formIdForm").val();
    pnx.traeForm(cid, fid, tid);
}

function activity_set(tipo, extra = null) {
    if (tipo == "Videollamada" || tipo.startsWith("Descanso")) {
        if (tipo != "Descanso, Llamada de salida") {
            $(".bloqui").css("display","block");
        }
        sipNoMolestar();
        agente.estado = "ocupado";
    }
    $.post(site_url+"consola/user_activity", { activity: tipo, status: '0' });
    $("#descBreak").val(tipo);
    $("#terminaBreak").css("display", "flex");
    $("#iniciaBreak").css("display", "none");
    if (!tipo.startsWith("Descanso")) {
        $("#unsetDescanso").css("display", "none");
    }
    $("#estadoagente").removeClass("alert-info").addClass("alert-danger");
    counter("inicia");
    if (tipo == "Descanso, acw" && extra != null) {
        let tiempo = (extra == 1) ? 1800000 : parseInt(extra)*1000;
        clearTimeout(autofinacw);
        autofinacw = null;
        autofinacw = setTimeout(function() {
            activity_unset();
        }, tiempo );
    } else if (tipo == "Descanso, acw") {
        activity_unset();
    }
}

function activity_unset(tipo = "Disponible") {
    clearTimeout(autofinacw);
    autofinacw = null;
    if (tipo != "noreactiva") {
        sipSiMolestar();
        agente.estado = "disponible";
        $(".bloqui").css("display","none");
    } else {
        tipo = "Disponible";
    }
    $.post(site_url+"consola/user_activity", { activity: tipo, status: '1' });
    $("#descBreak").val("En espera");
    $("#unsetDescanso").css("display", "flex");
    $("#terminaBreak").css("display", "none");
    $("#iniciaBreak").css("display", "flex");
    $("#estadoagente").removeClass("alert-danger").addClass("alert-info");
    counter("termina");
}

// f que activa y muestra el relog en los auxiliares, usa la otra f cronómetro
// Los auxiliares son opcionales, así que se valida que existan y si no, no hace nada
function counter(que) {
    if (typeof Centesimas != 'undefined') {
        if (que=="inicia") {
            if (!controla) {
                controla = setInterval(cronometro,10);
            }
        } else {
            clearInterval(controla);
            controla = null;
            centesimas = 0;
            segundos = 0;
            minutos = 0;
            horas = 0;
            Centesimas.innerHTML = ":00";
            Segundos.innerHTML = ":00";
            Minutos.innerHTML = ":00";
            Horas.innerHTML = "00";
        }
    }
}

function cronometro() {
    if (centesimas < 99) {
        centesimas++;
        if (centesimas < 10) { centesimas = "0"+centesimas }
        Centesimas.innerHTML = ":"+centesimas;
    }
    if (centesimas == 99) {
        centesimas = -1;
    }
    if (centesimas == 0) {
        segundos ++;
        if (segundos < 10) { segundos = "0"+segundos }
        Segundos.innerHTML = ":"+segundos;
    }
    if (segundos == 59) {
        segundos = -1;
    }
    if ( (centesimas == 0)&&(segundos == 0) ) {
        minutos++;
        if (minutos < 10) { minutos = "0"+minutos }
        Minutos.innerHTML = ":"+minutos;
    }
    if (minutos == 59) {
        minutos = -1;
    }
    if ( (centesimas == 0)&&(segundos == 0)&&(minutos == 0) ) {
        horas ++;
        if (horas < 10) { horas = "0"+horas }
        Horas.innerHTML = horas;
    }
}

function sipNoMolestar() {
    try {
        ctxSip.sipCall('*78');
    } catch(e) { console.log(e); }
}

function sipSiMolestar() {
    try {
        ctxSip.sipCall('*79');
        switch_view_forms('busqueda');
    } catch(e) { console.log(e); }
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
    $.each(params, function (i, val) {
        formData.append(val.name, val.value);
    });
    return formData;
};
})(jQuery);

// ajax template
// $.post(site_url+'controlador/funcion', {
//     parametro : enviar
// }, function(data) {
//     hacer algo con data.recibido
// }, 'json')
// .fail(function() {
//     location.reload();
// });
