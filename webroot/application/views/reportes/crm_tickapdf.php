<script type="text/javascript">
    var forms = JSON.parse('<?php echo json_encode($forms, true); ?>');
    var plant = JSON.parse('<?php echo json_encode($plant, true); ?>');
</script>
<form target="_blank" method="post" class="form form-inline" action="<?php echo site_url("crm/tickapdf"); ?>">
    <div class="container main" id="sla_graph">
        <div class="row">
            <div class="col-auto">
                <h1>Tickets a PDF</h1>
            </div>
            <div class="col text-right">
                <button class="logos"><img src="<?php echo site_url('assets/img/pdf4.png'); ?>"></button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Campaña</span>
                </div>
                <select id="campanas" class="campanas form-control" name="campana">
                    <?= options_select_campaign($campanas) ?>
                </select>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">CRM</span>
                </div>
                <select id="forms" class="form-control" name="crm">
                    <?php foreach ($forms as $form): ?>
                        <?php $cam = reset($campanas); if($cam->id == $form->id_campaign): ?>
                            <option value='<?php echo $form->id; ?>'><?php echo $form->name; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Plantilla</span>
                </div>
                <select id="plant" class="form-control" name="plantilla">
                    <?php foreach ($plant as $plt): ?>
                        <?php $cam = reset($campanas); if($cam->id == $plt->id_campaign): ?>
                            <option value='<?php echo $plt->file; ?>'><?php echo $plt->name; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="form-group input-daterange">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Año</span>
                    </div>
                    <input type="number" min="2021" max="2099" name="ano" class="form-control" placeholder="Año"  value="<?=date('Y')?>" required
                    oninvalid="this.setCustomValidity('Elige un año de 4 dígitos (2021 - 2099).')" oninput="setCustomValidity('')" />
                </div>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Mes</span>
                </div>
                <?php $mes = date('m'); ?>
                <select name="mes" class="form-control" required>
                    <option value="01" <?php echo ($mes=='01') ? "selected" : ""; ?>>Enero</option>
                    <option value="02" <?php echo ($mes=='02') ? "selected" : ""; ?>>Febrero</option>
                    <option value="03" <?php echo ($mes=='03') ? "selected" : ""; ?>>Marzo</option>
                    <option value="04" <?php echo ($mes=='04') ? "selected" : ""; ?>>Abril</option>
                    <option value="05" <?php echo ($mes=='05') ? "selected" : ""; ?>>Mayo</option>
                    <option value="06" <?php echo ($mes=='06') ? "selected" : ""; ?>>Junio</option>
                    <option value="07" <?php echo ($mes=='07') ? "selected" : ""; ?>>Julio</option>
                    <option value="08" <?php echo ($mes=='08') ? "selected" : ""; ?>>Agosto</option>
                    <option value="09" <?php echo ($mes=='09') ? "selected" : ""; ?>>Septiembre</option>
                    <option value="10" <?php echo ($mes=='10') ? "selected" : ""; ?>>Octubre</option>
                    <option value="11" <?php echo ($mes=='11') ? "selected" : ""; ?>>Noviembre</option>
                    <option value="12" <?php echo ($mes=='12') ? "selected" : ""; ?>>Diciembre</option>
                </select>
            </div>
        </div>
    </div>
</form>
