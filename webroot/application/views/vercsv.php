<table class="table">
    <thead>
        <tr>
            <?php foreach($titles as $tit): ?>
                <th><?php echo $tit; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data as $row): ?>
            <tr>
                <?php foreach($row as $field): ?>
                    <td><?php echo $field; ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
