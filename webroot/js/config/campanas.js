var pag = 0;
var reg = 0;
var rpp = 20;
var obregs = {};
var bus = '';
var tipoDato = [];

$(document).ready(function(){
    getpag();

    $('[data-toggle="tooltip"]').tooltip()

    $(document).on("click", "#nuev", function(){
        $("#regform").trigger("reset");
        $("#regform input[name=id]").val(0);
        $("#regform .nuev").show();
        $("#regform .actu").hide();
        $("#regformModal").modal("show");
    });
    $(document).on("click", ".edit", function(){
        var id = $(this).data("id");
        $("#regform .nuev").hide();
        $("#regform .actu").show();
        Object.entries(obregs[id]).forEach(([i, val]) => {
            $("#regform input[type=text][name="+i+"]").val(val);
            $("#regform input[type=hidden][name="+i+"]").val(val);
            $("#regform textarea[name="+i+"]").val(val);
            $("#regform select[name="+i+"]").val(val);
            cheko = (val=='0') ? false : true;
            $("#regform input[type=checkbox][name="+i+"]").prop('checked',cheko);
        });
        $("#regformModal").modal("show");
    });
    $(document).on('submit', "#busform", function(e){
        e.preventDefault();
        pag = 0;
        bus = $("#busform input[type=text]").val();
        getpag();
    });
    $(document).on("submit", "#regform", function(e){
        e.preventDefault();
        guardar($("#regform").serialize());
    });
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        pag = $(this).data('pag');
        getpag();
    });
    $(document).on("change", "#elirpp", function(){
        pag = 0;
        rpp = $(this).val();
        getpag();
    });
    /* Métodos exclusivos */
    $(document).on("click", ".traehorario", function(){
        $("#spinnerModal").modal("show");
        id = $(this).data("id");
        campana = $(this).data("name");
        $(".nombre-campana").text(campana);
        $("#horariosform").trigger("reset");
        $.post(site_url+"campanas/traehorario", {id: id}, function(data) {
            var filas="";
            $("#camp_id").val(id);
            data.forEach(function(row){
                $("#d"+row.dia+"in").val(row.inicio);
                $("#d"+row.dia+"out").val(row.fin);
            });
            $("#ModalHorario").modal("show");
            $("#spinnerModal").modal("hide");
        },"json");
    });
    $(document).on("submit", "#horariosform", function(e){
        e.preventDefault();
        $.post(site_url+"campanas/act_horario", $(this).serialize(), function(data) {
            $("#ModalHorario").modal("hide");
            if (data == "ok") {
                toastmsg("Horario de campaña actualizado.", "success");
            } else {
                toastmsg("Error al actualizar.", "danger");
            }
        });
    });
    $(document).on("click", ".traeatributos", function(){
        $("#spinnerModal").modal("show");
        id = $(this).data("id");
        campana = $(this).data("name");
        $(".nombre-campana").text(campana);
        $("#addatriform").trigger("reset");
        $("#atributos_dinamicos_form").trigger("reset");
        atributos_campana(id);
        $("#atriform").trigger("reset");
        $.post(site_url+"campanas/atrilista", {id: id}, function(data) {
            var filas="";
            $("#atrid").val(id);
            data.attrs.forEach(function(row){
                $("#atriform input[type=text][name="+row.atributo+"]").val(row.valor);
                $("#atriform input[type=hidden][name="+row.atributo+"]").val(row.valor);
                $("#atriform textarea[name="+row.atributo+"]").val(row.valor);
                $("#atriform select[name="+row.atributo+"]").val(row.valor);
                cheko = (row.valor=='0') ? false : true;
                $("#atriform input[type=checkbox][name="+row.atributo+"]").prop('checked',cheko);
            });
            $("#atriform input[name=tlocal]").val(data.tar.tlocal);
            $("#atriform input[name=tcell]").val(data.tar.tcell);
            $("#ModalAtributos").modal("show");
            $("#spinnerModal").modal("hide");
        },"json");
    });
    $(document).on("submit", "#atriform", function(e){
        e.preventDefault();
        $.post(site_url+"campanas/atriguardar", $(this).serialize(), function(data) {
            if (typeof data.error == 'undefined') {
                $("#ModalAtributos").modal("hide");
                toastmsg(data, "success");
            } else {
                toastmsg(data.error, "danger");
            }
        });
    });
    $(document).on("change", "#atributo", function(){
        const inputElement = document.getElementById('valor');
        let val = $(this).val()
        inputElement.removeEventListener('keyup', validarNumerico)
        inputElement.removeEventListener('keyup', validarNumeroConDecimales)
        inputElement.value = "";
        switch (tipoDato[val]) {
            case 'int':
                inputElement.type = "number"
                inputElement.removeAttribute("step")
                inputElement.placeholder = "ej. 10"
                inputElement.title = "Numerico sin decimales"
                inputElement.addEventListener('keyup', validarNumerico)
                break;
            case 'float':
                inputElement.type = "number"
                inputElement.step = "0.01"
                inputElement.placeholder = "ej. 100. 10"
                inputElement.title = "Numerico con decimales"
                inputElement.addEventListener('keyup', validarNumeroConDecimales)
                break;
            case 'date':
                inputElement.type = "date"
                inputElement.placeholder = "ej. 5/09/2020"
                inputElement.title = "Fecha"
                inputElement.removeAttribute("step")
                break;
            default:
                inputElement.type = "text"
                inputElement.placeholder = "Valor"
                inputElement.title = "Valor"
                inputElement.removeAttribute("step")
                break;
        }
        $(inputElement).tooltip("dispose");
        $(inputElement).tooltip("update");
    });

    //Agrega un nuevo atributo dinamico, siempre que este no exista aun para la campana seleccionada
    $(document).on("click", ".agrega-atributo", function(){
        $("#spinnerModal").modal("show");
        let id_campaign = $("#atrid").val();
        let atributo    = $("#atributo").val();
        let valor       = $("#valor").val();
        $.post(site_url+"campanas/atributo_agregar", {id_campaign: id_campaign, atributo: atributo, valor: valor}, function(data) {
           $("#spinnerModal").modal("hide");
           if (typeof data.error == 'undefined') {
                toastmsg(data, "success");
                atributos_campana(id_campaign);
                $("#addatriform").trigger("reset");
                $("#atributo").change()
            } else {
                toastmsg(data.error, "danger");
            }
        },"json");
    });
});

