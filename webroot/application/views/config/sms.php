<div class="container main">
    <div class="row">
        <div class="col-md-1">
            <h2>Campañas&nbsp;SMS</h2>
        </div>
    </div>
    <hr>
    <div class="row">
        <ul>
            <li>El CSV debera ser separado por comas y contener las columnas telefono (10 números, obligatorio),
                nombre, dato, saludo, mensaje, cierre.</li>
            <li>Si la columna está vacía se usarán los valores default del formulario para completar el registro.</li>
            <li>El tamaño total del mensaje debera ser de 250 caracteres o menos, incluyendo nombre y dato.</li>
            <li>El nombre y el dato se agregarán solo en caso de existir en el CSV, de lo contrario el saludo,
                el mensaje y el cierre consecutivos serán el mensaje.</li>
            <li>Si el nombre de la campaña se repite, se agregaran los registros a la existente.</li>
        </ul>
    </div>
    <div class="row">
        <div class="col">
            <a title="Click para descargar" class="btn btn-warning" href="<?php echo site_url('files/formatoCampSMS.csv'); ?>" download>Descargar formato</a>
        </div>
    </div><br />
    <div class="container pastilla">
        <div class="row">
            <div class="col-sm-6">
                <h5>Valores default</h5>
                <?php echo form_open_multipart('sms/crear_camp', array('role'=>'form', 'class'=>'form', 'id'=>'addform')); ?>
                <input class="form-control" type="text" name="camp" placeholder="Nombre de campaña" required />
                <input class="form-control" type="text" name="saludo" id="saludo" placeholder="Saludo" />
                <input class="form-control" type="text" name="msg" id="msg" placeholder="Mensaje" required />
                <input class="form-control" type="text" name="cierre" id="cierre" placeholder="Cierre" /><br />
                <input class="btn btn-info" type="file" name="csv" required />
                <input class="btn btn-info" type="submit" value="Crear" />
                <?php echo form_close(); ?>
            </div>
            <div class="col-sm-6">
                <h5>Preview</h5>
                <div id="smspreview"></div>
            </div>
        </div>
    </div>
    <hr>
    <div class="container pastilla">
        <div class="row">
            <div class="col-md-1">
                <h3>Campañas</h3>
            </div>
        </div>
        <hr>
        <div class="table table-striped">
            <div class="table-header-group">
                <div class="table-cell">Campaña</div>
                <div class="table-cell">Registros</div>
                <div class="table-cell">Enviados</div>
                <div class="table-cell">Por enviar</div>
                <div class="table-cell">Mensaje enviado</div>
                <div class="table-cell">Envio exitoso</div>
                <div class="table-cell">Destino invalido</div>
                <div class="table-cell">Error</div>
                <div class="table-cell">Error interno</div>
                <div class="table-cell">Acción</div>
            </div>
            <?php foreach ($data as $fila): ?>
                <div class="table-row">
                    <div class="table-cell"><?php echo $fila->camp; ?></div>
                    <div class="table-cell"><?php echo $fila->regs; ?></div>
                    <div class="table-cell"><?php echo $fila->sent; ?></div>
                    <div class="table-cell"><?php echo $fila->tose; ?></div>
                    <div class="table-cell"><?php echo $fila->menExi; ?></div>
                    <div class="table-cell"><?php echo $fila->senExi; ?></div>
                    <div class="table-cell"><?php echo $fila->desInv; ?></div>
                    <div class="table-cell"><?php echo $fila->otroError; ?></div>
                    <div class="table-cell"><?php echo $fila->errorInt; ?></div>
                    <div class="table-cell"><?php if($fila->tose >= 1) {
                        echo form_open('sms/camp_enviar', array('class'=>'startform'), array('camp' => $fila->camp)); ?>
                        <button class="btn btn-info" type="submit">Enviar</button>
                        <?php echo form_close(); } ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div><br />
        <div class="row">
            <div class="col-md-12 text-center">
                <nav aria-label="Page navigation">
                    <?php echo $pagination; ?>
                </nav>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo site_url('js/sms.js?v='.time()); ?>"></script>
