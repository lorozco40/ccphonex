var idconv = "";
var api = "";
var intervalId = "";
var count_llamar = 0;
var domain = cid.vcServ;
var estatus = "Abandonada";
var room = "";
var cam = cid.cam;
var options = {
    roomName: '',
    width: '100%',
    height: '535px',
    parentNode: document.querySelector('#msg'),
    lang: 'es',
    configOverwrite: {
        startWithAudioMuted: false,
        startWithVideoMuted: false,
        prejoinConfig: {
            enabled: false,
        },
        remoteVideoMenu: {
            disabled: true,
        },
        toolbarButtons: ['fullscreen','tileview','microphone','camera','__end'],
        fileRecordingsEnabled: false,
    },
    interfaceConfigOverwrite: {
        filmStripOnly: true,
    },
    userInfo: {
        email: cid.email,
        displayName: cid.nombre,
    }
};

function llamar() {
    estatus = "Abandonada";
    idconv = "";
    count_llamar = 0;
    $('#msg').html(`<div class="main"><center>Espera por favor!<br />Estamos contactando con un agente<br />
        <div class="centro">
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
      </div></center></div>`);
    intervalId = setInterval(function(){
        var sdata = (idconv == '') ? {idconv:idconv, data:cid} : {idconv:idconv} ;
        $.post(site_url+"videollamada/llamar", sdata, function(data) {
            if (data.idconv) { idconv = data.idconv; }
            if (data.close) { colgar(3); }
            if (data.room) {
                clearInterval(intervalId);
                room = data.room;
                esperaresp();
            } else {
                count_llamar++;
                if ((count_llamar==2 && data.idconv == '') || count_llamar==100) {
                    colgar();
                }
            }
        },"json")
        .fail(function(){
            clearInterval(intervalId);
            $("#msg").html("<br /><h3> Revisa tu conexión a internet e intenta nuevamente.</h3>");
        });
    },3000);
}

function esperaresp() {
    count_llamar = 0;
    intervalId = setInterval(function(){
        $.post(site_url+"videollamada/chekresp", {idconv:idconv}, function(data) {
            if (data.room) {
                clearInterval(intervalId);
                conektar(room);
            } else if (data.close) {
                colgar(2);
            }
            count_llamar++;
            if (count_llamar==100) {
                colgar(2);
            }
        },"json")
        .fail(function(){
            clearInterval(intervalId);
            $("#msg").html("<div class='main'><h3> Revisa tu conexión a internet e inténtalo nuevamente.</h3></div>");
        });
    }, 3000);
}

function conektar(room) {
    options.roomName = room;
    $("#msg").html("");
    api = new JitsiMeetExternalAPI(domain, options);

    api.addEventListener("videoConferenceJoined", function() {
        estatus = "Terminada";
        api.isVideoMuted().then(muted => {
            if(muted) {
                api.executeCommand('toggleVideo');
            }
        });
        api.isAudioMuted().then(muted => {
            if(muted) {
                api.executeCommand('toggleAudio');
            }
        });
    });
    api.addEventListener("videoConferenceLeft", function() {
        api.dispose();
        $("#msg").html("<br /><h3> Gracias por contactarnos! Buen día.</h3>");
    });
    api.addEventListener('participantLeft', function() {
        api.dispose();
        $("#msg").html("<br /><h3> Gracias por contactarnos! Buen día.</h3>");
    });
    api.addEventListener("errorOccurred", function(e) {
        api.dispose();
        $("#msg").html("<br /><h3> Se detectó un error en su conexión a internet. Intente nuevamente.</h3>");
    });
}

function colgar(tipo = 1) {
    clearInterval(intervalId);
    if (tipo == 1) {
        $("#msg").html("<br /><h3> Por el momento no hay un agente disponible.<br />" +
            "Intenta nuevamente más tarde.</h3><br /> <button class='btn btn-primary' id='llamnu'>Intentar nuevamente</button>");
        estatus = "Sin agente";
    } else {
        $("#msg").html("<br /><h3> El agente no ha respondido.<br />" +
        "Intenta nuevamente más tarde.</h3><br /> <button class='btn btn-primary' id='llamnu'>Intentar nuevamente</button>");
        estatus = "Agente no contesta";
    }
    if (tipo < 3) {
        $.post(site_url+"videollamada/fin", {tipo:estatus, idconv:idconv}, function(){
            estatus = "nomas";
        });
    }
}

$(document).ready(function() {
    llamar();
    window.onbeforeunload = function (e) {
        if (estatus == "Abandonada" && idconv != "") {
            $.post(site_url+"videollamada/fin", {tipo:estatus, idconv:idconv});
        }
    };
    $(document).on('click', '#llamnu', function(){
        llamar();
    });
});
