<div class="container-fluid main">
    <div class="row">
        <div class="col-auto">
            <h2>
                Menú
                <?php if(in_array('usuarios/crear', $agente['permiso'])): ?>
                    <button type="button" class="btn btn-primary" id="nuevo-menu">Nuevo</button>
                <?php endif; ?>
            </h2>
        </div>
        <div class="col"></div>
        <div class="col-auto">
            <form class="form" method="post" id="bmenu">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="etiquetas, permiso, nivel"/>
                    <div class="input-group-append">
                        <button title="Buscar usuario" type="submit" class="btn btn-info">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <hr>
    <div class="table table-striped" id="tabla-menu">
        <div class="table-header-group">
            <div class="table-cell">Etiqueta</div>
            <div class="table-cell text-center">Nivel</div>
            <div class="table-cell">Pertenece</div>
            <div class="table-cell text-center">Orden</div>
            <div class="table-cell text-center">Submenu</div>
            <div class="table-cell text-center">Icono</div>
            <div class="table-cell">Permiso</div>
            <div class="table-cell text-center">Activo</div>
            <div class="table-cell text-center" colspan="2">Acción</div>
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
<div class="modal fade" id="menuModel" tabindex="-1" role="dialog" aria-labelledby="menuModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open('usuarios/guardar', ['class'=>'form', 'id'=>'formmenu']); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Menú</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" for="etiqueta">Etiqueta</span>
                                </div>
                                <input class="form-control" type="text" id="etiqueta" name="etiqueta" placeholder="Etiqueta" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" for="icono">Icono</span>
                                </div>
                                <input class="form-control" type="text" id="icono" name="icono" placeholder="Icono" />
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" for="nivel">Nivel</span>
                                </div>
                                <select class="form-control" name="nivel" id="nivel">
                                    <option value="">-Seleccione-</option>
                                    <?php for($i=1; $i <= $nivel; $i++) : ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div id="ver-pertenece" class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" for="pertenece">Pertenece a</span>
                                </div>
                                <select class="form-control" name="pertenece" id="pertenece">
                                    <option value="">-Seleccione-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="input-group custom-switch">
                                <input type="checkbox" class="custom-control-input" id="submenu" name="submenu" value="1">
                                <label class="custom-control-label" for="submenu">¿Tiene submenus?</label>
                            </div>
                        </div>
                        <div class="col">
                            <div id="ver-permiso" class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" for="permiso">Permiso</span>
                                </div>
                                <input class="form-control" type="text" id="permiso" name="permiso" placeholder="Permiso" />
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" for="orden">Orden</span>
                                </div>
                                <select class="form-control" name="orden" id="orden">
                                    <?php for($i=1; $i <= $orden; $i++) : ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col" id="ver-active">
                            <div class="input-group custom-switch">
                                <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" checked='checked'>
                                <label class="custom-control-label" for="active">Activo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" value="0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
