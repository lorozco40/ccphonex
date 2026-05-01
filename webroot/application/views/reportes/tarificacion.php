<div class="container main">
    <form target="_blank" method="post" class="form form-inline" action="<?php echo site_url("tarificacion/excel"); ?>">
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <h1 id="title">Tarificación</h1>
                </div>
                <div class="col-auto">
                    <button title="Exportar a Excel" class="logos" name="excel" value="excel"><img src="<?php echo site_url('assets/img/excel5.png'); ?>"></button>&nbsp;
                    <button title="Exportar a PDF" class="logos" name="pdf" value="pdf" id="pdfbtn"><img src="<?php echo site_url('assets/img/pdf4.png'); ?>"></button>
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
            <select id="campanas" class="campanas form-control" name="campanas">
                <option value=''>Todas las campañas</option>
                <?php foreach ($campanas as $key => $value): ?>
                    <option value='<?php echo $value->id; ?>'><?php echo $value->name; ?></option>";
                <?php endforeach; ?>
            </select>
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Agente</span>
            </div>
            <select id="agentes" class="form-control" name="agente">
                <option value="0">Todos</option>
                <?php foreach ($agentes as $key => $value): ?>
                    <option value='<?php echo $value->id; ?>'><?php echo $value->name; ?></option>";
                <?php endforeach; ?>
            </select>
        </div>
        <div class="container"><br />
            <div class="row" id="datostodos">
                <div class="col">
                    <h5>
                        <span class="badge">Local</span>
                        <div class="badge badge-light">'<span id="minlocal"></span></div>
                        <div class="badge badge-light">$ <span id="local"></span></div>
                    </h5>
                </div>
                <div class="col">
                    <h5>
                        <span class="badge">Celular</span>
                        <div class="badge badge-light">'<span id="mincelular"></span></div>
                        <div class="badge badge-light">$ <span id="celular"></span></div>
                        </h5>
                </div>
                <div class="col">
                    <h5>
                        <span class="badge">Entrante</span>
                        <div class="badge badge-light">'<span id="mintrante"></span></div>
                        <div class="badge badge-light">$ <span id="entrante"></span></div>
                    </h5>
                </div>
                <div class="col">
                    <h5>
                        <span class="badge">No Aplica</span>
                        <div class="badge badge-light">'<span id="minna"></span></div>
                        <div class="badge badge-light">$ <span id="na">0.00</span></div>
                    </h5>
                </div>
                <div class="col">
                    <h5>
                        <span class="badge">Total</span>
                        <div class="badge badge-light">'<span id="mintotal"></span></div>
                        <div class="badge badge-light">$ <span id="total"></span></div>
                    </h5>
                </div>
            </div>
        </div>
    </form>
    <table id="out" class="table table-striped table-responsive" woutboundidth="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Número</th>
                <th>Extensión</th>
                <th>Agente</th>
                <th>Campaña</th>
                <th>CallerId</th>
                <th>Duración</th>
                <th>Estatus</th>
                <th>Tipo</th>
                <th>Tarifa</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th>Fecha</th>
                <th>Número</th>
                <th>Extensión</th>
                <th>Agente</th>
                <th>Campaña</th>
                <th>CallerId</th>
                <th>Duración</th>
                <th>Estatus</th>
                <th>Tipo</th>
                <th>Tarifa</th>
            </tr>
        </tfoot>
    </table>
    <div class="row d-none">
        <p>Mostrando registros <strong><span style="color: #0fa7ff;" id="inireg"></span></strong> a <strong><span style="color: #0fa7ff;" id="finreg"></span></strong> de <strong><span style="color: #0fa7ff;" id="totreg"></span></strong></p>
    </div>
    <div class="row d-none">
        <div class="text-center d-none">
            <nav aria-label="Page navigation" id="pagination">
            </nav>
        </div>
    </div><br />
    <div class="col-sm">
        <div class="row">
            <div class="col-sm-6" id="piechart_3d"></div>
            <div class="col-sm-6" id="chart_div"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="//www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="<?php echo site_url('js/jspdf.min.js?v='.time()); ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/reportes/tarificacion.js?v='.time()); ?>" charset="utf-8"></script>
