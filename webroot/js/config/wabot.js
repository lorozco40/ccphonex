var wabotobj = {
    pag: 0,
    reg: 0,
    rpp: 20,
    scripts_pag: 0,
    scripts_reg: 0,
    scripts_rpp: 5,
    bid: 0, // ID Bot activo
    obregs: {}, // Listado de bots
    ops: {}, // Listado de opciones
    scripts: [], //Listado de Scripts
    genord: {}, // JS no garantiza orden en sus objetos, array de orden de opciones por id bot
    accs: [], // Acciones
    truncate:(str,size) => {
        return (str.length > size) ? str.slice(0, size - 1) + '…' : str;
    },
    slug:(str) => {
         // Quitamos los acentos
        let strSA = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        // Reemplazamos los espacios por guiones
        let slug = strSA.replace(/\s+/g, "-");
        return slug;
    },
    getpag: function () {
        $("#spinnerModal").modal("show");
        $.get(site_url + 'wabot/list',
            { pag: wabotobj.pag, rpp: wabotobj.rpp, wid: wabotobj.wid }, function (data) {
                if (typeof data.error !== 'undefined') {
                    toastmsg(data.error, "danger");
                } else {
                    $("#libot .borrable").remove();
                    wabotobj.reg = data.regs;
                    wabotobj.pag = data.pag;
                    var html = "";
                    var cuenta = 0;
                    let label_ant = 'Sin agrupar';
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(row => {
                            cuenta++;
                            $("#nomcta").html(row.cuenta);
                            wabotobj.obregs[row.id] = row;
                            btnClase = (row.active == 1) ? 'danger' : 'success';
                            btnTexto = (row.active == 1) ? 'Desactivar' : 'Activar';
                            let labelSlug = wabotobj.slug(row.label);
                            if( label_ant != row.label ) {//En caso de nueva seccion de grupo
                                label_ant = row.label;
                                html += `
                                    <div class='table-row borrable'>
                                        <div class='table-cell'>
                                            <div class="btn ml-n2 p-0 text-primary">
                                                <span class='fa-stack small toggle-btn groupp-${labelSlug}' data-accion="hide" onclick="wabotobj.colapsarGrupos('${labelSlug}')">
                                                    <i class='fas fa-square fa-stack-2x'></i>
                                                    <i class='fas fa-angle-double-up fa-stack-1x fa-inverse icon-${'group-'+labelSlug}'></i>
                                                </span>
                                            </div>
                                            ${ (row.label == '') ? 'Sin agrupar' : row.label }
                                        </div>
                                        <div class='table-cell'></div><div class='table-cell'></div><div class='table-cell'></div>
                                        <div class='table-cell'></div><div class='table-cell'></div><div class='table-cell'></div>
                                        <div class='table-cell'></div><div class='table-cell'></div><div class='table-cell'></div>
                                    </div>
                                `;
                            }
                            html += "<div class='table-row borrable "+ 'grouph-'+labelSlug +"'><div class='table-cell'></div>" +
                                "<div class='table-cell'>" +
                                "<button type='button' data-id='" + row.id + "' data-wid='" + row.id_wacta + "' data-active='" + row.active + "' class='btn btn-" + btnClase + " actualizar-active-wabot'>" + btnTexto + "</button>" +
                                "</div>" +
                                "<div class='table-cell'>" + row.id + "</div>" +
                                "<div class='table-cell' id='wabot" + row.id + "'>" + row.name + "</div>" +
                                "<div class='table-cell wrapable'>" + row.intro + "</div>" +
                                "<div class='table-cell wrapable'>" + row.bye + "</div>" +
                                "<div class='table-cell wrapable'>" + row.out_of_time + "</div>" +
                                "<div class='table-cell'>" + row.creator + "</div>" +
                                "<div class='table-cell'>" + row.created_when + "</div>" +
                                "<div class='table-cell'>" +
                                "<button class='btn btn-dark edbot' data-id='" + row.id +
                                "'>Editar</a>" +
                                "<button data-wid='" + row.id_wacta + "' data-bid='" + row.id +
                                `' class='btn btn-primary botopsmod ml-3'>Opciones</button>
                                </div>
                            </div>`;
                        });
                        $("#libot").append(html);
                    }
                    paginacion(data.pag, data.regs, data.rpp, cuenta);
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
    guardarbot: () => {
        let data = $("#botform").serialize();
        $("#spinnerModal").modal("show");
        $.ajax({
            url: site_url + 'wabot/guardar',
            type: 'POST',
            data: data,
            dataType: 'json',
        })
        .done(function (data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                $("#wabotformModal").modal("hide");
                toastmsg(data);
                wabotobj.getpag();
            }
        })
        .fail(function (data) {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    traerops: condes => {
        $("#spinnerModal").modal("show");
        $.get(site_url + 'wabot/oplist', { wid: wabotobj.wid, bid: wabotobj.bid }, function (data) {
            if (typeof data.error !== 'undefined') {
                toastmsg(data.error, "danger");
            } else {
                wabotobj.ops[wabotobj.bid] = {};
                wabotobj.accs = data.accs;
                let tmpord = [];
                data.data.map(row => {
                    tmpord.push(row.id);
                    wabotobj.ops[wabotobj.bid][row.id] = row;
                    wabotobj.ops[wabotobj.bid][row.id].hijos = 0;
                    if ('undefined' !== typeof wabotobj.ops[wabotobj.bid][row.parent]) wabotobj.ops[wabotobj.bid][row.parent].hijos++;
                });
                wabotobj.genord[wabotobj.bid] = tmpord;
            }
            $("#spinnerModal").modal("hide");
            if (condes) wabotobj.despliegaops();
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
    despliegaops: () => {
        $("#libotop").html("");
        $("#mboptit").html("");
        $("input[class=solouno][data-id=0]").prop("checked", true);
        let html = "";
        wabotobj.genord[wabotobj.bid].map(ind => {
            let row = wabotobj.ops[wabotobj.bid][ind];
            html += "<div class='row hd-" + row.parent + "' data-tipo='show' data-ident='"+row.id+"' >" +
                "<div class='col-auto' >" +
                    "<div class='form-check'>" + "&nbsp;&nbsp;&nbsp;&nbsp;".repeat(row.depth) +
                        "<input type='checkbox' class='form-check-input solouno' data-id='" + row.id + "' value='1' />" +
                        "<span class='form-check-label mr-2'>" +
                            "<strong>" + row.option + "</strong> <i>["+row.id+"]</i> " + row.label + ", <> <i><strong>" + wabotobj.accs[row.action] + "</strong></i>" +
                        "</span>";
            if(row.hijos > 0) {
                html += "<div class='btn text-primary ml-n2 p-0 collaps' data-accion='hide' data-id='" + row.id + "'>" +
                            "<span class='fa-stack small'>" +
                                "<i class='fas fa-square fa-stack-2x'></i>" +
                                "<i class='fas fa-angle-double-up fa-stack-1x fa-inverse'></i>" +
                            "</span>" +
                        "</div>";
            }
                html += "<div class='btn ediop text-warning ml-n2 p-0' data-id='" + row.id + "'>" +
                            "<span class='fa-stack small'>" +
                                "<i class='fas fa-square fa-stack-2x'></i>" +
                                "<i class='fas fa-pen fa-stack-1x fa-inverse'></i>" +
                            "</span>" +
                        "</div>" +
                        "<div class='btn text-danger delop ml-n2 p-0' data-id='" + row.id + "'>" +
                            "<span class='fa-stack small'>" +
                                "<i class='fas fa-square fa-stack-2x'></i>" +
                                "<i class='fas fa-trash fa-stack-1x fa-inverse'></i>" +
                            "</span>" +
                        "</div>" +
                    "</div>" +
                "</div>" +
            "</div>";
        });
        $("#libotop").html(html);
        $("#mboptit").html($("#wabot" + wabotobj.bid).html());
        $("#formAdOp input[name=bid]").val(wabotobj.bid);
        $("#botinfo").html(wabotobj.bid);
        $("#opcionesModal").modal("show");
    },
    guardarbotop: () => {
        if ($("#formAdOp input[name=option]").val() == '' && $("#formAdOp textarea[name=label]").val() == '') {
            toastmsg("Todos los campos son requeridos!", "danger");
            return;
        }
        $("#spinnerModal").modal("show");
        let data = $("#formAdOp").serialize();
        $.post(site_url + "wabot/opguardar", data, function (r) {
            $("#spinnerModal").modal("hide");
            if (typeof r.error !== "undefined") {
                toastmsg(r.error, "danger");
            } else {
                wabotobj.resetformopt();
                wabotobj.traerops(true);
            }
        }, "json")
        .fail(function () {
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    },
    borrarop: idop => {
        if( confirm("¿Estás seguro que quieres eliminar esta opción?") ) {
            $("#spinnerModal").modal("show");
            $.get(site_url + 'wabot/opborrar', { id: idop }, function (data) {
                $("div[data-ident=" + idop + "]").remove();
                delete wabotobj.ops[wabotobj.bid][idop];
                let tmpind = wabotobj.genord[wabotobj.bid].indexOf(String(idop));
                wabotobj.genord[wabotobj.bid].splice(tmpind, 1);
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
    actualizarcampoactivobot: function (id, wid, active) {
        var mensajes = "";
        if (id == "") mensajes += "- No se obtuvo el campo identificador del bot. <br>";
        if (wid == "") mensajes += "- Por favor elige una cuenta primero!.";
        if (active != 1 && active != 0) mensajes += "- El campo active no es válido.";

        if (mensajes.length == 0) {
            $("#spinnerModal").modal("show");
            data = "";
            data += "id=" + id;
            data += "&wid=" + wid;
            active = active == 1 ? 0 : 1;
            //Invierto los valores de active, pues si, está activo lo va ha desactivar y si está desactivado lo va ha activar
            data += "&active=" + active;
            $.post(site_url + "wabot/actualizar_campo_activo", data, function (resultado) {
                $("#spinnerModal").modal("hide");
                if (typeof resultado.error !== "undefined") {
                    toastmsg(resultado.error, "danger");
                } else {
                    toastmsg(resultado);
                    wabotobj.getpag();
                }
            }, "json")
            .fail(function () {
                $("#spinnerModal").modal("hide");
                if (typeof data.responseJSON !== "undefined" && typeof data.responseJSON.error !== "undefined") {
                    toastmsg(data.responseJSON.error, "danger");
                } else {
                    toastmsg("Error de red, verifica tu conexión a internet.", "danger");
                }
            });
        } else {
            toastmsg(mensaje, "danger");
            return;
        }
    },
    controlar_hijos: (id, visibilidad) => {
        let elementos = $('div.hd-'+id);
        elementos.each(function() {
            id = $(this).attr('data-ident');
            if (visibilidad == 'hide') {
                $(this).hide(200);
            } else {
                $(this).find('.text-secondary').removeClass('text-secondary').addClass('text-primary').data('accion', 'hide');
                $(this).find('.fa-angle-double-down').removeClass('fa-angle-double-down').addClass('fa-angle-double-up');
                $(this).find('.fa-circle').removeClass('fa-circle').addClass('fa-square');
                $(this).show(200);
            }
            wabotobj.controlar_hijos(id, visibilidad);
        });
    },
    show_options: id => {
        // 7 = redirigir, 8 = script
        if ( id == 7 ) { //Mostramos redirigir y ocultamos script
            $("#head_id_script").hide();
            $("#head_redirect").show();
        } else if (id == 8 ){ //Mostramos script y ocultamos redirigir
            $("#head_id_script").show();
            $("#head_redirect").hide();
        } else { //Ocultamos redirigir y script
            $("#head_id_script, #head_redirect").hide();
        }    
    },
    resetformopt: () => {
        wabotobj.show_options(0);
        $("#formAdOp").trigger("reset");
        $("#formAdOp input[name=id]").val("0");
        $("#formAdOp button[name=agregar]").removeClass("d-none");
        $("#formAdOp button[name=actualizar]").addClass("d-none");
        $("#formAdOp button[name=cancelar]").addClass("d-none");
    },
    //===== APARTADO DE SCRIPTS =====//
    scriptsModal:(bot_id) => {
        wabotobj.scriptsFormReset(0);
        //Cargamos los scripts
        wabotobj.scriptsList();
    },
    scriptsFormReset: () => {
        wabotobj.show_options(0);
        $("#scriptForm").trigger("reset");
        $("#scriptForm input[name=id]").val("0");
        $("#scriptForm button[name=agregar]").removeClass("d-none");
        $("#scriptForm button[name=actualizar]").addClass("d-none");
        $("#scriptForm button[name=cancelar]").addClass("d-none");
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
    colapsarGrupos: (label) => {
        let elementos = $('div.grouph-'+label);
        let padre = $('.groupp-'+label);
        let accion = $(padre).attr('data-accion');
        if (accion == 'hide') {
            $(padre).attr('data-accion', 'show')
            $(padre).find('.fa-angle-double-up').removeClass('fa-angle-double-up').addClass('fa-angle-double-down');
            $(elementos).hide(200);
        } else {
            $(padre).attr('data-accion', 'hide')
            $(padre).find('.fa-angle-double-down').removeClass('fa-angle-double-down').addClass('fa-angle-double-up');
            $(padre).find('.fa-circle').removeClass('fa-circle').addClass('fa-square');
            $(elementos).show(200);
        }
    },
    scriptsList:() => {
        let options = `<option value=''>Seleccione</option>`;
        let options_scripts = `<option value=''>Seleccione</option>`;
        $("#scriptsList").html("");
        let html = '';
        let data = {
            pag: wabotobj.scripts_pag,
            rpp: wabotobj.scripts_rpp
        };
        $.post(site_url + "wabot/scripts_list", data, function (resultado) {
            wabotobj.scripts = [];
            let rows = resultado.array_scripts;
            let row = [];
            $.each(resultado.campanas, function(index, opcion) {
                options += `<option value='${opcion.id}'>${opcion.name}</option>`; 
            });
            $("#id_campaign").html(options);
            $.each(resultado.scripts_options, function(index, opcion) {
                options_scripts += `<option value='${opcion.id}'>${opcion.name}</option>`; 
            });
            $(".select-scripts").html(options_scripts);
            for (var key in rows) {
                row = rows[key];
                wabotobj.scripts[row.id] = row;
                html += `
                <tr class='table-row'>
                    <td class='table-cell'>
                        <div class="btn ml-n2 p-0 text-primary">
                            <span class='fa-stack small toggle-btn' onclick="wabotobj.colapsar('${'met-api-'+row.id}')">
                                <i class='fas fa-square fa-stack-2x'></i>
                                <i class='fas fa-angle-double-right fa-stack-1x fa-inverse icon-${'met-api-'+row.id}'></i>
                            </span>
                        </div>
                        ${row.nombre}
                    </td>
                    <td class='table-cell'>${row.campaign}</td>
                    <td class='table-cell' title="${row.siespera}">${wabotobj.truncate(row.siespera, 25)}</td>
                    <td class='table-cell'>${row.sibien}</td>
                    <td class='table-cell'>${row.simal}</td>
                    <td class='table-cell'>${(row.active == 1) ? 'Activo' : '' }</td>
                    <td class='table-cell'>
                        <div onclick="wabotobj.scriptActionNew(${row.id})" class='btn text-primary ml-n2 p-0'>
                            <span class='fa-stack small'>
                                <i class='fas fa-square fa-stack-2x'></i>
                                <i class='fas fa-plus fa-stack-1x fa-inverse'></i>
                            </span>
                        </div>
                        <div data-id="${row.id}" data-name="${row.nombre}" class='btn text-warning script_edit ml-n2 p-0'>
                            <span class='fa-stack small'>
                                <i class='fas fa-square fa-stack-2x'></i>
                                <i class='fas fa-pen fa-stack-1x fa-inverse'></i>
                            </span>
                        </div>
                        <div data-id="${row.id}" class='btn text-danger script_delete ml-n2 p-0'>
                            <span class='fa-stack small'>
                                <i class='fas fa-square fa-stack-2x'></i>
                                <i class='fas fa-trash fa-stack-1x fa-inverse'></i>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr class="${'met-api-'+row.id}" style="display: none;">
                    <td colspan="7" class="ps-5">
                        <table class="table m-0">
                `;
                if( row.steps ) {
                    html += `
                        <tr class='table-row'>
                            <th>Paso</th>
                            <th>Campo</th>
                            <th>Variable</th>
                            <th>Tipo</th>
                            <th>Modificador</th>
                            <th>Condición</th>
                            <th>Activo</th>
                            <th>Orden</th>
                            <th></th>
                        </tr>
                    `;
                    //Pintamos las steps de ese script
                    row.steps.map( row2 => {
                        html += `
                        <tr class='table-row'>
                            <td class='table-cell'>${row2.paso}</td>
                            <td class='table-cell' title="${row2.camp}">${wabotobj.truncate(row2.camp, 25)}</td>
                            <td class='table-cell' title="${row2.varb}">${wabotobj.truncate(row2.varb, 40)}</td>
                            <td class='table-cell'>${row2.tipo}</td>
                            <td class='table-cell'>${row2.modi}</td>
                            <td class='table-cell'>${row2.cond}</td>
                            <td class='table-cell'>${(row2.active == 1) ? 'Activo' : ''}</td>
                            <td class='table-cell'>${row2.orden}</td>
                            <td class='table-cell'>
                                <div onclick="wabotobj.scriptActionEdit(${row.id}, ${row2.id})" class='btn text-warning ml-n2 p-0'>
                                    <span class='fa-stack small'>
                                        <i class='fas fa-square fa-stack-2x'></i>
                                        <i class='fas fa-pen fa-stack-1x fa-inverse'></i>
                                    </span>
                                </div>
                                <div onclick="wabotobj.scriptStepDelete(${row2.id})" class='btn text-danger ml-n2 p-0'>
                                    <span class='fa-stack small'>
                                        <i class='fas fa-square fa-stack-2x'></i>
                                        <i class='fas fa-trash fa-stack-1x fa-inverse'></i>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        `;
                    });
                }
                else {
                    html += `<tr><td colspan="9" class="text-center">No hay pasos para este script</td></tr>`
                }
                html += `
                        </table>
                    </td>
                </tr>
                `;
            }
            $("#scriptsList").html(html);
            wabotobj.scripts_data = wabotobj.scripts;
            wabotobj.scripts_pag = resultado.pag;
            wabotobj.scripts_rpp = resultado.rpp;
            wabotobj.scripts_tot = resultado.tot;
            const keys = Object.keys(wabotobj.scripts);
            const cta = keys.length;
            paginacion(wabotobj.scripts_pag, wabotobj.scripts_tot, wabotobj.scripts_rpp, cta, 'paginacion_scripts');
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
    scriptSave: () => {
        $("#spinnerModal").modal("show");
        let params = $("#scriptForm").serialize();
        $.post(site_url + "wabot/script_save", params, function (r) {
            $("#spinnerModal").modal("hide");
            if (typeof r.error !== "undefined") {
                toastmsg(r.error, "danger");
            } else {
                toastmsg(r);
                wabotobj.scriptsFormReset();
                wabotobj.scriptsList();
                $(".name_script").html( 'Nuevo' );
            }
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
    scriptDelete:(id)=> {
        if( confirm("¿Estás seguro que quieres eliminar este script y lo relaciona a el?") ) {
            $("#spinnerModal").modal("show");
            $.post(site_url + "wabot/script_delete", {id: id}, function (r) {
                $("#spinnerModal").modal("hide");
                let tipo = (r.tipo == 'error') ? 'danger' : 'success';
                toastmsg(tipo, r.msg);
                wabotobj.scriptsList();
                if( r.tipo == 'ok')
                    wabotobj.scriptsFormReset();
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
    scriptStepDelete: (id)=> {
        if( confirm("¿Estás seguro que quieres eliminar este registro?") ) {
            $("#spinnerModal").modal("show");
            $.post(site_url + "wabot/script_delete_step", {id: id}, function (r) {
                $("#spinnerModal").modal("hide");
                if (typeof r.error !== "undefined") {
                    toastmsg(r.error, "danger");
                } else {
                    toastmsg(r);
                    wabotobj.scriptsFormReset();
                    wabotobj.scriptsList();
                }
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
    showFormOpt:()=> {
        $("#scriptForm").hide();
        $(".scripts-list").hide();
        $("#scriptActionsForm").hide();
        $("#scriptsList").hide();
        $("#formAdOp").show();
        $("#tab_opciones").addClass("active");
        $("#tab_scripts").removeClass("active");
    },
    showFormScript:()=> {
        $("#scriptForm").show();
        $("#scriptsList").show();
        $(".scripts-list").show();
        $("#formAdOp").hide();
        $("#tab_opciones").removeClass("active");
        $("#tab_scripts").addClass("active");
    },
    //Segun el valor que tenga el campo Paso, se mostraran o ocultaran los campos del formulario scriptActionsForm
    estructurCampos : () => {
        let paso = $("#scriptActionsForm select[name=paso]").val();
        //Deshabilitamos y ocultamos todo
        $(".col_camp").hide();              $('.col_camp').prop('disabled', true); 
        $(".col_modi_mensaje").hide();      $('.col_modi_mensaje').prop('disabled', true);
        $(".col_varb").hide();              $('.col_varb').prop('disabled', true);
        $(".col_tipo").hide();              $('.col_tipo').prop('disabled', true);
        $(".col_tipo_request").hide();      $('.col_tipo_request').prop('disabled', true);
        $(".col_modi_modificador").hide();  $('.col_modi_modificador').prop('disabled', true);
        $(".col_cond").hide();              $('.col_cond').prop('disabled', true);
        switch( paso ) {
            case 'borravar':
                $(".col_camp").show();              $('.col_camp').prop('disabled', false);
                $(".col_varb").show();              $('.col_varb').prop('disabled', false);
                break;
            case 'mensaje':
                $(".col_modi_mensaje").show();      $('.col_modi_mensaje').prop('disabled', false);
                $(".col_cond").show();              $('.col_cond').prop('disabled', false);
                break;
            case 'request':
                $(".col_camp").show();              $('.col_camp').prop('disabled', false);
                $(".col_varb").show();              $('.col_varb').prop('disabled', false);
                $(".col_tipo_request").show();      $('.col_tipo_request').prop('disabled', false);
                $(".col_modi_modificador").show();  $('.col_modi_modificador').prop('disabled', false);
                $(".col_cond").show();              $('.col_cond').prop('disabled', false);
                break;
            case 'pasavar':
                $(".col_camp").show();              $('.col_camp').prop('disabled', false);
                $(".col_varb").show();              $('.col_varb').prop('disabled', false);
                $(".col_tipo").show();              $('.col_tipo').prop('disabled', false);
                $(".col_modi_modificador").show();  $('.col_modi_modificador').prop('disabled', false);
                $(".col_cond").show();              $('.col_cond').prop('disabled', false);
                break;
            case 'variable':
                $(".col_camp").show();              $('.col_camp').prop('disabled', false);
                $(".col_varb").show();              $('.col_varb').prop('disabled', false);
                $(".col_tipo").show();              $('.col_tipo').prop('disabled', false);
                $(".col_modi_modificador").show();  $('.col_modi_modificador').prop('disabled', false);
                $(".col_cond").show();              $('.col_cond').prop('disabled', false);
                break;
            case 'redir':
                $(".col_varb").show();              $('.col_varb').prop('disabled', false);
                $(".col_cond").show();              $('.col_cond').prop('disabled', false);
                break;
        }
    },
    scriptActionNew:(id_whatsapp_bot_script) => {
        //Obtenemos el Script
        let script = wabotobj.scripts[id_whatsapp_bot_script];
        $("#scriptActionsForm button[name=cancelar]").removeClass("d-none");
        $("#scriptForm").trigger('reset');
        $("#scriptActionsForm").trigger('reset');
        $("#scriptForm").hide();
        //actualizamos la visibilidad de los campos
        wabotobj.estructurCampos();
        //Mostramos el formulario
        $("#scriptActionsForm").show();
        $("#scriptActionsForm input[name=script]").val(script.nombre);
        $("#scriptActionsForm input[name=id_whatsapp_bot_script]").val(id_whatsapp_bot_script);
        $("#scriptActionsForm input[name=id]").val(0);
        $("#scriptActionsForm input[name=active]").prop("checked", true);
    },
    scriptActionEdit: (id_whatsapp_bot_script, id_whatsapp_bot_scr_steps) => {
        //Obtenemos el Script
        let script = wabotobj.scripts[id_whatsapp_bot_script];
        //obtenemos la accion de ese script
        let paso = script.steps.find(action => action.id == id_whatsapp_bot_scr_steps);
        active = (paso.active == 1) ? true : false;
        //Cambiamos la visibilidad de los formularios y los reseteamos
        $("#scriptActionsForm").trigger("reset");
        $("#scriptForm").trigger('reset');
        $("#scriptForm").hide();
        $("#scriptActionsForm").show();
        //Modificamos los labels de los elementos segun el tipo
        $("#scriptActionsForm select[name=paso]").val(paso.paso);
        $("#scriptActionsForm input[name=id_whatsapp_bot_script").val(paso.id_whatsapp_bot_script);
        $("#scriptActionsForm input[name=id").val(paso.id);
        $("#scriptActionsForm input[name=script").val(script.nombre);
        wabotobj.estructurCampos();
        $("#scriptActionsForm [name=camp]").val(paso.camp);
        $("#scriptActionsForm input[name=varb]").val(paso.varb);
        $("#scriptActionsForm select[name=tipo]").val(paso.tipo);
        $("#scriptActionsForm textarea[name=modi]").val(paso.modi);
        //$("#scriptActionsForm input[name=modi]").val(paso.modi);
        $("#scriptActionsForm input[name=cond]").val(paso.cond);
        $("#scriptActionsForm input[name=orden").val(paso.orden);
        $("#scriptActionsForm input[name=active]").prop("checked", active);
        //Botones
        $("#scriptActionsForm button[name=agregar]").addClass("d-none");
        $("#scriptActionsForm button[name=actualizar]").removeClass("d-none");
        $("#scriptActionsForm button[name=cancelar]").removeClass("d-none");
    },
    scriptActionSave:() => {
        $("#spinnerModal").modal("show");
        let params = $("#scriptActionsForm").serialize();
        $.post(site_url + "wabot/script_step_save", params, function (r) {
            $("#spinnerModal").modal("hide");
            if (typeof r.error !== "undefined") {
                toastmsg(r.error, "danger");
            } else {
                toastmsg(r);
                wabotobj.scriptActionFormHide();
                wabotobj.scriptsList();
                $("#scriptForm").trigger('reset');
                $("#scriptForm").show();
                $(".name_script").html( 'Nuevo' );
            }
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
    //Ocultar formulario steps Forms
    scriptActionFormHide:() => {
        $("#scriptActionsForm").trigger("reset");
        $("#scriptActionsForm").hide();
    },
    scriptsPaginationRpp: (rpp) => {
        wabotobj.scripts_pag = 0;
        wabotobj.scripts_rpp = rpp;
        wabotobj.scriptsList();
    } 
}

$(document).ready(function () {
    wabotobj.wid = $("input[name=wid]").val();
    wabotobj.getpag();
    $(document).on("click", "#nubot", function () {
        $(".nonu").addClass("d-none");
        $("#botform").trigger("reset");
        $("#botform input[name=id]").val(0);
        $("#wabotformModal").modal("show");
    });
    $(document).on("click", ".edbot", function () {
        var id = $(this).data("id");
        $(".nonu").removeClass("d-none");
        Object.entries(wabotobj.obregs[id]).forEach(([i, val]) => {
            $("#botform input[type=text][name=" + i + "]").val(val);
            $("#botform input[type=hidden][name=" + i + "]").val(val);
            $("#botform input[type=email][name=" + i + "]").val(val);
            $("#botform select[name=" + i + "]").val(val);
            $("#botform textarea[name=" + i + "]").val(val);
            $("#botform select[name=" + i + "]").val(val);
            cheko = (val == '0') ? false : true;
            $("#botform input[type=checkbox][name=" + i + "]").prop('checked', cheko);
        });
        $("#wabotformModal").modal("show");
    });
    $(document).on("submit", "#botform", function (e) {
        e.preventDefault();
        wabotobj.guardarbot();
    });
    $(document).on("click", "#paginacion .page-link", function (e) {
        e.preventDefault();
        wabotobj.pag = $(this).data('pag');
        wabotobj.getpag();
    });
    $(document).on("click", "#paginacion_scripts .page-link", function (e) {
        e.preventDefault();
        wabotobj.scripts_pag = $(this).data('pag');
        wabotobj.scriptsList();
    });
    $(document).on("change", "#elirpp", function () {
        wabotobj.pag = 0;
        wabotobj.rpp = $(this).val();
        wabotobj.getpag();
    });
    $(document).on("click", ".botopsmod", function () {
        wabotobj.bid = $(this).data("bid");
        wabotobj.resetformopt();
        wabotobj.scriptsModal(wabotobj.bid);
        wabotobj.showFormOpt();
        if ('undefined' === typeof wabotobj.ops[wabotobj.bid]) {
            wabotobj.traerops(true);
        } else {
            wabotobj.despliegaops();
        }
    });
    $(document).on("click", ".solouno", function () {
        $(".solouno").prop("checked", false);
        $(this).prop("checked", true);
        let id = $(this).data("id");
        $("#formAdOp input[name=parent]").val(id);
    });
    $(document).on("submit", "#formAdOp", function(e) {
        e.preventDefault();
        wabotobj.guardarbotop();
    });
    $(document).on("click", ".delop", function (e) {
        e.preventDefault();
        let id = $(this).data("id");
        wabotobj.borrarop(id);
    });
    $(document).on("click", ".actualizar-active-wabot", function (e) {
        e.preventDefault();
        id = $(this).data("id");
        wid = $(this).data("wid");
        active = $(this).data("active");
        wabotobj.actualizarcampoactivobot(id, wid, active);
    });
    $(document).on("click", ".collaps", function(){
        let id = $(this).data('id');
        let visibilidad = $(this).data('accion');
        let contra = (visibilidad == 'hide') ? 'show' : 'hide';
        wabotobj.controlar_hijos(id, visibilidad);
        $(this).data('accion', contra);
        $(this).toggleClass('text-primary text-secondary');
        $(this).children().children(".fa-stack-1x").toggleClass('fa-angle-double-up fa-angle-double-down');
        $(this).children().children(".fa-stack-2x").toggleClass('fa-square fa-circle');
    });
    $(document).on("click", ".ediop", function() {
        wabotobj.showFormOpt()
        let id = $(this).data("id");
        let row = wabotobj.ops[wabotobj.bid][id];
        $("#formAdOp").trigger("reset");
        $("#formAdOp button[name=agregar]").addClass("d-none");
        $("#formAdOp button[name=actualizar]").removeClass("d-none");
        $("#formAdOp button[name=cancelar]").removeClass("d-none");
        $("#formAdOp input[name=id").val(row.id);
        $("#formAdOp input[name=parent").val(row.parent);
        $("#formAdOp input[name=option").val(row.option);
        $("#formAdOp textarea[name=label").val(row.label);
        $("#formAdOp select[name=action").val(row.action);
        $("#formAdOp input[name=redirect").val(row.redirect);
        $("#formAdOp select[name=id_script").val(row.id_script);
        $(".solouno").prop("checked", false)
        $(".solouno[data-id=" + row.parent + "]").prop("checked", true);
        wabotobj.show_options(row.action);
    })
    //Scripts
    $(document).on("submit", "#scriptForm", function(e) {
        e.preventDefault();
        wabotobj.scriptSave();
    })
    //Editar un script
    $(document).on("click", ".script_edit", function() {
        let id = $(this).attr("data-id");
        let row = wabotobj.scripts[id];
        let active = (row.active == 1) ? true : false;
        $(".name_script").html( $(this).attr("data-name") );
        //eventos para sripts steps
        $("#scriptActionsForm").trigger('reset');
        $("#scriptActionsForm").hide();
        //Eventos para scripts
        $("#scriptForm").show();
        $("#scriptForm").trigger("reset");
        $("#scriptForm button[name=agregar]").addClass("d-none");
        $("#scriptForm button[name=actualizar]").removeClass("d-none");
        $("#scriptForm button[name=cancelar]").removeClass("d-none");
        $("#scriptForm input[name=id").val(row.id);
        $("#id_campaign").val(row.id_campaign);
        $("#scriptForm input[name=nombre").val(row.nombre);
        $("#scriptForm input[name=siespera").val(row.siespera);
        $("#scriptForm input[name=sibien").val(row.sibien);
        $("#scriptForm input[name=simal").val(row.simal);
        $("#scriptForm input[name=active]").prop("checked", active);
    })
    //Cancelar Edicion
    $(document).on("click", ".script_cancel", function() {
        $("#scriptForm").trigger("reset");
        $("#scriptForm input[name=active]").prop("checked", true);
        $(".name_script").html( 'Nuevo' );
        $("#scriptForm input[name=id]").val("0");
        $("#scriptForm button[name=agregar]").removeClass("d-none");
        $("#scriptForm button[name=actualizar]").addClass("d-none");
        $("#scriptForm button[name=cancelar]").addClass("d-none");
    });
    //Cancelar Edicion Script Action
    $(document).on("click", ".script_action_cancel", function() {
        $("#scriptActionsForm").trigger("reset");
        $(".name_script").html( 'Nuevo' );
        $("#scriptActionsForm").hide();
        $("#scriptActionsForm input[name=id]").val("0");
        $("#scriptActionsForm button[name=agregar]").removeClass("d-none");
        $("#scriptActionsForm button[name=actualizar]").addClass("d-none");
        $("#scriptActionsForm button[name=cancelar]").addClass("d-none");
        $("#scriptForm").trigger('reset');
        $("#scriptForm").show();
    });
    //Eliminar Script
    $(document).on("click", ".script_delete", function() {
        let id = $(this).attr("data-id");
        wabotobj.scriptDelete(id);
    });
    $(document).on("click", ".script_action_delete", function() {
        let id = $(this).attr("data-id");
        wabotobj.scriptStepDelete(id);
    });
    //Agregamos un Scripts Action
    $(document).on("submit", "#scriptActionsForm", function(e) {
        e.preventDefault();
        wabotobj.scriptActionSave();
    });
    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();
        wabotobj.scripts_pag = $(this).data('pag');
        wabotobj.scriptsList();
    });
});