<div class="container-fluid main">
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
                        <?php $campaigns_id = $lasops = ''; foreach ($campanas as $key => $value){
                            $campaigns_id .= $value->id.',';
                            $lasops .= "<option value='$value->id'>$value->name</option>";
                        } $campaigns_id = rtrim($campaigns_id, ','); ?>
                        <option value='<?=$campaigns_id?>' selected>Todas ...</option>
                        <?=$lasops?>
                    </select>
                </div>&nbsp;
            <?php endif; ?>
            <?php if(!empty($agentes)): ?>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Agente</span>
                    </div>
                    <select id="agente" class="form-control" name="agente">
                        <?php $lasops = $agentes_id = ''; foreach ($agentes as $key => $value) { $agentes_id .= $value->id.',';
                            $lasops .= "<option value='$value->id'>$value->name $value->last</option>";
                        } $agentes_id = rtrim($agentes_id, ','); ?>
                        <option value="<?=$agentes_id . ",9999"?>" selected>Todos ...</option>
                        <?=$lasops?>
                        <option value="9999">Whatsapp bot</option>
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
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="evalModalTitle">Evaluación cualitativa de la sesón <span id="wasesid"></span></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="eval_form" action="<?php echo site_url('calidad/wa_save_ecs');?>" method="post">
                <input type="hidden" name="id_eval" id="eval_id" value="">
                <input type="hidden" name="redir" id="redir" value="<?php echo uri_string(); ?>">
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col-4">Fecha<br><span class="badge badge-dark" id="fecha"></span></div>
                                    <div class="col-4">Agente<br><span class="badge badge-dark" id="agente"></span></div>
                                    <div class="col-4">Número<br><span class="badge badge-dark" id="numero"></span></div>
                                </div><br />
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
                            <div class="col" id="whatsapptab">
                                <div class="row justify-content-between" style="margin-bottom:8px;">
                                    <div class="col-8"><i class="fas fa-user-circle"></i> <strong id="wacontactname">Nombre de usuario</strong></div>
                                    <div class="col-1"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 39 39"><path fill="#00E676" d="M10.7 32.8l.6.3c2.5 1.5 5.3 2.2 8.1 2.2 8.8 0 16-7.2 16-16 0-4.2-1.7-8.3-4.7-11.3s-7-4.7-11.3-4.7c-8.8 0-16 7.2-15.9 16.1 0 3 .9 5.9 2.4 8.4l.4.6-1.6 5.9 6-1.5z"></path><path fill="#FFF" d="M32.4 6.4C29 2.9 24.3 1 19.5 1 9.3 1 1.1 9.3 1.2 19.4c0 3.2.9 6.3 2.4 9.1L1 38l9.7-2.5c2.7 1.5 5.7 2.2 8.7 2.2 10.1 0 18.3-8.3 18.3-18.4 0-4.9-1.9-9.5-5.3-12.9zM19.5 34.6c-2.7 0-5.4-.7-7.7-2.1l-.6-.3-5.8 1.5L6.9 28l-.4-.6c-4.4-7.1-2.3-16.5 4.9-20.9s16.5-2.3 20.9 4.9 2.3 16.5-4.9 20.9c-2.3 1.5-5.1 2.3-7.9 2.3zm8.8-11.1l-1.1-.5s-1.6-.7-2.6-1.2c-.1 0-.2-.1-.3-.1-.3 0-.5.1-.7.2 0 0-.1.1-1.5 1.7-.1.2-.3.3-.5.3h-.1c-.1 0-.3-.1-.4-.2l-.5-.2c-1.1-.5-2.1-1.1-2.9-1.9-.2-.2-.5-.4-.7-.6-.7-.7-1.4-1.5-1.9-2.4l-.1-.2c-.1-.1-.1-.2-.2-.4 0-.2 0-.4.1-.5 0 0 .4-.5.7-.8.2-.2.3-.5.5-.7.2-.3.3-.7.2-1-.1-.5-1.3-3.2-1.6-3.8-.2-.3-.4-.4-.7-.5h-1.1c-.2 0-.4.1-.6.1l-.1.1c-.2.1-.4.3-.6.4-.2.2-.3.4-.5.6-.7.9-1.1 2-1.1 3.1 0 .8.2 1.6.5 2.3l.1.3c.9 1.9 2.1 3.6 3.7 5.1l.4.4c.3.3.6.5.8.8 2.1 1.8 4.5 3.1 7.2 3.8.3.1.7.1 1 .2h1c.5 0 1.1-.2 1.5-.4.3-.2.5-.2.7-.4l.2-.2c.2-.2.4-.3.6-.5s.4-.4.5-.6c.2-.4.3-.9.4-1.4v-.7s-.1-.1-.3-.2z"></path></svg></div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <!--a href="#" class="wa-cargar-mas" onclick="cw.load_more()"><span>Cargar anteriores</span></a-->
                                        <div class="pastilla wamsgs" id="wamsgs"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
