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
        var id  = $(this).data("id");
        var src = $(this).data("src");
        $("#escuchaudioAudio").html("");
        $("#audfecha").text("No audio");
        $("#audnumero, #audagente").text("");
        $.post(site_url+"ajax/tmpaudio", {src: $(this).data("src")}, function(data) {
            if (data == 'OK') {
                var sound      = document.createElement('audio');
                sound.id       = id;
                sound.controls = 'controls';
                sound.preload  = true;
                sound.autoplay = true;
                sound.src      = site_url+'files/'+src;
                $("#escuchaudioAudio").html(sound);
                $("#audfecha").text($("#fecha"+id).text());
                $("#audnumero").text($("#numero"+id).text());
                $("#audagente").text($("#agente"+id).text());
            } else {
                $("#escuchaudioAudio").html(data);
            }
        },"json");
    });

    $(document).on("click", ".lanzamodal", function(){
        $("#evaltotal").text("0");
        var id   = $(this).data("id");
        var cola = $(this).data("cola");
        var src  = $(this).data("src");
        $.post(site_url+"ajax/tmpaudio", {src: $(this).data("src")}, function(data) {
            if (data == 'OK') {
                var sound      = document.createElement('audio');
                sound.id       = id;
                sound.controls = 'controls';
                sound.preload  = true;
                sound.autoplay = true;
                sound.src      = site_url+'files/'+src;
                $("#m_fecha").text($("#fecha"+id).text());
                $("#m_numero").text($("#numero"+id).text());
                $("#m_agente").text($("#agente"+id).text());
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
