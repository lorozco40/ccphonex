<div class="container">
    <h1>Antes de continuar</h1>
    <h4>¡Hola <strong><?=$uname?></strong>, elige un rol por favor!</h4>
    <div class="row">
        <?php if (count($opts) == 0): ?>
            <div class="alert alert-danger" role="alert">
                No hay roles disponibles para ti, contacta al supervisor. y cuando esté listo 
                <a href="<?=base_url('inter')?>">recarga la página</a> y vuelve a intentar.
            </div>
        <?php else: ?>
            <div class="col-md-2">
                <form action="<?=base_url('inter/continuar')?>" method="post" class="form">
                    <!-- radio con los valores de opt -->
                    <?php $chkd = "checked"; foreach($opts as $opt): ?>
                        <input data-target="#t<?=$opt->id?>" class="form-check-input"
                            type="radio" name="tid" value="<?=$opt->id?>"
                            id="opt<?=$opt->id?>" <?=$chkd?> required>
                        <label for="opt<?=$opt->id?>"><?=$opt->eti?></label><br>
                    <?php $chkd = ""; endforeach; ?>
                    <button type="submit" class="btn btn-primary">Continuar</button>
                </form>
            </div>
            <div class="col-4">
                <div class="tab-content">
                    <?php $active = "active"; foreach($opts as $opt): ?>
                        <div class="tab-pane fade show <?=$active?>" id="t<?=$opt->id?>">
                            <h4 style="text-align:left;"><?=$opt->eti?></h4>
                            <p><?=$opt->des?></p>
                        </div>
                    <?php $active = ""; endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
