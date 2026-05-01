<div class="container">
    <div class="row">
        <div class="col">
            <form method="post" action="<?=site_url('facerecon/confirma')?>" class="form">
                <input type="hidden" name="sid" value="<?=$id?>">
                <label for="wara">Dime wara</label>
                <input type="text" class="form-control" name="wara">
                <br>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        </div>
    </div>
</div>
