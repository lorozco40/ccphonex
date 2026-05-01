'use strict'

var tap = {
    pag: 0,
    rpp: 20,
    fid: 0,
    cid: 0,
    tid: 0,
    testatus: 0,
    regs: {},
    noatri: ['apertura','asignar_a','cierre','cliente','id','id_cliente','informar','linkedid','uniqueid','estatus','detalle','files','histo'],
    getickets: () => {
        $("#spinnerModal").modal("show");
        tap.fid = $("#fid").val();
        tap.tid = $("#tid").val();
        tap.cid = $("#fid").find(":selected").data("cid");
        tap.testatus = $("#testatus").val();
        $.ajax({
            url: site_url+'panel/getickets',
            type: 'POST',
            data: {fid: tap.fid, tid: tap.tid, testatus: tap.testatus, pag: tap.pag, rpp: tap.rpp},
            dataType: 'json',
        })
        .done(function(data){
            $("#spinnerModal").modal("hide");
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, 'danger');
            } else {
                let html = "";
                tap.regs = {};
                $("#tickets .borrable").remove();
                if (data.data.length>0) {
                    for(let i in data.data) {
                        tap.regs[data.data[i].id] = data.data[i];
                        let detalle = (data.data[i].detalle.length > 60) ? data.data[i].detalle.substr(0, 60) + " ..." : data.data[i].detalle;
                        html += '<div class="table-row borrable">';
                        html += '<div class="table-cell">'+data.data[i].id+'</div>';
                        html += '<div class="table-cell">'+data.data[i].apertura+'</div>';
                        html += '<div class="table-cell">'+data.data[i].estatus+'</div>';
                        html += '<div class="table-cell">'+detalle+'</div>';
                        html += '<div class="table-cell">';
                        html += '<button class="btn btn-primary abrirticket" data-toggle="modal" data-target="#ticketModal" data-id="'+data.data[i].id+'">Abrir</button>';
                        html += '</div>';
                        html += '</div>';
                    }
                    $("#tickets").append(html);
                }
                paginacion(data.pag, data.tot, data.rpp, data.data.length, "paginacion", "tap.getickets");
            }
            if (tap.tid!="") $("#testatus").val(0);
        })
        .fail(function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, 'danger');
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", 'danger');
            }
        });
    },
    regenmodal: (id) => {
        pnx.traeForm(tap.cid, tap.fid, id);
    },
}

$(document).ready(function(){
    tap.getickets();
    $(document).on("submit", "#busform", function(e){
        e.preventDefault();
        tap.getickets();
    });
    $(document).on("click", ".abrirticket", function(){
        let id = $(this).data("id");
        tap.regenmodal(id);
    });
    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();
        tap.pag = $(this).data('pag');
        tap.getickets();
    });
    $(document).on("change", "#elirpp", function () {
        tap.pag = 0;
        tap.rpp = $(this).val();
        tap.getickets();
    });
    $(document).on("change", "#testatus, #fid", function () {
        tap.pag = 0;
        tap.getickets();
    });
    $(document).on("change", "#emctaspl", function(e){
        e.preventDefault();
        tap.pag = ($(this).val() * tap.rpp) - tap.rpp;
        tap.getickets();
    });
});

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
