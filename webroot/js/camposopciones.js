'use strict'

let cam = "";
let sigue = 1;
let sel = "a[data-cam="+cam+"][data-lvl=1]";
let accion = "";//DETERMINA SI EL CLIC VIENE DEL BTN NUEVO O EL BTN AGREGAR

$(document).on("click", ".adop", function(e){
    e.preventDefault();
    let lvl = $(this).data("lvl");
    let val = $(this).text();
    accion = "Agregar";
    $("#addbtn").removeClass("disabled");
    $("#delbtn").show();
    $("#actbtn").hide();
    $(".validar").removeClass("is-invalid");
    $("a[data-lvl="+lvl+"]").removeClass("active");
    $(this).addClass("active");

    if (lvl == 'cam') {
        $("#actbtn").show();
        cam = val;
        sigue = 1;
        sel = "a[data-cam="+cam+"][data-lvl=1]";
        $("input.lvls").prop("readonly", true);
        $("input.lvls").val("");
        $("input[name=lvl1]").prop("readonly", false).focus();
        $("input[name=cam]").prop("readonly", true).val(val);
        $("a[data-lvl=1],a[data-lvl=2],a[data-lvl=3],a[data-lvl=4]").removeClass("d-flex").addClass("d-none");

        $("a[data-lvl="+sigue+"]").removeClass("active");
    } else {
        sigue = parseInt(lvl) + 1;
        sel = "a[data-cam="+cam+"][data-padre='"+val+"'][data-lvl="+sigue+"]";
        $("input[name=cam]").prop("readonly", true);
        $("input[name=lvl"+sigue+"]").val("").prop("readonly", false).focus();
        $("input[name=lvl"+lvl+"]").val(val).prop("readonly", true);

        $("a[data-lvl="+sigue+"]").removeClass("active");

        if (lvl == 1) {
            $("a[data-lvl=2],a[data-lvl=3],a[data-lvl=4]").removeClass("d-flex").addClass("d-none");
            $("input[name=lvl3],input[name=lvl4]").val("").prop("readonly", true);
        } else if (lvl == 2) {
            $("a[data-lvl=3],a[data-lvl=4]").removeClass("d-flex").addClass("d-none");
            $("input[name=lvl1],input[name=lvl4]").prop("readonly", true);
        } else if (lvl == 3) {
            $("a[data-lvl=4]").removeClass("d-flex").addClass("d-none");
            $("input[name=lvl1],input[name=lvl2]").prop("readonly", true);
        } else{
            $("#addbtn").addClass("disabled");
        }
    }

    $(sel).removeClass("d-none").addClass("d-flex");
    corrigirBordes(sigue);
});

$(document).on("click", "#nuevo", () => {
    nuevo();
});
$(document).on("click", "#delbtn", function(e) {
    if( $(this).hasClass("disabled") ==  false ){
        let confirmado = confirm('Estas seguro de eliminar el elemento junto con todos sus descendientes?');
        if(confirmado) {
            $('#formCamposDependientes').submit();
        }
    }
});

const nuevo = () => {
    accion = "Nuevo";
    $("#addbtn").removeClass("disabled");
    $("#delbtn").hide();
    $("#actbtn").hide();
    $("input.lvls").val("").prop("readonly", true);
    $("input[name=lvl1]").prop("readonly", false);
    $("input[name=cam]").prop("readonly", false).val("").focus();
    $("a[data-lvl=1],a[data-lvl=2],a[data-lvl=3],a[data-lvl=4]").removeClass("d-flex").addClass("d-none");
    $("a[data-lvl=cam]").removeClass("active");
}

$(document).ready(function(){
    nuevo();
    // $("#primero").click();
});

