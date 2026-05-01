<?php
$paramercadopago = 0;
$mpnecesarios = ["precio", "idpago"];
foreach($camp->fields as $field) {
    if(in_array($field->slug, $mpnecesarios)) {
        $paramercadopago++;
    }
}
$visible = ($camp->autodial=='predictivo' || $camp->autodial=='predictivoamd') ? '' : 'style="display:none;"';
?>
<h3>
    <?php echo $camp->name.": ".$camp->entries; ?> Registros,
    <?php echo $camp->entries - $camp->finished - $camp->partial; ?> Nuevos,
    <?php echo $camp->partial; ?> Parciales,
    <?php echo $camp->finished; ?> Finalizados.
</h3>
<h3>
    Campaña asiganda:
    <?php
    foreach ($campanas as $c => $campana) {
        if( $campana->id == $camp->id_campaign ) {
            echo $campana->name;
            continue;
        }
    }
    ?>
</h3>
<div class="row mt-4">
    <div class="col col-lg-6">
        <div class="row">
            <div class="col">
                <?php if(empty($camp->running)): ?>
                    <form class="form" action="<?php echo site_url('despachador/accion'); ?>" method="post">
                        <div class="form-row">
                            <input type="hidden" name="id_desp" value="<?=$camp->id?>" />
                            <input type="hidden" name="cola" id="colaval<?=$camp->id?>" value="<?php echo (empty($camp->queue)) ? $primeracola : $camp->queue; ?>" />
                            <div class="form-check form-check-inline">
                                <input class="form-check-input tipodesp" type="radio" data-id_desp="<?=$camp->id?>" name="autodial" id="tipodesp1<?=$camp->id?>" value="manual" checked>
                                <label data-toggle="tooltip" data-original-title="Solo despliegue de información en consola." class="form-check-label" for="tipodesp1<?=$camp->id?>">Manual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input tipodesp" type="radio" data-id_desp="<?=$camp->id?>" name="autodial" id="tipodesp2<?=$camp->id?>" value="progresivo" <?php echo ($camp->autodial=='progresivo')?"checked":""; ?>>
                                <label data-toggle="tooltip" data-original-title="Despliegue en consola y automarcado." class="form-check-label" for="tipodesp2<?=$camp->id?>">Progresivo</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input tipodesp" type="radio" data-id_desp="<?=$camp->id?>" name="autodial" id="tipodesp3<?=$camp->id?>" value="predictivo" <?php echo ($camp->autodial=='predictivo')?"checked":""; ?>>
                                <label data-toggle="tooltip" data-original-title="Marca en segundo plano y envía llamadas contestadas a la cola. Humano y buzón." class="form-check-label" for="tipodesp3<?=$camp->id?>">Predictivo</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input tipodesp" type="radio" data-id_desp="<?=$camp->id?>" name="autodial" id="tipodesp4<?=$camp->id?>" value="predictivoamd" <?php echo ($camp->autodial=='predictivoamd')?"checked":""; ?>>
                                <label data-toggle="tooltip" data-original-title="Marca en segundo plano y envía llamadas contestadas a la cola, solo humano." class="form-check-label" for="tipodesp4<?=$camp->id?>">Predictivo AMD</label>
                            </div>
                        </div>
                        <br />
                        <div>
                            <input type="submit" class="btn btn-primary mr-4" value="Iniciar" data-toggle="confirmation" name="activar" />
                            <input type="submit" class="btn btn-info" value="Archivar" data-toggle="confirmation" name="archivar" />
                        </div>
                    </form>
                <?php else: ?>
                    Despachador <?=ucfirst($camp->autodial)?>, En operación !
                    <form class="form form-inline" action="<?php echo site_url('despachador/accion'); ?>" method="post">
                        <input type="hidden" name="id_desp" value="<?=$camp->id?>" />
                        <input type="submit" class="btn btn-info" value="Detener" data-toggle="confirmation" name="detener" />
                    </form><br />
                <?php endif; ?>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col">
                <button type="button" class="btn btn-info despaddregs" data-id="<?=$camp->id?>">Agregar registros</button>
            </div>
        </div>
        <div class="row mt-4" <?=$visible?> id="toques<?=$camp->id?>">
            <div class="col">
                <div class="form-group">
                    <label for="rounds">Toques por registro para auto-cierre:</label>
                    <select class="form-control w-25 vueltas" data-id_desp="<?=$camp->id?>">
                        <option value="0" selected>Sólo cierre manual</option>
                        <option value="7"<?php if ($camp->rounds==7) echo 'selected'; ?>>7</option>
                        <option value="5"<?php if ($camp->rounds==5) echo 'selected'; ?>>5</option>
                        <option value="3"<?php if ($camp->rounds==3) echo 'selected'; ?>>3</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col col-lg-3">
        <div id="preddata<?=$camp->id?>" class="row" <?=$visible?>>
            <div class="col pastilla text-center">
                <h5>Modificadores lanzador</h5>
                <!--p>Si haces alguna modificación, asegurate guardarla antes de iniciar el despachador.</p-->
                <form class="form formpreddata" action="<?=site_url('despachador/preddata')?>" method="post" onsubmit="esperar()">
                    <input type="hidden" name="id_desp" value="<?=$camp->id?>">
                    <input type="hidden" name="autodial" value="<?=$camp->autodial?>" id="despauto<?=$camp->id?>">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="gateway">Gateway</label>
                        </div>
                        <input type="text" name="gateway" class="form-control" value="<?=$camp->gateway?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="gateway">Salida</label>
                        </div>
                        <input type="text" name="dialer" class="form-control" value="<?=$camp->dialer?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="gateway">Máscara</label>
                        </div>
                        <input type="text" name="maskname" class="form-control" value="<?php echo $camp->maskname; ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="gateway">Número</label>
                        </div>
                        <input type="text" name="masknum" class="form-control" value="<?php echo $camp->masknum; ?>">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="gateway">Multiplicador</label>
                        </div>
                        <select name="multi" class="form-control">
                            <option value="1" selected>1</option>
                            <option value="2" <?php if ($camp->multi==2) echo 'selected'; ?>>2</option>
                            <option value="3" <?php if ($camp->multi==3) echo 'selected'; ?>>3</option>
                            <option value="4" <?php if ($camp->multi==4) echo 'selected'; ?>>4</option>
                            <option value="5" <?php if ($camp->multi==5) echo 'selected'; ?>>5</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="button">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
    <?php if($paramercadopago==2): ?>
        <div class="col col-lg-2 offset-lg-1">
            <div class="row">
                <div class="col pastilla text-center">
                    <h5>Mostrar botón mercado pago Link</h5>
                    <form class="form" action="<?=site_url('desp/activarmp')?>" method="post">
                        <input type="hidden" name="iddesp" value="<?=$camp->id?>">
                        <input type="hidden" name="idcamp" value="<?=$camp->id_campaign?>">
                        <input type="text" name="xclientid" value="" class="form-control" placeholder="X-Client-ID">
                        <input type="text" name="token" value="" class="form-control" placeholder="Token">
                        <br />
                        <button type="submit" name="button" class="btn btn-primary">Mostrar</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
    <hr class="mt-5" />
    <h5>Condiciones</h5>
    <?php if (count($camp->condis)>=1): ?>
        <?php foreach ($camp->condis as $condi): ?>
            <div class="row">&nbsp;&nbsp;&nbsp;&nbsp;
                <form class="form form-inline" method="post" action="<?php echo site_url('despachador/delcond'); ?>">
                <input type="hidden" name="id_desp" value="<?=$camp->id?>" />
                <input type="hidden" name="id" value="<?php echo $condi->id; ?>">
                <input type="submit" class="btn btn-info" value="Quitar"></form>&nbsp;&nbsp;<?php echo $condi->des; ?>
            </div><br />
            <?php endforeach; ?>
        <?php else: ?>
            <p>Sin condiciones</p>
    <?php endif; ?>
    <hr />
    <form class="form row" action="<?php echo site_url('despachador/addcond'); ?>" method="post">
        <div class="col">
            <input type="hidden" name="id_desp" value="<?=$camp->id?>" />
            <div class="form-row">
                <div class="form-group col-1">
                    <label for="hora">Hora</label>
                    <input type="time" class="form-control" name="hora" value="00:00">
                </div>
                <div class="form-group col-1">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="accion" value="1" id="accion1<?= $camp->id ?>">
                        <label class="form-check-label" for="accion1<?= $camp->id ?>">
                            Iniciar
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="accion" value="0" checked id="accion3<?= $camp->id ?>">
                        <label class="form-check-label" for="accion3<?= $camp->id ?>">
                            Cambio
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="accion" value="2" id="accion2<?= $camp->id ?>">
                        <label class="form-check-label" for="accion2<?= $camp->id ?>">
                            Detener
                        </label>
                    </div>
                </div>
                <div class="form-group col-3">
                    <label for="tipi">Tipificación</label>
                    <select name="tipi" class="form-control">
                        <option value="" selected>-- Todas --</option>
                        <?php foreach ($camp->yatipis as $key => $value): ?>
                            <option value="<?=$value?>"><?=$value?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-3">
                    <label for="campo">Campo interno</label>
                    <select name="campo" class="form-control">
                        <option value="" selected>-- Ninguno --</option>
                        <?php foreach ($camp->fields as $key => $field): ?>
                            <option value="<?=$field->name?>"><?=$field->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-3">
                    <label for="camval">Valor campo interno</label>
                    <input type="text" name="camval" class="form-control">
                </div>
                <div class="form-group col-1">
                    <input type="submit" class="btn btn-primary" value="Agregar" name="Agregar" />
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <?php if(empty($camp->running)): ?>
            <div class="col-sm-12 col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Campos Base</h4>
                    </div>
                    <ul class='list-group'>
                        <li class='list-group-item'>
                            <button class="btn btn-primary addField" id="addField<?=$camp->id?>">Nuevo</button>
                        </li>
                        <li class='list-group-item'><table>
                            <?php foreach($camp->fields as $field) {
                                if ($field->typedb == 0) {
                                    echo "<tr><td>".$field->name.
                                        " </td><td><button class='btn btn-secondary updField obj".$camp->id."tab' data-id='".$field->id."'>Modificar</button></td><td>";
                                    echo form_open('despachador/del_field', array('role'=>'form', 'class'=>'form delform'),
                                        array('id'=>$field->id, 'slug'=>$field->slug, 'id_desp'=>$camp->id, 'typedb'=>'0'));
                                    echo "<button type='submit' class='btn btn-info'>Eliminiar</button></div>";
                                    echo form_close();
                                    echo "</td></tr>";
                                }
                            } ?>
                        </table></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Tipificaciones X Llamada</h4>
                    </div>
                    <ul class='list-group'>
                        <li class='list-group-item'>
                            <button class="btn btn-primary addQualif" id="addQualif<?=$camp->id?>">Nueva</button>
                        </li>
                        <li class='list-group-item'><table>
                            <?php foreach($camp->fields as $field) {
                                if ($field->typedb == 1) {
                                    echo "<tr><td>".$field->name.
                                        " </td><td><button class='btn btn-secondary updField obj".$camp->id."tab' data-id='".$field->id."'>Modificar</button></td><td>";
                                    echo form_open('despachador/del_field', array('role'=>'form', 'class'=>'form delform'),
                                        array('id'=>$field->id, 'slug'=>$field->slug, 'id_desp'=>$camp->id, 'typedb'=>'1'));
                                    if( strtolower($field->slug) == 'comentarios' or str_replace("ó", "o", strtolower($field->slug)) == 'tipificacion' ) {
                                        echo "Obligatorio";
                                    } else {
                                        echo "<button type='submit' class='btn btn-info'>Eliminiar</button></div>";
                                    }
                                    echo form_close();
                                    echo "</td></tr>";
                                }
                            } ?>
                        </table></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <div class="col-sm-12 col-md-4">
            <div class="card" id="desp_colas<?=$camp->id?>" <?php echo ($camp->autodial!='predictivo' && $camp->autodial!='predictivoamd')?"style='display:none;'":""; ?>>
                <form action="<?php echo site_url('despachador/accion'); ?>" method="post">
                    <input type="hidden" name="id_desp" value="<?=$camp->id?>" />
                    <div class="card-header">
                        <h4>Cola
                            <?php if(!empty($camp->running)): ?>
                                <input type="submit" class="btn btn-info" value="Guardar" data-toggle="confirmation" name="actualizarcola" />
                            <?php endif; ?>
                        </h4>
                    </div>
                    <ul class='list-group'>
                        <li class='list-group-item'>
                            <select class="form-control queuedes" name="queue" id="cola<?=$camp->id?>" data-campid="<?=$camp->id?>">
                                <option value="">-- elegir --</option>
                                <?php foreach($colas as $key => $cola): ?>
                                        <?php if (is_numeric($key) && in_array($key, $name_colas)): ?>
                                            <option value='<?php echo $key; ?>'<?php echo ($key==$camp->queue) ? " selected" : ""; ?>><?php echo $key; ?></option>
                                        <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </li>
                        <span>
                            <?php if($camp->queue != '' && in_array($camp->queue, $name_colas)): ?>
                                <?php foreach($colas[$camp->queue]["members"] as $keym => $valm): ?>
                                    <li class='list-group-item'>
                                       <?php echo $keym; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </span>
                    </ul>
                </form>
            </div>
            <div class="card" id="desp_agentes<?=$camp->id?>" <?php echo ($camp->autodial=='predictivo' || $camp->autodial=='predictivoamd')?"style='display:none;'":""; ?>>
                <div class="card-header">
                    <h4>Agentes</h4>
                </div>
                <ul class='list-group'>
                    <li class='list-group-item'>
                        <form action="<?php echo site_url('despachador/adduser'); ?>" class='form' method='post'>
                            <input type='hidden' name='id_desp' value='<?=$camp->id?>' />
                            <div class="input-group">
                                <select class="form-control" name="id_user">
                                    <?php foreach($users as $user): ?>
                                        <option value='<?php echo $user->id; ?>'><?php echo $user->name.' '.$user->last; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-append">
                                    <button type='submit' class='btn btn-primary'>Agregar</button>
                                </div>
                            </div>
                        </form>
                    </li>
                    <?php foreach($camp->users as $user): ?>
                        <li class='list-group-item'>
                            <form action='<?php echo site_url("despachador/deluser") ?>' class='form' method='post'>
                                <input type='hidden' name='id_desp' value='<?=$camp->id?>' />
                                <input type='hidden' name='id_user' value='<?php echo $user->id; ?>' />
                                <?php echo $user->name; ?>
                                <button type='submit' class='btn btn-secondary'>Quitar</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col"><?php include(APPPATH.'views/despachador/cats.php'); ?></div>
    </div>
    <div class="row">
        <div class="col"><?php include(APPPATH.'views/despachador/actions.php'); ?></div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <strong> -- Despachadores Predictivo y predictivo AMD --</strong>
            <p>El sistema marca el registro como cerrado o terminado automáticamente al desplegarse el número de toques elegido.</p>
            <p>El despachador se detiene automáticamente al no haber más registros por marcar</p>
            <strong> -- Condiciones --</strong>
            <p>El horario siempre aplica, si quieres una sola condición durante todo el día, solo deja la hora 00:00</p>
            <p>Los valores de los campos internos deben estar homologados para funcionar, debes escribir su valor tal cual
                está en la base de datos (mayúsculas, minúsculas, etc). Puedes tener más de un valor separando
                por , (coma), por lo tanto éste caracter está prohibido en el campo que quieras filtrar.</p>
            <p>
                Importante: Se crean condiciones para iniciar, detener o cambiar, cada una con una hora específica. El sistema revisa todas las condiciones y ejecuta la que tenga la hora más cercana a la actual, siempre que esa hora sea menor o igual a la hora actual.
            </p>
            <p>
                <strong>Ejemplo:</strong><br/>
                Si tienes las siguientes condiciones:<br/>
                <ul>
                    <li>09:00:00, Iniciar</li>
                    <li>10:30:00, Tipificación: Buzón</li>
                    <li>12:00:00, Detener</li>
                </ul>
                Y la hora actual es 10:45:00, el sistema ejecutará la condición de "10:30:00, Tipificación: Buzón"<br>
                Ya que es la condición con el horario mas alto y que a su vez, el horario es menor a la hora actual.
            </p>
        </div>
    </div>

