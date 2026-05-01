var intervalo;
$(document).ready(function(){
    traereventos();
    setInterval(function(){
        traereventos();
    }, 30000);
    intervalo = setInterval(function(){
        llenarRecordatorio();
    }, 2000);
});
$(document).on("click", ".updatecal .btn", function(e){
    e.preventDefault();
    $.post(site_url+"calendario/umodificar", $(this).closest('form').serialize()+'&'+$(this).attr('name')+'=si', function(data){
        if (data == true) {
            toastmsg('Modificado con éxito', "success");
        } else {
            toastmsg('Error desconocido!', "danger");
        }
        traereventos();
    },"json");
    return false;
});
$(document).on("submit", "#modalCalendar form", function(e){
    e.preventDefault();
    $('#modalCalendar').modal('hide');
    $.post(site_url+'calendario/creacalendar', $("#modalCalendar form").serialize(), function(data){
        if (data == true) {
            toastmsg('Calendarización exitosa', "success");
            $("#modalCalendar form").trigger("reset");
        } else {
            toastmsg('Error desconocido!', "danger");
        }
        traereventos();
    },"json");
    return false;
});
$(document).on("click", "#recform .btn", function(e){
    e.preventDefault();
    $('#modalRecordatorio').modal('hide');
    $.post(site_url+"calendario/umodificar", $("#recform").serialize()+'&'+$(this).attr('name')+'=si', function(data){
        if (data == true) {
            $("#nuevoRecLink").html("");
            toastmsg('Gracias.', "success");
            $("#recform").trigger("reset");
            traereventos();
            intervalo = setInterval(function(){
                llenarRecordatorio();
            }, 2000);
        } else {
            toastmsg('Error desconocido consulta con tu supervisor.', "danger");
        }
    });
});
function llenarRecordatorio() {
    var scheduled = $("#ueventos .card:first-child").find("input[name='scheduled']").val();
    if (null != scheduled && "0000-00-00T00:00:00" != scheduled) {
        prueba = fechaAdatetime(scheduled);
        diferencia = (new Date()).getTime() - Date.parse(prueba);
        if (diferencia>0) {
            id = $("#ueventos .card:first-child").find("input[name='id']").val();
            agentes = $("#ueventos .card:first-child").find("input[name='agentes']").val();
            name = $("#ueventos .card:first-child").find("input[name='name']").val();
            last = $("#ueventos .card:first-child").find("input[name='last']").val();
            type = $("#ueventos .card:first-child").find("input[name='type']").val();
            observations = $("#ueventos .card:first-child").find("textarea[name='observations']").val();
            $("#recid").val(id);
            $("#recagentes").val(agentes);
            $("#recname").val(name);
            $("#reclast").val(last);
            $("#rectype").val(type);
            $("#recscheduled").val(scheduled);
            $("#recobservations").val(observations);
            $("#nuevoRecLink").html('<button type="button" class="btn btn-warning" data-toggle="modal" data-idr="" data-target="#modalRecordatorio" id="traerrecordatorio"><i class="fas fa-bell campana"></i> Recordatorio</button>');
            clearInterval(intervalo);
        }
    }
}
function traereventos() {
    $("#ueventos").html("<tr><td colspan='7' class='text-center'><i class='fas fa-spinner fa-2x fa-pulse'></i><span class='sr-only'>Cargando ...</span></tr>");
    var html = "No tienes eventos programados.";
    $.post(site_url+"calendario/traer_calendario",{uid: uid}, function(data){
        if (null != data) {
            var html = "";
            var clasecabeza = "azul";
            data.forEach(function(fila){
                clasecabeza = "azul";
                if ((new Date()).getTime() - Date.parse(fila.scheduled)>0) clasecabeza = "rojo";
                html += "<div class='col-sm-4 card'><div class='card-body'><div class='card-title text-center "+clasecabeza+"'>"+
                    fila.type+"</div><div class='card-content'><form action='#' class='updatecal form'>"+
                    "<input type='hidden' name='id' value='"+fila.id+"'>" +
                    "<input type='hidden' name='agentes' value='"+fila.id_user+"'>";
                html += "<input type='datetime-local' class='form-control date' name='scheduled' min="+hoylocale()+" value="+fechaAdatetimelocale(fila.scheduled)+">\n";
                html += "<input name='name' value='"+fila.name+"' class='form-control' readonly='readonly'>\n";
                html += "<input name='last' value='"+fila.last+"' class='form-control' readonly='readonly'>\n";
                html += "<input type='hidden' name='type' value='"+fila.type+"'>\n";
                html += "<textarea name='observations' class='form-control'>"+fila.observations+"</textarea>\n";
                html += "<br><button name='reagendar' class='btn btn-info'>Reagendar</button>\n";
                html += "<button name='cancelar' class='btn btn-secondary'>Cancelar</button></form></div></div></div>\n";
            });
        }
        $("#ueventos").html(html);
    },"json");
}

function fechaAdatetimelocale(cadena) {
    return cadena.substring(0, 10) + 'T' + cadena.substring(11, 19);
}

function fechaAdatetime(cadena) {
    return cadena.substring(0, 10) + ' ' + cadena.substring(11, 19);
}

function hoylocale() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var hh = today.getHours();
    var ii = today.getMinutes();
    var ss = today.getSeconds();
    if(dd<10) dd = '0'+dd;
    if(mm<10) mm = '0'+mm;
    if(hh<10) hh = '0'+hh;
    if(ii<10) ii = '0'+ii;
    if(ss<10) ss = '0'+ss;
    today = today.getFullYear() + '/' + mm + '/' + dd + 'T' + hh + ':' + ii + ':' + ss;
    return today;
}
