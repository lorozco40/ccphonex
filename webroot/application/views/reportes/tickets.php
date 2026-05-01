<style media="screen">
    div.table-cell {
        white-space: normal !important;
    }
    .ordenador {
        cursor: pointer;
        text-decoration: underline;
    }
    .ordasc:before {
        content: "\25BC ";
        color: #5fa5ff;
    }
    .orddesc:before {
        content: "\25B2 ";
        color: #5fa5ff;
    }
</style>
<div class="container-fluid main">
    <form id="repoform" target="_blank" class="form form-inline" action="<?php echo site_url("reportes/excel_aicm"); ?>" method="post" >
        <input type="hidden" id="reporte" name="reporte" value="<?php echo (isset($cual)) ? slugify($cual) : slugify($title); ?>">
        <input type="hidden" id="pag" name="pag" value="0">
        <input type="hidden" id="orden" name="orden" value="">
        <input type="hidden" name="modelo" value="<?php echo (isset($modelo)) ? $modelo : ''; ?>">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-auto">
                    <h1><?php echo $title; ?></h1>
                </div>
                <div class="col-1">
                    <button title="Exportar a Excel" class="logos" name="excel" value="excel"><img src="<?php echo site_url('assets/img/excel5.png'); ?>"></button>
                </div>
            </div>
            <hr>
        </div>
        <div class="container-fluid">
          <div class="row">
              <div class="col-auto">
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
                </div>
              </div>
                <?php if(!empty($agentes)): ?>
                  <div class="col-auto">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Agente</span>
                      </div>
                      <select id="agente" class="form-control" name="agente">
                        <?php $todos = ""; ?>
                        <?php foreach ($agentes as $key => $value):?>
                          <option value='<?php echo $value->id; ?>'><?php echo $value->name." ".$value->last; ?></option>
                          <?php $todos.=$value->id.",";?>
                        <?php endforeach; ?>
                        <option value="<?=rtrim($todos,',')?>" selected>Todos ...</option>
                      </select>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if(!empty($campanas)): ?>
                  <div class="col-auto">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Campaña</span>
                      </div>
                      <select id="campana" class="form-control" name="campana">
                        <?php $campaigns_id = ''; foreach ($campanas as $key => $value): $campaigns_id .= $value->id.','; ?>
                          <option value='<?php echo $value->id; ?>'><?php echo $value->name; ?></option>"
                        <?php endforeach; $campaigns_id = rtrim($campaigns_id, ','); ?>
                        <option value='<?=$campaigns_id?>' selected>Todas ...</option>
                      </select>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if(!empty($massel)): foreach($massel as $key => $vals): ?>
                  <div class="col-auto">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><?php echo $key; ?></span>
                      </div>
                      <select id="<?php echo slugify($key); ?>" class="form-control" name="<?php echo slugify($key); ?>">
                        <?php if($key == 'Formulario'): ?>
                          <option value='0'>Ninguno ...</option>
                          <?php endif ?>
                        <?php foreach ($vals as $valor): ?>
                          <option value='<?php echo $valor->id; ?>'><?php echo $valor->name; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                <?php endforeach; endif; ?>
                <?php if(!empty($tipo)): ?>
                  <div class="col-auto">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Tipo</span>
                      </div>
                      <select id="tipo" class="form-control" name="tipo">
                        <option value='0' selected>Todas ...</option>
                        <option value='Entrante'>Entrantes</option>
                        <option value='Saliente'>Salientes</option>
                      </select>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="col"></div>
                <div class="col-auto align-self-end">
                  <div class="input-group">
                      <input type="text" class="form-control" name="tid" id="tid" />
                      <div class="input-group-append">
                          <button title="Buscar usuario" id="btnbus" class="btn btn-info">Buscar</button>
                      </div>
                  </div>
                </div>
              </div>
          </div>
    </form><br />
    <div class="row">
        <div class="col overflow-auto" id="repo"></div>
    </div><br />
    <div class="row d-none">
        <p>Mostrando registros <strong><span style="color: #0fa7ff;" id="inireg"></span></strong> a <strong><span style="color: #0fa7ff;" id="finreg"></span></strong> de <strong><span style="color: #0fa7ff;" id="totreg"></span></strong></p>
    </div>
    <div class="row d-none">
        <div class="text-center d-none">
            <nav aria-label="Page navigation" id="pagination"></nav>
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
