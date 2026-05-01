<div id="catalogos_com<?=$camp->id?>" class="mt-3">
    <button class="btn btn-primary toglecats mb-3" data-did="<?=$camp->id?>">Catálogos</button>
    <div id="catalogos<?=$camp->id?>" class="d-none">
        <div class="row mb-4">
            <div class="col-auto">
                <?=form_open("despachador/ver_cats", ["class"=>"form form-ajax"], ["did"=>$camp->id])?>
                    <label for="name">Grupos de campos</label>
                    <div class="input-group">
                        <select name="name" class="form-control">
                            <option value="">-- Elige --</option>
                            <?php if ($this->db->table_exists("disp_" . $camp->id . "_cats")) {
                                $cats = $this->db->query("SELECT `id`, `name`, `eti` FROM `disp_" . $camp->id . "_cats` ORDER BY `name`, `seq`, `eti`")->result();
                                $cate = "";
                                $padres = "<select name='parent' class='form-control'><option value='0'>Sin padre (primer nivel)</option>";
                                foreach ($cats as $cat) {
                                    $padres .= "<option value='$cat->id'>$cat->id - $cat->name - $cat->eti</option>";
                                    if ($cat->name != $cate) {
                                        echo "<option value='$cat->name'>$cat->name</option>";
                                        $cate = $cat->name;
                                    }
                                }
                                $padres .= "</select>";
                            } ?>
                        </select>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Ver entradas</button>
                        </div>
                    </div>
                <?=form_close()?>
            </div>
            <div class="col-2">
                <label for="name">Agregar entrada</label>
                <button type="button" class="btn btn-primary vermisec" data-sec="#secnewent">Ver formulario</button>
            </div>
        </div>
        <div class="row onlyone d-none" id="secnewent">
            <div class="col-auto">
                <?=form_open("despachador/add_cat", ["class"=>"form form-ajax"], ["did"=>$camp->id])?>
                    <div class='form-group'>
                        <label for='parent'>Padre</label>
                        <?=$padres?>
                    </div>
                    <div class='form-group'>
                        <label for='name'>Campo</label>
                        <input type="text" name="name" class="form-control" placeholder="Campo" />
                    </div>
                    <div class='form-group'>
                        <label for='name'>Etiqueta</label>
                        <input type="text" name="eti" class="form-control" placeholder="Etiqueta" />
                    </div>
                    <div class='form-group'>
                        <label for='name'>Valor</label>
                        <input type="text" name="val" class="form-control" placeholder="Valor" />
                    </div>
                    <div class='form-group'>
                        <label for='name'>Orden</label>
                        <input type="text" name="seq" class="form-control" placeholder="Orden" value="0" />
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                <?=form_close()?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div id="catvals<?=$camp->id?>"></div>
            </div>
        </div>
    </div>
</div>
