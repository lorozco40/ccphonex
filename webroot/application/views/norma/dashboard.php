<div class="container" id="dashboard">
    <div class="row">
        <div class="col-11">
            <h1>Nom-035 estadísticas</h1>
        </div>
        <div class="col-1">
            <button title="Exportar a PDF" class="logos" name="pdf" value="pdf" id="pdfchart"><img src="assets/img/pdf5.png"></button>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form name="filtrar" method="post">
                <div class="form-group input-daterange">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="text" id="min" name="min" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
                        <div class="input-group-prepend">
                            <span class="input-group-text">A</span>
                        </div>
                        <input type="text" id="max" name="max" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Campaña</span>
                        </div>
                        <select id="campanas" class="campanas form-control" name="campanas">
                            <option value='<?=$campaigns_id?>'>Todas ...</option>
                            <?= options_select_campaign($campaigns) ?>
                        </select>
                    </div>
                </div> &nbsp;

            </form>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="pastilla">
                <div class="row">
                    <div class="col-md-12"><span id="area-example"></span></div>
                </div>
                <div class="">
                    Total: <span id="area-example_total"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="pastilla">
                <div class="row">
                    <div class="col-md-12"><span id="area-week"></span></div>
                </div>
                <div class="">
                    Total: <span id="area-week_total"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="//www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="<?php echo site_url('js/jspdf.min.js?v='.time()); ?>" charset="utf-8"></script>
<script src="<?php echo site_url('js/norma/dashboard.js?v='.time()); ?>" charset="utf-8"></script>
