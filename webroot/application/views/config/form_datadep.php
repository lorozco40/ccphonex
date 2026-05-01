<div class="container main">
    <div class="row">
        <div class="col-auto">
            <h2>Catálogos (<span class="text-info" id="text_form_name">--</span>)</h2>
        </div>
    </div>
    <form id="form_dataDepFilter" class="row">
        <div class="col-sm-12 col-md-4">
            <label for="">Catálogo</label>
            <select name="slug_key" class="form-control" onchange="dataDep.change_filter('slug_key')">
                <option value="">-Seleccione-</option>
            </select>
        </div>
        <div class="col-sm-12 col-md-8">
            <input type="hidden" name="id_form">
            <input type="hidden" name="id_campaign">
        </div>
    </form>
    <hr/>
    <div id="add_and_search" class="row mb-2" style="display: none;">
        <div class="col-sm-12 col-md-4">
            <button type="button" class="btn btn-primary" onclick="dataDep.add()">
                Nuevo
            </button>
        </div>
        <div class="col-sm-12 col-md-4 offset-md-4">
            <form class="form" method="post" id="form_bus">
                <div class="input-group">
                    <input type="search" class="form-control" id="buscar" placeholder="Utilice la primer columna como busqueda">
                    <div class="input-group-append">
                        <button class="btn btn-info" id="btnsearch" type="submit">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row scroll-x">
        <div id="dataDep_table" class="table table-striped">
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
<div class="modal fade pastilla" id="dataDep-modal" tabindex="-1" role="dialog" aria-labelledby="agenda-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form_dataDep">
                <div class="modal-header">
                    <h4 class="modal-title" id="agenda-modalLabel">Carga de agenda por archivo</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="dataDep-body-modal" class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardadoMasivo()">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

