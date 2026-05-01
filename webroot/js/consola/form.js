$(document).ready(function(){
    $(document).on("submit", ".statform", function(e){
        e.preventDefault();
        $("#spinnerModal").modal("show");
        var ticket = $('.crm_form :input').not('.container_tbr :input').serializeArray();
        var dataToSend = {
            ticket: ticket,
            tbr: {
                data: crm_tbr.data,
                to_delete: crm_tbr.to_delete,
                to_update: crm_tbr.to_update
            },
        };
        var jsonData = JSON.stringify(dataToSend);
        //Pasamos el valor de la plantilla al formulario de statform
        let val_plantilla = $(".crm_form select[name=pl4n71ll4]").val();
        $(".statform input[name=pl4n71ll4]").val(val_plantilla)
        $.post(site_url+'crm/guardar', { jsonData: jsonData }, function(data){
            if (data.status == 'error') {
                $("#spinnerModal").modal("hide");
                toastmsg(data.msg, "danger");
            } else {
                valores = $(".statform").serialize();
                valaray = $(".statform").serializeArray();
                var vals = {};
                for (var i = 0; i < valaray.length; i++) {
                    vals[valaray[i].name] = valaray[i].value;
                }
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: site_url+'crm/guardarcali',
                    data: valores,
                    dataType: 'json'
                })
                .done(function(data){
                    $("#spinnerModal").modal("hide");
                    toastmsg(data.msg, data.status);
                    if (data.status != 'error') {
                        $("#leform").html("");
                        switch_view_forms('busqueda');
                        //Tap es la variable de crm-panel, en caso de estar habilitada, se debe de tomar en cuenta que el formulario se crea en un modelo
                        if(typeof tap !== 'undefined') {
                            $("#ticketModal").modal("hide");
                            tap.getickets();
                        }
                    }
                    if (vals['status'] != "Abierto") {
                        $("#ticketabierto_"+vals['id_form']+"_"+vals['id']).remove();
                        delete formsection.tickets[vals['id_form']][vals['id']];
                    }
                })
                .fail(function(data) {
                    $("#spinnerModal").modal("hide");
                    toastmsg("Error de comunicación.", "danger");
                });
            }
        },"json")
        .fail(function(){
            $("#spinnerModal").modal("hide");
            toastmsg("Error de comunicación.", "danger");
        });
    });
    $(document).on("change", ".tienedep", function(){
        formsection.traerdependencia($(this));
    });
    $(document).on("change", ".ddeepp", function(){
        let val = $(this).val();
        let ddep = $(this).data("ddep")
        let fid = $("input[name=id_form]").val();
        $.post(site_url+"form/ddeepp", {fid: fid, ddep: ddep, val: val}, function(resp){
            let data = resp.ddeepp;
            let dep_asig = resp.depasig;
            let activador = resp.activador;
            //Aplicamos los valores de los catalogos dependientes
            for (var campo in data) {
                if (data.hasOwnProperty(campo) && campo != activador) {
                    let config = ftr.rows.find(itemTemp => itemTemp.activator === campo);
                    if(typeof config !== 'undefined') {
                        ftr.apply(config, data[campo]);
                    }
                    $("[name="+campo+"]").val(data[campo]);
                }
            }
            //Reasignamos los valores
            if( data.n___rows == '1') {
                dep_asig.map(row => {//debe de traer datos a reasignar row.copia
                    if( row.activador == resp.activador ) {//debe pertenecer al activador seleccionado
                        if( row.copia == 'id_cliente' ) {
                            $("select[name=id_cliente]").val('');
                            //obtenemos el id del cliente atravez del nombre y formulario
                            if( data[row.campo] != '' ) {
                                $.post(site_url+"consola/get_client_form", { fid: fid, name: data[row.campo] }, function(resp){
                                    $("select[name=id_cliente]").val(resp);
                                },"json")
                                .fail(function(){
                                    toastmsg("Error de comunicación.", "danger");
                                });
                            }
                        } else {
                            $("[name="+row.copia+"]").val(data[row.campo]);
                        }
                    }
                });
            } else {
                toastmsg('Campo no encontrado', "danger");
            }
        },"json")
        .fail(function(){
            toastmsg("Error de comunicación.", "danger");
        });
    });
    $(document).on("submit", ".fileform", function(e) {
        e.preventDefault();
        $(".fileform button").attr("disabled", "disabled");
        var data = $(this).serializefiles();
        $.ajax({
            type: 'POST',
            method: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            url: site_url+'crm/guardararchivo',
            data: data,
            dataType: 'json'
        })
        .done(function(data){
            toastmsg(data.msg, data.status);
            if (data.status != 'error') $(".filetable").append(data.fila);
        })
        .fail(function(data) {
            toastmsg("Tipo de archivo no permitido.", "danger");
        });
        setTimeout(function () {
            $(".fileform button").removeAttr("disabled");
        }, 2500);
    });
    $(document).on("submit", ".crm_form", function(e){
        e.preventDefault();
        $(".crm_form button").attr("disabled", "disabled");
        // Variable con todos los elementos del formulario menos los del div con la clase container_tbr
        var ticket = $('.crm_form :input').not('.container_tbr :input').serializeArray();
        var dataToSend = {
            ticket: ticket,
            tbr: {
                data: crm_tbr.data,
            },
        };
        var jsonData = JSON.stringify(dataToSend);
        $("#leform").append("<i class='fas fa-spinner fa-2x fa-pulse'></i><span class='sr-only'>Cargando ...</span>");
        setTimeout(function () {
            $(".crm_form button").removeAttr("disabled");
        }, 2500);
        $.post(site_url+'crm/guardar', { jsonData: jsonData }, function(data){
            $("#leform .fa-spinner").remove();
            if (data.status == 'error') {
                toastmsg(data.msg, "danger");
            } else {
                toastmsg( data.msg, "success");
                switch_view_forms('busqueda')
            }
        },"json")
        .fail(function(){
            $("#leform .fa-spinner").remove();
            toastmsg("Error de comunicación.", "danger");
        });
    });
    $(document).on("change", ".calc-field-activator ", function(){
        let id_form_field_global = $(this).data("fcfid");
        $.post(site_url+"form/get_operations_fcf", {id_form_field_global: id_form_field_global}, function(resp){
            resp.map(row => {
                a = $(row.s_field_a).val();
                b = $(row.s_field_b).val();
                switch(row.operator) {
                    case "+": r = parseFloat(a)+parseFloat(b); break;
                    case "-": r = parseFloat(a)-parseFloat(b); break;
                    case "*": r = parseFloat(a)*parseFloat(b); break;
                    case "/": r = parseFloat(a)/parseFloat(b); break;
                    default:  r = parseFloat(a)+parseFloat(b); break;
                }
                $(row.s_field_r).val(r);
            });
        },"json")
        .fail(function(){
            toastmsg("Error de comunicación.", "danger");
        });
    });
    // $(document).on("click", ".formActBtn", function(){
    //     var form = $(this).closest("form").serialize();
    //     formsection.humanAction(form, $(this));
    // });
});

