var crm = {
    cid: 0, // campaña id
    fid: 0, // form id
    pag: 0, // página
    rid: 0, // TicketID dentro de la lista para acción reabrir o transferir
    tid: '', // TicketID del formulario para filtro
    min: '', // Fecha inicial
    max: '', // Fecha final
    estatus: '', // Estatus del ticket
    now: '',
    traerForms: function() {
        $("#spinnerModal").modal("show");
        $("div.table-crm .table-row").remove();
        $("#pagination").html('');
        crm.now = new Date();
        crm.cid = $("#campana").val();
        $("#tid").val('');
        $("#estatus").val('');
        $.post(site_url+'crm/traerforms', {cid: crm.cid}, function(data){
            var html = "";
            if (data.length > 0) {
                data.forEach((item, i) => {
                    html += "<option value='"+item.id+"'>"+item.name+"</option>\n";
                });
                crm.fid = data[0].id;
                crm.traerData();
            } else {
                html = "<option value='0'>-- No hay CRM's --</option>" ;
                $("div.table-crm").append("<div class='table-row'>No hay registros</div>");
                $("#spinnerModal").modal("hide");
            }
            $("#form").html(html);
        });
    },
    traerData: function() {
        $("#spinnerModal").modal("show");
        $("div.table-crm .table-row").remove();
        $("#pagination").html('');
        crm.tid = $("#tid").val();
        crm.min = $("#min").val();
        crm.max = $("#max").val();
        crm.estatus = $("#estatus").val();
        $.post(site_url+'crm/traerdata', {
          fid: crm.fid,
          pag: crm.pag,
          tid: crm.tid,
          min: crm.min,
          max: crm.max,
          estatus: crm.estatus,
        }, function(data){
            if(data.cuenta>0) {
                $("div.table-crm").append(data.data);
                $("#pagination").append(data.pagination);
            } else {
                $("div.table-crm").append("<div class='table-row'>No hay registros</div>");
            }
            $("#spinnerModal").modal("hide");
        },'json');
    },
    reabrir: function() {
        $("#spinnerModal").modal("show");
        var razon = $("#reabrirform textarea").val();
        $.post(site_url+'crm/reabrir', {rid: crm.rid, fid: crm.fid, razon: razon}, function(data){
            $("#spinnerModal").modal("hide");
            crm.traerData();
            toastmsg(data.estatus, data.msg);
        });
    },
    transfer: function() {
        var aquien = $("#transferirform select").val();
        $.post(site_url+'crm/transferir', {rid: crm.rid, fid: crm.fid, tid:aquien}, function(data){
            crm.traerData();
            toastmsg(data.estatus, data.msg);
        });
    },
}

$(document).ready(function(){
    crm.traerForms();
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
    });

    $(document).on("change", "#campana", function(){
        crm.traerForms();
    });

    $(document).on("change", "#form", function(){
        crm.fid = $("#form").val();
        crm.pag = 0;
        $("#tid").val('');
        $("#estatus").val('');
        crm.traerData();
    });

    $(document).on("change", "#min, #max, #estatus", function(){
        crm.pag = 0;
        $("#tid").val('');
        crm.traerData();
    });

    $(document).on("change", "#tid", function(){
        crm.pag = 0;
        $("#estatus").val('');
        crm.traerData();
    });

    $(document).on("click", ".page-link", function(e) {
        e.preventDefault();
        crm.pag = ($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG;
        crm.traerData();
    });

    $(document).on("click", ".reabrir", function(){
        crm.rid = $(this).data("id");
        $(".tid").text(crm.rid);
        $("#reabrirmodal").modal("show");
    });

    $(document).on("click", ".transfer", function(e){
        crm.rid = $(this).data("id");
        $(".tid").text(crm.rid);
        $("#transferirmodal").modal("show");
    });

    $(document).on("submit", "#transferirform", function(e) {
        e.preventDefault();
        $("#transferirmodal").modal("hide");
        crm.transfer();
    });

    $(document).on("submit", "#reabrirform", function(e) {
        e.preventDefault();
        $("#reabrirmodal").modal("hide");
        crm.reabrir();
    });
});
