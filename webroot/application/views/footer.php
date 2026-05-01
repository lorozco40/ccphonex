<div id="toastzone" aria-live="polite" aria-atomic="true">
    <div id="toastplantilla" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
      <div class="toast-header">
        <span class="tipotoast rounded mr-2">OO</span>
        <strong class="mr-auto">Assertive</strong>
        <small class="text-muted"></small>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="toast-body"></div>
    </div>
</div>
<div class="container-fluid bg-dark mt-4" id="piedepag">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-auto">
                <a target="_blank" href="https://assertivebusiness.com">Assertive Business México</a>
                &copy; <?=date('Y').' - '.date('Y', strtotime('+3 years'))?>
            </div>
            <div class="col-auto">
                <a target="_blank" href="<?=site_url('aviso-de-privacidad')?>">Aviso de privacidad</a> |
                <a target="_blank" href="<?=site_url('terminos-y-condiciones')?>">Terminos y condiciones</a>
            </div>
        </div>
        <div class="row">
            <div class="col">
            Presa Salinillas 370-Piso 7, Col. Irrigación, Miguel Hidalgo,
            11200 Ciudad de México, CDMX, México. Tel +52 55 8942 7351
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="multiModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" id="multiModalContent"></div>
    </div>
</div>
<div class="modal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" id="spinnerModal">
    <div class="modal-dialog modal-dialog-centered text-center" role="document">
        <span class="fa fa-spinner fa-spin fa-3x w-100"></span>
    </div>
</div>
<script type="text/javascript" src="<?php echo site_url('js/jquery-ui.min.js'); ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/touchpunch.js'); ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/popper.min.js'); ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/bootstrap.min.js'); ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/bootstrap-confirmation.min.js'); ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/moment.min.js'); ?>" charset="utf-8"></script>
<?php if (isset($extjs)): ?>
    <?php if(is_array($extjs)): ?>
        <?php foreach ($extjs as $uno): ?>
            <script type="text/javascript" src="<?php echo $uno; ?>" charset="utf-8"></script>
        <?php endforeach; ?>
    <?php else: ?>
        <script type="text/javascript" src="<?php echo $extjs; ?>" charset="utf-8"></script>
    <?php endif; ?>
<?php endif; ?>
<script type="text/javascript" src="<?php echo site_url('js/main.js?v='.time()); ?>" charset="utf-8"></script>
<?php if (!empty($agente) && $agente['ruta'] == 'consola'): ?>
    <script type="text/javascript" src="<?php echo site_url('js/chatinternousr.js?v='.time()); ?>" charset="utf-8"></script>
<?php endif; ?>
<?php if (isset($jscript)): ?>
    <?php if(is_array($jscript)): ?>
        <?php foreach ($jscript as $uno): ?>
            <script type="text/javascript" src="<?php echo site_url('js/'.$uno.'.js?v='.time()); ?>" charset="utf-8"></script>
        <?php endforeach; ?>
    <?php else: ?>
        <script type="text/javascript" src="<?php echo site_url('js/'.$jscript.'.js?v='.time()); ?>" charset="utf-8"></script>
    <?php endif; ?>
<?php endif; ?>
<?php if (!empty($agente) && $agente['ruta'] == 'consola'): ?>
    <script type="text/javascript" src="<?php echo site_url('js/socks.js?v='.time()); ?>" charset="utf-8"></script>
<?php endif; ?>
<?php
    if( $this->session->flashdata('errormsg') != null )
        $errormsg = is_array($errormsg) ? implode(' ', $errormsg) : $errormsg;
?>
<?php if(isset($msg)): ?><script>toastmsg(<?=$msg["msg"]?>, <?=$msg["tipo"]?>);</script><?php endif; ?>
<?php if(null != $this->session->flashdata('errormsg')): ?><script>toastmsg('<?=$errormsg?>', 'danger');</script><?php endif; ?>
<?php if(null != $this->session->flashdata('infomsg')): ?><script>toastmsg('<?=$infomsg?>', 'success');</script><?php endif; ?>
</body>
</html>
