<div class="container-fluid main">
    <form target="_blank" method="post" class="form" id="filtroform" action="<?php echo site_url("reportes/excel"); ?>">
        <input type="hidden" name="reporte" value="outbound">
        <input type="hidden" id="pag" name="pag" value="0">
        <div class="row justify-content-between">
            <div class="col-auto">
                <h1>OUTBOUND</h1>
            </div>
            <div class="col-1">
                <button title="Exportar a Excel" class="logos" name="excel" value="excel"><img src="<?php echo site_url('assets/img/excel5.png'); ?>"></button>
            </div>
        </div>
        <hr>
        <div class="row form-inline">
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
                    <?php $todas=""; ?>
                    <?php foreach ($campanas as $key => $value): ?>
                        <option value='<?php echo $value->id; ?>'><?php echo $value->name; ?></option>";
                        <?php $todas.=$value->id."," ?>
                    <?php endforeach; ?>
                    <option value='<?=rtrim($todas, ",")?>' selected>Todas...</option>
                </select>
            </div>
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
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Calidad</span>
                </div>
                <select id="evaluacion" class="form-control" name="evaluacion">
                    <option value='0'>Todas...</option>
                    <option value='Evaluadas'>Evaluadas</option>
                    <option value='Noevaluadas'>No evaluadas</option>
                </select>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Llamadas</span>
                </div>
                <select id="llamadas" class="form-control" name="llamadas">
                    <option value='0'>Todas...</option>
                    <option value='Terminada'>Terminadas</option>
                    <option value='Abandonada'>Abandonadas</option>
                </select>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">CallerID o Número</span>
                </div>
                <input type="text" class="form-control" id="buscar" name="texto">
            </div>
        </div>
    </form>
    <br />
    <div class="row">
        <div class="col" id="out"></div>
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
<!-- /////////////////////////// Modal preguntas calidad ///////////////////////////////////////////////////////////// -->
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLongTitle">Evaluación cualitativa</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-4">Fecha<br><span class="badge badge-dark" id="fecha"></span></div>
                    <div class="col-4">Agente<br><span class="badge badge-dark" id="agente"></span></div>
                    <div class="col-4">Número<br><span class="badge badge-dark" id="numero"></span></div>
                </div><br />
                <div class="row">
                    <div class="col">
                        <center><span id="grabacion"></span></center>
                    </div>
                </div>
            </div>
            <form id="eval_form" action="<?php echo site_url('calidad/guardareval');?>" method="post">
                <input type="hidden" name="id_eval" id="eval_id">
                <input type="hidden" name="redir" id="redir" value="<?php echo uri_string(); ?>">
                <div class="modal-body">
                    <table class="table table-sm table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Atributo</th>
                                <th>Ponderación</th>
                                <th>Calificación</th>
                            </tr>
                        </thead>
                        <tbody id="calidadbody"></tbody>
                    </table>
                    <h5><span class="badge">Total de evaluación </span><span class="badge badge-dark" id="evaltotal">0</span></h5><br 7>
                    <h6><span>La ponderación es el valor que fue asignado a cada pregunta.</span></h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="escuchaudio" tabindex="-1" role="dialog" aria-labelledby="escuchaudioTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="escuchaudioTitle">Audio</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-4">Fecha<br><span class="badge badge-dark" id="audfecha"></span></div>
                    <div class="col-4">Agente<br><span class="badge badge-dark" id="audagente"></span></div>
                    <div class="col-4" >Número<br><span class="badge badge-dark" id="audnumero"></span></div>
                </div><br />
                <div class="row">
                    <div class="modal-body" id="escuchaudioAudio"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo site_url('js/reportes/outbound.js?v='.time()); ?>" charset="utf-8"></script>
