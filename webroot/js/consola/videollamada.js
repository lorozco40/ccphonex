var vintervalo = "";
var vtiempoout = "";
var vidllamdata = {
    tid: "", // Transfer user ID
    tsala: "", // Transfer Sala
    permisos: ['0','0','0','0','0'], // autodisponible, autocontestar, grabar, transferir, colgar
    domain: '',
    idconv: '',
    callerid: '',
    incdata: '',
    options: {
        jwt: '',
        roomName: '',
        width: '100%',
        height: '535px',
        parentNode: document.querySelector('#elvidchat'),
        lang: 'es',
        configOverwrite: {
            apiLogLevels: 'error',
            startWithAudioMuted: true,
            startWithVideoMuted: true,
            remoteVideoMenu: {
                disabled: true,
                disableKick: true,
                disableGrantModerator: true,
            },
            fileRecordingsEnabled: true,
            prejoinConfig: {
                enabled: false,
            },
            toolbarButtons: [
                // 'embedmeeting', 'feedback', 'invite', 'linktosalesforce', 'livestreaming',
                // 'shareaudio', 'sharedvideo', 'videoquality',
               'camera', 'chat', 'closedcaptions', 'desktop', 'download',
               'etherpad', 'filmstrip', 'fullscreen', 'help', 'highlight',
               'microphone', 'mute-everyone', 'mute-video-everyone',
               'participants-pane', 'security', 'select-background',
               'shortcuts', 'stats', 'tileview', 'toggle-camera', '__end'
            ],
        },
        interfaceConfigOverwrite: {
            filmStripOnly: true,
        },
        userInfo: {
            email: agente.user,
            displayName: agente.name+' '+agente.last,
        }
    },
    api: {},
    ringtone: document.getElementById('ringtone'),
    oirtimbre: function() {
        vintervalo = setInterval(function(){
            $.post(site_url+'videollamada/oirtimbre', function(data){
                if (data) {
                    vidllamdata.idconv = data.idconv;
                    vidllamdata.incdata = data;
                    vidllamdata.timbrar();
                }
            },'JSON');
        }, 4000);
    },
    timbrar: function() {
        clearInterval(vintervalo);
        var htmltop = "";
        var html = "<ul>";
        var excluir = ['idconv', 'sexo', 'folio', 'prioridad', 'perfil','tran'];
        Object.entries(vidllamdata.incdata).forEach(([key, value]) => {
            if (key == 'folio') {
                htmltop = "<p><strong class='frazul'>" + capitalize(key) +
                    ": </strong> <span class='frmorado'>" + value + "</span></p>";
            }
            if (key == 'tran' && value) {
                htmltop = "<p>Transferencia!</p>";
            }
            if (!excluir.includes(key)) {
                html += "<li><strong class='frazul'>" + capitalize(key) +
                    ": </strong> <span class='frmorado'>" + value + "</span></li>";
            }
        });
        html += "</ul>";
        $("#vgetdatatop").html(htmltop);
        $("#vgetdatabot").html(html);
        try {
            vidllamdata.ringtone.play();
        } catch(e) { console.log("Error en audio."); }
        if (vidllamdata.permisos[1]=='0') {
            toastmsg('Tienes una videollamada.');
            $("#vcontestar, #vrechazar").removeClass("d-none");
            $("#vnodispo").addClass("d-none");
            vtiempoout = setTimeout(function(){
                vidllamdata.rechazar();
            }, 60000);
        } else {
            vidllamdata.conectarsala();
        }
    },
    conectarsala: function(cual = 'normal') {
        clearTimeout(vtiempoout);
        $("#vcontestar, #vrechazar").addClass("d-none");
        activity_set('Videollamada');
        try {
            vidllamdata.ringtone.pause();
        } catch(e) { console.log("Error en audio."); }
        if (vidllamdata.options.roomName != '' && vidllamdata.domain != '') {
            $.ajax({
                url: 'https://localhost:8443/vidconf/token',
                type: 'get',
                data: {room: vidllamdata.options.roomName},
                dataType: 'json',
                headers: {key: agente.token},
                success: function (data) {
                    vidllamdata.options.jwt = data;
                    console.log(vidllamdata.options);
                    vidllamdata.mostrarsala();
                }
            });
        } else {
            vidllamdata.colgar('Error');
        }
    },
    mostrarsala: function() {
        vidllamdata.api = new JitsiMeetExternalAPI(vidllamdata.domain, vidllamdata.options);
        vidllamdata.api.addEventListener('videoConferenceJoined', function() {
            vidllamdata.aceptar();
        });
        vidllamdata.api.addEventListener('participantJoined', function(joined) {
            vidllamdata.callerid = joined.id;
            vidllamdata.ocupado();
            vidllamdata.api.isVideoMuted().then(muted => {
                if(muted) {
                    vidllamdata.api.executeCommand('toggleVideo');
                }
            });
            vidllamdata.api.isAudioMuted().then(muted => {
                if(muted) {
                    vidllamdata.api.executeCommand('toggleAudio');
                }
            });
        });
        vidllamdata.api.addEventListener('participantLeft', function(lefted) {
            vidllamdata.colgar();
        });
        vidllamdata.api.addEventListener('videoConferenceLeft', function() {
            vidllamdata.colgar();
        });
    },
    disponible: function() {
        $.post(site_url+'videollamada/hadis', function(data){
            if (data.error) {
                toastmsg(data.error, "danger");
                vidllamdata.options.roomName = '';
                vidllamdata.domain = '';
            } else if (data.sala) {
                vidllamdata.options.roomName = data.sala;
                vidllamdata.domain = data.serv;
                $("#vdispo").addClass("d-none");
                $("#vnodispo").removeClass("d-none");
                vidllamdata.oirtimbre();
            }
        });
    },
    nodisponible: function() {
        $.post(site_url+'videollamada/hanodis', function(data){
            if (data.error) {
                toastmsg(data.error, "danger");
            } else {
                vidllamdata.options.roomName = '';
                vidllamdata.domain = '';
                $("#vdispo").removeClass("d-none");
                $("#vnodispo").addClass("d-none");
                clearInterval(vintervalo);
            }
        });
    },
    aceptar: function() {
        // vidllamdata.api.executeCommand('startRecording');
        clearTimeout(vtiempoout);
        $.post(site_url + 'videollamada/aceptar').fail(function(err){ console.log(err); });
        vtiempoout = setTimeout(function(){
            vidllamdata.colgar('Abandonada');
        }, 60000);
    },
    ocupado: function(quien) {
        clearTimeout(vtiempoout);
        $.post(site_url + 'videollamada/ocupado', {idconv: vidllamdata.idconv, callerid: vidllamdata.callerid});
        if (vidllamdata.permisos[3] == '1') {
            $("#vtransferir").removeClass("d-none");
        }
    },
    rechazar: function() {
        clearTimeout(vtiempoout);
        try {
            vidllamdata.ringtone.pause();
        } catch(e) { console.log("Error en audio."); }
        $.post(site_url+'videollamada/rechazar');
        $("#vnodispo").removeClass("d-none");
        $("#vcontestar, #vrechazar").addClass("d-none");
        $("#vgetdatatop").html("");
        $("#vgetdatabot").html("");
    },
    esp_t_acep: function() { // Esperar transferencia aceptación / rechazo
        vintervalo = setInterval(function(){
            $.post(site_url+'videollamada/tranp3', {tid: vidllamdata.tid}, function(data){
                if (data.aceptada) {
                    api.executeCommand('sendParticipantToRoom', {
                        participantId: vidllamdata.callerid,
                        roomId: vidllamdata.tsala
                    });
                } else if (data.rechazada) {
                    clearInterval(vintervalo);
                    toastmsg("El usuario seleccionado ha rechazado la transferencia!", "danger");
                }
            },'JSON');
        }, 4000);
    },
    colgar: function(razon = 'Terminada') {
        $("#vtransferir").addClass("d-none");
        $("#vgetdatatop").html("");
        $("#vgetdatabot").html("");
        vidllamdata.api.executeCommand('hangup');
        vidllamdata.api.dispose();
        activity_unset();
        $.post(site_url+'videollamada/fin', {tipo:razon, idconv:vidllamdata.idconv}, function(){
            vidllamdata.options.roomName = '';
            vidllamdata.options.jwt = '';
            vidllamdata.domain = '';
            vidllamdata.callerid = '';
            vidllamdata.idconv = '';
            vidllamdata.incdata = '';
        });
        if (vidllamdata.permisos[0] == '1') {
            vidllamdata.disponible();
        } else {
            $("#vdispo").removeClass("d-none");
            $("#vnodispo").addClass("d-none");
        }
    }
}

