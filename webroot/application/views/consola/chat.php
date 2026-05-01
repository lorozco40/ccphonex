<div class="row mt-3">
    <div class="col-3">Sesiones:</div>
    <div class="col-6">Conversación activa:</div>
</div>
<div class="row mb-3" id="achat-container">
    <div class="col-3" id="achat-sesiones"></div>
    <div class="col-6" id="achat-msgs"></div>
</div>
<div class="row" id="achat-form">
    <div class="col-6">
        <textarea id="achat-input" class="form-control" disabled></textarea>
        <button id="achat-send-btn" class="btn btn-primary" disabled>
            <img src="<?=site_url('assets/img/enviar.png')?>" alt="Enviar">
        </button>
    </div>
    <div class="col-3">
        <div class="input-group">
            <select name="ir" class="form-control" id="achat-ir" disabled>
                <option value="0">Agradecer y terminar</option>
                <option value="1" disabled>Enviar a encuesta</option>
                <optgroup label="Transferir a ..." id="achat-transf-group">
                    <option value="3">Agente de respaldo</option>
                    <option value="4">Supervisor</option>
                </optgroup>
            </select>
            <button id="achat-ir-btn" class="btn btn-info" disabled>Ir</button>
        </div>
    </div>
</div>