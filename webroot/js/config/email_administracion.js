var pag = 0;
var reg = 0;
var rpp = 20;
var obregs = {};

$(document).ready(function(){
    getpag();
    $(document).on("click", "#nuemcta", function(){
        $("#emctaform").trigger("reset");
        $("#emctaform input[name=id]").val(0);
        $("#emctaModal").modal("show");
    });

    $(document).on("click", ".desemcta", function(){
        savecta("id="+$(this).data("id")+"&activa=0");
    });
    $(document).on("click", ".actemcta", function(){
        savecta("id="+$(this).data("id")+"&activa=1");
    });
    $(document).on("submit", "#emctaform", function(e){
        e.preventDefault();
        savecta($("#emctaform").serialize());
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
    })

    $(document).on("click", ".om-reasignar", function(){
        $("#FormReasignar").trigger('reset');
        let id = $(this).data("id");
        let asignado = $(this).data("asignado");
        $("#asignado_text").val(asignado)
        $("#email_entry_id").val(id)

        $("#emctaModal").modal("show");
    });
    $(document).on("click", ".om-ver-correo", function(){
        let id = $(this).data("id");
        $("#asunto").html('');
        $("#correo_date").html('');
        $("#correo_contend").html('Cargando...');
        $("#ModalCorreo").modal("show");
        $("#spinnerModal").modal("show");
        let data = {id: id};
        $.ajax({
            url: site_url+'email/verCorreo',
            type: 'POST',
            data: data,
            dataType: 'json',
        })
        .done(function(data){
            $("#spinnerModal").modal("hide");
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                $("#asunto").html(data.subject);
                $("#correo_date").html(data.date);
                $("#correo_sender").html(data.sender);
                $("#correo_from").html(data.from);
                $("#correo_contend").html(data.htmlmsg);
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
    $(document).on("click", ".actualizar-asignado", function(){
        alert(email_entry_id);
    });
});

function reasignarAgente() {
    $("#spinnerModal").modal("show");
    let fm = document.getElementById("FormReasignar");
    let fd = new FormData(fm);
    $.ajax({
        url: site_url+'email/reasignarAgente',
        type: 'POST',
        processData: false,
        contentType: false,
        data: fd,
    })
    .done(function(data){
        $("#spinnerModal").modal("hide");
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            $("#emctaModal").modal("hide");
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
    return false;
}

function getpag(cual = false) {
    pag = (cual !== false) ? cual : pag;
    let sender = $("#contacto").val();
    $("#spinnerModal").modal("show");
      $.post(site_url+"email/listarCorreos", {sender: sender, pag: pag, rpp: rpp}, function(data) {
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            $("#Tabla .borrable").remove();
            var html = "";
            let respuesta = '';
            data.data.forEach((row,key) => {
                respuesta = (row.datetime_reply) ? row.datetime_reply : "Sin respuesta";
                html += "<div class='table-row borrable'>" +
                "<div class='table-cell'>"+row.datetime_received+"</div>" +
                "<div class='table-cell'>"+row.subject+"</div>" +
                "<div class='table-cell'>"+row.sender+"</div>" +
                "<div class='table-cell'>"+row.asignado+"</div>" +
                "<div class='table-cell'>"+respuesta+"</div>" +
                "<div class='table-cell'><button type='button' class='btn btn-dark om-ver-correo' data-id='"+row.id+"'>Ver</button></div>" +
                "<div class='table-cell'>";
                    if( row.datetime_reply == null )
                        html += "<button type='button' class='btn btn-dark om-reasignar' data-id='"+row.id+"' data-asignado='"+row.asignado+"'>Reasignar a agente</button>";
                html +="</div>" +
                "</div>";
           });
            $("#Tabla").append(html);
            paginacion(data.pag, data.reg, data.rpp, data.rpp);
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