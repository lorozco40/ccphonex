<div class="tmain">
<ul class="nav nav-tabs" role="tablist" id="lostabos">
    <?php if (in_array('home', $agente['permisoSec'])): $nexact = ''; ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="Consola"><a class="nav-link active" data-toggle="tab" role="tab" href="#consola"><i class="fa fa-home"></i></a></li>
    <?php else: $nexact = 'active'; ?>
    <?php endif; ?>
    <?php if (in_array('chat', $agente['permisoSec']) && !empty($agente['chatUrl'])): ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="LHC"><a class="nav-link <?=$nexact?>" data-toggle="tab" role="tab" href="#lhc">LHC</a></li>
    <?php $nexact = ''; endif; ?>
    <?php if (in_array('chat', $agente['permisoSec'])): ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="Chat"><a class="nav-link <?=$nexact?>" data-toggle="tab" role="tab" href="#chat"><i class="fa fa-comments"></i></a></li>
    <?php $nexact = ''; endif; ?>
    <?php if (in_array('email', $agente['permisoSec']) && !empty($agente['ctas_email'])): ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="Email"><a class="nav-link <?=$nexact?>" data-toggle="tab" role="tab" href="#emailconsolax"><i class="fa fa-envelope"></i></a></li>
    <?php $nexact = ''; endif; ?>
    <?php if (in_array('sms', $agente['permisoSec'])): ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="S M S"><a class="nav-link <?=$nexact?>" data-toggle="tab" role="tab" href="#sms"><i class="far fa-comment-dots"></i></a></li>
    <?php $nexact = ''; endif; ?>
    <?php if (in_array('pit', $agente['permisoSec'])): ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="P I T"><a class="nav-link <?=$nexact?>" data-toggle="tab" role="tab" href="#pit"><i class="fa fa-bullhorn"></i></a></li>
    <?php $nexact = ''; endif; ?>
    <?php if (in_array('videocall', $agente['permisoSec'])): ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="Videollamada"><a class="nav-link <?=$nexact?>" data-toggle="tab" role="tab" href="#videochat"><i class="fas fa-video"></i></a></li>
    <?php $nexact = ''; endif; ?>
    <?php if (in_array('whatsapp', $agente['permisoSec']) && !empty($agente['whatsapp'])): ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="WhatsApp"><a class="nav-link <?=$nexact?>" data-toggle="tab" role="tab" href="#whatsapp"><i class="fab fa-whatsapp"></i></a></li>
    <?php $nexact = ''; endif; ?>
    <?php if (in_array('calendario', $agente['permisoSec'])): ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="Calendario"><a class="nav-link <?=$nexact?>" data-toggle="tab" role="tab" href="#calendario"><i class="far fa-calendar-alt"></i></a></li>
    <?php $nexact = ''; endif; ?>
    <?php if (in_array('extra', $agente['permisoSec']) && !empty($agente['extraUrl'])): ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="Extra"><a class="nav-link <?=$nexact?>" data-toggle="tab" role="tab" href="#extra">Ext</a></li>
    <?php $nexact = ''; endif; ?>
    <?php if (in_array('extra', $agente['permisoSec']) && !empty($agente['extraUrl2'])): ?>
        <li class="nav-item sele" data-toggle="tooltip" data-placement="top" title="Extra 2"><a class="nav-link <?=$nexact?>" data-toggle="tab" role="tab" href="#extra2">Ext2</a></li>
    <?php endif; ?>&nbsp;&nbsp;&nbsp;&nbsp;
    <li class="nav-item sele" id="nuevoRecLink"></li>&nbsp;
    <li class="nav-item sele" id="newemailink"></li>
