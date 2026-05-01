<div class="container main">
    <div class="row">
        <div class="col-sm-12 text-center">
            <h2 class="text-danger">Advertencia!</h2>
            <hr>
            <p>
                El siguiente botón creara un reset a la base de datos, eliminará todos los datos. por favor ¡ten CUIDADO!
            </p>
        </div>
        <div class="col-sm-12 text-center">
            <button class="btn btn-danger" type="button" onclick="hard_reset()">Limpiar Base de Datos</button>
        </div>
    </div>
    <br><br>
    <div class="row">
        <div class="col-sm-12">
            <div id="resultado"></div>
        </div>
        <div class="col-sm-12" id="error">
        </div>
    </div>
</div>