<script>var wactas = JSON.parse('<?=addslashes(json_encode($wactas))?>');</script>
<div id="whatsapptab">
    <ul class="nav nav-pills mb-3" id="wa-pills-tab" role="tablist">
        <?php $selected='true'; $active = 'active'; foreach ($wactas as $wacta): ?>
            <?php if($active == 'active') $idWaCtaActiva = $wacta->id; ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?=$active?>" id="pills-wa<?=$wacta->id?>-tab" data-toggle="pill"
                    href="#pills-wa<?=$wacta->id?>" role="tab" aria-controls="pills-wa<?=$wacta->id?>"
                    data-id="<?=$wacta->id?>" aria-selected="<?=$selected?>"><?=$wacta->nombre?></a>
            </li>
        <?php $active = ''; $selected='false'; endforeach; ?>
    </ul>
    <div class="tab-content" id="wa-pills-tabContent">
        <?php $active = 'show active'; foreach ($wactas as $wacta): ?>
            <div class="tab-pane fade <?=$active?>" id="pills-wa<?=$wacta->id?>" role="tabpanel" aria-labelledby="pills-wa<?=$wacta->id?>-tab">
                <?php include("whatsapp_cta.php"); ?>
            </div>
        <?php $active = ''; endforeach; ?>
    </div>
    <?php include('emojis.html'); ?>
</div>
<div class="modal fade" id="modalWaFile" data-backdrop="static" data-keyboard="false"
    tabindex="-1" role="dialog" aria-labelledby="wafileModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="wafileModalTitle">Enviar archivo</h4>
            </div>
            <form id="enviarWaMediaForm">
                <div class="modal-body">
                    <input type="hidden" name="cid"    class="wacid">
                    <input type="hidden" name="sid"    class="wasid">
                    <input type="hidden" name="wid"    class="wawid" value="<?=$idWaCtaActiva?>">
                    <input type="hidden" name="wac"    class="wawac">
                    <input type="hidden" name="lastid" class="walastid">
                    <input type="hidden" name="type"   class="watype" value="media">
                    <input type="file"   name="file"   class="form-control-file">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" id="mediaModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
