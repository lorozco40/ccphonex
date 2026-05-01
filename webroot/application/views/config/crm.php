<div class="container main">
    <div class="row">
        <div class="col-auto">
            <h3 class="nowrap">Formularios CRM</h3>
        </div>
    </div>
    <hr>
    <div class="form-inline">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Campaña</span>
            </div>
            <select class="form-control" name="campana" id="campana">
                <?= options_select_campaign($campanas, '') ?>
            </select>
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Formulario</span>
            </div>
            <select class="form-control" name="form" id="form">
                <?php foreach ($forms as $key => $value): ?>
                    <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">TicketID</span>
            </div>
            <input class="form-control" name="tid" id="tid" />
        </div>
        <div class="form-group input-daterange">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                </div>
                <input type="text" id="min" name="min" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
                <div class="input-group-prepend">
                    <span class="input-group-text">A</span>
                </div>
                <input type="text" id="max" name="max" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
            </div>
        </div>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">Estatus</span>
          </div>
          <select class="form-control" name="status" id="estatus">
              <option value="">Todos</option>
              <option value='En proceso'>En proceso</option>
              <option value='Pausado'>Pausado</option>
              <option value='Abierto'>Abierto</option>
              <option value='Cerrado'>Cerrado</option>
          </select>
        </div>
    </div>
    <hr>
    <div class="table table-crm table-striped">
        <div class="table-header-group">
            <div class="table-cell">TicketID</div>
            <div class="table-cell">Detalle</div>
            <div class="table-cell">Cliente</div>
            <div class="table-cell">Asignación</div>
            <div class="table-cell">Apertura</div>
            <div class="table-cell">Cierre</div>
            <div class="table-cell">Status</div>
            <div class="table-cell" colspan="2">Acción</div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="text-center">
            <nav aria-label="Page navigation" id="pagination"></nav>
        </div>
    </div>
</div>
<div class="modal fade" id="reabrirmodal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="reabrirform">
                <div class="modal-header">
                    <h5 class="modal-title">¿Porque reabrir el ticket <strong class="tid"></strong>?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col">
                        <textarea name="razon" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Reabrir</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="transferirmodal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="transferirform">
                <div class="modal-header">
                    <h5 class="modal-title">¿A quien transferir el ticket <strong class="tid"></strong>?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col">
                        <select class="form-control" name="agente">
                            <?php foreach ($agentes as $key => $value): ?>
                                <option value="<?php echo $value->id; ?>"><?php echo $value->name.' '.$value->last; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Transferir</button>
                </div>
            </form>
        </div>
    </div>
</div>
