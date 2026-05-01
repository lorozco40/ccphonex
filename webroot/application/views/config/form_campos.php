<div class="container-fluid main">
    <?php $cont = $this->router->class; $pag = ($form->crm) ? "CRM" : "Formulario" ?>
    <div class="row">
        <div class="col">
            <h3>Detalles del <?=$pag?></h3>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <span class="text text-info" style="font-size: 1.3em;"><?php echo $form->name . ' (' . $form->id .')'; ?></span>
        </div>
    </div>
    <hr />
    <span>Si modificas un <?=$pag?>, toma en cuenta que <strong class="text-danger">PUEDES PERDER </strong>valores anteriores.</span>
    <?php if ($form->crm): ?>
        <hr />
        <?php if (!empty($consem)): ?>
            <?php echo form_open('crm/savesem', ['class'=>'form', 'id'=>'savesemform'], ['fid'=>$form->id]); ?>
            <div class="row">
                <div class="col">
                    <h5 class="form-check">
                        <input type="checkbox" value="1" name="active" class="form-check-input"
                            style="width:1.5em;height:1.5em;margin:-0.1em 0 0 -2em;" <?=($consem->active)?"checked":""?>>
                        <label for="active" class="form-check-label">Semáforo activo</label>
                    </h5>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col">Tiempo de advertencia (pasar a amarillo)</div>
            </div>
            <div class="row">
                <div class="col-auto">
                    <label for="notify_warn">Email</label>
                    <input type="checkbox" name="notify_warn" class="form-control"
                    value="1" <?=($consem->notify_warn)?"checked":""?>>
                </div>
                <div class="col-1">
                    <label for="time_to_warn">Minutos</label>
                    <input type="number" name="time_to_warn" class="form-control"
                        value="<?=$consem->time_to_warn?>">
                </div>
                <div class="col">
                    <label for="msg_warn">Texto</label>
                    <textarea name="msg_warn" class="form-control" cols="30" rows="2" required><?=$consem->msg_warn?></textarea>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col">Tiempo total para resolver (pasar a rojo)</div>
            </div>
            <div class="row">
                <div class="col-auto">
                    <label for="notify_red">Email</label>
                    <input type="checkbox" name="notify_red" class="form-control"
                        value="1" <?=($consem->notify_red)?"checked":""?>>
                    </div>
                <div class="col-1">
                    <label for="time_to_red">Minutos</label>
                    <input type="number" name="time_to_red" class="form-control"
                        value="<?=$consem->time_to_red?>">
                </div>
                <div class="col">
                    <label for="msg_red">Texto</label>
                    <textarea name="msg_red" class="form-control" cols="30" rows="2" required><?=$consem->msg_red?></textarea>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col">Tiempo de recordatorios por intervalo de tiempo</div>
            </div>
            <div class="row">
                <div class="col-auto">
                    <label for="notify_step">Email</label>
                    <input type="checkbox" name="notify_step" class="form-control"
                    value="1" <?=($consem->notify_step)?"checked":""?>>
                </div>
                <div class="col-1">
                    <label for="time_to_step">Minutos</label>
                    <input type="number" name="time_to_step" class="form-control"
                        value="<?=$consem->time_to_step?>">
                </div>
                <div class="col">
                    <label for="msg_step">Texto</label>
                    <textarea name="msg_step" class="form-control" cols="30" rows="2" required><?=$consem->msg_step?></textarea>
                </div>
                <div class="col-auto">
                    <label for="notify_step_after_red">Sólo en rojo</label>
                    <input type="checkbox" name="notify_step_after_red" class="form-control"
                    value="1" <?=($consem->notify_step_after_red)?"checked":""?>>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col">
                    <p>Tipo de Horario que se aplicara para calcular el estado del semaforo</p>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="campaign_hours" id="campaign_hours0" value="0" <?=($consem->campaign_hours==0)?"checked":""?>>
                        <label class="form-check-label" for="campaign_hours0">
                            Horario Corrido
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="campaign_hours" id="campaign_hours1" value="1" <?=($consem->campaign_hours == 1)?"checked":""?>>
                        <label class="form-check-label" for="campaign_hours1">
                            Horario Laboral
                        </label>
                    </div>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col text-center">
                    <button type="submit" class="btn btn-primary" id="savesembtn">Guardar semáforo</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php else: ?>
            <div class="col">
                <?php echo form_open('crm/activasem', ['class'=>'form'], ['fid'=>$form->id]); ?>
                <button type="submit" class="btn btn-primary">Habilitar Semáforo</button>
                <?php echo form_close(); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <hr />
    <?php /* $reservados = (empty($form->crm)) ? ["ID", "Apertura"] : ["ID", "Detalle", "ID cliente", "Asignar a", "Cierre", "Apertura", "Estatus", "Informar", "Semáforo"]; ?>
    <?php foreach ($data as $key => $fila): ?>
        <?php if (in_array($fila->name, $reservados)) {
            unset($data[$key]);
        } ?>
    <?php endforeach; */ ?>
    <form method="post", action="<?=site_url('form/addtabladep')?>" enctype="multipart/form-data">
        <input type="hidden" name="fid" value="<?=$form->id?>">
        <div class="row mb-3">
            <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                <div class="form-group">
                    <label for="tabladep">Agregar tabla dependiente</label>
                    <input type="file" class="form-control-file" name="archivo">
                </div>
            </div>
            <?php if( count($tbrs) == 0 ): ?>
                <input type="hidden" name="id_form_field" value="0"/>
            <?php else: ?>
                <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                    <label for="id_form_field">Formulario</label>
                    <select id="id_form_field" name="id_form_field" class="form-control">
                        <option value="">Campos base del formulario</option>
                        <?php foreach ($tbrs as $tbr): ?>
                            <option value="<?= $tbr['id_form_field'] ?>">Tabla relacionada: <?= $tbr['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            <?php endif ?>
        </div>
        <div class="form-group">
            <button class="btn btn-primary" type="submit">Subir</button>
        </div>
    </form>
    <p>IMPORTANTE! Se usará la primera fila como nombre de los campos que se agreguen.
        La primera columna será el índice de búsqueda y las demás dependientes.
        Si hay un nombre de campo repetido con los existentes, causarás error.
        Los valores de la primer columna son índice no puede habere duplicados o causarás error.
        Si modificas una sola de las columnas después, causarás error.
        Longitud máxima de índice, 25; longitud máxima de dependientes, 100.
        Todos los campos son tratados como texto.</p>
    <hr />
    <?php if ($sidep): ?>
        <p>Agregaste la(s) tabla(s) dependiente con la siguiente estructura:
            <strong>(NO MODIFIQUES ESOS CAMPOS!)</strong></p>
            <?php foreach ($fields_full as $key => $deps): ?>
                <form method="post" class="mb-3" action="<?=site_url('form/deltabladep')?>" enctype="multipart/form-data">
                    <?php if ($regs==0): ?>
                        <button type="submit" class="btn btn-danger ml-2 btn-sm" onclick="return confirm('¿Estás seguro de borrar esta tabla dependiente?');" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    <?php endif ?>
                    <?=ucfirst($key)?>
                    <code>&lt;campos&gt;
                    <?php $nomod=[]; foreach ($deps['campos'] as $field) {
                        if( $field != 'active_system_row' ) echo ucfirst($field).", "; 
                        $nomod[] = $field;
                    } ?>
                    &lt;/campos&gt;</code><br />
                    <input type="hidden" name="fid" value="<?=$form->id?>">
                    <input type="hidden" name="id_form_fields" value="<?=$deps['info']->id_form_fields?>">
                    <input type="hidden" name="depend" value="<?=$deps['info']->depend?>">
                    <input type="hidden" name="pk" value="<?=$deps['info']->name?>">
                </form>
            <?php endforeach; ?>
        <hr />
        <?php if ($regs!=0): ?>
            <form class="form" action="<?=site_url('form/addtotabladep')?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="fid" value="<?=$form->id?>">
                <select name="did" class="form-control w-25">
                    <?php foreach ($fields as $key => $value): ?>
                    <option value="<?=$key?>"><?=ucfirst($key)?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo" value="1" checked>
                    <label class="form-check-label">
                        Agregar id's faltantes con sus valores
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo" value="2">
                    <label class="form-check-label">
                        Reemplazar toda la base con los nuevos valores
                    </label>
                </div>
                <div class="form-group">
                    <label for="tabladep">Archivo con la misma estructura:</label>
                    <input type="file" class="form-control-file" name="archivo">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Subir</button>
                </div>
            </form>
        <?php endif; ?>
        <hr />
    <?php endif; ?>
    <div>
        <ul class="nav nav-tabs mb-0">
            <li class="nav-item">
                <div onclick="crm.tab(this.id)" id="tab_oct" class="nav-link tab-crm" style="cursor: pointer;">Operaciones Cierre Ticket</div>
            </li>
            <li class="nav-item">
                <div onclick="crm.tab(this.id)" id="tab_reasignacion" class="nav-link tab-crm" style="cursor: pointer;" aria-current="page">Reasignación</div>
            </li>
            <li class="nav-item">
                <div onclick="crm.tab(this.id)" id="tab_ftr" class="nav-link tab-crm" style="cursor: pointer;" aria-current="page">Filtros Dependientes</div>
            </li>
            <li class="nav-item">
                <div onclick="crm.tab(this.id)" id="tab_campo_cal" class="nav-link tab-crm" style="cursor: pointer;">Campos Calculados</div>
            </li>
        </ul>  
        <!-- APARTADO PARA CAMPOS DE REASIGNACION -->
        <div id="sec_tab_reasignacion" class="sec-tab-crm p-2">
            <p>Puedes agregar reasignadores de valores por cada campo de alguna tabla dependiente</p>
            <div class="table table-striped">
                <div class="table-header-group">
                    <div class="table-row">
                        <div class="table-cell">Activador</div>
                        <div class="table-cell">Campo</div>
                        <div class="table-cell">Copiar a</div>
                        <div class="table-cell">Acción</div>
                    </div>
                    <form id="form_dep_asig" class="table-row" method="">
                        <div class="table-cell">
                            <input class="form-control" type="hidden" name="id" value="0" />
                            <input class="form-control" type="hidden" name="id_form" value="<?= $form->id ?>" />
                            <select class="form-control" type="text" name="activador" onchange="formAsig.depasig_fields()">
                                <option value="">-Seleccione-</option>
                                <?php foreach ($fields as $key => $value): ?>
                                    <option value="<?=$value[0]?>"><?=ucfirst($key)?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="table-cell">
                            <select name="campo" class="form-control" id="options_campo">
                                <option value="">-Seleccione-</option>
                            </select>
                        </div>
                        <div class="table-cell">
                            <select class="form-control" name="copia">
                                <option value="">-Seleccione-</option>
                                <?php foreach ($fields_form as $row): ?>
                                    <option value="<?= $row->slug ?>"><?= $row->name ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="table-cell">
                            <input type="button" name="accion" value="Agregar" class="btn btn-primary" onclick="formAsig.save()">
                            <input type="button" name="cancelar" value="Cancelar" class="btn btn-secondary" onclick="formAsig.reset()" style="display: none;" />
                        </div>
                    </form>
                </div>
                <div class="table-body-group" id="depasig_rows">
                </div>
            </div>
        </div>
        <!-- APARTADO PARA CAMPOS CALCULADOS -->
        <div id="sec_tab_campo_cal" class="sec-tab-crm p-2">
            <p>
                Aquí puedes establecer reglas para calcular campos basados en la fórmula <code>CampoR = CampoA (operador) CampoB </code>, 
                Esta operación se realizará cuando el campo del formulario seleccionado como activador pierda el enfoque.
            </p>
            <div class="table table-striped">
                <div class="table-header-group">
                    <div class="table-row">
                        <div class="table-cell">Activador</div>
                        <div class="table-cell">Campo Resultado</div>
                        <div class="table-cell">Campo A</div>
                        <div class="table-cell">Operador</div>
                        <div class="table-cell">Campo B</div>
                        <div class="table-cell">Acción</div>
                    </div>
                    <form id="form_calc_field" class="table-row">
                        <div class="table-cell">
                            <input class="form-control" type="hidden" name="id" value="0" />
                            <input class="form-control" type="hidden" name="id_form" value="<?= $form->id ?>" />
                            <select class="form-control" type="text" name="activator">
                                <option value="">-Seleccione-</option>
                                <?php foreach ($fields_form_by_table as  $table): ?>
                                    <optgroup label="<?= $table['table'] ?>">
                                        <?php foreach($table['fields'] as $field): ?>
                                            <option value="<?= $field->id_form_field ?>"><?= $field->name ?></option>
                                        <?php endforeach ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="table-cell">
                            <select class="form-control" type="text" name="field_r">
                                <option value="">-Seleccione-</option>
                                <?php foreach ($fields_form_by_table as  $table): ?>
                                    <optgroup label="<?= $table['table'] ?>">
                                        <?php foreach($table['fields'] as $field): ?>
                                            <option value="<?= $field->id_form_field ?>"><?= $field->name ?></option>
                                        <?php endforeach ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="table-cell">
                            <select class="form-control" type="text" name="field_a">
                                <option value="">-Seleccione-</option>
                                <?php foreach ($fields_form_by_table as  $table): ?>
                                    <optgroup label="<?= $table['table'] ?>">
                                        <?php foreach($table['fields'] as $field): ?>
                                            <option value="<?= $field->id_form_field ?>"><?= $field->name ?></option>
                                        <?php endforeach ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="table-cell">
                            <select class="form-control" name="operator">
                                <option value="">-Seleccione-</option>
                                <option value="+">+</option>
                                <option value="-">-</option>
                                <option value="*">*</option>
                                <option value="/">/</option>
                            </select>
                        </div>
                        <div class="table-cell">
                            <select class="form-control" type="text" name="field_b">
                                <option value="">-Seleccione-</option>
                                <?php foreach ($fields_form_by_table as  $table): ?>
                                    <optgroup label="<?= $table['table'] ?>">
                                        <?php foreach($table['fields'] as $field): ?>
                                            <option value="<?= $field->id_form_field ?>"><?= $field->name ?></option>
                                        <?php endforeach ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="table-cell">
                            <input type="button" name="accion" value="Agregar" class="btn btn-primary" onclick="calcField.save()">
                            <input type="button" name="cancelar" value="Cancelar" class="btn btn-secondary" onclick="calcField.reset()" style="display: none;" />
                        </div>
                    </form>
                </div>
                <div class="table-body-group" id="calc_field_list">
                </div>
            </div>
        </div>
        <!-- FILTROS DEPENDIENTES -->
        <div id="sec_tab_ftr" class="sec-tab-crm p-2">
            <p>
                Crea un filtro para una tabla dependiente cuando se carguen los datos de otra tabla dependiente.
                <br/>
                - Activador: Campo de la tabla dependiente que activará el filtro cuando se cargue este valor.
                <br />
                - Campo a Filtrar: Campo principal para indicar la tabla dependiente que se convertirá en un combo filtrado.
                <br />
                - Campo a comprar: Campo con el cual se filtrará la información de la tabla dependiente con el activador ( activador = campo a comparar ).
                <br />
            </p>
            <button class="btn btn-primary mb-2" onclick="ftr.new()">Nuevo</button>
            <div class="table table-striped">
                <div class="table-header-group">
                    <div class="table-row"></div>
                    <div class="table-row">
                        <div class="table-cell">Activador</div>
                        <div class="table-cell">Campo a filtrar</div>
                        <div class="table-cell">Campo a comparar</div>
                        <div class="table-cell"># Tabla Union</div>
                        <div class="table-cell">Campo A Table Union</div>
                        <div class="table-cell">Campo B Table Union</div>
                        <div class="table-cell">Acción</div>
                    </div>
                </div>
                <div class="table-body-group" id="ftr_list">
                </div>
            </div>
        </div>
        <!-- OPERACIONES AL CIERRE DE TICKETS -->
        <div id="sec_tab_oct" class="sec-tab-crm p-2">
            <p>
                Aquí puedes establecer operaciones para modificar campos de los formularios basados en la fórmula <code>CampoR = CampoA (operador) CampoB</code>,
                Puedes personalizar los valores o establecer valores de otras tablas a las que tenga acceso el formulario.
                En caso de que el operador sea <code>N/A</code> se aplicará la fórmula <code>CampoR = CampoA</code>
                estas operaciones se ejecutarán en el orden seleccionado justo al momento del cierre del ticket.
            </p>
            <button class="btn btn-primary mb-2" onclick="oct.new()">Nuevo</button>
            <div class="table table-striped">
                <div class="table-header-group">
                    <div class="table-row"></div>
                    <div class="table-row">
                        <div class="table-cell py-2">Campo Resultado</div>
                        <div class="table-cell">Campo A</div>
                        <div class="table-cell">Operador</div>
                        <div class="table-cell">Campo B</div>
                        <div class="table-cell">Orden</div>
                        <div class="table-cell">Acción</div>
                    </div>
                </div>
                <div class="table-body-group" id="oct_list">
                </div>
            </div>
        </div>
    </div>
    <hr />
    <p class="text-center"><h5>Campos</h5></p>
    <?php
        $tiposv = array(
            "text"      => "Texto corto",
            "textarea"  => "Texto largo",
            "checkbox"  => "Check",
            "dropdown"  => "Lista",
            "radio"     => "Opciones",
            "datetime"  => "Fecha hora",
            "boton"     => "Botón",
            "separador" => "Separador",
            "tabla"     => "Tabla Relacionada"
        );
        $tiposd = array("0"=>"No", "1"=>"1", "2"=>"2", "3"=>"3", "4"=>"4"); ?>
    <table class="table table-responsive table-long">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Etiqueta</th>
                <th>Slug</th>
                <?php if($agente["perfil"] == "admin"): ?>
                <th>Largo</th>
                <?php endif; ?>
                <th>valores</th>
                <th>Depend</th>
                <th>Bus</th>
                <th>Req</th>
                <?php if($agente["perfil"] == "admin"): ?>
                <th>API</th>
                <?php endif; ?>
                <th>Ver</th>
                <th>Edit</th>
                <th>Rep</th>
                <th>Orden</th>
                <th colspan="2">Acción</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php echo form_open("form/crearc", array('role'=>'form', 'class'=>'form-inline', 'class'=>'table-row'), array("id_form"=>$form->id)); ?>
                    <td class="table-cell"><?php echo form_dropdown("type", $tiposv, $form->id, "class='form-control'"); ?></td>
                    <td class="table-cell"><input class="form-control" type="text" name="name" maxlength="35" /></td>
                    <td class="table-cell"><input class="form-control" type="text" name="slug" maxlength="35" placeholder="Autogenerado" /></td>
                    <?php if($agente["perfil"] == "admin"): ?>
                    <td class="table-cell"><input class="form-control" type="number" name="len" /></td>
                    <?php endif; ?>
                    <td class="table-cell"><input class="form-control" type="text" name="values" /></td>
                    <td class="table-cell">
                        <select class="form-control" name="depend">
                            <option value="0">No</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </td>
                    <td class="table-cell"><input class="form-control" type="checkbox" name="searchable" value="1" /></td>
                    <td class="table-cell"><input class="form-control" type="checkbox" name="required" value="1" /></td>
                    <?php if($agente["perfil"] == "admin"): ?>
                    <td class="table-cell"><input class="form-control" type="checkbox" name="api" value="1" /></td>
                    <?php endif; ?>
                    <td class="table-cell"><input class="form-control" type="checkbox" name="front" value="1" /></td>
                    <td class="table-cell"><input class="form-control" type="checkbox" name="editable" value="1" /></td>
                    <td class="table-cell"><input class="form-control" type="checkbox" name="report" value="1" /></td>
                    <td class="table-cell"><input class="form-control" type="number" name="order" value="0" min="0" max="100" step="1" /></td>
                    <td class="table-cell"><?php echo form_submit('agregar','Agregar','class="btn btn-primary"'); ?></td>
                    <td class="table-cell"></td>
                <?php echo form_close(); ?>
            </tr>
            <?php foreach ($data as $fila): $reo = $noc = ""; if ($fila->base == "1") { $reo = "readonly"; $noc = 'onclick="return false;"'; } ?>
                <tr>
                    <?php echo form_open('form/actualizarc', array('class'=>'form'), array('id' => $fila->id, 'id_form'=>$form->id)); ?>
                        <td class="table-cell"><?php echo form_dropdown('type', $tiposv, $fila->type, 'class="form-control" ' . $reo); ?></td>
                        <td class="table-cell"><?php echo form_input('name', $fila->name,'class="form-control" maxlength="35"'); ?></td>
                        <td class="table-cell"><?php echo form_input('slug', $fila->slug,'class="form-control" maxlength="35" ' . $reo); ?></td>
                        <?php if($agente["perfil"] == "admin"): ?>
                        <td class="table-cell"><?php echo form_input('len', $fila->len,'class="form-control" ' . $reo); ?></td>
                        <?php endif; ?>
                        <td class="table-cell"><?php echo form_input('values', $fila->values,'class="form-control"'); ?></td>
                        <td class="table-cell"><?php echo form_dropdown('depend', $tiposd, $fila->depend, ($fila->type == 'tabla') ? 'class="form-control" readonly ' : 'class="form-control" ' . $reo  ); ?></td>
                        <td class="table-cell"><?php echo form_checkbox('searchable', 1, ($fila->searchable==1)?true:false, 'class="form-control" ' . $noc); ?></td>
                        <td class="table-cell"><?php echo form_checkbox('required', 1, ($fila->required==1)?true:false, 'class="form-control" ' . $noc); ?></td>
                        <?php if($agente["perfil"] == "admin"): ?>
                        <td class="table-cell"><?php echo form_checkbox('api', 1, ($fila->api==1)?true:false, 'class="form-control"'); ?></td>
                        <?php endif; ?>
                        <td class="table-cell"><?php echo form_checkbox('front', 1, ($fila->front==1)?true:false, 'class="form-control"'); ?></td>
                        <td class="table-cell"><?php echo form_checkbox('editable', 1, ($fila->editable==1)?true:false, 'class="form-control"'); ?></td>
                        <td class="table-cell"><?php echo form_checkbox('report', 1, ($fila->report==1)?true:false, 'class="form-control"'); ?></td>
                        <td class="table-cell"><input class="form-control" type="number" name="order" value="<?php echo $fila->order; ?>" min="0" max="99" step="1" /></td>
                        <td class="table-cell"><?php echo form_submit('guardar','Actualizar','class="btn btn-info"'); ?></td>
                    <?php echo form_close(); ?>
                    <?php if ($fila->base == "0"): ?>
                        <td class="table-cell">
                            <?php echo form_open('form/borrarc', array('role'=>'form', 'class'=>'form delform'), array('id' => $fila->id, 'id_form'=>$form->id));
                            if( $fila->type == 'tabla' ) {
                                $advertencia = 'onclick="return confirm(\'Advertencia: Borrar este campo eliminará la tabla y la información relacionada a ella. ¿Quieres continuar?\');"';
                            } else if( $fila->type == 'separador' ) {
                                $advertencia = $advertencia = 'onclick="return confirm(\'¿Estás seguro de borrar este separador?\');"';
                            } else {
                                $advertencia = 'onclick="return confirm(\'¿Estás seguro de borrar este campo y su información?\');"';
                            }
                            echo form_submit('borrar','Borrar','class="btn btn-warning"'.' '.$advertencia);
                            echo form_close(); ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <hr />
    <?php foreach ($tbrs as $tbr): ?>
    <p class="text-center"><h5>Tabla Relacionada: <span class="text-info"><?= $tbr['name'] ?></span></h5></p>
    <div class="table table-striped">
        <div class="table-header-group">
            <div class="table-cell">Tipo</div>
            <div class="table-cell">Nombre</div>
            <div class="table-cell">valores</div>
            <div class="table-cell">Dependencia</div>
            <div class="table-cell">Requerido</div>
            <div class="table-cell">Editable</div>
            <div class="table-cell">Orden</div>
            <div class="table-cell">Acción</div>
        </div>
        <?php
            $tiposv = array(
                "text"=>"Texto corto",
                "textarea"=>"Texto largo",
                "checkbox"=>"Check",
                "dropdown"=>"Lista",
                "radio"=>"Opciones",
                "datetime"=>"Fecha hora",
                "boton"=>"Botón",
                "separador"=>"Separador",
                "datetime_pdf"=>"Fecha Hora PDF",
                "datetime_pdf_update"=>"Fecha Hora PDF Actualización"
            );
            $tiposd = array("0"=>"No", "1"=>"1", "2"=>"2", "3"=>"3", "4"=>"4");
            echo form_open(
                "form/crearctbr",
                array('role'=>'form', 'class'=>'form-inline', 'class'=>'table-row'),
                array("id_form"=>$form->id)
            );
        ?>
            <div class="table-cell">
                <?php echo form_dropdown("type", $tiposv, $form->id, "class='form-control'"); ?>
            </div>
            <div class="table-cell">
                <input class="form-control" type="hidden" name="id_form_field" value="<?= $tbr['id_form_field'] ?>" />
                <input class="form-control" type="text" name="name" placeholder="Nombre" maxlength="35" />
            </div>
            <div class="table-cell">
                <input class="form-control" type="text" name="values" placeholder="Valores(separados por coma)" />
            </div>
            <div class="table-cell">
                <select class="form-control" name="depend">
                    <option value="0">No</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <div class="table-cell"><input class="form-control" type="checkbox" name="required" value="1" /></div>
            <div class="table-cell"><input class="form-control" type="checkbox" checked name="editable" value="1" /></div>
            <div class="table-cell"><input class="form-control" type="number" name="order" value="0" min="0" max="100" step="1" /></div>
            <div class="table-cell"><?php echo form_submit('agregar','Agregar','class="btn btn-primary"'); ?></div>
            <div class="table-cell"></div>
        <?php echo form_close(); ?>
        <?php foreach ($tbr['data'] as $fila): ?>
            <?php
                $btn_action_props = '';
                $btn_action_props = ($fila->type == 'datetime_pdf_update') ? 'disabled' : $btn_action_props;
            ?>
            <?php echo form_open('form/actualizarctbr', array('class'=>'table-row'), array('id' => $fila->id, 'id_form'=>$form->id)); ?>
                <div class="table-cell"><?php echo form_dropdown('type', $tiposv, $fila->type, 'class="form-control"'); ?></div>
                <div class="table-cell"><?php echo form_input('name', $fila->name,'class="form-control" maxlength="35"'); ?></div>
                <div class="table-cell"><?php echo form_input('values', $fila->values,'class="form-control"'); ?></div>
                <div class="table-cell"><?php echo form_dropdown('depend', $tiposd, $fila->depend, 'class="form-control"'  ); ?></div>
                <div class="table-cell"><?php echo form_checkbox('required', 1, ($fila->required==1)?true:false, 'class="form-control"'); ?></div>
                <div class="table-cell"><?php echo form_checkbox('editable', 1, ($fila->editable==1)?true:false, 'class="form-control"'); ?></div>
                <div class="table-cell"><input class="form-control" type="number" name="order" value="<?php echo $fila->order; ?>" min="0" max="100" step="1" /></div>
                <div class="table-cell"><?php echo form_submit('guardar','Actualizar','class="btn btn-info" '.$btn_action_props); ?></div>
                <div class="table-cell">
                    <?php echo form_open('form/borrarctbr', array('role'=>'form', 'class'=>'form delform'),
                    array('id' => $fila->id, 'id_form'=>$form->id)); ?>
                    <?php 
                        if( $fila->type == 'separador' ) {
                            $advertencia = 'onclick="return confirm(\'¿Estás seguro de borrar este separador?\');"';
                        } else {
                            $advertencia = 'onclick="return confirm(\'¿Estás seguro de borrar este campo y su información?\');"';
                        }
                    ?>
                    <?php echo form_submit('borrar','Borrar','class="btn btn-warning"'.' '.$advertencia.' '.$btn_action_props); ?>
                    <?php echo form_close(); ?>
                </div>
            <?php echo form_close(); ?>
        <?php endforeach; ?>
    </div>
    <?php endforeach ?>
    <br />
    <?php if (empty($form->crm)): ?>
        <hr />
        <?php echo form_open('form/hacercrm', array('role'=>'form'), array("id_form"=>$form->id)); ?>
        <?php echo form_submit('hacercrm','Hacer CRM','class="btn btn-primary"'); ?>
        <?php echo form_close(); ?>
        <br />
        <p>Toma en cuenta que un CRM utiliza los siguientes campos reservados, si tu formulario usa alguno, éste será "confiscado" para funcionar como CRM, y si conviertes un formulario a CRM la administración también se mueve a la sección correspondiente:</p>
        <p>Formulario simple: ID, Apertura, ID User, Uniqueid, Linkedid<br />
        CRM aumenta con: Detalle, ID Cliente, Asignar a, Cierre, Estatus, Informar, Semáforo</p>
        <br />
    <?php endif; ?>
</div>
<!--Modal OCT-->
<div class="modal fade pastilla" id="modal-oct" tabindex="-1" role="dialog" aria-labelledby="oct-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form id="form_oct">
                <div class="modal-header">
                    <h4 class="modal-title" id="oct-modalLabel">Operación al Cierre de Ticket</h4>
                    <input class="form-control" type="hidden" name="id" value="0" />
                    <input class="form-control" type="hidden" name="id_form" value="<?= $form->id ?>" />
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="oct_field_r">Campo Resultado</label>
                                <select class="form-control" name="field_r" id="oct_field_r">
                                    <option value="">-Seleccione-</option>
                                    <?php foreach( $oct_selects as $table ): ?>
                                        <optgroup label="<?= $table['text'] ?>">
                                            <?php foreach($table['fields'] as $field): ?>
                                                <option value="<?= $table['sufix'].$field->slug ?>"><?= $field->name ?></option>
                                            <?php endforeach ?>
                                        </optgroup>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group oct_field_a">
                                <label for="oct_field_a">Campo A</label>
                                <select class="form-control" name="field_a" id="oct_field_a" onchange="oct.field_visibility()">
                                    <option value="">-Seleccione-</option>
                                    <option value="0">Personalizado</option>
                                    <?php foreach( $oct_selects as $table ): ?>
                                        <optgroup label="<?= $table['text'] ?>">
                                            <?php foreach($table['fields'] as $field): ?>
                                                <option value="<?= $table['sufix'].$field->slug ?>"><?= $field->name ?></option>
                                            <?php endforeach ?>
                                        </optgroup>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 oct_custom_a">
                            <div class="form-group">
                                <label for="oct_custom_a">Campo Personalizado A</label>
                                <input type="text" class="form-control" name="custom_a" id="oct_custom_a"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 oct_operator">
                            <div class="form-group">
                                <label for="oct_operator">Operador</label>
                                <select class="form-control" name="operator" id="oct_operator" onchange="oct.field_visibility()">
                                    <option value="">-Seleccione-</option>
                                    <option value="N/A">N/A</option>
                                    <option value="+">+</option>
                                    <option value="-">-</option>
                                    <option value="*">*</option>
                                    <option value="/">/</option>
                                    <option value=".">.</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 oct_field_b">
                            <div class="form-group">
                                <label for="oct_field_b">Campo B</label>
                                <select class="form-control" name="field_b" id="oct_field_b" onchange="oct.field_visibility()">
                                    <option value="">-Seleccione-</option>
                                    <option value="0">Personalizado</option>
                                    <option value="N/A">N/A</option>
                                    <?php foreach( $oct_selects as $table ): ?>
                                        <optgroup label="<?= $table['text'] ?>">
                                            <?php foreach($table['fields'] as $field): ?>
                                                <option value="<?= $table['sufix'].$field->slug ?>"><?= $field->name ?></option>
                                            <?php endforeach ?>
                                        </optgroup>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 oct_custom_b">
                            <div class="form-group">
                                <label for="oct_custom_b">Campo Personalizado B</label>
                                <input type="text" class="form-control" name="custom_b" id="oct_custom_b"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="oct_order">Orden de ejecución</label>
                                <input type="number" class="form-control" name="order" id="oct_order"/>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <input type="button" class="btn btn-primary oct-action" value="Guardar" onclick="oct.save()" />
                </div>
            </form>
        </div>
    </div>
</div>

<!--Modal FTR-->
<div class="modal fade pastilla" id="modal-ftr" tabindex="-1" role="dialog" aria-labelledby="ftr-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form id="form_ftr">
                <div class="modal-header">
                    <h4 class="modal-title" id="ftr-modalLabel">Filtros a tablas dependientes</h4>
                    <input class="form-control" type="hidden" name="id" value="0" />
                    <input class="form-control" type="hidden" name="id_form" value="<?= $form->id ?>" />
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ftr_activator" data-toggle="tooltip"
                                    data-original-title="Campo de la tabla dependiente que activará el filtro cuando se cargue este valor."
                                >
                                    <i class="fas fa-info-circle"></i> Activador
                                </label>
                                <select class="form-control" name="activator" id="ftr_activator">
                                    <option value="">-Seleccione-</option>
                                    <?php foreach ($ftr_selects['activator'] as $item): ?>
                                        <optgroup label="<?= $item->table_name ?>">
                                            <?php foreach($item->fields as $field): ?>
                                                <option value="<?= $field->slug ?>"> <?= $field->slug ?> </option>
                                            <?php endforeach ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ftr_field_to_filter" data-toggle="tooltip" 
                                    data-original-title="Campo principal para indicar la tabla dependiente que se convertirá en un combo filtrado."
                                >
                                    <i class="fas fa-info-circle"></i> Campo a Filtrar
                                </label>
                                <select class="form-control" name="field_to_filter" id="ftr_field_to_filter">
                                    <option value="">-Seleccione-</option>
                                    <?php foreach ($ftr_selects['field_to_filter'] as $item): ?>
                                        <option value="<?=$item->slug?>"><?=$item->name?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ftr_field_to_compare" data-toggle="tooltip"
                                    data-original-title="Campo con el cual se filtrará la información de la tabla dependiente con el activador ( activador = campo a comparar )."
                                >
                                    <i class="fas fa-info-circle"></i> Campo a comparar
                                </label>
                                <select class="form-control" name="field_to_compare" id="ftr_field_to_compare">
                                    <option value="">-Seleccione-</option>
                                    <?php foreach ($ftr_selects['activator'] as $item): ?>
                                        <optgroup label="<?= $item->table_name ?>">
                                            <?php foreach($item->fields as $field): ?>
                                                <option value="<?= $field->slug ?>"> <?= $field->slug ?> </option>
                                            <?php endforeach ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <input type="checkbox" id="advanced_options" onclick="ftr.handle_advanced_options()" value="1">
                            <label for="advanced_options"> Opciones Avanzadas</label>
                            <hr>
                        </div>
                    </div>
                    <div id="opt_adv_section">
                        <div class="col-12 ftr-file-section">
                            <div class="form-group">
                                <label for="archivo_tbu" data-toggle="tooltip" 
                                    data-original-title="Indica la tabla que sera intermediaria entre la activador y la tabla dependiente que tendra los datos."
                                >
                                    <i class="fas fa-info-circle"></i> Tabla Union
                                </label>
                                <input type="file" class="form-control-file" id="archivo_tbu" name="archivo_tbu">
                            </div>
                        </div>
                        <div class="col-12 ftr-file-text-section">
                            <button type="button" class="btn btn-danger" onclick="ftr.delete_table_union()">
                                Eliminar tabla de filtro union
                            </button>
                            <input type="hidden" class="form-control" name="union_table" id="ftr_union_table"/>
                        </div>
                        <div class="row ftr-file-text-section mt-3">
                            <div class="col-12">
                                <div class="alert alert-warning" role="alert">
                                    Para aplicar el funcionamiento de filtros con una tabla de datos unión, es necesario rellenar la información para el campo A y el campo B
                                </div>
                            </div>
                        </div>
                        <div class="row ftr-file-text-section">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="ftr_field_to_filter" data-toggle="tooltip" 
                                        data-original-title="Campo A de la tabla union que sera comparado con el campo B de a tabla dependiente, la cual se usara para crear el combo de filtro"
                                    >
                                        <i class="fas fa-info-circle"></i> Campo A Tabla Union
                                    </label>
                                    <input type="text" class="form-control" name="union_field_a" id="ftr_union_field_a"/>
                                </div>
                            </div>
                        </div>
                        <div class="row ftr-file-text-section">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="ftr_field_to_filter" data-toggle="tooltip" 
                                        data-original-title="Campo B que se vinculara con el campo A de la tabla union."
                                    >
                                        <i class="fas fa-info-circle"></i> Campo B Tabla Union
                                    </label>
                                    <input type="text" class="form-control" name="union_field_b" id="ftr_union_field_b"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <input type="button" class="btn btn-primary ftr-action" value="Guardar" onclick="ftr.save()" />
                </div>
            </form>
        </div>
    </div>
</div>