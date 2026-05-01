<div class="container-fluid main">
    <h1>Monitoreo</h1>
    <div class="row">
        <div class="col-7 col-md-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Campaña</span>
                </div>
                <select id="campana" class="form-control" name="campana">
                    <?php
                        $todas="";
                        foreach ($campanas as $key => $value) {
                            $todas.=$value->id.",";
                        }
                        $todas = rtrim($todas, ",");
                    ?>
                    <option value='<?= $todas ?>' selected>Todas...</option>
                    <?= options_select_campaign($campanas) ?>
                </select>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-7 noexpande">
            <div id="agentes" class="table">
                <div class="table-header-group">
                    <div class="table-cell"></div>
                    <div class="table-cell">Nombre</div>
                    <div class="table-cell">Extensión</div>
                    <div class="table-cell">Estatus</div>
                    <div class="table-cell">Actividad</div>
                    <div class="table-cell">Tiempo</div>
                    <div class="table-cell">Campaña(s)</div>
                </div>
                <?php foreach ($agentes as $agente): ?>
                    <div class="table-row" id="fila<?= $agente->id ?>" style="display:none">
                        <div class="table-cell" id="ico<?php echo $agente->id; ?>" style="text-align:center; color:#7D7D7D">
                            <i class="fas fa-user-alt-slash"></i>
                        </div>
                        <div class="table-cell"><?php echo $agente->name; ?></div>
                        <div class="table-cell"><?php echo $agente->extension; ?></div>
                        <div class="table-cell" id="stat<?php echo $agente->id; ?>"></div>
                        <div class="table-cell" id="act<?php echo $agente->id; ?>"></div>
                        <div class="table-cell" id="time<?php echo $agente->id; ?>"></div>
                        <div class="table-cell" style="padding-right:5px;" id="camp<?php echo $agente->id; ?>"><?php echo $agente->camps; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="row pastilla">
                <div id="grafica" class="chart"></div>
            </div>
            <div class="row pastilla">
                <div class="col">
                    <table class="tbl-exten">
                        <?php if( in_array('monencolayespera', $this->udata['permisoSec'])) : ?>
                        <tr><td nowrap>En cola</td><td nowrap id="encolam">0</td></tr>
                        <?php endif; ?>
                        <tr><td nowrap>% SL (<?php echo $sl->segundos; ?>s)</td><td nowrap id="porsl">0</td></tr>
                        <tr><td nowrap>Llam Rec</td><td nowrap id="llamrm">0</td></tr>
                        <tr><td nowrap>Llam Aba</td><td nowrap id="llamam">0</td></tr>
                    </table>
                </div>
                <div class="col">
                    <table class="tbl-exten">
                        <?php if( in_array('monencolayespera', $this->udata['permisoSec'])) : ?>
                        <tr><td nowrap>Espera más larga</td><td nowrap id="longw">00:00</td></tr>
                        <?php endif; ?>
                        <tr><td nowrap>Prom respuesta</td><td nowrap id="promrm">00:00</td></tr>
                        <tr><td nowrap>Prom llamada</td><td nowrap id="promlm">00:00</td></tr>
                        <tr><td nowrap>Prom abandono</td><td nowrap id="promam">00:00</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php if(isset($predictivo)): ?>
        <h1>Predictivo</h1>
        <hr>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Sin contestar</th>
                    <th>Agentes</th>
                    <th>Corta</th>
                    <th>Larga</th>
                    <th>Promedio</th>
                    <th>Cola</th>
                    <th>Siguiente</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="sincontestar"><?php echo $registros->faltan; ?></td>
                    <td id="agentes"><?php echo count($extensiones); ?></td>
                    <td id="gcorta">0</td>
                    <td id="glarga">0</td>
                    <td id="gpromedio">0</td>
                    <td id="gcola">0</td>
                    <td id="gsiguiente">0</td>
                </tr>
            </tbody>
        </table>
        <br />
        <table class="table table-striped" id="extensiones">
            <thead>
                <tr>
                    <th>Extensión</th>
                    <th>Llamadas</th>
                    <th>Actual</th>
                    <th>Corta</th>
                    <th>Larga</th>
                    <th>Promedio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($extensiones as $extension): ?>
                    <tr id="<?php echo $extension; ?>extension">
                        <td id="<?php echo $extension; ?>ext"><?php echo $extension; ?></td>
                        <td id="<?php echo $extension; ?>lla">0</td>
                        <td id="<?php echo $extension; ?>act">0</td>
                        <td id="<?php echo $extension; ?>cor">0</td>
                        <td id="<?php echo $extension; ?>lar">0</td>
                        <td id="<?php echo $extension; ?>pro">0</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
