var repgen = {
    rpp: 20,
    dndc: ['id'], // Columnas que no quiero mostrar, Default no quiero el id
    valcols: function(data) {
        if (data.cbl) { // Lista negra de columnas para agregar a no mostrar
            data.cbl.forEach(function(val) {
                if (!repgen.dndc.includes(val)) repgen.dndc.push(val);
            });
        }
        if (data.cwl) { // Lista blanca de columnas para quitar de no mostrar
            data.cwl.forEach(function(val, i) {
                if (repgen.dndc.includes(val)) repgen.dndc.splice(i,1);
            });
        }
    },
    getpag: function(cual = 0) {
        $("#spinnerModal").modal("show");
        $("#repo, #paginacion").html("");
        $("#pag").val(cual);
        postdata = $("#repoform").serialize();
        postdata += "&rpp="+repgen.rpp;
        $.post(site_url+'reportes/data', postdata, function(data) {
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                if (data.data && data.data.length>0) {
                    if (typeof data.since !== 'undefined') { //SI EXISTE LA VARIABLE(OCUPADA EN VISTA DESPACHADOR DETALLE Y desp_model ) PUES ES UN CAMPO NUEVO QUE NO TODAS LAS TABLAS desp_# LO TIENEN
                        if( $("#filtro-since").length > 0 ){ //SI EXISTE EL FILTRO (ESTA ACTIVO DESDE EL CONTROLADOR)
                            if( data.since ) $("#filtro-since").removeClass("d-none") // SI, QUE SE MUESTRE
                            else $("#filtro-since").addClass("d-none") // NO, QUE SE OCULTE
                        }
                    }
                    repgen.valcols(data);
                    var html = "<div class='table table-striped'><div class='table-header-group'>";
                    data.campos.forEach(function(row, i){
                        if (!repgen.dndc.includes(row.toLowerCase())) {
                            html += "<div class='table-cell'>"+data.tits[i]+"</div>\n";
                        }
                    });
                    html += "</div>";
                    data.data.forEach(function(row,key) {
                        html += "<div class='table-row'>\n";
                        data.campos.forEach(function(row2){
                            let sem = "";
                            if (row2 == 'Semáforo' ) {
                                if (row['Semáforo'] == 'amarillo') {
                                    sem = "<span style='display:inline-block;background-color:#dedc00;color:#dedc00;width:20px;height:20px;border-radius:10px;text-align:center;'>O</span> ";
                                } else if (row['Semáforo'] == 'rojo') {
                                    sem = "<span style='display:inline-block;background-color:red;color:red;width:20px;height:20px;border-radius:10px;text-align:center;'>O</span> ";
                                } else {
                                    sem = "<span style='display:inline-block;background-color:green;color:green;width:20px;height:20px;border-radius:10px;text-align:center;'>O</span> ";
                                }
                            }
                            if (!repgen.dndc.includes(row2.toLowerCase())) {
                                let wrapclass = ("undefined" != typeof wrapable && wrapable.includes(row2.toLowerCase())) ? " wrapable" : "";
                                html += "<div class='table-cell" + wrapclass + "'>"+sem+row[row2]+"</div>\n";
                            }
                            sem = "";
                        })
                        html += "</div>\n";
                    });
                    //Imprimimos los totales
                    if(data.totales !== false && data.totales !== undefined){
                        html += "<div class='table-row totales'>\n";
                        for (key in data.totales) {
                            if (typeof data.totales[key] !== 'function') {
                                html += "<div class='table-cell font-weight-bold'>"+data.totales[key]+"</div>\n";
                            }
                        }
                        html += "</div>\n";
                    }
                    $("#repo").html(html);
                }
                paginacion(data.pag, data.cuenta, data.rpp, data.data.length, "paginacion", "repgen.getpag");
            }
            $("#spinnerModal").modal("hide");
        },"json")
        .fail(function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    }
}

