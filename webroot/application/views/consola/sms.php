<div class="row">
    <div class="col-lg mb-4">
        <form id="smsform" class="blockForm form">
            <div class="form-group">
                <label for="input_num">Número telefónico:</label>
                <input name="input_num" id="input_num" type="number" class="form-control" maxlength="15" required />
            </div>
            <div class="form-group">
                <label for="input_msg">Mensaje:</label>
                <textarea name="input_msg" id="input_msg" class="form-control" maxlength="160" rows="4" required></textarea>
            </div>
            <div class="row justify-content-between">
                <div class="col-auto">
                    <p class="text-right">Caracteres disponibles <strong>
                        <span style="font-size: .8em;" class="badge badge-dark text-success" id="resto">160</span>
                    </strong></p>
                </div>
                <div class="col-6">
                    <p>* Por favor evita el uso de caracteres especiales como acentos, tildes, eñes, etc.</p>
                </div>
            </div>
            <div class="row justify-content-around">
                <div class="col-auto form-group">
                    <button type="submit" id="btn_enviarsms" action="<?php echo site_url('sms/enviar'); ?>" class="btn btn-info">Enviar SMS</button>
                </div>
                <div class="col-auto form-group">
                    <button class="btn btn-secondary" id="limpiasms">Limpiar</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg">
        <div id="listaplantillasms">
            <div class="pastilla">
                <div class="row mb-4">
                    <div class="col-6">
                        <h5>Plantillas</h5>
                    </div>
                    <?php if( in_array("sms/plantillas", $this->udata["permiso"]) ): ?>
                        <div class="col-6 text-right">
                            <button type="button" class="btn btn-primary" id="btnNuevaPlantillaSMS">Crear Plantilla</button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div id="divPlantillasSMS" class="col">
                        <?php if( count($plantiSms) > 0 ): ?>
                            <?php foreach($plantiSms as $key=> $item): ?>
                                <div id="<?php echo $item->id; ?>PlantillaSMS" class="row mb-4">
                                    <div class="col-6 col-sm-9">
                                        <label for="textArea<?php echo $item->id; ?>SMS"><?php echo $item->name; ?></label>
                                        <textarea id="textArea<?php echo $item->id; ?>SMS" class="form-control" rows="4"><?php echo $item->valor; ?></textarea>
                                    </div>
                                    <div class="col-6 col-sm-3 d-flex align-items-center justify-content-center" >
                                        <button type="button" class="btn btn-dark ml-2 btnUsarPlantillaSMS" title="Usar" data-id="<?php echo $item->id; ?>"><i class="fas fa-hand-pointer"></i></button>
                                        <?php if( in_array("sms/plantillas", $this->udata["permiso"]) ): ?>
                                            <button type="button" class="btn btn-success ml-2 btnActualizarPlantillaSMS" title="Guardar" data-id="<?php echo $item->id; ?>"><i class="fas fa-save"></i></button>
                                            <button type="button" class="btn btn-danger ml-2 btnEliminarTemplateSMS" title="Eliminar" data-id="<?php echo $item->id; ?>"><i class="fas fa-trash"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <h6 id="NoHayPlantillasSMS">No hay plantillas para la campaña</h6>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="plantillaModalSMS" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="plantillaModalLabelSMS" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="plantillaModalLabelSMS">Plantilla Nueva</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if(count($campanas) > 1): ?>
                <div class="row">
                    <div class="col">
                        <div class='form-group'>
                            <label for='user'>Campaña</label>
                            <select id="selectCampanasModalSMS" name="campanas" class="form-control" required>
                                <option value="">-Seleccione-</option>
                                <?php foreach($campanas as $key=> $item): ?>
                                    <option value="<?=$item->id;?>"><?=$item->name;?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                    <input type="hidden" id="selectCampanasModalSMS" name="campanas" value="<?php echo $this->udata["campanas"]; ?>">
                <?php endif; ?>
                <div class="row">
                    <div class="col">
                        <div class='form-group'>
                            <label for='last'>Nombre Plantilla</label>
                            <input type="text" id="nameModalSMS" name="name" class="form-control" maxlength="100" placeholder="Nombre" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class='form-group'>
                            <label for='last'>Texto Plantilla</label>
                            <textarea id="textAreaModalSMS" name="plantilla" class="form-control" rows="4" placeholder="Plantilla" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardarPlantillaSMS">Guardar</button>
            </div>
        </div>
    </div>
</div>
