<div class="row">
    <div class="col-lg">
        <form id="smspitform" class="blockForm form">
            <div id="alert_aviso" style="display: none;">
                <div class="alert alert-primary d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle mr-2 "></i>
                    <div id="text-aviso">
                        An example alert with an icon
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="input_pin">Clave / Nombre:</label>
                    <div class="input-group">
                        <input name="input_pin" id="input_pin" type="text" class="form-control" maxlength="10" required />
                        <button class="btn btn-secondary" type="button" id="buscar_nombre">Buscar</button>
                    </div>
                    <input name="input_id_pit_catalog" id="input_id_pit_catalog" type="hidden"/>
                </div>
                <div class="form-group col-sm-6">
                    <label for="nombre_pit" class="form-label d-block">Nombre:</label>
                    <div class="input-group">
                        <input readonly name="nombre" id="nombre_pit" type="text" class="form-control" />
                        <button class="btn btn-secondary" id="mpc_btn" type="button" onclick="edit_contact_pit()" style="display: none;">Editar</button>
                    </div>
                </div>
                <div id="tabla_contacto_pit" class="col-sm-12"></div>
            </div>
            <div class="form-group">
                <label for="input_msg_pit">Mensaje:</label>
                <textarea name="input_msg_pit" id="input_msg_pit" class="form-control" maxlength="160" rows="4" required disabled></textarea>
            </div>
            <div class="row justify-content-between">
                <div class="col-auto">
                    <p class="text-right">Caracteres disponibles <strong>
                        <span style="font-size: .8em;" class="badge badge-dark text-success" id="restopit">160</span>
                    </strong></p>
                </div>
                <div class="col-6">
                    <p>* Por favor evita el uso de caracteres especiales como acentos, tildes, eñes, etc.</p>
                </div>
            </div>
            <div class="row justify-content-around">
                <div class="col-auto form-group">
                    <button type="button" id="btn_enviarsms_pit" class="btn btn-info" disabled>Enviar PIT</button>
                </div>
                <div class="col-auto form-group">
                    <button class="btn btn-secondary" id="limpiasmspit" disabled>Limpiar</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg d-none">
        <h5>Plantillas</h5>
        <div id="listaplantillapitxx">
            <?php if(isset($plantiPit)): foreach($plantiPit as $item): ?>
                <div class="pastilla" data-id="<?php echo $item->id; ?>">
                    <div class="cerrarpastilla pBorrarPit"><i class="fas fa-times"></i></div>
                    <div class="form-group">
                        <textarea id="ptextPit<?php echo $item->id; ?>" class="form-control"><?php echo $item->valor; ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col"><button class="btn btn-secondary pUsarPit">Usar</button></div>
                        <div class="col text-right"><button class="btn btn-info pActuPit">Actualizar</button></div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
        <div class="pastilla">
            <h5>Plantilla nueva:</h5>
            <div class="form-group">
                <textarea name="nuevavalorpit" id="nuevavalorpit" class="form-control" rows="4"></textarea>
            </div>
            <button type="button" class="btn btn-secondary" id="pNuevaPit">Crear Plantilla</button>
        </div>
    </div>
    <div class="col-lg">
        <div id="listaplantillapit">
            <div class="pastilla">
                <div class="row mb-4">
                    <div class="col-6">
                        <h5>Plantillas</h5>
                    </div>
                    <?php if( in_array("pit/plantillas", $this->udata["permiso"]) ): ?>
                        <div class="col-6 text-right">
                            <button type="button" class="btn btn-primary" id="btnNuevaPlantillaPIT">Crear Plantilla</button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div id="divPlantillasPIT" class="col">
                        <?php if( count($plantiPit) > 0 ): ?>
                            <?php foreach($plantiPit as $key=> $item): ?>
                                <div id="<?php echo $item->id; ?>PlantillaPIT" class="row mb-4">
                                    <div class="col-6 col-sm-9">
                                        <label for="textArea<?php echo $item->id; ?>PIT"><?php echo $item->name; ?></label>
                                        <textarea id="textArea<?php echo $item->id; ?>PIT" class="form-control" rows="4"><?php echo $item->valor; ?></textarea>
                                    </div>
                                    <div class="col-6 col-sm-3 d-flex align-items-center justify-content-center" >
                                        <button type="button" class="btn btn-dark ml-2 btnUsarPlantillaPIT" title="Usar" data-id="<?php echo $item->id; ?>"><i class="fas fa-hand-pointer"></i></button>
                                        <?php if( in_array("pit/plantillas", $this->udata["permiso"]) ): ?>
                                            <button type="button" class="btn btn-success ml-2 btnActualizarPlantillaPIT" title="Guardar" data-id="<?php echo $item->id; ?>"><i class="fas fa-save"></i></button>
                                            <button type="button" class="btn btn-danger ml-2 btnEliminarTemplatePIT" title="Eliminar" data-id="<?php echo $item->id; ?>"><i class="fas fa-trash"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <h6 id="NoHayPlantillasPIT">No hay plantillas para la campaña</h6>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pastilla" id="mpc" tabindex="-1" role="dialog" aria-labelledby="pit-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form_pit" onsubmit="mpc_guardar();return false;" class="form">
                <div class="modal-header">
                    <h4 class="modal-title">Edición PIT</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="mpc_id" id='mpc_id' value="0" />
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="mpc_pin">Clave</label>
                                </div>
                                <input type="text" class="form-control" id="mpc_pin" disabled />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="mpc_phone">Telefono</label>
                                </div>
                                <input type="text" class="form-control" name="mpc_phone" id="mpc_phone" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="mpc_name">Nombre</label>
                                </div>
                                <input type="text" class="form-control" disabled id="mpc_name" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="mpc_aviso">Aviso</label>
                                </div>
                                <textarea class="form-control" rows="4" name="mpc_aviso" id="mpc_aviso"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row rediLeyenda">
                        <div class="col-sm-12">
                            <hr/>
                            <div class="fw-bold text-center fs-6">Apartado de Redireccionamiento</div>
                        </div>
                    </div>

                    <!-- Redireccionamiento Habilitar-->
                    <div class="row rediControl">
                        <div class="col-sm-12 text-center">
                            <button type="button" class="btn btn-secondary" onclick="rediPanel()">Agregar Redireccionamiento de mensajes</button>
                        </div>
                    </div>
                    <!-- Redireccionamiento Habilitar -->

                    <!-- Redireccionamiento existente -->
                    <div class="row rediExistente">
                        <div class="col-sm-12">
                            <p>Actualmente existe un redireccionamiento de mensajes, el cual se realizara siempre que este vigente.</p>
                        </div>
                        <div class="col-sm-12">
                            <div class="input-group mb-3 mr-2">
                                <input type="hidden" class="form-control" name="redi_id" id="redi_id" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-4">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="redi_pin">Clave</label>
                                </div>
                                <input type="text" class="form-control" name="redi_pin" disabled id="redi_pin" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-8">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="redi_nombre">Nombre</label>
                                </div>
                                <input type="text" class="form-control" disabled id="redi_nombre" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-5">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="redi_vigencia">Vigencia</label>
                                </div>
                                <input type="date" class="form-control date" name="redi_vigencia" disabled id="redi_vigencia" />
                                <input type="time" class="form-control date" name="redi_vigencia_hora" disabled id="redi_vigencia_hora" />
                            </div>
                        </div>
                        <div class="col-sm-2 col-lg-2">
                            <button type="button" class="btn btn-danger" onclick="eliminarRedirect()">Eliminar</button>
                        </div>
                    </div>
                    <!-- Redireccionamiento existente -->

                    <!-- Espacio de redireccionamiento -->
                    <div class="rediPanel">
                            <div class="row">
                                <div class="col-md-5 offset-md-7 mb-3">
                                <input name="mpc_input_id_pit_catalog" id="mpc_input_id_pit_catalog" type="hidden"/>
                                    <div class="input-group">
                                        <input type="search" class="form-control" id="rediBuscador" placeholder="Clave, Nombre">
                                        <div class="input-group-append">
                                            <button class="btn btn-info" id="rediBtnBuscador" type="button" onclick="rediTabla()">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div id="rediTablaContactoPit"></div>
                                </div>

                                <div class="col-sm-12 col-md-12 col-lg-4">
                                    <div class="input-group mb-3 mr-2">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="rediPin">Clave</label>
                                        </div>
                                        <input type="text" class="form-control" name="rediPin" disabled id="rediPin" />
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-8">
                                    <div class="input-group mb-3 mr-2">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="rediNombre">Nombre</label>
                                        </div>
                                        <input type="text" class="form-control" name="rediNombre" disabled id="rediNombre" />
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 offset-lg-3">
                                    <div class="input-group mb-3 mr-2">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="rediVigencia">Vigencia</label>
                                        </div>
                                        <input type="date" class="form-control date" name="vigencia" id="rediVigencia" />
                                        <input type="time" class="form-control date" name="vigencia_hora" id="rediVigenciaHora" />
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="input-group mb-3 mr-2">
                                        <input type="hidden" class="form-control" name="id_pit_catalog_redirect" id="id_pit_catalog_redirect" />
                                    </div>
                                </div>
                            </div>

                    </div>
                    <!-- Espacio de redireccionamiento -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
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
                            <select id="selectCampanasModalPIT" name="campanas" class="form-control" required>
                                <option value="">-Todas-</option>
                                <?php foreach($campanas as $key=> $item): ?>
                                    <option value="<?=$item->id;?>"><?=$item->name;?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                    <input type="hidden" id="selectCampanasModalPIT" name="campanas" value="<?php echo $this->udata["campanas"]; ?>">
                <?php endif; ?>
                <div class="row">
                    <div class="col">
                        <div class='form-group'>
                            <label for='last'>Nombre Plantilla</label>
                            <input type="text" id="nameModalPIT" name="name" class="form-control" maxlength="100" placeholder="Nombre" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class='form-group'>
                            <label for='last'>Texto Plantilla</label>
                            <textarea id="textAreaModalPIT" name="plantilla" class="form-control" rows="4" placeholder="Plantilla" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardarPlantillaPIT">Guardar</button>
            </div>
        </div>
    </div>
</div>