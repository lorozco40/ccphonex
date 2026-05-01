$(document).ready(function(){
    crm.start();
    $(document).on("submit","form",function(e){
        $("#spinnerModal").modal("show");
    });
    $(document).on("click", "#savesembtn", function(e){
        e.preventDefault();
        $("#spinnerModal").modal("show");
        let data = $("#savesemform").serialize();
        $.post(site_url+"crm/savesem", data, function(data){
            toastmsg(data.msg, data.tipo);
            $("#spinnerModal").modal("hide");
        }, "json");
    })
    formAsig.list();
    calcField.list();
    oct.list();
    ftr.list();
});

var crm = {
    start:()=> {
        crm.tab('tab_oct');
    },
    tab:(tab_id)=> {
        //Desactivamos todos los tabs
        $(".tab-crm").removeClass("active");
        //Activamos el tab que nos interesa
        $("#"+tab_id).addClass("active");
        //Ocultamos todas las secciones de los tabs
        $(".sec-tab-crm").hide();
        //Mostramos la seccion de tab seleccionada
        $("#sec_"+tab_id).show();
    },
}

var calcField = {
    registros: [],
    campDeta: [], //Contiene la informacion de todos los campos de el formulario en curso
    list: () => {
        let html = '';
        let id_form = $("#form_calc_field input[name=id_form]").val();
        $("#spinnerModal").modal("show");
        $.post(site_url + 'form/calc_field_list', { id_form: id_form }, function (res) {
            calcField.campDeta = res.campos_detalle;
            let campos_calculados = res.campos_calculados
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                campos_calculados.map(row => {
                    calcField.registros[row.id] = row;
                    html += `
                    <div class="table-row">
                        <div class="table-cell">${ calcField.campDeta[row.activator].name }</div>
                        <div class="table-cell">${ calcField.campDeta[row.field_r].name }</div>
                        <div class="table-cell">${ calcField.campDeta[row.field_a].name }</div>
                        <div class="table-cell">( ${ row.operator } )</div>
                        <div class="table-cell">${ calcField.campDeta[row.field_b].name }</div>
                        <div class="table-cell">
                            <button type="button" class="btn btn-info" onclick="calcField.edit(${ row.id })">
                                Editar
                            </button>
                            <button type="button" class="btn btn-warning" onclick="calcField.delete(${ row.id })">
                                Borrar
                            </button>
                        </div>
                    </div>
                    `;
                });
                $("#calc_field_list").html(html);
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
    edit: (id)=> {
        let row = calcField.registros[id];
        $("#form_calc_field input[name=id]").val(row.id);
        $("#form_calc_field select[name=activator]").val(row.activator);
        $("#form_calc_field select[name=field_r]").val(row.field_r);
        $("#form_calc_field select[name=field_a]").val(row.field_a);
        $("#form_calc_field select[name=operator]").val(row.operator);
        $("#form_calc_field select[name=field_b]").val(row.field_b);
        $("#form_calc_field input[name=accion]").val("Actualizar");
        $("#form_calc_field input[name=cancelar]").show();
    },
    reset: () => {
        $("#form_calc_field input[name=id]").val(0);
        $("#form_calc_field select[name=activator]").val('');
        $("#form_calc_field select[name=field_r]").val('');
        $("#form_calc_field select[name=field_a]").val('');
        $("#form_calc_field select[name=operator]").val('');
        $("#form_calc_field select[name=field_b]").val('');
        $("#form_calc_field input[name=accion]").val("Agregar");
        $("#form_calc_field input[name=cancelar]").hide();
    },
    save: () => {
        $("#spinnerModal").modal("show");
        let data = $("#form_calc_field").serialize();
        $.post(site_url + 'form/calc_field_save', data, function (res) {
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                calcField.reset();
                toastmsg(res, "success");
            }
            calcField.list();
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
    delete: (id) => {
        if( confirm("¿Esta seguro que quiere borrar el registro?")){
            $("#spinnerModal").modal("show");
            $.post(site_url + 'form/calc_field_delete', { id:id }, function (res) {
                if (typeof res.error !== 'undefined') {
                    toastmsg(res.error, "danger");
                } else {
                    toastmsg(res, "success");
                }
                calcField.list();
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

var formAsig = {
    registros: [],
    get_id_form: () => {
        let url = window.location.href;
        let partes = url.split('/');
        return partes[partes.length - 1];
    },
    depasig_fields: () => {//Traemos los campos pertenecienteas al activador seleccionado
        let activator = $("#form_dep_asig select[name=activador]").val();
        let id_form = formAsig.get_id_form();
        let options = '<option value="">-Seleccione-</option>';
        if( activator == "" ) {
            $("#options_campo").html(options);
        }
        else {
            $("#spinnerModal").modal("show");
            $.post(site_url + 'form/depasig_fields', { id_form: id_form, activator: activator }, function (res) {
                if (typeof res.error !== 'undefined') {
                    toastmsg(res.error, "danger");
                } else {
                    res.map(item => {
                        options += `<option value="${item}">${item}</option>`;
                    });
                }
                $("#options_campo").html(options);
                $("#spinnerModal").modal("hide");
            }, "json")
            .fail(function (data) {
                $("#spinnerModal").modal("hide");
                $("#options_campo").html(options);
                if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                    toastmsg(data.responseJSON.error, "danger");
                } else {
                    toastmsg("Error de red, verifica tu conexión a internet.", "danger");
                }
            });
        }
    },
    list: () => {
        let html = '';
        let id_form = formAsig.get_id_form();
        $("#spinnerModal").modal("show");
        $.post(site_url + 'form/depasig_list', { id_form: id_form }, function (res) {
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                res.map(row => {
                    formAsig.registros[row.id] = row;
                    html += `
                    <div class="table-row">
                        <div class="table-cell">${ row.activador }</div>
                        <div class="table-cell">${ row.campo }</div>
                        <div class="table-cell">${ row.copia }</div>
                        <div class="table-cell">
                            <button type="button" class="btn btn-info" onclick="formAsig.edit(${ row.id })">
                                Editar
                            </button>
                            <button type="button" class="btn btn-warning" onclick="formAsig.delete(${ id_form }, ${ row.id })">
                                Borrar
                            </button>
                        </div>
                    </div>
                    `;
                });
                $("#depasig_rows").html(html);
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
        $("#form_dep_asig input[name=id]").val(0);
        $("#form_dep_asig select[name=activador]").val('');
        $("#form_dep_asig select[name=campo]").val('');
        $("#form_dep_asig select[name=copia]").val('');
        $("#form_dep_asig input[name=accion]").val("Agregar");
        $("#form_dep_asig input[name=cancelar]").hide();
        $("#options_campo").html(`<option value="">-Seleccione-</option>`);
    },
    edit: (id)=> {
        $("#form_dep_asig input[name=cancelar]").show();
        let item = formAsig.registros[id];
        let activator = item.activador;
        let id_form = formAsig.get_id_form();
        let options = '<option value="">-Seleccione-</option>';
        $("#spinnerModal").modal("show");
        $.post(site_url + 'form/depasig_fields', { id_form: id_form, activator: activator }, function (res) {
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                res.map(item => {
                    options += `<option value="${item}">${item}</option>`;
                });
                $("#options_campo").html(options);
            }
            $("#form_dep_asig input[name=id]").val(item.id);
            $("#form_dep_asig select[name=activador]").val(item.activador);
            $("#form_dep_asig select[name=campo]").val(item.campo);
            $("#form_dep_asig select[name=copia]").val(item.copia);
            $("#form_dep_asig input[name=accion]").val("Actualizar");
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
    save: () => {
        $("#spinnerModal").modal("show");
        let data = $("#form_dep_asig").serialize();
        $.post(site_url + 'form/depasig_save', data, function (res) {
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                formAsig.reset();
                toastmsg(res, "success");
            }
            formAsig.list();
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
    delete: (id_form, id) => {
        if( confirm("¿Esta seguro que quiere borrar el registro?")){
            $("#spinnerModal").modal("show");
            $.post(site_url + 'form/depasig_delete', { id_form:id_form, id:id }, function (res) {
                if (typeof res.error !== 'undefined') {
                    toastmsg(res.error, "danger");
                } else {
                    toastmsg(res, "success");
                }
                formAsig.list();
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

var ftr = {
    registros: [],
    list: () => {
        let html = '';
        let id_form = $("#form_ftr input[name=id_form]").val();
        $("#spinnerModal").modal("show");
        $.post(site_url + 'form/ftr_list', { id_form: id_form }, function (res) {
            let rows = res
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                rows.map(row => {
                    ftr.registros[row.id] = row;
                    html += `
                    <div class="table-row">
                        <div class="table-cell">${ row.activator }</div>
                        <div class="table-cell">${ row.field_to_filter }</div>
                        <div class="table-cell">${ row.field_to_compare }</div>
                        <div class="table-cell">${ row.union_table }</div>
                        <div class="table-cell">${ row.union_field_a }</div>
                        <div class="table-cell">${ row.union_field_b }</div>
                        <div class="table-cell">
                            <button type="button" class="btn btn-info" onclick="ftr.edit(${ row.id })">
                                Editar
                            </button>
                            <button type="button" class="btn btn-warning" onclick="ftr.delete(${ row.id })">
                                Borrar
                            </button>
                        </div>
                    </div>
                    `;
                });
                $("#ftr_list").html(html);
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
    new:() => {
        ftr.reset();
        $(".ftr-action").val('Guardar');
        $("#modal-ftr").modal("show");
    },
    reset: () => {
        $("#form_ftr :input[name=id]").val(0);
        $("#form_ftr :input[name=activator]").val('');
        $("#form_ftr :input[name=field_to_filter]").val('');
        $("#form_ftr :input[name=field_to_compare]").val('');
        $("#form_ftr :input[name=union_table]").val('');
        $("#form_ftr :input[name=union_field_a]").val('');
        $("#form_ftr :input[name=union_field_b]").val('');
        $("#form_ftr :input[name=archivo_tbu]").val(null);
        $("#advanced_options").prop('checked', false);
        ftr.handle_advanced_options();
        ftr.handle_visibility_file();
    },
    save: () => {
        $("#spinnerModal").modal("show");
        let data = new FormData( document.getElementById("form_ftr") );
        $.ajax({
            url: site_url+'form/ftr_save',
            data: data,
            processData:false,
            contentType:false,
            type: 'POST',
            success: function (res) {
                if ( !res.success ) {
                    toastmsg(res.message, "danger");
                } else {
                    toastmsg(res.message, "success");
                    let id = res.data.row.id;
                    //Todo bien, actualizamos los datos de ftr.registros[id];
                    ftr.registros[id] = res.data.row;
                    // Llamanos a la funcion de editar el registro actual para recargar los datos
                    ftr.edit(id);
                }
                ftr.list();
                $("#spinnerModal").modal("hide");
            },
            error: function (error)
            {
                $("#spinnerModal").modal("hide");
                if (typeof error.responseJSON !== "undefined" && typeof error.responseJSON.error !== "undefined") {
                    toastmsg(error.responseJSON.error, "danger");
                } else {
                    toastmsg("Error de red, verifica tu conexión a internet.", "danger");
                }
            }
        });
    },
    edit: (id)=> {
        let row = ftr.registros[id];
        $(".ftr-action").val('Actualizar');
        $("#form_ftr :input[name=id]").val(row.id);
        $("#form_ftr :input[name=activator]").val(row.activator);
        $("#form_ftr :input[name=field_to_filter]").val(row.field_to_filter);
        $("#form_ftr :input[name=field_to_compare]").val(row.field_to_compare);
        $("#form_ftr :input[name=union_table]").val(row.union_table);
        $("#form_ftr :input[name=union_field_a]").val(row.union_field_a);
        $("#form_ftr :input[name=union_field_b]").val(row.union_field_b);
        $("#form_ftr :input[name=archivo_tbu]").val(null);
        if(row.union_table || row.union_field_a || row.union_field_b)
            $("#advanced_options").prop('checked', true);
        else
            $("#advanced_options").prop('checked', false);
        ftr.handle_advanced_options();
        ftr.handle_visibility_file();
        $("#modal-ftr").modal("show");
    },
    delete: (id) => {
        if( confirm("¿Esta seguro que quiere borrar el registro?")){
            $("#spinnerModal").modal("show");
            $.post(site_url + 'form/ftr_delete', { id:id }, function (res) {
                if (typeof res.error !== 'undefined') {
                    toastmsg(res.error, "danger");
                } else {
                    toastmsg(res, "success");
                }
                ftr.list();
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
    },
    delete_table_union: () => {
        if( confirm("¿Estas seguro que quieres borrar los datos de tabla union?")){
            $("#spinnerModal").modal("show");
            let id = $("#form_ftr :input[name=id]").val();
            let id_form = $("#form_ftr :input[name=id_form]").val();
            let union_table = $("#form_ftr :input[name=union_table]").val();
            $.post(site_url + 'form/ftr_delete_table_union', { id: id, id_form: id_form, union_table: union_table }, function (res) {
                if ( !res.success ) {
                    toastmsg(res.message, "danger");
                } else {
                    ftr.list();
                    $("#modal-ftr").modal("hide");
                    toastmsg(res.message, "success");
                }
                $("#spinnerModal").modal("hide");
            }, "json")
            .fail(function (error) {
                $("#spinnerModal").modal("hide");
                if (typeof error.responseJSON !== "undefined" && typeof error.responseJSON.error !== "undefined") {
                    toastmsg(error.responseJSON.error, "danger");
                } else {
                    toastmsg("Error de red, verifica tu conexión a internet.", "danger");
                }
            });
        }
    },
    handle_advanced_options: () => {
        let checked = $("#advanced_options").is(':checked');
        // verificamos si el checkbox con id advanced_options esta seleccionado
        if( checked ) {
            // Mostramos el campo de union_table
            $("#opt_adv_section").show();
        } else {
            // Ocultamos el campo de union_table
            $("#opt_adv_section").hide();
        }
    },
    handle_visibility_file: () => {
        let file = $("#form_ftr input[name=union_table]");
        if( file.val() != '' ) {
            $(".ftr-file-text-section").show();
            $(".ftr-file-section").hide();
        } else {
            $(".ftr-file-text-section").hide();
            $(".ftr-file-section").show();
        }
    }
};

var oct = {
    registros: [],
    list: () => {
        let html = '';
        let id_form = $("#form_oct input[name=id_form]").val();
        $("#spinnerModal").modal("show");
        $.post(site_url + 'form/oct_list', { id_form: id_form }, function (res) {
            let rows = res.oct
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                rows.map(row => {
                    oct.registros[row.id] = row;
                    r = oct.props_fields(row.field_r, '');
                    a = oct.props_fields(row.field_a, row.custom_a);
                    b = oct.props_fields(row.field_b, row.custom_b);
                    html += `
                    <div class="table-row">
                        <div class="table-cell">${ r.text } =</div>
                        <div class="table-cell">${ a.text }</div>
                        <div class="table-cell">${ (row.operator) }</div>
                        <div class="table-cell">${ b.text }</div>
                        <div class="table-cell">${ row.order }</div>
                        <div class="table-cell">
                            <button type="button" class="btn btn-info" onclick="oct.edit(${ row.id })">
                                Editar
                            </button>
                            <button type="button" class="btn btn-warning" onclick="oct.delete(${ row.id })">
                                Borrar
                            </button>
                        </div>
                    </div>
                    `;
                });
                $("#oct_list").html(html);
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
    props_fields:(field, custom) => {
        res = {
            'table': '',
            'field': '',
            'value': '',
            'text': '',
        };
        if( field == '0' ) {
            res.value = custom
            res.text = "Valor: "+custom;
        } else {
            if( field == 'N/A' ) {
                res.text = "N/A";
            }
            else {
                x = field.split('.');
                res.table = x[0];
                res.field = x[1];
                res.text = "Campo: "+res.field;
            }
        }

        return res;
    },
    reset: () => {
        $("#form_oct :input[name=id]").val(0);
        $("#form_oct :input[name=field_r]").val('');
        $("#form_oct :input[name=field_a]").val('');
        $("#form_oct :input[name=custom_a]").val('');
        $("#form_oct :input[name=operator]").val('');
        $("#form_oct :input[name=field_b]").val('');
        $("#form_oct :input[name=custom_b]").val('');
        $("#form_oct :input[name=order]").val('0');
    },
    field_visibility: () => {
        let field_a     = $("#form_oct :input[name=field_a]");
        let field_b     = $("#form_oct :input[name=field_b]");
        let operator    = $("#form_oct :input[name=operator]");
        let col_custom_a = $(".oct_custom_a");
        let col_custom_b = $(".oct_custom_b");
        if(field_a.val() == '0') { col_custom_a.show(); }
        else { col_custom_a.hide(); }

        if(field_b.val() == '0') { col_custom_b.show(); }
        else { col_custom_b.hide(); }

        if( operator.val() == 'N/A') {
            field_b.val('N/A');
            col_custom_b.hide();
        } 
    },
    new:() => {
        oct.reset();
        oct.field_visibility();
        $(".oct-action").val('Guardar');
        $("#modal-oct").modal("show");
    },
    edit: (id)=> {
        let row = oct.registros[id];
        $(".oct-action").val('Actualizar');
        $("#form_oct :input[name=id]").val(row.id);
        $("#form_oct :input[name=field_r]").val(row.field_r);
        $("#form_oct :input[name=field_a]").val(row.field_a);
        $("#form_oct :input[name=custom_a]").val(row.custom_a);
        $("#form_oct :input[name=operator]").val(row.operator);
        $("#form_oct :input[name=field_b]").val(row.field_b);
        $("#form_oct :input[name=custom_b]").val(row.custom_b);
        $("#form_oct :input[name=order]").val(row.order);
        oct.field_visibility();
        $("#modal-oct").modal("show");
    },
    save: () => {
        $("#spinnerModal").modal("show");
        let data = $("#form_oct").serialize();
        $.post(site_url + 'form/oct_save', data, function (res) {
            if (typeof res.error !== 'undefined') {
                toastmsg(res.error, "danger");
            } else {
                $("#modal-oct").modal("hide");
                toastmsg(res, "success");
            }
            oct.list();
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
    delete: (id) => {
        if( confirm("¿Esta seguro que quiere borrar el registro?")){
            $("#spinnerModal").modal("show");
            $.post(site_url + 'form/oct_delete', { id:id }, function (res) {
                if (typeof res.error !== 'undefined') {
                    toastmsg(res.error, "danger");
                } else {
                    toastmsg(res, "success");
                }
                oct.list();
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