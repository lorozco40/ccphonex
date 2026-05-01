<div class="container main">
    <div class="row">
        <h2 class="tit-div">Emergencia <strong style="color: #B40404; text-shadow: 2px 2px 5px black;">(Cuidado !!!)</strong></h2>
    </div>
    <hr>
    <div class="row">
        <p>El siguiente botón reiniciará el servicio de conmutador, cortará
            cualquier llamada en curso y cerrará las sesiones de usuario activas en assertive.
        </p>
        <p>Guardaremos tus datos, la hora y fecha en que se utilizó por favor ¡ten CUIDADO!</p>
    </div>
    <div class="row">
        <pre id="respuesta" style="padding:15px; display:none;"></pre>
    </div>
    <div class="row text-center">
        <button type="button" name="button" class="btn btn-danger" id="btn_emergencia"
            style="margin:25px auto; border-radius:100%; width:200px; height:200px; box-shadow: #FF0000 0 0 35px; font-size: 20px">¡ EMERGENCIA !</button>
    </div>
    <script type="text/javascript" src="<?php echo site_url('js/emer.js?v='.time()); ?>"></script>
</div>
