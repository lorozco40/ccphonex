<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="es"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="es"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="es"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="es"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Cache-control" content="no-cache">
    <title>Assertive<?php echo (isset($title)) ? ' - '.$title : ''; ?></title>
    <meta name="description" content="Assertive contact center">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="<?php echo site_url('assets/img/logoassertive.jpg'); ?>">
    <link rel="icon" href="<?php echo site_url('favicon.ico'); ?>">

    <link rel="stylesheet" href="<?php echo site_url('css/bootstrap.min.css'); ?>" >
    <link rel="stylesheet" href="<?php echo site_url('css/all.css'); ?>" >
    <link rel="stylesheet" href="<?php echo site_url('css/jquery-ui.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo site_url('css/main.css?v='.time()); ?>">
    <?php if(!empty($agente['permiso']['NOM-035'])) $css = 'nom035'; ?>
    <link rel="stylesheet" href="<?php echo site_url('css/'.$css.'.css?v='.time()); ?>">

    <script src="<?php echo site_url('js/modernizr.min.js'); ?>"></script>
    <script src="<?php echo site_url('js/jquery.min.js'); ?>"></script>
    <?php if(!empty($agente)): ?>
        <script type="text/javascript">
            const site_url = '<?php echo site_url(); ?>';
            const REGS_POR_PAG = <?php echo (int)REGS_POR_PAG; ?>;
            <?php $uid = $this->session->userdata("uid"); ?>
            const uid = '<?php echo (!empty($uid)) ? $uid : 0; ?>';
            <?php if(isset($bago_url)): ?>
                var bago_url = '<?=$bago_url?>';
            <?php else: ?>
                var bago_url = 'localhost';
            <?php endif; ?>
            <?php if(isset($agente)): ?>
                var agente = JSON.parse('<?= json_encode($agente) ?>');
            <?php else: ?>
                var agente = {};
            <?php endif; ?>
            agente.estado = 'disponible';
        </script>
    <?php endif; ?>
</head>
<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">Estás usando un navegador <strong>Descontinuado</strong>. Por favor <a href="http://browsehappy.com/">actualízalo</a> Para mejorar tu experiencia.</p>
    <![endif]-->
