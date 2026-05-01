<div class="container main">
    <form target="_blank" method="post" class="form form-inline">
        <div class="container">
            <div class="row">
                <div class="col-md-1">
                    <h1>Distribución&nbsp;llamadas&nbsp;por&nbsp;agente</h1>
                </div>
                <div class="col offset-md-10">
                    <button title="Exportar a PDF" class="logos" name="pdf" value="pdf" id="pdfchart"><img src="<?php echo site_url('assets/img/pdf5.png'); ?>"></button>
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
                <?php $todos = ""; ?>
                <?php foreach ($agentes as $key => $value): ?>
                    <option value='<?php echo $value->id; ?>'><?php echo $value->name." ".$value->last; ?></option>";
                    <?php $todos.=$value->id.",";?>
                <?php endforeach; ?>
                <option value="<?=rtrim($todos,',')?>" selected>Todos ...</option>
            </select>
        </div>
    </form><br />
    <div class="pastilla pastillablanca">
        <div class="container">
            <div id="chart_div"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="<?php echo site_url('js/jspdf.min.js?v='.time()); ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/reportes/poragentegra.js?v='.time()); ?>" charset="utf-8"></script>
