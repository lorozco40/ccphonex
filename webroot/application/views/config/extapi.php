<div class="container main">
    <div class="row">
        <div class="col-auto">
            <h2>
                <?=$title?>
                <button type="button" class="btn btn-primary" id="nuextapi"
                    data-toggle="modal" data-target="#extApiModal">Nueva</button>
            </h2>
        </div>
        <div class="col"></div>
    </div>
    <div class="row">
        <div class="col"></div>
        <div class="col-auto">
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">Campaña</div>
                </div>
                <select class="form-control" name="campanas" id="campanas">
                    <option value="">-- Todas --</option>
                    <?php
                        $show = true;
                        foreach ($campanas as $camp) {
                            if ( $camp->active == 0 && $show ) {
                                $show = false;
                                echo "<option disabled>──── Inactivas ────</option>";
                            }
                            echo "<option value='".$camp->id."'>".$camp->name."</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <hr>
    <div class="table-responsive-xl">
    <table class="table table-striped table-sm">
        <tr class="table-header-group">
            <td class="table-cell">Campaña</td>
            <td class="table-cell">API</td>
            <td class="table-cell">URL</td>
            <td class="table-cell">Activa</td>
            <td class="table-cell"></td>
        </tr>
        <tbody id="liextapi"></tbody>
    </table>
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
<div class="modal fade" id="extApiModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open('extapi/guardar', ['class'=>'form', 'id'=>'formextapi'], ['id'=>0]); ?>
                <div class="modal-header">
                    <h5 class="modal-title">API</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col">
                            <label for="campana">Campaña</label>
                            <select class="form-control" name="campana">
                                <option value="" selected>-- Todas !Cuidado --</option>
                                <?php
                                    $show = true;
                                    foreach ($campanas as $camp) {
                                        if ( $camp->active == 0 && $show ) {
                                            $show = false;
                                            echo "<option disabled>──── Inactivas ────</option>";
                                        }
                                        echo "<option value='".$camp->id."'>".$camp->name."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col">
                            <label for="name">API</label>
                            <input class="form-control" type="text" name="name" placeholder="nombre de la API" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label for="url">URL</label>
                            <input class="form-control" type="text" name="url" placeholder="URL básica" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label for="user">Usuario</label>
                            <input class="form-control" name="user" />
                        </div>
                        <div class="col">
                            <label for="pass">Contraseña</label>
                            <input type="password" class="form-control" name="pass" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label class="form-check-label" for="sign">Nombre de token</label>
                            <input class="form-control" name="sign" />
                        </div>
                        <div class="col">
                            <label class="form-check-label" for="logloc">Ubicación de token</label>
                            <select class="form-control" name="logloc">
                                <option value="0" selected>Headers</option>
                                <option value="1">Body</option>
                                <option value="2">Auth</option>
                                <option value="3">Parámetro URL</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label for="token">Token</label>
                            <textarea class="form-control" name="token"></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label for="xhash">Extra Hash</label>
                            <textarea class="form-control" name="xhash"></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label for="methods">Información extra, especifícaciones, etc.</label>
                            <textarea class="form-control" name="info"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="valid_crt" checked>
                                <label class="form-check-label" for="valid_crt">Certificado válido</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="active" checked>
                                <label class="form-check-label" for="active">Activa</label>
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
<div class="modal fade" id="extApiMetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Métodos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open('#', ['class'=>'form', 'id'=>'form_extapi_met']); ?>
                    <input class="form-control" type="hidden" name="id" placeholder="id" />
                    <input class="form-control" type="hidden" name="id_extapi" placeholder="id_extapi" />
                    <div class="form-row">
                        <div class="col col-12 col-sm-12 col-lg-4">
                            <label for="api">Api</label>
                            <input class="form-control" type="text" name="api" disabled placeholder="Api" />
                        </div>
                        <div class="col col-12 col-sm-6 col-lg-4">
                            <label for="prot">Prot</label>
                            <select class="form-control" name="prot">
                                <option value="GET" selected>GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                            </select>
                        </div>
                        <div class="col col-12 col-sm-6 col-lg-4">
                            <label for="met">Método</label>
                            <input class="form-control" type="text" name="met" placeholder="metodo" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col col-12 col-sm-4 col-lg-3">
                            <label for="xtype">X-Type</label>
                            <input class="form-control" type="text" name="xtype" placeholder="x-type" />
                        </div>
                        <div class="col col-12 col-sm-8 col-lg-9">
                            <label for="info">Información</label>
                            <input class="form-control" type="text" name="info" placeholder="información" />
                        </div>
                    </div>
                    <div class="form-row mt-2">
                        <div class="col justify-content-end d-inline-flex ">
                            <button type="submit" name="Agregar" class="btn btn-primary d-none mx-2">Agregar</button>
                            <button type="submit" name="Actualizar" class="btn btn-primary d-none mx-2">Actualizar</button>
                            <button type="button" class="btn btn-secondary" onclick="extapi.reset_form_met(0)">Cancelar</button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
                <hr/>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Prot</th>
                            <th>Met</th>
                            <th>Xtype</th>
                            <th>Info</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="lista_extapi_met">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="extApiFieldsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Campos<span id="api_selected"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <?php echo form_open('#', ['class'=>'form', 'id'=>'form_extapi_fields']); ?>
                <input class="form-control" type="hidden" name="id_extapi_met" placeholder="id_extapi_met" />
                <input class="form-control" type="hidden" name="id" placeholder="id" />
                <div class="form-row">
                    <div class="col">
                        <label for="api">Api External</label>
                        <input class="form-control" type="text" name="api" disabled placeholder="api" />
                    </div>
                    <div class="col">
                        <label for="met">Método</label>
                        <input class="form-control" type="text" name="met" disabled placeholder="met" />
                    </div>
                </div>
                <div class="form-row">
                    <div class="col col-12 col-sm-4 col-lg-3">
                        <label for="field">Campo</label>
                        <input class="form-control" type="text" name="field" placeholder="campo" />
                    </div>
                    <div class="col col-12 col-sm-3 col-lg-3">
                        <label for="ftype">Tipo</label>
                        <select class="form-control" name="ftype">
                            <option value="">-Seleccione-</option>
                            <option value="int">Int</option>
                            <option value="string">String</option>
                            <option value="bool">Bool</option>
                        </select>
                    </div>
                    <div class="col col-12 col-sm-5 col-lg-6">
                        <label for="descript">Descripción</label>
                        <input class="form-control" type="text" name="descript" placeholder="descripción" />
                    </div>
                    <div class="col col-12">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" name="dir">
                            <label class="form-check-label" for="dir">Dir</label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" name="req">
                            <label class="form-check-label" for="req">Req</label>
                        </div>
                    </div>
                </div>
                <div class="form-row mt-2">
                    <div class="col justify-content-end d-inline-flex ">
                        <button type="submit" name="Agregar" class="btn btn-primary d-none mx-2">Agregar</button>
                        <button type="submit" name="Actualizar" class="btn btn-primary d-none mx-2">Actualizar</button>
                        <button type="button" class="btn btn-secondary" onclick="extapi.reset_form_fields(0)">Cancelar</button>
                    </div>
                </div>
                <hr/>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Tipo</th>
                            <th>Dir</th>
                            <th>Req</th>
                            <th>Descripción</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="lista_extapi_fields">
                    </tbody>
                </table>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>