<div class="modal fade" id="fieldModal<?php echo $camp->id?>" tabindex="-1" role="dialog" aria-labelledby="fieldModalLabel<?=$camp->id?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('despachador/upd_field', array('role'=>'form', 'class'=>'form', 'id'=>'fieldForm'.$camp->id)); ?>
            <div class="modal-header">
                <h5 class="modal-title" id="fieldModalLabel<?php echo $camp->id?>">Campo de despachador</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id<?=$camp->id?>" value="0" />
                <input type="hidden" name="id_desp" id="id_desp<?=$camp->id?>" value="<?=$camp->id?>"/>
                <input type="hidden" name="oldname" id="oldname<?=$camp->id?>" value="" />
                <input type="hidden" name="typedb" id="typedb<?=$camp->id?>" value="" />
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" value="" id="name<?=$camp->id?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="type">Tipo</label>
                    <select class="form-control" name="type" id="type<?=$camp->id?>">
                        <option value="text" selected="">Texto corto</option>
                        <option value="textarea">Texto largo</option>
                        <option value="checkbox">Check</option>
                        <option value="dropdown">Lista</option>
                        <option value="radio">Opciones</option>
                        <option value="url">Url</option>
                        <option value="date">Fecha</option>
                        <option value="datetime">Fecha y hora</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="opciones">Valores</label>
                    <input type="text" name="opciones" value="" id="opciones<?=$camp->id?>" class="form-control">
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="order">Orden</label>
                            <input type="text" name="order" id="order<?=$camp->id?>" class="form-control" value="">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="readonly">Solo lectura</label>
                            <input type="checkbox" name="readonly" id="readonly<?=$camp->id?>" class="form-control" value="1">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="required">Requerido</label>
                            <input type="checkbox" name="required" id="required<?=$camp->id?>" class="form-control" value="1">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <h5>Dependencia</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="depend" id="inlineRadio<?= $camp->id ?>0" value="0" checked>
                            <label class="form-check-label" for="inlineRadio0">No</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="depend" id="inlineRadio<?= $camp->id ?>1" value="1">
                            <label class="form-check-label" for="inlineRadio1">1</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="depend" id="inlineRadio<?= $camp->id ?>2" value="2">
                            <label class="form-check-label" for="inlineRadio2">2</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="depend" id="inlineRadio<?= $camp->id ?>3" value="3">
                            <label class="form-check-label" for="inlineRadio3">3</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="depend" id="inlineRadio<?= $camp->id ?>4" value="4">
                            <label class="form-check-label" for="inlineRadio4">4</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