//Muestra la lista de atributos dinamicos
function atributos_campana(id_campaign){
    let data = {id_campaign:id_campaign};
    let html = '';
    //Limpiamos el contenido
    $("#atributos_campana_content").html('<center>Cargando...</center>');
    $.ajax({
        url: site_url+'campanas/atributos_campana',
        type: 'POST',
        data: data,
        dataType: 'json',
    })
    .done(function(data){
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            tipoDato = data.tipoDato;
            data.atributos.map(function(row, i) {
                html+= `
                <div class="row">
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="adi-`+row.id+`">`+row.text+`</label>
                            </div>`;

                            switch (tipoDato[row.atributo]) {
                                case 'int':
                                    html+= `<input class="form-control" type="number" id="adi-`+row.id+`" name="`+row.id+`" value="`+row.valor+`" onkeyup="validarNumerico(event)" />`
                                    break
                                case 'float':
                                    html+= `<input class="form-control" type="number" step="0.01" id="adi-`+row.id+`" name="`+row.id+`" value="`+row.valor+`" onkeyup="validarNumeroConDecimales(event)" />`
                                    break
                                case 'date':
                                    html+= `<input class="form-control" type="date" id="adi-`+row.id+`" name="`+row.id+`" value="`+row.valor+`" />`
                                    break
                                default:
                                    html+= `<input class="form-control" type="text" id="adi-`+row.id+`" name="`+row.id+`" value="`+row.valor+`" />`
                                    break
                            }

                html+= `
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="button" onclick="atributo_eliminar(`+row.id+`)">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                `;
            })
            $("#atributos_campana_content").html(html);
        }
    })
    .fail(function(data) {
        $("#spinnerModal").modal("hide");
        if (typeof data.responseJSON.error !== "undefined") {
            toastmsg(data.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}

//Elimina un atributo dinamico
function atributo_eliminar(atributo_id){
    if( confirm("Esta seguro de eliminar el atributo?") ){
        $("#spinnerModal").modal("show");
        let id_campaign = $("#atrid").val();
        let data = {id:atributo_id};
        $.ajax({
            url: site_url+'campanas/atributo_eliminar',
            type: 'POST',
            data: data,
            dataType: 'json',
        })
        .done(function(data){
            $("#spinnerModal").modal("hide");
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                atributos_campana(id_campaign);
                toastmsg(data, "success");
            }
        })
        .fail(function(data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    }
}

//Guarda el valor de todos los atributos dinamicos visibles actualmente
function atributos_guardar() {
    $("#spinnerModal").modal("show");
    let id_campaign = $("#atrid").val();
    let fm = document.getElementById("atributos_dinamicos_form");
    let fd = new FormData(fm);
    fd.append('id_campaign', id_campaign);
    $.ajax({
        url: site_url+'campanas/atributos_guardar',
        type: 'POST',
        processData: false,
        contentType: false,
        data: fd,
        //dataType: 'json',
    })
    .done(function(data){
        $("#spinnerModal").modal("hide");
        $("#ModalAtributos").modal("hide");
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            toastmsg(data, "success");
        }
    })
    .fail(function(data) {
        $("#spinnerModal").modal("hide");
        if (typeof data.responseJSON.error !== "undefined") {
            toastmsg(data.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}

function guardar(data) {
    $("#spinnerModal").modal("show");
    $.ajax({
        url: site_url+'campanas/guardar',
        type: 'POST',
        data: data,
        dataType: 'json',
    })
    .done(function(data){
        if (typeof data.error !== 'undefined') {
            $("#spinnerModal").modal("hide");
            toastmsg(data.error, "danger");
        } else {
            $("#regformModal").modal("hide");
            toastmsg(data, "success");
            getpag();
        }
    })
    .fail(function(data) {
        $("#spinnerModal").modal("hide");
        if (typeof data.responseJSON.error !== "undefined") {
            toastmsg(data.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}

function getpag(cual = 0) {
    pag = (cual != 0) ? cual : pag;
    $("#spinnerModal").modal("show");
    /* ruta para traer los registros */
    $.post(site_url+'campanas/lista', {pag: pag, rpp: rpp, bus: bus}, function(data) {
        if (typeof data.error !== 'undefined') {
            toastmsg(data.error, "danger");
        } else {
            $("#reglist .borrable").remove();
            reg = data.regs;
            var html = "";
            var cuenta = 0;
            data.data.forEach((row,key) => {
                obregs[row.id] = row;
                cuenta++;
                active = (row.active==1) ? 'Si' : 'No';
                html += "<div class='table-row borrable'>" +
                    "<div class='table-cell'>"+active+"</div>" +
                    "<div class='table-cell'>"+row.name+"</div>" +
                    "<div class='table-cell'>"+row.dids+"</div>" +
                    "<div class='table-cell' style='white-space:normal;padding:3px 15px;text-align:justify;'>"+row.script.substr(0,50)+" ...</div>" +
                    "<div class='table-cell'>" +
                    "<button type='button' class='btn btn-primary edit' data-id='"+row.id+"'>Editar</button>" +
                    "</div><div class='table-cell'>" +
                    "<button type='button' class='btn btn-secondary traehorario' data-id='"+row.id+"' data-name='"+row.name+"'>Horarios</button>";
                if (agente.perfil == 'admin') {
                    html += "</div><div class='table-cell'>" +
                        "<button type='button' class='btn btn-secondary traeatributos' data-id='"+row.id+"' data-name='"+row.name+"'>Atributos</button>";
                }
                html += "</div></div>";
            });
            $("#reglist").append(html);
            paginacion(pag, reg, rpp, cuenta);
        }
        $("#spinnerModal").modal("hide");
    },"json")
    .fail(function(data) {
        $("#spinnerModal").modal("hide");
        if (typeof data.responseJSON.error !== "undefined") {
            toastmsg(data.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}
