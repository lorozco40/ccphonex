<div class="container main">
    <div class="row">
        <div class="col-auto">
            <h3>
                Cuentas de email
                <button type="button" class="btn btn-primary" id="nuemcta">Nueva</button>
            </h3>
        </div>
        <div class="col"></div>
    </div>
    <hr>
    <div class="scroll-x">
        <div class="table table-striped" id="TablaEmCtas">
            <div class="table-header-group">
                <div class="table-cell">Email</div>
                <div class="table-cell">Nombre</div>
                <div class="table-cell">Uso</div>
                <div class="table-cell">Tipo</div>
                <div class="table-cell">Campaña</div>
                <div class="table-cell">Entrante</div>
                <div class="table-cell">Saliente</div>
                <div class="table-cell">Activa</div>
                <div class="table-cell text-center" colspan="2">Acción</div>
            </div>
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
            <?php echo form_open('email/guardarcta', ['class'=>'table', 'id'=>'emctaform', 'enctype'=>'multipart/form-data' ], ['id'=>0]); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="emctaModalLabel">Cuenta de email <span id="nombre-modal-cuenta"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-2"></div>
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="campana">Campaña</label>
                                </div>
                                <select class="form-control select-campana" name="id_campaign" required>
                                    <option value="">-- elige --</option>
                                    <script>var campanas = {};</script>
                                        <?php foreach ($campanas as $campana): ?>
                                            <?php
                                            $show = true;
                                            if ( $campana->active == 0 && $show ) {
                                                $show = false;
                                                echo "<option disabled>──── Inactivas ────</option>";
                                            }
                                            ?>
                                            <script>campanas[<?php echo $campana->id; ?>] = "<?php echo $campana->name; ?>";</script>
                                            <option value="<?php echo $campana->id; ?>"><?php echo $campana->name; ?></option>
                                        <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="email">Email</label>
                                </div>
                                <input class="form-control" type="text" name="email" required />
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="nombre" data-toggle="tooltip" title="El que se va a mostrar en los correos">Nombre</label>
                                </div>
                                <input class="form-control" type="text" name="nombre" placeholder="El que se va a mostrar en los correos" required />
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="tipo">Tipo</label>
                                </div>
                                <select class="form-control" name="tipo">
                                    <option value="imap">imap</option>
                                    <option value="pop">pop</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="use">Uso</label>
                                </div>
                                <select class="form-control" name="use" id="use">
                                    <option value="1">CRM Remitente</option>
                                    <option value="2">Modulo Email</option>
                                </select>
                            </div>
                            <p><strong>FIRMA</strong></p>
                            <div class="row border pb-2">
                                <div class="col-12">
                                    <label for="signature_text">Texto</label>
                                    <textarea class="form-control" name="signature_text" id="signature_text" cols="30" rows="3"></textarea>
                                </div>
                                <div class="col-12 img_input">
                                    <label for="signature_img">Imagen</label>
                                    <input class="form-control" type="file" name="signature_img" id="signature_img">
                                    <input type="hidden" name="delete_firma" id="delete_firma">
                                </div>
                                <div class="col-12 img_ui">
                                    <label for="signature_img">Imagen</label>
                                    <br>
                                    <ul class="list-group" id="fileList">
                                        <li class="list-group-item bg-dark py-1">
                                            <div class="d-flex align-items-center">
                                                <div class="px-1">
                                                    <i class="far fa-file-archive"></i>
                                                </div>
                                                <div class="pl-1">
                                                    <small  id="signature-img-ui"></small>
                                                </div>
                                                <div class="ml-auto">
                                                    <button type="button" onclick="delete_img()" class="btn btn-danger btn-sm py-0" title="Quitar">X</button>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-2"></div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <hr>
                            <h5>Servidor entrante</h5>
                            <hr>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="in_servidor">Servidor</label>
                                </div>
                                <input class="form-control" type="text" name="in_servidor" required />
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="in_puerto">Puerto</label>
                                </div>
                                <input class="form-control" type="text" name="in_puerto" value="143" required />
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="in_seguridad">Seguridad</label>
                                </div>
                                <select class="form-control" name="in_seguridad">
                                    <option value="">Ninguna</option>
                                    <option value="tls">STARTTLS</option>
                                    <option value="ssl">SSL/TLS</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="in_user">Usuario</label>
                                </div>
                                <input class="form-control" type="text" name="in_user" required />
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="in_pass">Contraseña</label>
                                </div>
                                <input class="form-control" type="password" name="in_pass" />
                            </div>
                        </div>
                        <div class="col">
                            <hr>
                            <h5>Servidor saliente</h5>
                            <hr>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="out_servidor">Servidor</label>
                                </div>
                                <input class="form-control" type="text" name="out_servidor" required />
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="out_puerto">Puerto</label>
                                </div>
                                <input class="form-control" type="text" name="out_puerto" value="587" required />
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="out_seguridad">Seguridad</label>
                                </div>
                                <select class="form-control" name="out_seguridad">
                                    <option value="">Ninguna</option>
                                    <option value="tls">STARTTLS</option>
                                    <option value="ssl">SSL/TLS</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="out_user">Usuario</label>
                                </div>
                                <input class="form-control" type="text" name="out_user" required />
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="out_pass">Contraseña</label>
                                </div>
                                <input class="form-control" type="password" name="out_pass" />
                            </div>
                        </div>
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

<div class="modal fade" id="SeleccionarCrmModal" tabindex="-1" role="dialog" aria-labelledby="crmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('email/guardarcta', ['class'=>'table', 'id'=>'crmform'], ['id'=>0]); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="crmModalLabel">Asignar CRM - <span id="nombre-modal-crm"></span> -</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="campana">Formulario</label>
                                </div>
                                <select class="form-control in_tipo" id="in_tipo2" name="in_tipo" required></select>
                                <input type="hidden" name="id">
                            </div>
                        </div>
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
