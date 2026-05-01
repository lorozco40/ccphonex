<div class="container main">
    <form target="_blank" method="post" id="reporag" class="form form-inline" action="<?php echo site_url("reportes/excel"); ?>">
        <input type="hidden" name="reporte" value="poragente" />
        <input type="hidden" name="pag" id="pag" value="0" />
        <div class="container">
            <div class="row">
                <div class="col-md-1">
                    <h1>Llamadas&nbsp;por&nbsp;agente</h1>
                </div>
                <div class="col offset-md-10">
                    <button title="Exportar a Excel" class="logos" name="excel" value="excel"><img src="<?php echo site_url('assets/img/excel5.png'); ?>"></button>
                </div>
            </div>
            <hr>
        </div>
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
        </div>&nbsp;
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Tipo Llamada</span>
            </div>
            <select id="tipo" class="form-control" name="tipo">
                <option value='0'>Todas...</option>
                <option value='Entrante'>Entrantes</option>
                <option value='Saliente'>Salientes</option>
            </select>
        </div>
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
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Agente</span>
            </div>
            <select id="agente" class="form-control" name="agente">
                <?php $todos = ""; ?>
                <?php foreach ($agentes as $key => $value): ?>
                    <option value='<?php echo $value->id; ?>'><?php echo $value->name." ".$value->last; ?></option>";
                    <?php $todos.=$value->id.",";?>
                <?php endforeach; ?>
                <option value="<?=rtrim($todos,',')?>" selected>Todos ...</option>
            </select>
        </div>
    </form><br />
    <div class="row">
        <div class="col" id="repo"></div>
    </div>
    <div class="row d-none">
        <p>Mostrando registros <strong><span style="color: #0fa7ff;" id="inireg"></span></strong> a <strong><span style="color: #0fa7ff;" id="finreg"></span></strong> de <strong><span style="color: #0fa7ff;" id="totreg"></span></strong></p>
    </div>
    <div class="row d-none">
        <div class="text-center d-none">
            <nav aria-label="Page navigation" id="pagination"></nav>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo site_url('js/reportes/poragente.js?v='.time()); ?>" charset="utf-8"></script>
