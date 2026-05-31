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
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="escuchaudioTitle">Grabacion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid px-0">
                    <div class="row report-modal-meta align-items-stretch mb-3">
                        <div class="col-md-4 col-12 mb-3 mb-md-3">
                            <span class="analysis-meta-label">Fecha</span>
                            <span class="analysis-meta-value" id="audfecha"></span>
                        </div>
                        <div class="col-md-4 col-12 mb-3 mb-md-3">
                            <span class="analysis-meta-label">Campaña</span>
                            <span class="analysis-meta-value" id="audcampana"></span>
                        </div>
                        <div class="col-md-4 col-12 mb-3 mb-md-3">
                            <span class="analysis-meta-label">Duración</span>
                            <span class="analysis-meta-value" id="audduracion"></span>
                        </div>
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <span class="analysis-meta-label">Agente</span>
                            <span class="analysis-meta-value" id="audagente"></span>
                        </div>
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <span class="analysis-meta-label">Numero</span>
                            <span class="analysis-meta-value" id="audnumero"></span>
                        </div>
                        <div class="col-md-4 col-12">
                            <span class="analysis-meta-label">Estatus</span>
                            <span class="analysis-meta-value" id="audestatus"></span>
                        </div>
                    </div>
                    <div class="card report-modal-card legacy-modal-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Reproductor</h5>
                            <div id="escuchaudioAudio"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<style>
    #escuchaudio .modal-dialog,
    #analysisModal .modal-dialog {
        max-width: 1100px;
    }

    #escuchaudio .modal-content,
    #evalModal .modal-content,
    #analysisModal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        background: linear-gradient(180deg, #f6f9fc 0%, #edf3f9 100%);
        box-shadow: 0 24px 64px rgba(31, 51, 73, 0.28);
    }

    #escuchaudio .modal-header,
    #evalModal .modal-header,
    #analysisModal .modal-header {
        background: linear-gradient(135deg, #60758e 0%, #7f93a9 100%);
        color: #fff;
        border-bottom: 0;
        padding: 1rem 1.25rem;
    }

    #escuchaudio .modal-title,
    #evalModal .modal-title,
    #analysisModal .modal-title {
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    #escuchaudio .close,
    #evalModal .close,
    #analysisModal .close {
        color: #fff;
        opacity: 0.95;
    }

    #escuchaudio .modal-body,
    #evalModal .modal-body,
    #analysisModal .modal-body {
        padding: 1.25rem;
    }

    .report-modal-meta,
    #analysisModal .analysis-meta {
        background: #dce6f1;
        border-radius: 14px;
        padding: 0.85rem 1rem;
        margin-bottom: 1rem;
    }

    .report-modal-meta .analysis-meta-label,
    #analysisModal .analysis-meta-label {
        display: block;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #4e647d;
        margin-bottom: 0.3rem;
    }

    .report-modal-meta .analysis-meta-value,
    #analysisModal .analysis-meta-value {
        display: inline-block;
        background: #35495e;
        color: #fff;
        border-radius: 999px;
        padding: 0.18rem 0.55rem;
        font-size: 0.86rem;
        font-weight: 600;
        max-width: 100%;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .report-modal-card,
    #analysisModal .analysis-card {
        background: #fff;
        border: 1px solid #dbe4ee;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(63, 86, 110, 0.08);
        height: 100%;
    }

    .report-modal-card .card-body,
    #analysisModal .analysis-card .card-body {
        padding: 1.15rem 1.2rem;
    }

    .report-modal-card .card-title,
    #analysisModal .analysis-card .card-title {
        color: #334a62;
        font-weight: 700;
        margin-bottom: 0.95rem;
    }

    #analysisModal #analysis_audio {
        background: #fff;
        border: 1px solid #dbe4ee;
        border-radius: 14px;
        padding: 0.85rem 1rem;
        box-shadow: 0 10px 30px rgba(63, 86, 110, 0.08);
    }

    #escuchaudio .legacy-modal-card,
    #evalModal .legacy-modal-card {
        margin-bottom: 1rem;
    }

    #escuchaudio #escuchaudioAudio {
        min-height: 84px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #escuchaudio #escuchaudioAudio audio,
    #evalModal #grabacion audio,
    #analysisModal #analysis_audio audio {
        width: 100%;
        max-width: 380px;
    }

    #evalModal .modal-dialog {
        max-width: 1180px;
    }

    #evalModal .eval-table {
        margin-bottom: 0;
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
    }

    #evalModal .eval-table thead th {
        background: #35495e;
        color: #fff;
        border: 0;
        font-weight: 600;
    }

    #evalModal .eval-table td,
    #evalModal .eval-table th {
        vertical-align: middle;
    }

    #evalModal .eval-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.85rem 1rem;
        background: #dce6f1;
        border-radius: 14px;
        margin-top: 1rem;
        color: #334a62;
        font-weight: 600;
    }

    #evalModal .eval-total-value {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 46px;
        height: 46px;
        border-radius: 999px;
        background: #35495e;
        color: #fff;
        font-size: 1rem;
        font-weight: 700;
    }

    #evalModal .eval-note {
        margin-top: 0.95rem;
        color: #5d7188;
        font-size: 0.92rem;
    }

    #analysisModal #analysis_transcription {
        min-height: 280px;
        height: 54vh;
        max-height: 58vh;
        overflow-y: scroll;
        overflow-x: hidden;
        line-height: 1.62;
        color: #24384d;
        padding: 0.9rem 1rem;
        background: #f7fbff;
        border: 1px solid #d5e2ef;
        border-radius: 12px;
        resize: vertical;
        font-size: 0.98rem;
        font-family: "Courier New", monospace;
        scrollbar-gutter: stable both-edges;
    }

    #analysisModal #analysis_transcription::-webkit-scrollbar {
        width: 10px;
    }

    #analysisModal #analysis_transcription::-webkit-scrollbar-thumb {
        background: #afbecd;
        border-radius: 999px;
    }

    #analysisModal #analysis_timing {
        margin-top: -0.35rem;
    }

    #escuchaudio .modal-footer,
    #evalModal .modal-footer,
    #analysisModal .modal-footer {
        background: #eef3f8;
        border-top: 1px solid #d6e0ea;
        padding: 0.9rem 1.25rem 1.15rem;
    }

    #evalModal .modal-footer {
        position: sticky;
        bottom: 0;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        box-shadow: 0 -8px 24px rgba(63, 86, 110, 0.12);
    }

    #evalModal .modal-footer .btn {
        min-width: 168px;
        font-weight: 600;
    }
