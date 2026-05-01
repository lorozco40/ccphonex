<div class="container main">
    <form target="_blank" method="post" class="form form-inline" action="<?php echo site_url("videollamada/excel"); ?>">
        <input type="hidden" name="reporte" value="video_indicador"/>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-1">
                        <h1>Reporte&nbsp;Videollamada&nbsp;indicador</h1>
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
        </div>
    </form><br />
    <div class="row">
        <div class="col" id="video_indicador"></div>
    </div><br />
</div>
<script src="<?php echo site_url('js/reportes/videollamada_indicador.js?v='.time()); ?>" charset="utf-8"></script>