var formsection = {
    tickets: {},
    traerdependencia: function(element) {
        let tabla  = element.data("tabla");
        let parent = element.find(':selected').data('id');
        let name   = element.attr("name");
        let hijos  = $("select[data-parent="+name+"]");
        $.post(site_url+"form/getdep", {tabla, parent}, function(resp){
            hijos.each(function() {
                $(this).html(resp);
                if ($(this).hasClass("tienedep")) {
                    formsection.traerdependencia($(this));
                }
            });
        },"json")
        .fail(function(){
            toastmsg("Error de comunicación.", "danger");
        });
    },
    // humanAction: function(formdata, trigerel) {
    //     $.post(bago_url+"form/humanaction", {formdata}, function(resp){
    //         if (resp.status == 'error') {
    //             toastmsg(resp.msg, "danger");
    //         } else {
    //             toastmsg(resp.msg, "success");
    //             if (resp.action == 'reload') {
    //                 location.reload();
    //             }
    //         }
    //     },"json")
    //     .fail(function(){
    //         toastmsg("Error de comunicación.", "danger");
    //     });
    // },
    setTickets: function(data) {
        data.map(function(ticket) {
            if (typeof formsection.tickets[ticket.id_form] == 'undefined') {
                formsection.tickets[ticket.id_form] = {};
            }
            if (typeof formsection.tickets[ticket.id_form][ticket.id] == 'undefined' && ticket.estatus == 'Abierto') {
                formsection.tickets[ticket.id_form][ticket.id] = ticket;
                // convertir fecha a formato legible
                fecha = moment(ticket.apertrua).format('DD-MM-YYYY HH:mm:ss');
                var fila = `<div class="row" id="ticketabierto_${ticket.id_form}_${ticket.id}">${ticket.name}&nbsp;
                    <a href="#" class="ticketlink" data-cid="${ticket.id_campaign}" data-fid="${ticket.id_form}"
                    data-id="${ticket.id}"> (${ticket.estatus} ID: ${ticket.id}) </a>&nbsp;${fecha}&nbsp;
                    <a class="closeparentdiv" href="#">X</a></div>`;
                $("#ticketsabiertos").append(fila);
            } else if (typeof formsection.tickets[ticket.id_form][ticket.id] != 'undefined' && ticket.estatus != 'Abierto') {
                delete formsection.tickets[ticket.id_form][ticket.id];
                $("#ticketabierto_"+ticket.id_form+"_"+ticket.id).remove();
            }
        });
    }
}

