var pag = 0;
var reg = 0;
var rpp = 20;
var obregs = {};
var bus = '';
var permisoBD = ["pc","emd","ems","emu","rmu"];

$(document).ready(function(){
    getpag();

    $(document).on('submit', "#busform", function(e){
        e.preventDefault();
        pag = 0;
        bus = $("#busform input[type=text]").val();
        console.log(bus);
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

    $(".btn-update").click(function(){
        if($.isFunction(window.getConnectstatus) ) {
            if(!getConnectstatus()) {
                id = $(this).attr("id").replace("btn_", "");
                data = "";
                data += ($("#usr_pc-"+id).is(":checked"))?"1,":"0,";
                data += ($("#usr_emd-"+id).is(":checked"))?"1,":"0,";
                data += ($("#usr_ems-"+id).is(":checked"))?"1,":"0,";
                data += ($("#usr_emu-"+id).is(":checked"))?"1,":"0,";
                data += ($("#usr_rmu-"+id).is(":checked"))?"1":"0";
                socket.emit('new_user', {data: data, id:id });
                toastmsg("Cambios realizados exitosamente.", "success");
            }
        } else {
            toastmsg("Servidor para chat interno no está conectado.", "danger");
        }
    });

});

$(".btn-save").click(function(){
    $("#spinnerModal").modal("show");
    let fm = document.getElementById("permisos_form");
    let fd = new FormData(fm);
    $.ajax({
        url: site_url+'chatinterno/guardarPermisos',
        type: 'POST',
        processData: false,
        contentType: false,
        data: fd,
    })
    .done(function(data){
        $("#spinnerModal").modal("hide");
        $("#ModalAtributos").modal("hide");
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
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
});

function getpag(cual = 0) {
    pag = (cual != 0) ? cual : pag;
    $("#spinnerModal").modal("show");
    /* ruta para traer los registros */
    $.post(site_url+'chatinterno/lista', {pag: pag, rpp: rpp, bus: bus}, function(data) {
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            $("#reglist .borrable").remove();
            reg = data.regs;
            var html = "";
            var cuenta = 0;
            data.data.forEach((row,key) => {
                obregs[row.id] = row;
                cuenta++;
                permisos = row.permisos.split(",");
                html += "<div class='table-row borrable'>" +
                            "<div class='table-cell'>"+row.nombre+"</div>" +
                            "<div class='table-cell'>"+row.perfil+"</div>";
                            for (let index = 0; index < permisos.length; index++) {
                                checked = permisos[index] == 1 ? "checked" : "";
                                html += "<div class='table-cell'>"+
                                            "<input class='form-control check' type='checkbox' "+checked+" name='"+permisoBD[index]+"-"+row.id+"' id='usr_"+permisoBD[index]+"-"+row.id+"' />" +
                                        "</div>";
                            }
                html += "<input type='hidden' name='id_user-"+row.id+"' value='"+row.id+"'/>" +
                        "</div>";
            });
            $("#reglist").append(html);
            paginacion(pag, reg, rpp, cuenta);
        }
        $("#spinnerModal").modal("hide");
    },"json")
    .fail(function(data) {
        $("#spinnerModal").modal("hide");
        if (typeof data.responseJSON.error !== "undefined") {
            toastmsg(data.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}