$(document).ready(function(){
    vidllamdata.permisos = agente.pervl.split(',');
    if (vidllamdata.permisos[2]=='1') {
        vidllamdata.options.configOverwrite.toolbarButtons.push('recording');
    }
    if (vidllamdata.permisos[4]=='1') {
        vidllamdata.options.configOverwrite.toolbarButtons.push('hangup');
    }
    if(vidllamdata.permisos[0] == '1'){
        vidllamdata.disponible();
    } else {
        $("#vdispo").removeClass("d-none");
    }
    $(document).on('click', '#vdispo', function(){
        vidllamdata.disponible();
    });
    $(document).on('click', '#vnodispo', function(){
        vidllamdata.nodisponible();
    });
    $(document).on('click', '#vcontestar', function(){
        vidllamdata.conectarsala();
    });
    $(document).on('click', '#vrechazar', function(){
        vidllamdata.rechazar();
    });
    $(document).on('click', '#vtransferir', function(){
        $("#spinnerModal").modal("show");
        $.post(site_url+'videollamada/tranp1', function(data){
            $("#spinnerModal").modal("hide");
            if (data.form) {
                $("#multiModalContent").html(data.form);
                $("#multiModal").modal("show");
            } else {
                toastmsg(data.msg, "danger");
            }
        });
    });
    $(document).on('click', '.tranp2', function(){
        vidllamdata.tid = $(this).data('id');
        vidllamdata.tsala = $(this).data('sala');
        $.post(site_url+'videollamada/tranp2', {tid: vidllamdata.tid, tfrom: vidllamdata.idconv});
        vidllamdata.esp_t_acep();
    });
    window.onbeforeunload = function(e) {
        vidllamdata.nodisponible();
        if (!agente.permisoSec.includes('nav')) {
            // Si no hay barra de navegación y si hay videollamada,
            // al salir de la consola se cierra la sesión
            $.post(site_url+'acceso/logout');
        }
    };
});

function capitalize(word) {
    var ret = word[0].toUpperCase() + word.slice(1).toLowerCase();
    return ret.replace('_', ' ');
}
