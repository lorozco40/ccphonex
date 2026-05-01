<div id="monitoreo_despachador" class="container main">
    <div class="row">
        <form target="_blank" class="form form-inline" action="<?php echo site_url("despachador/excel"); ?>" method="post">
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
        </form>
    </div>
    <hr>
    <div class="row justify-content-center">
        <div id="despStatus" class="card card-danger">
            <div class="card-body">
                <h5 class="card-title">Detenido</h5>
                <p class="text-center"></p>
            </div>
        </div>
        <div id="despLlamando" class="card card-info">
            <div class="card-body">
                <h5 class="card-title">Registros</h5>
                <p class="text-center" id="da-totreg"></p>
            </div>
        </div>
        <!--div id="despLlamando" class="card">
            <div class="card-body">
                <h5 class="card-title">Llamando</h5>
                <p class="text-center" id="cr-lanzadas"></p>
            </div>
        </div-->
        <div id="despEnCola" class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-phone-volume"></i> En cola</h5>
                <p class="text-center" id="co-encola"></p>
            </div>
        </div>
        <div id="despAgentesLoged" class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user"></i> Conect</h5>
                <p class="text-center"></p>
            </div>
        </div>
        <div id="despAgentesOcupados" class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user" style="color:#E92C26"></i> Ocup</h5>
                <p class="text-center"></p>
            </div>
        </div>
        <div id="despAgentesLibres" class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user" style="color:#3266C6"></i> Libres</h5>
                <p class="text-center"></p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="table table-striped">
                <div class="table-row">
                    <div class="table-cell" id="da-fin"></div>
                    <div class="table-cell">Finalizados</div>
                    <div class="table-cell">
                        <div class="progress">
                            <div class="progress-bar"  id="pr-da-fin" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-cell" id="da-nue"></div>
                    <div class="table-cell">Nuevos</div>
                    <div class="table-cell">
                        <div class="progress">
                            <div class="progress-bar" id="pr-da-nue" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-cell" id="da-despl"></div>
                    <div class="table-cell">Desplegados</div>
                    <div class="table-cell">
                        <div class="progress">
                            <div class="progress-bar" id="pr-da-despl" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-cell" id="da-par"></div>
                    <div class="table-cell">Parciales</div>
                    <div class="table-cell">
                        <div class="progress">
                            <div class="progress-bar" id="pr-da-par" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-cell" id="da-open"></div>
                    <div class="table-cell">Abiertos</div>
                    <div class="table-cell">
                        <div class="progress">
                            <div class="progress-bar" id="pr-da-open" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="table table-striped" id="ti-table"></div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="table" id="tablagentes">
                <div class="table-header-group">
                    <div class="table-cell"></div>
                    <div class="table-cell">Agente</div>
                    <div class="table-cell">Extensión</div>
                    <div class="table-cell">Estatus</div>
                    <div class="table-cell">Actividad</div>
                    <div class="table-cell">Tiempo</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="table">
                <div class="table-header-group">
                    <div class="table-cell">Tipo</div>
                    <div class="table-cell">Campaña</div>
                    <div class="table-cell">Multiplicador</div>
                    <div class="table-cell">Condiciones</div>
                </div>
                <div class="table-row">
                    <div class="table-cell" id="di-tipo"></div>
                    <div class="table-cell" id="di-camp"></div>
                    <div class="table-cell" id="di-mult"></div>
                    <div class="table-cell" id="di-cond"></div>
                </div>
            </div>
        </div>
    </div>
</div>
