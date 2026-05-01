<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h1><?php echo $cual; ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="table table-striped">
                <div class="table-header-group">
                    <?php foreach($data[0] as $key => $val): ?>
                        <div class="table-cell"><?php echo $key; ?></div>
                    <?php endforeach; ?>
                </div>
                <?php foreach($data as $key => $row): ?>
                    <div class="table-row">
                        <?php foreach($row as $keyy => $roww): ?>
                            <div class="table-cell">
                                <?php if(is_array($roww)): ?>
                                    <?php foreach ($roww as $val) {
                                        echo $val."<br>";
                                    } ?>
                                <?php else: ?>
                                    <?php echo $roww; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