$(document).ready(function() {
    repgen.getpag();

    function resetAnalysisModal() {
        $("#analysis_call_id, #analysis_call_type").val("");
        $("#analysis_fecha, #analysis_agente, #analysis_numero, #analysis_campana").text("");
        $("#analysis_status").removeClass("alert-danger alert-success alert-warning alert-info").addClass("alert-secondary").text("Cargando analisis...");
        $("#analysis_timing").text("");
        $("#analysis_audio, #analysis_sentiment_label, #analysis_sentiment_score, #analysis_sentiment_summary").html("");
        $("#analysis_transcription").val("");
        $("#analysis_transcription_meta").text("");
        $("#analysis_error").text("");
        $("#analysis_error_wrap").addClass("d-none");
        $("#analysis_request").addClass("d-none");
        $("#analysis_reprocess").addClass("d-none");
        $("#analysis_reprocess_hq").addClass("d-none");
        $("#analysis_reprocess_max").addClass("d-none");
    }

    function loadAnalysisAudio(src) {
        $("#analysis_audio").html("");
        if (!src) {
            return;
        }
        $.post(site_url + "ajax/tmpaudio", {src: src}, function(data) {
            if (data == 'OK') {
                var sound = document.createElement('audio');
                sound.controls = 'controls';
                sound.preload = true;
                sound.src = site_url + 'files/' + src;
                $("#analysis_audio").html(sound);
            } else {
                $("#analysis_audio").html(data);
            }
        }, "json");
    }

    function paintAnalysisResponse(response) {
        var status = response.processing_status || 'pendiente';
        $("#analysis_status").removeClass("alert-secondary alert-danger alert-success alert-warning alert-info");
        if (status === 'listo') {
            $("#analysis_status").addClass("alert-success");
        } else if (status === 'error') {
            $("#analysis_status").addClass("alert-danger");
        } else if (status === 'no_solicitado') {
            $("#analysis_status").addClass("alert-info");
        } else {
            $("#analysis_status").addClass("alert-warning");
        }
        $("#analysis_status").text(response.status_message || 'Estado no disponible.');
        if (response.processing_duration_seconds !== null && response.transcription_seconds_per_minute !== null) {
            $("#analysis_timing").text(
                'Tiempo de proceso: ' + response.processing_duration_seconds + ' s | Rendimiento: ' + response.transcription_seconds_per_minute + ' s por minuto de audio'
            );
        } else {
            $("#analysis_timing").text('');
        }
        $("#analysis_sentiment_label").text(response.sentiment_label || 'Sin dato');
        $("#analysis_sentiment_score").text(response.sentiment_score === null ? '' : 'Score: ' + response.sentiment_score);
        $("#analysis_sentiment_summary").text(response.sentiment_summary || '');
        $("#analysis_transcription").val(response.transcription_text || 'Sin transcripcion disponible.');
        if (response.transcription_length && response.transcription_length > 0) {
            $("#analysis_transcription_meta").text('Transcripcion completa cargada: ' + response.transcription_length + ' caracteres. Usa la barra lateral o agranda el cuadro si necesitas mas espacio.');
        } else {
            $("#analysis_transcription_meta").text('Sin transcripcion disponible.');
        }
        if (response.processing_error) {
            $("#analysis_error").text(response.processing_error);
            $("#analysis_error_wrap").removeClass("d-none");
        }
        if (response.can_request) {
            $("#analysis_request").removeClass("d-none");
        }
        if (response.can_reprocess) {
            $("#analysis_reprocess").removeClass("d-none");
        }
        if (response.can_reprocess_high_quality) {
            $("#analysis_reprocess_hq").removeClass("d-none");
        }
        if (response.can_reprocess_max_quality) {
            $("#analysis_reprocess_max").removeClass("d-none");
        }
        loadAnalysisAudio(response.grabacion);
    }
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
        onSelect: function(){
            repgen.getpag();
        }
    });

    $(document).on("change", "select", function() {
        if($(this).attr("id")=="elirpp") {
            $("#pag").val(0);
            repgen.rpp = $("#elirpp").val();
        }
        repgen.getpag();
    });

    $(document).on("keydown", "input.nosend", function(e){
        var keycode = (e.keyCode ? e.keyCode : (e.which ? e.which : e.key));
        if(keycode == 13) {
            e.preventDefault();
            repgen.getpag();
        }
    });

    $(document).on("click", ".page-link", function(e) {
        e.preventDefault();
        repgen.getpag($(this).data("pag"));
    });

    $(document).on("click", ".dinau", function(){
        var button = $(this);
        var id  = button.data("id");
        var src = button.data("src");
        $("#escuchaudioAudio").html("");
        $("#audfecha").text("No audio");
        $("#audnumero, #audagente, #audcampana, #audduracion, #audestatus").text("");
        $.post(site_url+"ajax/tmpaudio", {src: button.data("src")}, function(data) {
            if (data == 'OK') {
                var sound      = document.createElement('audio');
                sound.id       = id;
                sound.controls = 'controls';
                sound.preload  = true;
                sound.autoplay = true;
                sound.src      = site_url+'files/'+src;
                $("#escuchaudioAudio").html(sound);
                $("#audfecha").text(button.data("fecha") || $("#fecha"+id).text());
                $("#audnumero").text(button.data("numero") || $("#numero"+id).text());
                $("#audagente").text(button.data("agente") || $("#agente"+id).text());
                $("#audcampana").text(button.data("campana") || '');
                $("#audduracion").text(button.data("duracion") || '');
                $("#audestatus").text(button.data("estatus") || '');
            } else {
                $("#escuchaudioAudio").html(data);
            }
        },"json");
    });

    $(document).on("click", ".lanzamodal", function(){
        $("#evaltotal").text("0");
        var button = $(this);
        var id   = button.data("id");
        var cola = button.data("cola");
        var src  = button.data("src");
        $("#m_campana, #m_duracion, #m_estatus").text('');
        $.post(site_url+"ajax/tmpaudio", {src: button.data("src")}, function(data) {
            if (data == 'OK') {
                var sound      = document.createElement('audio');
                sound.id       = id;
                sound.controls = 'controls';
                sound.preload  = true;
                sound.autoplay = true;
                sound.src      = site_url+'files/'+src;
                $("#m_fecha").text(button.data("fecha") || $("#fecha"+id).text());
                $("#m_numero").text(button.data("numero") || $("#numero"+id).text());
                $("#m_agente").text(button.data("agente") || $("#agente"+id).text());
                $("#m_campana").text(button.data("campana") || cola || '');
                $("#m_duracion").text(button.data("duracion") || '');
                $("#m_estatus").text(button.data("estatus") || '');
                $("#grabacion").html(sound);
                $.post(site_url+"calidad/traercampos", {id: id, cola: cola}, function(data){
                    if (false == data) {
                        salida = "No hay formulario activo para ésta campaña.";
                    } else {
                        salida = "";
                        data.forEach(function(fila){
                            if (fila.question == 'Comentario') {
                                salida += '<tr>'+
                                    '<td>'+fila.question+'</td>'+
                                    '<td colspan="2">'+
                                        '<textarea name="'+fila.id+'" class="form-control" maxlength="600" rows="4"></textarea>'+
                                    '</td>'+
                                '</tr>';
                            } else {
                                salida += '<tr>'+
                                    '<td>'+fila.question+'</td>'+
                                    '<td>'+fila.weight+'</td>'+
                                    '<td>'+
                                        '<div class="custom-control custom-checkbox">'+
                                            '<input type="checkbox" class="custom-control-input evalpoint" value="'+fila.weight+
                                                '" id="customCheck'+fila.id+'" name="'+fila.id+'">'+
                                            '<label class="custom-control-label" for="customCheck'+fila.id+'"></label>'+
                                        '</div>'+
                                    '</td>'+
                                '</tr>';
                            }
                        });
                        $("#eval_id").val(id);
                    }
                    $("#calidadbody").html(salida);
                },"json");
            } else {
                $("#grabacion").html(data);
            }
        },"json");
    });

    $("#escuchaudio").on('hide.bs.modal', function() {
        var audio = $("#escuchaudio audio");
        if (typeof audio[0] != 'undefined') {
            audio[0].pause();
        }
    });

    $("#evalModal").on('hide.bs.modal', function() {
        var audio = $("#evalModal audio");
        if (typeof audio[0] != 'undefined') {
            audio[0].pause();
        }
    });

    $(document).on("click", ".evalpoint", function(){
        var prev = parseInt($("#evaltotal").text());
        var este = parseInt($(this).val());
        if ($(this).is(":checked")) {
            var total = prev + este;
        } else {
            var total = prev - este;
        }
        $("#evaltotal").text(total);
    });

    $(document).on("click", ".launch-analysis", function(){
        var button = $(this);
        resetAnalysisModal();
        $("#analysis_call_id").val(button.data("call-id"));
        $("#analysis_call_type").val(button.data("call-type"));
        $("#analysis_fecha").text(button.data("fecha"));
        $("#analysis_agente").text(button.data("agente"));
        $("#analysis_numero").text(button.data("numero"));
        $("#analysis_campana").text(button.data("campana"));

        $.post(site_url + "analisis/detalle", {
            call_id: button.data("call-id"),
            call_type: button.data("call-type")
        }, function(data) {
            if (!data.ok) {
                $("#analysis_status").removeClass("alert-secondary alert-success alert-warning").addClass("alert-danger").text(data.error || 'No fue posible consultar el analisis.');
                $("#analysis_error").text(data.error || 'Error no especificado.');
                $("#analysis_error_wrap").removeClass("d-none");
                loadAnalysisAudio(button.data("src"));
                return;
            }
            paintAnalysisResponse(data.data);
        }, "json").fail(function() {
            $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text("Error de red al consultar el analisis.");
            loadAnalysisAudio(button.data("src"));
        });
    });

    $(document).on("click", ".request-analysis", function(){
        var button = $(this);
        resetAnalysisModal();
        $("#analysis_call_id").val(button.data("call-id"));
        $("#analysis_call_type").val(button.data("call-type"));
        $("#analysis_fecha").text(button.data("fecha"));
        $("#analysis_agente").text(button.data("agente"));
        $("#analysis_numero").text(button.data("numero"));
        $("#analysis_campana").text(button.data("campana"));
        loadAnalysisAudio(button.data("src"));

        $("#analysis_status").removeClass("alert-secondary alert-danger alert-success alert-info").addClass("alert-warning").text("Encolando llamada para analisis...");
        $("#analysisModal").modal("show");

        $.post(site_url + "analisis/solicitar", {
            call_id: button.data("call-id"),
            call_type: button.data("call-type")
        }, function(data) {
            if (!data.ok) {
                $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text(data.error || 'No fue posible solicitar el analisis.');
                $("#analysis_error").text(data.error || 'Error no especificado.');
                $("#analysis_error_wrap").removeClass("d-none");
                return;
            }
            paintAnalysisResponse(data.data);
            repgen.getpag($("#pag").val() || 0);
        }, "json").fail(function() {
            $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text("Error de red al solicitar el analisis.");
        });
    });

    $(document).on("click", "#analysis_request", function(){
        var callId = $("#analysis_call_id").val();
        var callType = $("#analysis_call_type").val();
        if (!callId || !callType) {
            return;
        }

        $("#analysis_status").removeClass("alert-secondary alert-danger alert-success alert-info").addClass("alert-warning").text("Encolando llamada para analisis...");
        $.post(site_url + "analisis/solicitar", {
            call_id: callId,
            call_type: callType
        }, function(data) {
            if (!data.ok) {
                $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text(data.error || 'No fue posible solicitar el analisis.');
                return;
            }
            paintAnalysisResponse(data.data);
        }, "json").fail(function() {
            $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text("Error de red al solicitar el analisis.");
        });
    });

    $(document).on("click", "#analysis_reprocess", function(){
        var callId = $("#analysis_call_id").val();
        var callType = $("#analysis_call_type").val();
        if (!callId || !callType) {
            return;
        }

        $("#analysis_status").removeClass("alert-danger alert-success alert-info").addClass("alert-warning").text("Marcando llamada para reproceso...");
        $.post(site_url + "analisis/reprocesar", {
            call_id: callId,
            call_type: callType
        }, function(data) {
            if (!data.ok) {
                $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text(data.error || 'No fue posible reprocesar la llamada.');
                return;
            }
            paintAnalysisResponse(data.data);
        }, "json").fail(function() {
            $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text("Error de red al solicitar reproceso.");
        });
    });

    $(document).on("click", "#analysis_reprocess_hq", function(){
        var callId = $("#analysis_call_id").val();
        var callType = $("#analysis_call_type").val();
        if (!callId || !callType) {
            return;
        }

        $("#analysis_status").removeClass("alert-danger alert-success alert-info").addClass("alert-warning").text("Marcando llamada para reproceso en alta calidad...");
        $.post(site_url + "analisis/reprocesar_alta_calidad", {
            call_id: callId,
            call_type: callType
        }, function(data) {
            if (!data.ok) {
                $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text(data.error || 'No fue posible reprocesar la llamada en alta calidad.');
                return;
            }
            paintAnalysisResponse(data.data);
        }, "json").fail(function() {
            $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text("Error de red al solicitar reproceso en alta calidad.");
        });
    });

    $(document).on("click", "#analysis_reprocess_max", function(){
        var callId = $("#analysis_call_id").val();
        var callType = $("#analysis_call_type").val();
        if (!callId || !callType) {
            return;
        }

        $("#analysis_status").removeClass("alert-danger alert-success alert-info").addClass("alert-warning").text("Marcando llamada para reproceso en maxima calidad...");
        $.post(site_url + "analisis/reprocesar_maxima_calidad", {
            call_id: callId,
            call_type: callType
        }, function(data) {
            if (!data.ok) {
                $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text(data.error || 'No fue posible reprocesar la llamada en maxima calidad.');
                return;
            }
            paintAnalysisResponse(data.data);
        }, "json").fail(function() {
            $("#analysis_status").removeClass("alert-secondary alert-success alert-warning alert-info").addClass("alert-danger").text("Error de red al solicitar reproceso en maxima calidad.");
        });
    });

    $("#analysisModal").on('hide.bs.modal', function() {
        var audio = $("#analysisModal audio");
        if (typeof audio[0] != 'undefined') {
            audio[0].pause();
        }
    });

    $(".auco").each(function() {
        var that = $(this);
        var id   = that.attr("id");
        var mod  = that.data("mod");
        var met  = that.data("met");
        var dep  = that.data("dep");
        $(that).autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: site_url + "ajax/auco",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        mod: mod,
                        met: met,
                        bus: request.term,
                        dep: $("#"+dep).val(),
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $("#"+id+"val").val(ui.item.id);
                repgen.getpag();
            }
        });
    });

    $(".auco").on("focus", function(){
        var id = $(this).attr('id');
        $("#"+id+"val, #"+id).val('');
    });

});
