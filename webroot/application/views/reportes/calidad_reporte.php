<div class="container-fluid main">
    <form target="_blank" class="form form-inline" action="<?php echo site_url("calidad/excelcalidad"); ?>" method="post">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-1">
                    <h1>Calidad&nbsp;llamadas</h1>
                </div>
                <div class="col offset-md-10">
                    <button title="Exportar a Excel" class="logos" name="excel" value="excel"><img src="<?php echo site_url('assets/img/excel5.png'); ?>"></button>&nbsp;
                    <button title="Exportar a PDF" class="logos" name="pdf" value="pdf" id="pdfbtn"><img src="<?php echo site_url('assets/img/pdf4.png'); ?>"></button>
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
                <span class="input-group-text">Nombre de Evaluación</span>
            </div>
            <select id="tipeval" class="tipeval form-control" name="tipeval" required>
                <?php $sel = "SELECTED"; foreach ($tipeval as $key => $value): ?>
                    <option value='<?php echo $value->id; ?>' <?php echo $sel; ?>><?php echo $value->name; ?></option>";
                <?php $sel = ""; endforeach; ?>
            </select>
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Tipo de Llamada</span>
            </div>
            <select id="tipo" class=" tipo form-control" name="tipo">
                <option value="0">Todas...</option>
                <option value="Entrante">Entrantes</option>
                <option value="Saliente">Salientes</option>
            </select>
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Agente</span>
            </div>
            <select id="agentes" class="form-control" name="agente">
                <?php $todos = ""; ?>
                <?php foreach ($agentes as $key => $value):?>
                    <option value='<?php echo $value->id; ?>'><?php echo $value->name." ".$value->last; ?></option>";
                    <?php $todos.=$value->id.",";?>
                <?php endforeach; ?>
                <option value="<?=rtrim($todos,',')?>" selected>Todos...</option>
            </select>
        </div>
    </form><br />
    <div class="row">
        <div class="col">
                <div class="scroll-x" id="repocalidad"></div>
        </div>
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
    <div class="container">
        <div id="bar_chart"></div>
    </div>
</div>
<script type="text/javascript" src="//www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="<?php echo site_url('js/jspdf.min.js?v='.time()); ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/reportes/calidad.js?v='.time()); ?>" charset="utf-8"></script>
