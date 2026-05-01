<div class="container main">
    <div class="row">
        <div class="col-md-1">
            <h3>Preguntas&nbsp;asignadas&nbsp;a&nbsp;la&nbsp;cédula:&nbsp;</h3>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <span class="text text-info" style="font-size: 1.3em;"><?php echo $quality->name; ?> (<?= $quality->type ?>)</span>
        </div>
    </div>
    <hr>
    <div class="row">
    </div>
    <?php $disabled = ($suma_weight >= 100 || $is_blocked) ? "disabled" : "" ?>
    <?php echo form_open('calidad/addfieldcoment', array('role'=>'form', 'id'=>'form_add_comment', 'class'=>'form-inline'), array("id_quality"=>$quality->id)); ?>
    <?php echo form_close(); ?>
    <?php echo form_open('calidad/deletefield', array('role'=>'form', 'id'=>'form_delete_field', 'class'=>'form-inline'), array("id_quality_fields"=>'')); ?>
    <?php echo form_close(); ?>
    <div class="table table-striped">
        <div class="table-header-group">
            <div class="table-cell">Pregunta</div>
            <div class="table-cell">Ponderación</div>
            <div class="table-cell">No. Orden</div>
            <div class="table-cell">Acción</div>
        </div>
        <?php echo form_open('calidad/crearc', array('role'=>'form', 'class'=>'form-inline', 'class'=>'table-row'), array("id_quality"=>$quality->id)); ?>
            <div class="table-cell"><input class="form-control" type="text" name="question" placeholder="Ingresa la pregunta" maxlength="100" <?php echo $disabled; ?> /></div>
            <div class="table-cell"><input class="form-control" type="number" name="weight" placeholder="Asignar valor" <?php echo $disabled; ?> /></div>
            <div class="table-cell"><input class="form-control" type="number" name="num_order" placeholder="Asignar orden" <?php echo $disabled; ?> /></div>
            <div class="table-cell"><?php echo form_submit('agregar', 'Agregar', "class='btn btn-primary' $disabled");?></div>
            <div class="table-cell">
                <?php if(!$has_comment_field): ?>
                    <button type="button" onclick="cc.addComentarioForm(<?= $quality->id ?>)" class="btn btn-primary" <?= ($is_blocked) ? "disabled" : "" ?>>Comentario</button>
                <?php endif ?>
            </div>
        <?php echo form_close(); ?>
        <?php $total=0; foreach ($data as $fila): ?>
            <?php $total += $fila->weight; ?>
            <?php $disabled = $is_blocked ? "disabled" : "" ?>
            <?php echo form_open('calidad/actualizarc', array('class'=>'table-row'), array('id' => $fila->id, 'id_quality'=>$quality->id)); ?>
                <div class="table-cell"><?php echo form_input('question', $fila->question, 'class="form-control"'); ?></div>
                <div class="table-cell"><?php echo form_input('weight', $fila->weight, 'class="form-control"'); ?></div>
                <div class="table-cell"><?php echo form_input('num_order', $fila->num_order, 'class="form-control"'); ?></div>
                <div class="table-cell"><?php echo form_submit('guardar', 'Actualizar', 'class="btn btn-info" '.$disabled); ?></div>
                <div class="table-cell">
                    <button type="button" onclick="cc.deleteQuestionForm(<?= $fila->id ?>)" class="btn btn-danger" <?= $disabled ?>>Eliminar</button>
                </div>
            <?php echo form_close(); ?>
        <?php endforeach; ?>
    </div>
    <hr>
    <?php if( $quality->active == 1 && !$is_blocked ): ?>
        <span><strong class="text text-warninsg">Si la cédula esta activa, no podrás modificar la ponderación de las preguntas.</strong></span><br />
    <?php endif ?>
    <?php if( $is_blocked ): ?>
        <span class="text text-warning">Esta cédula esta bloqueda. Ya hay registros calificados con ella.</span> <br />
    <?php endif ?>
    <span>La ponderación total por cédula es de <strong class="text text-info">100</strong> puntos,&nbsp;<?php $restan = 0; $restan = (100 - $total); echo "disponibles:<strong class='text text-info'>&nbsp;$restan</strong> puntos." ?></span><br />
    <div class="row">
        <div class="col-md-12 text-center">
            <nav aria-label="Page navigation">
                <?php echo $pagination; ?>
            </nav>
        </div>
    </div>
</div>
