<div class="container main">
    <form target="_blank" method="post" class="form form-inline" action="<?php echo site_url("reportes/excel"); ?>">
    <input type="hidden" name="reporte" value="excel_abandono">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-1">
                    <h1>NOM035</h1>
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
        </div> &nbsp;
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Campaña</span>
            </div>
            <select id="campanas" class="campanas form-control" name="campanas">
                <option value=''>Todas...</option>
                <?php foreach ($campaigns as $key => $cam): ?>
                    <option value='<?php echo $cam->id; ?>'><?php echo $cam->name; ?></option>"
                <?php endforeach; ?>
            </select>
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Llamadas</span>
            </div>
            <select id="llamadas" class="form-control" name="llamadas">
                <option value='0'>Todas...</option>
                <option value='Abandonada'>Abandonadas</option>
                <option value='Abandonada nosl'>Abandonadas nosl</option>
                <option value='Abandonada Troncal'>Abandonadas Troncal</option>
            </select>
        </div>
    </form><br />
    <div class="row">
        <div class="col" id="abandono"></div>
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

<div class="modal fade" id="escuchaudio" tabindex="-1" role="dialog" aria-labelledby="escuchaudioTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Audio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Fecha<br><span class="badge badge-dark" id="audfecha"></span></div>
                    <div class="col-4">Agente<br><span class="badge badge-dark" id="audagente"></span></div>
                    <div class="col-4" >Número<br><span class="badge badge-dark" id="audnumero"></span></div>
                </div><br />
                <div class="row">
                    <div class="col" id="escuchaudioAudio"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo site_url('js/norma/abandono.js?v='.time()); ?>" charset="utf-8"></script>
