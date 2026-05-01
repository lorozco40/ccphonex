<div class="container-fluid main">
    <form target="_blank" method="post"
        class="form form-inline" action="<?php echo site_url("desconexion/reporte"); ?>">
        <input type="hidden" name="reporte" value="excelin">
        <input type="hidden" id="pag" name="pag" value="0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-1">
                    <h1>Desconexión&nbsp;de&nbsp;llamadas</h1>
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
                    <span class="input-group-text">De</span>
                </div>
                <input type="text" id="min" name="min" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
            </div>
            <div class="input-group">
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
            <select id="campana" class="campanas form-control" name="campana">
                <?php $todas=""; ?>
                <?php foreach ($campanas as $key => $value): ?>
                    <option value='<?php echo $value->id; ?>'><?php echo $value->name; ?></option>";
                    <?php $todas.=$value->id."," ?>
                <?php endforeach; ?>
                <option value='<?=rtrim($todas, ",")?>' selected>Todas...</option>
            </select>
        </div>
    </form><br />
    <div class="row">
        <div class="col" id="desconec"></div>
    </div><br />
    <div class="row d-none">
        <p>Mostrando registros <strong><span style="color: #0fa7ff;" id="inireg"></span></strong> a <strong><span style="color: #0fa7ff;" id="finreg"></span></strong> de <strong><span style="color: #0fa7ff;" id="totreg"></span></strong></p>
    </div>
    <div class="row d-none">
        <div class="text-center d-none">
            <nav aria-label="Page navigation" id="pagination">
            </nav>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo site_url('js/especiales/desconexion.js'); ?>" charset="utf-8"></script>
