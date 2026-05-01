<div class="row">
    <div class="col">
        <table class="table onlyone" id="tablacats">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Padre</th>
                    <th>Campo</th>
                    <th>Etiqueta</th>
                    <th>Valor</th>
                    <th>Orden</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $forms = ""; foreach ($cats as $cat) {
                    $forms .= "<div class='catdetail$cat->id onlyone d-none col-3' id='catformedit$cat->id'>";
                    $forms .= form_open("despachador/edit_cat", ["class"=>"form form-ajax"], ["did"=>$did, "cid"=>$cat->id]);
                    $forms .= "  <div class='form-group'><label for='id'>ID</label><input class='form-control' type='text' name='id' value='$cat->id' readonly /></div>";
                    $forms .= "  <div class='form-group'><label for='parent'>Padre</label><input class='form-control' type='text' name='parent' value='$cat->parent' /></div>";
                    $forms .= "  <div class='form-group'><label for='name'>Campo</label><input class='form-control' type='text' name='name' value='$cat->name' /></div>";
                    $forms .= "  <div class='form-group'><label for='eti'>Eti</label><input class='form-control' type='text' name='eti' value='$cat->eti' /></div>";
                    $forms .= "  <div class='form-group'><label for='val'>Val</label><input class='form-control' type='text' name='val' value='$cat->val' /></div>";
                    $forms .= "  <div class='form-group'><label for='seq'>Orden</label><input class='form-control' type='text' name='seq' value='$cat->seq' /></div>";
                    $forms .= "  <div class='d-flex'>";
                    $forms .= "    <button type='submit' class='btn btn-primary mr-3' data-class='catdetail$cat->id'>Guardar</button>";
                    $forms .= "    <button type='button' class='btn btn-info togleditcat' data-class='catdetail$cat->id'>Cancelar</button>";
                    $forms .= "  </div>";
                    $forms .= form_close();
                    $forms .= "</div>";
                    echo "<tr class='catdetail$cat->id'>";
                    echo "  <td>$cat->id</td>";
                    echo "  <td>$cat->parent</td>";
                    echo "  <td>$cat->name</td>";
                    echo "  <td>$cat->eti</td>";
                    echo "  <td>$cat->val</td>";
                    echo "  <td>$cat->seq</td>";
                    echo "  <td class='d-flex'>";
                    echo "    <button type='button' class='btn btn-primary togleditcat mr-3' data-cid='$cat->id'>Editar</button>";
                    echo "    <a href='" . base_url("despachador/del_cat?did=$did&cid=$cat->id") . "' class='btn btn-danger link-ajax' data-did='$did'>Eliminar</a>";
                    echo "  </td>";
                    echo "</tr>";
                } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <?=$forms?>
</div>
