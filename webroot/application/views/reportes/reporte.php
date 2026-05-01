<script>let wrapable = JSON.parse(<?php echo (empty($wrapable)) ? "'[]'" : "'".json_encode($wrapable)."'"; ?>)</script>
<?php if (empty($grande)): ?>
    <div class="container main">
<?php else: ?>
    <div class="container-fluid main">
<?php endif; ?>
    <form id="repoform" target="_blank" class="form" action="<?php echo site_url("reportes/excel"); ?>" method="post" >
        <input type="hidden" id="reporte" name="reporte" value="<?php echo (isset($cual)) ? slugify($cual) : slugify($title); ?>">
        <input type="hidden" id="pag" name="pag" value="0">
        <input type="hidden" id="modelo" name="modelo" value="<?php echo (isset($modelo)) ? $modelo : 'reportes'; ?>">
        <div class="row justify-content-between">
            <div class="col-auto">
                <h1><?php echo $title; ?></h1>
            </div>
            <div class="col-1">
                <button title="Exportar a Excel" class="logos"><img src="<?php echo site_url('assets/img/excel5.png'); ?>"></button>
            </div>
        </div>
        <hr>
        <div class="row form-inline">
            <?php if (empty($nodates)): ?>
                <div class="form-group input-daterange">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="text" id="min" name="min" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
                        <div class="input-group-prepend">
                            <span class="input-group-text">A</span>
                        </div>
                        <input type="text" id="max" name="max" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
                    </div>
                </div> &nbsp;
            <?php endif; ?>
            <?php if(!empty($campanas)): ?>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Campaña</span>
                    </div>
                    <select id="campana" class="form-control" name="campana">
                        <?php
                            $campaigns_id = '';
                            foreach ($campanas as $key => $value) {
                                $campaigns_id .= $value->id.',';
                            }
                            $campaigns_id = rtrim($campaigns_id, ',');
                        ?>
                        <option value='<?=$campaigns_id?>' selected>Todas ...</option>
                        <?= options_select_campaign($campanas) ?>
                    </select>
                </div>&nbsp;
            <?php endif; ?>
            <?php if(!empty($agentes)): ?>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Agente</span>
                    </div>
                    <select id="agente" class="form-control" name="agente">
                        <?php $lasops = $agentes_id = '';
                            if (!empty($nojoinags)) { $agentes_id = 'todos'; }
                            foreach ($agentes as $key => $value) {
                                if ($agentes_id !== 'todos') $agentes_id .= $value->id.',';
                                $lasops .= "<option value='$value->id'>$value->name $value->last</option>";
                            } $agentes_id = rtrim($agentes_id, ','); ?>
                        <option value="<?=$agentes_id?>" selected>Todos ...</option>
                        <?=$lasops?>
                    </select>
                </div>&nbsp;
            <?php endif; ?>
            <?php if(!empty($massel)): foreach($massel as $key => $vals): ?>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?php echo $key; ?></span>
                    </div>
                    <select id="<?=slugify($key)?>" class="form-control" name="<?=slugify($key)?>">
                        <?php foreach ($vals as $vkey => $valor): ?>
                            <?php if(is_object($valor)): ?>
                                <option value='<?=$valor->id?>'><?=$valor->name?></option>
                            <?php else: ?>
                                <option value='<?=$vkey?>'><?=$valor?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>&nbsp;
            <?php endforeach; endif; ?>
            <?php if(!empty($aucos)): foreach($aucos as $auco): ?>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$auco->lab?></span>
                        <input type="hidden" name="<?=$auco->nam?>" id="auco_<?=$auco->nam?>val" />
                    </div>
                    <input type="text" class="form-control nosend auco" id="auco_<?=$auco->nam?>"
                        data-mod="<?=$auco->mod?>" data-met="<?=$auco->met?>" data-dep="<?=$auco->dep?>" />
                </div>&nbsp;
            <?php endforeach; endif; ?>
            <?php if(!empty($masinput)): foreach($masinput as $key => $val): ?>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <label for="<?php echo slugify($val); ?>" class="input-group-text"><?=$key?></label>
                    </div>
                    <input type="text" class="form-control nosend" id="<?php echo slugify($val); ?>"
                        name="<?php echo slugify($val); ?>" value="" />
                </div>&nbsp;
            <?php endforeach; endif; ?>
            <?php if(!empty($filtro_estatus)): ?>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Estatus</span>
                    </div>
                    <select id="estatus" class="form-control" name="estatus">
                        <option value="">Todos ...</option>
                        <option value="0">Abierto</option>
                        <option value="1">Cerrado</option>
                    </select>
                </div>&nbsp;
            <?php endif; ?>
            <?php if(!empty($filtro_agendar)): ?>
                <div id="filtro-since" class="input-group d-none">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Agendado</span>
                    </div>
                    <select id="agendado" class="form-control" name="agendado">
                        <option value="">Todos ...</option>
                        <option value="si">Si</option>
                        <option value="no">No</option>
                    </select>
                </div>&nbsp;
            <?php endif; ?>
        </div>
    </form><br />
    <div class="row">
        <div class="col" id="repo"></div>
    </div><br />
    <div class="row">
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
<div class="modal fade" id="escuchaudio" tabindex="-1" role="dialog" aria-labelledby="escuchaudioTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Audio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Fecha<br><span class="badge badge-dark" id="audfecha"></span></div>
                    <div class="col-4">Agente<br><span class="badge badge-dark" id="audagente"></span></div>
                    <div class="col-4" >Número<br><span class="badge badge-dark" id="audnumero"></span></div>
                </div><br />
                <div class="row">
                    <div class="col" id="escuchaudioAudio"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="evalModal" tabindex="-1" role="dialog" aria-labelledby="evalModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="evalModalTitle">Evaluación cualitativa</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="eval_form" action="<?php echo site_url('calidad/guardareval');?>" method="post">
                <input type="hidden" name="id_eval" id="eval_id" value="">
                <input type="hidden" name="redir" id="redir" value="<?php echo uri_string(); ?>">
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-4">Fecha<br><span class="badge badge-dark" id="m_fecha"></span></div>
                            <div class="col-4">Agente<br><span class="badge badge-dark" id="m_agente"></span></div>
                            <div class="col-4">Número<br><span class="badge badge-dark" id="m_numero"></span></div>
                        </div><br />
                        <div class="row">
                            <div class="col">
                                <center><span id="grabacion"></span></center>
                            </div>
                        </div>
                        <table class="table table-sm table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Atributo</th>
                                    <th>Ponderación</th>
                                    <th>Calificación</th>
                                </tr>
                            </thead>
                            <tbody id="calidadbody"></tbody>
                        </table>
                        <h5><span class="badge">Total de evaluación </span><span class="badge badge-dark" id="evaltotal">0</span></h5><br />
                        <h6><span>La ponderación es el valor que fue asignado a cada pregunta.</span></h6>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
