<div class="container-fluid main" id="wa_graph">
    <input type="hidden" id="reporte" name="reporte" value="<?php echo (isset($cual)) ? slugify($cual) : slugify($title); ?>">
    <input type="hidden" id="pag" name="pag" value="0">
    <input type="hidden" id="modelo" name="modelo" value="<?php echo (isset($modelo)) ? $modelo : 'reportes'; ?>">
    <div class="row justify-content-between">
        <div class="col-auto">
            <h1>Encuesta whatsapp indicadores</h1>
        </div>
        <div class="col offset-md-10">
            <button class="logos" name="pdf" value="pdf" id="pdfbtn"><img src="<?php echo site_url('assets/img/pdf4.png'); ?>"></button>
        </div>
    </div>
    <hr>
    <div class="row form-inline">
        <div class="form-group input-daterange">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                </div>
                <input type="text" id="min" name="min" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker" onchange="cargar()">
                <div class="input-group-prepend">
                    <span class="input-group-text">A</span>
                </div>
                <input type="text" id="max" name="max" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker" onchange="cargar()">
            </div>
        </div> &nbsp;
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Cuenta</span>
            </div>
            <select id="cuenta" class="form-control" name="cuenta">
                <?php foreach($cuentas as $cuenta): ?>
                <option value="<?= $cuenta->id ?>"><?= $cuenta->name ?></option>
                <?php endforeach; ?>
            </select>
        </div> &nbsp;
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Encuesta</span>
            </div>
            <select id="encuesta" class="form-control" name="encuesta" onchange="cargar()">
            </select>
        </div> &nbsp;
    </div>
    <br />
    <div class="row">
        <div class="col" id="reporte_ind"></div>
    </div>
    <br />
</div>