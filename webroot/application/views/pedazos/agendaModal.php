<div class="modal fade pastilla" id="agenda-modal" tabindex="-1" role="dialog" aria-labelledby="agenda-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open('agenda/crear', array('role'=>'form', 'class'=>'form', 'id'=>'ageform')); ?>
            <div class="modal-header">
                <h4 class="modal-title" id="agenda-modalLabel">Registro de agenda</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap">
                    <input type="hidden" name="id" id='id' value="0" />
                    <div class="input-group mr-2 mb-2">
                        <div class="form-check ml-2">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" class="form-check-input" id="active" name="active" value="1" checked="checked">
                            <label class="form-check-label" for="active"><?=traduce('active')?></label>
                        </div>
                        <div class="form-check ml-3">
                            <input type="hidden" name="available" value="0">
                            <input type="checkbox" class="form-check-input" id="available" name="available" value="1" checked="checked">
                            <label class="form-check-label" for="available"><?=traduce('available')?></label>
                        </div>
                    </div>
                    <div class="input-group mb-2 mr-2">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="id_user">Propiedad</label>
                        </div>
                        <select class="form-control" name="id_user" id="id_user">
                            <option value="">Pública (P)</option>
                            <?php foreach ($agentes as $agen): ?>
                                <?php if( ( in_array($agente['perfil'], ['agente', 'crm']) && $agente['id'] == $agen->id ) || !in_array($agente['perfil'], ['agente', 'crm']) ): ?>
                                    <option value="<?php echo $agen->id; ?>"><?php echo $agen->name.' '.$agen->last; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group mb-2 mr-2 scam"></div>
                    <?php if(count($campanas)>1): ?>
                        <div class="input-group mb-2 mr-2">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="id_campaign">Campaña</label>
                            </div>
                            <select class="form-control" name="id_campaign">
                                <option value="">Todas</option>
                                <?= options_select_campaign($campanas) ?>
                            </select>
                        </div>
                        <div class="input-group mb-2 mr-2"></div>
                    <?php else: $cam = array_pop($campanas); ?>
                        <input type="hidden" name="id_campaign" value="<?=$cam->id?>" />
                    <?php endif; ?>
                    <?php $ageNomos = ['id', 'id_user', 'id_campaign', 'active', 'available', 'agenda']; ?>
                    <?php foreach($camposagen as $campo): if(!in_array($campo, $ageNomos)): ?>
                        <div class="input-group mb-2 mr-2">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="<?=$campo?>"><?=traduce($campo)?></label>
                            </div>
                            <input type="text" class="form-control" name="<?=$campo?>" />
                        </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
