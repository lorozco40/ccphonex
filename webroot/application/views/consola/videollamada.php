<div id="vcbtns" class="mb-3">
    <button class="btn btn-secondary" id="vdispo">Hacerme Disponible</button>
    <button class="btn btn-success d-none" id="vnodispo">Hacerme No Disponible</button>
    <button class="btn btn-primary d-none" id="vcontestar">Contestar</button>
    <button class="btn btn-secondary ml-3 d-none" id="vrechazar">Rechazar</button>
    <button class="btn btn-secondary d-none" id="vtransferir">Transferir</button>
</div>
<div id="vgetdatatop"></div>
<div id="elvidchat" class="mb-3"></div>
<div id="vgetdatabot"></div>
<?php if (empty($agente['exten'])): ?>
    <audio id="ringtone" src="<?php echo site_url('assets/sounds/incoming.mp3'); ?>" loop></audio>
<?php endif; ?>
