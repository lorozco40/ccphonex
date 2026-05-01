<div class="container main">
    <form target="_blank" method="post" class="form form-inline" action="<?php echo site_url("reportes/excel"); ?>">
    <input type="hidden" name="reporte" value="excelporacw">
        <div class="container">
            <div class="row">
                <div class="col-md-1">
                    <h1>After&nbsp;Call&nbsp;Work</h1><span>(Actividad&nbsp;después&nbsp;de&nbsp;llamada)</span>
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
                <span class="input-group-text">Agente</span>
            </div>
            <select id="agentes" class="form-control" name="agente">
                <option value="0">Todos...</option>
                <?php foreach ($agentes as $key => $value): ?>
                    <option value='<?php echo $value->id; ?>'><?php echo $value->name; ?></option>";
                <?php endforeach; ?>
            </select>
        </div>
    </form><br />
    <div class="row">
        <div class="col" id="poracw"></div>
    </div>
</div>
<script type="text/javascript" src="<?php echo site_url('js/reportes/poracw.js?v='.time()); ?>" charset="utf-8"></script>
