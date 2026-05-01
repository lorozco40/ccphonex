<div class="container main">
    <div class="row justify-content-between">
        <div class="col-auto">
            <h2>
                Whatsapp Encuestas
                <br />
                <small><small><?= "($wac->id)" . $wac->nombre . " " . $wac->cuenta ?></small></small>
            </h2>
        </div>
        <div class="col-auto">
            <input type="hidden" name="wid" value="<?= $wac->id ?>" />
            <button type="button" class="btn btn-primary" data-toggle="modal"
                data-target="#warateFormModal">Nueva</button>
        </div>
    </div>
    <hr>
    <div class="table table-striped" id="libot">
        <div class="table-header-group">
            <div class="table-cell text-center">Activa</div>
            <div class="table-cell">Nombre</div>
            <div class="table-cell">Comentario</div>
            <div class="table-cell">Acción</div>
        </div>
        <?php foreach ($data as $row): ?>
            <form method="post" class="table-row" action="<?=site_url('warate/save')?>">
                <input type="hidden" name="id" value="<?=$row->id?>" />
                <input type="hidden" name="wid" value="<?=$row->id_wacta?>" />
                <div class="table-cell">
                    <input type="checkbox" name="active" class="form-control mb-n3"
                    <?php if ($row->active) echo "checked "; ?>/>
                </div>
                <div class="table-cell">
                    <input type="text" name="name"  class="form-control" value="<?=$row->name?>" />
                </div>
                <div class="table-cell">
                    <textarea rows="1" name="comment" class="form-control"><?=$row->comment?></textarea>
                </div>
                <div class="table-cell">
                    <button type="submit" data-toggle="tooltip" data-title="Guardar"
                        class="btn btn-primary mx-3"><li class="fa fa-save"></li></button>
                    <button type="button" class="btn btn-secondary relis" data-toggle="modal"
                        data-target="#warareModal" data-id="<?=$row->id?>">Reactivos</button>
                </div>
            </form>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="warateFormModal" tabindex="-1" role="dialog" aria-labelledby="warateFormModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('warate/save', ['class' => 'form'], ['id' => 0, 'wid' => $wac->id]); ?>
            <div class="modal-header">
                <h5 class="modal-title">Encuesta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <label for="email">Nombre</label>
                        <input class="form-control" type="text" name="name" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="intro">Comentario</label>
                        <textarea class="form-control" name="comment"></textarea>
                    </div>
                </div>
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="warareModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reactivos encuesta <strong id="eid"></strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <div class="alert alert-danger" role="alert">
                            Cuidado! Cualquier cambio que hagas afecta directo en
                            la tabla de guardado.
                        </div>
                    </div>
                </div>
                <?php echo form_open('warate/saverctv', ['class' => 'form', 'id' => 'nurctv'], ['id' => 0, 'rid' => 0]); ?>
                <div class="row">
                    <div class="col-auto">
                        <label for="email">Tipo</label>
                        <select name="tipo" class="form-control">
                            <option value="1" selected>Numérico</option>
                            <option value="2">Texto</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="intro">Reporte</label>
                        <input type='checkbox' name='reporte' class='form-control mb-n3' />
                    </div>
                    <div class="col">
                        <label for="intro">Reactivo</label>
                        <textarea class="form-control" name="reactivo"></textarea>
                    </div>
                </div>
                <div class="row text-center my-3">
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </div>
                <?php echo form_close(); ?>
                <hr />
                <div class="table" id="tbrctv">
                    <div class="table-header-group">
                        <div class="table-cell">Tipo</div>
                        <div class="table-cell text-center">Reporte</div>
                        <div class="table-cell">Reactivo</div>
                        <div class="table-cell">Acción</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
