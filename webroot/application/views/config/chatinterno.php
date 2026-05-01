<div class="container main">
    <div class="row">
        <div class="col-auto">
            <h2>
                <?php echo $title; ?>
            </h2>
        </div>
        <div class="col"></div>
        <div class="col-auto">
            <form class="form" method="post" id="busform">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Usuario"/>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-info">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col d-flex justify-content-end">
            <button type='button' class="btn-save btn btn-success">Actualizar permisos</button>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col">
            <form id="permisos_form">
                <div class="table table-striped" id="reglist">
                    <div class="table-header-group">
                        <div class="table-cell">Usuario</div>
                        <div class="table-cell">Perfil</div>
                        <div class="table-cell">PC</div>
                        <div class="table-cell">EMD</div>
                        <div class="table-cell">EMS</div>
                        <div class="table-cell">EMU</div>
                        <div class="table-cell">RMU</div>
                    </div>
                </div>
            </form>
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
    </div>
    <div class="row">
        <div class="col">
            <small>
                <p>PC = Permiso de chat</p>
                <p>EMD = Enviar mensajes de difusión</p>
                <p>EMS = Enviar mensajes a supervisor</p>
                <p>EMU = Enviar mensajes a otros usuarios</p>
                <p>RMU = Recibir mensajes de otros usuarios</p>
            </small>
        </div>
    </div>
</div>
