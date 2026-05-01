<div class="container">
    <div class="row">
        <h1>Agregar csv</h1>
        <h3>Para empezar agrega un archivo csv con la base de datos a completar.</h3>
        <ul>
            <li>El archivo deberá estar separado por comas.</li>
            <li>No debe contener comillas simples ni dobles en los valores, pero las puede tener como delimitadores de campo.</li>
            <li>Deberá tener un encabezado con el nombre de las columnas como quieres que se vean en el formulario.</li>
            <li>Deberá tener forzosamente una columna con nombre "Teléfono".</li>
            <li>Los datos deben estar homologados(tener el mismo formato) para ser reconocidos.</li>
            <li>El largo máximo de caracteres por campo es de 255.</li>
        </ul>
    </div>
    <div class="row">
        <?php echo form_open_multipart('welcome/config', 'class="form" role="form"'); ?>
            <input name="elcsv" type="file" />
            <input type="submit" name="Enviar" value="Enviar" />
        <?php echo form_close(); ?>
    </div>
