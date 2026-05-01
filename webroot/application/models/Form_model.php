<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Form_model extends CI_Model
{

    public function get_all() {
        $query = $this->db->query("SELECT * from form order by name");
        return $query->result();
    }

    public function get_list_active(int $type = 1) { // 0 = Formularios, 1 = CRMs, 2 = Todos
        $tipo = "AND crm = '$type'";
        if ($type == 2) $tipo = "";
        $query = $this->db->query("SELECT f.*, c.name AS 'cam' FROM form f
            JOIN campaign c ON c.id = f.id_campaign
            WHERE f.active=1 AND f.id_campaign IN (".$this->udata['campanas'].")
            $tipo order by c.name, f.name");

        return $query->result();
    }

    public function get_one($id) {
        $query          = $this->db->query("SELECT * from form where id='$id'");
        $data['form']   = $query->row();
        $data['regs']   = $this->db->count_all("formd_".$id);
        $query = $this->db->query("SELECT '0' AS `id_form_fields`, `name`, `depend`
            FROM form_fields
            WHERE id_form='$id'
            AND descen='ddeepp'
            UNION
            SELECT `id_form_fields`, `name`, `depend`
            FROM form_fields_tbr
            WHERE id_form='$id'
            AND descen='ddeepp'
        ");
        $deps = $query->result();
        $data["sidep"]  = (count($deps)>0) ? true : false;
        $data['fields'] = [];
        foreach ($deps as $row) {
            $fdt = ($row->depend == 0) ? "" : $row->depend;
            $data['fields'][$row->name] = $this->db->list_fields("formd_" . $id . "_dep" . $fdt);
        }
        $data['fields_full'] = [];
        foreach ($deps as $row) {
            $fdt = ($row->depend == 0) ? "" : $row->depend;
            $data['fields_full'][$row->name] = [
                'campos' => $this->db->list_fields("formd_" . $id . "_dep" . $fdt),
                'info'   => $row
            ];
        }

        return $data;
    }

    public function get_sem(int $fid = 0) {
        $query = $this->db->query("SELECT * FROM crm_light WHERE id_form = ?", [$fid]);

        return $query->row();
    }

    public function getByCam($cid) {
        $query = $this->db->query("SELECT id, name from form where id_campaign = ? and `active` = '1' order by `type` desc, `name`", $cid);
        return $query->result();
    }

    public function countByCams(string $cams = null, int $tipo = 0) { // 0 formulario simple, 1 formulario CRM
        $query = $this->db->query("SELECT count(*) cuenta FROM form WHERE id_campaign IN ($cams) AND crm = '$tipo'");

        return $query->row()->cuenta;
    }

    public function get_list($cams, $ini, $crm = 0) {
        $query = $this->db->query("SELECT f.*, dep.n_dep
        FROM form f
        LEFT JOIN (
            SELECT id_form, COUNT(*) AS n_dep 
            FROM form_fields
            WHERE descen='ddeepp'
            GROUP BY id_form
        ) dep ON dep.id_form = f.id 
        WHERE f.id_campaign IN ($cams) 
        AND f.crm = '".$crm."' 
        ORDER BY f.name LIMIT ?, ?",
            [$ini, REGS_POR_PAG]
        );
        return $query->result();
    }

    public function create($data, $crm = 0) {
        $short_name = slugify($data['name']);
        $type = (isset($data['type'])) ? 1 : 0;
        if ($type == 1) $this->db->query("UPDATE form SET `type` = 0 WHERE id_campaign = ? AND `type` = 1 ", [$data['campaign']]);
        $this->db->query("INSERT INTO form (`id_campaign`, `name`, `short_name`, `type`, `crm`, `created_by`, `created_when`)
            values (?,?,?,?,?,?,now())", array($data['campaign'], $data['name'], $short_name, $type, $crm, $this->session->userdata('uid')));
        $fid = $this->db->insert_id();

        return ($this->generate($fid)) ? $fid : false;
    }

    public function eliminaForm(int $fid = 0) {
        $cont = false;
        // ToDo: Permitir sólo adminsitradores o agregar permiso de eliminación ?
        if ($this->udata['perfil'] == 'admin') {
            $sqlstatemnt = "DROP TABLE IF EXISTS formd_" . $fid;
            $this->db->query($sqlstatemnt . "_dep");
            $this->db->query($sqlstatemnt . "_dep1");
            $this->db->query($sqlstatemnt . "_dep2");
            $this->db->query($sqlstatemnt . "_dep3");
            $this->db->query($sqlstatemnt . "_tb0");
            $this->db->query($sqlstatemnt . "_tb1");
            $this->db->query($sqlstatemnt . "_tb2");
            $this->db->query($sqlstatemnt . "_tb3");
            $this->db->query($sqlstatemnt . "_file");
            $this->db->query($sqlstatemnt . "_crm");
            $this->db->query($sqlstatemnt . "_cats");
            $this->db->query($sqlstatemnt . "_depasign");
            $this->db->query($sqlstatemnt);
            $this->db->query("DELETE FROM form_fields_tbr WHERE id_form = ?", [$fid]);
            $this->db->query("DELETE FROM form_filter_dep WHERE id_form = ?", [$fid]);
            $this->db->query("DELETE FROM form_closing_operations WHERE id_form = ?", [$fid]);
            $this->db->query("DELETE FROM form_calc_fields WHERE id_form = ?", [$fid]);
            $this->db->query("DELETE FROM crm_light WHERE id_form = ?", [$fid]);
            $this->db->query("DELETE FROM crm_plant_pdf WHERE id_form = ?", [$fid]);
            $this->db->query("DELETE FROM form_fields WHERE id_form = ?", [$fid]);
            $cont = $this->db->query("DELETE FROM form WHERE id = ?", [$fid]);
            if (file_exists(APPPATH . '/views/form/form_' . $fid . '.php')) {
                unlink(APPPATH . '/views/form/form_' . $fid . '.php');
            }
        }

        return $cont;
    }

    public function update() {
        $data = $this->input->post();
        if($data['type']==1) {
            $this->db->query("UPDATE `form` set `type` = 0 where `id_campaign` = ?", [$data['campaign']]);
        }
        return $this->db->query("UPDATE `form` set `id_campaign`=?, `name`=?, `type`=?, `active`=? where `id`=?",
            array($data['campaign'], $data['name'], $data['type'], $data['active'], $data['id']));
    }

    public function save_form() {
        $data = $this->input->post();
        if (!empty($data['id_form'])) {
            $form = $this->get_one($data['id_form']);
            $query  = $this->db->query("SELECT * from form_fields
                where id_form='".$data['id_form']."' AND `type` <> 'separador' order by `order`, `id`");
            $campos = $query->result();
            $qcam = "`id_user`, `apertura`, `uniqueid`";
            $qval = array($this->udata['id'], $data['uniqueid']);
            $qdef = "?,now(),?";
            foreach ($campos as $campo) {
                $qcam .= ", `".$campo->slug."`";
                $qdef .= ",?";
                if ($campo->type == 'checkbox' && isset($data[$campo->slug])) {
                    $qval[] = 1;
                } elseif ($campo->type == 'checkbox') {
                    $qval[] = 0;
                } else {
                    $qval[] = (!empty($data[$campo->slug])) ? $data[$campo->slug] : "0";
                }
            }
            $query = "INSERT into formd_".$data['id_form']." (".$qcam.") values (".$qdef.")";
            if ($this->db->query($query, $qval)) {
                return true;
            }
        }
        return false;
    }

    public function crear_form_file($fid, $cid) {
        $query = $this->db->query("SELECT * from form where id=?", array($fid));
        if ($form = $query->row()) {
            $res1   = "<h5>$form->name</h5>\n";
            $res    = "\t<?php $"."padrecero_id = 0; ?>\n\t<input type='hidden' name='id' value='<?php echo $"."id; ?>' />\n".
                "\t<input type='hidden' name='id_form' value='$form->id' />\n".
                "\t<input type='hidden' name='uniqueid' value='<?php echo $"."uniqueid; ?>' />\n";
            $fields = $this->get_fields($form->id);
            if ($form->crm == 1) { // Es CRM
                $res1 .= "<form action='".site_url('crm/guardar')."' method='post' class='form crm_form'>\n";
                $res  .= "\t<div class='row crminfo'>\n\t\t<div class='col'>Ticket ID<br><span class='ticketid esrojo'><?php echo $".
                    "id; ?></span></div>\n".
                    "\t\t<div class='col'>Llamada ID<br><span class='uniqueid'><?php echo $"."uniqueid; ?></span></div>\n".
                    "\t\t<div class='col'>Apertura<br><span class='apertura'><?php echo $"."apertura; ?></span></div>\n".
                    "\t\t<div class='col'>Estatus<br><span class='estatus'><?php echo $"."estatus; ?></span></div>\n\t</div>\n\t<br>\n".
                    "\t<div class='row'>\n\t\t<div class='col'>\n\t\t\t<div class='form-group'>\n\t\t\t\t<label for='id_cliente'>Cliente ".
                    "<button type='button' class='btn btn-info ageedit' data-id='".
                    "<?php echo $"."registro->id_cliente; ?>' <?php if($"."dis=='0' || $".
                    "dis=='3') echo \"style='display:none'\"; ?>>Editar</button></label>\n".
                    "\t\t\t\t<select class='form-control' name='id_cliente' required<?php if(($".
                    "dis>=1 && $"."perfil == 'agente') || $"."dis==3) echo \" readonly\"; ?>>\n".
                    "\t\t\t\t\t<option value=''>-- Elige --</option>\n\t\t\t\t\t<?php foreach($"."clientes as $"."key => $"."val): ?>\n".
                    "\t\t\t\t\t\t<option value='<?php echo $"."val->id; ?>'<?php if ($"."val->id == $"."registro->id_cliente) echo ' selected'; ?>>".
                    "<?php echo $"."val->nombre; ?></option>\n\t\t\t\t\t<?php endforeach; ?>\n".
                    "\t\t\t\t</select>\n\t\t\t</div>\n\t\t</div>\n\t\t<div class='col'>\n\t\t\t<div class='form-group'>\n\t\t\t\t<label>Asignar a</label>\n".
                    "\t\t\t\t<select class='form-control' name='asignar_a'<?php if(($".
                    "dis>=2 && $"."perfil == 'agente') || $"."dis==3) echo \" readonly\"; ?>>\n".
                    "\t\t\t\t\t<option value=''>-- Elige --</option>\n\t\t\t\t\t<?php foreach($"."agentes as $"."key => $"."val): ?>\n".
                    "\t\t\t\t\t\t<option value='<?php echo $"."val->id; ?>'<?php if ($"."val->id == $"."registro->asignar_a) echo ' selected'; ?>>".
                    "<?php echo $"."val->nombre; ?></option>\n\t\t\t\t\t<?php endforeach; ?>\n".
                    "\t\t\t\t</select>\n\t\t\t</div>\n\t\t</div>\n\t\t<div class='col'>\n\t\t\t<div class='form-group'>\n\t\t\t\t<label for='informar'>Informar a</label>\n".
                    "\t\t\t\t<input type='text' name='informar' class='form-control' placeholder='email (opcional)' value='<?php echo (!empty($".
                    "registro->informar)) ? $"."registro->informar : ''; ?>'".
                    "<?php if($"."dis=='3') echo \"readonly\" ?>>\n\t\t\t</div>\n\t\t</div>\n\t</div>\n\t<div class='form-group'>\n".
                    "\t\t<label for='detalle'>Detalle</label>\n".
                    "\t\t<textarea class='form-control' id='detalle' name='detalle' required='required'<?php if(($".
                    "dis>=1 && $"."perfil == 'agente') || $"."dis==3) echo \" readonly\"; ?>><?php echo $".
                    "registro->detalle; ?></textarea>\n\t</div>\n\t<div class='d-flex flex-wrap mb-3'>\n";
                $listos  = array('id', 'detalle', 'apertura', 'cierre', 'id_cliente', 'asignar_a', 'estatus', 'uniqueid', 'informar', 'semaforo');
                $botones = "\t</div>\n\t<div class='row'>\n\t\t<?php if($"."dis=='0'): ?>\n\t\t\t<div class='col'>\n".
                    "\t\t\t\t<button class='btn btn-primary guardarticket'>Abrir ticket</button>\n\t\t\t</div>\n\t\t<?php endif; ?>\n".
                    "\t</div>\n</form>\n";
                $campostatus = $this->getCampoFromResult($fields, 'slug', 'estatus');
                $statops = $this->getStatusOptsByFieldVal($campostatus->values, $form->id);
                $extraf  = "<span <?php if($"."dis=='0') echo \"style='display:none'\"; ?>>\n<hr>\n<h5>Historial</h5>\n".
                    "<form class='form statform' method='post' <?php if($"."dis=='3') echo \"style='display:none'\"; ?>>\n".
                    "\t<input type='hidden' name='id' value='<?php echo $"."id; ?>' />\n".
                    "\t<input type='hidden' name='pl4n71ll4' value='' />\n".
                    "\t<input type='hidden' name='id_form' value='$form->id' />\n".
                    "\t<input type='hidden' name='uniqueid' value='<?php echo $"."uniqueid; ?>' />\n".
                    "\t<div class='row'>\n\t\t<div class='col'>\n\t\t\t<p>Comentarios</p>\n".
                    "\t\t\t<textarea name='coment' rows='3' class='form-control' required='required'></textarea>\n".
                    "\t\t</div>\n\t\t<div class='col-4'>\n\t\t\t<p>Estatus</p>\n\t\t\t<select class='form-control' name='status'>\n".
                    $statops.
                    "\t\t\t</select>\n\t\t\t<br>\n\t\t\t<button type='submit' name='button' class='btn btn-info w-100'>Guardar</button>\n\t\t</div>\n".
                    "\t</div>\n</form>\n<br>\n<div class='tabla-gin'>\n<div class='table table-stripped statable'>\n\t<div class='table-header-group'>\n".
                    "\t\t<div class='table-cell'>fecha</div>\n\t\t<div class='table-cell'>Usuario</div>\n".
                    "\t\t<div class='table-cell'>Comentario</div>\n\t\t<div class='table-cell'>Estatus</div>\n\t</div>\n".
                    "\t<?php foreach ($"."comentarios as $"."coment): ?>\n\t\t<div class='table-row'>\n".
                    "\t\t\t<div class='table-cell'><?php echo $"."coment->fecha; ?></div>\n".
                    "\t\t\t<div class='table-cell'><?php echo $"."coment->agente; ?></div>\n".
                    "\t\t\t<div class='table-cell'><?php echo $"."coment->comentario; ?></div>\n".
                    "\t\t\t<div class='table-cell'><?php echo $"."coment->estatus; ?></div>\n\t\t</div>\n".
                    "\t<?php endforeach; ?>\n</div>\n</div>\n<hr>\n<h5>Archivos</h5>\n".
                    "<form class='form fileform' method='post' enctype='multipart/form-data' accept-charset='utf-8'<?php if($".
                    "dis=='3') echo \" style='display:none'\"; ?>>\n".
                    "\t<input type='hidden' name='id' value='<?php echo $"."id; ?>' />\n".
                    "\t<input type='hidden' name='id_form' value='$form->id' />\n".
                    "\t<div class='row'>\n\t\t<div class='col'>\n\t\t\t<p>Archivo</p>\n\t\t\t<input type='file' name='archivo' />\n".
                    "\t\t</div>\n\t\t<div class='col-2'>\n\t\t\t<button type='submit' name='button' class='btn btn-info'>Guardar</button>\n".
                    "\t\t</div>\n\t</div>\n</form>\n<div class='table table-stripped filetable'>\n".
                    "\t<div class='table-header-group'>\n\t\t<div class='table-cell'>fecha</div>\n".
                    "\t\t<div class='table-cell'>Usuario</div>\n\t\t<div class='table-cell'>Archivo</div>\n".
                    "\t</div>\n\t<?php foreach ($"."archivos as $"."archivo): ?>\n\t\t<div class='table-row'>\n".
                    "\t\t\t<div class='table-cell'><?php echo $"."archivo->fecha; ?></div>\n".
                    "\t\t\t<div class='table-cell'><?php echo $"."archivo->agente; ?></div>\n".
                    "\t\t\t<div class='table-cell'>\n\t\t\t\t<a target='_blank' href='<?php echo site_url('archivo?name='.$".
                    "archivo->filename.'&amp;type=form'); ?>'>".
                    "<?php echo $"."archivo->name; ?></a>\n\t\t\t</div>\n\t\t</div>\n\t<?php endforeach; ?>\n</div>\n</span>\n";
            } else {
                $res1   .= "<form action='".site_url('ajax/save_form')."' method='post' class='form call_form'>\n";
                $res    .= "<div class='row'>\n<div class='col'>Llamada ID<br><span class='uniqueid'><?php echo $".
                    "uniqueid; ?></span></div>\n</div><br>\n<div class='d-flex flex-wrap'>\n";
                $listos  = array();
                $botones = "</div><div align='center'>\n<button class= 'btn btn-primary' type='submit'>Guardar</button>\n</div>\n</form>";
                $extraf  = "";
            }
            $calc_fields = $this->calc_field_list($fid);
            foreach ($fields as $campo) {
                if ($campo->type == "separador") {
                    $res .= "<div class='w-100 mb-2 text-center separ'>" . $campo->name . "</div>";
                } else if ($campo->type == "tabla") {
                    $res .= "<div class='w-100 mb-2 text-center separ-table'>" . $campo->name . "</div>";
                    //consultamos los campos de esa tabla dependiente y los mostramos
                    $id_form_fields = $campo->id;
                    $rows_tbr = $this->db->query("SELECT * FROM form_fields_tbr WHERE id_form_fields=? ORDER BY `order` ASC, `name` ASC", [$id_form_fields])->result();
                    $res .= "<div class='container_tbr d-flex flex-wrap w-100 mb-3' data-ntbr='".$campo->id."'>
                        <input type='hidden' name='id' value='0'>";
                    foreach($rows_tbr as $campo_tbr) {
                        if ($campo_tbr->type == "separador") {
                            $res .= "<div class='w-100 mb-2 text-center separ'>" . $campo_tbr->name . "</div>";
                        } else {
                            $class = '';
                            $atributo = '';
                            if( $this->is_calc_field_activator( 'tbr-'.$campo_tbr->id, $calc_fields ) ) {
                                $class = 'calc-field-activator';
                                $atributo = "data-fcfid='tbr-".$campo_tbr->id."'";
                            }
                            $extra_options = [
                                'class'     => $class,
                                'atributo'  => $atributo,
                            ];
                            $res .= $this->generate_field_html($campo_tbr, $cid, $extra_options);
                        }
                    }
                    $res .= "
                    <div class='w-100 d-grid gap-2'>
                        <div class='row'>
                            <div class='col-12'>
                                <button type='button' class='btn btn-primary w-100 btn-add' onclick='crm_tbr.add(".$campo->id.")'>
                                    Agregar
                                </button>
                            </div>
                            <div class='col-6 p-1 pl-3 mt-1'>
                                <button type='button' class='btn btn-secondary w-100 btn-update' style='display:none;' value='x' onclick='crm_tbr.update(".$campo->id.", this.value)'>
                                    Actualizar
                                </button>
                            </div>
                            <div class='col-6 p-1 pr-3 mt-1'>
                                <button type='button' class='btn btn-danger w-100 btn-cancel' style='display:none;' onclick='crm_tbr.reset(".$campo->id.")'>
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class='container-tbr-data w-100 scroll-x my-1' data-tbr-id='".$campo->id."'>
                    </div>
                    ";
                } elseif (!in_array($campo->slug, $listos) && $campo->front == 1) {
                    if($this->is_calc_field_activator($campo->id, $calc_fields)) {
                        $res .= $this->generate_field_html($campo, $cid, ['class' => 'calc-field-activator', 'atributo' => "data-fcfid='".$campo->id."'"]);
                    } else {
                        $res .= $this->generate_field_html($campo, $cid);
                    }
                }
            }
            // Agregar plantillas PDF pre-relacionadas al formulario en tabla
            $query = $this->db->query("SELECT * FROM crm_plant_pdf WHERE id_form = ?", [$fid]);
            $plantillas = $query->result();
            $addplan = "";
            if (count($plantillas)>0) {
                $addplan = "<div class='w-100 mb-2 text-center separ'>Plantillas PDF</div>
                    <select class='form-control' name='pl4n71ll4'<?php if(($".
                    "dis>=1 && $"."perfil == 'agente') || $"."dis==3) echo \" readonly\"; ?>>
                    <option value='0'>-- Sin PDF --</option>";
                foreach ($plantillas as $ptl) {
                    $addplan .= "<option value='$ptl->id'>$ptl->name</option>";
                }
                $addplan .= "</select>";
            }
            $res = $res1 . $res . $addplan . $botones . $extraf;
            if (!file_exists(APPPATH.'views/form')) mkdir(APPPATH.'views/form', 0755, true);
            $archivo = fopen(APPPATH.'views/form/form_'.$fid.'.php', 'w');
            fwrite($archivo, $res);
            fclose($archivo);
        } else {
            $res = "";
        }
        return $res;
    }

    public function get_list_forms($cid = 0, $fid = 0, $bus = '', $pag = 0, $rpp = 20) {
        $success = true;
        $msg     = '';
        $head    = [];
        $data    = [];
        $rows    = [];
        $tot     = 0;
        // Obtenemos los campos buscables de ese formulario
        $sf = $this->searchable_fields($fid);
        //Recorremos los campos
        $n = count($sf);
        if( $n > 0 ) {
            $head[] = 'ID';
            $head[] = 'Apertura';
            $select = "SELECT `id` AS `ID`, `apertura` AS `Apertura`, ";
            $where = "WHERE 1=2 ";
            foreach( $sf as $row ) {
                $head[] = $row->name;
                $select .= "`".$row->slug."` AS `".$row->name."`,";
                $where .= "OR `".$row->slug."` LIKE '%$bus%'";
            }
            $select = rtrim( $select, ',');
        } else {
            $success = false;
            $msg = "Este formulario no esta habilitado para este tipo de busquedas.";
        }
        if( $success ) {
            $query = $this->db->query("SELECT COUNT(*) tot FROM formd_$fid $where");
            $tot = $query->row()->tot;
            $rows = $this->db->query("$select FROM formd_$fid $where ORDER BY id DESC LIMIT ?, ?", [$pag, $rpp])->result();
        }
        
        return [
            'head'      => $head,
            'rows'      => $rows,
            'msg'       => $msg,
            'tot'       => $tot,
            'pag'       => $pag,
            'rpp'       => $rpp,
            'success'   => $success
        ];
    }

    public function searchable_fields($fid = 0) {
        $result = $this->db->query("SELECT `name`, `slug`, `searchable`
        FROM form_fields
        WHERE id_form = ?
        AND searchable = 1", [$fid])->result();

        return $result;
    }

    public function get_form($cid, $data = [], $tipo = 'form', $fid = false) {
        $data['perfil']      = $this->udata['perfil'];
        $data['id']          = (isset($data['id'])) ? $data['id'] : '-';
        $data['uniqueid']    = (isset($data['uniqueid'])) ? $data['uniqueid'] : '';
        $data['apertura']    = '';
        $data['estatus']     = '';
        $data['comentarios'] = [];
        $data['archivos']    = [];
        // Datos de los campos de el formulario
        $data['form_fields_tbr'] = $this->get_tbr($fid);
        $data['registro']    = (object)['asignar_a'=>'','id_cliente'=>'', 'detalle'=>''];
        if (!empty($fid)) {
            $query = $this->db->query("SELECT * from `form` where `id` = '$fid' and `active`='1'");
        } else {
            $query = $this->db->query("SELECT * from `form` where `id_campaign` = '$cid'
                and `type` = '1' and `active`='1' order by `id` DESC limit 1");
        }
        $form = $query->row();
        if ($form) {
            $escrm = !empty($form->crm);
            if(!file_exists(APPPATH.'/views/form/form_'.$form->id.'.php')) {
                $this->crear_form_file($form->id, $cid);
            }
        }
        // Entrante ya trae en el array data la campaña y los datos de la  llamada, por eso se omite éste paso
        if ($tipo == 'saliente') {
            $exten  = $this->udata['exten'];
            $ctxOps = ["ssl"=>["verify_peer"=>false,"verify_peer_name"=>false]];
            $ariserv = "https://" . getenv('ARI_USER') . ":" . getenv('ARI_PASS') . "@" . getenv('ASS_DB_HOST') . ":8089";
            $extenstat = json_decode(file_get_contents($ariserv . "/ari/endpoints/PJSIP/".$exten, false, stream_context_create($ctxOps)));
            foreach ($extenstat->channel_ids as $chan) {
                $chanstat = json_decode(file_get_contents($ariserv . "/ari/channels/".$chan, false, stream_context_create($ctxOps)));
                if ($chanstat->dialplan->exten != "*79" && $chanstat->dialplan->exten != "*78" &&
                    $chanstat->dialplan->exten != "") {
                    $data['uniqueid'] = $chanstat->id;
                    $data['name'] = $chanstat->dialplan->context;
                    break;
                }
            }
        }

        if ($form && file_exists(APPPATH.'/views/form/form_'.$form->id.'.php')) {
            $data['dis'] = 0; // deshabilitar nada, formulario vacío, default
            if(!empty($data['id']) && $data['id'] != "-" && $escrm) {
                $data['dis'] = 1; // deshabilitar básico, formulario existente, debe haber cliente elegido
                $query = $this->db->query("SELECT *, date_format(apertura, '$this->dtfor') apertura,
                    date_format(cierre, '$this->dtfor') cierre from `formd_$form->id` where id = '".$data['id']."'");
                if ($registro = $query->row()) {
                    $data['registro'] = $registro;
                    $data['apertura'] = $registro->apertura;
                    if (!empty($registro->asignar_a) && $form->repstatdet < 2) $data['dis'] = 2; // deshabilitar avanzado, ticket asignado
                    if ($registro->estatus == 'Cerrado' || $registro->estatus == 'Cancelado') {
                        $data['dis'] = 3; // deshabilitar total, ticket cerrado
                        $data['estatus'] = 'Cerrado<br>'.$registro->cierre;
                    } else {
                        $data['estatus'] = $registro->estatus;
                    }
                    $this->load->model("crm_model");
                    $data['comentarios'] = $this->crm_model->getCrmHisto($form->id, $data['id']);
                    $data['archivos']    = $this->crm_model->getFileHisto($form->id, $data['id']);
                } else {
                    $data = ['status' => 'error', 'msg' => 'No existe el ticket <strong>'.$data['id'].'</strong> en la campaña seleccionada.'];
                }
            } else if(!empty($data['id']) && $data['id'] != "-") {
                $query = $this->db->query("SELECT * from `formd_$form->id` where id = ?", [$data['id']]);
                $data['registro'] = $query->row();
                if (empty($data['registro'])) {
                    $data = ['status' => 'error', 'msg' => 'No existe el registro.'];
                }
            }
            if (empty($data['status'])) {
                $this->load->model("agentes_model");
                $data['agentes']  = $this->datos_model->getRelUsers(["cam"=>$cid, "per"=>'"crm"']);
                if ($this->db->table_exists("formd_".$form->id."_cats")) {
                    $data['cats'] = $this->db->query("SELECT * from formd_".$form->id."_cats ORDER BY field, seq, eti")->result();
                }
                $clid = (!empty($registro->id_cliente)) ? $registro->id_cliente : 0;
                $data['clientes'] = $this->agentes_model->getCliXCam($cid, $clid);
                $data['form'] = $this->load->view('form/form_'.$form->id, $data, true);
                unset($data['perfil']);
            }
        } else {
            $data['status'] = 'error';
            $data['msg'] = 'No se pudo crear el formulario.';
        }

        return $data;
    }

    public function get_fields($idForm) {
        $query = $this->db->query("SELECT * from form_fields where id_form='$idForm' order by `order`");
        return $query->result();
    }

    public function get_field($id) {
        $query = $this->db->query("SELECT * from form_fields where id='$id'");
        return $query->row();
    }

    public function get_fields_form($idForm) {
        $query = $this->db->query("SELECT * from form_fields where id_form='$idForm' order by `order`, `name`");
        return $query->result();
    }

    //obtenemos las tablas relacionadas al formulario
    public function get_tbr($id_formulario = 0) {
        $tbrs = [];
        $query = $this->db->query("SELECT id, `name`
            FROM form_fields
            WHERE id_form='$id_formulario'
            AND descen = 'ttbbrr'
            ORDER BY `order`, `name`
        ")->result();
        foreach ($query as $tbr) {
            $data = $this->db->query("SELECT *
            from form_fields_tbr
            where id_form_fields='".$tbr->id."' order by `order`, `id`")->result();
            $tbrs[] = [
                'id_form_field'  => $tbr->id,
                'name'  => $tbr->name,
                'data'  => $data,
            ];
        }

        return $tbrs;
    }

    public function createc($data = false) {
        $data = (empty($data)) ? $this->input->post() : $data;
        if (empty($data['id_form']) || empty($data['name']) || empty($data['type'])) {
            return false;
        }
        $fields = "`id_form`, `name`, `slug`, `type`";
        $fieldmarks = "?,?,?,?";
        $fieldvals = [$data['id_form'], $data['name'], slugify($data['name']), $data['type']];
        if (!empty($data['slug'])) $fieldvals[2] = $data['slug'];
        switch ($data['type']) {
            case 'dropdown':
                $fields .= ",`values`";
                $fieldmarks .= ",?";
                $fieldvals[] = (!empty($data['values'])) ? $data['values'] : "default";
                break;
            case 'tabla':
                $fields .= ",`values`, `depend`";
                $fieldmarks .= ",?,?";
                $fieldvals[] = "ttbbrr";
                $result = $this->db->query("SELECT MAX(depend) AS max
                    FROM form_fields
                    WHERE id_form = ? AND descen = 'ttbbrr'",[$data['id_form']])->row();
                $fieldvals[] = (empty($result->max)) ? 0 : $result->max+1;
                $this->db->query("CREATE TABLE `formd_".$data['id_form']."_tb".$max."` (
                    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `id_formd` int(11) NOT NULL
                )");
                break;
            case 'separador':
                $fieldvals[2] = "sep_" . $fieldvals[2];
                break;
            case 'boton':
            case 'text':
                $fields .= ",`len`";
                $fieldmarks .= ",?";
                $fieldvals[] = (!empty($data['len'])) ? $data['len'] : 127;
                break;
            case 'int':
                $fields .= ",`len`";
                $fieldmarks .= ",?";
                $fieldvals[] = (!empty($data['len'])) ? $data['len'] : 11;
                break;
            case 'bool':
                $fields .= ",`len`";
                $fieldmarks .= ",?";
                $fieldvals[] = (!empty($data['len'])) ? $data['len'] : 1;
                break;
            case 'ndropdown':
                $fieldvals[3] = 'dropdown';
                $data['type'] = 'number';
                break;
            case 'nndropdown':
                $fieldvals[3] = 'dropdown';
                $data['type'] = 'nnumber';
                break;
            default:
                break;
        }
        $resto = ['searchable', 'required', 'editable', 'order', 'api', 'report', 'base', 'front','descen'];
        foreach ($resto as $r) {
            if (!empty($data[$r])) {
                $fields .= ",`".$r."`";
                $fieldmarks .= ",?";
                $fieldvals[] = $data[$r];
            }
        }
        //TODO: Revisar esta parte: de momento se comentara
        // Si es un campo dependiente mayor que primer nivel, se actualiza el campo anterior
        /*if ($data['depend']>1) {
            $query = $this->db->query("SELECT id from form_fields where `id_form` = ? and `depend` = ? and `values` = ? limit 1",
                array($data['id_form'], (((int)$data['depend'])-1), $values)
            );
            $toupd = $query->row();
            $this->db->query("UPDATE form_fields set descen = ? where id = ?", array($slug, $toupd->id));
        }*/
        if (file_exists(APPPATH.'/views/form/form_'.$data['id_form'].'.php')) {
            unlink(APPPATH.'/views/form/form_'.$data['id_form'].'.php');
        }
        $this->db->query("INSERT into `form_fields` (" . $fields . ") values (" . $fieldmarks . ")", $fieldvals);
        $fieldef = $this->definicionTablaCampo($this->db->insert_id());
        if ($data['type'] != 'separador' && $data['type'] != 'tabla') {
            return $this->db->query("ALTER TABLE formd_" . $data['id_form'] . " ADD `" . $fieldvals[2] . "` " . $fieldef);
        }

        return true;
    }

    public function updatec() {
        $data  = $this->input->post();
        if (empty($data['id']) || empty($data['id_form']) || empty($data['name']) || empty($data['type'])) {
            return false;
        }
        $sets = "`name`=?, `slug`=?, `values`=?, `type`=?, `depend`=?, `len`=?, `searchable`=?, `required`=?, `api`=?, `front`=?, `editable`=?, `report`=?, `order`=?";
        $slug  = (empty($data['slug']))       ? slugify($data['name']) : $data["slug"];
        $valores = array($data['name'], $slug, $data['values'], $data['type'], $data['depend'], NULL, '0', '0', '0', '0', '0', '0', '0', $data['id']);
        if(!empty($data['len']))       $valores[5] = $data['len'];
        if(isset($data['searchable'])) $valores[6] = '1';
        if(isset($data['required']))   $valores[7] = '1';
        if(isset($data['api']))        $valores[8] = '1';
        if(isset($data['front']))      $valores[9] = '1';
        if(isset($data['editable']))   $valores[10] = '1';
        if(isset($data['report']))     $valores[11] = '1';
        if(!empty($data['order']))     $valores[12] = $data['order'];
        $oldfield = $this->db->query("SELECT * from form_fields where id = ?", [$data['id']])->row();
        $this->db->query("UPDATE `form_fields` SET $sets WHERE `id`=?", $valores);
        if ($data['type'] != 'separador' && $data['type'] != 'tabla' && ($oldfield->slug != $slug || $oldfield->type != $data['type'] || $oldfield->len != $valores[5])) {
            $paso = $this->db->query("ALTER TABLE `formd_".$data['id_form']."` CHANGE `".$oldfield->slug."` `".$slug."` ".$this->definicionTablaCampo($data['id']));
            if (!$paso) return false;
        }
        if ($data['depend']>1 && !empty($data['values'])) {
            $query = $this->db->query("SELECT id from form_fields where `id_form` = ? and `depend` = ? and `values` = ? limit 1",
                array($data['id_form'], (((int)$data['depend'])-1), $data["values"])
            );
            $toupd = $query->row();
            $paso = $this->db->query("UPDATE form_fields set descen = ? where id = ?", array($slug, $toupd->id));
            if (!$paso) return false;
        }
        if (file_exists(APPPATH.'/views/form/form_'.$data['id_form'].'.php')) {
            unlink(APPPATH.'/views/form/form_'.$data['id_form'].'.php');
        }

        return true;
    }

    private function getOldRowTbr($id) {
        $query = $this->db->query("SELECT tbr.*, ff.depend
        FROM form_fields_tbr tbr
        JOIN form_fields ff ON ff.id = tbr.id_form_fields
        WHERE tbr.id =?", [$id]);
        $res = $query->row();
        return $res;
    }

    public function getdatatbr($fid = 0, $id_ticket = 0) {
        //Creamos la estructura de la data
        $data = [
            'headers' => [],
            'deleted' => [],
            'ff_ids' => [],
            'otros' => [],
        ];
        $data['fid'] = $fid;
        //Buscamos las tablas relacionales de este formulario
        $sql = "SELECT ff.id, ff.id_form, ff.depend
        FROM form_fields ff
        WHERE ff.id_form = ?
        AND descen = 'ttbbrr'
        ORDER BY ff.order;";
        $tablas = $this->db->query($sql, [$fid])->result();
        if( $tablas ) {
            $headers = $this->db->query("SELECT id_form_fields, `name`, slug, `type`, `required`
            FROM form_fields_tbr
            WHERE id_form = ?
            ORDER BY id_form_fields ASC, `order` ASC, `name` ASC", [$fid])->result();
            $data['headers'] = $headers;
            foreach($tablas as $tabla_ff) {
                $data['ff_ids'][] = $tabla_ff->id;
                $index_table = $tabla_ff->depend;
                $tabla = 'formd_'.$fid.'_tb'.$index_table;
                $data[$tabla_ff->id] = $this->db->query('SELECT * FROM '.$tabla.' WHERE id_formd = ?', [$id_ticket])->result();
            }
        }

        return $data;
    }

    //Calcula el contador a usar de las tablas dependientes fomrd_##_dep? desde 0 hasta n
    public function get_number_table_dep($id_form) {
        $query = $this->db->query("SELECT COALESCE(MAX(depend), -1) max
            FROM (
                SELECT ff.depend
                FROM form_fields ff
                WHERE ff.id_form = ?
                AND ff.descen = 'ddeepp'
                UNION
                SELECT fftbr.depend
                FROM form_fields_tbr fftbr
                WHERE fftbr.id_form = ?
                AND fftbr.descen = 'ddeepp'
            ) as a", [$id_form, $id_form]
        );
        $max = $query->row()->max + 1;
        
        return $max;
    }

    public function createctbr( $data = false ) {
        $data = (empty($data)) ? $this->input->post() : $data;
        $req = (empty($data['required'])) ? 0 : 1;
        $des = (empty($data['des'])) ? "" : $data['des'];
        $id_form = $data['id_form'];
        $slug = slugify($data['name']);
        //obtenemos informacion del campo de la tbr
        $tbr = $this->db->query("SELECT * FROM form_fields WHERE id = ?", [$data['id_form_field']])->row();
        $depend = $tbr->depend;
        if ($data['type'] == 'dropdown' && empty($data['values'])) {
            $values = "default";
        } else {
            $values = $data['values'];
        }
        $tipoff = $data['type'];
        //El campo datetime_pdf_update no puede ser manipulado
        if( $tipoff == 'datetime_pdf_update' ) {
            return false;
        }
        if ($data['type'] == 'ndropdown') { $tipoff = 'dropdown'; $data['type']='number'; }
        if ($data['type'] == 'nndropdown') { $tipoff = 'dropdown'; $data['type']='nnumber'; }
        $nullable = "";
        $this->db->trans_start();
        if ( $data['type'] == 'separador' ) {
            $slug = "sep_" . $slug;
        } else {
            if ($data['type']=='text' || $data['type']=='textarea') {
                $nullable = " NOT NULL";
            }
            $sql = "ALTER TABLE formd_".$id_form."_tb".$depend." ADD `" . $slug . "` " . $this->tipos($data['type']) . $nullable;
            $this->db->query($sql);
            if( $data['type']=='datetime_pdf') { // Agregaremos su campo secundario para controlar la fecha de actualizacion de datos de envio pdf
                $sql = "ALTER TABLE formd_".$id_form."_tb".$depend." ADD `" . $slug . "_update` " . $this->tipos($data['type'].'_update') . $nullable;
                $this->db->query($sql);
            }
        }
        //TODO: Revisar esta parte: de momento se comentara
        // Si es un campo dependiente mayor que primer nivel, se actualiza el campo anterior
        /*if ($data['depend']>1) {
            $query = $this->db->query("SELECT id from form_fields where `id_form` = ? and `depend` = ? and `values` = ? limit 1",
                array($data['id_form'], (((int)$data['depend'])-1), $values)
            );
            $toupd = $query->row();
            $this->db->query("UPDATE form_fields set descen = ? where id = ?", array($slug, $toupd->id));
        }*/
        if (file_exists(APPPATH.'/views/form/form_'.$data['id_form'].'.php')) {
            unlink(APPPATH.'/views/form/form_'.$data['id_form'].'.php');
        }
        $this->db->query("INSERT into `form_fields_tbr`
            (`id_form`, `id_form_fields`, `name`, `slug`, `type`, `required`, `order`, `values`, `depend`, `descen`)
            values (?,?,?,?,?,?,?,?,?,?)",
            array($id_form, $data['id_form_field'], $data['name'], $slug, $tipoff, $req, $data['order'], $values, $data['depend'], $des)
        );
        if( $data['type']=='datetime_pdf') { // Agregaremos su campo secundario para controlar la fecha de actualizacion de datos de envio pdf
            $this->db->query("INSERT into `form_fields_tbr`
                (`id_form`, `id_form_fields`, `name`, `slug`, `type`, `required`, `order`, `values`, `depend`, `descen`)
                values (?,?,?,?,?,?,?,?,?,?)",
                array($id_form, $data['id_form_field'], $data['name'].' actualización', $slug.'_update', $tipoff.'_update', $req, $data['order']+1, $values, $data['depend'], $des)
            );
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $success = false;
        } else {
            $success = true;
            $this->db->trans_commit();
        }

        return $success;
    }

    public function updatectbr() {
        $data = $this->input->post();
        //El campo datetime_pdf_update no puede ser manipulado
        if( $data['type'] == 'datetime_pdf_update' ) {
            return false;
        }
        $req     = (isset($data['required'])) ? 1 : 0;
        $edi     = (isset($data['editable'])) ? 1 : 0;
        $slug    = slugify($data['name']);
        $old_row = $this->getOldRowTbr($data['id']);
        $oldSlug = $old_row->slug;
        $depend  = $old_row->depend;
        if ($data['type'] == 'dropdown' && empty($data['values']) ) {
            $values = "default";
        } else {
            $values = $data['values'];
        }
        $this->db->trans_start();

        if ($data['type'] != 'separador') {
            $this->db->query("ALTER TABLE `formd_".$data['id_form']."_tb".$depend."` CHANGE `".$oldSlug."` `".$slug."` ".$this->tipos($data['type']));
            if( $data['type']=='datetime_pdf') {
                $this->db->query("ALTER TABLE `formd_".$data['id_form']."_tb".$depend."` CHANGE `".$oldSlug."_update` `".$slug."_update` ".$this->tipos($data['type'].'_update'));
            }
        }
        if (file_exists(APPPATH.'/views/form/form_'.$data['id_form'].'.php')) {
            unlink(APPPATH.'/views/form/form_'.$data['id_form'].'.php');
        }
        $this->db->query("UPDATE form_fields_tbr set name=?, slug=?, type=?, `required`=?, `editable`=?,
            `order`=?, `values`=? where id=?",
        array($data['name'], $slug, $data['type'], $req, $edi, $data['order'], $values, $data['id']));
        if( $data['type']=='datetime_pdf') {
            $this->db->query( "UPDATE form_fields_tbr set name=?, slug=?, type=?, `required`=?, `editable`=?,
                `order`=?, `values`=? where slug=? AND id_form=? AND id_form_fields=?",
                array($data['name'].' actualización', $slug.'_update', $data['type'].'_update', $req, $edi, $data['order']+1, $values,
                $old_row->slug.'_update', $old_row->id_form, $old_row->id_form_fields
            ));
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $success = false;
        } else {
            $success = true;
            $this->db->trans_commit();
        }

        return $success;
    }

    //Elimina un campo por id de una tabla relacional
    public function deletectbr($id, $fid, $name) {
        $slug = slugify($name);
        $sslug = "sep_" . $slug;
        //En caso de ser este campo, no se eliminara nada, ya que no esta presente en otras tablas, y retornamos true
        if( $name == 'active_system_row' ) return true; 
        $query = $this->db->query("SELECT fftbr.*, ff.depend
        FROM form_fields_tbr fftbr 
        JOIN form_fields ff ON ff.id = fftbr.id_form_fields
        WHERE fftbr.id = ? 
        AND fftbr.id_form = ? 
        AND (fftbr.slug = ? OR fftbr.slug = ?)",
        [$id, $fid, $slug, $sslug]);
        $campo = $query->row();
        $index_table = $campo->depend;
        $this->db->trans_start();

        if ($campo->type != "separador") {
            $this->db->query("ALTER TABLE formd_".$fid."_tb".$index_table." DROP `".$slug."` ");
            if( $campo->type == 'datetime_pdf') {
                $this->db->query("ALTER TABLE formd_".$fid."_tb".$index_table." DROP `".$slug."_update` ");
            }
        }
        if (file_exists(APPPATH.'/views/form/form_'.$fid.'.php')) {
            unlink(APPPATH.'/views/form/form_'.$fid.'.php');
        }
        $this->db->query("DELETE FROM form_fields_tbr WHERE id = ?", [$campo->id]);
        $this->db->query("DELETE FROM form_fields_tbr WHERE slug = ? AND id_form=? AND id_form_fields=?", [$campo->slug.'_update', $campo->id_form, $campo->id_form_fields]);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $success = false;
        } else {
            $success = true;
            $this->db->trans_commit();
        }

        return $success;
    }

    //elimina un campo por id_form_field de una tabla relacional
    public function deletectbr_by_iff($id_form_fields, $fid, $name) {
        $slug = slugify($name);
        $sslug = "sep_" . $slug;
        //En caso de ser este campo, no se eliminara nada, ya que no esta presente en otras tablas, y retornamos true
        if( $name == 'active_system_row' ) return true; 
        $query = $this->db->query("SELECT fftbr.*, ff.depend
        FROM form_fields_tbr fftbr 
        JOIN form_fields ff ON ff.id = fftbr.id_form_fields
        WHERE fftbr.id_form_fields = ? 
        AND fftbr.id_form = ? 
        AND (fftbr.slug = ? OR fftbr.slug = ?)",
        [$id_form_fields, $fid, $slug, $sslug]);
        $campo = $query->row();
        $index_table = $campo->depend;
        if ($campo->type != "separador") {
            $this->db->query("ALTER TABLE formd_".$fid."_tb".$index_table." DROP `".$slug."` ");
        }
        if (file_exists(APPPATH.'/views/form/form_'.$fid.'.php')) {
            unlink(APPPATH.'/views/form/form_'.$fid.'.php');
        }

        return $this->db->query("DELETE FROM form_fields_tbr WHERE id = ?", [$campo->id]);
    }

    private function getOldname($id) {
        $query = $this->db->query("SELECT `slug` FROM form_fields WHERE id =?", array($id));
        $res = $query->row();
        return $res->slug;
    }

    public function deletec($fid, $name, $cid = 0) {
        if ($cid > 0) {
            $query = $this->db->query("SELECT * FROM form_fields WHERE id = ?", [$cid]);
        } else {
            $slug = slugify($name);
            $sslug = "sep_" . $slug;
            if( $name == 'active_system_row' ) return true; //En caso de ser este campo, no se eliminara nada, ya que no esta presente en otras tablas, y retornamos true
            $query = $this->db->query("SELECT * FROM form_fields WHERE id_form = ? AND (slug = ? OR slug = ?)",
                [$fid, $slug, $sslug]);
        }
        $campo = $query->row();
        if( $campo->type == 'tabla' ) {
            //eliminamos campo de form_fields_tbr pertenecientes a la tabla relacional
            $this->db->query("DELETE FROM form_fields_tbr WHERE id_form_fields = ?", [$campo->id]);
            //eliminamos la tabla relacional formd_#_tb#
            $this->db->query("DROP TABLE IF EXISTS `formd_".$campo->id_form."_tb".$campo->depend."`");
        }
        else if ($campo->type != "separador") {
            $this->db->query("ALTER TABLE formd_".$fid." DROP `".$slug."` ");
        }
        if (file_exists(APPPATH.'/views/form/form_'.$fid.'.php')) {
            unlink(APPPATH.'/views/form/form_'.$fid.'.php');
        }
        return $this->db->query("DELETE FROM form_fields WHERE id = ?", [$campo->id]);
    }

    public function backup_table($table) {
        $query = $this->db->query("SHOW TABLES LIKE '".$table."'");
        if ($query->num_rows() > 0) {
            return $this->db->query("ALTER TABLE `".$table."` RENAME TO `".$table."_bak`");
        }
        return false;
    }

    public function generate($idForm) {
        $this->db->query("INSERT IGNORE INTO form_fields (id_form, name, slug, type, len, searchable, required, editable, base, api, report)
            values (?,?,?,?,?,?,?,?,?,?,?)", [$idForm, 'ID', 'id', 'number', 11, 0, 1, 0, 1, 0, 1]);
        $this->db->query("INSERT IGNORE INTO form_fields (id_form, name, slug, type, len, searchable, required, editable, base, api, report, front)
            values (?,?,?,?,?,?,?,?,?,?,?,?)", [$idForm, 'Creado por', 'id_user', 'number', 11, 1, 1, 0, 1, 0, 1, 0]);
        $this->db->query("INSERT IGNORE INTO form_fields (id_form, name, slug, type, required, editable, base, api, report)
            values (?,?,?,?,?,?,?,?,?)", [$idForm, 'Apertura', 'apertura', 'datetime', 1, 0, 1, 0, 1]);
        $this->db->query("INSERT IGNORE INTO form_fields (id_form, name, slug, type, len, editable, base, api, report, front)
            values (?,?,?,?,?,?,?,?,?,?)", [$idForm, 'Caller ID', 'uniqueid', 'text', 32, 0, 1, 0, 1, 1]);
        $this->db->query("INSERT IGNORE INTO form_fields (id_form, name, slug, type, len, editable, base, api, report, front)
            values (?,?,?,?,?,?,?,?,?,?)", [$idForm, 'Linkedid', 'linkedid', 'text', 32, 0, 1, 0, 0, 0]);
        $this->backup_table("formd_".$idForm);
        $comando = "CREATE TABLE `formd_$idForm` (`id` int(11) NOT NULL AUTO_INCREMENT,
            `id_user` int(11), `apertura` datetime, `uniqueid` varchar(32), `linkedid` varchar(32),
            PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $this->session->set_flashdata('info', 'Formulario generado.');
        $this->db->query("UPDATE form set last_update=now() where id = ?", [$idForm]);

        return $this->db->query($comando);
    }

    public function generate_field_html($field, $id_campaign, $extra_options = []) {
        $extra_class    = (isset($extra_options['class']))    ? $extra_options['class'].' ' : '';
        $extra_atributo = (isset($extra_options['atributo'])) ? $extra_options['atributo']  : '';
        $extra_atributo .= ($field->editable == 0) ? " readonly" : "";
        $extra_atributo .= ($field->required == 1) ? " required" : "";
        $html  = "";
        switch ($field->type) {
            case "textarea":
                $html .= "<div class='form-group'>\n".
                    "<label for='$field->slug'>$field->name</label>\n".
                    "<textarea class='".$extra_class."form-control' $extra_atributo id='f$field->id_form"."_$field->slug' name='$field->slug' <?php if($"."dis=='3') echo \"readonly\" ?>><?php echo (!empty($"."registro->".$field->slug.")) ? $"."registro->".$field->slug." : ''; ?></textarea>\n".
                    "</div>";
                break;
            case "checkbox":
                $html = "<div class='form-check form-group'>
                        <input type='checkbox' class='".$extra_class."form-check-input' $extra_atributo id='f$field->id_form"."_$field->slug' name='$field->slug' <?php if($"."dis=='3') echo \"readonly\" ?> <?php echo (!empty($"."registro->".$field->slug.")) ? 'checked' : ''; ?>>
                        <label class='form-check-label' for='$field->slug'>$field->name</label>
                    </div>";
                break;
            case "dropdown":
                $html = "\t\t<div class='input-group mb-2 mr-2'>\n".
                    "\t\t\t<div class='input-group-prepend'>\n".
                    "\t\t\t\t<label class='input-group-text d-block text-right' for='$field->slug'>$field->name</label>\n".
                    "\t\t\t</div>\n";
                $clase = $descen = $parent = $opts = $masdata = "";
                if (substr($field->values, 0, 3) == "cat") {
                    $opts .= "\t\t\t\t<option value='0' data-id='0' selected>-- Elige --</option>\n";
                    if (substr($field->values, 0, 4) == "cat_") {
                        $parent = substr($field->values, 4);
                        $masdata .= " data-parent='".$parent."'";
                    } else {
                        $parent = "padrecero";
                    }
                    $opts .= "\t\t\t\t<?php $"."field_par_id = $".$parent."_id; $".$field->slug."_id = 0; foreach($"."cats as $"."cat): if($"."cat->field == '$field->slug' && $"."cat->parent == $"."field_par_id): ?>\n";
                    $opts .= "\t\t\t\t\t<option value='<?=$"."cat->val?>' data-id='<?=$"."cat->id?>'<?php if($"."cat->val==($"."registro->$field->slug ?? '')) { echo ' selected'; $".$field->slug."_id = $"."cat->id; } ?>><?=$"."cat->eti?></option>\n";
                    $opts .= "\t\t\t\t<?php endif; endforeach; ?>\n";
                    if ($field->depend > 0) {
                        $clase .= " tienedep";
                        $masdata .= " data-tabla='formd_".$field->id_form."_cats'";
                    }
                    $field->depend = 0;
                } else if ($this->isJson($field->values)) {
                    $values = json_decode($field->values, true);
                    $profundo = $this->arrayDepth($values);
                    if ($profundo == 2) {
                        $descen = key($values);
                        $values = $values[$descen];
                        $clase = " condescen";
                    } elseif ($profundo==3) {
                        $values = reset($values);
                        foreach ($values as $key => $val1) {
                            foreach ($val1 as $val) {
                                $opts .= "\t\t\t\t<option data-parent='$key' value='$val' <?php echo (($"."registro->".$field->slug." ?? '')=='$val') ? 'selected' : ''; ?>>$val</option>\n";
                            }
                        }
                    }
                } else if (strpos($field->values, ',')) {
                    $values = explode(",", str_replace(", ",",",preg_replace('!\s+!', ' ',trim($field->values))));
                } else {
                    $opts .= "\t\t\t\t<option value=''>-- Elige --</option>\n";
                    $values = "";
                    if (substr($field->values, 0, 4) == 'cat_') {
                        $values = $this->db->query("SELECT val as id, eti as val FROM `formd_" . $field->id_form .
                            "cats` WHERE `field` = ? ORDER BY `seq`, `eti`",
                        [substr($field->values, 5)]);
                    } else if ($field->depend < 2) {
                        $values = $this->getOpciones($field->values, $id_campaign);
                    }
                    foreach ($values as $key => $estaop) {
                        $opts .= "\t\t\t\t<option value='$estaop->id' <?php echo (!empty($"."registro->".$field->slug.") && $"."registro->".$field->slug."=='$estaop->id') ? 'selected' : ''; ?>>$estaop->val</option>\n";
                    }
                }
                if ($field->depend > 0) { $clase .= " ajaxdep"; }
                $html .= "\t\t\t<select class='".$extra_class."form-control".$clase."' $extra_atributo data-depend='$field->depend' data-descen='$field->descen' ".
                    "data-descencol='". (((int)$field->depend) + 1) ."' $masdata data-descencampo='$field->values' id='f$field->id_form"."_$field->slug' ".
                    "data-presel='<?=$"."registro->".$field->slug." ?: ''?>' ".
                    "name='$field->slug' <?php if($"."dis=='3') echo \"readonly\" ?>>\n";
                if (empty($opts)) {
                    $opts = "\t\t\t\t<option value=''>-- Elige --</option>\n";
                    foreach ($values as $key => $val) {
                        $opts .= "\t\t\t\t<option value='$val' <?php echo (!empty($"."registro->".$field->slug.") && $"."registro->".$field->slug."=='$val') ? 'selected' : ''; ?>>$val</option>\n";
                    }
                }
                $html .= $opts;
                $html .= "\t\t\t</select>\n\t\t</div>\n";
                break;
            case "radio":
                $values = explode(",", str_replace(", ",",",preg_replace('!\s+!', ' ',trim($field->values))));
                $html = "<div class='form-group'><div class='form-check form-check-inline'><label>$field->name : </label></div>";
                foreach ($values as $val) {
                    $html .= "<div class='form-check form-check-inline'>\n".
                        "<input class='".$extra_class."form-check-input' $extra_atributo type='radio' name='$field->slug' value='$val' <?php echo (!empty($".
                        "registro->".$field->slug.") && $"."registro->".$field->slug."=='$val') ? 'checked' : ''; ?> <?php if($"."dis=='3') echo \"readonly\" ?>>\n".
                        "<label class='form-check-label' for='$field->slug'>$val</label>\n</div>\n";
                }
                $html .= "</div>";
                break;
            case "datetime":
                $html = "\t\t<div class='input-group mb-2 mr-2'>\n".
                    "\t\t\t<div class='input-group-prepend'>\n".
                    "\t\t\t<label class='input-group-text' for='$field->slug'>$field->name</label>\n</div>\n".
                    "\t\t\t<input type='datetime-local' class='".$extra_class."form-control date' $extra_atributo name='$field->slug' <?php if($"."dis=='3') echo \"readonly\" ?> value=\"<?php echo (!empty($"."registro->".$field->slug.")) ? implode('T', explode(' ',$"."registro->".$field->slug.")) : '' ?>\">\n".
                    "\t\t</div>\n";
                break;
            case 'datetime_pdf':
                $html = "<input type='hidden' id='f$field->id_form"."_$field->slug' name='$field->slug'>\n";
                break;
            case 'datetime_pdf_update':
                $html = "<input type='hidden' id='f$field->id_form"."_$field->slug' name='$field->slug'>\n";
                break;
            case 'boton':
                $html = "\t\t<div class='w-100 mb-2 text-center'>\n".
                    "\t\t\t<input type='hidden' name='$field->slug' value=\"<?php echo (!empty($"."registro->".$field->slug.")) ? htmlspecialchars($"."registro->".$field->slug.", ENT_QUOTES, 'UTF-8') : ''; ?>\">\n".
                    "\t\t\t<button type='button' class='btn btn-info formActBtn' id='f$field->id_form"."_$field->slug'>$field->name</button>\n".
                    "\t\t</div>\n";
                break;
            default:
                $clydata = (!empty($field->descen)) ? (($field->descen=="ddeepp") ? " ddeepp' data-ddep='$field->depend'" : " contdep' data-tdep='$field->descen'") : "'";
                $html = "\t\t<div class='input-group mb-2 mr-2'>\n".
                    "\t\t\t<div class='input-group-prepend'>\n".
                    "\t\t\t\t<label class='input-group-text d-block text-right' for='$field->slug'>$field->name</label>\n".
                    "\t\t\t</div>\n".
                    "\t\t\t<input type='text' class='".$extra_class."form-control$clydata id='f$field->id_form"."_$field->slug' $extra_atributo name='$field->slug' placeholder='".
                    $field->name."' value='<?php echo (isset($"."registro->".$field->slug.")) ? htmlspecialchars($"."registro->".$field->slug.", ENT_QUOTES, 'UTF-8') : ''; ?>' <?php if($"."dis=='3') echo \"readonly\" ?>>\n".
                "\t\t</div>\n";
                break;
        }
        return $html;
    }

    public function getOpciones($name, $id_campaign = false) {
        if($name == 'user') {
            $quienes = $this->datos_model->getRelUsers(["cam"=>$id_campaign]);
            $res = array();
            foreach ($quienes as $key => $row) {
                $res[] = (object)array('id'=>$row->id, 'val'=>$row->name.' '.$row->last);
            }
        } else if($name == 'client') {
            $query = $this->db->query("SELECT id, concat(name,' ',last) as val
                from client where id_campaign is null or id_campaign = $id_campaign order by last, name");
            $res = $query->result();
        } else {
            $query = $this->db->query("SELECT DISTINCT(val1) AS val FROM disp_depend WHERE campo='$name'");
            $quienes = $query->result();
            $res = array();
            foreach ($quienes as $key => $row) {
                $res[] = (object)array('id'=>$row->val, 'val'=>$row->val);
            }
        }

        return $res;
    }

    public function formulario($data) {
        $form = $this->get_one($data['formulario']);
        $tabla = "formd_".$data['formulario'];
        $res = array();
        $res['campos'] = $this->get_fields($data['formulario']);
        $camposquery = "COALESCE(date_format(fd.apertura, '$this->dtfor'),'') Fecha,
            COALESCE(ce.cid_num,'') Teléfono, COALESCE(fd.linkedid,'') linkedid";
        $cuantos = (count($res['campos'])>6 && $data["pag"]!=="x") ? 6 : count($res['campos']);
        if ($cuantos>0) {
            for ($i=0; $i < $cuantos; $i++) {
                if ($res['campos'][$i]->type != 'separador') {
                    $camposquery .= ", COALESCE(fd.".$res['campos'][$i]->slug.",'') ".$res['campos'][$i]->slug;
                }
            }
        }
        $camposquery .= ($data["pag"]==="x") ? ", COALESCE(concat(u.name,' ',u.last), '') agente" : "";
        $prequery = "SELECT $camposquery
            FROM $tabla AS fd
            LEFT JOIN call_entry AS ce ON ce.uniqueid = fd.linkedid
            LEFT JOIN user u ON u.id = fd.id_user
            WHERE date(fd.apertura) BETWEEN '".$data['min']."' AND '".$data['max']."'";
        $query = $this->db->query($prequery);
        $data["cuenta"] = $query->num_rows();
        $data["campos"] = $query->list_fields();
        if ($data["pag"]==="x") {
            $data["data"] = $query->result_array();
            return $data;
        }
        $limites = " limit " . $data['pag'] . ", " . $data['rpp'];
        $query = $this->db->query($prequery . $limites);
        $data["data"] = $query->result();

        return $data;
    }

    public function delform($idForm) {
        $this->db->query("DROP TABLE IF EXISTS `formd_".$idForm."_file`");
        $this->db->query("DROP TABLE IF EXISTS `formd_".$idForm."_crm`");
        $this->db->query("DROP TABLE IF EXISTS `formd_".$idForm."`");
        $this->db->query("DELETE FROM `form_fields` where `id_form` = ?", [$idForm]);
        $res = $this->db->query("DELETE from `form` where `id` = ?", [$idForm]);
        unlink(APPPATH."views/form/form_".$idForm.".php");
        return $res;
    }

    public function getddeepp($data) {
        $fdt = (empty($data['ddep'])) ? "" : $data['ddep'];
        //Obtenemos el nombre de la tabla dependiente
        $tabla = "formd_" . $data['fid'] . "_dep" . $fdt;
        //Reestructuramos la tabla en caso de ser necesario
        $this->dep_structure_update($tabla, $data['fid']);
        $query = $this->db->query("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS`
            WHERE `TABLE_NAME`='$tabla'");
        $in = $query->row()->COLUMN_NAME; // índice primer columna
        $query = $this->db->query("SELECT *, count(*) AS 'n___rows' FROM `$tabla` where $in = ? AND active_system_row = 1", [$data['val']]);
        $json['ddeepp'] = $query->row();
        $json['activador'] = $in;
        $json['depasig'] = $this->depasig_list($data['fid']);

        return $json;
    }

    function getdep($data) : string {
        // parent tabla campo val
        $ret = "";
        $filas = $this->db->query("SELECT id, val, eti FROM `$data[tabla]`
            WHERE `parent` = ?", [$data["parent"]])->result();
        foreach ($filas as $fila) {
            $ret .=  "<option value='$fila->val' data-id='$fila->id'>$fila->eti</option>\n";
        }

        return $ret;
    }

    // Obtene la informacion de las tablas dependientes de un formulario
    public function get_tables_dep($fid = 0) {
        $table_name = 'formd_'.$fid.'_dep';
        $sql = "SELECT `depend`, `name`, `slug`,  IF(depend=0, '$table_name', CONCAT('$table_name', depend)) AS `table_name`
            FROM 
            (
                SELECT `name`, `slug`, `depend`
                FROM form_fields
                WHERE id_form = $fid
                AND descen = 'ddeepp'
                UNION
                SELECT `name`, `slug`, `depend`
                FROM form_fields_tbr
                WHERE id_form = $fid
                AND descen = 'ddeepp'
            ) t
            ORDER BY t.depend
        ;";

        return $this->db->query($sql)->result();
    }


    private function isJson($string) {
        $primero = substr($string, 0, 1);
        if ($primero != "{" && $primero != "[") { return false; }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    private function arrayDepth(array $array) {
        $maxDepth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = $this->arrayDepth($value) + 1;
                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }
        return $maxDepth;
    }

    //APARTADO DE FORMD_#_DEPASIG
    public function table_exist($table_name = '') {
        if ($this->db->table_exists($table_name))
            return true;
        else
            return false;
    }

    public function depasig_createtable($table_name) {
        $tabla = "CREATE TABLE $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            activador varchar(100) NOT NULL,
            campo varchar(100) NOT NULL,
            copia varchar(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $this->db->query($tabla);
    }

    //Carga los campos de la tabla dependiente de acuerdo al activador seleccionado
    public function depasig_fields($table) {
        $resp = $this->db->list_fields($table);

        return $resp;
    }

    public function depasig_list($id_form = 0) {
        $table_name = 'formd_'.$id_form.'_depasign';
        //validamos si existe la tabla
        if( $this->form_model->table_exist($table_name) ) {
            return $this->db->query("SELECT * FROM $table_name order by activador")->result();
        }
        else {
            return [];
        }
    }

    public function depasig_save($id_form, $data = [], $id = 0) {
        if( $id == 0 ){
            if ($this->db->insert('formd_'.$id_form.'_depasign', $data)) {
                return 'Registro agregado correctamente.';
            } else {
                return FALSE;
            }
        } else {
            $where = ['id' => $id];
            $this->db->update('formd_'.$id_form.'_depasign', $data, $where);
            return 'Registro actualizado correctamente.';
        }
    }

    public function depasig_delete($id_form = 0, $id = 0) {
        $table_name = 'formd_'.$id_form.'_depasign';
        $this->db->where('id', $id);
        $this->db->delete($table_name);

        return 'Registro eliminado.';
    }
    //APARTADO DE FORMD_#_DEPASIG

    /*TABLAS DEPENDIENTES*/
    public function loadforms( $id_campaign = 0 ) {
        $query = $this->db->query("SELECT id, name
        FROM form 
        WHERE id_campaign = ?
        AND active = 1
        ORDER BY name", [$id_campaign]);

        return $query->result();
    }

    //Reestruccturacion de tabla dependientes, Agregando columna active_system_row, en caso de que esta no este presente
    public function dep_structure_update($table_name = '', $id_form = '') {
        $result = false;
        if( $table_name != '' && $id_form != '') {
            $exist = $this->db->field_exists('active_system_row', $table_name);
            $result = true;
            if( !$exist ) { //Si no existe la columna, la agregamos
                $result = $this->db->query("ALTER TABLE $table_name ADD COLUMN active_system_row tinyint NOT NULL DEFAULT 1");
            }
        }

        return $result;
    }

    public function loaddepend($id_form = 0) {
        $validacion = "";
        if( $this->udata['perfil'] != 'admin')
            $validacion = "WHERE id_campaign IN (".$this->udata['campanas'].")";
        //Buscamos dentro del formulario proporcionado si existe algun campo dependiente y tomamos su name y depend
        $query = $this->db->query("SELECT *
        FROM (
            SELECT ff.slug, ff.name, f.name AS form_name, f.id_campaign
            FROM form_fields ff
            JOIN form f ON ff.id_form = f.id
            WHERE ff.id_form=?
            AND ff.descen='ddeepp'
            UNION    
            SELECT ff.slug, ff.name, f.name AS form_name, f.id_campaign
            FROM form_fields_tbr ff
            JOIN form f ON ff.id_form = f.id
            WHERE ff.id_form = ?
            AND ff.descen = 'ddeepp'
        ) a
        $validacion", [$id_form,$id_form]);

        return $query->result();
    }

    public function get_name_table_depen($id_form = '', $slug_key ='') {
        $table = false;
        if( !empty( $id_form ) && !empty( $slug_key ) ) {
            $validacion = "";
            if( $this->udata['perfil'] != 'admin')
                $validacion = "AND t.id_campaign IN (".$this->udata['campanas'].")";
            $query = $this->db->query("SELECT t.depend, t.slug, t.id_form, t.id_campaign
            FROM (
                SELECT ff.depend, ff.slug, ff.id_form, f.id_campaign
                FROM form_fields ff
                JOIN form f ON ff.id_form = f.id
                WHERE ff.descen = 'ddeepp'
                UNION    
                SELECT ff.depend, ff.slug, ff.id_form, f.id_campaign
                FROM form_fields_tbr ff
                JOIN form f ON ff.id_form = f.id
                WHERE ff.descen = 'ddeepp'
            ) t
            WHERE t.id_form = ?
            AND t.slug = ? $validacion", [$id_form, $slug_key]);
            $row = $query->row();
            if( !empty($row) ) {
                $end = ($row->depend == 0) ? '' : $row->depend;
                $table = 'formd_'.$id_form.'_dep'.$end;
            }
        }

        return $table;
    }

    public function datadep_list($array = []) {
        $table      = $array['table'];
        $pag        = $array['pag'];
        $rpp        = $array['rpp'];
        $bus        = $array['bus'];
        $slug_key   = $array['slug_key'];
        $query = $this->db->query("SELECT COUNT(*) tot FROM $table WHERE $slug_key LIKE '%$bus%'");
        $data['tot'] = $query->row()->tot;
        $data['rows'] = $this->db->query("SELECT * FROM $table WHERE $slug_key LIKE '%$bus%' LIMIT ?, ?", [$pag, $rpp])->result();
        $data['pag'] = $pag;
        $data['rpp'] = $rpp;
        
        return $data;
    }

    public function datadep_insert($table = '', $data_form = []) {
        $resp = $this->db->insert($table, $data_form);
        
        return ($resp) ? 'Registro agregado correctamente' : ['error' => 'Error: No se pudo agregar el registro'];
    }

    public function datadep_update($table = '', $data_form = [], $where = ['id' => 0]) {
        $resp = $this->db->update($table, $data_form, $where);

        return ($resp) ? 'Registro actualizado correctamente' : ['error' => 'Error: No se pudo actualizar el registro'];
    }

    public function datadep_delete($table = '', $where = []) {
        $resp = $this->db->delete($table, $where);
        
        return ($resp) ? 'Registro eliminado correctamente' : ['error' => 'Error: No se pudo eliminar el registro'];
    }
    /*TABLAS DEPENDIENTES*/
    
    /*CAMPOS CALCULADOS*/
    //Obtiene la lista completa de campos de un formulario, tanto de tablas dependientes como de tablas relacionales en un array por tabla
    public function get_fields_form_by_table($fid = 0) {
        $datos = [];
        $query = $this->db->query("SELECT id, id_form, name, slug, `order`, depend, descen, id AS id_form_field
            FROM form_fields
            WHERE id_form='$fid'
            ORDER BY `order`, `name`
        ");
        $campos = $query->result();
        $datos[] = [
            'table' => 'Campos',
            'fields' => $campos,
        ];
        foreach( $campos as $campo) {
            if( $campo->descen == 'ttbbrr' ) {
                $query = $this->db->query("SELECT id, id_form, id_form_fields, name, slug, `order`, depend, descen, CONCAT('tbr-',id) AS id_form_field
                    FROM form_fields_tbr
                    WHERE id_form = $fid
                    AND id_form_fields = ?
                    ORDER BY `order`, name
                ", [$campo->id]);
                $campos_tbr = $query->result();
                $datos[] = [
                    'table' => $campo->name,
                    'fields' => $campos_tbr,
                ];
            }
        }

        return $datos;
    }

    //Obtene un solo registro de form_fields segun el id_form_field_global
    public function get_field_form_global($id_form_field_global = 0) {
        $query = $this->db->query("SELECT *
            FROM (
                SELECT id_form, id AS id_form_field_global, id, 
                    '' as padre, name, slug, `order`, type, depend, descen, '0' AS is_tbr
                FROM form_fields
                UNION
                SELECT id_form, CONCAT('tbr-',id) AS id_form_field_global, id, 
                    id_form_fields AS padre, name, slug, `order`, type, depend, descen, '1' AS is_tbr
                FROM form_fields_tbr
            ) a
        WHERE id_form_field_global = ?
        ORDER BY is_tbr, `order`, name
        ", [$id_form_field_global]);
        $result = $query->row();
        
        return $result;
    }

    public function get_fields_form_global($fid = 0) {
        $datos =[];
        $query = $this->db->query("SELECT *
            FROM (
                SELECT id_form, id AS id_form_field_global, id, 
                    '' as padre, name, slug, `order`, type, depend, descen, '0' AS is_tbr
                FROM form_fields
                UNION
                SELECT id_form, CONCAT('tbr-',id) AS id_form_field_global, id, 
                    id_form_fields AS padre, name, slug, `order`, type, depend, descen, '1' AS is_tbr
                FROM form_fields_tbr
            ) a
        WHERE id_form = ?
        ORDER BY is_tbr, `order`, name
        ", [$fid]);
        $result = $query->result();
        foreach ($result as $row) 
            $datos[$row->id_form_field_global] = $row;

        return $datos;
    }

    public function calc_fields_find($id = 0) {
        $query = $this->db->query("SELECT *
            FROM form_calc_fields
            WHERE id = ?
        ", [$id]);

        return $query->row();
    }

    public function calc_field_list($fid = 0){
        $query = $this->db->query("SELECT *
            FROM form_calc_fields
            WHERE id_form = ?
            ORDER BY id DESC
        ", [$fid]);

        return $query->result();
    }

    public function calc_field_save($post = []) {
        $id = $post['id'];
        $data = [
            'id_form'   => $post['id_form'],
            'activator' => $post['activator'],
            'field_a'   => $post['field_a'],
            'field_b'   => $post['field_b'],
            'field_r'   => $post['field_r'],
            'operator'  => $post['operator'],
        ];
        if( $id == 0 ){
            if ($this->db->insert('form_calc_fields', $data)) {
                return 'Registro agregado correctamente.';
            } else {
                return FALSE;
            }
        } else {
            $where = ['id' => $id];
            $this->db->update('form_calc_fields', $data, $where);
            return 'Registro actualizado correctamente.';
        }
    }

    public function calc_field_delete($id = 0) {
        $this->db->where('id', $id);
        $this->db->delete('form_calc_fields');

        return 'Registro eliminado.';
    }

    //Obtiene los selectores que se enviaran a javascript para hacer los calculos
    public function get_op_calc_field($id_form = 0, $activador = 0) {
        $query = $this->db->query("SELECT *
            FROM form_calc_fields
            WHERE activator = ?
            ORDER BY id DESC
        ", [$activador]);
        $registros = $query->result();
        //Obtenemos el detalle de los campos de ese formulario
        $cf = $this->get_fields_form_global($id_form);
        $datos = [];
        foreach ($registros as $row) {
            $datos[] = [
                's_activator'   => $this->get_selector_calc_field($cf[$row->activator]),
                'operator'      => $row->operator,
                's_field_a'     => $this->get_selector_calc_field($cf[$row->field_a]),
                's_field_b'     => $this->get_selector_calc_field($cf[$row->field_b]),
                's_field_r'     => $this->get_selector_calc_field($cf[$row->field_r]),
            ];
        }

        return $datos;
    }

    private function get_selector_calc_field($field) {
        if( $field->is_tbr == 1 ) {
            $selector = '.container_tbr[data-ntbr='.$field->padre.'] :input[name='.$field->slug.']';
        } else {
            $selector = '.crm_form :input[name='.$field->slug.']';
        }

        return $selector;
    }

    //Validamos si un campo es un campo calculado, indicando el id y si este es de una tabla relacional o no
    private function is_calc_field_activator( $id_ff_global=0, $calc_fields = [] ) {
        $return = false;
        foreach ($calc_fields as $field) {
            if($field->activator == $id_ff_global)
                $return = true;
        }

        return $return;
    }
    /*CAMPOS CALCULADOS*/
    /*OPERACIONES AL CIERRE DE TICKET (OCT)*/
    public function oct_list($id_form = 0) {
        $rows = $this->db->query("SELECT *
            FROM form_closing_operations
            WHERE id_form = ?
            ORDER BY `order` ASC
        ", [$id_form]);
        
        return $rows->result();
    }

    public function oct_save( $id = 0, $data = [] ) {
        if( $id == 0 ){
            if ($this->db->insert('form_closing_operations', $data)) {
                return 'Registro agregado correctamente.';
            } else {
                return FALSE;
            }
        } else {
            $where = ['id' => $id];
            $this->db->update('form_closing_operations', $data, $where);
            return 'Registro actualizado correctamente.';
        }
    }

    public function oct_delete($id = 0) {
        $this->db->where('id', $id);
        $this->db->delete('form_closing_operations');

        return 'Registro eliminado.';
    }

    //obtenemos los campos de la tabla principal y los campos de las tablas dependientes
    public function oct_base_fields_and_depends( $id_form ) {
        //Obtenemos los datos de los campos base
        $query = $this->db->query("SELECT id_form, name, slug, depend, descen
            FROM form_fields
            WHERE id_form = ?
            AND type NOT IN ('separador','tabla')
            ORDER BY `order`
        ", [$id_form]);
        $campos = $query->result();
        $tables_fields[] = [
            'name'  => 'formd_'.$id_form,
            'text'  => 'Campos base',
            'sufix' => '.',
            'fields'=> $campos
        ];
        //Obtenemos las tablas dependientes por los campos proporcionados del formulario
        $depends = $this->get_table_depend_by_form($id_form, $campos);
        foreach( $depends as $table=>$pk ) {
            $fields = $this->oct_get_fields_depends($table);
            $diferenciador = ( isset($fields[0]->name) ) ? ' ('.$fields[0]->name.')' : '';
            $tables_fields[] = [
                'name'  => $table,
                'text'  => 'Campos dependientes'.$diferenciador,
                'sufix' => str_replace('formd_'.$id_form,'',$table).'.',
                'fields'=> $fields
            ];
        }

        return $tables_fields;
    }

    //Obtiene los campos de una tabla dependiente
    public function oct_get_fields_depends($table) {
        $campos = [];
        $rows = $this->db->list_fields($table);
        foreach( $rows as $row ) {
            if( $row != 'active_system_row' ) {
                $campos[] = (object)[
                    'name' => str_replace('_', ' ', ucfirst($row)),
                    'slug' => $row,
                ];
            }
        }

        return $campos;
    }

    //Obtenemos las tablas dependientes de un formulario
    public function get_table_depend_by_form($id_form, $campos) {
        $depends = [];
        foreach ($campos as $campo) {
            if( $campo->descen == 'ddeepp' ) {
                $c = ($campo->depend == 0) ? '' : $campo->depend;
                $table = 'formd_'.$id_form.'_dep'.$c;
                $pk = $campo->slug;
                $depends[$table] = $pk;
            }
        }

        return $depends;
    }

    public function oct_apply( $fid = 0, $id_ticket = 0 ) {
        $success = true;
        $error   = '';
        $querys = [];
        $tables_row = [];
        //Obtenemos las operaciones de ese formulario
        $operaciones = $this->oct_list($fid);
        if( count($operaciones) > 0 ) {
            $this->db->trans_begin();
            $table_base = 'formd_'.$fid;
            $querys = [];
            $query = $this->db->query("SELECT id_form, name, slug, depend, descen
                FROM form_fields
                WHERE id_form = ?
                AND depend = 'ddeepp'
                ORDER BY `order`
            ", [$fid]);
            $campos = $query->result();
            $table_details = $this->get_table_depend_by_form($fid, $campos);
            //recorremos las operaciones a realizar
            foreach( $operaciones as $instruction ) {
                $data_a = [];
                $data_b = [];
                $data_r = [];
                //procesamos A
                if( $success ) {
                    if( $instruction->field_a == '0' ) { 
                        $data_a['value'] = $instruction->custom_a;
                    }
                    else {
                        $x = $this->oct_get_table_field_operation( $fid, $instruction->field_a );
                        $data_a  = $x['data'];
                        $success = $x['success'];
                        $error   = $x['error'];
                        if( $success ) {//Ahora obtendremos el valor de A
                            $table_a = $data_a['table'];
                            $field_a = $data_a['field'];
                            $tables_row = $this->oct_get_row_table( $table_base, $table_details, $id_ticket, $tables_row, $table_a );
                            $data_a['value'] = $tables_row[$table_a]->$field_a;
                        }
                    }
                }
                //procesamos B
                if( $success ) {
                    if( $instruction->operator != 'N/A' ) {
                        if( $instruction->field_b == '0' ) { 
                            $data_b['value'] = $instruction->custom_b;
                        }
                        else {
                            $x = $this->oct_get_table_field_operation( $fid, $instruction->field_b );
                            $data_b  = $x['data'];
                            $success = $x['success'];
                            $error   = $x['error'];
                            if( $success ) {//Ahora obtendremos el valor de B
                                $table_b = $data_b['table'];
                                $field_b = $data_b['field'];
                                $tables_row = $this->oct_get_row_table( $table_base, $table_details, $id_ticket, $tables_row, $table_b );
                                $data_b['value'] = $tables_row[$table_b]->$field_b;
                            }
                        }
                    } else {
                        $data_b = [
                            'table' => null,
                            'field' => null,
                            'value' => null,
                        ];
                    }
                }
                //procesamos R
                if( $success ) {
                    //obtenemos los datos de R
                    $x = $this->oct_get_table_field_operation( $fid, $instruction->field_r );
                    $data_r  = $x['data'];
                    $success = $x['success'];
                    $error   = $x['error'];
                    if( $success ) {//Ahora obtendremos el valor de R
                        $table_r = $data_r['table'];
                        $field_r = $data_r['field'];
                        $tables_row = $this->oct_get_row_table( $table_base, $table_details, $id_ticket, $tables_row, $table_r );
                        $x = $this->oct_exec_operation($data_a['value'], $instruction->operator, $data_b['value']);
                        $r  = $x['r'];
                        $success = $x['success'];
                        $error   = $x['error'];
                        if ( $success ) {
                            $data_r['value'] = $r; 
                            $table_r = $data_r['table'];
                            $value_r = $data_r['value'];                    
                            if( $table_r == $table_base ) {
                                $pk = 'id';
                                $pk_value = $id_ticket;
                            } else {
                                //Validamos que exista en memoria el row de tabla base
                                if( !isset($tables_row[$table_base]) ) {
                                    $condition = ['id' => $id_ticket];
                                    $tables_row[$table_base] = $this->db->get_where($table_base, $condition)->row();
                                }
                                $pk = $table_details[$table_r];
                                $pk_value = $tables_row[$table_base]->$pk;
                            }
                            $this->db->update($table_r, [$field_r => $value_r], [$pk => $pk_value]);
                            $tables_row[$table_r]->$field_r = $value_r; //Actualizamos el valor en memoria
                            //$querys[] = $this->db->last_query();
                        }
                    }
                }
            }
            if ($this->db->trans_status() === FALSE || $success === FALSE){      
                $this->db->trans_rollback();      
            }else{        
                $this->db->trans_commit();        
            }
        }
        $result = [
            'querys'      => $querys,
            'operaciones' => $operaciones,
            'tables_row'  => $tables_row,
            'success'     => $success,
            'error'       => $error,
        ];

        return $result;
    }

    /*
    $table_base:    La tabla base de 
    $table_details: Informacion de las tablas de este formulario y su PK
    $id_ticket:     El id del ticket
    $tables_row:    Renglon de las tablas de este formulario en memoria
    retorna el $tables_row actualizado
    */
    public function oct_get_row_table($table_base, $table_details, $id_ticket, $tables_row, $table_query) {
        //Validamos la existencia de la tabla consultada en memoria
        if( !isset($tables_row[$table_query]) ) { //Consultamos el row de esa tabla
            if( $table_query == $table_base ) {
                $condition = ['id' => $id_ticket];
            } else {
                //Validamos que exista en memoria el row de tabla base
                if( !isset($tables_row[$table_base]) ) {
                    $condition = ['id' => $id_ticket];
                    $tables_row[$table_base] = $this->db->get_where($table_base, $condition)->row();
                }
                $pk = $table_details[$table_query];
                $condition = [
                    $pk => $tables_row[$table_base]->$pk
                ];
            }
            $tables_row[$table_query] = $this->db->get_where($table_query, $condition)->row();
        }

        return $tables_row;
    }

    public function oct_get_table_field_operation($fid, $field) {
        $error = "";
        $part   = explode('.', $field);
        if( isset($part[0]) && isset($part[1]) ) {
            $subf  = $part[0];
            $field = $part[1];
            $data = [
                'table' => 'formd_'.$fid.$subf,
                'field' => $field,
                'value' => null
            ];
            $success = true;
        } else {
            $data = [];
            $success = false;
            $error = 'Error: No se puedo encontrar un campo valido';
        }

        return [
            'data'      => $data,
            'success'   => $success,
            'error'     => $error
        ];
    }

    private function oct_exec_operation($a, $operator, $b) {
        $r = null;
        $success = true;
        $error = '';
        switch($operator) {
            case '+':
                if( is_numeric($a) && is_numeric($b) ) {
                    $r = $a + $b;
                } else {
                    $success = false;
                    $error = "Error, operaciones al cierre de tickets: el valor ($a) o ($b) no es numerico.";
                }
            break;
            case '-':
                if( is_numeric($a) && is_numeric($b) ) {
                    $r = $a - $b;
                } else {
                    $success = false; 
                    $error = "Error, operaciones al cierre de tickets: el valor ($a) o ($b) no es numerico.";
                }
            break;
            case '*':
                if( is_numeric($a) && is_numeric($b) ) {
                    $r = $a * $b;
                } else {
                    $success = false; 
                    $error = "Error, operaciones al cierre de tickets: el valor ($a) o ($b) no es numerico.";
                }
            break;
            case '/':
                if( is_numeric($a) && is_numeric($b) ) {
                    if( $b != '0' ) {
                        $r = $a / $b;
                    }
                    else {
                        $success = false; 
                        $error = "Error, operaciones al cierre de tickets: No se puede dividir un valor entre 0.";
                    }
                } else {
                    $success = false; 
                    $error = "Error, operaciones al cierre de tickets: el valor ($a) o ($b) no es numerico.";
                }
            break;
            case '.':
                $r = $a . $b;
            break;
            case 'N/A':
                $r = $a;
            break;
        }

        return [
            'r'       => $r,
            'success' => $success,
            'error'   => $error,
        ];
    }

    public function update_dep_table_columns(){
        //obtenemos todos los formularios
        $query = $this->db->query("SELECT id_form, depend
        FROM (
            SELECT id_form, depend, descen
            FROM form_fields
            UNION
            SELECT id_form, depend, descen
            FROM form_fields_tbr
        ) a
        WHERE descen = 'ddeepp' 
        ORDER BY id_form, depend");
        $rows = $query->result();
        $resp = [];
        //Recorremos cada tabla dependiente
        foreach( $rows as $row ) {
            $n = ($row->depend == 0) ? '' : $row->depend;
            $table = 'formd_'.$row->id_form.'_dep'.$n; 
            // Existe
            if( $this->db->field_exists('active_system_row', $table) === true ) { 
                $resp[] = [
                    'tabla' => $table,
                    'info'  => 'Tabla Ok'
                ];
            }
            // No existe
            else { 
                if( $this->db->query("ALTER TABLE $table ADD COLUMN active_system_row tinyint NOT NULL DEFAULT 1") ) {
                    $resp[] = [
                        'tabla' => $table,
                        'info'  => 'infoSe agrego columna active_system_row'
                    ];
                }
                else {
                    $resp[] = [
                        'tabla' => $table,
                        'info'  => 'Error: No se pudo agregar la columna active_system_row'
                    ];
                }
                
            }
        }

        return $resp;
    }

    /* FILTROS DEPNEDIENTES (FTR)*/
    /**
     * @param int       $data.id_form           ID del formulario
     * @param string    $data.field_to_compare  Campo de la tabla dependiente a comparar
     * @param string    $data.string_bus        Valor a comparar
    */
    public function get_data_dep_filter($data) {
        $fid                = $data['id_form'];
        $field_to_compare   = $data['field_to_compare'];    // Campo con el cual se comparara
        $string_bus         = $data['string_bus'];          // string a buscar
        $field_to_filter    = $data['field_to_filter'];     //Campo a filtrar         
        $union_table        = $data['union_table'];
        $union_field_a      = $data['union_field_a'];
        $union_field_b      = $data['union_field_b'];
        $resp       = [];
        // Calculamos el nombre de la tabla union
        if( $union_table != '' )
            $union_table = 'formd_'.$fid.'_tb_union'.$union_table;
        // Obtenemos una lista de las tablas dependientes de ese formulario con los campos depend, name, slug, table_name
        $tables = $this->get_tables_dep($fid);
        // $field_to_filter contiene el nombre el campo principal de la tabla dependiente, asi que lo usamos para obtener el nombre de la tabla
        $table_dep = '';
        foreach($tables as $table) {
            if( $table->slug == $field_to_filter ) {
                $table_dep = $table->table_name;
            }
        }
        // Verificamos si este filtro de busqueda contiene una tabla intermedia union, para obtener los datos de la tabla dependiente
        if( !empty($union_field_a) && !empty($union_field_b) ) {
            $resp   = $this->db->query("SELECT dt.* 
                FROM $union_table AS ut
                INNER JOIN $table_dep AS dt ON ut.$union_field_a = dt.$union_field_b
                WHERE ut.$field_to_compare = '$string_bus'
            ")->result();
        }
        else {
            $resp   = $this->db->query("SELECT dt.*
                FROM $table_dep AS dt
                WHERE dt.$field_to_compare LIKE '%$string_bus%'
            ")->result();
        }

        return $resp;
    }

    public function ftr_selects($fid) {
        $tabdeps = $this->get_tables_dep($fid);
        // Campos de tablas dependientes para combo field_to_filter
        $activator = [];
        foreach ($tabdeps as $tab) {
            $dep = ($tab->depend == 0) ? '' : $tab->depend;
            $table = 'formd_'.$fid.'_dep'.$dep;
            $rows = $this->db->list_fields($table);
            $campos = [];
            foreach( $rows as $row ) {
                if( $row != 'active_system_row' ) {
                    $campos[] = (object)[
                        'name' => str_replace('_', ' ', ucfirst($row)),
                        'slug' => $row,
                    ];
                }
            }
            $activator[] = (object)[
                'table_name' => $tab->name,
                'fields'    => $campos,
            ];
        }

        return [
            'activator'      => $activator,
            'field_to_filter' => $tabdeps,
        ];
    }

    public function ftr_list($id_form = 0) {
        $rows = $this->db->query("SELECT *
            FROM form_filter_dep
            WHERE id_form = ?
            ORDER BY `id` DESC
        ", [$id_form]);
        
        return $rows->result();
    }

    public function ftr_one($id) {
        $sql = "SELECT *
            FROM form_filter_dep
            WHERE id = ?
            LIMIT 1;
        ";
        $query = $this->db->query($sql, [$id]);
        $row = $query->row();

        return $row;
    }

    public function ftr_save( $id = 0, $data = [] ) {        
        if( $id == 0 ){
            if ($this->db->insert('form_filter_dep', $data)) {
                $return = $this->db->insert_id();
            } else {
                $return = FALSE;
            }
        } else {
            $where = ['id' => $id];
            $return = $this->db->update('form_filter_dep', $data, $where);
        }

        return $return;
    }

    public function ftr_delete($id = 0) {
        $success = true;
        $message = 'Registro eliminado correctamente.';
        //Antes de eliminar el registro verificamos si existe
        $sql = "SELECT id, id_form, union_table
            FROM form_filter_dep
            WHERE id = ?
            LIMIT 1;
        ";
        $query = $this->db->query($sql, [$id]);
        $row = $query->row();
        // Procedemos con la eiminacion del registro
        if( !isset($row) ) {
            $success = false;
            $message = 'Error: No existe el registro que quiere eliminar.';
        }
        if( $success ) {
            // Vaidamos si tiene tabla union ligaga y de ser asi, la eliminamos
            if( !empty($row->union_table) ) {
                //Eliminamos la tabla union de ese formulario y ese registro
                $union_table_name = 'formd_'.$row->id_form.'_tb_union'.$row->union_table;
                $sql = "DROP TABLE IF EXISTS ".$union_table_name;
                $result = $this->db->query($sql);
                if( !$result ) {
                    $success = false;
                    $message = 'Error: No se pudo eliminar la tabla union';
                }
            }
        }
        // Eliminamos el registro
        if( $success ) {
            $this->db->where('id', $id);
            $this->db->delete('form_filter_dep');
        }

        return [
            'success' => $success,
            'message' => $message,
        ];
    }

    public function ftr_delete_table_union($data) {
        $success = true;
        $message = '';
        //Eliminamos la tabla union de ese formulario y ese registro
        $union_table_name = 'formd_'.$data['id_form'].'_tb_union'.$data['union_table'];
        $sql = "DROP TABLE IF EXISTS ".$union_table_name;
        $result = $this->db->query($sql);
        if( !$result ) {
            $success = false;
            $message = 'Error: No se pudo eliminar la tabla';
        }
        //Eliminamos los datos de opciones avanzadas del registro seleccionado
        if( $success ) {
            $where = ['id' => $data['id']];
            $data_update = [
                'union_table'   => '',
                'union_field_a' => '',
                'union_field_b' => '',
            ];
            $result = $this->db->update('form_filter_dep', $data_update, $where);
            if( !$result ) {
                $success = false;
                $message = 'Error: No se pudo actualizar el registro, intente nuevamente';
            }
        }
        if( $success )
            $message = 'Informacion actualizada correctamente.';

        return [
            'success' => $success,
            'message' => $message,
        ];
    }

    /**
     * definicionTablaCampo regresa la definición de un campo para su creación en una tabla
     * @param $irff ID Registro form_field
     * @return string
     */
    private function definicionTablaCampo($irff) {
        $field = $this->db->query("SELECT * FROM form_fields WHERE id = ?", [$irff])->row();
        $len   = (empty($field->len)) ? "254" : $field->len;
        if ($field->slug == 'id_cliente') { $tipo = "nnumber"; $len = "11"; }
        if ($field->slug == 'asignar_a') { $tipo = "number"; $len = "11"; }
        if ($field->type == 'checkbox') $len = "1";
        $tipos = array(
            "textarea" => "text NOT NULL DEFAULT ''",
            "boton"    => "varchar($len) NOT NULL DEFAULT ''",
            "text"     => "varchar($len) NOT NULL DEFAULT ''",
            "dropdown" => "varchar($len) NOT NULL DEFAULT ''",
            "radio"    => "varchar($len) NOT NULL DEFAULT ''",
            "date"     => "date",
            "datetime" => "datetime",
            "datetime_pdf" => "datetime",
            "datetime_pdf_update" => "datetime",
            "checkbox" => "tinyint($len) NOT NULL DEFAULT '0'",
            "number"   => "int($len)",
            "nnumber"  => "int($len) NOT NULL DEFAULT '0'",
        );
        $tipo  = (array_key_exists($field->type, $tipos)) ? $field->type : "text";

        return (array_key_exists($tipo, $tipos)) ? $tipos[$tipo] : "";
    }

    private function getCampoFromResult($datarray, $indice, $valor) : stdClass {
        foreach ($datarray as $row) {
            if ($row->$indice == $valor) return $row;
        }
    }

    private function getStatusOptsByFieldVal($values, $fid) : string {
        $opts = "";
        if ($values == "cat" && $this->db->table_exists("formd_".$fid."_cats")) {
            $query = $this->db->query("SELECT eti FROM formd_".$fid."_cats WHERE field = 'estado' || field = 'id_estado' ORDER BY seq, eti");
            foreach ($query->result() as $row) {
                if ($row->eti != "Abierto") $opts .= "\t\t\t\t\t<option value='$row->eti'>$row->eti</option>\n";
            }
        } else if (!empty($values) && stripos($values, ",") !== false) {
            $arr = explode(",", $values);
            foreach ($arr as $eti) {
                $opts .= "\t\t\t\t\t<option value='$eti'>$eti</option>\n";
            }
        } else {
            $opts = "\t\t\t\t<option value='En proceso'>En proceso</option>\n\t\t\t\t<option value='En pausa'>En pausa</option>\n".
            "\t\t\t\t<option value='En espera'>En espera</option>\n\t\t\t\t<option value='Resuelto'>Resuelto</option>\n".
            "\t\t\t\t<option value='Cancelado'>Cancelado</option>\n\t\t\t\t<option value='Cerrado'>Cerrado</option>\n";
        }

        return $opts;
    }
}
