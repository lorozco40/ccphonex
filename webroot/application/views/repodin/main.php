<div>
    <h1><?=$title?></h1>
</div>
<div class="container">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ediaddModal" id="newrd">Nuevo</button>
</div>
<div class="container">
    <div id="contentList" class="table table-striped"></div>
</div>
<div id="ediaddModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="addform" method="post" action="<?=site_url('repodin/guardar')?>">
            <div class="modal-body">
                <input type="hidden" name="id" value="0">
                <label for="nomrepo">Nombre del reporte</label>
                <input type="text" name="nomrepo" class="form-control" />
                <?php echo selectCats($forms, ['cat'=>'cam','name'=>'form','label'=>'Formulario']); ?>
                <label for="filtro">Campo índice</label>
                <select name="filtro" class="form-control"></select>
                <label for="paraver">Campos a agregar</label>
                <select name="paraver" class="form-control" multiple></select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Crear</button>
            </div>
        </form>
    </div>
</div>
<style>
    label {
        margin-top: 5px;
        margin-bottom: 0;
    }
</style>
