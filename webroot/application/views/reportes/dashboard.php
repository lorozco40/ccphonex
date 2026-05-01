<div class="container-fluid tmain" id="dashboard">
    <div class="row">
        <div class="col dashside">
            <form name="filtrar" method="post">
                <select id="campanas" class="campanas form-control" name="campanas">
                    <?php 
                    $todas = "";
                    $show = true;
                    foreach ($campanas as $key => $value) {
                        $todas.= $value->id.",";
                    } 
                    $todas = rtrim($todas, ","); 
                    ?>
                    <option value='<?=$todas?>' selected>Todas las campañas</option>
                    <?= options_select_campaign($campanas) ?>
                </select>
            </form>
        </div>
        <div class="col">
            <h1>Dashboard Assertive</h1>
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
                <div class="card card-outline-vani">
                    <div class="card-block">
                        <img src="<?php echo site_url("assets/img/queue.png"); ?>" alt="" height="25" width="25">
                        <span id="wait"></span>
                        <cite title="Source Title">Llamadas en cola</cite>
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
        <div class="col-sm">
            <div class="pastilla">
                <div class="row">
                    <div class="col-md-3"><span id="donut-example-out"></span></div>
                    <div class="col-md-9"><span id="area-example-out"></span></div>
                </div>
            </div>
        </div>
    </div>
</div>