</ul>
<div class="tab-content">
    <?php if (in_array('home', $agente['permisoSec'])): $nexact = ''; ?>
        <div id="consola" class="tab-pane fade show active" role="tabpanel">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="pastilla">
                        <h5>Información de llamada</h5>
                        <p><strong>Nombre: </strong><span id="cnombre"></span><br />
                            <strong>Número: </strong><span id="cnumero"></span><br />
                            <strong>Campaña: </strong><span id="ccampana"></span><br />
                            <strong>Id llamada: </strong><span id="cuniqueid"></span><br />
                            <strong>Script: </strong><span id="cscript"></span>
                        </p>
                        <input type="hidden" id="ccall_entry" />
                        <span id="agendainfo"></span>
                    </div>
                    <div class="pastilla" id="agenda">
                        <form class="form form-inline ml-3" id="agebusform">
                            <span class="btn btn-info nuevoclientebtn"><i class="fas fa-user-plus"></i></span>
                            <div class="input-group ml-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Agenda</span>
                                </div>
                                <input type="text" name="catabus" class="form-control" placeholder="Teléfono o nombre o email">
                                <div class="input-group-append">
                                    <button type="submit" name="button" class="btn btn-info" id="btnage">Buscar</button>
                                </div>
                            </div>
                        </form><br/>
                        <div id="agesearchresult"></div>
                    </div>
                    <div class="pastilla" id="ticketsabiertos"></div>
                </div>
                <div class="col-12 col-lg-6">
                    <?php if(!empty($campanas) && in_array('qualif', $agente['permisoSec'])): ?>
                        <div class="pastilla" id="campanasal">
                            <div class="row">
                                <div class="col-6 p-1">
                                    <label for="campana" class="mb-0">Campaña</label>
                                    <select class="form-control" name="formIdCam" id="formIdCam">
                                        <?php
                                        $show = true;
                                        foreach ($campanas as $campana) {
                                            if ( $campana->active == 0 && $show ) {
                                                $show = false;
                                                echo "<option disabled>──── Inactivas ────</option>";
                                            }
                                            echo "<option value='".$campana->id."'>".$campana->name."</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-6 p-1">
                                    <label for="formulario" class="mb-0">Formulario</label>
                                    <select class="form-control" name="formIdForm" id="formIdForm">
                                        <?php foreach ($forms as $form) {
                                            echo "<option value='".$form->id."'>".$form->name."</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-6 p-1">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                            <label for="id" data-toggle="tooltip" title="Opcional" class="mb-0">
                                                ID
                                            </label>
                                            </span>
                                        </div>
                                        <input type="text" name="ticketid" class="form-control" placeholder="">
                                        <div class="input-group-append">
                                            <button type="button" id="tmpform" class="btn btn-info" id="btnage">Ver</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 p-1 col-searchform" style="display: none;">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Buscar</span>
                                        </div>
                                        <input type="text" id="search_form_text" onkeypress="search_form_eval(event.keyCode)" class="form-control" placeholder="">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" onclick="search_form()">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-12" id="list-forms">
                                </div>
                            </div>
                            <div id="forms_pag" class="row" style="display: none;">
                                <div class="col">
                                    <div id="paginacion"></div>
                                </div>
                                <div class="col text-right">
                                    <p>Registros por página:</p>
                                    <select class="form-control" id="elirpp" style="max-width:5em;float:right;">
                                        <option value="10">10</option>
                                        <option value="20" selected>20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <button class="btn btn-danger delform mb-n4 ml-3" style="display: none;" id="tmpformdel">X</button>
                    <div class="pastilla" id="leform"></div>

                        <div class="pastilla d-none" id="despastilla">
                            <form class="form" method="post" id="busdespform">
                                <input type="hidden" id="id_desp" value="<?php echo $id_desp; ?>" />
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text">Despachador, telefono <?= $compl_text_busq ?>:</label>
                                    </div>
                                    <input class="form-control" type="text" id="busdespval" value="">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Buscar</button>
                                    </div>
                                </div>
                            </form>
                            <div id="busdespres"></div>
                            <div class="pastilla" id="despachador"></div>
                            <div class="pastilla" id="disp_histo"></div>
                        </div>

                </div>
            </div>
        </div>
    <?php else: $nexact='show active'?>
    <?php endif; ?>
    <?php if (in_array('chat', $agente['permisoSec']) && !empty($agente['chatUrl'])): ?>
        <div id="lhc" class="tab-pane fade <?=$nexact?>" role="tabpanel">
            <iframe frameborder="0" height="800" src="<?php echo $agente['chatUrl']; ?>" style="width:100%"></iframe>
        </div>
    <?php $nexact=''; endif; ?>
    <?php if (in_array('chat', $agente['permisoSec'])): ?>
        <div id="chat" class="tab-pane fade <?=$nexact?>" role="tabpanel">
        <?php include_once('chat.php'); ?>
        </div>
    <?php $nexact=''; endif; ?>
    <?php if (in_array('email', $agente['permisoSec']) && !empty($agente['ctas_email'])): ?>
        <div id="emailconsolax" class="tab-pane fade <?=$nexact?>" role="tabpanel">
            <?php include_once('email.php'); ?>
        </div>
    <?php $nexact=''; endif; ?>
    <?php if (in_array('sms', $agente['permisoSec'])): ?>
        <div id="sms" class="tab-pane fade <?=$nexact?>" role="tabpanel">
            <?php include_once('sms.php'); ?>
        </div>
    <?php $nexact=''; endif; ?>
    <?php if (in_array('pit', $agente['permisoSec'])): ?>
        <div id="pit" class="tab-pane fade <?=$nexact?>" role="tabpanel">
            <?php include_once('pit.php'); ?>
        </div>
    <?php $nexact=''; endif; ?>
    <?php if (in_array('videocall', $agente['permisoSec'])): ?>
        <div id="videochat" class="tab-pane fade <?=$nexact?>" role="tabpanel">
            <?php include_once('videollamada.php'); ?>
        </div>
    <?php $nexact=''; endif; ?>
    <?php if (in_array('whatsapp', $agente['permisoSec']) && !empty($agente['whatsapp'])): ?>
        <div id="whatsapp" class="tab-pane fade <?=$nexact?>" role="tabpanel">
            <?php include_once('whatsapp.php'); ?>
        </div>
    <?php $nexact=''; endif; ?>
    <?php if (in_array('calendario', $agente['permisoSec'])): ?>
        <div id="calendario" class="tab-pane fade <?=$nexact?>" role="tabpanel">
            <?php include_once('calendario.php'); ?>
        </div>
    <?php $nexact=''; endif; ?>
    <?php if (in_array('extra', $agente['permisoSec']) && !empty($agente['extraUrl'])): ?>
        <div id="extra" class="tab-pane fade <?=$nexact?>" role="tabpanel">
            <div class="row">
                <div class="col-lg">
                    <div class="pastilla">
                        <iframe frameborder="0" height="800" src="<?php echo $agente['extraUrl']; ?>" style="width:100%"></iframe>
                    </div>
                </div>
            </div>
        </div>
    <?php $nexact=''; endif; ?>
    <?php if (in_array('extra', $agente['permisoSec']) && !empty($agente['extraUrl2'])): ?>
        <div id="extra2" class="tab-pane fade <?=$nexact?>" role="tabpanel">
            <div class="row">
                <div class="col-lg">
                    <div class="pastilla">
                        <iframe frameborder="0" height="800" src="<?php echo $agente['extraUrl2']; ?>" style="width:100%"></iframe>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<div class="modal fade pastilla" id="asistransfmodal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transferencia asistida</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="sesasistransf" />
                <div class="form-group">
                    <label>Teléfono o extensión</label>
                    <input class="form-control" type="number" id="numasistransf" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="callasistransf" class="btn btn-primary">Marcar</button>
                <button type="button" id="endasistransf" class="btn btn-secondary">Transferir</button>
            </div>
        </div>
    </div>
</div>
<?php include_once(APPPATH.'views/pedazos/agendaModal.php'); ?>
<?php if(in_array('auxiliares', $agente['permisoSec'])): ?>
    <div id="estadoagente" class="alert-info">
        <div class="row">
            <div class="col col-11">
                <div class="input-group" id="iniciaBreak">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Descanso:</span>
                    </div>
                    <select id="breakList" name="breakList" class="form-control">
                        <?php foreach ($breaks as $break): ?>
                            <option value="<?php echo $break->name; ?>" <?php if($break->name=="acw") echo 'disabled'; ?>>
                                <?php echo $break->description; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-info" id="setDescanso">Iniciar</button>
                    </div>
                </div>
                <div class="input-group" id="terminaBreak">
                    <input type="text" class="form-control" id="descBreak" readonly="true">
                    <div class="input-group-append">
                        <button class="btn btn-info" id="unsetDescanso">Terminar</button>
                    </div>
                </div>
                <div id="crono">
            		<div class="reloj" id="Horas">00</div>
            		<div class="reloj" id="Minutos">:00</div>
            		<div class="reloj" id="Segundos">:00</div>
            		<div class="reloj" id="Centesimas">:00</div>
            	</div>
            </div>
            <div class="col col-1" id="edoagehide">
                <i class="fas fa-angle-double-right"></i>
                <i class="fas fa-angle-double-left" style="display:none"></i>
            </div>
        </div>
    </div>
<?php endif; ?>
<div id="confadmin"></div>
<div id="encola">
    <?php if (count($agente['queues'])>0): ?>
        <table class="table table-striped"><thead class="thead-dark"><tr><th>Cola</th><th>En espera</th></tr></thead>
            <?php foreach($agente['queues'] as $key => $cola): ?>
                <tr><td><?php echo $cola; ?></td><td id="<?php echo 'c'.$cola; ?>">0</td></tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
<!--Modal recordatorio-->
<div class="modal fade bd-example-modal-md" id="modalRecordatorio" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" id="modalancho"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalCenterTitle"><i class="fas fa-bell campana"></i>&nbsp;&nbsp;Recordatorio</h4>
            </div>
            <form action="<?php echo site_url('calendario/modificar');?>" method="post" id="recform">
                <div class="modal-body">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text nombre">Nombre</span>
                        </div>
                        <input type="hidden" class="form-control" name="id" id="recid" value="">
                        <input type="hidden" class="form-control" name="agentes" id="recagentes" value="">
                        <input type="text" class="form-control" name="name" id="recname" value="" readonly="readonly">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text apellido">Apellido</span>
                        </div>
                        <input type="text" class="form-control" name="last" id="reclast" value="" readonly="readonly">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text contacto">Tipo</span>
                        </div>
                        <input type="text" class="form-control" name="type" id="rectype" value="" readonly="readonly">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text fecha">Fecha & Hora</span>
                        </div>
                        <input type="datetime-local" class="form-control" name="scheduled" id="recscheduled" required="required">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text obser">Observaciones</span>
                        </div>
                        <textarea type="text" class="form-control" name="observations" id="recobservations" value="" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php echo form_submit('reagendar','Reagendar','class="btn btn-success"'); ?>
                    <?php echo form_submit('terminar','Terminada','class="btn btn-warning"'); ?>
                    <?php echo form_submit('cancelar','Cancelada','class="btn btn-danger"'); ?>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var telefono = "<?php echo (!empty($telefono)) ? $telefono : ''; ?>";
</script>
</div>