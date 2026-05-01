<div class="container main">
    <form class="form" method="post" id="agendaenter">
        <div class="row">
            <div class="col-md-1">
                <h2>Agenda</h2>
            </div>
            <div class="col-md-4 offset-md-6">
                <div class="input-group">
                    <input type="seach" class="form-control" id="buscar" placeholder="nombre, apellido, telefono o email ...">
                    <div class="input-group-append">
                        <button class="btn btn-info" id="btnsearch" type="submit">Buscar</button>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <a title="Exportar a Excel" class="logos" href="<?php echo site_url('agenda/acsv'); ?>" target="_blank"> <img src="<?php echo site_url('assets/img/excel5.png'); ?>"></a>
            </div>
        </div>
        <hr>
    </form>
    <div class="row">
        <div class="col-md-9">
            <button type="button" id="nuereg" class="btn btn-primary">Nuevo</button>
        </div>
        <div class="col-md-3 text-align-end justify-content-md-end text-end">
            <button type="button" onclick="guardadoMasivoModal()" class="btn btn-primary">Carga desde archivo</button>
        </div>
    </div><br />
    <div class="scroll-x">
        <div class="table table-striped" id="tablaagenda">
            <div class="table-header-group">
                <div class="table-cell">Propiedad</div>
                <div class="table-cell"><?=traduce('name')?></div>
                <div class="table-cell"><?=traduce('last')?></div>
                <div class="table-cell"><?=traduce('phone')?></div>
                <div class="table-cell"><?=traduce('email')?></div>
                <div class="table-cell"><?=traduce('active')?></div>
                <div class="table-cell"><?=traduce('available')?></div>
                <div class="table-cell">Acción</div>
            </div>
        </div>
    </div><br />
    <div class="row">
        <div class="col">
            <div id="paginacion"></div>
        </div>
        <div class="col text-right">
            <p>Registros por página:</p>
            <select class="form-control" id="elirpp" style="max-width:5em;float:right;">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
</div>
<?php include(APPPATH.'views/pedazos/agendaModal.php'); ?>

<div class="modal fade pastilla" id="agendafile-modal" tabindex="-1" role="dialog" aria-labelledby="agenda-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form_agenda_file">
                <div class="modal-header">
                    <h4 class="modal-title" id="agenda-modalLabel">Carga de agenda por archivo</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="input-group mb-2 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="id_userm">Propiedad</label>
                                </div>
                                <select class="form-control" name="id_user" id="id_userm">
                                    <option value="">Pública (P)</option>
                                    <?php foreach ($agentes as $agen): ?>
                                        <?php if(($agente['perfil']=='agente' && $agente['id']==$agen->id) || $agente['perfil']!='agente'): ?>
                                            <option value="<?php echo $agen->id; ?>"><?php echo $agen->name.' '.$agen->last; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <?php if(count($campanas_aux)>1): ?>
                            <div class="input-group mb-2 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="id_campaignm">Campaña</label>
                                </div>
                                <select class="form-control" name="id_campaign" id="id_campaignm">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($campanas_aux as $cam): ?>
                                    <option value="<?php echo $cam->id; ?>"><?php echo $cam->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="input-group mb-2 mr-2"></div>
                            <?php else: $cam = array_pop($campanas_aux); ?>
                            <input type="hidden" name="id_campaign" value="<?=$cam->id?>" />
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <input type="file" class="form-control" name="contactos">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <ul>
                                <h3>Preparación del archivo.</h3><br>
                                <li>La primer fila es usada solo para mostrar el orden de los campos, esta no debe ser modificada.</li>
                                <li>El archivo deberá estar separado por comas (<strong> , </strong>).</li>
                                <li>Solo se tomaran en cuenta las filas cuyo nombre no este vacio, empezando por la fila #2.</li>
                                <li><strong>NO</strong> debe contener comillas simples(<strong> ' </strong>) ni dobles(<strong> " </strong>) en los valores, pero las puede tener como delimitadores de campo.</li>
                                <li>Clic en el boton para descargar el formato base. <a title="Click para descargar" class="btn btn-warning" href="<?php echo site_url('files/formatoAgenda.csv'); ?>" download>Formato Base</a></li>
                            </ul>
                        </div>
                    </div>
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardadoMasivo()">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

