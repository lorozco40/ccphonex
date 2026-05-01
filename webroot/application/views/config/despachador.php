<script type="text/javascript">var primeracola = '<?php foreach($colas as $key=>$cola){if(is_numeric($key)){echo $primeracola=$key; break;}}?>';</script>
<script type="text/javascript">var colas = JSON.parse('<?php echo json_encode($colas); ?>');</script>
<style media="screen">
    .pastilla {
        border: 1px solid gray;
        border-radius: 10px;
    }
    .input-group-text {
        min-width: 115px;
    }
</style>
<div class="container-fluid main" id="disp_page">
    <div class='row'>
        <div class="col-md-1">
            <h1>Despachadores</h1>
        </div>
        <div class="col-md-4 offset-md-7">
            <form action='<?php echo site_url("despachador/adddesp"); ?>' method='post' id='adddesp' class='form'>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Campaña</span>
                    </div>
                    <select id="campana" class="form-control" name="campana">
                        <option value="0">-- Seleccionar --</option>
                        <?= options_select_campaign($campanas) ?>
                    </select>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Nombre</span>
                    </div>
                    <input name="name" id="despname" class='form-control' placeholder="Despachador" required />
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Nuevo</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-2">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <?php if ($selcamp==0) { $active = "active"; $selected = "true"; } else {$active = ""; $selected = "false"; } ?>
                <?php foreach($camps as $camp): ?>
                    <?php if ($camp->active==1): ?>
                        <?php if($selcamp==$camp->id) { $active = "active"; $selected = "true"; } ?>
                        <a class="nav-link <?php echo $active; ?>" id="a<?php echo $camp->id; ?>tab" data-toggle="pill" href="#a<?php echo $camp->id; ?>" role="tab" aria-controls="a<?php echo $camp->id; ?>" aria-selected="<?php echo $selected; ?>"><?php echo $camp->name; ?></a>
                        <?php $active = ""; $selected = "false"; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <a class="nav-link" id="archivetab" data-toggle="pill" href="#archive" role="tab" aria-controls="archive">Archivo</a>
            </div>
        </div>
        <div class='col-10'>
            <div class="tab-content">
                <?php if ($selcamp==0) { $active = "active"; $show = "show"; } else { $active = $show = ""; } ?>
                <?php foreach($camps as $camp): ?>
                    <?php if ($camp->active==1): ?>
                        <?php if($selcamp==$camp->id) { $active = "active"; $show = "show"; } ?>
                        <div class="tab-pane fade <?php echo $show." ".$active; ?>" id="a<?php echo $camp->id; ?>" role="tabpanel" aria-labelledby="a<?php echo $camp->id; ?>tab">
                            <?php if(count($camp->csvs)>0): ?>
                                <?php foreach($camp->csvs as $csv): ?>
                                    <?php include(APPPATH.'views/despachador/csv.php'); ?>
                                <?php endforeach; ?>
                            <?php elseif($camp->entries>0): ?>
                                <?php include(APPPATH.'views/despachador/info.php'); ?>
                            <?php else: ?>
                                <div class="row">
                                    <ul>
                                        <h1 style="text-align: left;">Agregar csv</h1>
                                        <h3>Para empezar agrega un archivo csv con la base de datos a completar.</h3><br>

                                        <li>El archivo deberá estar separado por comas (<strong> , </strong>).</li>
                                        <li><strong>NO</strong> debe contener comillas simples(<strong> ' </strong>) ni dobles(<strong> " </strong>) en los valores, pero las puede tener como delimitadores de campo.</li>
                                        <li>Los encabezados <strong>NO</strong> deben contener caracteres especiales <strong> " ! # $ % & ' ( ) * + , - . / </strong>.</li>
                                        <li><strong>NO</strong> deberá contener ningún encabezado con la palabra reservada <strong>id</strong>.</li>
                                        <li>Deberá tener un encabezado con el nombre de las columnas como quieres que se vean en el formulario.</li>
                                        <li>Deberá tener forzosamente una columna con nombre "<strong> Teléfono </strong>".</li>
                                        <li>Los datos deben estar homologados(tener el mismo formato) para ser reconocidos.</li>
                                        <li>Los campos de fecha deberán tener el formato "<strong>AAAA-mm-dd H-m-s</strong>".</li>
                                        <li>Los campos con valores deberán tener el formato como numero standar. </li>
                                        <li>El largo máximo de caracteres por campo es de 255.</li>
                                        <li>Para buscar los registros por Nombre de cliente, debera tener una columna con el nombre "<strong>Cliente</strong>" (nombre completo).</li>
                                    </ul>
                                </div><br/ >
                                <div class="row">
                                    <?php echo form_open_multipart('despachador/subir', 'class="form" role="form"'); ?>
                                        <input type="hidden" name="id_desp" value="<?php echo $camp->id; ?>">
                                        <!-- <input name="elcsv" class="form-control" type="file" />
                                        <input type="submit" class="btn btn-info" name="Enviar" value="Enviar" /> -->
                                        <input name="elcsv" class="btn btn-info" type="file" style="width: 400px;"/>
                                        <input type="submit" class="btn btn-info" name="Enviar" value="Enviar"/>
                                    <?php echo form_close(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php $active = ""; $show = ""; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="tab-pane fade" id="archive" role="tabpanel" aria-labelledby="archivetab">
                    <?php foreach($camps as $camp): ?>
                        <?php if ($camp->active==0): ?>
                            <div class="row">
                                <div class="col">
                                    <?php echo $camp->name.": ".$camp->entries; ?> Registros,
                                    <?php echo $camp->entries - $camp->finished - $camp->partial; ?> Nuevos,
                                    <?php echo $camp->partial; ?> Parciales,
                                    <?php echo $camp->finished; ?> Finalizados.
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <?php echo form_open('despachador/accion', 'class="form"', array('id_desp'=>$camp->id)); ?>
                                        <button type="submit" class="btn btn-info" name="reactivar" value="reactivar">Reactivar</button>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <button type="submit" class="btn btn-danger" name="eliminar" value="eliminar"  data-toggle="confirmation">Eliminar</button>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                            <hr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- //////////////////////////  Modal agregar registros a despachador ////////////////// -->

<div class="modal fade" id="ModalAddRegs" tabindex="-1" role="dialog" aria-labelledby="ModalAddRegsTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="ModalAddRegsTitle">Agregar registros</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo form_open_multipart('despachador/subir', 'class="form" role="form"'); ?>
                <input type="hidden" name="id_desp" value="0">
                <input type="hidden" name="masregs" value="sihay">
                <div class="modal-body">
                    <p><strong>IMPORTANTE!</strong> El archivo deberá contener las mismas columnas que el despachador existente y los datos deberán estar en el mismo órden.</p>
                    <input name="elcsv" class="btn btn-info" type="file" style="width: 400px;"/>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Agregar</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
