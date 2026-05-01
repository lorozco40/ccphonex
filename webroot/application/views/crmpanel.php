<style media="screen">
    #ticketModal {
        overflow-y: auto !important;
    }
    .form-group {
        width: 100%;
    }
    button.ageedit {
        display:none;
    }
    .input-group {
        width: 48%;
    }
    .input-group label {
        min-with: 98px;
    }
</style>
<div class="container main">
    <h2 class="text-left">Tickets asignados a mi</h2>
    <?php if(count($forms)<1): ?>
        <div class="row">
            <div class="col">
                <p>No tienes tickets asignados</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col">
                <div class="table table-striped">
                    <div class="table-header-group">
                        <div class="table-cell">CRM</div>
                        <div class="table-cell">Estatus</div>
                        <div class="table-cell">Ticket ID</div>
                        <div class="table-cell"></div>
                    </div>
                    <form class="table-row" id="busform">
                        <div class="table-cell">
                            <?php if(count($forms)>1): ?>
                                <select class="form-control" name="fid" id="fid">
                                    <?php foreach ($forms as $form): ?>
                                        <option data-cid="<?=$form->id_campaign?>" value="<?=$form->id?>"><?=$form->name?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="hidden" name="fid" id="fid" value="<?=$forms[0]->id?>" />
                                <?=$forms[0]->name?>
                            <?php endif; ?>
                        </div>
                        <div class="table-cell">
                            <select class="form-control" name="testatus" id="testatus">
                                <option value="0">Todos</option>
                                <option value="1">Abiertos</option>
                                <option value="2">Cerrados</option>
                            </select>
                        </div>
                        <div class="table-cell">
                            <input type="text" class="form-control" name="tid" id="tid" placeholder="Todos" />
                        </div>
                        <div class="table-cell"><button type="submit" class="btn btn-primary">Buscar</button></div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h4> </h4>
                <div class="table table-striped" id="tickets">
                    <div class="table-header-group">
                        <div class="table-cell">Ticket</div>
                        <div class="table-cell">Fecha</div>
                        <div class="table-cell">Estatus</div>
                        <div class="table-cell">Detalle</div>
                        <div class="table-cell">Acción</div>
                    </div>
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
    <?php endif; ?>
</div>

<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketModalLabel">Ticket <strong id="modalid"></strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pastilla" id="leform"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