$(document).on("click", "#addbtn", function() {
    var inputCam = $("input[name=cam]");
    var inputLvl1 = $("input[name=lvl1]");
    var inputLvl2 = $("input[name=lvl2]");
    var inputLvl3 = $("input[name=lvl3]");
    var inputLvl4 = $("input[name=lvl4]");

    var inputCamVal  = $.trim( $(inputCam).val() );
    var inputLvl1Val = $.trim( $(inputLvl1).val() );
    var inputLvl2Val = $.trim( $(inputLvl2).val() );
    var inputLvl3Val = $.trim( $(inputLvl3).val() );
    var inputLvl4Val = $.trim( $(inputLvl4).val() );

    var Camp = false;
    var Lvl1 = false;
    var Lvl2 = false;
    var Lvl3 = false;
    var Lvl4 = false;
    var mensaje = "";
    var data = "";
    var readonly, val;


    if( accion =="Nuevo" && inputCamVal.length > 0 && inputLvl1Val.length > 0 && inputLvl2Val.length == 0 && inputLvl3Val.length == 0 && inputLvl4Val.length == 0 ) {// SI EL GUARDADO VIENE DEL BOTON NUEVO
        Camp = true;
    } else if( accion == "Agregar" && inputLvl1Val.length > 0 && inputLvl2Val.length == 0 && inputLvl3Val.length == 0 && inputLvl4Val.length == 0 ) {// SI EL GUARDADO VIENE DEL BOTON AGREGAR
        Lvl1 = true;
    } else if( accion == "Agregar" && inputLvl2Val.length > 0 && inputLvl3Val.length == 0 && inputLvl4Val.length == 0 ) {// SI EL GUARDADO VIENE DEL BOTON AGREGAR
        Lvl2 = true;
    } else if( accion == "Agregar" && inputLvl3Val.length > 0 && inputLvl4Val.length == 0 ) {// SI EL GUARDADO VIENE DEL BOTON AGREGAR
        Lvl3 = true;
    } else if( accion == "Agregar" && inputLvl4Val.length > 0 ) {// SI EL GUARDADO VIENE DEL BOTON AGREGAR
        Lvl4 = true;
    }

    // BUSCAME SI UN INPUT ESTA SIN EL READONLY Y VALIDAME QUE TENGA TEXTO
    $(".validar").removeClass("is-invalid");
    $(".validar").each(function(index, value){
        readonly = $(this).prop("readonly");
        if( !readonly ){
            val = $.trim( $(this).val() );
            if( val.length == 0 ) {
                $(this).addClass("is-invalid");
                mensaje = "Escribe un valor en el campo. <br>";
            }
        }
    });

    // SI EL BOTON ESTA DESHABILITADO(POR QUE LE DIO CLIC A LA OPCION DE NIVEL 4)
    if( mensaje.length == 0 && $(this).hasClass("disabled") == false ){
        $("#spinnerModal").modal("show");
        data = $("#formCamposDependientes").serialize();
        data += "&addbtn=Agregar";
        $.post(site_url + "camposopciones/guardar", data, function (resultados) {
            $("#spinnerModal").modal("hide");
            if ( resultados.success ) {
                if( Camp ) {//SI ES UNO NUEVO(SI LE DIO CLIC AL BOTON NUEVO)
                    $("#camp").append("<a href='#' class='adop d-flex list-group-item list-group-item-action' data-lvl='cam'>"+inputCamVal+"</a>");
                    $("#lvl1").append("<a href='#' class='adop d-none list-group-item list-group-item-action' data-padre='"+inputCamVal+"' data-cam='"+inputCamVal+"' data-lvl='1'>"+inputLvl1Val+"</a>");
                    $(inputCam).val("").focus();
                    $(inputLvl1).val("");
                    corrigirBordes(1);
                } else if( Lvl1 ) {//SI ESTA AGREGANDO NIVEL 1
                    $("#lvl1").append("<a href='#' class='adop d-flex list-group-item list-group-item-action' data-padre='"+inputCamVal+"' data-cam='"+inputCamVal+"' data-lvl='1'>"+inputLvl1Val+"</a>");
                    $(inputLvl1).val("").focus();
                    corrigirBordes(1);
                } else if( Lvl2 ) {//SI ESTA AGREGANDO NIVEL 2
                    $("#lvl2").append("<a href='#' class='adop d-flex list-group-item list-group-item-action' data-padre='"+inputLvl1Val+"' data-cam='"+inputCamVal+"' data-lvl='2'>"+inputLvl2Val+"</a>");
                    $(inputLvl2).val("").focus();
                    corrigirBordes(2);
                } else if( Lvl3 ) {//SI ESTA AGREGANDO NIVEL 3
                    $("#lvl3").append("<a href='#' class='adop d-flex list-group-item list-group-item-action' data-padre='"+inputLvl2Val+"' data-cam='"+inputCamVal+"' data-lvl='3'>"+inputLvl3Val+"</a>");
                    $(inputLvl3).val("").focus();
                    corrigirBordes(3);
                } else {//SI ESTA AGREGANDO NIVEL 4
                    $("#lvl4").append("<a href='#' class='adop d-flex list-group-item list-group-item-action' data-padre='"+inputLvl3Val+"' data-cam='"+inputCamVal+"' data-lvl='4'>"+inputLvl4Val+"</a>");
                    $(inputLvl4).val("").focus();
                    corrigirBordes(4);
                }
                toastmsg(resultados.mensaje, "success");
            } else toastmsg(resultados.mensaje, "danger");
        }, "json")
        .fail(function () {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    } else {
        if( mensaje.length > 0 ) toastmsg(mensaje, "danger");
    }
});

$(document).on("keyup", ".validar", function(e) {
    // SI PRECIONA LA TECLA ENTER EN CUALQUIER INPUT
    var code = (e.keyCode ? e.keyCode : e.which);
    if (code==13) $("#addbtn").click();
});

$(document).on("change", "#selectCampanaCampos", function(e) {
    nuevo();
    let campana = $.trim( $(this).val() );
    $("#spinnerModal").modal("show");
    $.post(site_url+"camposopciones/buscar", { campanas: campana }, function(data) {
        $("#spinnerModal").modal("hide");
        $("#camp").empty().html(data.cds.cams);
        $("#lvl1").empty().html(data.cds.l1);
        $("#lvl2").empty().html(data.cds.l2);
        $("#lvl3").empty().html(data.cds.l3);
        $("#lvl4").empty().html(data.cds.l4);
    })
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

$(document).on("click", "#actbtn", function(e) {
    $("#spinnerModal").modal("show");
    let data = $("#formCamposDependientes").serialize();
    $.post(site_url+"camposopciones/actualizar", data, function(resultados) {
        console.log(resultados);
        $("#spinnerModal").modal("hide");
        if ( resultados.success ) {
            $("#selectCampanaCampos").change();
            toastmsg(resultados.mensaje, "success");
        } else toastmsg(resultados.mensaje, "danger");
    })
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

function corrigirBordes(sigue){
    // CORRIGIENDO LOS BORDES DEL PRIMER Y ULTIMO HIJO, PARA COMPROBAR, COMENTAR LAS SIGUIENTES 3 LINEAS
    $("a[data-lvl="+sigue+"]").removeClass("rounded-top rounded-bottom");//QUITAME LOS BORDES REDONDOS DE LOS HIJOS
    $("a[data-lvl="+sigue+"].d-flex:first").addClass("rounded-top");//AGREGAME LOS BORDES REDONDOS DE TOP AL PRIMER HIJO
    $("a[data-lvl="+sigue+"].d-flex:last").addClass("rounded-bottom");//AGREGAME LOS BORDES REDONDOS DE BOTTOM AL ULTIMO HIJO
}