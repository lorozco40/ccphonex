<style>
    #opciones .btn {
        text-transform: none;
        text-align: left;
    }
</style>
<div class="container main">
    <div class="container">
        <div class="row">
            <div class="col-auto">
                <h2>Campos dependientes <button class="btn btn-primary" id="nuevo">Nuevo</button></h2>
            </div>
        </div>
        <hr>
        <div class="row">
            <form id="formCamposDependientes" action="<?=site_url('camposopciones/eliminar')?>" class="form" method="post">
                <div class="col-12">
                <?php if(count($campanas) > 1): ?>
                    <div class='form-group'>
                        <label for='user'>Campaña</label>
                        <select id="selectCampanaCampos" name="campanas" class="form-control" required>
                            <option value="">-Todas-</option>
                            <?php foreach($campanas as $key=> $item): ?>
                                <option value="<?=$item->id;?>"><?=$item->name;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                </div>
                <div class="col-12">
                    <div id="addform" class="row">
                        <div class="form-group col">
                            <label for="cam">Campo (sin espacios)</label>
                            <input type="text" class="form-control validar" name="cam" maxlength="15" required />
                        </div>
                        <div class="form-group col">
                            <label for="lvl1">Opción de nivel 1</label>
                            <input type="text" class="form-control lvls validar" name="lvl1" maxlength="100" />
                        </div>
                        <div class="form-group col">
                            <label for="lvl2">Opción de nivel 2</label>
                            <input type="text" class="form-control lvls validar" name="lvl2" maxlength="100" />
                        </div>
                        <div class="form-group col">
                            <label for="lvl3">Opción de nivel 3</label>
                            <input type="text" class="form-control lvls validar" name="lvl3" maxlength="100" />
                        </div>
                        <div class="form-group col">
                            <label for="lvl4">Opción de nivel 4</label>
                            <input type="text" class="form-control lvls validar" name="lvl4" maxlength="100" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-auto">
                            <button id="addbtn" type="button" class="btn btn-primary">Agregar</button>
                        </div>
                        <div class="col-auto">
                            <button id="delbtn" type="button" class="btn btn-secondary">Eliminar</button>
                            <input type="hidden" name="delbtn" value="Eliminar">
                        </div>
                        <div class="col-auto">
                            <button id="actbtn" type="button" class="btn btn-info">Actualizar Campaña</button>
                            <input type="hidden" name="actbtn" value="Actualizar">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col">
                <hr>
            </div>
        </div>
        <div class="row" id="opciones">
        	<div class="col list-group" id="camp"><?=$cds['cams']?></div>
            <div class="col list-group" id="lvl1"><?=$cds['l1']?></div>
            <div class="col list-group" id="lvl2"><?=$cds['l2']?></div>
            <div class="col list-group" id="lvl3"><?=$cds['l3']?></div>
            <div class="col list-group" id="lvl4"><?=$cds['l4']?></div>
        </div>
    </div>
</div>
