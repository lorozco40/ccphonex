<?php foreach($data as $key => $val): ?>
    <option value="<?php echo $key; ?>" <?php if ($city==$key) echo "Selected='selected'"; ?>><?php echo $val; ?></option>
<?php endforeach; ?>
