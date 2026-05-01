<div class="row pl-3" id="emcontainer">
    <div class="col-lg-3">
        <div class="row">
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <input type="text" class="form-control" id="embuscar" placeholder="email o asunto">
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" role="tab" href="#emlista">Entrantes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" role="tab" href="#emvlista">Salientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" role="tab" href="#embures">Buscado</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane lista fade pl-2 pr-1 active show" role="tabpanel" id="emlista"><p>No se ha recibido ningún mensaje aún</p></div>
                <div class="tab-pane lista fade pl-2 pr-1" role="tabpanel" id="emvlista"><p>No se ha enviado ningún mensaje aún</p></div>
                <div class="tab-pane lista fade pl-2 pr-1" role="tabpanel" id="embures"><p>Realiza una búsqueda por email.</p></div>
            </div>
        </div>
    </div>
    <div class="col border-left mt-3 mt-lg-0">
        <div class="row">
            <div class="col form-inline">
                <button type="button" class="btn btn-success" id="emnuevo" data-toggle="tooltip" title="Nuevo"><i class="far fa-edit"></i></button>
                <button type="button" class="btn btn-primary noemselin" id="emreply" data-toggle="tooltip" title="Responder"><i class="fas fa-reply"></i></button>
                <button type="button" class="btn btn-primary noemselin" id="emreplyall" data-toggle="tooltip" title="Responder a todos"><i class="fas fa-reply-all"></i></button>
                <button type="button" class="btn btn-primary noemselin" id="emforward" data-toggle="tooltip" title="Reenviar"><i class="fas fa-share"></i></button>
                <div class="input-group noemselin">
                    <select id="emtransto" class="form-control">
                        <option value="">-- Elige --</option>
                    </select>
                    <div class="input-group-append">
                        <button id="emdotrans" class="btn btn-primary" data-toggle="tooltip" title="Transferir"><i class="far fa-share-square"></i></button>
                    </div>
                </div>
                <button type="button" class="btn btn-danger noemsel" id="emclose" data-toggle="tooltip" title="Cerrar"><i class="far fa-times-circle"></i></button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-8" id="eminfo"></div>
            <div class="col-4 left-border" id="emadjuntos"></div>
        </div>
        <hr>
        <div class="row">
            <div class="col" id="emcuerpo">
                <iframe style="width:100%;height:550px;border:0;" id="emiframe" srcdoc="<p>Assertive mail! cliente de correo</p>">
                    <p>Tu navegador no soporta iframes para visualizar los correos.</p>
                </iframe>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="emModal" tabindex="-1" role="dialog" aria-labelledby="emModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="emForm" class="form" method="post" enctype="multipart/form-data">
                <input name="id_cuenta" type="hidden" value="0" />
                <input name="id" type="hidden" value="0" />
                <div class="modal-header">
                    <h5 class="modal-title">Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="btn-toolbar row">
                        <div class="form-group col-9 col-lg-10">
                            <label for="to">Para (separar con comas):</label>
                            <textarea name="to" class="form-control" rows="2" required="required"></textarea>
                        </div>
                        <div class="col-3 col-lg-2 py-4">
                            <div class="btn-group h-100" role="group" aria-label="First group">
                                <button id="btn-cc" type="button" class="btn btn-secondary font-weight-bolder">CC</button>
                                <button id="btn-cco" type="button" class="btn btn-secondary font-weight-bolder">CCO</button>
                            </div>
                        </div>
                        
                    </div>
                    <div id="div-cc" class="form-group" hidden>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">CC:</span>
                            </div>
                            <textarea id="textarea-cc" name="cc" class="form-control" rows="2"></textarea>
                            <div class="input-group-append">
                                <button class="btn btn-info" type="button" id="quitar-cc">Quitar</button>
                            </div>
                        </div>
                    </div>
                    <div id="div-cco" class="form-group" hidden>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">CCO:</span>
                            </div>
                            <textarea id="textarea-cco" name="cco" class="form-control" rows="2"></textarea>
                            <div class="input-group-append">
                                <button class="btn btn-info" type="button" id="quitar-cco">Quitar</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject">Asunto:</label>
                        <input name="subject" type="text" class="form-control" required="required" />
                    </div>
                    <div class="form-group">
                        <label for="body">Mensaje:</label>
                        <textarea name="body" id="input_email_body" class="form-control" required="required"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlFile1">Adjunto</label>
                        <div class="custom-file">
                            <input type="hidden" name="existingFilesAtachList" id="existingFilesAtachList" />
                            <input id="fileEmailAdjuntoModal" name="files[]" type="file" class="custom-file-input" lang="es" multiple>
                            <input id="fileEmailAdjuntoModal2" name="attachment[]" type="file" class="custom-file-input" lang="es" multiple style="display: none;">
                            <label class="custom-file-label" for="customFile">Seleccionar archivos</label>
                        </div>
                        <div id="feedbackEmailAdjuntoModal" class="invalid-feedback">Example invalid custom file feedback</div>
                        <label for="exampleFormControlFile1"><em>*El tamaño del archivo no debe ser mayor a <?php echo $this->config->config["post_max_size"]; ?>B</em></label>
                        <input type="hidden" name="post_max_size" id="post_max_size_email" value="<?php echo $this->config->config["post_max_size"]; ?>">
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <ul class="list-group" id="fileList"></ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info" id="btnEnviarEmailModal">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>
