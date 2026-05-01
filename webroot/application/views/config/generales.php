<div class="container main">
    <?php $ft = array("1"=>["dd-mm-yy","d-m-Y"],"2"=>["mm-dd-yy","m-d-Y"],"3"=>["yy-mm-dd","Y-m-d"]); ?>
    <div class="row">
        <h2>Configuración general del sistema <strong style="color: #B40404; text-shadow: 2px 2px 5px black;">(Cuidado!!!)</strong></h2>
    </div>
    <hr>
    <h4>Estas configuraciones han quedado obsoletas y proximamente
        desaparecerá está página. Si tienes dudas, comentarios o
        alguna configuración por realizar, por favor consulta con tu
        programador favorito (Kinon). Dirígete a
        <a href="<?=site_url('campanas')?>">Configuración / Admin / Campañas</a></h4>
    <!--
    <?php /* echo form_open('generales/guardar', 'class="form"'); ?>
        <?php $count = 1; ?>
        <?php foreach($data as $key => $row): ?>
            <?php if($count==1) echo '<div class="row">'; ?>
                <div class="col-md">
                    <h5><?php echo ucwords($key); ?></h5>
                    <?php foreach($row as $keyin => $val): ?>
                        <?php if($val->eti=="FormatoFechaMysql"): ?>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Fomato Fechas</span>
                                </div>
                                <select class="form-control" name="<?php echo $val->id; ?>" id="<?php echo $val->eti; ?>">
                                    <option value="%d-%m-%Y %H:%i:%s" data-tipo="1" <?php if($val->val=="%d-%m-%Y %H:%i:%s") echo "selected"; ?>>Día Mes Año</option>
                                    <option value="%m-%d-%Y %H:%i:%s" data-tipo="2" <?php if($val->val=="%m-%d-%Y %H:%i:%s") echo "selected"; ?>>Mes Día Año</option>
                                    <option value="%Y-%m-%d %H:%i:%s" data-tipo="3" <?php if($val->val=="%Y-%m-%d %H:%i:%s") echo "selected"; ?>>Año Mes Día</option>
                                </select>
                            </div>
                        <?php elseif ($val->eti == "FormatoFechaJs" || $val->eti == "FormatoFechaInput"): ?>
                            <input type="hidden" name="<?php echo $val->id; ?>" id="<?php echo $val->eti; ?>" value="<?php echo $val->val; ?>">
                        <?php else: ?>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><?php echo $val->eti; ?></span>
                                </div>
                                <input name="<?php echo $val->id; ?>" id="<?php echo $val->id; ?>"
                                    type="text" class="form-control" value="<?php echo $val->val; ?>" />
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php if($count==2) { $count = 0; echo "</div><hr>"; } ?>
            <?php $count++; ?>
            <?php endforeach; ?>
            <?php if($count>1) { echo "</div><hr>"; } ?>
        <div class="row">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        <style>.input-group-text { width: 14em; }</style>
    <?php echo form_close(); */ ?>
    -->
</div>
