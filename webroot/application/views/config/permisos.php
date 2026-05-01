<div class="container main">
    <div class="row">
        <h3 class="tit-div">Permisos</h3>
    </div>
    <div class="row">
        <table class="table table-striped">
        <thead>
            <tr class="warning">
                <th>Etiqueta</th>
                <th>Valor</th>
                <th>Orden</th>
                <th colspan="2">Acción</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php echo form_open('permisos/crear', array('role'=>'form', 'class'=>'form')); ?>
                <td><?php echo form_input('eti',set_value('eti'),'class="form-control"'); ?></td>
                <td><?php echo form_input('val',set_value('val'),'class="form-control"'); ?></td>
                <td><?php echo form_input('num_order',set_value('num_order'),'class="form-control"'); ?></td>
                    <td><input class="btn btn-primary" type="submit" value="Crear" /></td>
                    <td></td>
                <?php echo form_close(); ?>
            </tr>
            <?php foreach ($permisos as $permiso): ?>
                <tr>
                    <?php echo form_open('permisos/actualizar', '', array('id' => $permiso->id)); ?>
                        <td><?php echo form_input('eti',$permiso->eti,'class="form-control"'); ?></td>
                        <td><?php echo form_input('val',$permiso->val,'class="form-control"'); ?></td>
                        <td><?php echo form_input('num_order',$permiso->num_order,'class="form-control"'); ?></td>
                        <td><?php echo form_submit('guardar','Actualizar','class="btn btn-info"'); ?></td>
                    <?php echo form_close(); ?>
                    <td>
                        <?php echo form_open('permisos/borrar', array('role'=>'form', 'class'=>'form delform', 'data-id' => $permiso->id)); ?>
                            <input type="hidden" name="id" value="<?php echo $permiso->id; ?>" />
                            <?php echo form_submit('borrar','Borrar','class="btn btn-danger"'); ?>
                        <?php echo form_close(); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    </div>
    <div class="row">
        <div class="text-center">
            <nav aria-label="Page navigation">
                <?php echo $pagination; ?>
            </nav>
        </div>
    </div>
</div>
