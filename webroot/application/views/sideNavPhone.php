<link href="<?php echo site_url('css/ctxSip.css?v='.time()); ?>" rel="stylesheet" type="text/css" />
<div id="sipClient">
    <div class="clearfix sipStatus">
        <div id="txtCallStatus" class="pull-right">&nbsp;</div>
        <div id="txtRegStatus"></div>
    </div>
    <div id="phoneUI">
        <div class="bloqui"></div>
        <div id="info">
            <div class="input-group">
                <div class="input-group-prepend resetnum">
                    <span class="btn btn-secondary"><i class="fas fa-times"></i></span>
                </div>
                <input type="text" name="number" id="numDisplay" class="form-control text-center input-sm" value="" placeholder="Número ..." autocomplete="off" />
            </div>
        </div>
        <div id="sip-dialpad">
            <button type="button" class="btn btn-light digit" data-digit="1">1<span>&nbsp;</span></button>
            <button type="button" class="btn btn-light digit" data-digit="2">2<span>ABC</span></button>
            <button type="button" class="btn btn-light digit" data-digit="3">3<span>DEF</span></button>
            <button type="button" class="btn btn-light digit" data-digit="4">4<span>GHI</span></button>
            <button type="button" class="btn btn-light digit" data-digit="5">5<span>JKL</span></button>
            <button type="button" class="btn btn-light digit" data-digit="6">6<span>MNO</span></button>
            <button type="button" class="btn btn-light digit" data-digit="7">7<span>PQRS</span></button>
            <button type="button" class="btn btn-light digit" data-digit="8">8<span>TUV</span></button>
            <button type="button" class="btn btn-light digit" data-digit="9">9<span>WXYZ</span></button>
            <button type="button" class="btn btn-light digit" data-digit="*">*<span>&nbsp;</span></button>
            <button type="button" class="btn btn-light digit" data-digit="0">0<span>+</span></button>
            <button type="button" class="btn btn-light digit" data-digit="#">#<span>&nbsp;</span></button>
            <div class="clearfix">&nbsp;</div>
            <button class="btn btn-success btn-block btnCall" title="Send">
                Llamar <i class="fa fa-phone"></i>
            </button>
        </div>
    </div>
    <div class="well-sip">
        <div id="sip-log" class="panel panel-default hide">
            <div class="panel-heading">
                <h5 class="text-muted panel-title">LLamadas recientes <i class="fa fa-trash sipLogClear" title="Clear Log"></i></h5>
                <button class="btn btn-info" id="iniConf">Conferencia</button>
                <?php if(in_array('chanspy', $agente['permisoSec'])): ?>
                    <button class="btn btn-primary" id="iniCS">Chanspy</button>
                <?php endif; ?>
            </div>
            <div id="sip-logitems" class="list-group">
                <p class="text-muted text-center">No hay llamadas recientes.</p>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlError" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Sip Error</h4>
                </div>
                <div class="modal-body text-center text-danger">
                    <h3><i class="fa fa-3x fa-ban"></i></h3>
                    <p class="lead">Falló el registro. No se pueden hacer llamadas.</p>
                </div>
            </div>
        </div>
    </div>
    <audio id="ringtone" src="<?php echo site_url('assets/sounds/incoming.mp3'); ?>" loop></audio>
    <audio id="ringbacktone" src="<?php echo site_url('assets/sounds/outgoing.mp3'); ?>" loop></audio>
    <audio id="dtmfTone" src="<?php echo site_url('assets/sounds/dtmf.mp3'); ?>"></audio>
    <audio id="eventoEnProceso" src="<?php echo site_url('assets/sounds/evento_en_proceso.mp3'); ?>"></audio>
    <audio id="audioRemote"></audio>
</div>
