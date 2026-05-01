<div class="container-fluid main">
    <form target="_blank" class="form form-inline" method="post" action="<?php echo site_url("despachador/excel"); ?>" >
    <input type="hidden" name="reporte" value="despachador_detalle"/>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-1">
                    <h1>Despachador&nbsp;detalle</h1>
                </div>
                <div class="col offset-md-10">
                    <button title="Exportar a Excel" class="logos" name="excel" value="excel"><img src="<?php echo site_url('assets/img/excel5.png'); ?>"></button>
                </div>
            </div>
            <hr>
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Despachador</span>
            </div>
            <select id="id_desp" class="form-control" name="id_desp">
                <?php foreach ($despachadores as $key => $desp): ?>
                    <option value="<?php echo $key; ?>"><?php echo $desp; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form><br />
    <div class="row">
        <div class="col" id="despachador"></div>
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
<script src="<?php echo site_url('js/reportes/despachador_detalle.js?v='.time()); ?>" charset="utf-8"></script>
