<div class="container">
    <div class="row">
        <h4>Posibilidades encontradas, click para editar</h4>
    </div>
    <div class="row">
        <table class="table">
            <?php foreach ($filas as $fila): ?>
                <tr>
                    <td><a href="<?php echo site_url('captura/').$fila->id; ?>"><?php echo $fila->telefono; ?></a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
