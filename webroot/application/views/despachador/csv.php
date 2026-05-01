<div class="card">
    <div class="card-header">
        <p><?php echo $csv->name; ?></p>
        <?php echo form_open('despachador/delcsv', 'class="form form-inline"', array('id_csv'=>$csv->id, 'csv'=>$csv->name)); ?>
            <button type="submit" class="btn btn-info">Eliminar</button>
        <?php echo form_close(); ?>
    </div>
    <ul class='list-group list-group-flush'>
        <form action='<?php echo site_url("despachador/pasarcsvamysql") ?>' class='form' method='post'>
            <input type='hidden' name='id_desp' value='<?php echo $camp->id; ?>' />
            <input type='hidden' name='csv' value='<?php echo $csv->name; ?>' />
            <li class='list-group-item'>
                <div class="row">
                    <div class="col">Columna</div>
                    <div class="col">Nombre</div>
                    <div class="col">Tipo</div>
                    <div class="col">Valores</div>
                    <div class="col">Orden</div>
                    <div class="col">Lectura</div>
                    <div class="col">Requerido</div>
                    <div class="col">Usar</div>
                </div>
            </li>
            <?php foreach ($csv->fields as $key => $value): ?>
                <li class='list-group-item'>
                    <div class="row">
                        <div class="col">
                            <input type="text" name="col<?php echo $key; ?>" class="form-control" value="<?php echo $key; ?>" readonly>
                        </div>
                        <div class="col">
                            <input type="text" name="name<?php echo $key; ?>" class="form-control" value="<?php echo $value; ?>">
                        </div>
                        <div class="col">
                            <select class="form-control options" name="c<?php echo $camp->id; ?>type<?php echo $key; ?>"
                                data-camp="<?php echo $camp->id; ?>" data-id="<?php echo $key; ?>">
                                <?php // $tiposv = array("text"=>"Texto corto", "textarea"=>"Texto largo", "checkbox"=>"Check", "dropdown"=>"Lista", "radio"=>"Opciones"); ?>
                                <option value="text" selected>Texto corto</option>
                                <option value="textarea">Texto largo</option>
                                <option value="checkbox">Check</option>
                                <option value="dropdown">Lista</option>
                                <option value="radio">Opciones</option>
                                <option value="url">Url</option>
                                <option value="datetime">Fecha y hora</option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="text" name="c<?php echo $camp->id; ?>values<?php echo $key; ?>" class="form-control" value="" readonly>
                        </div>
                        <div class="col">
                            <input type="text" name="order<?php echo $key; ?>" class="form-control" value="">
                        </div>
                        <div class="col">
                            <input type="checkbox" name="readonly<?php echo $key; ?>" class="form-control" value="1">
                        </div>
                        <div class="col">
                            <input type="checkbox" name="required<?php echo $key; ?>" class="form-control" value="1">
                        </div>
                        <div class="col">
                            <input type="checkbox" name="use<?php echo $key; ?>" class="form-control" value="1" checked>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
            <li class='list-group-item'>
                <button type='submit' class='btn btn-info'>Pasar</button>
            </li>
        </form>
    </ul>
</div>
