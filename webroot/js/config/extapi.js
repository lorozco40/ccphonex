const extapi = {
    pag: 0,
    rpp: 20,
    tot: 0,
    data: {},
    id_extapi: 0,
    id_extapi_met: 0,
    campanas: '',
    getData: (listar) => {
        $("#spinnerModal").modal("show");
        $.post(site_url + "extapi/lista", {campanas: extapi.campanas, pag: extapi.pag, rpp: extapi.rpp}, function(res) {
            let tmpdata = {};
            let html = "";
            let active = "";
            res.data.map(row => {
                tmpdata[row.id] = row;
                active = (row.active == 1) ? "Si" : "";
                url = (row.url.length > 35) ? row.url.substring(0, 35) + "..." : row.url;
                html += `
                <tr class='table-row renglon-padre'>
                    <td class='table-cell'>
                        <div class="btn ml-n2 p-0 text-primary">
                            <span class='fa-stack small toggle-btn' onclick="extapi.colapsar('${'met-api-'+row.id}')">
                                <i class='fas fa-square fa-stack-2x'></i>
                                <i class='fas fa-angle-double-right fa-stack-1x fa-inverse icon-${'met-api-'+row.id}'></i>
                            </span>
                        </div>
                        ${row.campana} 
                    </td>
                    <td class='table-cell'>${row.name}</td>
                    <td class='table-cell'>${url}</td>
                    <td class='table-cell'>${active}</td>
                    <td class='table-cell'>
                        <button class='btn btn-secondary ediextapi' data-id='${row.id}'>Editar</button>
                        <button class='btn btn-danger' onclick="extapi.delete(${row.id})">
                            Eliminar
                        </button>
                        <button class='btn btn-primary' data-id='${row.id}' data-api-name='${row.name}' onclick="extapi.metodo_modal(${row.id})">
                            M(${row.mets.length})
                        </button>
                    </td>
                </tr>
                <tr style="display: none;"></tr>
                <tr class="${'met-api-'+row.id}" style="display: none;">
                    <td colspan="6" class="ps-5">
                        <table class="table m-0">`;
                row.mets.map(met => {
                    html += `
                    <tr class=''>
                        <td class='table-cell'></td>
                        <td class='table-cell'>
                            <div class="btn ml-n2 p-0 text-primary">
                                <span class='fa-stack small toggle-btn' onclick="extapi.colapsar('${'fields-met-'+met.id}')">
                                    <i class='fas fa-square fa-stack-2x'></i>
                                    <i class='fas fa-angle-double-right fa-stack-1x fa-inverse icon-${'fields-met-'+met.id}'></i>
                                </span>
                            </div>
                        M ->${met.prot}</td>
                        <td class='table-cell'>Método: ${met.met}</td>
                        <td class='table-cell'>Xtype: ${met.xtype}</td>
                        <td class='table-cell'></td>
                        <td class='table-cell'></td>
                        <td class='table-cell'>
                            <button class='btn btn-primary' onclick="extapi.campos_modal(${row.id}, ${met.id})">
                            (${met.fields.length}) Campos
                            </button>
                        </td>
                    </tr>
                    <tr style="display: none;"></tr>
                    <tr class="${'fields-met-'+met.id}" style="display: none;">
                        <td colspan="7" class="ps-5">
                            <table class="table m-0">`;
                    met.fields.map(field => {
                        html += `
                        <tr class='table-row'>
                            <td class='table-cell'></td>
                            <td class='table-cell'></td>
                            <td class='table-cell'>Campo:</td>
                            <td class='table-cell'>${field.field} [${field.ftype}]</td>
                            <td class='table-cell'>${field.descript}</td>
                            <td class='table-cell'></td>
                            <td class='table-cell'></td>
                        </tr>`;
                    });
                    html += `</table></td></tr>`;
                });
                html += `</table></td></tr>`;
            });
            $("#liextapi").html(html);
            extapi.data = tmpdata;
            extapi.pag = res.pag;
            extapi.rpp = res.rpp;
            extapi.tot = res.tot;
            paginacion(res.pag, res.tot, res.rpp, res.data.length);
            //ahora listaremos met o fields
            if( listar == 'fields' )
                extapi.listamos_campos();
            if( listar == 'met' )
                extapi.listar_metodos(extapi.id_extapi);
                $("#spinnerModal").modal("hide");
        });
    },
    //LISTAMOS LA INFORMACION
    listar_metodos: () => {
        let api = extapi.data[extapi.id_extapi];
        let html='';
        api.mets.map(row => {
            html += `
            <tr class='table-row'>
                <td class='table-cell'>${row.prot}</td>
                <td class='table-cell'>${row.met}</td>
                <td class='table-cell'>${row.xtype}</td>
                <td class='table-cell'>${row.info}</td>
                <td>
                    <button type="button" class='btn text-warning p-0' onclick="extapi.reset_form_met(${row.id})">
                        <span class='fa-stack small'>
                            <i class='fas fa-square fa-stack-2x'></i>
                            <i class='fas fa-pen fa-stack-1x fa-inverse'></i>
                        </span>
                    </button>
                    <button type="button" onclick="extapi.deleteMet(${row.id})" class='btn text-danger p-0'>
                        <span class='fa-stack small'>
                            <i class='fas fa-square fa-stack-2x'></i>
                            <i class='fas fa-trash fa-stack-1x fa-inverse'></i>
                        </span>
                    </button>
                </td>
            </tr>
            `;
        });
        if( api.mets.length == 0 )
            html = `<tr class="table-row"><td class="text-center" colspan="5">No se encontraron registros</td></tr>`;
        $("#lista_extapi_met").html(html);
    },
    listamos_campos: () => {
        let api = extapi.data[extapi.id_extapi];
        let met = api.mets.find(mets => mets.id == extapi.id_extapi_met);
        let html='';
        met.fields.map(row => {
            html += `
            <tr class='table-row'>
                <td class='table-cell'>${row.field}</td>
                <td class='table-cell'>${row.ftype}</td>
                <td class='table-cell'>${row.dir}</td>
                <td class='table-cell'>${row.req}</td>
                <td class='table-cell'>${row.descript}</td>
                <td>
                    <button type="button" class='btn text-warning ml-n2 p-0' onclick="extapi.reset_form_fields(${row.id},${extapi.id_extapi},${row.id_extapi_met})">
                        <span class='fa-stack small'>
                            <i class='fas fa-square fa-stack-2x'></i>
                            <i class='fas fa-pen fa-stack-1x fa-inverse'></i>
                        </span>
                    </button>
                    <button type="button" onclick="extapi.deleteFields(${row.id})" class='btn text-danger ml-n2 p-0'>
                        <span class='fa-stack small'>
                            <i class='fas fa-square fa-stack-2x'></i>
                            <i class='fas fa-trash fa-stack-1x fa-inverse'></i>
                        </span>
                    </button>
                </td>
            </tr>
            `;
        });
        if( met.fields.length == 0 )
            html = `<tr class="table-row"><td class="text-center" colspan="6">No se encontraron registros</td></tr>`;
        $("#lista_extapi_fields").html(html);
    },
    //GUARDADO DE INFORMACION
    guardar: () => {
        $("#spinnerModal").modal("show");
        let data = $("#formextapi").serialize();
        $.post(site_url+'extapi/guardar', data, function(res){
            toastmsg(res.msg, res.tipo);
            if(res.tipo == 'ok'){
                $("#extApiModal").modal("hide");
            }
            $("#spinnerModal").modal("hide");
            extapi.getData();
        }, "json")
        .fail(function(data){
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    guardar_met: () => {
        $("#spinnerModal").modal("show");
        let data = $("#form_extapi_met").serialize();
        $.post(site_url+'extapi/guardarMet', data, function(res){
            toastmsg(res.msg, res.tipo);
            if(res.tipo == 'ok'){
                extapi.reset_form_met(0);
            }
            extapi.getData('met')
            $("#spinnerModal").modal("hide");
        }, "json")
        .fail(function(data){
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    guardar_fields:  () => {
        $("#spinnerModal").modal("show");
        let data = $("#form_extapi_fields").serialize();
        $.post(site_url+'extapi/guardarFields', data, function(res){
            toastmsg(res.msg, res.tipo);
            if(res.tipo == 'ok'){
                extapi.reset_form_fields(0);
            }
            extapi.getData('fields');
            $("#spinnerModal").modal("hide");
        }, "json")
        .fail(function(data){
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    //SETEAMOS/RESETEAMOS FORMULARIOS
    reset_form_met: (id) => {
        let api = extapi.data[extapi.id_extapi];
        if( id == 0 ) {
            $("#form_extapi_met button[name=Agregar]").removeClass("d-none");
            $("#form_extapi_met button[name=Actualizar]").addClass("d-none");
            $("#form_extapi_met input[name=id]").val(0);
            $("#form_extapi_met input[name=id_extapi]").val(extapi.id_extapi);
            $("#form_extapi_met input[name=api]").val(api.name);   

            $("#form_extapi_met select[name=prot]").val('');
            $("#form_extapi_met input[name=met]").val('');
            $("#form_extapi_met input[name=xtype]").val('');
            $("#form_extapi_met input[name=info]").val('');
        }
        else {
            let met = api.mets.find(mets => mets.id == id);
            $("#form_extapi_met button[name=Agregar]").addClass("d-none");
            $("#form_extapi_met button[name=Actualizar]").removeClass("d-none");
            $("#form_extapi_met input[name=id]").val(id);
            $("#form_extapi_met input[name=id_extapi]").val(extapi.id_extapi);
            $("#form_extapi_met input[name=api]").val(api.name);   
            
            $("#form_extapi_met select[name=prot]").val(met.prot);
            $("#form_extapi_met input[name=met]").val(met.met);
            $("#form_extapi_met input[name=xtype]").val(met.xtype);
            $("#form_extapi_met input[name=info]").val(met.info);
        }
    },
    reset_form_fields: (id) => {
        let api = extapi.data[extapi.id_extapi];
        let met = api.mets.find(mets => mets.id == extapi.id_extapi_met);
        let dir = false;
        let req = false;
        if( id == 0 ) {
            $("#form_extapi_fields button[name=Agregar]").removeClass("d-none");
            $("#form_extapi_fields button[name=Actualizar]").addClass("d-none");
            $("#form_extapi_fields input[name=id]").val(0);
            $("#form_extapi_fields input[name=id_extapi_met]").val(extapi.id_extapi_met);
            $("#form_extapi_fields input[name=api]").val(api.name);
            $("#form_extapi_fields input[name=met]").val(met.met);
            
            $("#form_extapi_fields input[name=field]").val('');   
            $("#form_extapi_fields select[name=ftype]").val('');
            $("#form_extapi_fields input[name=dir]").prop("checked", dir);
            $("#form_extapi_fields input[name=req]").prop("checked", req);
            $("#form_extapi_fields input[name=descript]").val('');
        }
        else {
            let field = met.fields.find(fields => fields.id == id);
            dir = (field.dir == 1) ? true : false;
            req = (field.req == 1) ? true : false;
            $("#form_extapi_fields button[name=Agregar]").addClass("d-none");
            $("#form_extapi_fields button[name=Actualizar]").removeClass("d-none");
            $("#form_extapi_fields input[name=id]").val(id);
            $("#form_extapi_fields input[name=id_extapi_met]").val(extapi.id_extapi_met);
            $("#form_extapi_fields input[name=api]").val(api.name);
            $("#form_extapi_fields input[name=met]").val(met.met);

            $("#form_extapi_fields input[name=field]").val(field.field);
            $("#form_extapi_fields select[name=ftype]").val(field.ftype);   
            $("#form_extapi_fields input[name=dir]").prop("checked", dir);
            $("#form_extapi_fields input[name=req]").prop("checked", req);
            $("#form_extapi_fields input[name=descript]").val(field.descript);
        }
    },
    //MODALES
    metodo_modal: (id_extapi) => {
        extapi.id_extapi = id_extapi;
        extapi.listar_metodos();
        extapi.reset_form_met(0);        
        $("#extapiMetModal").modal("show");
    },
    campos_modal: (id_extapi, id_extapi_met) => {
        extapi.id_extapi = id_extapi;
        extapi.id_extapi_met = id_extapi_met;
        extapi.listamos_campos();
        extapi.reset_form_fields(0);
        $("#extapiFieldsModal").modal("show");
    },
    //ELIMINACION DE REGISTROS
    delete: (id) => {
        if( confirm("¿Esta seguro de eliminar el registro y sus métodos relacionados?") ) {
            $("#spinnerModal").modal("show");
            $.post(site_url+'extapi/delete', {id: id}, function(res){
                toastmsg(res.msg, res.tipo);
                $("#spinnerModal").modal("hide");
                extapi.getData('');   
            }, "json")
            .fail(function(data){
                $("#spinnerModal").modal("hide");
                if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                    toastmsg(data.responseJSON.error, "danger");
                } else {
                    toastmsg("Error de red, verifica tu conexión a internet.", "danger");
                }
            });
        }
    },
    deleteMet: (id) => {
        if( confirm("¿Esta seguro de eliminar el registro y sus campos relacionados?") ) {
            $("#spinnerModal").modal("show");
            $.post(site_url+'extapi/deleteMet', {id: id}, function(res){
                toastmsg(res.msg, res.tipo);
                $("#spinnerModal").modal("hide");
                extapi.reset_form_met(0);
                extapi.getData('met');   
            }, "json")
            .fail(function(data){
                $("#spinnerModal").modal("hide");
                if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                    toastmsg(data.responseJSON.error, "danger");
                } else {
                    toastmsg("Error de red, verifica tu conexión a internet.", "danger");
                }
            });
        }
    },
    deleteFields: (id) => {
        if( confirm("¿Esta seguro de eliminar el registro?") ) {
            $("#spinnerModal").modal("show");
            $.post(site_url+'extapi/deleteFields', {id: id}, function(res){
                toastmsg(res.msg, res.tipo);
                $("#spinnerModal").modal("hide");
                extapi.reset_form_fields(0);
                extapi.getData('fields');
            }, "json")
            .fail(function(data){
                $("#spinnerModal").modal("hide");
                if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                    toastmsg(data.responseJSON.error, "danger");
                } else {
                    toastmsg("Error de red, verifica tu conexión a internet.", "danger");
                }
            });
        }
    },
    colapsar: (clase) => {
        let icon = $('.icon-'+clase);
        let elemento = $('.'+clase);
        elemento.toggle(0);
        if (elemento.is(':visible')) {
            icon.removeClass('fa-angle-double-right').addClass('fa-angle-double-down')
        } else {
            icon.removeClass('fa-angle-double-down').addClass('fa-angle-double-right')
        }
    },
    llenaForm: id => {
        let active    = (extapi.data[id].active == 1) ? true : false;
        let valid_crt = (extapi.data[id].valid_crt == 1) ? true : false;
        $("#formextapi input[name=id]").val(id);
        $("#formextapi input[name=name]").val(extapi.data[id].name);
        $("#formextapi input[name=url]").val(extapi.data[id].url);
        $("#formextapi input[name=sign]").val(extapi.data[id].sign);
        $("#formextapi input[name=user]").val(extapi.data[id].user);
        $("#formextapi input[name=pass]").val(extapi.data[id].pass);
        $("#formextapi select[name=campana]").val(extapi.data[id].id_campaign);
        $("#formextapi select[name=logloc]").val(extapi.data[id].logloc);
        $("#formextapi textarea[name=token]").val(extapi.data[id].token);
        $("#formextapi textarea[name=xhash]").val(extapi.data[id].xhash);
        $("#formextapi textarea[name=info]").val(extapi.data[id].info);
        $("#formextapi input[name=active]").prop("checked", active);
        $("#formextapi input[name=valid_crt]").prop("checked", valid_crt);
        $("#extApiModal").modal("show");
    },
}

$(document).ready(function(){
    extapi.getData();
    $(document).on("click", ".ediextapi", function(){
        let id = $(this).data("id");
        extapi.llenaForm(id);
    });
    $(document).on("click", "#nuextapi", function(){
        $("#formextapi").trigger("reset");
        $("#formextapi input[name=id]").val(0);
    });
    $(document).on("change", "#campanas", function(){
        extapi.campanas = $(this).val();
        extapi.getData();
    });
    $(document).on("submit", "#formextapi", function(e){
        e.preventDefault();
        extapi.guardar();
    });
    $(document).on("submit", "#form_extapi_met", function(e){
        e.preventDefault();
        extapi.guardar_met();
    });
    $(document).on("submit", "#form_extapi_fields", function(e){
        e.preventDefault();
        extapi.guardar_fields();
    });

    // Paginación:

    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();
        extapi.pag = $(this).data('pag');
        extapi.getData();
    });
    $(document).on("change", "#elirpp", function () {
        extapi.pag = 0;
        extapi.rpp = $(this).val();
        extapi.getData();
    });
});