</style>
<div class="modal fade" id="evalModal" tabindex="-1" role="dialog" aria-labelledby="evalModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
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
                    <div class="container-fluid px-0">
                        <div class="row report-modal-meta align-items-stretch mb-3">
                            <div class="col-md-4 col-12 mb-3 mb-md-3">
                                <span class="analysis-meta-label">Fecha</span>
                                <span class="analysis-meta-value" id="m_fecha"></span>
                            </div>
                            <div class="col-md-4 col-12 mb-3 mb-md-3">
                                <span class="analysis-meta-label">Campaña</span>
                                <span class="analysis-meta-value" id="m_campana"></span>
                            </div>
                            <div class="col-md-4 col-12 mb-3 mb-md-3">
                                <span class="analysis-meta-label">Duración</span>
                                <span class="analysis-meta-value" id="m_duracion"></span>
                            </div>
                            <div class="col-md-4 col-12 mb-3 mb-md-0">
                                <span class="analysis-meta-label">Agente</span>
                                <span class="analysis-meta-value" id="m_agente"></span>
                            </div>
                            <div class="col-md-4 col-12 mb-3 mb-md-0">
                                <span class="analysis-meta-label">Numero</span>
                                <span class="analysis-meta-value" id="m_numero"></span>
                            </div>
                            <div class="col-md-4 col-12">
                                <span class="analysis-meta-label">Estatus</span>
                                <span class="analysis-meta-value" id="m_estatus"></span>
                            </div>
                        </div>
                            <div class="card report-modal-card legacy-modal-card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Grabacion</h5>
                                <span id="grabacion"></span>
                            </div>
                        </div>
                            <div class="card report-modal-card legacy-modal-card">
                            <div class="card-body">
                                <h5 class="card-title">Formulario de calidad</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover eval-table">
                                        <thead>
                                            <tr>
                                                <th>Atributo</th>
                                                <th>Ponderación</th>
                                                <th>Calificación</th>
                                            </tr>
                                        </thead>
                                        <tbody id="calidadbody"></tbody>
                                    </table>
                                </div>
                                <div class="eval-total">
                                    <span>Total de evaluación</span>
                                    <span class="eval-total-value" id="evaltotal">0</span>
                                </div>
                                <div class="eval-note">La ponderación es el valor asignado a cada pregunta del formulario.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar evaluación</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="analysisModal" tabindex="-1" role="dialog" aria-labelledby="analysisModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="analysisModalTitle">Analisis de llamada</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="analysis_call_id" value="">
                <input type="hidden" id="analysis_call_type" value="">
                <div class="container-fluid">
                    <div class="row analysis-meta align-items-stretch">
                        <div class="col-md-3 col-6 mb-3 mb-md-0">
                            <span class="analysis-meta-label">Fecha</span>
                            <span class="analysis-meta-value" id="analysis_fecha"></span>
                        </div>
                        <div class="col-md-3 col-6 mb-3 mb-md-0">
                            <span class="analysis-meta-label">Agente</span>
                            <span class="analysis-meta-value" id="analysis_agente"></span>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="analysis-meta-label">Numero</span>
                            <span class="analysis-meta-value" id="analysis_numero"></span>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="analysis-meta-label">Campaña</span>
                            <span class="analysis-meta-value" id="analysis_campana"></span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <div id="analysis_status" class="alert alert-secondary mb-3">Cargando analisis...</div>
                            <div id="analysis_timing" class="small text-muted mb-3"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col" id="analysis_audio"></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <div class="card analysis-card">
                                <div class="card-body">
                                    <h5 class="card-title">Sentimiento</h5>
                                    <div id="analysis_sentiment_label" class="font-weight-bold"></div>
                                    <div id="analysis_sentiment_score" class="small text-muted"></div>
                                    <div id="analysis_sentiment_summary" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <div class="card analysis-card">
                                <div class="card-body">
                                    <h5 class="card-title">Transcripcion</h5>
                                    <div id="analysis_transcription_meta" class="small text-muted mb-2"></div>
                                    <textarea id="analysis_transcription" class="form-control" readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 d-none" id="analysis_error_wrap">
                        <div class="col text-danger" id="analysis_error"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary d-none" id="analysis_request">Solicitar analisis</button>
                <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info d-none" id="analysis_reprocess_hq">Reprocesar alta calidad</button>
                <button type="button" class="btn btn-warning d-none" id="analysis_reprocess_max">Reprocesar maxima calidad</button>
                <button type="button" class="btn btn-secondary d-none" id="analysis_reprocess">Reprocesar</button>
            </div>
        </div>
    </div>
</div>
