<div class="container main">
    <div class="row">
        <div class="col-md-1">
            <h2>Configuración&nbsp;de&nbsp;colas</h2>
        </div>
    </div>
    <hr>
    <div class="table table-striped">
        <div class="table-header-group">
            <div class="table-cell">Descripción</div>
            <div class="table-cell">Colas</div>
            <div class="table-cell">Campaña</div>
            <div class="table-cell">Show</div>
            <div class="table-cell">Active</div>
            <div class="table-cell">Acción</div>
        </div>
        <?php foreach ($data as $fila): ?>
            <?php echo form_open('colas/actualizar', array('class'=> 'table-row') , array('id' => $fila->id)); ?>
                <div class="table-cell"><?php echo form_input('desc', $fila->desc, 'class="form-control"'); ?></div>
                <div class="table-cell"><?php echo form_input('name', $fila->name, 'class="form-control" readonly="readonly"'); ?></div>
                <div class="table-cell">
                    <select name="id_campaign" class="form-control">
                        <option value="">--Seleccione una opcion --</option>
                        <?= options_select_campaign($campanas, $fila->id_campaign) ?>
                    </select>
                </div>
                <div class="table-cell"><?php echo form_checkbox('show',1,($fila->show==1)?true:false, 'class="form-control check"'); ?></div>
                <div class="table-cell"><?php echo form_checkbox('active',1,($fila->active==1)?true:false, 'class="form-control check" disabled="disabled"'); ?></div>
                <div class="table-cell"><?php echo form_submit('guardar','Actualizar','class="btn btn-info"'); ?></div>
            <?php echo form_close(); ?>
        <?php endforeach; ?>
    </div>
</div>
