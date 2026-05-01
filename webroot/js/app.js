var ctxSip;
var chanConf = 0;
var comandos = ["*78", "*79"];
var lastsessid = null;
var nowsessid = null;

$(document).ready(function(){
    $("#txtRegStatus").html('<i class="fa fa-ban" style="color:red;"></i> '+'En espera de datos');
    if (typeof(agente) === 'undefined') {
        agente = JSON.parse(localStorage.getItem('SIPCreds'));
    }
    if (typeof(agente) != 'undefined' && agente.exten != null && agente.exten != "") {
        var servaskUrl = (agente.servask || '').replace(/\/+$/, '');
        if (!/^https?:\/\//i.test(servaskUrl)) {
            servaskUrl = ((location.protocol == 'https:') ? 'https://' : 'http://') + servaskUrl;
        }
        var servaskHost = servaskUrl.replace(/^https?:\/\//i, '');

        ctxSip = {
            config : {
                password        : agente.passask,
                displayName     : agente.name+" "+agente.last,
                uri             : agente.exten+'@'+servaskHost,
                wsServers       : servaskUrl.replace(/^http/i, 'ws')+'/ws',
                // stunServers : [],
                // usePreloadedRoute : true,
                registerExpires : 30,
                traceSip        : true,
                log             : {
                    level : 0,
                }
            },
            ringtone     : document.getElementById('ringtone'),
            ringbacktone : document.getElementById('ringbacktone'),
            dtmfTone     : document.getElementById('dtmfTone'),
            eventoproc   : document.getElementById('eventoEnProceso'),

            Sessions     : [],
            callTimers   : {},
            callActiveID : null,
            callVolume   : 1,
            Stream       : null,

            /**
             * Parses a SIP uri and returns a formatted US phone number.
             *
             * @param  {string} phone number or uri to format
             * @return {string}       formatted number
             */
            formatPhone : function(phone) {

                var num;

                if (phone.indexOf('@')) {
                    num =  phone.split('@')[0];
                } else {
                    num = phone;
                }

                num = num.toString().replace(/[^0-9]/g, '');

                if (num.length === 10) {
                    return '(' + num.substr(0, 3) + ') ' + num.substr(3, 3) + '-' + num.substr(6,4);
                } else if (num.length === 11) {
                    return '(' + num.substr(0, 3) + ') ' + num.substr(3, 4) + '-' + num.substr(7,4);
                } else {
                    return num;
                }
            },

            // Sound methods
            startRingTone : function() {
                try { ctxSip.ringtone.play(); } catch (e) { }
            },

            stopRingTone : function() {
                try { ctxSip.ringtone.pause(); } catch (e) { }
            },

            startRingbackTone : function() {
                try { ctxSip.ringbacktone.play(); } catch (e) { }
            },

            stopRingbackTone : function() {
                try { ctxSip.ringbacktone.pause(); } catch (e) { }
            },

            // Genereates a rendom string to ID a call
            getUniqueID : function() {
                return Math.random().toString(36).substring(2, 9);
            },

            newSession : function(newSess) {
                newSess.displayName = newSess.remoteIdentity.displayName || newSess.remoteIdentity.uri.user;
                newSess.ctxid       = ctxSip.getUniqueID();

                var status;

                if (newSess.direction === 'incoming') {
                    status = "Entrante: "+ newSess.displayName;
                    pnx.suenaLlamada(newSess.displayName);
                    ctxSip.startRingTone();
                } else {
                    if (!newSess.displayName.startsWith("*7")) {
                        ctxSip.startRingbackTone();
                        status = "Intentando: "+ newSess.displayName;
                        pnx.saleLlamada(newSess.displayName);
                    }
                    if (newSess.displayName == parseInt(agente.exten)+1000) {
                        chanConf = parseInt(newSess.displayName) - parseInt(agente.exten);
                        pnx.confBridge(newSess.displayName);
                    }
                }

                ctxSip.logCall(newSess, 'ringing');

                ctxSip.setCallSessionStatus(status);

                // EVENT CALLBACKS

                newSess.on('progress',function(e) {
                    if (e.direction === 'outgoing') {
                        ctxSip.setCallSessionStatus('Llamando...');
                    }
                });

                newSess.on('connecting',function(e) {
                    if (e.direction === 'outgoing') {
                        ctxSip.setCallSessionStatus('Conectando...');
                    }
                });

                newSess.on('accepted',function(e) {
                    // If there is another active call, hold it
                    if (ctxSip.callActiveID && ctxSip.callActiveID !== newSess.ctxid) {
                        ctxSip.phoneHoldButtonPressed(ctxSip.callActiveID);
                    }
                    ctxSip.stopRingbackTone();
                    ctxSip.stopRingTone();
                    ctxSip.setCallSessionStatus('En proceso');
                    ctxSip.logCall(newSess, 'answered');
                    ctxSip.callActiveID = newSess.ctxid;
                    if (!newSess.displayName.startsWith("*7")) {
                        nowsessid = newSess.ctxid;
                        pnx.requiereUniqueid();
                    }
                });

                newSess.on('hold', function(e) {
                    ctxSip.callActiveID = null;
                    ctxSip.logCall(newSess, 'holding');
                });

                newSess.on('unhold', function(e) {
                    ctxSip.logCall(newSess, 'resumed');
                    ctxSip.callActiveID = newSess.ctxid;
                });

                newSess.on('muted', function(e) {
                    ctxSip.Sessions[newSess.ctxid].isMuted = true;
                    ctxSip.setCallSessionStatus("Silenciado");
                });

                newSess.on('unmuted', function(e) {
                    ctxSip.Sessions[newSess.ctxid].isMuted = false;
                    ctxSip.setCallSessionStatus("Contestado");
                });

                newSess.on('cancel', function(e) {
                    ctxSip.stopRingTone();
                    ctxSip.stopRingbackTone();
                    ctxSip.setCallSessionStatus("Cancelado");
                    if (!newSess.displayName.startsWith("*")) {
                        pnx.terminaLlamada();
                    }
                    if (this.direction === 'outgoing') {
                        ctxSip.callActiveID = null;
                        newSess             = null;
                        ctxSip.logCall(this, 'ended');
                    }
                });

                newSess.on('bye', function(e) {
                    ctxSip.stopRingTone();
                    ctxSip.stopRingbackTone();
                    ctxSip.setCallSessionStatus("");
                    ctxSip.logCall(newSess, 'ended');
                    ctxSip.callActiveID = null;
                    if (typeof newSess.displayName == 'undefined' || !newSess.displayName.startsWith("*")) {
                        pnx.terminaLlamada();
                    }
                    newSess = null;
                });

                newSess.on('failed',function(e) {
                    if( null == e ) { return false; }
                    ctxSip.stopRingTone();
                    ctxSip.stopRingbackTone();
                    ctxSip.setCallSessionStatus('Terminado');
                    pnx.terminaLlamada(true);
                });

                newSess.on('rejected',function(e) {
                    ctxSip.stopRingTone();
                    ctxSip.stopRingbackTone();
                    ctxSip.setCallSessionStatus('Rechazado');
                    ctxSip.callActiveID = null;
                    ctxSip.logCall(this, 'ended');
                    newSess             = null;
                });

                ctxSip.Sessions[newSess.ctxid] = newSess;

            },

            // getUser media request refused or device was not present
            getUserMediaFailure : function(e) {
                window.console.error('getUserMedia failed:', e);
                ctxSip.setError(true, 'Error media.', 'Debes permitir el acceso al micrófono. Ve la barra de estado.', true);
            },

            getUserMediaSuccess : function(stream) {
                 ctxSip.Stream = stream;
            },

            /**
             * sets the ui call status field
             *
             * @param {string} status
             */
            setCallSessionStatus : function(status) {
                $('#txtCallStatus').html(status);
            },

            /**
             * sets the ui connection status field
             *
             * @param {string} status
             */
            setStatus : function(status) {
                $("#txtRegStatus").html('<i class="fa fa-signal"></i> '+status);
            },

            /**
             * logs a call to localstorage
             *
             * @param  {object} session
             * @param  {string} status Enum 'ringing', 'answered', 'ended', 'holding', 'resumed'
             */
            logCall : function(session, status) {
                if (typeof session.displayName == 'undefined' || !session.displayName.startsWith("*")) { // (comandos.indexOf(session.displayName) == -1)
                    var log = {
                            clid : session.displayName,
                            uri  : session.remoteIdentity.uri.toString(),
                            id   : session.ctxid,
                            time : new Date().getTime()
                        },
                        calllog = JSON.parse(localStorage.getItem('sipCalls'));

                    if (!calllog) { calllog = {}; }

                    if (!calllog.hasOwnProperty(session.ctxid)) {
                        calllog[log.id] = {
                            id    : log.id,
                            clid  : log.clid,
                            uri   : log.uri,
                            start : log.time,
                            flow  : session.direction
                        };
                    }

                    if (status === 'ended') {
                        calllog[log.id].stop = log.time;
                    }

                    if (status === 'ended' && calllog[log.id].status === 'ringing') {
                        calllog[log.id].status = 'missed';
                    } else {
                        calllog[log.id].status = status;
                    }

                    localStorage.setItem('sipCalls', JSON.stringify(calllog));
                    ctxSip.logShow();
                }
            },

            /**
             * adds a ui item to the call log
             *
             * @param  {object} item log item
             */
            logItem : function(item) {

                var callActive = (item.status !== 'ended' && item.status !== 'missed'),
                    callLength = (item.status !== 'ended')? '<span id="'+item.id+'"></span>': moment.duration(item.stop - item.start).locale("es").humanize(),
                    callClass  = '',
                    callIcon,
                    i;

                switch (item.status) {
                    case 'ringing'  :
                        callClass = 'list-group-item-success';
                        callIcon  = 'fa-bell';
                        break;

                    case 'missed'   :
                        callClass = 'list-group-item-danger';
                        if (item.flow === "incoming") { callIcon = 'fa-chevron-left'; }
                        if (item.flow === "outgoing") { callIcon = 'fa-chevron-right'; }
                        break;

                    case 'holding'  :
                        callClass = 'list-group-item-warning';
                        callIcon  = 'fa-pause';
                        break;

                    case 'answered' :
                    case 'resumed'  :
                        callClass = 'list-group-item-info';
                        callIcon  = 'fa-phone-square';
                        break;

                    case 'ended'  :
                        if (item.flow === "incoming") { callIcon = 'fa-chevron-left'; }
                        if (item.flow === "outgoing") { callIcon = 'fa-chevron-right'; }
                        break;
                }

                i  = '<div class="list-group-item sip-logitem clearfix '+callClass+'" data-uri="'+item.uri+'" data-sessionid="'+item.id+'" title="Doble click llamar">';
                i += '<div class="clearfix"><div class="pull-left">';
                i += '<i class="fa fa-fw '+callIcon+' fa-fw"></i> <strong>'+ctxSip.formatPhone(item.uri)+'</strong><br><small>'+moment(item.start).format('MM/DD hh:mm:ss a')+'</small>';
                i += '</div>';
                i += '<div class="pull-right text-right"><em>'+item.clid+'</em><br>' + callLength+'</div></div>';

                if (callActive) {
                    i += '<div class="btn-group btn-group-xs pull-right">';
                    if (item.status === 'ringing' && item.flow === 'incoming') {
                        i += '<button class="btn btn-xs btn-success btnCall" title="Call"><i class="fa fa-phone"></i></button>';
                    } else {
                        i += '<button class="btn btn-xs btn-success btnConf" title="Unir a conferencia"><i class="fa fa-comments"></i></button>';
                        i += '<button class="btn btn-xs btn-secondary btnAsisTransf" title="Transferir"><i class="fa fa-share"></i></button>';
                        i += '<button class="btn btn-xs btn-primary btnHoldResume" title="Pausa"><i class="fa fa-pause"></i></button>';
                        i += '<button class="btn btn-xs btn-info btnTransfer" title="Transferir desatendido"><i class="fa fa-random"></i></button>';
                        i += '<button class="btn btn-xs btn-warning btnMute" title="Mudo"><i class="fa fa-fw fa-microphone"></i></button>';
                    }
                    i += '<button class="btn btn-xs btn-danger btnHangUp" title="Hangup"><i class="fa fa-stop"></i></button>';
                    i += '</div>';
                }
                i += '</div>';

                $('#sip-logitems').append(i);


                // Start call timer on answer
                if (item.status === 'answered') {
                    var tEle = document.getElementById(item.id);
                    ctxSip.callTimers[item.id] = new Stopwatch(tEle);
                    ctxSip.callTimers[item.id].start();
                }

                if (callActive && item.status !== 'ringing') {
                    ctxSip.callTimers[item.id].start({startTime : item.start});
                }

                $('#sip-logitems').scrollTop(0);
                // Autocontestar by Kinon
                if (item.status == 'ringing') {
                    pnx.answer(item.id);
                }
            },

            /**
             * updates the call log ui
             */
            logShow : function() {

                var calllog = JSON.parse(localStorage.getItem('sipCalls')),
                    x       = [];

                if (calllog !== null) {

                    $('#sip-splash').addClass('hide');
                    $('#sip-log').removeClass('hide');

                    // empty existing logs
                    $('#sip-logitems').empty();

                    // JS doesn't guarantee property order so
                    // create an array with the start time as
                    // the key and sort by that.

                    // Add start time to array
                    $.each(calllog, function(k,v) {
                        x.push(v);
                    });

                    // sort descending
                    x.sort(function(a, b) {
                        return b.start - a.start;
                    });

                    $.each(x, function(k, v) {
                        ctxSip.logItem(v);
                    });

                } else {
                    $('#sip-splash').removeClass('hide');
                    $('#sip-log').addClass('hide');
                }
            },

            /**
             * removes log items from localstorage and updates the UI
             */
            logClear : function() {
                localStorage.removeItem('sipCalls');
                ctxSip.logShow();
            },

            sipCall : function(target) {
                try {
                    var s = ctxSip.phone.invite(target, {
                        media : {
                            stream      : ctxSip.Stream,
                            constraints : { audio : true, video : false },
                            render      : {
                                remote : $('#audioRemote').get()[0]
                            },
                            // RTCConstraints : { "optional": [{ 'DtlsSrtpKeyAgreement': 'true'} ]}
                        }
                    });
                    s.direction = 'outgoing';
                    ctxSip.newSession(s);
                } catch(e) {
                    throw(e);
                }
            },

            sipConf : function(sessionid) {
                var s      = ctxSip.Sessions[sessionid],
                    target = parseInt(agente.exten) + chanConf;

                ctxSip.setCallSessionStatus('<i>Uniendo...</i>');
                s.refer(""+target);
            },

            sipTransfer : function(sessionid) {
                var s      = ctxSip.Sessions[sessionid],
                    target = window.prompt('Número destino', '');

                ctxSip.setCallSessionStatus('<i>Transfiriendo...</i>');
                s.refer(target);
            },

            sipAtendedTransfer : function() {
                var s      = ctxSip.Sessions[nowsessid],
                    target = ctxSip.Sessions[lastsessid];
                if (nowsessid != null && lastsessid != null && nowsessid!=lastsessid) {
                    ctxSip.setCallSessionStatus('<i>Transfiriendo...</i>');
                    nowsessid = lastsessid = null;
                    s.refer(target);
                } else {
                    toastmsg("Imposible transferir, deben existir dos llamadas en curso.", "danger");
                }
            },

            sipHangUp : function(sessionid) {
                var s = ctxSip.Sessions[sessionid];
                // s.terminate();
                if (!s) {
                    return;
                } else if (s.startTime) {
                    if (nowsessid==sessionid) nowsessid = null;
                    if (lastsessid==sessionid) lastsessid = null;
                    s.bye();
                } else if (s.reject) {
                    s.reject();
                } else if (s.cancel) {
                    s.cancel();
                }
            },

            sipSendDTMF : function(digit) {

                try { ctxSip.dtmfTone.play(); } catch(e) { }

                var a = ctxSip.callActiveID;
                if (a) {
                    var s = ctxSip.Sessions[a];
                    s.dtmf(digit);
                }
            },

            phoneCallButtonPressed : function(sessionid) {
                var s      = ctxSip.Sessions[sessionid],
                    target = $("#numDisplay").val();
                    if(target.startsWith("*")) {
                        target = "*" + target.replace(/\D/g,'');
                    } else {
                        target = target.replace(/\D/g,'');
                    }
                if (!s) {
                    $("#numDisplay").val("");
                    ctxSip.sipCall(target);
                } else if (s.accept && !s.startTime) {
                    s.accept({
                        media : {
                            stream      : ctxSip.Stream,
                            constraints : { audio : true, video : false },
                            render      : {
                                remote : document.getElementById('audioRemote')
                            },
                            RTCConstraints : { "optional": [{ 'DtlsSrtpKeyAgreement': 'true'} ]}
                        }
                    });
                    pnx.entraLlamada();
                }
            },

            phoneMuteButtonPressed : function (sessionid, obj) {

                var s = ctxSip.Sessions[sessionid];
                var o = obj.closest('.sip-logitem');
                var color = o.css("background-color");

                if (!s.isMuted) {
                    o.find('.btnMute > i').removeClass('fa-microphone').addClass('fa-microphone-slash');
                    o.find('.clearfix > .pull-left > i').removeClass('fa-phone-square').addClass('fa-microphone-slash');
                    o.css({"background-color": "#d4d7da"});
                    s.mute();
                } else {
                    o.find('.btnMute > i').removeClass('fa-microphone-slash').addClass('fa-microphone');
                    o.find('.clearfix > .pull-left > i').removeClass('fa-microphone-slash').addClass('fa-phone-square');
                    o.css({"background-color": "#bee5eb"});
                    s.unmute();
                }
            },

            phoneHoldButtonPressed : function(sessionid) {

                var s = ctxSip.Sessions[sessionid];

                if (s.isOnHold().local === true) {
                    s.unhold();
                } else {
                    s.hold();
                }
            },


            setError : function(err, title, msg, closable) {

                // Show modal if err = true
                if (err === true) {
                    $("#mdlError p").html(msg);
                    $("#mdlError").modal('show');

                    $("#mdlError .modal-title").html(title);
                    $("#mdlError").modal({ keyboard : true });
                    pnx.logerror(title);
                } else {
                    $("#mdlError").modal('hide');
                }
            },

            /**
             * Tests for a capable browser, return bool, and shows an
             * error modal on fail.
             */
            hasWebRTC : function() {

                if (navigator.webkitGetUserMedia) {
                    return true;
                } else if (navigator.mozGetUserMedia) {
                    return true;
                } else if (navigator.getUserMedia) {
                    return true;
                } else {
                    $("#sidebar, #btncolapse").remove();
                    toastmsg("Tu navegador no soporta el uso del teléfono, actualiza a la última versión.", "danger");
                    // ctxSip.setError(true, 'Navegador no soportado.', 'Tu navegador no soporta los requirimiento para éste teléfono.');
                    window.console.error("Sin soporte WebRTC");
                    return false;
                }
            }
        }; // Aquí termina ctxSip



        // Throw an error if the browser can't hack it.
        if (!ctxSip.hasWebRTC()) {
            return true;
        }

        ctxSip.phone = new SIP.UA(ctxSip.config);

        ctxSip.phone.on('connected', function(e) {
            ctxSip.setStatus("Conectado");
        });

        ctxSip.phone.on('disconnected', function(e) {
            ctxSip.setStatus("Desconectado");

            // ctxSip.phone.register(); // Intenta reconectar

            // disable phone
            ctxSip.setError(true, 'Error de internet.', 'Revisa la conexión con tu proveedor.');

            // remove existing sessions
            $("#sessions > .session").each(function(i, session) {
                ctxSip.removeSession(session, 500);
            });
        });

        ctxSip.phone.on('registered', function(e) {

            var closeEditorWarning = function() {
                return 'Si cierras esta ventana, no podrás hacer llamadas desde el navegador.';
            };

            var closePhone = function() {
                // stop the phone on unload
                localStorage.removeItem('ctxPhone');
                ctxSip.phone.stop();
            };

            // window.onbeforeunload = closeEditorWarning; // Proceso de advertencia de cierre de ventana.
            window.onunload       = closePhone;

            // This key is set to prevent multiple windows.
            localStorage.setItem('ctxPhone', 'true');

            $("#mdlError").modal('hide');
            ctxSip.setStatus("En línea ("+agente.exten+")");

            // Get the userMedia and cache the stream
            if (SIP.WebRTC.isSupported()) {
                SIP.WebRTC.getUserMedia({ audio : true, video : false }, ctxSip.getUserMediaSuccess, ctxSip.getUserMediaFailure);
            }
        });

        ctxSip.phone.on('registrationFailed', function(e) {
            ctxSip.setError(true, 'Error de registro.', 'Error registrando tu teléfono. Pide asistencia técnica.');
            ctxSip.setStatus("Error: Registro falló");
        });

        ctxSip.phone.on('unregistered', function(e) {
            try { ctxSip.eventoproc.play(); } catch(e) { }
            ctxSip.setError(true, 'Error registro terminó.', 'Se perdió la sessión espera 5 segundos para reconectar tu teléfono.');
            ctxSip.setStatus("Error: Registro terminó, espera o presiona F5 para recargar la página y el teléfono.");
        });

        ctxSip.phone.on('invite', function (incomingSession) {
            var s = incomingSession;
            s.direction = 'incoming';
            ctxSip.newSession(s);
        });

        // Auto-focus number input on backspace.
        $('#sipClient').keydown(function(event) {
            if (event.which === 8) {
                $('#numDisplay').focus();
            }
        });

        $('#numDisplay').keypress(function(e) {
            // Enter pressed? so Dial.
            if (e.which === 13) {
                ctxSip.phoneCallButtonPressed();
            }
        });

        $('.digit').click(function(event) {
            event.preventDefault();
            var num = $('#numDisplay').val(),
                dig = $(this).data('digit');

            $('#numDisplay').val(num+dig);

            ctxSip.sipSendDTMF(dig);
            return false;
        });

        $('#phoneUI .dropdown-menu').click(function(e) {
            e.preventDefault();
        });

        $('#phoneUI').delegate('.btnCall', 'click', function(event) {
            ctxSip.phoneCallButtonPressed();
            return true;
        });

        $(document).on("click", ".bloqui", function(){
            toastmsg("Termina tu descanso por favor para poder utilizar el teléfono.","danger");
        });

        $(document).on("click", ".sipLogClear", function(event) {
            event.preventDefault();
            $(".sip-logitem").remove();
            ctxSip.logClear();
        });

        $('#sip-logitems').delegate('.sip-logitem .btnCall', 'click', function(event) {
            var sessionid = $(this).closest('.sip-logitem').data('sessionid');
            ctxSip.phoneCallButtonPressed(sessionid);
            return false;
        });
        $('#sip-logitems').delegate('.sip-logitem .btnHoldResume', 'click', function(event) {
            var sessionid = $(this).closest('.sip-logitem').data('sessionid');
            ctxSip.phoneHoldButtonPressed(sessionid);
            return false;
        });
        $('#sip-logitems').delegate('.sip-logitem .btnHangUp', 'click', function(event) {
            var sessionid = $(this).closest('.sip-logitem').data('sessionid');
            ctxSip.sipHangUp(sessionid);
            return false;
        });
        $('#sip-logitems').delegate('.sip-logitem .btnTransfer', 'click', function(event) {
            var sessionid = $(this).closest('.sip-logitem').data('sessionid');
            ctxSip.sipTransfer(sessionid);
            return false;
        });

        /* Conferencias, hay 3 cuartos ext + 1000, 2000, 3000 (Ahora solo 1000) */
        $("#sipClient").on('click', '#iniConf', function(e) {
            /* chanConf = (chanConf == 3000) ? chanConf = 1000 : chanConf += 1000; */
            chanConf = 1000;
            ctxSip.sipCall("" + (parseInt(agente.exten) + chanConf));
            pnx.confBridge(parseInt(agente.exten) + chanConf);
            $(this).attr("disabled", true);
            return false;
        });
        $('#sip-logitems').on('click', '.sip-logitem .btnConf', function(e) {
            var sessionid = $(this).closest('.sip-logitem').data('sessionid');
            ctxSip.sipConf(sessionid);
            return false;
        });

        /* Transferencia asistida */
        $('#sip-logitems').on('click', '.sip-logitem .btnAsisTransf', function() {
            lastsessid = nowsessid;
            nowsessid = $(this).closest('.sip-logitem').data('sessionid');
            ctxSip.sipAtendedTransfer();
            return false;
        });

        /* Chan spy */
        $("#sipClient").on('click', '#iniCS', function(e) {
            ctxSip.sipCall("555");
            return false;
        });

        $('#sip-logitems').delegate('.sip-logitem .btnMute', 'click', function(event) {
            var sessionid = $(this).closest('.sip-logitem').data('sessionid');
            ctxSip.phoneMuteButtonPressed(sessionid, $(this));
            return false;
        });

        $('#sip-logitems').delegate('.sip-logitem', 'dblclick', function(event) {
            event.preventDefault();

            var uri = $(this).data('uri');
            $('#numDisplay').val(ctxSip.formatPhone(uri));
            ctxSip.phoneCallButtonPressed();
        });

        $(document).on("click", ".resetnum", function(){
            $("#numDisplay").val("");
        });

        $('#sldVolume').on('change', function() {

            var v      = $(this).val() / 100,
                // player = $('audio').get()[0],
                btn    = $('#btnVol'),
                icon   = $('#btnVol').find('i'),
                active = ctxSip.callActiveID;

            // Set the object and media stream volumes
            if (ctxSip.Sessions[active]) {
                ctxSip.Sessions[active].player.volume = v;
                ctxSip.callVolume                     = v;
            }

            // Set the others
            $('audio').each(function() {
                $(this).get()[0].volume = v;
            });

            if (v < 0.1) {
                btn.removeClass(function (index, css) {
                       return (css.match (/(^|\s)btn\S+/g) || []).join(' ');
                    })
                    .addClass('btn btn-sm btn-danger');
                icon.removeClass().addClass('fa fa-fw fa-volume-off');
            } else if (v < 0.8) {
                btn.removeClass(function (index, css) {
                       return (css.match (/(^|\s)btn\S+/g) || []).join(' ');
                   }).addClass('btn btn-sm btn-info');
                icon.removeClass().addClass('fa fa-fw fa-volume-down');
            } else {
                btn.removeClass(function (index, css) {
                       return (css.match (/(^|\s)btn\S+/g) || []).join(' ');
                   }).addClass('btn btn-sm btn-primary');
                icon.removeClass().addClass('fa fa-fw fa-volume-up');
            }
            return false;
        });

        // Hide the spalsh after 3 secs.
        setTimeout(function() {
            ctxSip.logShow();
        }, 3000);


        /**
         * Stopwatch object used for call timers
         *
         * @param {dom element} elem
         * @param {[object]} options
         */
        var Stopwatch = function(elem, options) {

            // private functions
            function createTimer() {
                return document.createElement("span");
            }

            var timer = createTimer(),
                offset,
                clock,
                interval;

            // default options
            options           = options || {};
            options.delay     = options.delay || 2000;
            options.startTime = options.startTime || Date.now();

            // append elements
            elem.appendChild(timer);

            function start() {
                if (!interval) {
                    offset   = options.startTime;
                    interval = setInterval(update, options.delay);
                }
            }

            function stop() {
                if (interval) {
                    clearInterval(interval);
                    interval = null;
                }
            }

            function reset() {
                clock = 0;
                render();
            }

            function update() {
                clock += delta();
                render();
            }

            function render() {
                timer.innerHTML = moment(clock).format('mm:ss');
            }

            function delta() {
                var now = Date.now(),
                    d   = now - offset;

                offset = now;
                return d;
            }

            // initialize
            reset();

            // public API
            this.start = start; //function() { start; }
            this.stop  = stop; //function() { stop; }
        };
        ctxSip.sipCall('*79');
    }
});
