$(document).ready(function(){
    dataDep.preload_forms();
    $(document).on('submit', "#form_bus", function(e){
        e.preventDefault();
        dataDep.pag = 0;
        dataDep.list();
    });
    // Paginación:
    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();
        dataDep.pag = $(this).data('pag');
        dataDep.list();
    });
    $(document).on("change", "#elirpp", function () {
        dataDep.pag = 0;
        dataDep.rpp = $(this).val();
        dataDep.list();
    });
});

var dataDep = {
    pag: 0,
    reg: 0,
    rpp: 20,
    id_campaign: '',
    id_form: '',
    slug_key: '',
    registros: [],
    pk: '',
    human_text: (string) => {
        return string.charAt(0).toUpperCase() + string.slice(1).replace(/_/g, " ");
    },
    //obtenemos las variables de la url
    getUrlVar:(variable) => {
        let query = window.location.search.substring(1);
        let vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            if (pair[0] == variable) {
                return pair[1];
            }
        }
        return false;
    },
    preload_forms() {
        let f = dataDep.getUrlVar('f');
        let c = dataDep.getUrlVar('c');
        $("#form_dataDepFilter input[name=id_form]").val(f);
        $("#form_dataDepFilter input[name=id_campaign]").val(c);
        setTimeout(() => {
            dataDep.change_filter();
        }, 200);
    },
    change_filter: (modificado) => {
        //Reiniciamos algunos valores
        $("#add_and_search").hide();
        $("#paginacion").html('');
        $("#form_dataDep").html('');
        $("#dataDep_table").html('');
        switch (modificado) {
            case 'id_campaign': 
                $("#form_dataDepFilter select[name=id_form]").val('');
                $("#form_dataDepFilter select[name=slug_key]").val('');
                break;
            
            case 'id_form':
                $("#form_dataDepFilter select[name=slug_key]").val('');
                break;
        }
        //obtenemos los valores de los filtros
        dataDep.id_campaign = $("#form_dataDepFilter input[name=id_campaign]").val();
        dataDep.id_form     = $("#form_dataDepFilter input[name=id_form]").val();
        dataDep.slug_key    = $("#form_dataDepFilter select[name=slug_key]").val();
        //calculamos los eventos
        if( dataDep.id_campaign == '') {
            $("#dataDep_table").html('<center>Seleccione una campaña</center>');
        } else if ( dataDep.id_form == '') {
            dataDep.load_forms();
            $("#dataDep_table").html('<center>Seleccione un formulario</center>');
        } else if ( dataDep.slug_key == '' ) {
            dataDep.load_depen();
            $("#dataDep_table").html('<center>Seleccione un catálogo</center>');
        } else {
            $("#add_and_search").show();
            $("#buscar").val('');
            $("#buscar").attr('placeholder','Buscar por: '+dataDep.human_text(dataDep.slug_key));
            dataDep.pag = 0;
            dataDep.reg = 0;
            dataDep.list();
        }
    },
    load_forms: () => {
        let options = '<option value="">-Seleccione-</option>';
        $("#spinnerModal").modal("show");
        $.post(site_url + 'form/datadep_loadforms', { id_campaign: dataDep.id_campaign }, function (res) {
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                res.map( row => {
                    options += `<option value="${ row.id }">${ row.name }</option>`;
                })
                $("#form_dataDepFilter select[name=id_form]").html(options);
            }
            $("#spinnerModal").modal("hide");
        }, "json")
        .fail(function (data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    load_depen: () => {
        let options = '<option value="">-Seleccione-</option>';
        $("#spinnerModal").modal("show");
        $.post(site_url + 'form/datadep_loaddepend', { id_form: dataDep.id_form }, function (res) {
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                //Validamos si tiene catalogos
                let n = res.length;
                if( n > 0 ) {
                    $("#text_form_name").html(res[0].form_name);
                    res.map( row => {
                        options += `<option value="${ row.slug }">${ row.name }</option>`;
                    })
                    $("#form_dataDepFilter select[name=slug_key]").html(options);
                } else {
                    toastmsg('No existen catalogos en este formulario', "danger");
                }
            }
            $("#spinnerModal").modal("hide");
        }, "json")
        .fail(function (data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    list: () => {
        let html = '';
        let html_modal = '';
        let bus = $("#buscar").val();
        $("#spinnerModal").modal("show");
        $.post(site_url + 'form/datadep_list', { id_form: dataDep.id_form, slug_key: dataDep.slug_key, pag: dataDep.pag, rpp: dataDep.rpp, bus: bus}, function (res) {
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                dataDep.pk = res.pk;
                dataDep.head = res.head;
                dataDep.registros = [];
                //Encabezado
                html += `
                <div class="table-header-group">
                    <div class="table-row">`;
                    res.head.map( item => {
                        if( item == 'active_system_row' )
                            html += `<div class="table-cell">Registro Activo</div>`;
                        else
                            html += `<div class="table-cell">${ dataDep.human_text(item) }</div>`;
                    })
                    if( res.head.length > 0 ) {
                        html += `<div class="table-cell">Acción</div>`;
                    }
                html += `
                    </div>
                </div>
                `;
                //Formulario
                html_modal += `
                <div class="modal-header">
                    <h4 class="modal-title">Catálogo: ${ dataDep.human_text(dataDep.slug_key) }</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-end">`;
                res.head.map( item => {
                    if( item === 'active_system_row') {
                        html_modal += `
                        <div class="col-xs-12 col-md-6 mb-2">
                            <div class="input-group custom-switch">
                                <input type="checkbox" class="custom-control-input" id="active_system_row" name="active_system_row" value="1" checked='checked'>
                                <label class="custom-control-label" for="active_system_row">Activo</label>
                            </div>
                        </div>`;
                    }
                    else {
                        html_modal += `
                        <div class="col-xs-12 col-md-6 mb-2">
                            <label>${ dataDep.human_text(item) }</label>
                            <input type="text" name="${ item }" class="form-control"/>
                        </div>`;
                    }
                })
                html_modal += `
                    </div>
                </div>`;
                html_modal += `
                <div class="modal-footer">
                    <input type="button" name="param___accion" value="Agregar" class="btn btn-info" onclick="dataDep.save()"/>
                    <input type="hidden" name="param___accion" value="Agregar" class="form-control" />
                    <input type="hidden" name="param___original_key" class="form-control" />
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>`;
                //Tabla
                res.rows.map(row => {
                    dataDep.registros[row[dataDep.pk]] = row;
                    html += `<div class="table-row">`;
                    res.head.map( item => {
                        if( item === 'active_system_row') {
                            html += `<div class="table-cell">${ (row[item] == 1) ? 'Si' : 'No' }</div>`;
                        } else {
                            html += `<div class="table-cell">${ row[item] }</div>`;
                        }
                    });
                    if( res.rows.length > 0 ) {
                        html += `
                            <div class="table-cell">
                                <button type="button" class="btn btn-info" onclick="dataDep.edit('${row[dataDep.pk]}')">
                                    Editar
                                </button>
                                <!--button type="button" class="btn btn-warning" onclick="dataDep.delete('${row[dataDep.pk]}')">
                                    Borrar
                                </button-->
                            </div>
                        </div>`;
                    }
                });
                $("#dataDep_table").html(html);
                $("#form_dataDep").html(html_modal);
                dataDep.pag = res.pag;
                dataDep.tot = res.tot;
                dataDep.rpp = res.rpp;
                paginacion(res.pag, res.tot, res.rpp, res.rows.length);
            }
            $("#spinnerModal").modal("hide");
        }, "json")
        .fail(function (data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    reset: () => {
        $("#form_dataDep input[name=param___accion]").val("Agregar");
        $("#form_dataDep").trigger("reset");
    },
    add: () => {
        dataDep.reset();
        $("#dataDep-modal").modal('show');
    },
    edit: (pk) => {
        let row = dataDep.registros[pk];
        let check = false;
        //recorremos los campos
        dataDep.head.map( item => {
            if( item == 'active_system_row' ) {
                cheko = (row['active_system_row']=='0') ? false : true;
                $("#form_dataDep input[type=checkbox][name=active_system_row]").prop('checked',cheko);
            }
            else 
                $("#form_dataDep input[name="+item+"]").val(row[item]);
        })
        $("#form_dataDep input[name=param___accion]").val("Actualizar");
        $("#form_dataDep input[name=param___original_key]").val(row[dataDep.slug_key]);
        $("#dataDep-modal").modal('show');
    },
    save: () => {
        $("#spinnerModal").modal("show");
        let formDataArray = $("#form_dataDep").serializeArray();
        formDataArray.push(
            { name: "param___id_form",  value: dataDep.id_form },
            { name: "param___slug_key", value: dataDep.slug_key },
        );
        let data = $.param(formDataArray);
        $.post(site_url + 'form/datadep_save', data, function (res) {
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                $("#dataDep-modal").modal('hide');
                toastmsg(res, "success");
                dataDep.reset();
            }
            dataDep.list();
            $("#spinnerModal").modal("hide");
        }, "json")
        .fail(function (data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
            
        });
    },
    delete: (pk) => {
        if( confirm("¿Estás seguro(a) de eliminar el registro?") ) {
            let data = {
                'id_form':      dataDep.id_form,
                'slug_key':     dataDep.slug_key,
                'key_value':    pk,
            };
            $.post(site_url + 'form/datadep_delete', data, function (res) {
                if (typeof res.error !== 'undefined') {
                    toastmsg(res.error, "danger");
                } else {
                    dataDep.reset();
                    toastmsg(res, "success");
                }
                dataDep.list();
                $("#spinnerModal").modal("hide");
            }, "json")
            .fail(function (data) {
                $("#spinnerModal").modal("hide");
                if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                    toastmsg(data.responseJSON.error, "danger");
                } else {
                    toastmsg("Error de red, verifica tu conexión a internet.", "danger");
                }
                
            });
        }
    }
}