// FILTROS
var ftr = {
    fid: 0,
    rows: [],
    // Obtiene la lista de todos los filtros para ese formulario
    getAll: () => {
        ftr.fid = $("#formIdForm").val()
        $.post(site_url+"form/ftr_list", {id_form: ftr.fid}, function(resp){
            ftr.rows = resp;
            ftr.analyzes();
        },"json")
        .fail(function(){
            toastmsg("Error de comunicación.", "danger");
        });
    },
    // Analiza si al cargar el formulario, uno de los activadores de filtros cambio o entro con un valor definido
    analyzes: () => {
        ftr.rows.map(function(row){
            let selector = $(`.crm_form :input[name=${row.activator}]`);
            let activator_value = $(selector).val();
            if( activator_value != '' ) {
                ftr.apply(row, activator_value);
            }
        });
    },
    // Applica el filtro segun la regla establecida
    apply: (row, string_bus) => {
        row['string_bus'] = string_bus;
        $.post(site_url+"form/ftr_ddeepp", row, function(resp){
            // Tenemos los datos, debemos de cambiar el elemento del formulario a un selec con esos datos
            ftr.filtros_catalogos(row, resp);
        },"json")
        .fail(function(){
            toastmsg("Error de comunicación.", "danger");
        });
    },
    // Convierte el input a un select filtrado 
    filtros_catalogos: (config, data) => {
        let oldElement = document.getElementById("f" + config.id_form + "_" + config.field_to_filter);
        //Respaldamos las clases
        let respClass = oldElement.className;
        //Respaldamos el valor de data-ddep
        let respDataDdep = oldElement.getAttribute('data-ddep');
        let dataDdep = ( respDataDdep == null ) ? '' : 'data-ddep="' + respDataDdep+'"';
        // Crea un nuevo elemento select
        var selectElement = $(`<select class="${respClass}" ${dataDdep} id="${config.field_to_filter}" name="${config.field_to_filter}"></select>`);
        selectElement.append('<option value="">Selecciona una opción</option>');
        data.map( (row) => {
            selectElement.append(`<option value="${row[config.field_to_filter]}">${row[config.field_to_filter]}</option>`);
        })
        // Reemplaza el input con el select creado
        $(`.crm_form :input[name=${config.field_to_filter}]`).replaceWith(selectElement);
    }
}

