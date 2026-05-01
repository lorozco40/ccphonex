<div class="container-fluid main">
    <div class="row">
        <div class="col-auto">
            <h2>
                Whatsapp Bots
                <button type="button" class="btn btn-primary" id="nubot">Nuevo</button>
            </h2>
            <span id="nomcta"></span>
        </div>
        <div class="col"></div>
    </div>
    <hr>
    <div class="table table-striped" id="libot">
        <div class="table-header-group">
            <div class="table-cell">Grupo</div>
            <div class="table-cell text-center">Activo</div>
            <div class="table-cell">ID Bot</div>
            <div class="table-cell">Nombre</div>
            <div class="table-cell">Saludo</div>
            <div class="table-cell">Despedida</div>
            <div class="table-cell">Fuera de Horario</div>
            <div class="table-cell">Creador</div>
            <div class="table-cell">Desde</div>
            <div class="table-cell text-center">Acción</div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div id="paginacion"></div>
        </div>
        <div class="col text-right">
            <p>Registros por página:</p>
            <select class="form-control" id="elirpp" style="max-width:5em;float:right;">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
</div>
<div class="modal fade" id="wabotformModal" tabindex="-1" role="dialog" aria-labelledby="wabotformModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open('whastapp/botguardar', ['class' => 'form', 'id' => 'botform'], ['id' => 0]); ?>
                <input type="hidden" name="wid" value="<?=$id_wacta?>" />
                <div class="modal-header">
                    <h5 class="modal-title">Bot</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <br />
                    <div class="row">
                        <div class="col">
                            <label for="email">Nombre</label>
                            <input class="form-control" type="text" name="name" />
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <label for="label">Grupo</label>
                            <input class="form-control" type="text" name="label" />
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <label for="intro">Saludo (max 254)</label>
                            <textarea class="form-control" name="intro">Hola</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <label for="bye">Despedida (max 254)</label>
                            <textarea class="form-control" name="bye">Hasta pronto</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <label for="bye">Fuera de Horario (max 254)</label>
                            <textarea class="form-control" name="out_of_time">Fuera de Horario</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <label for="wait_time">Tiempo de espera</label>
                            <select name="wait_time" class="form-control" id="wait_time">
                                <option value="1">1 min</option>
                                <option value="5">5 min</option>
                                <option value="10">10 min</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <label for="ini_script">Script de Inicio</label>
                            <select type="text" class="form-control select-scripts" name="ini_script">
                                <option value="">Seleccione</option>
                                <?php foreach($scripts as $item): ?>
                                    <option value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="modal fade" id="opcionesModal" tabindex="-1" role="dialog" aria-labelledby="opcionesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="opcionesModalLabel">Bot opciones <strong id="mboptit"></strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <div onclick="wabotobj.showFormOpt()" id="tab_opciones" class="nav-link active" style="cursor: pointer;" aria-current="page">Opciones</div>
                    </li>
                    <li class="nav-item">
                        <div onclick="wabotobj.showFormScript()" id="tab_scripts" class="nav-link" style="cursor: pointer;">Scripts</div>
                    </li>
                </ul>
                <form class="form" id="formAdOp">
                    <input type="hidden" name="wid" value="<?=$id_wacta?>" />
                    <input type="hidden" name="id" value="0" />
                    <input type="hidden" name="bid" value="0" />
                    <input type="hidden" name="parent" value="0" />
                    <div class="row">
                        <div class="col-12 col-sm-2 col-lg-1">
                            <label for="option">Opción</label>
                            <input type="text" class="form-control" name="option" />
                        </div>
                        <div class="col-12 col-sm-10 col-lg-5">
                            <label for="Texto">Texto</label>
                            <textarea class="form-control" name="label" rows="1"></textarea>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="accion">Acción</label>
                            <select class="form-control" name="action" onchange="wabotobj.show_options(this.value)">
                                <option value="1">Submenu</option>
                                <option value="2">Operador</option>
                                <option value="3">Terminar</option>
                                <option value="4">Sub/Terminar</option>
                                <option value="6">Terminar/Encuesta</option>
                                <option value="7">Redirigir</option>
                                <option value="8">Script</option>
                                <option value="5">Menú inicio</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3" id="head_redirect">
                            <label for="redirect">Redirección</label>
                            <input type="text" class="form-control" name="redirect" />
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3" id="head_id_script">
                            <label for="id_script">Script</label>
                            <select type="text" class="form-control select-scripts" name="id_script">
                                <option value="0">Seleccione</option>
                                <?php foreach($scripts as $item): ?>
                                    <option value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <button class="btn btn-primary" type="submit" name="agregar">Agregar</button>
                            <button class="btn btn-primary d-none" type="submit" name="actualizar">Actualizar</button>
                            <button class="btn btn-secondary d-none ml-3" type="button" name="cancelar" onclick="wabotobj.resetformopt()">Cancelar</button>
                        </div>
                    </div>
                </form>
                <form class="form" id="scriptForm">
                    <label class="font-weight-bold">Script: <span class="name_script">Nuevo</span></label>
                    <input type="hidden" class="form-control" name="id" />
                    <div class="row mb-3">
                        <div class="col-12 col-sm-5 col-lg-2">
                            <label for="id_campaign2">Campaña</label>
                            <select class="form-control" name="id_campaign" id="id_campaign">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-7 col-lg-3">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" name="nombre" />
                        </div>
                        <div class="col-12 col-sm-2 col-sm-5 col-lg-2">
                            <label for="siespera">Si Espera</label>
                            <input type="text" class="form-control" name="siespera" />
                        </div>
                        <div class="col-12 col-sm-2 col-lg-2">
                            <label for="sibien">Si Bien</label>
                            <input type="text" class="form-control" name="sibien" />
                        </div>
                        <div class="col-12 col-sm-2 col-lg-2">
                            <label for="simal">Si Mal</label>
                            <input type="text" class="form-control" name="simal" />
                        </div>
                        <div class="col-12 col-sm-3 col-lg-1">
                            <div class="form-check mt-5">
                                <input class="form-check-input" type="checkbox" value="1" name="active" id="form_script_active" checked>
                                <label class="form-check-label" for="form_script_active">Activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit" name="agregar">Agregar</button>
                            <button class="btn btn-primary d-none" type="submit" name="actualizar">Actualizar</button>
                            <button class="btn btn-secondary d-none ml-3 script_cancel" type="button" name="cancelar">Cancelar</button>
                        </div>
                    </div>
                </form>
                <form class="form" id="scriptActionsForm">
                    <input type="hidden" class="form-control" name="id_whatsapp_bot_script" />
                    <input type="hidden" class="form-control" name="id" />
                    <div class="row">
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label>Script</label>
                            <input type="text" class="form-control" name="script" disabled />
                        </div>
                        <!-- PASO -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="sorce">Paso</label>
                            <select name="paso" class="form-control" onchange="wabotobj.estructurCampos()">
                                <option value="">-- Seleccionar --</option>
                                <option value="borravar">borravar</option>
                                <option value="mensaje">mensaje</option>
                                <option value="pasavar">pasavar</option>
                                <option value="redir">redir</option>
                                <option value="request">request</option>
                                <option value="variable">variable</option>
                            </select>
                        </div>
                        <!-- CAMPO -->
                        <div class="col-12 col-sm-6 col-lg-4 col_camp">
                            <label for="camp" class="col_camp ">Tipo de campo</label> 
                            <select name="camp" class="form-control col_camp">
                                <option value="">-- Seleccionar --</option>
                                <option value="permanent">Permanent</option>
                                <option value="temporal">Temporal</option>
                                <option value="secure">Secure</option>
                                <option value="file">File</option>
                            </select>
                        </div>
                        <!-- VARIABLE -->
                        <div class="col-12 col-sm-6 col-lg-4 col_varb">
                            <label for="varb">Variable</label>
                            <input type="text" class="form-control col_varb" name="varb" />
                        </div>
                        <!-- TIPO DE DATO -->
                        <div class="col-12 col-sm-6 col-lg-4 col_tipo col_tipo_request" >
                            <label for="tipo" class="col_tipo">Tipo de dato</label>
                            <select name="tipo" class="form-control col_tipo">
                                <option value="">-- Seleccionar --</option>
                                <option value="string">String</option>
                                <option value="bool">Bool</option>
                                <option value="int">Int</option>
                            </select>
                            <label for="tipo" class="col_tipo_request">Request</label>
                            <select name="tipo" class="form-control col_tipo_request">
                                <option value="">-- Seleccionar --</option>
                                    <?php 
                                    $aux_name = "";
                                    foreach($list_extapi_met as $row):
                                        if($aux_name != $row->name) {
                                            if($aux_name != "") echo '</optgroup>';
                                            $aux_name = $row->name;
                                            echo "<optgroup label='$row->name'>";
                                        }
                                    ?>
                                        <option value="<?= $row->id ?>"><?= $row->name ?>: <?= $row->info ?></option>
                                    <?php endforeach ?>
                                </optgroup>
                            </select>
                        </div>
                        <!-- MODIFICADOR -->
                        <div class="col-12 col-sm-6 col-lg-4 col_modi_modificador col_modi_mensaje">
                            <label for="modi" class="col_modi_mensaje">Mensaje</label> 
                            <textarea class="form-control col_modi_mensaje" name="modi" rows="2"></textarea>
                            <label for="modi" class="col_modi_modificador">Modificador</label>
                            <textarea class="form-control col_modi_modificador" name="modi" rows="2"></textarea>
                        </div>
                        <!-- CONDICION -->
                        <div class="col-12 col-sm-6 col-lg-4 col_cond">
                            <label for="cond">Condición</label>
                            <input type="text" class="form-control col_cond" name="cond" />
                        </div>
                        <!-- ORDEN -->
                        <div class="col-12 col-sm-3 col-lg-2">
                            <label for="orden">Orden</label>
                            <input type="number" min="0" max="99" class="form-control" name="orden" />
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2">
                            <div class="form-check mt-5">
                                <input class="form-check-input" type="checkbox" value="1" name="active" id="step_active">
                                <label class="form-check-label" for="step_active">Activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4 mt-3">
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit" name="agregar">Agregar</button>
                            <button class="btn btn-primary d-none" type="submit" name="actualizar">Actualizar</button>
                            <button class="btn btn-secondary ml-3 script_action_cancel" type="button" name="cancelar">Cancelar</button>
                        </div>
                    </div>
                </form>
                <div class="scripts-list">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Campaña</th>
                                <th>Si espera</th>
                                <th>Si bien</th>
                                <th>Si mal</th>
                                <th>Activo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="scriptsList">
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col">
                            <div id="paginacion_scripts"></div>
                        </div>
                        <div class="col text-right">
                            <p>Registros por página:</p>
                            <select class="form-control" onchange="wabotobj.scriptsPaginationRpp(this.value)" style="max-width:5em;float:right;">
                                <option value="5" selected>5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="lasops">
                    <br />
                    <input type="checkbox" data-id="0" checked="checked" name="primaria" class="solouno" value="1" />
                    <label for="primaria">Opción de menú base <span id="botinfo"></span>-0</label>
                    <hr class="mt-0" />
                    <div id="libotop" style="max-height:400px;overflow:hidden auto;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
