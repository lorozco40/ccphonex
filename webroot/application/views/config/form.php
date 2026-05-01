<div class="container main">
    <?php $cont = $this->router->class; $pag = ($cont=="crm") ? "CRM" : "Formulario" ?>
    <div class="row">
        <div class="col-md-1">
            <h2><?=$pag?>s</h2>
        </div>
    </div>
    <hr>
    <div class="table table-striped">
        <div class="table-header-group">
            <div class="table-cell">Campaña</div>
            <div class="table-cell">Nombre</div>
            <div class="table-cell">Auto (*)</div>
            <div class="table-cell">Activo</div>
            <div class="table-cell">Acción</div>
        </div>
        <?php echo form_open($cont . '/crear',array('class'=>'table-row form', 'role'=>'form')); ?>
            <div class="table-cell"><select class="form-control alto" name="campaign">
                <?= options_select_campaign($campaigns) ?>
            </select></div>
            <div class="table-cell"><input class="form-control" type="text" name="name" placeholder="Nombre" /></div>
            <div class="table-cell"><input class="form-control check" type="checkbox" name="type" /></div>
            <div class="table-cell"><input class="form-control check" type="checkbox" checked="checked" disabled /></div>
            <div class="table-cell"><input class="btn btn-primary" type="submit" value="Crear" /></div>
        <?php echo form_close(); ?>
        <?php foreach ($data as $fila): ?>
            <?php echo form_open($cont . '/actualizar', array('class'=>'table-row form'), array('id' => $fila->id, 'type'=>0, 'active'=>0)); ?>
                <div class="table-cell">
                    <select class="form-control" name="campaign">
                        <?= options_select_campaign($campaigns, $fila->id_campaign) ?>
                    </select>
                </div>
                <div class="table-cell"><?php echo form_input('name',$fila->name,'class="form-control"'); ?></div>
                <div class="table-cell"><?php echo form_checkbox('type',1,($fila->type==1)?true:false, 'class="form-control check"'); ?></div>
                <div class="table-cell"><?php echo form_checkbox('active',1,($fila->active==1)?true:false, 'class="form-control check"'); ?></div>
                <div class="table-cell">
                    <?php echo form_submit('guardar','Actualizar','class="btn btn-info"'); ?>
                        <a class="btn btn-secondary ml-3" href="<?php echo site_url('form/campos/').$fila->id; ?>">Detalle</a>
                    <?php if ($this->udata['perfil'] == "admin" || $this->udata['id'] <= 5): ?>
                        <a class="btn btn-danger ml-3" onclick="return confirm('¿Confirmas Eliminar?')"
                            href="<?=site_url($cont . '/eliminar/' . $fila->id)?>">Eliminar</a>
                    <?php endif; ?>
                    <?php if ($fila->crm == 1 ): ?>
                        <a class="btn btn-dark ml-3 abrir-email-modal" data-id="<?php echo $fila->id;?>" data-idemailaccount="<?php echo $fila->id_email_account;?>" data-idcampaign='<?php echo $fila->id_campaign;?>' data-name='<?php echo $fila->name;?>'>Asignar Cuenta de Email</a>
                    <?php endif; ?>
                    <?php if($fila->n_dep): ?>
                        <a class="btn btn-secondary ml-3" href="<?=site_url('form/datadep?f='.$fila->id)?>">Catálogos</a>
                    <?php endif; ?>
                </div>
            <?php echo form_close(); ?>
        <?php endforeach; ?>
    </div><br />
    <div class="row">
        <div class="col-md-12 text-center">
            <nav aria-label="Page navigation">
                <?php echo $pagination; ?>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <small>* El <?=$cont?> que será desplegado automáticamente al entrar o salir una llamada de esa campaña.</small>
        </div>
    </div>
</div>

<div class="modal fade" id="SeleccionarEmailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formCrmModal">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLabel">Asignar Email - <span id="nombre-modal-crm"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="campana">Formulario</label>
                                </div>
                                <select class="form-control id_email_account" id="id_email_account2" name="id_email_account" required>
                                    <option value="">-Selecionar-</option>
                                    <?php foreach($cuentas as $key => $item) : ?>
                                        <?php if($cuentas) : ?>
                                            <option value="<?php echo $item->id;?>" data-idcampaign="<?php echo $item->id_campaign;?>">
                                                <?php echo $item->nombre ." (". $item->email .")";?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="id" id="id_form_hidden">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button id="BtnActualizarCuentaEmail" type="button" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