var crm_tbr = {
    data: {},
    form_fields: [],
    to_delete : {},
    to_update : {},
    headers: {},
    init: (fid, id) => {
        crm_tbr.data = {};
        crm_tbr.to_delete = {};
        crm_tbr.to_update = {};
        crm_tbr.headers = {};
        //Consultamos todos los datos de las tablas relacionadas y las listamos
        $.post(site_url+'form/getdatatbr', { fid: fid, id: id }, function(resp){
            let formatoSerializado = [];
            let data = [];
            crm_tbr.headers = resp.headers;
            //Recorremos los form_fields que tienen tablas relacionadas
            resp['ff_ids'].forEach(ff_id => {
                data = resp[ff_id];
                //****************** Cambiamos a formato serializeArray ******************
                formatoSerializado = [];
                for (let i = 0; i < data.length; i++) {
                    let objetoOriginal = data[i];
                    let rows = [];
                    for (let clave in objetoOriginal) {
                        if (objetoOriginal.hasOwnProperty(clave)) {
                            if( clave != 'id_formd' ) {
                                rows.push({ "name": clave, "value": objetoOriginal[clave] });
                            }
                        }
                    }
                    formatoSerializado.push(rows);
                }
                //****************** Cambiamos a formato serializeArray ******************
                crm_tbr.data[ff_id] = formatoSerializado;
                crm_tbr.reset(ff_id);
                crm_tbr.list(ff_id);
            });
        },"json")
        .fail(function(){
            $("#spinnerModal").modal("hide");
            toastmsg("Error de comunicación.", "danger");
        });
    },
    //Restaura los campos de un formulario a su estado normal desbloqueado
    restartFields: (id_tbr) => {
        // Recorremos todos los campos y los removemos el atributo readonly
        crm_tbr.form_fields.forEach(tabla => {
            if( tabla.id_form_field == id_tbr ) {
                tabla.data.forEach( row => {
                    if( row.editable == 0 ) {
                        $('.container_tbr[data-ntbr='+tabla.id_form_field+'] :input[name="'+row.slug+'"]').prop('disabled', false)
                    }
                });
            }
        });
    },
    reset: (id_tbr) => {
        $('.container_tbr[data-ntbr="'+id_tbr+'"] :input').each(function () {
            let tipo_elemento = $(this).prop('type');
            if ($(this).attr('name') === 'id') {
                $(this).val(0);
            } else {
                switch (tipo_elemento) {
                    case 'radio':
                        $(this).prop('checked',false);
                        $(this).prop('required',false);
                        break;
                    case 'checkbox':
                        $(this).prop('checked',false);
                        $(this).prop('required',false);
                        break;
                    default:
                        $(this).val("");
                        $(this).prop('required',false);
                        break;
                }
            }
        });
        crm_tbr.restartFields(id_tbr);
        //Ocultamos el boton de editar
        $('.container_tbr[data-ntbr='+id_tbr+'] button.btn-cancel').hide();
        $('.container_tbr[data-ntbr='+id_tbr+'] button.btn-update').hide();
        $('.container_tbr[data-ntbr='+id_tbr+'] button.btn-update').val('x');
        //Mostramos el boton de agregar
        $('.container_tbr[data-ntbr='+id_tbr+'] button.btn-add').show();
        //Habilitamos todos los botones de la tabla
        $('.container-tbr-data[data-tbr-id='+id_tbr+'] button.btn').attr("disabled", false);
    },
    //Agregamos un renglon al 
    add: (id_tbr) => {
        if (!crm_tbr.data[id_tbr]) {
            crm_tbr.data[id_tbr] = [];
        }
        let row = $('.container_tbr[data-ntbr="'+id_tbr+'"] :input').serializeArray();
        //Reestructuramos el row con todos los campos segun el header
        let rowNew = [];
        let success = true;
        rowNew.push({name: 'id', value: '0'});
        crm_tbr.headers.forEach(item => {
            if (success === true) {
                if( item.id_form_fields == id_tbr) { //Es un elemento de esa tabla relacionada, se evalua
                    rowTemp = row.find(itemTemp => itemTemp.name === item.slug);//obtenemos el renglon del nombre y valor del elemento del formulario serializado
                    valueTemp = rowTemp?.value ?? '';
                    switch (item.type) {
                        case 'checkbox':
                            valueTemp = (valueTemp == 'on') ? 1 : 0;
                            if( item.required == 1 && valueTemp == 0 ){
                                success = false;
                                toastmsg("Error el campo: "+item.name+" es requerido", "danger");
                                $('.container_tbr[data-ntbr="'+id_tbr+'"] :input[name='+item.slug+']').focus();
                                break;
                            }
                            break;
                        default:
                            if( item.required == 1 && valueTemp == 0 ){
                                success = false;
                                toastmsg("Error el campo: "+item.name+" es requerido", "danger");
                                $('.container_tbr[data-ntbr="'+id_tbr+'"] :input[name='+item.slug+']').focus();
                                break;
                            }
                            break;
                    }
                    rowNew.push({name: item.slug, value: valueTemp});
                }
            }
        });
        if( success === true ){
            crm_tbr.data[id_tbr].push(rowNew);
            crm_tbr.reset(id_tbr);
            crm_tbr.list(id_tbr);
        }
    },
    edit: (id_tbr, index) => {
        //Colocamos el index en el boton de actualizar
        $('.container_tbr[data-ntbr='+id_tbr+'] button.btn-update').val(index);
        //Recorremos el renglon y pegamos sus datos en cada elemento del formulario
        crm_tbr.data[id_tbr][index].map( (input) => {
            //obtenemos el elemento
            let elemento = $('.container_tbr[data-ntbr="'+id_tbr+'"] :input[name='+input.name+']'); 
            let tipo_elemento = elemento.prop('type');
            if( input.name ) {
                switch (tipo_elemento) {
                    case 'radio':
                        //Desmarcamos el elemento
                        $('.container_tbr[data-ntbr="'+id_tbr+'"] :input[name='+input.name+']').prop('checked', false);
                        //Selector para ese elemento con el valor cargado
                        radio = $('.container_tbr[data-ntbr="'+id_tbr+'"] :input[name='+input.name+'][value="'+input.value+'"]');
                        radio.prop('checked', true);                        
                        break;
                    case 'checkbox':
                        checkbox = $('.container_tbr[data-ntbr="'+id_tbr+'"] :input[name='+input.name+']');
                        if( input.value == '1' )
                            checkbox.prop('checked', true);
                        else
                            checkbox.prop('checked', false);
                        break;
                    default:
                        elemento.val(input.value)
                        break;
                }
            }
        });
        // Evaluaremos los form_field de la tbr
        crm_tbr.form_fields.forEach(tabla => {
            if( tabla.id_form_field == id_tbr ) {
                // Obtenemos los form_fields de la tabla seleccionada
                tabla.data.forEach( row => {
                    // Evaluamos que campos podran ser modificables y cuales no
                    if( row.editable == 0 ) {
                        $('.container_tbr[data-ntbr='+tabla.id_form_field+'] :input[name="'+row.slug+'"]').prop('disabled', true)
                    } else {
                        $('.container_tbr[data-ntbr='+tabla.id_form_field+'] :input[name="'+row.slug+'"]').prop('disabled', false)
                    }
                });
            }
        });
        $('.container_tbr[data-ntbr='+id_tbr+'] button.btn-update').show();
        $('.container_tbr[data-ntbr='+id_tbr+'] button.btn-cancel').show();
        $('.container_tbr[data-ntbr='+id_tbr+'] button.btn-add').hide();
        //Deshabilitamos todos los botones de la tabla
        $('.container-tbr-data[data-tbr-id='+id_tbr+'] button.btn').attr("disabled", true);
    },
    update: (id_tbr, index) => {
        // Restauramos el formulario para poder acceder a los campos desabilitados
        crm_tbr.restartFields(id_tbr);
        if (!crm_tbr.data[id_tbr]) {
            crm_tbr.data[id_tbr] = [];
        }
        let row = $('.container_tbr[data-ntbr="'+id_tbr+'"] :input').serializeArray();
        //Reestructuramos el row
        let rowNew = [];
        let success = true;
        //obtenemos el renglon para el campo oculto ID
        rowTemp = row.find(itemTemp => itemTemp.name === 'id');
        valueTemp = rowTemp?.value ?? '0';
        rowNew.push({name: 'id', value: valueTemp});
        crm_tbr.headers.forEach(item => {
            if (success === true) {
                if( item.id_form_fields == id_tbr) { //Es un elemento de esa tabla relacionada
                    rowTemp = row.find(itemTemp => itemTemp.name === item.slug);//obtenemos el renglon del nombre y valor del elemento
                    valueTemp = rowTemp?.value ?? '';
                    switch (item.type) {
                        case 'checkbox':
                            valueTemp = (valueTemp == 'on') ? 1 : 0;
                            if( item.required == 1 && valueTemp == 0 ){
                                success = false;
                                toastmsg("Error el campo: "+item.name+" es requerido", "danger");
                                $('.container_tbr[data-ntbr="'+id_tbr+'"] :input[name='+item.slug+']').focus();
                                break;
                            }
                            break;
                        default:
                            if( item.required == 1 && valueTemp == 0 ){
                                success = false;
                                toastmsg("Error el campo: "+item.name+" es requerido", "danger");
                                $('.container_tbr[data-ntbr="'+id_tbr+'"] :input[name='+item.slug+']').focus();
                                break;
                            }
                            break;
                    }
                    rowNew.push({name: item.slug, value: valueTemp});
                }
            }
        });
        if( success === true ){
            crm_tbr.data[id_tbr][index] = rowNew;
            crm_tbr.reset(id_tbr);
            crm_tbr.list(id_tbr);
        }
        //Recorremos los campos de el renglon
        row.forEach(campo => {
            // indicamos que este registro fue modificado
            if( campo.name == 'id' && campo.value != 0 ) {
                if (!crm_tbr.to_update[id_tbr]) {
                    crm_tbr.to_update[id_tbr] = [];
                }
                crm_tbr.to_update[id_tbr].push(campo.value);
            }
        });
    },
    // Eliminar un registro por su índice
    remove: (id_tbr, index) => {
        let row = crm_tbr.data[id_tbr][index];
        //Recorremos los campos de el renglon
        row.forEach(campo => {
            if( campo.name == 'id' && campo.value != 0 ) {
                if (!crm_tbr.to_delete[id_tbr]) {
                    crm_tbr.to_delete[id_tbr] = [];
                }
                crm_tbr.to_delete[id_tbr].push(campo.value);
            }
        });
        crm_tbr.data[id_tbr].splice(index, 1);
        crm_tbr.list(id_tbr);
    },
    list: (id_tbr) => {
        let div = $(".container-tbr-data[data-tbr-id="+id_tbr+"]");
        let i = -1;
        let html = `
            <div class="table table-striped">`;
        if (crm_tbr.data[id_tbr][0]) { //La tabla solo se muestra si hay algun registro
            //HEADER
            html += ` <div class="table-header-group">
                <div class="table-row">`;
            crm_tbr.headers.forEach( item => {
                if( item.id_form_fields == id_tbr)
                    html+= `<div class="table-cell">${item.name}</div>`;
            });
            html += `<div class="table-cell">Acción</div>
                </div>
            </div>`;
            //BODY
            crm_tbr.data[id_tbr].forEach(row => {
                i++;
                html += `
                <div class="table-row">`;
                crm_tbr.headers.forEach( item => {
                    if( item.id_form_fields == id_tbr) {
                        renglonTemp =  row.find(itemTemp => itemTemp.name === item.slug);
                        valueShow = renglonTemp?.value ?? ''
                        html += `
                    <div class="table-cell">${valueShow}</div>`;
                    }
                });
                html += `
                    <div class="table-cell">
                        <button type="button" class="btn btn-secondary ml-2" title="Editar" onclick="crm_tbr.edit(${id_tbr}, ${i})">
                            <i class="far fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger ml-2" title="Eliminar" onclick="crm_tbr.remove(${id_tbr}, ${i})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>`;
            });
        }
        html += `</div>`;
        div.html(html);
    },
};

// Cambios de vista entre busqueda y filtros para formularios
function switch_view_forms(tipo) {
    // DETECTAMOS EL TIPO DE BUSQUEDA QUE SE HIZO
    const buscador = $("#search_form_text").val();
    if( tipo == 'busqueda' ) {
        pnx.setUniqueid("");
        $("#leform").html("");
        $("#campanasal").show();
        $("#tmpformdel").hide();
        if( buscador == '' ){ // Filtros de busqueda: Solo se muestra el filtro
            $("#list-forms").html("");
            $("#forms_pag").hide();
        } else { // Lista de formularios: Se mustra el filtro y los resultados de busqueda
            $("#forms_pag").show();
        }
    }
    // Formulario: solo se muestra el formulario
    if( tipo == 'formulario' ) {
        $("#campanasal").hide();
    }
}
