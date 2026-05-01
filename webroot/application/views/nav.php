<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" role="navigation">
    <a class="navbar-brand" href="<?php echo site_url(''); ?>"><img src="<?php echo site_url('assets/img/logo.png'); ?>" height="35" /></a>
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar"
        aria-controls="mainNavbar" aria-expanded="false" aria-label="navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="mainNavbar">
            <?php if(isset($uname)): ?>
                <ul class="navbar-nav mr-auto">
                    <?php foreach ($menuops as $menk => $menv): ?>
                        <?php if(is_array($menv)): ?>
                            <li class="nav-item dropdown<?php if(key_in_array($agente['ruta'], $menv)) echo ' active'; ?>">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown<?php echo slugify($menk); ?>"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $menk; ?></a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown<?php echo slugify($menk); ?>">
                                    <?php foreach ($menv as $subk => $subv): ?>
                                        <?php if(is_array($subv)): ?>
                                            <li class="dropdown-submenu">
                                                <a class="dropdown-item dropdown-toggle" href="#"><?php echo $subk; ?></a>
                                                <ul class="dropdown-menu">
                                                    <?php foreach ($subv as $terk => $terv): ?>
                                                        <li><a class="dropdown-item" href="<?php echo site_url($terk); ?>"><?php echo $terv; ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </li>
                                        <?php else: ?>
                                            <li>
                                                <a class="dropdown-item" href="<?php echo site_url($subk); ?>" ><?php echo $subv; ?></a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if($menk=='Configuración'): ?>
                                        <li><a class="dropdown-item" href="" data-toggle="modal" data-target="#licenciaModal" id="licm">Licencia</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php else: ?>
                            <?php
                                // SE PIDIÓ QUE CUANDO HABRA EL MANUAL, SE HABRA EN OTRA VENTANA
                                if($menk == "manual") $target=" target='_blank' ";
                                else $target="";
                            ?>
                            <li class="nav-item<?php if(is_numeric(strpos($seg, $menk))) echo ' active'; ?>">
                                <a class="nav-link" href="<?php echo site_url($menk); ?>" <?php echo $target; ?> ><?php echo $menv; ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <?php if (!empty($agente) && $agente['ruta'] == 'consola'): ?>
                    <div id="chatinterno_container" data-toggle="tooltip" data-placement="top" title="Chat interno" class="nav-item">
                        <span class="badge wabadge" id="nuevochatinterno" style="display:none">1</span>
                        <i class="fab fa-stack-exchange openchatinterno"></i>
                    </div>
                    <div>
                        <a href="" data-toggle="modal" data-target="#misDatosModal">
                            <i class="fas fa-user-circle esazulogo"></i>
                        </a>
                    </div>&nbsp;&nbsp;
                <?php endif; ?>
                <div class="esblanco" style="line-height:1;">
                    <strong><?php echo $uname; ?> </strong><br /><small><?php echo $agente['perfil']; ?></small>
                </div>
                <div>
                    <a href="<?php echo site_url('acceso/logout'); ?>" id="btnsalir" class="btn btn-outline-info">Salir <i class="fas fa-sign-out-alt"></i><?php if ($css=="turki" || $css=="dark"): ?><?php endif; ?></a>
                </div>
            <?php else: ?>
                <ul class="navbar-nav mr-auto"></ul>
                <?php echo form_open('acceso/login', array('class'=>'form-inline', 'role'=>'form')); ?>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                        </div>
                        <input class="form-control" name="maillogin" autocomplete="username" type="email" placeholder="Correo" aria-describedby="sizing-addon2" required autofocus />
                    </div>
                    <input name="passwordlogin" autocomplete="current-password" type="password" placeholder="Contraseña"
                    class="form-control" required style='margin-left:5px;' />
                    <button type="submit" class="btn btn-info" style='height:32px;margin-left:5px;'>Entrar</button>
                <?php echo form_close(); ?>
            <?php endif; ?>
        </div>
    </div>
</nav>
<section id="msgs"></section>
<?php include(APPPATH.'views/chatinterno.php'); ?>
<?php if (isset($uname)): ?>
    <div class="modal fade" id="licenciaModal" tabindex="-1" role="dialog" aria-labelledby="licenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <?php $campana = count($licenciasxcampana) == 1 ? "- " . $licenciasxcampana[0]->name . " -" : ""; ?>
                    <h5 class="modal-title" id="licenciaModalLabel">Licencia ASSERTIVE <?php echo $campana; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if (count($licenciasxcampana) >= 1 ): ?>
                        <div class="table table-striped" id="tablacampanaslicenciasinfo">
                            <div class="table-header-group">
                                <div class="table-cell text-center">Campaña</div>
                                <div class="table-cell text-center">Existentes</div>
                                <div class="table-cell text-center">En Uso</div>
                                <div class="table-cell text-center">Disponibles</div>
                            </div>
                            <?php foreach($licenciasxcampana as $key=> $datos): ?>
                            <div class="table-row">
                                <div class="table-cell"><?php echo $datos->name;?></div>
                                <div class="table-cell text-center"><?php echo $datos->licenses;?></div>
                                <div class="table-cell text-center"><?php echo $datos->used_licenses;?></div>
                                <div class="table-cell text-center"><?php echo $datos->available_licenses;?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="lead">Dudas y sugerencias al <strong>+52 (55) 5420 5000 ext. 103</strong></p>
                        <p class="lead">Versión <strong><?=VER_ASS;?></strong></p>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No tienes campañas asignadas.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="misDatosModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="misDatosModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="misDatosModalLabel">Mis datos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php echo form_open('usuarios/misdatos', array('class'=>'form', 'role'=>'form', 'id'=>'misdatosform'),
                    array('id'=>$agente['id'])); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class='form-group'>
                                <label for='user'>eMail</label>
                                <?php echo form_input('user', $agente['email'],
                                array('readonly'=>'readonly', 'class'=>'form-control')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class='form-group'>
                                <label for='name'>Nombre(s)</label>
                                <?php echo form_input('name', $agente['name'], array('class'=>'form-control')); ?>
                            </div>
                            <div class='form-group'>
                                <label for='last'>Apellido(s)</label>
                                <?php echo form_input('last', $agente['last'], array('class'=>'form-control')); ?>
                            </div>
                            <div class="form-group">
                                <label for="tema">Tema</label>
                                <select class="form-control" name="tema">
                                    <option value="" selected>Default</option>
                                    <option value="claro"<?php echo ($agente['tema']=='claro') ? ' selected' : ''; ?>>Claro</option>
                                    <option value="turki"<?php echo ($agente['tema']=='turki') ? ' selected' : ''; ?>>Turki</option>
                                    <option value="dark"<?php echo ($agente['tema']=='dark') ? ' selected' : ''; ?>>Dark</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class='form-group'>
                                <label for='oldpass'>Contraseña anterior</label>
                                <?php echo form_password('oldpass', '', array('class'=>'form-control')); ?>
                            </div>
                            <div class='form-group'>
                                <label for='pass'>Contraseña nueva</label>
                                <?php echo form_password('pass', '', array('class'=>'form-control')); ?>
                            </div>
                            <div class='form-group'>
                                <label for='confpass'>Confirmar contraseña</label>
                                <?php echo form_password('confpass', '', array('class'=>'form-control')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class='form-group'>
                                <label for='confpass'>Token</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-primary verotro" data-otro="#mdf-token">Ver</button>
                                    <input type="hidden" name="token" id="mdf-token" class="form-control" placeholder="m170k3n80n1t0" value="<?php echo $agente['token']; ?>" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-info gentoken" data-target="token">(Re)Generar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
