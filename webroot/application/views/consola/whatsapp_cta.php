<div class="row" id="wasaaa<?=$wacta->id?>">
    <div class="col-lg-3 order-2 order-lg-1">
        <i class="wastatus"><?php echo formatel($wacta->cuenta); ?></i>
        <br />
        <h5>Contactos <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="right"
            title="Se muestran los primeros 100 registros, los demás se pueden encontrar con el buscador."></i></h5>
        <div class="pastilla">
            <div class="input-group">
                <input type="text" class="form-control wabusc" id="wabusc<?=$wacta->id?>" />
                <div class="input-group-append">
                    <button class="btn btn-info wabuscbtn">Buscar</button>
                </div>
            </div>
            <div id="wabuscres<?=$wacta->id?>"></div>
            <br>
            <?php if (in_array('wa_masivos', $agente['permisoSec'])): ?>
                <a class="waactivate mb-1" id="waContactsTodos<?=$wacta->id?>" data-id="0" data-wac="todos" href="#">Todos *</a>
            <?php endif; ?>
            <div id="wacontactos<?=$wacta->id?>" class="listascroll">
                <?php foreach($wacta->contactos as $key => $cont): ?>
                    <p id="w<?=$wacta->id?>ac<?=$cont->id?>" class="wacontact" data-wac="<?=$cont->account?>">
                        <a class="waactivate" data-id="<?php echo $cont->id; ?>" data-sid="0" data-wac="<?php echo $cont->account; ?>" href="#"><?=$cont->name?></a>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-9 order-1 order-lg-2">
        <div class="row justify-content-between" style="margin-bottom:8px;">
            <div class="col-8"><i class="fas fa-user-circle"></i> <strong id="wacontactname<?=$wacta->id?>">Nombre de usuario</strong></div>
            <div class="col-1"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 39 39"><path fill="#00E676" d="M10.7 32.8l.6.3c2.5 1.5 5.3 2.2 8.1 2.2 8.8 0 16-7.2 16-16 0-4.2-1.7-8.3-4.7-11.3s-7-4.7-11.3-4.7c-8.8 0-16 7.2-15.9 16.1 0 3 .9 5.9 2.4 8.4l.4.6-1.6 5.9 6-1.5z"></path><path fill="#FFF" d="M32.4 6.4C29 2.9 24.3 1 19.5 1 9.3 1 1.1 9.3 1.2 19.4c0 3.2.9 6.3 2.4 9.1L1 38l9.7-2.5c2.7 1.5 5.7 2.2 8.7 2.2 10.1 0 18.3-8.3 18.3-18.4 0-4.9-1.9-9.5-5.3-12.9zM19.5 34.6c-2.7 0-5.4-.7-7.7-2.1l-.6-.3-5.8 1.5L6.9 28l-.4-.6c-4.4-7.1-2.3-16.5 4.9-20.9s16.5-2.3 20.9 4.9 2.3 16.5-4.9 20.9c-2.3 1.5-5.1 2.3-7.9 2.3zm8.8-11.1l-1.1-.5s-1.6-.7-2.6-1.2c-.1 0-.2-.1-.3-.1-.3 0-.5.1-.7.2 0 0-.1.1-1.5 1.7-.1.2-.3.3-.5.3h-.1c-.1 0-.3-.1-.4-.2l-.5-.2c-1.1-.5-2.1-1.1-2.9-1.9-.2-.2-.5-.4-.7-.6-.7-.7-1.4-1.5-1.9-2.4l-.1-.2c-.1-.1-.1-.2-.2-.4 0-.2 0-.4.1-.5 0 0 .4-.5.7-.8.2-.2.3-.5.5-.7.2-.3.3-.7.2-1-.1-.5-1.3-3.2-1.6-3.8-.2-.3-.4-.4-.7-.5h-1.1c-.2 0-.4.1-.6.1l-.1.1c-.2.1-.4.3-.6.4-.2.2-.3.4-.5.6-.7.9-1.1 2-1.1 3.1 0 .8.2 1.6.5 2.3l.1.3c.9 1.9 2.1 3.6 3.7 5.1l.4.4c.3.3.6.5.8.8 2.1 1.8 4.5 3.1 7.2 3.8.3.1.7.1 1 .2h1c.5 0 1.1-.2 1.5-.4.3-.2.5-.2.7-.4l.2-.2c.2-.2.4-.3.6-.5s.4-.4.5-.6c.2-.4.3-.9.4-1.4v-.7s-.1-.1-.3-.2z"></path></svg></div>
        </div>
        <div class="row">
            <div class="col">
                <a href="#" id="wa-cargar-mas<?=$wacta->id?>" class="wa-cargar-mas d-none"><span>Cargar anteriores</span></a>
                <div class="pastilla wamsgs" id="wamsgs<?=$wacta->id?>"></div>
            </div>
        </div>
        <?php echo form_open('whatsapp/enviar', 'class="waform"'); ?>
        <div class="row">
            <div class="col">
                <input type="hidden" name="cid"    class="wacid"><!-- Watsapp Contact ID -->
                <input type="hidden" name="sid"    class="wasid"><!-- Watsapp Session ID -->
                <input type="hidden" name="wid"    value="<?=$wacta->id?>"><!-- Watsapp Account ID -->
                <input type="hidden" name="wac"    class="wawac"><!-- Watsapp Phone Number (account) -->
                <input type="hidden" name="lastid" class="walastid">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text btn btn-secondary emojikinon-btn" disabled="disabled"><i class="far fa-smile em2"></i></span>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalWaFile" disabled="disabled"><i class="fas fa-paperclip"></i></button>
                    </div>
                    <textarea name="watext" class="form-control emojikinon-con" id="watext<?=$wacta->id?>" disabled="disabled"></textarea>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary" disabled="disabled"><i class="fas fa-paper-plane em2"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <br>
                <p>Después de agradecer el contácto, no olvides</p>
                <button type="button" class="btn btn-info waterminabtn" disabled="disabled">Terminar conversación</button>
                <span class="mx-2">o</span>
                <button type="button" class="btn btn-info waencytermbtn" disabled="disabled">Encuesta y Terminar</button>
            </div>
            <div class="col">
                <br>
                <p class="form-inline">
                    <select class="form-control" id="transferlist<?=$wacta->id?>" disabled="disabled">
                        <option value="0">Transferir chat a: </option>
                        <?php $show = true;
                        foreach($wacta->agentes as $key => $as):
                            if ( $as->conectado == 0 && $show ) {
                                $show = false;
                                echo "<option disabled>──── Desconectados ────</option>";
                            }
                        ?>
                        <?php $sup = ($as->perfil == 'supervisor') ? '(S) ' : ''; ?>
                        <option value="<?=$as->id?>"><?=$sup.$as->nombre?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-info watransferir" disabled="disabled">Transferir</button>
                </p>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
