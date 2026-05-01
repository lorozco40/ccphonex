<div class="container main">
    <div class="row">
        <div class="col-md-1">
            <h2>Cédula&nbsp;de&nbsp;calidad</h2>
        </div>
    </div>
    <hr>
    <div class="table table-striped">
        <div class="table-header-group">
            <div class="table-cell">Nombre evaluación</div>
            <div class="table-cell">Nombre campaña</div>
            <div class="table-cell">Tipo</div>
            <div class="table-cell">Activo</div>
            <div class="table-cell">Acción</div>
        </div>
        <?php echo form_open('calidad/crear', array('class'=>'table-row'), array('role'=>'form', 'class'=>'form-inline')); ?>
            <div class="table-cell"><input type="text" class="form-control " name="name" placeholder="Nombre de evaluación"/></div>
            <div class="table-cell"><select class="form-control alto" name="campaign">
                    <option selected="true" disabled="disable">Seleccione campaña</option>
                    <?= options_select_campaign($campaigns) ?>
                </select>
            </div>
            <div class="table-cell">
                <select class="form-control alto" name="type">
                    <option value='llamadas'>Llamadas</option>
                    <option value='whatsapp'>Whatsapp</option>
                </select>
            </div>
            <div class="table-cell"><input class="form-control check" type="checkbox" name="activo"></div>
            <div class="table-cell" colspan="2"> <input class="btn btn-primary" type="submit" value="Crear"></div>
            <div class="table-cell"></div>
        <?php echo form_close(); ?>
        <?php foreach ($data as $fila): ?>
            <?php echo form_open('calidad/actualizar', array('class'=>'table-row'), array('id' => $fila->id)); ?>
                <div class="table-cell"><?php echo form_input('name', $fila->name, 'class="form-control "'); ?></div>
                <div class="table-cell">
                    <select class="form-control alto" name="campaign">
                        <option selected="true" disabled="disable">Seleccione campaña</option>
                        <?= options_select_campaign($campaigns, $fila->id_campaign) ?>
                    </select>
                </div>
                <div class="table-cell">
                    <select class="form-control alto" name="type">
                        <option selected="true" disabled="disable">Seleccione el tipo</option>
                        <?php foreach($tipos as $row): ?>
                            <?php $selected = ($fila->type == $row->value) ? 'selected' : ''; ?>
                            <option <?= $selected ?> value='<?= $row->value ?>'><?= $row->text ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="table-cell"><?php echo form_checkbox('active',1,($fila->active==1)?true:false, "class='form-control check check-active' data-id='$fila->id' data-cid='$fila->id_campaign'"); ?></div>
                <div class="table-cell"><?php echo form_submit('guardar','Actualizar','class="btn btn-info"'); ?></div>
                <div class="table-cell"><a class="btn btn-secondary" href="<?php echo site_url('calidad/campos/').$fila->id; ?>">Campos</a></div>
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
</div>
