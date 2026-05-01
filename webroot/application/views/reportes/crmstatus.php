<div class="container main">
    <div class="row justify-content-between">
        <div class="col-auto">
            <h1><?php echo $title; ?></h1>
        </div>
        <div class="col-auto">
            <button class="logos" name="pdf" value="pdf" id="pdfbtn"><img src="<?php echo site_url('assets/img/pdf4.png'); ?>" alt="PDF"></button>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-auto">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">CRM</span>
                </div>
                <select class="form-control" name="crm">
                    <?php foreach ($forms as $valor): ?>
                        <option value='<?php echo $valor->id; ?>'><?php echo $valor->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-lg mb-3" id="graf"></div>
        <div class="col-lg-4" id="repo"></div>
    </div>    
    <div class="row">
        <div class="col-lg mb-3" id="graf2"></div>
        <div class="col-lg-4" id="repo2"></div>
    </div><br />
</div>
