var pag = 0;
var reg = 0;
var rpp = 20;
var obregs = {};
var bus = '';
var modo = "";//PARA QUE NO HAGA CHANGE DE PERTENECE AL ABRIR EL EL MODAL AL EDITAR
var perteneceVal = '';//PARA QUE NO SE PIERDA EL VALOR AL EDITAR

$(document).ready(function(){
    getpag();
    verPermiso();

    $(document).on("click", "#submenu", function(){
        verPermiso();
    });

    $(document).on("change", "#nivel", function(){
        let nivel = $(this).val();
        buscarPertenece(this);
        if ( nivel && nivel > 1 ) {
            $("#ver-pertenece").show();
        }else{
            buscarOrden("nivel",nivel);
            $("#ver-pertenece").hide();
        }
    });

    $(document).on("change", "#pertenece", function(){
        let pertenece = $(this).val();
        buscarOrden("pertenece", pertenece);
    });

    $(document).on("click", "#nuevo-menu", function(){
        modo = "nuevo";
        $("#formmenu").trigger("reset");
        // $("#nivel").change();
        verPermiso();
        $("#ver-active").hide();
        $("#formmenu input[type=hidden][name=id]").val(0);
        $("#menuModel").modal("show");
    });

    $(document).on("click", ".editar-menu", function(){
        modo = "editar";
        var id = $(this).data("id");
        Object.entries(obregs[id]).forEach(([i, val]) => {
            $("#formmenu input[type=text][name="+i+"]").val(val);
            $("#formmenu input[type=hidden][name="+i+"]").val(val);
            $("#formmenu input[type=email][name="+i+"]").val(val);
            $("#formmenu select[name="+i+"]").val(val);
            cheko = (val=='0') ? false : true;
            $("#formmenu input[type=checkbox][name="+i+"]").prop('checked',cheko);
        });

        perteneceVal = obregs[id].pertenece;
        verPermiso();

        if( obregs[id].nivel && obregs[id].nivel > 1 ) $("#nivel").change();
        else $("#ver-pertenece").hide();

        $("#ver-active").show();
        $("#menuModel").modal("show");
    });

    $(document).on("submit", "#formmenu", function(e){
        e.preventDefault();
        guardar($("#formmenu").serialize());
    });

    $(document).on('submit', "#bmenu", function(e){
        e.preventDefault();
        bus = $("#bmenu input[type=text]").val();
        pag = 0;
        getpag();
    });
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        pag = $(this).data('pag');
        getpag();
    });
    $(document).on("change", "#elirpp", function(){
        pag = 0;
        rpp = $(this).val();
        getpag();
    });
});

function getpag(cual = 0) {
    $("#spinnerModal").modal("show");
    pag = (cual != 0) ? cual : pag;
    $.get(site_url+'menu/lista', {pag: pag, lim: rpp, bus: bus,}, function(data) {
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            $("#tabla-menu .borrable").remove();
            reg = data.regs;
            var html = "";
            var cuenta = 0;
            if ( data.data && data.data.length > 0 ) {
                data.data.forEach((row,key) => {
                    obregs[row.id] = row;
                    cuenta++;

                    active    = ( row.active == 1 ) ? 'Si' : 'No';
                    icono     = ( row.icono != null ) ? row.icono : '-';
                    permiso   = ( row.permiso != null ) ? row.permiso : '';
                    submenu   = ( row.submenu == 1 ) ? 'Si' : 'No';

                    html += "<div class='table-row borrable'>" +
                                "<div class='table-cell'>"+row.etiqueta+"</div>" +
                                "<div class='table-cell text-center'>"+row.nivel+"</div>" +
                                "<div class='table-cell'>"+row.n2etiqueta+"</div>" +
                                "<div class='table-cell text-center'>"+row.orden+"</div>" +
                                "<div class='table-cell text-center'>"+submenu+"</div>" +
                                "<div class='table-cell text-center'>"+icono+"</div>" +
                                "<div class='table-cell'>"+permiso+"</div>" +
                                "<div class='table-cell text-center'>"+active+"</div>" +
                                "<div class='table-cell text-center'>" +
                                    "<button type='button' class='btn btn-dark editar-menu' data-id='"+row.id+"'>Editar</button>" +
                                "</div>" +
                            "</div>";
                });
                $("#tabla-menu").append(html);
            }
            paginacion(pag, reg, rpp, cuenta);
        }
        $("#spinnerModal").modal("hide");
    },"json")
    .fail(function(data) {
        $("#spinnerModal").modal("hide");
        if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
            toastmsg(data.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}

function verPermiso() {
    if( $("#submenu").is(":checked") ){
        $("#ver-permiso").hide();
    }else{
        $("#ver-permiso").show();
    }
}

function buscarPertenece(el) {
    let nivel = $.trim($(el).val());

    if( nivel > 1 ){
        $("#spinnerModal").modal("show");
        $.get(site_url+'menu/buscarPertenece', {nivel: nivel}, function(data) {
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {

                $("#pertenece").empty();
                var html = "";
                if ( data.data && data.data.length > 0 ) {
                    data.data.forEach((row,key) => {
                        html += "<option value='"+row.id+"'>"+row.etiqueta+"</option>"
                    });
                }
                if( modo == "nuevo" ) $("#pertenece").html(html).change();
                else {
                    $("#pertenece").html(html);
                    $("#pertenece").val(perteneceVal);
                }

            }
            $("#spinnerModal").modal("hide");
        },"json")
        .fail(function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    }else $("#pertenece").empty();
}

function buscarOrden(campo, id) {

    if( id > 0 ){
        $("#spinnerModal").modal("show");
        $.get(site_url+'menu/buscarOrden', {campo: campo, id: id}, function(data) {
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                if ( data.data && data.data?.orden > 0 ) {
                    $("#orden").val(data.data.orden);
                }
            }
            $("#spinnerModal").modal("hide");
        },"json")
        .fail(function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    } else $("#orden").val(1);
}

function guardar(data) {
    $("#spinnerModal").modal("show");
    $.ajax({
        url: site_url+'menu/guardar',
        type: 'POST',
        data: data,
        dataType: 'json',
    })
    .done(function(data){
        $("#spinnerModal").modal("hide");
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            if( modo == "nuevo" ){
                $("#nivel").val(1);
                $("#formmenu").trigger("reset");
                verPermiso();
                $("#ver-pertenece").hide();
                $("#ver-active").hide();
                $("#formmenu input[type=hidden][name=id]").val(0);
                $("#orden").val(1);
            }else{
                $("#menuModel").modal("hide");
            }
            toastmsg(data, "success");
            getpag();
        }
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


