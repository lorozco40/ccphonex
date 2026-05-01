<div class="container main" id="sla">
    <form id="slaform" target="_blank" method="post" class="form form-inline" action="<?php echo site_url("sl/excel"); ?>">
    <input type="hidden" name="reporte" value="slou"/>
        <div class="container">
            <div class="row">
                <div class="col-md-1">
                    <h1>Nivel&nbsp;de&nbsp;servicio&nbsp;Outbound</h1>
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
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Horario</span>
            </div>
            <select id="tipo" class="form-control" name="tipo">
                <option value='1'>En servicio</option>
                <option value='2'>Fuera de horario</option>
                <option value='0' selected>Todos...</option>
            </select>
        </div>
    </form><br />
    <div class="row">
        <div class="col" id="slout"></div>
    </div>
    <div class="row">
        <div class="col" id="leyend"></div>
    </div>
</div>
<script type="text/javascript" src="<?php echo site_url('js/reportes/slout.js?v='.time()); ?>" charset="utf-8"></script>
