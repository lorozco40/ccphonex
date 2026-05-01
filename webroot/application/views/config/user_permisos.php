<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h3 class="tit-div">Permisos para el usuario <span class="text text-info"><?php echo $usuario_nombre; ?></span> id <span class="text text-info">(<?php echo $id_user;?>)</span></h3>
        </div>
    </div>
    <hr>
    <?php echo form_open('usuarios/permisos_guardar', array('role'=>'form', 'class'=>'form')); ?>
    <input type="hidden" name="id_user" value="<?php echo $id_user; ?>" />
    <div class="row">
        <div class="col">
            <input type="submit" class="btn btn-primary" value="Guardar todo" />
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-2"><h4>Parámetros</h4></div>
        <div class="col"></div>
    </div>
    <div class="row">
        <div class="col">
            <h5>Usuario</h5>
            <div class="table table-striped">
                <?php $nomostrar = ["chatinterno","servask","passask","pervidllam"]; ?>
                <?php foreach ($userData as $per): ?>
                    <?php if($per->data == "campanas"): ?>
                        <?php $filacams = $per; ?>
                    <?php elseif($per->data == "whatsapp"): ?>
                        <?php $filawhats = $per; ?>
                    <?php elseif(in_array($per->data, $nomostrar)): ?>
                    <?php else: ?>
                        <?php if($per->data == "perfil"): ?>
                            <?php if($this->udata['perfil'] == "admin" || $this->udata['id']<=5): ?>
                                <div class="table-row">
                                    <div class="table-cell"><?php echo $per->eti; ?></div>
                                    <div class="table-cell">
                                        <select name="<?php echo $per->id?>" class="form-control">
                                            <option <?php if($per->val=='agente') { echo "selected"; } ?> value="agente">Agente</option>
                                            <option <?php if($per->val=='crm') { echo "selected"; } ?> value="crm">CRM</option>
                                            <option <?php if($per->val=='supervisor') { echo "selected"; } ?> value="supervisor">Supervisor</option>
                                            <option <?php if($per->val=='superior') { echo "selected"; } ?> value="superior">Superior</option>
                                            <option <?php if($per->val=='admin') { echo "selected"; } ?> value="admin">Administrador</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php elseif($per->data == "theme"): ?>
                            <div class="table-row">
                                <div class="table-cell"><?php echo $per->eti; ?></div>
                                <div class="table-cell">
                                    <select name="<?php echo $per->id?>" class="form-control">
                                        <option value="default">Default</option>
                                        <option <?php if($per->val=='claro') { echo "selected"; } ?> value="claro">Claro</option>
                                        <option <?php if($per->val=='integra') { echo "selected"; } ?> value="integra">Integra</option>
                                        <option <?php if($per->val=='dark') { echo "selected"; } ?> value="dark">Dark</option>
                                        <option <?php if($per->val=='turki') { echo "selected"; } ?> value="turki">Turki</option>
                                        <option <?php if($per->val=='yoko') { echo "selected"; } ?> value="yoko">Yoko</option>
                                        <?php if($miper=='admin'): ?>
                                            <option <?php if($per->val=='nom035') { echo "selected"; } ?> value="admin">Norma 035</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php elseif($per->data == "whatsapp"): ?>
                            <div class="table-row">
                                <div class="table-cell"><?php echo $per->eti; ?></div>
                                <div class="table-cell">
                                    <select name="<?php echo $per->id?>" class="form-control">
                                        <option value="" selected>-- Elige --</option>
                                        <?php foreach ($wactas as $war): ?>
                                            <option <?php if($per->val==$war->id) { echo "selected"; } ?> value="<?php echo $war->id; ?>"><?php echo $war->nombre.' ('.$war->cuenta.')'; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php elseif($per->data == "email"): ?>
                            <div class="table-row">
                                <div class="table-cell"><?php echo $per->eti; ?></div>
                                <div class="table-cell">
                                    <select name="<?php echo $per->id?>" class="form-control" id="selemail">
                                        <option value="" selected>-- Elige --</option>
                                        <?php foreach ($emailctas as $emr): ?>
                                            <option data-idcampaign="<?=$emr->id_campaign?>" <?php if($per->val==$emr->id) { echo "selected"; } ?> value="<?php echo $emr->id; ?>"><?php echo $emr->nombre.' ('.$emr->email.')'; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php elseif($per->data == "token"): ?>
                            <div class="table-row">
                                <div class="table-cell"><?php echo $per->eti; ?></div>
                                <div class="table-cell">
                                    <div class="input-group">
                                        <input type="text" name="<?php echo $per->id?>" class="form-control" placeholder="m170k3n80n1t0" value="<?php echo $per->val; ?>" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-info gentoken" data-target="<?php echo $per->id?>" data-uid="<?php echo $id_user; ?>">(Re)Generar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php elseif($per->data == "userask"): ?>
                            <?php if($this->udata['perfil'] == "admin" || $this->udata['id']<=5): ?>
                                <div class="table-row">
                                    <div class="table-cell"><?php echo $per->eti; ?></div>
                                    <div class="table-cell">
                                        <input type="text" name="<?php echo $per->id; ?>" value="<?php echo $per->val; ?>" class="form-control" />
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="table-row">
                                <div class="table-cell"><?php echo $per->eti; ?></div>
                                <div class="table-cell">
                                    <input type="text" name="<?php echo $per->id; ?>" value="<?php echo $per->val; ?>" class="form-control" />
                                </div>
                            </div>
                        <?php endif;?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col">
            <h5>Campañas</h5>
            <?php $camsasigned = explode(',', str_replace(' ', '', $filacams->val)); ?>
            <select multiple class="form-select" data-target="<?=$filacams->id?>" id="selcams">
                <option value="" <?php if(count($camsasigned)==0) echo 'selected'; ?>>Ninguna</option>
                <?php foreach ($campanas as $cam): ?>
                    <option value="<?=$cam->id?>"
                        <?php if(in_array($cam->id, $camsasigned)) echo 'selected'; ?>><?=$cam->name?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="<?=$filacams->id?>" value="<?=$filacams->val?>">
        </div>
        <div class="col">
            <?php $wactasigned = explode(',', str_replace(' ', '', $filawhats->val)); ?>
            <h5>Cuentas WhatsApp</h5>
            <select multiple class="form-select" data-target="<?=$filawhats->id?>" id="selwhats">
                <option value="" <?php if(count($wactasigned)==0) echo 'selected'; ?>>Ninguna</option>
                <?php foreach ($wactas as $wacta): ?>
                    <option data-idcampaign="<?=$wacta->id_campaign?>" value="<?=$wacta->id?>"
                        <?php if(in_array($wacta->id, $wactasigned)) echo 'selected'; ?>><?=$wacta->nombre?>: <?=$wacta->cuenta?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="<?=$filawhats->id?>" value="<?=$filawhats->val?>">
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-2"><h4>Funcionalidad</h4></div>
        <div class="col"><small>Todos </small><input type="checkbox" id="sel-permisoSec" /></div>
    </div>
    <div class="row">
        <div class="col">
            <div class="table table-striped">
                <?php $cuenta = $ctaencol = 0;
                    $tot = count($this->udata['permisoSec'])-1;
                    $tot += (in_array('autoanswer', $this->udata['permisoSec'])) ? 0 : 1;
                    $prom = floor($tot/4);
                    $resto = $tot - ($prom*4);
                    $col = $prom;
                    if ($resto>0) {
                        $col = $prom + 1;
                        $resto--;
                    } ?>
                <?php foreach ($permisoSec as $per): ?>
                    <?php if($per->data=='qualif'): ?>
                        <?php $qid = $per->id; ?>
                    <?php else: ?>
                        <?php if($this->udata['perfil']=='admin' || in_array($per->data, $this->udata['permisoSec']) || $per->data=='autoanswer'): ?>
                            <div class="table-row">
                                <div class="table-cell"><?php echo $per->eti; ?></div>
                                <div class="table-cell">
                                    <input type="hidden" name="<?php echo $per->id; ?>" value="0">
                                    <input class="permisoSec" type="checkbox" value='1' name="<?php echo $per->id; ?>" <?php if($per->val == 1) echo "checked='checked'"; ?> />
                                </div>
                            </div>
                            <?php $cuenta++; $ctaencol++;
                                if($ctaencol==$col && $cuenta < $tot) {
                                    $ctaencol = 0;
                                    $col = $prom;
                                    if ($resto>0) {
                                        $col = $prom + 1;
                                        $resto--;
                                    }
                                    echo '</div></div><div class="col"><div class="table table-striped">';
                                }
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <input type="hidden" name="<?php echo $qid; ?>" value="1" />
    <?php if($miper=='admin'): ?>
        <hr>
        <div class="row">
            <div class="col-2"><h4>Configuración</h4></div>
            <div class="col"><small>Todos </small><input type="checkbox" id="sel-permiso" /></div>
        </div>
        <div class="row">
            <div class="col">
                <div class="table table-striped">
                    <?php $cuenta = 0; $cuantos = ceil(count($permiso)/4); ?>
                    <?php foreach ($permiso as $per): ?>
                        <div class="table-row">
                            <div class="table-cell"><?php echo $per->eti; ?></div>
                            <div class="table-cell">
                                <input type="hidden" name="<?php echo $per->id; ?>" value="0">
                                <input class="permiso" type="checkbox" value='1' name="<?php echo $per->id; ?>" <?php if($per->val == 1) echo "checked='checked'"; ?> />
                            </div>
                        </div>
                        <?php $cuenta++; if(fmod($cuenta, $cuantos)==0 && $cuenta < count($permiso)) echo '</div></div><div class="col"><div class="table table-striped">'; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <hr>
    <div class="row">
        <div class="col-2"><h4>Reportes</h4></div>
        <div class="col"><small>Todos </small><input type="checkbox" id="sel-permisoRepo" /></div>
    </div>
    <?php if(isset($this->udata['permisoRepo'])): ?>
    <div class="row">
        <div class="col">
            <div class="table table-striped">
                <?php $cuenta = $ctaencol = 0;
                    $tot = count($this->udata['permisoRepo']);
                    $prom = floor($tot/5);
                    $resto = $tot - ($prom*5);
                    $col = $prom;
                    if ($resto>0) {
                        $col = $prom + 1;
                        $resto--;
                    } ?>
                <?php $cuenta = 0; $cuantos = ceil(count($this->udata['permisoRepo'])/4); ?>
                <?php foreach ($permisoRepo as $per): ?>
                    <?php if($this->udata['perfil']=='admin' || in_array($per->data, $this->udata['permisoRepo'])): ?>
                        <div class="table-row">
                            <div class="table-cell"><?php echo $per->eti; ?></div>
                            <div class="table-cell">
                                <input type="hidden" name="<?php echo $per->id; ?>" value="0">
                                <input class="permisoRepo" type="checkbox" value='1' name="<?php echo $per->id; ?>" <?php if($per->val == 1) echo "checked='checked'"; ?> />
                            </div>
                        </div>
                        <?php $cuenta++; $ctaencol++;
                            if($ctaencol==$col && $cuenta < $tot) {
                                $ctaencol = 0;
                                $col = $prom;
                                if ($resto>0) {
                                    $col = $prom + 1;
                                    $resto--;
                                }
                                echo '</div></div><div class="col"><div class="table table-striped">';
                            }
                        ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if($miper=='admin'): ?>
        <hr>
        <div class="row">
            <div class="col-2"><h4>Reportes especiales</h4></div>
            <div class="col"><small>Todos </small><input type="checkbox" id="sel-permisoEsp" /></div>
        </div>
        <div class="row">
            <div class="col">
                <div class="table table-striped">
                    <?php $cuenta = 0; $cuantos = ceil(count($permisoEsp)/4); ?>
                    <?php foreach ($permisoEsp as $per): ?>
                            <div class="table-row">
                                <div class="table-cell"><?php echo $per->eti; ?></div>
                                <div class="table-cell">
                                    <input type="hidden" name="<?php echo $per->id; ?>" value="0">
                                    <input class="permisoEsp" type="checkbox" value='1' name="<?php echo $per->id; ?>" <?php if($per->val == 1) echo "checked='checked'"; ?> />
                                </div>
                            </div>
                        <?php $cuenta++; if(fmod($cuenta, $cuantos)==0 && $cuenta < count($permisoEsp)) echo '</div></div><div class="col"><div class="table table-striped">'; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div>
            </div>
        </div>
    <?php endif; ?>
    <hr>
    <div class="row">
        <div class="col">
            <input type="submit" class="btn btn-primary" value="Guardar todo" />
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
