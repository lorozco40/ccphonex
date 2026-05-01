<div class="container main">
    <div class="row">
        <div class="col-md-1">
            <h2>Plantillas&nbsp;PIT</h2>
        </div>
    </div>
    <hr>
    <div class="col-lg">
        <div id="listaplantillapit">
            <div class="pastilla">
                <div class="row mb-4">
                    <div class="col-6">
                        <h5>Plantillas</h5>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="btn btn-primary" id="btnNuevaPlantillaPIT">Crear Plantilla</button>
                    </div>
                </div>
                <div class="row">
                    <div id="divPlantillasPIT" class="col">
                        <?php if( count($plantiPit) > 0 ): ?>
                            <?php if(count($campanas) > 1): ?>
                                <div class='form-group'>
                                    <label for='user'>Campaña</label>
                                    <select id="selectCampanaPIT" name="campanas" class="form-control" required>
                                        <option value="">-Seleccione-</option>
                                        <?php foreach($campanas as $key=> $item): ?>
                                            <option value="<?=$item->id;?>"><?=$item->name;?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <input type="hidden" id="hiddenCampanaPIT" name="campanas" value="<?php echo $this->udata["campanas"]; ?>" >
                            <?php endif; ?>
                            <div class="row mb-4">
                                <div class="col-6 col-sm-9">
                                    <select id="selectPlantillaPIT" name="plantilla" class="form-control"></select>
                                </div>
                                <div class="col-6 col-sm-3 d-flex align-items-center justify-content-center" >
                                    <button id="btnActualizarPlantillaPIT" type="button" class="btn btn-warning ml-2" title="Editar"><i class="fas fa-edit"></i></button>
                                    <button id="btnEliminarTemplatePIT" type="button" class="btn btn-danger ml-2" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        <?php else: ?>
                            <h6 id="NoHayPlantillasPIT">No hay plantillas para la campaña</h6>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="plantillaModalPIT" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="plantillaModalLabelPIT" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="plantillaModalLabelPIT">Plantilla Nueva</h5>
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
                            <select id="selectCampanasModalPIT" name="campana" class="form-control" required>
                                <option value="">-Seleccione-</option>
                                <?php foreach($campanas as $key=> $item): ?>
                                    <option value="<?=$item->id;?>"><?=$item->name;?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col">
                        <div class='form-group'>
                            <label for='last'>Nombre Plantilla</label>
                            <input id="textModalPIT" name="nombre" class="form-control" required />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class='form-group'>
                            <label for='last'>Texto Plantilla</label>
                            <textarea id="textAreaModalPIT" name="plantilla" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="idModalPIT" name="id" value="">
                <input type="hidden" id="accionModalPIT" name="accion" value="">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardarPlantillaPIT">Guardar</button>
            </div>
        </div>
    </div>
</div>
