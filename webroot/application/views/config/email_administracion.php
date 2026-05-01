<div class="container-fluid main">
    <div class="row">
        <div class="col-auto">
            <h3>
                <?php echo $title; ?>
            </h3>
            <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text">Contactos</div>
            </div>
            <select id="contacto" class="form-control" onclick="getpag(0)">
                <option value="">-- Todos --</option>
                <?php foreach($senders as $row): ?>
                    <option value="<?= $row->sender ?>"><?= $row->sender ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        </div>
        <div class="col"></div>
    </div>
    <hr>
    <div class="table table-striped" id="Tabla">
        <div class="table-header-group">
            <div class="table-cell">Fecha</div>
            <div class="table-cell">Asunto</div>
            <div class="table-cell">Contacto</div>
            <div class="table-cell">Asignado a</div>
            <div class="table-cell">Respuesta</div>
            <div class="table-cell">Acción</div>
        </div>
    </div>
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
<div class="modal fade" id="emctaModal" tabindex="-1" role="dialog" aria-labelledby="emctaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="#" class="table" id="FormReasignar" method="post" accept-charset="utf-8" onSubmit="return reasignarAgente()">
                <div class="modal-header">
                    <h5 class="modal-title" id="emctaModalLabel">Reasignar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="asignado_text">Asignado a</label>
                                </div>
                                <input class="form-control" type="hidden" name="id" id="email_entry_id" />
                                <input class="form-control" type="text" id="asignado_text" disabled readonly required />
                            </div>
                            <br>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="reasignar">Reasigar a:</label>
                                </div>
                                <select class="form-control" name="id_user" id="reasignar" required >
                                    <option value="">--Seleccione un agente--</option>
                                    <?php foreach($usuarios as $row): ?>
                                        <option value="<?= $row->id ?>"><?= $row->nombre ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="ModalCorreo" tabindex="-1" role="dialog" aria-labelledby="ModalCorreoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="ModalCorreoLabel">
                    <i>De: </i><b id="correo_sender"></b>, <b id="correo_from"></b><br>
                    <i>Fecha: </i><b id="correo_date"></b><br>
                    <i>Asunto: </i><b id="asunto"></b><br>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col" class="white">
                        <div id="correo_contend" </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>