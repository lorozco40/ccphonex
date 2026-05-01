<div class="container main">
    <div class="row">
        <div class="col-md-1">
            <h1>Despachador&nbsp;indicadores</h1>
        </div>
    </div>
    <hr>
    <form target="_blank" class="form form-inline" action="<?php echo site_url("despachador/excel"); ?>" method="post" >
    <div class="form-group input-daterange">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Despachador</span>
            </div>
            <select class="form-control" name="id_desp" id="id_desp">
                <?php foreach ($despachadores as $key => $desp): ?>
                    <option value="<?php echo $key; ?>"><?php echo $desp; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>&nbsp;
    </form><br />
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 mb-4" id="totales"></div>
            <div class="col-12 col-md-6 mb-4" id="tipi"></div>
            <div class="col-12 col-md-6 mb-4" id="estatus-agendado-totales"></div>
        </div>
    </div>
</div>
