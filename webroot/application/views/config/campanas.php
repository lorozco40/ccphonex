<div class="container main">
    <div class="row">
        <div class="col-auto">
            <h2>
                Campañas
                <?php if($agente['perfil']=='admin'): ?>
                    <button type="button" class="btn btn-primary" id="nuev">Nueva</button>
                <?php endif; ?>
            </h2>
        </div>
        <div class="col"></div>
        <div class="col-auto">
            <form class="form" method="post" id="busform">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Mi buena campaña"/>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-info">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <hr>
    <div class="table table-striped" id="reglist">
        <div class="table-header-group">
            <div class="table-cell">Activa</div>
            <div class="table-cell">Nombre</div>
            <div class="table-cell">DID's</div>
            <div class="table-cell">Script</div>
            <div class="table-cell text-center">Acción</div>
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

<!-- /////////////////////////// Modal de horarios ///////////////////////////////////////////////////////////// -->

<div class="modal fade" id="ModalHorario" tabindex="-1" role="dialog" aria-labelledby="ModalHorarioTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <? //exit( json_encode($data) ); ?>
                <h3 class="modal-title" id="ModalHorarioTitle">Horario por campaña - <span class="nombre-campana"></span> -</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo site_url('campanas/act_horario');?>" method="post" id='horariosform'>
                <input type="hidden" name="camp_id" id="camp_id" />
                <div class="modal-body">
                    <div class="table table-striped">
                        <div class="table-header-group">
                            <div class="table-cell">Dia</div>
                            <div class="table-cell">Inicio</div>
                            <div class="table-cell">Fin</div>
                        </div>
                        <?php $dias = array("nada", "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"); for($i=1; $i<8; $i++): ?>
                            <?php $thisin = ($i==1 || $i==7) ? "" : ""; $thisout = ($i==1 || $i==7) ? "" : ""; ?>
                            <div class="table-row">
                                <div class="table-cell"><input type='text' class='form-control' name="dia" value='<?php echo $dias[$i]; ?>' readonly /></div>
                                <div class="table-cell"><input type='text' class='form-control' name='d<?php echo $i; ?>in'
                                    id='d<?php echo $i; ?>in' value='<?php echo $thisin; ?>' /></div>
                                <div class="table-cell"><input type='text' class='form-control' name='d<?php echo $i; ?>out'
                                    id='d<?php echo $i; ?>out' value='<?php echo $thisout; ?>' /></div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Actualizar</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if($agente['perfil']=='admin'): ?>

<!-- /////////////////////////// Modal de atributos //////////////////////////////////////////////////////////// -->

<div class="modal fade" id="ModalAtributos" tabindex="-1" role="dialog" aria-labelledby="ModalAtributosTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="ModalAtributosTitle">Atributos de campaña - <span class="nombre-campana"></span> -</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Formulario para agregar atributos -->
            <div class="modal-body">
                <form action="<?php echo site_url('campanas/atriagregar');?>" method="post" id='addatriform'>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="atributo">Nuevo</label>
                                    </div>
                                    <select class="custom-select" type="text" name="atributo" id="atributo">
                                        <option value="">-Seleccione-</option>
                                        <?php foreach ( $atributos as $key => $value): ?>
                                            <option value="<?= $key ?>"><?= $value ?></option>
                                        <?php endforeach;?>
                                    </select>
                                    <input class="form-control" type="text" name="valor" id="valor" placeholder="Valor" data-toggle="tooltip" data-placement="top" onkeyup="validarNumerico(event)"/>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary agrega-atributo" type="button">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- Fromulario para agregar atributos -->

                <br/>

                <!-- Lista de atributos dinamicos -->
                <form action="#" method="post" id='atributos_dinamicos_form'>
                    <div class="modal-body" id="atributos_campana_content"></div>
                    <br/>
                    <?php if($agente['perfil']=='admin'): ?>
                            <div class="row">
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recaltarifa" id="recaltarifa" value='ok'>
                                        <label class="form-check-label" for="recaltarifa">Recalcular costo de todas las llamadas anteriores con éstas tarifas</label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                </form>
            </div>
            <!-- Lista de atributos dinamicos -->

            <!--form action="" method="post" id='atriform'-->
                <input type="hidden" name="id" id="atrid" />
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="atributos_guardar()">Guardar</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
                </div>
            <!--/form-->
        </div>
    </div>
</div>

<?php endif; ?>

<!-- /////////////////////////// Modal de campañas ///////////////////////////////////////////////////////////// -->

<div class="modal fade" id="regformModal" tabindex="-1" role="dialog" aria-labelledby="regformModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open('campanas/guardar', ['class'=>'form', 'id'=>'regform'], ['id'=>0]); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="regformModalTitle">Campaña</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="name">Nombre</label>
                                </div>
                                <input class="form-control" type="text" name="name" placeholder="Nombre campaña" />
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <label for="script">Script</label>
                                <textarea name="script" rows="3" cols="80"></textarea>
                            </div>
                        </div>
                    </div>
                    <br>
                    <?php if($agente['perfil']=='admin'): ?>
                        <div class="row">
                            <div class="col">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="dids">DID's</label>
                                    </div>
                                    <input class="form-control" type="text" name="dids" placeholder="1234,5678" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="active" checked='checked' />
                                    <label class="form-check-label" for="active">Activa</label>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="active" value='0' />
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
