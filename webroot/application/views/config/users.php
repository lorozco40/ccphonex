<div class="container-fluid main">
    <div class="row">
        <div class="col-auto">
            <h2>
                Usuarios
                <?php if(in_array('usuarios/crear', $agente['permiso'])): ?>
                    <button type="button" class="btn btn-primary" id="nuser">Nuevo</button>
                <?php endif; ?>
            </h2>
        </div>
        <div class="col"></div>
        <div class="col-auto">
            <form class="form" method="post" id="buser">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="email, nombre, apellido o extensión"/>
                    <div class="input-group-append">
                        <button title="Buscar usuario" type="submit" class="btn btn-info">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col"></div>
        <div class="col-auto">
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">Campaña</div>
                </div>
                <select class="form-control" name="cam">
                    <option value="">-- Todas --</option>
                    <script type="text/javascript">var cams = {};</script>

                    <?php 
                    $show = true;
                    foreach ($campanas as $camp) {
                        if ( $camp->active == 0 && $show ) {
                            $show = false;
                            echo "<option disabled>──── Inactivas ────</option>";
                        }
                        echo "
                        <script type='text/javascript'>cams[".$camp->id."] = '".$camp->name."';</script>
                        <option value='".$camp->id."'>".$camp->name."</option>
                        ";
                    } ?>
                </select>
            </div>
        </div>
    </div>
    <hr>
    <div class="table table-striped" id="liuser">
        <div class="table-header-group">
            <div class="table-cell">Online</div>
            <div class="table-cell">Extensión</div>
            <div class="table-cell">Nombre</div>
            <div class="table-cell">Tipo</div>
            <div class="table-cell">Email</div>
            <div class="table-cell">Campaña(s)</div>
            <div class="table-cell">Activo</div>
            <div class="table-cell" colspan="2">Acción</div>
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
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open('usuarios/guardar', ['class'=>'form', 'id'=>'formuser'], ['id'=>0]); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="email">Nombre(s)</label>
                                </div>
                                <input class="form-control" type="text" name="name" placeholder="Nombres" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="email">Apellido(s)</label>
                                </div>
                                <input class="form-control" type="text" name="last" placeholder="Apellidos" />
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="email">Email</label>
                                </div>
                                <input class="form-control" type="email" name="user" placeholder="correo@dominio.com" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="perfil">Perfil</label>
                                </div>
                                <select class="form-control" id="perfil" name="perfil">
                                    <?php foreach ($perfiles as $perfil) : ?>
                                        <option value='<?= $perfil->value ?>'><?= $perfil->text ?></option>
                                    <?php endforeach; ?>
                                    <?php if( $this->udata['perfil'] == 'admin' ) : ?>
                                        <option value='admin'>Administrador</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputPass">Contraseña</label>
                                </div>
                                <input class="form-control" type="password" name="pass" id="inputPass" placeholder="pass" />
                                <div class="input-group-prepend">
                                    <span id="btnPass" class="input-group-text" onclick="mostrarContrasena('inputPass', 'iconPass')"><i id="iconPass" class="far fa-eye-slash"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="email">Extensión</label>
                                </div>
                                <input class="form-control" type="text" name="extension" placeholder="1001" />
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="input-group" id="caminput">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="campanas">Campaña</label>
                                </div>
                                <select multiple class="form-control" id=campanas  name="campana[]">
                                    <?= options_select_campaign($campanas) ?>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="active" checked='checked' />
                                <label class="form-check-label" for="active">Activo</label>
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
