var agenda = {
    pag: 0,
    reg: 0,
    rpp: 20,
    bus: '',
    data: {},
    traerdata: function(nuval = 0) {
        agenda.pag = (nuval != 0) ? (nuval-1)*agenda.rpp : agenda.pag;
        $("#spinnerModal").modal("show");
        agenda.bus = $("#buscar").val();
        $.post(site_url+"agenda/buscarAgenda", {bus: agenda.bus, pag: agenda.pag, rpp: agenda.rpp}, function(data) {
            var html = "<div class='table-row'><div class='table-cell text-warning'> No se encontro ningún registro.</td></tr>";
            if (!data.data || data.data.length == 0) {
                $("#tablaagenda .table-row").remove();
                $("#paginacion").html("");
                agenda.pag=0;
                $("#tablaagenda").append(html);
            } else {
                var cuenta = 0;
                html = '';
                agenda.reg = data.reg;
                data.data.forEach((row,key) => {
                    agenda.data[row.id] = row;
                    cuenta++;
                    activa = (row.active=='1') ? 'Si' : 'No';
                    conttble = (row.available=='1') ? 'Si' : 'No';
                    html += "<div class='table-row'>" +
                        "<div class='table-cell'>"+row.agenda+"</div>" +
                        "<div class='table-cell'>"+row.name+"</div>" +
                        "<div class='table-cell'>"+row.last+"</div>" +
                        "<div class='table-cell'>"+row.phone+"</div>" +
                        "<div class='table-cell'>"+row.email+"</div>" +
                        "<div class='table-cell'>"+activa+"</div>" +
                        "<div class='table-cell'>"+conttble+"</div>" +
                        "<div class='table-cell'><button type='button' class='btn btn-dark editar' data-id='"+row.id+"'>Editar</button></div>" +
                    "</div>";
                });
                $("#tablaagenda .table-row").remove();
                $("#tablaagenda").append(html);
                paginacion(agenda.pag, data.reg, agenda.rpp, cuenta, 'paginacion', 'agenda.traerdata');
            }
            $("#spinnerModal").modal("hide");
        },"json")
        .fail(function(data) {
            console.log(data);
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    modificar: function(id) {
        reg = agenda.data[id];
        if ( (agente.perfil == 'agente' || agente.perfil == 'crm') && reg.id_user == null ) {
            $("#ageform select[name=id_user]").parents("div.input-group").hide();
            $("#ageform div.scam").show();
        } else {
            $("#ageform select[name=id_user]").parents("div.input-group").show();
            $("#ageform div.scam").hide();
        }
        Object.keys(reg).forEach(function(key){
            if (key=="active" || key=="available") {
                como = (reg[key]==1) ? true : false;
                $("#ageform input[name="+key+"]").prop("checked", como);
            } else {
                $("#ageform input[name="+key+"]").val(reg[key]);
                $("#ageform select[name="+key+"]").val(reg[key]);
            }
        });
        $("#agenda-modal").modal('show');
    },
    guardar: function() {
        $("#agenda-modal").modal("hide");
        $("#spinnerModal").modal("show");
        var id  = $("#ageform #id").val();
        var nom = $("#ageform input[name=name]").val() + ' ' + $("#ageform input[name=last]").val();
        $.post(site_url+'agenda/guardar', $("#ageform").serialize(), function(data){
            $("#spinnerModal").modal("hide");
            if(typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                if (id==0) {
                   $("select[name=id_cliente]").append('<option value="'+data+'" selected>'+nom+'</option>');
                   $("select[name=id_cliente]").val(data);
                }
                toastmsg("Registro guardado", "success");
                agenda.traerdata();
            }
        },'JSON');
    }
}
function guardadoMasivoModal() {
    $("#form_agenda_file").trigger("reset");
    $("#agendafile-modal").modal('show');
}
function guardadoMasivo() {
    var data = new FormData(document.getElementById("form_agenda_file"));     
    $.ajax({
        url: site_url+'agenda/guardarArchivo',
        data: data,
        processData:false,
        contentType:false,
        type: 'POST',
        success: function (data)
        {
            if(typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                $("#agendafile-modal").modal("hide");
                toastmsg(data, "success");
                agenda.traerdata();
            }      
        }
    });
}
$(document).ready(function(){
    agenda.traerdata();
    $(document).on("click", ".editar", function(){
        var id = $(this).data("id");
        agenda.modificar(id);
    });
    $(document).on("click", "#nuereg", function(){
        $("#ageform").trigger("reset");
        $("#id").val('0');
        $("#id_user").parents("div.input-group").show();
        $("div.scam").hide();
        $("#agenda-modal").modal('show');
    });
    $(document).on('submit', "#agendaenter", function(e){
        e.preventDefault();
        agenda.pag = 0;
        agenda.traerdata();
    });
    $(document).on('submit', "#ageform", function(e){
        e.preventDefault();
        agenda.guardar();
    });
    $(document).on("change", "#elirpp", function(){
        agenda.rpp = $(this).val();
        agenda.traerdata();
    });
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        agenda.pag = $(this).data('pag');
        agenda.traerdata();
    });
});
