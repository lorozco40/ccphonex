<div class="container main">
    <form target="_blank" name="fecha" method="post" class="form form-inline">
        <div class="container">
            <div class="row">
                <div class="col-md-1">
                    <h1>Llamadas&nbsp;por&nbsp;campaña</h1>
                </div>
                <div class="col offset-md-10">
                    <button  title="Exportar a PDF" class="logos" name="pdf" value="pdf" id="pdfchart"><img src="<?php echo site_url('assets/img/pdf4.png'); ?>"></button>
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
        </div>
        <?php if(!empty($campaigns)): ?>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Campaña</span>
                </div>
                <select id="campana" class="form-control" name="campana">
                    <?php
                        $campaigns_id = '';
                        foreach ($campaigns as $key => $value) {
                            $campaigns_id .= $value->id.',';
                        }
                        $campaigns_id = rtrim($campaigns_id, ',');
                    ?>
                    <option value='<?=$campaigns_id?>' selected>Todas las campañas</option>
                    <?= options_select_campaign($campaigns) ?>
                </select>
            </div>&nbsp;
        <?php endif; ?>
    </form><br />
    <div class="pastilla pastillablanca">
        <div class="container">
            <div id="chart_div"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="<?php echo site_url('js/jspdf.min.js?v='.time()); ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/reportes/porcolasgra.js?v='.time()); ?>" charset="utf-8"></script>
