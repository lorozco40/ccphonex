<?php echo $header; ?>
<?php echo $nav; ?>
<a href="#" data-target="#sidebar" data-toggle="collapse" id="btncolapse"><img src="<?php echo site_url('assets/img/ico_phone.png'); ?>" alt="phone"></a>
<div id="two_col" class="container-fluid">
    <div class="row">
        <div class="col collapse width show" id="sidebar">
            <?php echo $colleft; ?>
        </div>
        <main class="col-sm">
            <?php echo $colmain; ?>
        </main>
    </div>
</div>
<?php echo $footer; ?>
