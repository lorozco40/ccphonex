<div class="container main">
    <form class="form" method="post" id="pitenter">
        <div class="row">
            <div class="col-md-1">
                <h2>PIT</h2>
            </div>
            <div class="col-md-4 offset-md-6">
                <div class="input-group">
                    <input type="seach" class="form-control" id="buscar" placeholder="PIN, Nombre, Telefono ">
                    <div class="input-group-append">
                        <button class="btn btn-info" id="btnsearch" type="button" onclick="traerdata()">Buscar</button>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    </form>
    <div class="row">
        <div class="col-md-9">
            <button type="button" onclick="nuevo()" class="btn btn-primary">Nuevo</button>
        </div>
        <div class="col-md-3 text-align-end justify-content-md-end text-end">
            <button type="button" onclick="guardadoMasivoModal()" class="btn btn-primary">Carga desde archivo</button>
        </div>
    </div><br />
    <div class="scroll-x">
        <div class="table table-striped" id="tablaPit">
            <div class="table-header-group">
                <div class="table-cell">Clave</div>
                <div class="table-cell">Telefono</div>
                <div class="table-cell">Nombre</div>
                <div class="table-cell">Activo</div>
                <div class="table-cell">Usuario Vinculado</div>
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

<div class="modal fade pastilla" id="modal_pit" tabindex="-1" role="dialog" aria-labelledby="pit-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form_pit" onsubmit="guardar();return false;" class="form">
                <div class="modal-header">
                    <h4 class="modal-title" id="pit-modalLabel">Registro de PIT</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id='id' value="0" />
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="pin">Clave</label>
                                </div>
                                <input type="text" class="form-control" name="pin" id="pin" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="phone">Telefono</label>
                                </div>
                                <input type="text" class="form-control" name="phone" id="phone" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="name">Nombre</label>
                                </div>
                                <input type="text" class="form-control" name="name" id="name" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="last">Apellidos</label>
                                </div>
                                <input type="text" class="form-control" name="last" id="last" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="aviso">Aviso</label>
                                </div>
                                <textarea class="form-control" rows="4" name="aviso" id="aviso"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="input-group mb-3 mr-2">
                                <div class="form-check ml-2">
                                    <input type="checkbox" class="form-check-input" id="active" name="active" value="1" checked="checked" onchange="motivo_display()">
                                    <label class="form-check-label" for="active">Activo</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-10">
                            <p class="motivo_display">
                                <i>Motivo por el cual este usuario no estara disponible.</i>
                            </p>
                        </div>
                        <div class="col-sm-12 motivo_display">
                            <div class="input-group mb-3 mr-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="motivo">Motivo</label>
                                </div>
                                <input type="text" class="form-control" name="motivo" id="motivo" />
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
                                <input name="input_id_pit_catalog" id="input_id_pit_catalog" type="hidden"/>
                                    <div class="input-group">
                                        <input type="search" class="form-control" id="rediBuscador" placeholder="PIN, Nombre">
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

<!-- Modal Carga Masiva -->
<div class="modal fade pastilla" id="pitfile-modal" tabindex="-1" role="dialog" aria-labelledby="pit-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form_pit_file">
                <div class="modal-header">
                    <h4 class="modal-title" id="pit-modalLabel">Carga masiva de registros PIT</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
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
                                <li>Solo se tomaran en cuenta las filas cuyo PIN no este vacio, empezando por la fila #2.</li>
                                <li><strong>NO</strong> debe contener comillas simples(<strong> ' </strong>) ni dobles(<strong> " </strong>) en los valores, pero las puede tener como delimitadores de campo.</li>
                                <li>Clic en el boton para descargar el formato base. <a title="Click para descargar" class="btn btn-warning" href="<?php echo site_url('files/formatoPit.csv'); ?>" download>Formato Base</a></li>
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