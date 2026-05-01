<div class="container-fluid tmain" id="dashboard">
    <div class="row">
        <div class="col dashside">
            <form method="post">
                <div class="input-group mb-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text camp">Campaña</span>
                    </div>
                    <select id="campanas" class="campanas form-control" name="campanas">
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
                <div class="input-group mb-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <input type="text" id="min" name="min" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
                    <div class="input-group-prepend">
                        <span class="input-group-text">A</span>
                    </div>
                    <input type="text" id="max" name="max" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
                </div>
                <div class="input-group mb-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                    </div>
                    <select name="hora_inicio" id="hora_inicio" class="form-control">
                        <option value="">-- Elije --</option>
                    </select>
                    <div class="input-group-prepend">
                        <span class="input-group-text">A</span>
                    </div>
                    <select name="hora_fin" id="hora_fin" class="form-control">
                        <option value="">-- Elije --</option>
                    </select>
                </div>

            </form>
        </div>
        <div class="col">
            <span><h1>Histórico de llamadas</h1></span>
        </div>
    </div>
    <div class="row">
        <div class="col dashside">
            <div class="pastilla">
                <h5>Inbound</h5>
                <div class="card card-outline-total">
                    <div class="card-block">
                        <img src="<?php echo site_url("assets/img/voip.png"); ?>" alt="" height="25" width="25">
                        <span id="dashindicador"></span>
                        <cite title="Source Title">Total</cite>
                    </div>
                </div>
                <div class="card card-outline-att">
                    <div class="card-block">
                        <img src="<?php echo site_url("assets/img/inbound2.png"); ?>" alt="" height="25" width="25">
                        <span id="dashindicadorate"></span>
                        <cite title="Source Title">Atendidas</cite>
                    </div>
                </div>
                <div class="card card-outline-aba">
                    <div class="card-block">
                        <img src="<?php echo site_url("assets/img/abaa2.png"); ?>" alt="" height="25" width="25">
                        <span id="dashindicadoraba"></span>
                        <cite title="Source Title">Abandono</cite>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="pastilla">
                <div class="row">
                    <div class="col-md-3"><span id="donut-example"></span></div>
                    <div class="col-md-9"><span id="area-example"></span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col dashside">
            <div class="pastilla">
                <h5>Outbound</h5>
                <div class="card card-outline-total">
                    <div class="card-block">
                        <img src="<?php echo site_url("assets/img/Number Pad_40px.png"); ?>" alt="" height="25" width="25">
                        <span id="dashindicadorout"></span>
                        <cite title="Source Title">Total</cite>
                    </div>
                </div>
                <div class="card card-outline-att">
                    <div class="card-block">
                        <img src="<?php echo site_url("assets/img/sal2.png"); ?>" alt="" height="25" width="25">
                        <span id="dashindicadorateout"></span>
                        <cite title="Source Title">Finalizadas</cite>
                    </div>
                </div>
                <div class="card card-outline-aba">
                    <div class="card-block">
                        <img src="<?php echo site_url("assets/img/noexito2.png"); ?>" alt="" height="25" width="25">
                        <span id="dashindicadorabaout"></span>
                        <cite title="Source Title">No Exitosas</cite>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="pastilla">
                <div class="row">
                    <div class="col-md-3"><span id="donut-example-out"></span></div>
                    <div class="col-md-9"><span id="area-example-out"></span></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="//www.gstatic.com/charts/loader.js"></script>
<script src="<?php echo site_url('js/reportes/historico.js?v='.time()); ?>" charset="utf-8"></script>
