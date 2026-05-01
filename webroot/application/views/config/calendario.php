<div class="container main">
    <div class="row">
        <div class="col-md-1">
            <h2>Calendarizar</h2>
        </div>
    </div>
    <hr>
    <div class="table table-striped">
        <div class="table-header-group">
            <div class="table-cell">Nombre</div>
            <div class="table-cell">Apellidos</div>
            <div class="table-cell">Tipo</div>
            <div class="table-cell">Fecha & Hora</div>
            <div class="table-cell">Agente</div>
            <div class="table-cell">Observaciones</div>
            <div class="table-cell">Acción</div>
        </div>
        <?php echo form_open('calendario/crear', array('class'=>'table-row'), array('role'=>'form', 'class'=>'form-inline')); ?>
            <div class="table-cell"><input type="text" class="form-control" name="name" placeholder="Nombre" required="required"></div>
            <div class="table-cell"><input type="text" class="form-control" name="last" placeholder="Apellidos"></div>
            <!-- <div class="table-cell"><input type="text" class="form-control" name="type" placeholder="Llamar" required="required"></div> -->
            <div class="table-cell"><select type="text" class="form-control" name="type">
                    <option value="Llamar">Llamar</option>
                    <option value="SMS">Envia SMS</option>
                    <option value="eMail">eMail</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="table-cell"><input type="datetime-local" class="form-control" name="scheduled" min="<?php $hoy=date("Y-m-d\TH:i"); echo $hoy; ?>" value="<?php echo $hoy; ?>" required="required"></div>
            <div class="table-cell"><select class="form-control" name="agentes" required="required">
                <option selected="true" value="">-- Elige agente --</option>
                    <?php foreach ($agentes as $key => $val) {
                        echo "<option value='".$val->id."'>".$val->nombre."</option>";
                    } ?>
                </select>
            </div>
            <div class="table-cell"><textarea type="text" class="form-control" name="observations"></textarea> </div>
            <div class="table-cell" colspan="2"> <input class="btn btn-primary" type="submit" value="Crear"></div>
            <div class="table-cell"></div>
        <?php echo form_close(); ?>
        <?php foreach ($data as $fila): ?>
            <?php echo form_open('calendario/modificar', array('class'=>'table-row'), array('id' => $fila->id)); ?>
                <div class="table-cell"><?php echo form_input('name', $fila->name, 'class="form-control" readonly="readonly"'); ?></div>
                <div class="table-cell"><?php echo form_input('last', $fila->last, 'class="form-control" readonly="readonly"'); ?></div>
                <div class="table-cell"><?php echo form_input('type', $fila->type, 'class="form-control" readonly="readonly"'); ?></div>
                <div class="table-cell"><input type="datetime-local" class="form-control" name="scheduled" min="<?php echo $hoy; ?>" value="<?php echo date("Y-m-d\TH:i", strtotime($fila->scheduled)); ?>"></div>
                <div class="table-cell"><?php echo form_dropdown('agentes', $agentes2, $fila->id_user, 'class="form-control"'); ?></div>
                <div class="table-cell"><textarea name='observations' class="form-control"><?php echo $fila->observations; ?></textarea></div>
                <div class="table-cell"><?php echo form_submit('reagendar','Reagendar','class="btn btn-info"'); ?></div>
                <div class="table-cell"><?php echo form_submit('cancelar','Cancelar','class="btn btn-secondary"'); ?></div>
            <?php echo form_close(); ?>
        <?php endforeach; ?>
    </div><br />
    <div class="row">
        <div class="text-center">
            <nav aria-label="Page navigation" id="pagination">
            </nav>
        </div>
    </div>
</div>
