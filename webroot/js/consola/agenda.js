var agenda = {
    bus: '1n1c14nd0',
    data: {},
    nobtn: " <span class='btn btn-secondary disabled' disabled><i class='far fa-times-circle'></i></span> ",
    datahtml: "<span class='ml-3'>No se encontraron registros con esos parámetros.</span>",
    buscar: function(quebus) {
        if (quebus.length < 3) {
            $("#agesearchresult").html("<span class='ml-3'>Mínimo 3 caracteres para buscar. (solo se muestran los primeros 20 resultados)</span>");
            return;
        }
        if (quebus != agenda.bus) {
            agenda.bus = quebus;
            $("#agesearchresult").html("<i class='fas fa-spinner fa-2x fa-pulse'></i><span class='sr-only'>Cargando ...</span>");
            $.post(site_url+'agenda/buscaragenda', {bus: quebus, tca: false, client_active: 1}, function(data){
                if (typeof data.error !== 'undefined') {
                    toastmsg("No se encontraron datos", "danger");
                    $("#agesearchresult").html("<span class='ml-3'>No se encontraron registros con esos parámetros.</span>");
                } else {
                    agenda.datahtml = "<div class='table table-stripped'><div class='table-header-group'>" +
                    "<div class='table-cell'>Nombre</div><div class='table-cell'>Teléfono</div>" +
                    "<div class='table-cell'>Email</div></div>";
                    Object.values(data.data).forEach(item => {
                        agenda.data[item.id] = item;
                        dequien = (item.id_user == agente.id) ? "(M)" : "(P)";
                        agenda.datahtml += "<div class='table-row'><div class='table-cell'>" + dequien + ' ' +
                        item.name + ' ' + item.last + "</div><div class='table-cell'>" + item.phone + "</div>" +
                        "<div class='table-cell'>" + item.email + "</div><div class='table-cell'>" +
                        "<span class='btn btn-link agever' data-id='" + item.id + "' data-toggle='tooltip' title='Ver " +
                        item.name + " " + item.last + "'><i class='far fa-eye'></i></span>" +
                        "<span class='btn btn-dark ageedit' data-id='" + item.id + "' data-toggle='tooltip' title='Editar " +
                        item.name + " " + item.last + "'><i class='far fa-edit'></i></span> ";
                        if (item.email.length > 5 && agente.permisoSec.includes('email') && agente.email != "" && agente.email != 0 && item.available == 1) {
                            agenda.datahtml += " <span class='btn btn-light emailacliente' data-email='" + item.email + "' data-toggle='tooltip' title='Enviar email a " +
                            item.name + " " + item.last + "'><i class='far fa-envelope'></i></span> ";
                        } else {
                            agenda.datahtml += agenda.nobtn;
                        }
                        if (item.phone.length > 1 && agente.exten.length>0 && item.available == 1) {
                            //  "<span class='btn btn-success waacliente' data-id='" + item.id + "'><i class='fab fa-whatsapp'></i></span>" +
                            agenda.datahtml += " <span class='btn btn-primary llamarcliente' data-numero='" + item.phone + "' data-toggle='tooltip' title='Llamar a " +
                                item.name + " " + item.last + "'><i class='fas fa-phone'></i></span> ";
                            agenda.datahtml += " <span class='btn btn-secondary smsacliente' data-numero='" + item.phone + "' data-toggle='tooltip' title='SMS a " +
                                item.name + " " + item.last + "'><i class='fas fa-sms'></i></span> ";
                        } else {
                            agenda.datahtml += agenda.nobtn;
                        }
                        agenda.datahtml += "</div></div>";
                    });
                    $("#agesearchresult").html(agenda.datahtml);
                }
            },"json");
        } else {
            if(Object.keys(agenda.data).length>0) {
                $("#agesearchresult").html(agenda.datahtml);
            } else {
                $("#agesearchresult").html("<span class='ml-3'>No se encontraron registros con esos parámetros.</span>");
            }
        }
    },
    ver: function(id) {
        $("#agesearchresult").html("<i class='fas fa-spinner fa-2x fa-pulse'></i><span class='sr-only'>Cargando ...</span>");
        $.post(site_url+'agenda/traerporid', {id: id}, function(data){
            if (typeof data.error === 'undefined') {
                var res = "<span class='btn btn-dark ageedit ml-3' data-id='" + data.cliente.id + "' data-toggle='tooltip' title='Editar " +
                    data.cliente.id + "'><i class='far fa-edit'></i></span>" +
                    "<br><br><div class='row' style='margin:0'><div class='col'><div class='table table-stripped'>";
                Object.keys(data.cliente).forEach(function(key){
                    if(key != 'id' && key != 'id_campaign')
                        res += "<div class='table-row'><div class='table-cell'>"+key+"</div><div class='table-cell'><strong>"+data.cliente[key]+"</strong></div></div>\n";
                });
                res += "</div></div><div class='col'><h6 class='text-center'><i>Tickets abiertos</i></h6>";
                Object.keys(data.tics).forEach(function(key){
                    res += "<div class='row'><a class='ticketlink' href='#' data-fid='"+data.tics[key].fid+
                        "' data-cid='"+data.tics[key].cid+"' data-id='"+data.tics[key].id+"'>"+data.tics[key].fname+
                        " (ID: "+data.tics[key].id+")</a></div>\n";
                });
                res += "</div>";
                $("#agesearchresult").html(res);
            } else {
                toastmsg(data.error, "danger");
            }
        },"json")
        .fail(function(){
            toastmsg("Error de comunicación.", "danger");
        });
    },
    editar: function(id) {
        if (agenda.data[id]) {
            reg = agenda.data[id];
        } else {
            agenda.buscar($(".crm_form select[name=id_cliente] option:selected").text().substring(4,7));
            reg = agenda.data[id];
        }
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
    nuevo: function() {
        $("#ageform").trigger("reset");
        $("#ageform input[name=id]").val('0');
        $("#ageform select[name=id_user]").parents("div.input-group").show();
        $("#ageform div.scam").hide();
        $("#agenda-modal").modal('show');
    },
    guardar: function() {
        let cid = $("#ageform select[name=id_campaign]").val();
        if(!cid) {
          cid = $("#ageform input[name=id_campaign]").val();
        }
        let fcid = $("#campanasal select[name=formIdCam]").val();
        if(!cid) {
            toastmsg('Elige una campaña', "danger");
            return false;
        }
        let nombre = $("#ageform input[name=name]").val() + ' ' + $("#ageform input[name=last]").val();
        $.post(site_url+'agenda/guardar', $("#ageform").serialize(), function(data){
            if(typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                if (cid != fcid && document.body.contains($(".crm_form select[name=id_cliente]")[0])) {
                    toastmsg('Cliente agregado a otra campaña', "danger");
                } else if (cid == fcid && document.body.contains($(".crm_form select[name=id_cliente]")[0])) {
                    $(".crm_form select[name=id_cliente]").append("<option value='"+data+"' selected>"+nombre+"</option>");
                }
                $("#agenda-modal").modal("hide");
                toastmsg("Registro guardado", "success");
                $("#agesearchresult").html("");
                $("#catabus").val("");
                agenda.data = {};
                agenda.bus = '1n1c14nd0';
            }
        }, 'json')
        .fail(function() {
            $("#agenda-modal").modal("hide");
            toastmsg("Error de comunicación.", "danger");
        });
    }
}

$(document).ready(function(){
    $(document).on("submit", "#agebusform", function(e){
        e.preventDefault();
        agenda.buscar($("#agebusform input[name=catabus]").val());
    });
    $(document).on("click", ".agever", function(e){
        e.preventDefault();
        agenda.ver($(this).data('id'));
    });
    $(document).on("click", ".nuevoclientebtn", function(e){
        e.preventDefault();
        agenda.nuevo();
    });
    $(document).on("click", ".ageedit", function(e){
        e.preventDefault();
        agenda.editar($(this).data('id'));
    });
    $(document).on('submit', "#ageform", function(e){
        e.preventDefault();
        agenda.guardar();
    });
    $(document).on("click", ".ticketlink", function(e){
        e.preventDefault();
        pnx.traeForm($(this).data('cid'), $(this).data('fid'), $(this).data('id'))
    });
});
