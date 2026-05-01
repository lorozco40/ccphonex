<div class="container main">
    <div class="row">
        <button type="button" id="btn_wac_nueva" class="btn btn-success">Crear cuenta</button>
    </div>
    <div class="row" id="wac_nueva" style="display:none">
        <div class="col">
            <input type="checkbox" class="d-none" id="wac_active_0" checked>
            <div><label>Nombre de la cuenta</label></div>
            <div><input type="text" class="form-control" id="wac_nombre_0"></div>
            <div><label>No. de contacto</label></div>
            <div><input type="text" class="form-control" id="wac_numero_0"></div>
            <div><label>Instancia chat-api (vacío wabox)</label></div>
            <div><input type="text" class="form-control" id="wac_idchatapi_0"></div>
            <div><label>Token</label></div>
            <div><input type="text" class="form-control" id="wac_token_0"></div>
            <div><label>Campaña</label></div>
            <div>
                <select id="wac_campana_0" class="form-control">
                    <option value="0">-- Elige --</option>
                    <?php foreach ($campanas as $camp): ?>
                        <option value="<?php echo $camp->id; ?>"><?php echo $camp->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label>Almacén</label></div>
            <div>
                <select id="wac_almacen_0" class="form-control">
                    <option value="localhost">Este dominio</option>
                    <option value="10.10.2.101">Gateway</option>
                </select>
            </div>
            <div>
                <button type="button" class="btn btn-success btn_wac_guarda" data-id="0">Crear</button>
                <button type="button" id="btn_wac_cancel" class="btn btn-danger">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="table" id="wac_tabla">
                <div class="table-header-group">
                    <div class="table-cell">Activa</div>
                    <div class="table-cell">Nombre</div>
                    <div class="table-cell">Número</div>
                    <div class="table-cell">Instancia chat-api<br>(vacío wabox)</div>
                    <div class="table-cell">Token</div>
                    <div class="table-cell">Campaña</div>
                    <div class="table-cell">Almacén</div>
                    <div class="table-cell"></div>
                </div>
                <?php foreach ($cuentas as $key => $cuenta): ?>
                    <div class="table-row wac_existnt">
                        <div class="table-cell">
                            <input type="checkbox" class="form-control" id="wac_active_<?php echo $cuenta->id; ?>" <?php if($cuenta->active=='1') echo "checked"; ?>>
                        </div>
                        <div class="table-cell">
                            <input type="text" class="form-control" id="wac_nombre_<?php echo $cuenta->id; ?>" value='<?php echo $cuenta->nombre; ?>'>
                        </div>
                        <div class="table-cell">
                            <input type="text" class="form-control" id="wac_numero_<?php echo $cuenta->id; ?>" value='<?php echo $cuenta->cuenta; ?>'>
                        </div>
                        <div class="table-cell">
                            <input type="text" class="form-control" id="wac_idchatapi_<?php echo $cuenta->id; ?>" value='<?php echo $cuenta->idchatapi; ?>'>
                        </div>
                        <div class="table-cell">
                            <input type="text" class="form-control" id="wac_token_<?php echo $cuenta->id; ?>" value='<?php echo $cuenta->token; ?>'>
                        </div>
                        <div class="table-cell">
                            <select id="wac_campana_<?php echo $cuenta->id; ?>" class="form-control">
                                <option value="0">-- Elige --</option>
                                <?= options_select_campaign($campanas, $cuenta->id_campaign) ?>
                            </select>
                        </div>
                        <div class="table-cell">
                            <select id="wac_almacen_<?=$cuenta->id?>" class="form-control">
                                <option value="localhost"<?=($cuenta->almacen=='localhost') ? ' selected' : ''?>>Este dominio</option>
                                <option value="10.10.2.101"<?=($cuenta->almacen=='10.10.2.101') ? ' selected' : ''?>>Gateway</option>
                            </select>
                        </div>
                        <div class="table-cell">
                            <button type="button" class="btn btn-success btn_wac_guarda" data-id="<?=$cuenta->id?>">Actualizar</button>
                            <a class="btn btn-primary" href="<?=site_url('wabot/ver/'.$cuenta->id)?>">Bots</a>
                            <a class="btn btn-primary" href="<?=site_url('warate/ver/'.$cuenta->id)?>">Encuestas</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
