<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Desp_model extends CI_Model
{

    public function get_all($campsids = 0) {
        $query = $this->db->query("SELECT * from dispatcher where id_campaign in ($campsids)");
        $disps = $query->result();
        foreach ($disps as $key => $disp) {
            $query = $this->db->query("SELECT u.id, concat(u.name,' ',u.last) name from disp_user du
                left join user u on u.id = du.id_user where du.id_dispatcher = $disp->id");
            $disps[$key]->users = $query->result();
            $query = $this->db->query("SELECT * from disp_field df
                where df.id_dispatcher = $disp->id order by `order`, id");
            $disps[$key]->fields = $query->result();
            $query = $this->db->query("SELECT * from disp_csv dc where dc.id_dispatcher = $disp->id");
            $disps[$key]->csvs = $query->result();
            if (count($disps[$key]->csvs)>0) {
                $this->load->library('csvreader');
                foreach ($disps[$key]->csvs as $csvkey => $csval) {
                    $this->csvreader->auto('/var/www/upload/'.$csval->name);
                    $disps[$key]->csvs[$csvkey]->fields = $this->csvreader->titles;
                }
            }
            $query = $this->db->query("SELECT COUNT(*) si FROM `INFORMATION_SCHEMA`.`TABLES`
            WHERE `TABLE_SCHEMA` = (DATABASE()) AND `TABLE_NAME` = 'disp_$disp->id'");
            $disps[$key]->entries = $query->row()->si;
            if ($disps[$key]->entries > 0) {
                $disps[$key]->entries = $this->db->count_all("disp_$disp->id");
                $query = $this->db->query("SELECT count(id) tot from disp_$disp->id where status = 1");
                $disps[$key]->finished = $query->row()->tot;
                $query = $this->db->query("SELECT count(id) tot from disp_$disp->id where qualif <> '' and status = 0");
                $disps[$key]->partial = $query->row()->tot;
            } else {
                $disps[$key]->finished = 0;
                $disps[$key]->partial = 0;
            }
            $disps[$key]->condis = [];
            $query = $this->db->query("SELECT * from disp_cond where id_dispatcher = $disp->id order by hora");
            if ($query->num_rows()>=1) {
                foreach ($query->result() as $row) {
                    $accion = ["", ", Iniciar", ", Detener"];
                    $mas = $accion[$row->accion];
                    if ($mas != ", Detener") {
                        $mas .= (!empty($row->tipi)) ? ", Tipificación: ".$row->tipi : "";
                        $mas .= (!empty($row->campo)) ? ", ".$row->campo.": ".$row->camcond : "";
                        $mas = (empty($mas)) ? ", Sin efecto." : $mas;
                    }
                    $disps[$key]->condis[] = (object)["id"=>$row->id, "des" => $row->hora.$mas];
                }
            }
            $disps[$key]->yatipis = [];
            if ($disps[$key]->entries > 0) {
                $query = $this->db->query("SELECT DISTINCT(`qualif`) AS 'val' FROM `disp_$disp->id` where qualif <> ''");
                if ($query->num_rows()>=1) {
                    foreach ($query->result() as $row) {
                        $disps[$key]->yatipis[] = $row->val;
                    }
                }
            }
        }
        return $disps;
    }

    private function getUserCampaigns() {
      $query = $this->db->query("SELECT val
                FROM user_data
                WHERE `id_user` = '".$this->session->userdata('uid')."'
                AND id_catalog = (SELECT id FROM `catalogs` WHERE `cat` = 'userData' AND `val` = 'campanas') ")->row();
      return $query->val;
    }

    public function addqualif($data) {
        $query = $this->db->query("SELECT * from disp_field where id_dispatcher=? and name='qualif' limit 1",
        array($data['id_desp']));
        $fila = $query->row();
        $qualif = json_decode($fila->options);
        $qualif[] = $data['qualif'];
        if (file_exists(APPPATH.'views/despachador/desp_form_'.$data['id_desp'].'.php')) {
            unlink(APPPATH.'views/despachador/desp_form_'.$data['id_desp'].'.php');
        }
        return $this->db->query("UPDATE disp_field set options=? where id=?",
        array(json_encode(array_values($qualif), JSON_UNESCAPED_UNICODE), $fila->id));
    }

    public function delqualif($data) {
        $query = $this->db->query("SELECT * from disp_field where id_dispatcher=? and name='qualif' limit 1",
        array($data['id_desp']));
        $fila = $query->row();
        $qualif = json_decode($fila->options);
        foreach ($qualif as $key => $value) {
            if ($value == $data['qualif']) unset($qualif[$key]);
        }
        if (file_exists(APPPATH.'views/despachador/desp_form_'.$data['id_desp'].'.php')) {
            unlink(APPPATH.'views/despachador/desp_form_'.$data['id_desp'].'.php');
        }
        return $this->db->query("UPDATE disp_field set options=? where id=?",
        array(json_encode(array_values($qualif), JSON_UNESCAPED_UNICODE), $fila->id));
    }

    public function get_users($campanas) {
        $base     = $this->datos_model->getRelUsers(["cam"=>$campanas]);
        $query    = $this->db->query("SELECT group_concat(`id_user`) AS 'dusrs' FROM `disp_user`");
        $ocupados = explode(',', $query->row()->dusrs);
        foreach ($base as $key => $row) {
            if(in_array($row->id, $ocupados)) {
                unset($base[$key]);
            }
        }
        return $base;
    }

    public function adduser($data) {
        return $this->db->query("INSERT into disp_user values (?, ?)", $data);
    }

    public function deluser($data) {
        return $this->db->query("DELETE from disp_user where id_dispatcher=? and id_user=?", $data);
    }

    public function adddesp($data) {
        return $this->db->query("INSERT into dispatcher (name, created_by, created_when, id_campaign)
            values (?, ?, now(), ?)",
            array($data['name'], $this->session->userdata("uid"), $data["campana"])
        );
    }

    public function csvname($id_desp, $file_name) {
        return $this->db->query("INSERT INTO disp_csv (id_dispatcher, name) values (?, ?)", array($id_desp, $file_name));
    }

    public function pasarcsv($data) {
        $archivo = '/var/www/upload/'.$data['csv'];
        $this->csvreader->auto($archivo);
        $id_camp = "";
        $query = $this->db->query("SELECT id_campaign from dispatcher where id = ?", [$data['id_desp']]);
        if ($query->num_rows()==1) $id_camp = $query->row()->id_campaign;

        $this->db->query("DROP TABLE IF EXISTS disp_".$data['id_desp']);
        $this->db->query("DELETE FROM disp_field where id_dispatcher = ?", array($data['id_desp']));
        $query = "CREATE TABLE disp_".$data['id_desp']." (`id` int(11) NOT NULL AUTO_INCREMENT, ";
        $cquery = "INSERT INTO disp_field VALUES ";
        $keyphone = "";
        $tipodatetime = array();
        foreach ($this->csvreader->titles as $key => $tit) {
            if (isset($data['use'.$key])) {
                if (!empty($data['c'.$data['id_desp'].'values'.$key])) {
                    $options = $data['c'.$data['id_desp'].'values'.$key];
                    $options = strToJson($options);
                } else {
                    $options = "";
                }
                $readonly = (isset($data['readonly'.$key])) ? 1 : 0;
                $required = (isset($data['required'.$key])) ? 1 : 0;
                $slug = slugify($data['name'.$key]);
                if ( $slug == "telefono") $keyphone = $key;
                if ($data['c'.$data['id_desp'].'type'.$key]=='datetime') $tipodatetime[] = $key;
                $query .= "`".$slug."` ".$this->tipos($data['c'.$data['id_desp'].'type'.$key]).", ";
                $cquery .= "(0, '".$data['id_desp']."', '".$data['name'.$key]."', '".$slug."', '".
                $data['c'.$data['id_desp'].'type'.$key]."', '0', '0', '0', '".$options."', '0' ,'".
                $readonly."', '".$required."', '".$data['order'.$key]."'), ";
            }
        }
        $_cquery = "INSERT INTO disp_field VALUES (0,'".$data['id_desp']."','Tipificación','tipificacion','dropdown',1,0,1,'default',0, 0,1,0); ";
        $this->db->query($_cquery);
        $_cquery = "INSERT INTO disp_field VALUES (0,'".$data['id_desp']."','Comentarios','comentarios','textarea',1,0,0,'',0, 0, 0,0); ";
        $this->db->query($_cquery);
        $query .= "`qualif` varchar(50) NOT NULL DEFAULT '', `llamadas` tinyint(1) NOT NULL DEFAULT '0',
        `invalid` tinyint(1) NOT NULL DEFAULT '0', `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
        `busy` datetime DEFAULT NULL,
        `status` tinyint(1) NOT NULL DEFAULT '0', `last_update` datetime DEFAULT NULL, `added` datetime DEFAULT NULL,
        `since` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        if ($keyphone == '0' || !empty($keyphone)) {
            $prefijo = "";
            $querypref = $this->db->query("SELECT valor from campaign_data where id_campaign = ? and atributo = 'prefijo'", [$id_camp]);
            if ($querypref->num_rows()==1) $prefijo = $querypref->row()->valor;
            $this->db->query($query);
            $options = json_encode(array('Buzón', 'No contesta', 'Seguimiento', 'Número incorrecto', 'No le interesa'), JSON_UNESCAPED_UNICODE);
            $cquery = rtrim($cquery, ", ");
            $cquery.=";";
            $this->db->query($cquery);
            $contador = 1;
            $query = "INSERT INTO `disp_".$data['id_desp']."` values ";
            foreach ($this->csvreader->data as $key => $row) {
                $query .= "(0, ";
                $fila = array_values($row);
                foreach ($fila as $col => $val) {
                    if (isset($data['use'.$col])) {
                        if ($col == $keyphone) $val = $prefijo . trim(preg_replace('/[^0-9]+/', '', $val));
                        if (in_array($col, $tipodatetime)) {
                            if (!empty($val)) {
                                $val = date("Y-m-d H:i:s", strtotime($val));
                            } else {
                                $val = 'null';
                            }
                        }
                        $val = str_replace("'", "", $val);
                        $query .= "'".$val."', ";
                    }
                }
                $query = rtrim($query, ", ");
                $query .= ", '', 0, 0, 0, null, 0, null, now(), null), ";
                $contador++;
                if ($contador == 200) {
                    $query = rtrim($query, ", ");
                    $res = $this->db->query($query);
                    $contador = 1;
                    $query = "INSERT INTO `disp_".$data['id_desp']."` values ";
                }
            }
            $query = rtrim($query, ", ");
            $res = $this->db->query($query);
            if ($res) {
                $this->db->query("DELETE from disp_csv where name = ?", array($data['csv']));
                $this->db->query("CREATE TABLE `disp_".$data['id_desp']."_qualif` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `id_disp_data` int(11) NOT NULL,
                    `uniqueid` varchar(50) NOT NULL,
                    `linkedid` varchar(50) NOT NULL,
                    `tipificacion` varchar(20) NOT NULL,
                    `comentarios` text,
                    `saved_by` int(11) NOT NULL,
                    `saved_when` datetime NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `id_disp_data` (`id_disp_data`),
                    KEY `saved_by` (`saved_by`),
                    CONSTRAINT `disp_".$data['id_desp']."_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
                    CONSTRAINT `disp_log_ibfk_".$data['id_desp']."` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_".$data['id_desp']."` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
                unlink($archivo);
            } else {
                $this->db->query("DROP TABLE IF EXISTS disp_".$data['id_desp']);
                $this->db->query("DELETE from disp_field where id_dispatcher = ?", [$data['id_desp']]);
            }
            return $res;
        } else {
            return false;
        }
    }

    public function delcsv($id_csv, $csv) {
        unlink(FCPATH.'../upload/'.$csv);
        return $this->db->query("DELETE from disp_csv where id=?", array($id_csv));
    }

    public function updtipo($data) {
        $cola = ($data['autodial'] == 'manual' || $data['autodial'] == 'progresivo') ? "" : $data['cola'];
        $this->db->query("UPDATE dispatcher set autodial = ?, queue = ?
            where id=?", array($data['autodial'], $cola, $data['id_desp']));
        if ($cola != '') $this->db->query("DELETE FROM disp_user where id_dispatcher = ?", array($data['id_desp']));

        return true;
    }

    public function updvueltas($data) {
        return $this->db->query("UPDATE dispatcher set rounds = ?
            where id=?", array($data['vueltas'], $data['id_desp']));
    }

    public function activar($data) {
        $this->updtipo($data);
        $query = $this->db->query("SELECT * from dispatcher where id=?", array($data['id_desp']));
        $desp = $query->row();
        $respu = "Error";
        $sigue = ($desp->active==1 && $desp->running==0) ? 1 : 0;
        if ($sigue==1) {
            $this->db->where('status', 0);
            $regs = $this->db->count_all_results("disp_".$data['id_desp']);
            if ($regs < 1) {
                $sigue =  0;
                $respu = "No hay registros nuevos por marcar.";
            }
        }
        if ($sigue==1) {
            $this->db->where('id_dispatcher', $data['id_desp']);
            $this->db->where('typedb', 1);
            $tipis = $this->db->count_all_results('disp_field');
            if ($tipis < 1) {
                $sigue = 0;
                $respu = "No hay tipificaciones.";
            }
        }
        if ($sigue==1) {
            $this->db->where('id_dispatcher', $data['id_desp']);
            $opers = $this->db->count_all_results('disp_user');
            if ($opers < 1 && $cola = '') {
                $sigue = 0;
                $respu = "No hay agentes asignados.";
            }
        }
        if ($sigue==1) {
            $file = APPPATH.'views/despachador/desp_form_'.$data['id_desp'].'.php';
            if (file_exists($file)) {
                unlink(APPPATH.'views/despachador/desp_form_'.$data['id_desp'].'.php');
            }
            $this->crear_form_file($data['id_desp'], $data['autodial']);
            return $this->db->query("UPDATE dispatcher set running = 1 where id=?", array($data['id_desp']));
        }
        return $respu;
    }

    public function detener($id_desp) {
        return $this->db->query("UPDATE dispatcher set running = 0 where id=?", array($id_desp));
    }

    public function actualizarcola($data) {
        return $this->db->query("UPDATE dispatcher set queue = ? where id = ?", [$data['queue'], $data['id_desp']]);
    }

    public function archivar($data) {
        $cola = ($data['autodial'] == 'manual' || $data['autodial'] == 'progresivo') ? "" : $data['cola'];
        $this->db->query("UPDATE dispatcher set autodial = ?, queue = ?
            where id=?", array($data['autodial'], $cola, $data['id_desp']));
        $this->db->query("DELETE from disp_user where id_dispatcher = ?", array($data['id_desp']));
        return $this->db->query("UPDATE dispatcher set active = 0 where id=?", array($data['id_desp']));
    }

    public function desarchivar($id_desp) {
        return $this->db->query("UPDATE dispatcher set active = 1 where id=?", array($id_desp));
    }

    public function addcond($data) {
        $str = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $data["camval"])));
        $str = str_replace(" ,", ",", $str);
        $str = str_replace(", ", ",", $str);
        return $this->db->query("INSERT into disp_cond (`id_dispatcher`, `hora`, `accion`, `tipi`, `campo`, `camcond`)
            values (?,?,?,?,?,?)", [$data["id_desp"], $data["hora"], $data["accion"], $data["tipi"], $data["campo"], $str]);
    }

    public function delcond($data) {
        return $this->db->query("DELETE FROM disp_cond where id = ?", [$data["id"]]);
    }

    public function getForm($data = [], $desp = null) {
        if(!empty($data['unlock_reg'])) {
            // Existe un registro para desbloquaer
            $this->db->query("UPDATE `disp_".$desp->id."` set `last_update` = now(), `busy` = NULL where `id` = ? AND `busy` <> null", [$data['unlock_reg']]);
        }
        if (!empty($data['id_reg'])) {
            // Despliegue de un registro de forma manual
            $query = $this->db->query("SELECT * from `disp_".$desp->id."` where `id`=? AND `busy` IS NULL", array($data['id_reg']));
        } else {
            $query = $this->db->query("SELECT * from `disp_cond`
                where `id_dispatcher` = ? and `hora` < time(now()) order by `hora` DESC limit 1", array($desp->id));
            // Si existe una condicion entra, siempre debe de haber una, por lo menos la de inicio
            if ($query->num_rows()==1) {
                $cond = $query->row();
                // Detener: Aqui se cumple la condicion y retornamos false
                if ($cond->accion == '2') return false;
                // Primero lanzo todo lo agendado, quiere decir que tiene la prioridad máxima sin otras condiciones
                $query = $this->db->query("SELECT * from `disp_".$desp->id."`
                    WHERE `status` = 0 AND `busy` IS NULL AND `invalid` = 0
                    AND `since` IS NOT NULL AND `since` < now()
                    ORDER BY `since` LIMIT 1");
                if ($query->num_rows()==0) {
                    $maswhere = "";
                    if (!empty($cond->tipi)) $maswhere .= " AND `qualif` = '$cond->tipi'";
                    if (!empty($cond->campo) && !empty($cond->camcond)) {
                        $valores = explode(",", $cond->camcond);
                        $campo = slugify($cond->campo);
                        if (count($valores) == 1) {
                            $maswhere .= " AND `$campo` = '$cond->camcond'";
                        } else {
                            $maswhere .= " AND (";
                            foreach ($valores as $valor) {
                                $maswhere .= "`$campo` = '$valor' or ";
                            }
                            $maswhere = rtrim($maswhere," or ").")";
                        }
                    }
                    // Medida desesperada del supervisor para ponerse al día con poco personal
                    // Apilar en lugar de encolar
                    $pila  = (!empty($desp->extra) && $desp->extra == "pila") ? "DESC" : "";
                    $query = $this->db->query("SELECT * FROM `disp_".$desp->id."`
                        WHERE status=0 AND `busy` IS NULL AND `invalid`=0 AND `since` IS NULL
                        AND (last_update IS NULL OR last_update <= NOW() - INTERVAL 120 MINUTE) $maswhere
                        ORDER BY `access`, `llamadas`, `id` $pila LIMIT 1");
                }
            } else {
                // No se encontro ninguna condicion, nisiquiera la de inicio, asi que retornamos false
                return false;
            }
        }
        $fila = $query->row();
        // ToDo: Detener el despachador condicionalmente, si no se ha modificado en 2 días (podría ser)
        // $this->db->where('status', 0);
        // $this->db->where('invalid', 0);
        // $regs = $this->db->count_all_results("disp_".$desp->id);
        // if ($regs < 1) {
        //     $this->db->query("UPDATE dispatcher set running = 0 where id = ?", array($desp->id));
        // }
        if (!empty($fila)) {
            $datos = "`access` = `access` + 1, `busy` = now()";
            $qalifs = $this->db->query("SELECT count(id) tot from disp_".$desp->id."_qualif WHERE id_disp_data = ?",
                [$fila->id])->row()->tot;
            if ($qalifs < 1) {
                $this->db->query("INSERT INTO disp_".$desp->id."_qualif (id_disp_data, tipificacion, saved_by, saved_when)
                    VALUES (?, 'Desplegado', ?, now())", [$fila->id, $this->udata["id"]]);
                $datos .= ", `qualif` = 'Desplegado', `last_update` = now()";
            }
            if ($desp->autodial == 'predictivo' || $desp->autodial == 'predictivoamd') {
                if ($desp->rounds > 0 && $fila->access >= ($desp->rounds - 1)) {
                    $datos .= ", `status` = 1";
                }
            }
            $this->db->query("UPDATE `disp_".$desp->id."` set $datos where id = ".$fila->id);
        }

        return $fila;
    }

    // Limpieza de registros ocupados por minutos
    public function clean_disp_busy($min = 120) {
        echo "Liberación de registros ocupados en despachador<br>";
        $desps = $this->db->query('SELECT id 
        FROM dispatcher
        WHERE active = 1')->result();

        foreach ($desps as $desp) {
            $tbl = 'disp_'.$desp->id;
            $this->db->query("UPDATE $tbl SET last_update = now(), busy = NULL
                WHERE busy IS NOT NULL AND TIMESTAMPDIFF(MINUTE, busy, NOW()) > ?",[$min]);
            $n = $this->db->affected_rows();
            echo "($n) $tbl <br/>";
        }
    }

    public function getDespInfo($id) {
        if (empty($id)) {
            $query = $this->db->query("SELECT d.* FROM disp_user du JOIN dispatcher d ON d.id = du.id_dispatcher
                WHERE du.id_user = ? AND d.active = 1 AND d.running = 1
                AND (autodial='manual' || autodial='progresivo') ORDER BY d.id DESC LIMIT 1", [$this->udata["id"]]
            );
        } else {
            $query = $this->db->query("SELECT * FROM dispatcher
                WHERE id=? AND active=1 AND running=1
                AND (autodial='manual' || autodial='progresivo')", [$id]
            );
        }

        return $query->row();
    }

    public function histo($id_desp, $id_reg) {
        $query = $this->db->query("SELECT `tipificacion`, concat(u.name,' ', u.last) nombre, dq.saved_when, dq.comentarios com
            from disp_".$id_desp."_qualif dq
            left join user u on u.id=dq.saved_by
            where id_disp_data = '$id_reg'");

        return $query->result();
    }

    private function crear_form_file($id_desp, $tipo = "manual") {
        $query = $this->db->query("SELECT * from disp_field where id_dispatcher = '".$id_desp."' order by `typedb`, `order`, `id`");
        $result = $query->result();
        $badge_rea = '<?= ( isset($since) ? "<div class=\'text-right\'><span class=\'badge text-info\'>Pre-agendado: ".substr($since, 0, 16)."</span></div>" : \'\' ) ?>';
        $archiv_content = "<h4>Despachador</h4>$badge_rea\n<form id='desp_data' method='post'>
        <input type='hidden' name='id_desp' value='$id_desp' />
        <input type='hidden' name='uniqueid' value='<?php echo $"."uniqueid; ?>' />
        <input type='hidden' name='id' value='<?php echo $"."id; ?>' />\n";
        foreach ($result as $key => $campo) {
            $archiv_content .= $this->generate_html($campo);
        }
        $archiv_content .= "<br />\n
        <div class='row'>\n
        <div class='col'><input type='submit' class='btn btn-info' name='parcial' id='d_parcial_btn' value='Parcial'></div>\n
        <div class='col text-center'><input type='submit' class='btn btn-primary' name='final'  id='d_final_btn' value='Finalizar'></div>\n";
        if ($tipo == "manual" ) {
            $archiv_content .= "<?= isset(".'$since'.") ? '' : '<div class=\'col text-right\'><button class=\'btn btn-secondary\' id=\'d_saltar_btn\'>Saltar</button></div>' ?>\n";
        }
        $archiv_content .= "</div><div class='row'>&nbsp;</div>\n<div class='row'>\n
        <div class='col'><input type='datetime-local' name='postponedate' class='postponedate form-control'></div>\n
        <div class='col'><input type='submit' class='btn btn-secondary' id='d_postpone' value='Agendar'></div></div>\n
        </form>\n";
        $archivo = fopen(APPPATH.'views/despachador/desp_form_'.$id_desp.'.php', 'w');
        fwrite($archivo, $archiv_content);
        fclose($archivo);
    }

    private function generate_html($field) {
        $html = "";
        $readonly = ($field->readonly == 1) ? "readonly" : "";
        $required = ($field->required == 1 && $field->readonly == 0) ? "required" : "";
        $reqlabel = ($required == 'required' || ($field->typedb==1 && $field->slug != "comentarios")) ? "<span class='esrojo'>*</span>" : "";
        $sfdes = ($field->sfdes == 1) ? " sfdes' style='display:none'" : "'";
        if ($field->name == 'qualif') $field->name = "Tipificación";
        if($field->typedb == '1') { $field->slug = $field->slug."1"; }
        switch ($field->type) {
            case "date":
                $esteval = ($field->typedb==0) ? "<?php echo ($".$field->slug.
                    "!=\"0000-00-00\" && !empty($".$field->slug.
                    ")) ? date(\"Y-m-d\", strtotime($".$field->slug.")) : \"\"; ?>" : "";
                $html = "<div class='input-group$sfdes>
                <div class='input-group-prepend'>
                <label class='input-group-text' for='$field->slug'>$field->name $reqlabel</label>
                </div>
                <input type='date' class='form-control date' name='$field->slug'
                value='$esteval' $required $readonly>
                </div>\n";
            break;
            case "datetime":
                $esteval = ($field->typedb==0) ? "<?php echo ($".$field->slug.
                    "!=\"0000-00-00 00:00:00\" && !empty($".$field->slug.
                    ")) ? date(\"Y-m-d\TH:i\", strtotime($".$field->slug.")) : \"\"; ?>" : "";
                $html = "<div class='input-group$sfdes>
                <div class='input-group-prepend'>
                <label class='input-group-text' for='$field->slug'>$field->name $reqlabel</label>
                </div>
                <input type='datetime-local' class='form-control date' name='$field->slug'
                value='$esteval' $required $readonly>
                </div>\n";
            break;
            case "textarea":
                $esteval = ($field->typedb==0) ? "<?php echo $".$field->slug."; ?>" : "";
                $html = "<div class='input-group$sfdes>
                <div class='input-group-prepend'>
                <label class='input-group-text' for='$field->slug'>$field->name $reqlabel</label>
                </div>
                <textarea class='form-control' id='".$field->slug."' name='".$field->slug.
                "' $required $readonly>$esteval</textarea>
                </div>\n";
            break;
            case "checkbox":
                $esteval = ($field->typedb==0) ? "<?php echo $".$field->slug."; ?>" : "";
                $html = "<div class='form-check'>
                <input type='checkbox' class='form-check-input' id='".$field->slug.
                "' name='".$field->slug."' value='$esteval' $required $readonly>
                <label class='form-check-label' for='$field->slug'>$field->name $reqlabel</label>
                </div>\n";
            break;
            case "dropdown":
                $esteval = ($field->typedb==0) ? "<?php echo $".$field->slug."; ?>" : "";
                $masdata = $clase = $descen = $descencol = $parent = $opts = $campo = "";
                if(!empty($field->showform)) {
                    $clase =  " showform";
                    $masdata = " data-showform='".$field->showform."'";
                }
                $html = "<div class='input-group$sfdes>
                <div class='input-group-prepend'>
                <label class='input-group-text' for='$field->slug'>$field->name $reqlabel</label>
                </div>\n";
                if (substr($field->options, 0, 3) == "cat") {
                    if (substr($field->options, 0, 4) == "cat_") {
                        $opts .= "<option value='' data-id=''>-- Elige --</option>\n";
                        $masdata .= " data-parent='".substr($field->options, 4)."'";
                    } else {
                        $query = $this->db->query("SELECT id, val, eti from disp_" . $field->id_dispatcher .
                        "_cats WHERE field = ? ORDER BY seq, eti", [$field->slug]);
                        $res = $query->result();
                        $opts .= "<option value='0' data-id='0' selected>-- Elige --</option>\n";
                        foreach ($res as $row) {
                            $opts .= "<option value='".$row->val."' data-id='".$row->id."'>".$row->eti."</option>\n";
                        }
                        $masdata .= " data-parent='0'";
                    }
                    if ($field->depend==1) {
                        $clase .= " tienedep";
                        $masdata .= " data-tabla='disp_".$field->id_dispatcher."_cats'";
                    }
                } else if ($field->depend==1) {
                    $query = $this->db->query("SELECT distinct(val1) ops from disp_depend where campo = ?", array($field->options));
                    $res = $query->result();
                    $opts .= "<option value=''>-- Elige --</option>\n";
                    foreach ($res as $key => $row) {
                        $opts .= "<option value='".$row->ops."'>".$row->ops."</option>\n";
                    }
                    $query = $this->db->query("SELECT slug from disp_field where id_dispatcher=? and options=? and depend=?",
                        array($field->id_dispatcher, $field->options, $field->depend + 1)
                    );
                    if ($query->num_rows()>0) {
                        $res = $query->row();
                        $descen = $res->slug;
                        $descencol = $field->depend + 1;
                        $campo = $field->options;
                        $clase .= " ajaxdep";
                    }
                } elseif ($field->depend>1) {
                    $opts .= "<option value=''>-- Elige --</option>\n";
                    $query = $this->db->query("SELECT slug from disp_field where id_dispatcher=? and options=? and depend=?",
                        array($field->id_dispatcher, $field->options, $field->depend + 1)
                    );
                    if ($query->num_rows()>0) {
                        $res = $query->row();
                        $descen = $res->slug;
                        $descencol = $field->depend + 1;
                        $campo = $field->options;
                        $clase .= " ajaxdep";
                    }
                } elseif (isJson((string)$field->options)) {
                    $values = json_decode($field->options, true);
                    $profundo = arrayDepth($values);
                    if ($profundo == 2) {
                        $descen = key($values);
                        $values = $values[$descen];
                        $clase .= " condescen";
                    } elseif ($profundo==3) {
                        $values = reset($values);
                        foreach ($values as $key => $val1) {
                            foreach ($val1 as $val) {
                                $opts .= "<option data-parent='".$key."' value='".$val.
                                "' <?php if ($".$field->slug."=='".$val."') echo 'selected'; ?>>".$val."</option>\n";
                            }
                        }
                    }
                } else if (strpos($field->options, ',') !== false) {
                    $values = explode(",", str_replace(", ",",",preg_replace('!\s+!', ' ',trim($field->options))));
                }
                if($field->typedb == '1') { $descen = $descen."1"; }
                $html .= "<select class='form-control".$clase."' data-descen='".$descen."' data-descencol = '".
                    $descencol."' data-descencampo='".$campo."' $masdata id='".$field->slug."' name='".$field->slug."' $required $readonly>\n";
                if (empty($opts)) {
                    if ($field->typedb == 0) {
                        foreach ($values as $key => $val) {
                            $opts .= "<option value='".$val.
                            "' <?php if ($".$field->slug."=='".$val."') echo 'selected'; ?>>".$val."</option>\n";
                        }
                    } else {
                        $opts .= "<option value=''>-- Elige --</option>\n";
                        foreach ($values as $key => $val) {
                            $opts .= "<option value='".$val."'>".$val."</option>\n";
                        }
                    }
                }
                $html .= $opts;
                $html .= "</select>\n</div>\n";
            break;
            case "radio":
                $values = explode(",", preg_replace('!\"+|\[+|\]+!','',str_replace('"', '', str_replace(", ",",",preg_replace('!\s+!', ' ',trim($field->options))))));
                $html = "<p>".$field->name."</p>\n";
                $count = 0;
                foreach ($values as $val) {
                    $html.="<div class='form-check form-check-inline'>";
                    $html.="<input class='form-check-input' type='radio' name='".$field->slug."'";
                    if ($count==0) {
                        $html.=" checked ";
                    }
                    $html.=" id='".$field->slug."' value='".$val."' >";
                    $html.="<label class='form-check-label' for='".$field->slug."'>".$val."</label>";
                    $html.="</div>\n";
                    $count++;
                }
            break;
            case "url":
                $esteval = ($field->typedb==0) ? "<?php echo $".$field->slug."; ?>" : "";
                $html = "
                <div class='d-block input-group$sfdes>
                    <a class='btn btn-primary d-block' href='$esteval' target='_blank' role='button'>$field->name</a>
                </div>\n";
            break;
            default:
                $esteval = ($field->typedb==0) ? "<?php echo $".$field->slug."; ?>" : "";
                $html = "<div class='input-group$sfdes>
                <div class='input-group-prepend'>
                <label class='input-group-text' for='$field->slug'>$field->name $reqlabel</label>
                </div>
                <input type='text' class='form-control' id='".$field->slug."' name='".$field->slug.
                "' value='$esteval' $required $readonly>
                </div>\n";
            break;
        }
        return $html;
    }

    public function actualiza_registro($data) {
        $query  = $this->db->query("SELECT autodial FROM dispatcher WHERE id = ?", [$data['id_desp']]);
        $res    = $query->row();
        $llamas = (stripos($res->autodial, 'pred') === false) ? "llamadas = llamadas+1," : "";
        $status = ($data['accion'] == 'final') ? 1 : 0;
        $setsf = "last_update = now(), $llamas status='$status'";
        $camposq = "`id_disp_data`, `uniqueid`, `linkedid`, `saved_by`, `saved_when`";
        $setsq   = "";
        $linkedid = "";
        if (!empty($data['uniqueid'])) {
            $query = $this->db->query("SELECT linkedid FROM asteriskcdrdb.cel
                WHERE uniqueid = ? LIMIT 1", [$data['uniqueid']]);
            $linkedid = $query->row()->linkedid ?? "";
        }
        $valsq   = array($data['id'], $data['uniqueid'], $linkedid, $this->session->userdata('uid'));
        $query = $this->db->query("SELECT slug, typedb, depend FROM disp_field
            WHERE id_dispatcher=? AND type != 'url'", [$data['id_desp']]);
        $campos = $query->result();
        foreach ($campos as $key => $campo) {
            if ($campo->typedb==0) {
                $setsf .= ", `".$campo->slug."`='".$data[$campo->slug]."'";
            } else {
                $camposq .= ", `".$campo->slug."`";
                $setsq .= ", ?";
                $valsq[] = $data[$campo->slug."1"];
            }
        }

        $this->db->query("INSERT INTO disp_".$data['id_desp']."_qualif ($camposq) values (?, ?, ?, ?, now() $setsq)", $valsq);
        return $this->db->query("UPDATE disp_".$data['id_desp']." set $setsf, qualif = '".
            $data['tipificacion1']."', since = NULL, busy = NULL where id=?", [$data['id']]);
    }

    public function get_field($id) {
        $query = $this->db->query("SELECT * from disp_field where `id`=?", array($id));

        return $query->row();
    }

    public function add_field($data) {
        if( !empty($data['opciones']) ) {
            if(stripos($data['opciones'], ",") !== FALSE) {
                $options = strToJson($data['opciones']);
            } else {
                $options = $data['opciones'];
            }
        } else {
            $options = "";
        }
        $readonly = (!empty($data['readonly'])) ? 1 : 0;
        $required = (!empty($data['required'])) ? 1 : 0;
        $order = (!empty($data['order'])) ? $data['order'] : 0;
        if (file_exists(APPPATH.'views/despachador/desp_form_'.$data['id_desp'].'.php')) {
            unlink(APPPATH.'views/despachador/desp_form_'.$data['id_desp'].'.php');
        }
        if( $data['typedb'] == '0' ) {
            if ($this->db->query("ALTER TABLE `disp_".$data['id_desp'] . "` ADD `" .
                slugify($data['name']) . "` " . $this->tipos($data['type']))) {
                return $this->db->query("INSERT into disp_field values (0,?,?,?,?,?,'0',?,?,'0',?,?,?)",
                array($data['id_desp'], $data['name'], slugify($data['name']), $data['type'], $data['typedb'] ,$data['depend'], $options, $readonly, $required, $order));
            }
        } else {
            if ($this->db->query("ALTER TABLE `disp_".$data['id_desp'] . "_qualif` ADD `" .
                slugify($data['name']) . "` " . $this->tipos($data['type']))) {
                return $this->db->query("INSERT into disp_field values (0,?,?,?,?,?,'0',?,?,'0',?,?,?)",
                array($data['id_desp'], $data['name'], slugify($data['name']), $data['type'], $data['typedb'] ,$data['depend'], $options, $readonly, $required, $order));
            }
        }
    }

    public function upd_field($data) {
        if( !empty($data['opciones']) ) {
            if(stripos($data['opciones'], ",") !== FALSE) {
                $options = strToJson($data['opciones']);
            } else {
                $options = $data['opciones'];
            }
        } else {
            $options = "";
        }
        $readonly = (!empty($data['readonly'])) ? 1 : 0;
        $required = (!empty($data['required'])) ? 1 : 0;
        $order = (!empty($data['order'])) ? $data['order'] : 0;
        $slug = slugify($data['name']);
        $id_desp = $data['id_desp'];
        if (file_exists(APPPATH.'views/despachador/desp_form_'.$id_desp.'.php')) {
            unlink(APPPATH.'views/despachador/desp_form_'.$id_desp.'.php');
        }
        if( $data['typedb'] == '0' ) {
            if ($this->db->query("ALTER TABLE `disp_".$id_desp."` CHANGE `".$data['oldname'].
                "` `".$slug."` ".$this->tipos($data['type']))) {
                return $this->db->query("UPDATE disp_field set `name`=?, `slug`=?, `type`=?, `depend`=?,
                    `options`=?, `readonly`=?, `required`=?, `order`=? WHERE `id`=?",
                    array($data['name'], $slug, $data['type'], $data['depend'], $options, $readonly, $required, $order, $data['id']));
            }
        } else {
            if ($this->db->query("ALTER TABLE `disp_".$id_desp."_qualif` CHANGE `".$data['oldname'].
                "` `".$slug."` ".$this->tipos($data['type']))) {
                return $this->db->query("UPDATE disp_field set `name`=?, `slug`=?, `type`=?, `depend`=?,
                    `options`=?, `readonly`=?, `required`=?, `order`=? WHERE `id`=?",
                    array($data['name'], $slug, $data['type'], $data['depend'], $options, $readonly, $required, $order, $data['id']));
            }
        }
        return $this->db->error();
    }

    public function del_field($data) {
        $id_desp = $data['id_desp'];
        if (file_exists(APPPATH.'views/despachador/desp_form_'.$id_desp.'.php')) {
            unlink(APPPATH.'views/despachador/desp_form_'.$id_desp.'.php');
        }
        if( $data['typedb'] == '0' ) {
            if ($this->db->query("ALTER TABLE `disp_".$id_desp."` drop `".$data['slug']."`")) {
                return $this->db->query("DELETE FROM disp_field WHERE id =?", array($data['id']));
            }
        } else {
            if ($this->db->query("ALTER TABLE `disp_".$id_desp."_qualif` drop `".$data['slug']."`")) {
                return $this->db->query("DELETE FROM disp_field WHERE id =?", array($data['id']));
            }
        }
        return $this->db->error();
    }

    private function tipos($tipo) {
        $tipos = array(
            "text"     => "varchar(255) NOT NULL DEFAULT ''",
            "textarea" => "text NOT NULL DEFAULT ''",
            "checkbox" => "tinyint(1) NOT NULL DEFAULT '0'",
            "dropdown" => "varchar(255) NOT NULL DEFAULT ''",
            "radio"    => "varchar(255) NOT NULL DEFAULT ''",
            "datetime" => "datetime DEFAULT NULL",
            "date"     => "date DEFAULT NULL",
            "url"      => "varchar(255) NOT NULL DEFAULT ''",
        );
        return $tipos[$tipo];
    }

    public function get_despachadores($ids) {
        $query = $this->db->query("SELECT id, name from dispatcher where active = 1 AND id_campaign IN (".$ids.") ");
        $posibles = $query->result();
        $data = array();
        foreach ($posibles as $key => $row) {
            if ($this->db->table_exists('disp_'.$row->id)) {
                $data[$row->id] = $row->name;
            }
        }
        return $data;
    }

    public function despachador_detalle($data) {
        if ($this->db->table_exists('disp_'.$data['despachador']) && $this->db->table_exists('disp_'.$data['despachador'].'_qualif')) {
            $query  = $this->db->query("SELECT id from disp_".$data['despachador']);
            $campos = $this->db->list_fields("disp_".$data['despachador']);
            $camqu  = "d.`id` 'Id', d.`telefono` 'Teléfono', ";
            $cuenta = 0;
            $noadd  = ["id", "telefono", "since", "status", "last_update", "busy", "invalid", "qualif", "access", "added"];
            foreach ($campos as $key => $value) {
                if (!in_array($value, $noadd)) {
                    $nom = traduce($value);
                    if ($data['pag'] !== 'x') {
                        $camqu .= "substr(d.`".$value."`,1 ,20) `".ucfirst($nom)."`, ";
                    } else {
                        $camqu .= "d.`".$value."` `".ucfirst($nom)."`, ";
                    }
                    $cuenta++;
                }
                if ($cuenta == 2 && $data['pag'] !== 'x') break;
            }
            $and = "";
            if( isset($data["estatus"]) && $data["estatus"] != "" ) $and .= " AND d.status = '$data[estatus]' ";
            $tabla = 'disp_'.$data['despachador'];
            $campo = 'since';
            $CamposExtra = "";
            $data['since'] = false;
            if($this->db->field_exists($campo, $tabla)){
                $data['since'] = true;
                $CamposExtra .= ", COALESCE(date_format(d.since, '$this->dtfor'), '') `Agendado` ";
                if( isset($data["agendado"]) && $data["agendado"] != "" ) {
                    if( $data["agendado"] == "si" ) $and .= " AND d.since IS NOT NULL ";
                    if( $data["agendado"] == "no" ) $and .= " AND d.since IS NULL ";
                }
            }
            $data['prequery'] = "SELECT $camqu
                if(d.`status`!=0, 'Cerrado', 'Abierto') 'Estatus', d.`access` 'Despliegues',
                coalesce(date_format(d.`added`, '$this->dtfor'),'') 'Agregado',
                coalesce(date_format(min(t.saved_when), '$this->dtfor'),'') 'Primer llamada',
                coalesce(IF(DATEDIFF(min(t.saved_when), d.added)>365,'+1 año',
                IF(DATEDIFF(min(t.saved_when), d.added)>30,'+1 mes',timediff(min(t.saved_when), d.added))), '') 'Atención',
                coalesce(date_format(d.`last_update`, '$this->dtfor'),'') 'Última llamada',
                coalesce(`qualif`,'') 'Última tipificación' $CamposExtra
                FROM disp_".$data['despachador']." d
                LEFT JOIN disp_".$data['despachador']."_qualif t ON t.id_disp_data = d.id
                WHERE 1=1 $and
                GROUP BY d.id";

            return $this->datos_model->manejadorqueries($data);
        }

        return false;
    }

    public function despachador_llamadas($data) {
        if ($this->db->table_exists('disp_'.$data['despachador']) && $this->db->table_exists('disp_'.$data['despachador'].'_qualif')) {
            $maswere = (empty($data['tipificacion'])) ? "" : "AND dq.tipificacion = '$data[tipificacion]'";
            $campost = $this->db->list_fields("disp_".$data['despachador']."_qualif");
            $cuenta  = 0;
            $camqut  = "";
            $excluir = ["id", "uniqueid", "saved_by", "saved_when"];
            if ($data['pag'] !== 'x') {
                $excluir[] = "id_disp_data";
            }
            foreach ($campost as $key => $value) {
                if (!in_array($value, $excluir)) {
                    if ($data['pag'] !== 'x') {
                        $camqut .= "substr(dq.`".$value."`,1 ,40) ".ucfirst($value).", ";
                    } else {
                        $camqut .= "dq.`".$value."` ".ucfirst($value).", ";
                    }
                    $cuenta++;
                }
                if ($cuenta == 4 && $data['pag'] !== 'x') break;
            }
            if ($data['agente'] !== 'todos') {
                $maswere = "AND dq.saved_by IN ($data[agente])" . $maswere;
            }
            $data['prequery'] = "SELECT date_format(dq.saved_when, '$this->dtfor') Fecha,
                if(dq.saved_by != 1, concat(u.name,' ',u.last), '') Agente,
                d.telefono Teléfono, $camqut
                if(d.status=0, 'Abierto', 'Cerrado') Estatus
                FROM disp_".$data['despachador']."_qualif dq
                inner join disp_".$data['despachador']." d on d.id = dq.id_disp_data
                inner join user u on u.id = dq.saved_by
                where date(dq.saved_when) between '$data[min]' and '$data[max]' $maswere
                order by d.`telefono`, dq.`saved_when`";

            return $this->datos_model->manejadorqueries($data);
        }
        return false;
    }

    public function despachador_llamadas_full($data) {
        if ($this->db->table_exists('disp_'.$data['despachador']) && $this->db->table_exists('disp_'.$data['despachador'].'_qualif')) {
            $maswere = (empty($data['tipificacion'])) ? "" : "AND dq.tipificacion = '$data[tipificacion]'";
            $campost = $this->db->list_fields("disp_".$data['despachador']."_qualif");
            $cuenta  = 0;
            $camqut  = "";
            $paraexcel = "";
            $excluir = ["id", "uniqueid", "saved_by", "saved_when"];
            if ($data['pag'] !== 'x') {
                $paraexcel = "";
                $excluir[] = "id_disp_data";
            } else {
                $paraexcel = $this->db->query("SELECT group_concat(concat(concat('d.',slug),concat(' AS ',concat('\'',concat(name,'\''))))) camps
                    from disp_field where id_dispatcher = 47 AND type <> 'tabla' AND type <> 'separador' AND typedb = 0
                    AND slug not in ('id','llamadas','invalid','access','busy','status','last_update','added','since')")->row()->camps;
                $paraexcel .= (!empty($paraexcel)) ? "," : "";
            }
            foreach ($campost as $key => $value) {
                if (!in_array($value, $excluir)) {
                    if ($data['pag'] !== 'x') {
                        $camqut .= "substr(dq.`".$value."`,1 ,40) ".ucfirst($value).", ";
                    } else {
                        $camqut .= "dq.`".$value."` ".ucfirst($value).", ";
                    }
                    $cuenta++;
                }
                if ($cuenta == 4 && $data['pag'] !== 'x') break;
            }
            if ($data['agente'] !== 'todos') {
                $maswere = "AND dq.saved_by IN ($data[agente])" . $maswere;
            }
            $data['prequery'] = "SELECT $paraexcel date_format(dq.saved_when, '$this->dfor') Fecha,
                if(dq.saved_by != 1, concat(u.name,' ',u.last), '') Agente,
                d.telefono Teléfono, $camqut
                if(d.status=0, 'Abierto', 'Cerrado') Estatus
                FROM disp_".$data['despachador']."_qualif dq
                inner join disp_".$data['despachador']." d on d.id = dq.id_disp_data
                inner join user u on u.id = dq.saved_by
                where date(dq.saved_when) between '$data[min]' and '$data[max]' $maswere
                order by d.`telefono`, dq.`saved_when`";

            return $this->datos_model->manejadorqueries($data);
        }
        return false;
    }

    public function buscar($data) {
        //Comprobamos si tiene la columna cliente
        $n = $this->valida_columna_en_tabla('disp_'.$data['id_desp'],'cliente');
        $where_cliente = ($n == 1) ? " OR cliente like '%".$data['buscar']."%'" : '';
        $campos = $this->db->list_fields('disp_'.$data['id_desp']);
        $contador = 0;
        $camqu = $n == 1 ? "`id`, `telefono`, `cliente`, " : "`id`, `telefono`, ";
        foreach ($campos as $key => $value) {
            if ($value != 'id' && $value != 'telefono' && $key > 2) {
                $camqu .= "`".$value."`";
                $contador++;
                if ($contador==3 || ($key+1) == count($campos)) {
                    break;
                } else {
                    $camqu .= ", ";
                }
            }
        }
        $query = $this->db->query("SELECT $camqu from disp_".$data['id_desp'].
        " where telefono like '%".$data['buscar']."%' $where_cliente  order by telefono limit 10");
        $res = $query->result();
        $camres = $query->field_data();
        $camarra = array();
        foreach ($camres as $key => $cam) {
            $camarra[] = $cam->name;
        }
        if (empty($res)) {
            $respu = '<hr><p>Sin resultados</p>';
        } else {
            $respu = '<hr>';
            foreach ($res as $key => $row) {
                $respu .= '<a class="btn btn-link despreg" data-id="'.$row->id.'"> ' . $row->telefono . ' ';
                foreach ($camarra as $cam) {
                    if ($cam != "id" && $cam != "telefono") {
                        $respu .= " - ".$row->$cam;
                    }
                }
                $respu .= '</a><br>';
            }
        }
        return $respu;
    }

    //validamos si existe el campo cliente en la tabla de disp_n
    public function valida_columna_en_tabla($table, $column) {
        $query = $this->db->query('SELECT count(*) n FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?  AND COLUMN_NAME = ?', [$table, $column]);
        $row = $query->row();
        return $row->n;
    }

    public function despachador_indicadores($data) {
        if ($this->db->table_exists('disp_'.$data['despachador'])) {
            $data['prequery'] = "SELECT COUNT(id) totreg,
                COALESCE(SUM(IF((status=1),1,0)),0) cerradas,
                COALESCE(SUM(IF((llamadas=0),1,0)),0) sintocar,
                COALESCE(SUM(IF((access!=0),1,0)),0) despliegues,
                COALESCE(SUM(IF((llamadas!=0 AND status=0),1,0)),0) parcial,
                COALESCE(SUM(IF((status=0),1,0)),0) abiertas
                FROM disp_".$data['despachador'];

            return $this->datos_model->manejadorqueries($data);
        }

        return false;
    }

    public function reporte_indicador_data($id_desp) {
        if ($this->db->table_exists('disp_'.$id_desp)) {
            $query = $this->db->query("SELECT
                    COUNT(id) totreg,
                    COALESCE(SUM(IF((status=1),1,0)),0) cerradas,
                    COALESCE(SUM(IF((llamadas=0),1,0)),0) sintocar,
                    COALESCE(SUM(IF((access!=0),1,0)),0) despliegues,
                    COALESCE(SUM(IF((llamadas!=0 AND status=0),1,0)),0) parcial,
                    COALESCE(SUM(IF((status=0),1,0)),0) abiertas
                    FROM disp_$id_desp");
            $data['data'] = $query->row();
            $query = $this->db->query("SELECT qualif,
                        count(id) total,
                        COALESCE(SUM(IF((status=1),1, 0)), 0) cerrado,
                        COALESCE(SUM(IF((status=0),1, 0)), 0) abierto
                    FROM disp_$id_desp GROUP BY qualif ORDER BY qualif ASC");
            $data['tipi'] = $query->result();

            return $data;
        }
        return false;
    }

    public function monitor_data($id_desp) {
        if ($this->db->table_exists('disp_'.$id_desp)) {
            $query = $this->db->query("SELECT d.*, c.name campana from dispatcher d
                left join campaign c on c.id = d.id_campaign where d.id = ?", [$id_desp]);
            $data['disp'] = $query->row();
            $query = $this->db->query("SELECT
                COUNT(id) totreg,
                COALESCE(SUM(IF((status=1),1,0)),0) cerradas,
                COALESCE(SUM(IF((llamadas=0),1,0)),0) sintocar,
                COALESCE(SUM(IF((access!=0),1,0)),0) despliegues,
                COALESCE(SUM(IF((llamadas!=0 AND status=0),1,0)),0) parcial,
                COALESCE(SUM(IF((status=0),1,0)),0) abiertas
                FROM disp_$id_desp");
            $data['data'] = $query->row();
            $query = $this->db->query("SELECT qualif,
                count(id) total,
                COALESCE(SUM(IF((status=1),1, 0)), 0) cerrado,
                COALESCE(SUM(IF((status=0),1, 0)), 0) abierto
                FROM disp_$id_desp GROUP BY qualif ORDER BY qualif ASC");
            $data['tipi'] = $query->result();
            $query = $this->db->query("SELECT * from disp_cond where id_dispatcher = ?", [$id_desp]);
            $data['cond'] = $query->result();
            $colas = colas();
            $data['ocu'] = $data['desocu'] = 0;
            if ($data['disp']->queue!='') {
                $data['cola'] = !empty($colas[$data['disp']->queue]) ? $colas[$data['disp']->queue] : ['members' => []];
                $members = '';
                foreach ($data['cola']['members'] as $key => $value) {
                    $members .= $key.',';
                }
                $members = rtrim($members, ',');
                $members = (strlen($members)==0) ? "'A'" : $members;
                $query = $this->db->query("SELECT u.id, concat(u.name,' ',u.last) agente, ud.val ext
                    from user u
                    left join user_data ud on ud.id_user = u.id
                    left join catalogs c on c.id = ud.id_catalog
                    where c.cat = 'userData' AND c.val = 'userask' AND ud.val in ($members)");
            } else {
                $query = $this->db->query("SELECT u.id, concat(u.name,' ',u.last) agente, ud.val ext
                    from disp_user du left join user u on u.id = du.id_user
                    left join user_data ud on ud.id_user = u.id
                    left join catalogs c on c.id = ud.id_catalog
                    where c.cat = 'userData' AND c.val = 'userask' AND id_dispatcher = ?", [$id_desp]);
            }
            $data['user'] = $query->result();
            $arraycolores = array(
                "Desconectado" => "#7D7D7D", // gris
                "En llamada"   => "#E92C26", // rojo
                "Llamando"     => "#FF9330", // naranja
                "comida"       => "#00972E", // verde
                "sanitario"    => "#BD7032", // cafe
                "acw"          => "#A30094", // morado
                "retro"        => "#0099C2", // turki
                "break"        => "#930017", // guinda
                "Disponible"   => "#3266C6", // azul
                "Otro"         => "#EA3976"  // Rosa
            );
            $ctxOps = ["ssl"=>["verify_peer"=>false,"verify_peer_name"=>false]];
            $ariserv = "https://" . getenv('ARI_USER') . ":" . getenv('ARI_PASS') . "@" . getenv('ASS_DB_HOST') . ":8089";
            $exts = json_decode(file_get_contents($ariserv . "/ari/endpoints/PJSIP", false, stream_context_create($ctxOps)));
            $salida = $eventos = array();
            foreach ($exts as $key => $value) {
                $salida[$value->resource]['stat'] = $value->state;
                $salida[$value->resource]['chans'] = $value->channel_ids;
            }
            $query = $this->db->query("SELECT user_id, detalle evto,
                time_format(TIMEDIFF(now(), evento), '%H:%i:%s') `time`,
                time_to_sec(TIMEDIFF(now(), evento)) `timecalc`
                from user_log where id in (select max(id) id from user_log group by user_id) order by user_id");
            $times = $query->result();
            foreach ($times as $key => $row) {
                $eventos[$row->user_id]['time'] = $row->time;
                $eventos[$row->user_id]['timecalc'] = $row->timecalc;
                $eventos[$row->user_id]['evto'] = $row->evto;
            }
            $data['ocu'] = $data['desocu'] = $data['loged'] = 0;
            foreach ($data['user'] as $key => $row) {
                if (!empty($eventos[$row->id])) {
                    $valfin = (substr($eventos[$row->id]['evto'], 0, 8) == "Descanso") ?
                        substr($eventos[$row->id]['evto'], 10) :
                        $eventos[$row->id]['evto'];
                    $valfin = (substr($valfin, 0, 20) == "En llamada, llamando") ? "Llamando" : $valfin;
                    $minutos = (int)explode(":",$eventos[$row->id]['time']);
                    if ($eventos[$row->id]['evto'] == "Logout" || $eventos[$row->id]['timecalc']>14400) $valfin = "Desconectado";
                } else {
                    $valfin = "Desconectado";
                }
                $data['user'][$key]->statgraf = (array_key_exists($valfin, $arraycolores)) ? $valfin : "Otro";
                // if (!empty($salida[$row->ext]) && $salida[$row->ext]['stat']=='online') {
                if ($valfin != "Desconectado" && isset($salida[$row->ext]['stat']) && $salida[$row->ext]['stat'] != 'offline') {
                    $data['loged']++;
                    $data['user'][$key]->exstat = $salida[$row->ext]['stat'];
                    $data['user'][$key]->acttime = $eventos[$row->id]['time'];
                    if (count($salida[$row->ext]['chans'])>0 || substr($eventos[$row->id]['evto'], 0, 8) == "Descanso") {
                        $data['ocu']++;
                        $data['user'][$key]->estatus = 'Ocupado';
                        $data['user'][$key]->act = ($eventos[$row->id]['evto'] == "Disponible" || $eventos[$row->id]['evto'] == "Entra consola") ? "" : $eventos[$row->id]['evto'];
                    } else {
                        $data['desocu']++;
                        $data['user'][$key]->estatus = 'Disponible';
                        $data['user'][$key]->act = '';
                        $data['user'][$key]->statgraf = 'Disponible';
                    }
                } else {
                    $data['user'][$key]->estatus = 'Desconectado';
                    $data['user'][$key]->exstat = 'offline';
                    $data['user'][$key]->acttime = '';
                    $data['user'][$key]->act = '';
                    $data['user'][$key]->statgraf = 'Desconectado';
                }
                $data['user'][$key]->color = $arraycolores[$data['user'][$key]->statgraf];
            }

            return $data;
        }
        return false;
    }

    public function traetipi($id_desp) {
        $query = $this->db->query("SELECT * from `disp_field`
            where `id_dispatcher` = ? and `slug` = 'tipificacion'", array($id_desp));
        $field = $query->row();
        if (isJson((string)$field->options)) {
            $values = json_decode($field->options, true);
        } else {
            $query = $this->db->query("SELECT distinct(val1) val from `disp_depend` where `campo` = '$field->options'");
            $pre = $query->result();
            $values = array();
            foreach ($pre as $key => $row) {
                $values[] = $row->val;
            }
        }
        return $values;
    }

    public function getActivo() {
        $query = $this->db->query("SELECT * from dispatcher
            where (autodial='predictivo' or autodial='predictivoamd')
            and running = 1");
        return $query->result();
    }

    public function lanzar($id_disp, $cola) {
        $numeros = array();
        $lanzar = $med_llam = 0;
        $tret = 30;
        // Verifica que el despachador sigue activo y corriendo
        $query = $this->db->query("SELECT * from dispatcher where id=? AND active = '1' and running = '1'", array($id_disp));
        if ($pred = $query->row()) {
            // Verifica las condiciones de tiempo
            if (!$this->db->field_exists('since', 'disp_'.$id_disp)) {
                // Agrega el campo since a la tabla del despachador
                $this->db->query("ALTER TABLE disp_" . $id_disp .
                    " ADD `since` datetime DEFAULT NULL");
            }
            $maswhere = " AND (`since` is null OR `since` <= now())";
            $query = $this->db->query("SELECT * from `disp_cond`
                where `id_dispatcher` = ? and `hora` < time(now()) order by `hora` DESC limit 1", array($id_disp));
            if ($cond = $query->row()) {
                if ($cond->accion == '2') return "Despachador pausado";
                if (!empty($cond->tipi)) $maswhere .= " AND `qualif` = '$cond->tipi'";
                if (!empty($cond->campo) && !empty($cond->camcond)) {
                    $valores = explode(",", $cond->camcond);
                    if (count($valores) == 1) {
                        $maswhere .= " AND `$cond->campo` = '$cond->tipi'";
                    } else {
                        $maswhere .= " AND (";
                        foreach ($valores as $valor) {
                            $maswhere .= "`$cond->campo` = '$valor' or ";
                        }
                        $maswhere = rtrim($maswhere," or ").")";
                    }
                }
            }
            $regsdisp = $this->db->query("SELECT count(*) as 'tot' from `disp_" . $id_disp .
                    "` WHERE `status`=0 and `invalid`=0 $maswhere")->row();
            // ToDo, Auto - apagar despachador condicionalmente
            // $query = $this->db->query("SELECT count(*) AS 'cuenta'
            //     from `disp_" . $id_disp . "` WHERE `status` = 0 and `invalid` = 0");
            // if ($query->row()->cuenta == 0) {
            //     // Desactiva el despachador por no haber más registros por marcar
            //     $this->db->query("UPDATE dispatcher set running=0 where id=".$id_disp);
            // } else {
            if ($regsdisp->tot > 0) {
                // Promedio duración de llamada, mínimo 40 segundos
                $query = $this->db->query("SELECT
                    if((IFNULL(ROUND(AVG(data.duration)),0)+15)<60,60,(IFNULL(ROUND(AVG(data.duration)),0)+15)) 'avg'
                    FROM (SELECT `duration` FROM `call_entry`
                    WHERE `cid_name` like '%pred-".$id_disp."%' AND `duration`>5 AND `status`='Terminada'
                    ORDER BY id DESC LIMIT 10) data");
                $res = $query->row();
                $med_llam = $res->avg;
                // Promedio de retorno de llamada desde gateway, mínimo 35 segundos
                $query = $this->db->query("SELECT
                    if((IFNULL(ROUND(AVG(data.tiempo_ret)),0))<35,35,(IFNULL(ROUND(AVG(data.tiempo_ret)),0))) 'avg'
                    FROM (SELECT `tiempo_ret`
                    FROM `disp_sent`
                    WHERE `id_dispatcher` = '".$id_disp."' AND `actu`=1 AND `tiempo_ret`>5
                    ORDER BY id DESC LIMIT 10) data");
                $res = $query->row();
                $med_ret = $res->avg;
                $tiempo_lanzar = $med_llam - $med_ret;
                foreach ($cola['members'] as $key => $value) {
                    $ctxOps = ["ssl"=>["verify_peer"=>false,"verify_peer_name"=>false]];
                    $ariserv = "https://" . getenv('ARI_USER') . ":" . getenv('ARI_PASS') . "@" . getenv('ASS_DB_HOST') . ":8089";
                    $exten = json_decode(file_get_contents($ariserv . "/ari/endpoints/PJSIP/".$key, false, stream_context_create($ctxOps)));
                    if (count($exten->channel_ids) > 0) {
                        $chan = json_decode(file_get_contents($ariserv . "/ari/channels/".$exten->channel_ids[0], false, stream_context_create($ctxOps)));
                        // tiempo en llamada
                        $inicio = new Datetime(date("Y/m/d H:i:s", strtotime($chan->creationtime)));
                        $ahora = new DateTime();
                        $diff = date_diff($inicio, $ahora);
                        $yavan = ($diff->i * 60) + $diff->s;
                        // Traer el promedio de llamada del agente y el promedio de llamada preestablecido
                        if ($yavan >= $tiempo_lanzar) $lanzar++;
                    } elseif($exten->state=='online') {
                        $lanzar++;
                    }
                }
                // Resto las llamadas ya lanzadas a las por lanzar
                $query = $this->db->query("SELECT id from disp_sent where id_dispatcher='".$id_disp."' AND actu=0");
                $lanzar = ($lanzar * $pred->multi) - ($query->num_rows() * $pred->multi);
                $lanzar = ($lanzar > 0) ? $lanzar : 0;
                // Traigo números disponibles del despachador
                $query = $this->db->query("SELECT * from `disp_".$id_disp.
                    "` WHERE `status`=0 and `invalid`=0 $maswhere order by `access`, `id` limit $lanzar");
                $numeros = $query->result();
                $ip = "";
                $last_line = exec('ip addr | grep "inet "', $full_output);
                foreach ($full_output as $key => $value) {
                    $val = explode("/",explode(" ",trim($value))[1])[0];
                    $check = substr($val, 0, 3);
                    if ($check != "127" && $check != "172") {
                        $ip = explode(".", $val)[3];
                        break;
                    }
                }
                if (empty($ip)) {
                    return "IP retorno despachador no legible.";
                }
                if (count($numeros)>0) {
                    $tmpDir='/var/www/';
                    $outDir='/var/spool/asterisk/outgoing/';
                    $contexten = ($pred->autodial=='predictivo') ? 'pred' : 'predamd';
                    $file = 'Channel: SIP/%s/%s
Callerid: "%s" <%s>
Context: predictivo
Extension: '.$contexten.'
Set: COLA=%s
Set: IDPRED=%s
Set: NUMFON=%s
Set: PBX=SIP/Ask'.$ip.'/';
                    if ($pred->gateway == "local") {
                        foreach ($numeros as $key => $row) {
                            $randomFilename = md5($this->microtime_float());
                            $fileResult = sprintf($file, $pred->dialer, $row->telefono, $pred->maskname, $pred->masknum,
                                $pred->queue, $row->telefono."-pred-".$id_disp."-".$row->id, $row->telefono);
                            $handle = fopen($tmpDir.$randomFilename,'x');
                            fputs($handle,$fileResult);
                            fclose($handle);
                            rename($tmpDir.$randomFilename, $outDir.$randomFilename);
                            $this->db->query("UPDATE disp_".$id_disp." set access = access + 1,
                                status = 1 where id = ".$row->id);
                            $this->db->query("INSERT INTO disp_sent
                                (`id_dispatcher`,`id_registro`,`fechahora_lanzada`)
                                values ('".$id_disp."','".$row->id."',now())");
                        }
                    } else {
                        $connection = ssh2_connect($pred->gateway, 22);
                        ssh2_auth_password($connection, 'phonex', '4sk+2017');
                        foreach ($numeros as $key => $row) {
                            $randomFilename = md5($this->microtime_float());
                            $fileResult = sprintf($file, $pred->dialer, $row->telefono, $pred->maskname, $pred->masknum,
                                $pred->queue, $row->telefono."-pred-".$id_disp."-".$row->id, $row->telefono);
                            $handle = fopen($tmpDir.$randomFilename,'x');
                            fputs($handle,$fileResult);
                            fclose($handle);
                            ssh2_scp_send($connection, $tmpDir.$randomFilename, $outDir.$randomFilename, 0644);
                            unlink($tmpDir.$randomFilename);
                            // rename($tmpDir.$randomFilename, $outDir.$randomFilename);
                            $this->db->query("UPDATE disp_".$id_disp." set access = access + 1,
                                status = 1 where id = ".$row->id);
                            $this->db->query("INSERT INTO disp_sent
                                (`id_dispatcher`,`id_registro`,`fechahora_lanzada`)
                                values ('".$id_disp."','".$row->id."',now())");
                        }
                        ssh2_disconnect($connection);
                    }
                }
            }
            // }
            $respu = $lanzar . ', Med Llam: '.$med_llam.'. Med Ret: '.$med_ret;
        } else {
            $respu = "Despachador ". $id_disp . " desactivado.";
        }
        return $respu;
    }

    public function noregresa($id_disp) {
        // Marca llamadas como AMD porque no regresan desde GateWay
        // Proceso iniciado en cron que corre cada minuto
        $query = $this->db->query("SELECT rounds from dispatcher where id = ?", [$id_disp]);
        $desp = $query->row();
        $query = $this->db->query("SELECT * from disp_sent where id_dispatcher='".$id_disp."' AND actu=0
        AND fechahora_lanzada < DATE_SUB(NOW(), INTERVAL 60 SECOND)");
        $res = $query->result();
        foreach ($res as $key => $row) {
            $this->db->query("UPDATE disp_sent set actu=1 where id='$row->id'");
            $this->db->query("INSERT INTO disp_".$id_disp."_qualif
                (`id_disp_data`,`tipificacion`,`saved_by`,`saved_when`) values
                ('".$row->id_registro."','AMD','1',now())");
            $access = ($desp->rounds>0) ? "if(access >= ".$desp->rounds.", 1, 0)" : "'0'";
            $this->db->query("UPDATE disp_".$id_disp." set status = $access,
                qualif='AMD', last_update=now(), llamadas=llamadas+1
                where id='".$row->id_registro."'");
        }
        return count($res);
    }

    // Eliminar COMPLETAMENTE un despachador del sistema
    public function eliminar($id_desp) {
        $this->db->query("DROP TABLE if exists disp_".$id_desp."_qualif");
        $this->db->query("DROP TABLE if exists disp_".$id_desp);
        $this->db->query("DELETE from `disp_cond` where `id_dispatcher` = ?", [$id_desp]);
        $this->db->query("DELETE from `disp_csv` where `id_dispatcher` = ?", [$id_desp]);
        $this->db->query("DELETE from `disp_field` where `id_dispatcher` = ?", [$id_desp]);
        $this->db->query("DELETE from `disp_sent` where `id_dispatcher` = ?", [$id_desp]);
        $this->db->query("DELETE from `disp_user` where `id_dispatcher` = ?", [$id_desp]);
        return $this->db->query("DELETE from `dispatcher` where id = ?", [$id_desp]);
    }

    public function preddata($data) {
        return $this->db->query("UPDATE dispatcher set gateway=?, dialer=?, autodial=?, multi=?,
            maskname=?, masknum=? where id=?",
            [$data['gateway'], $data['dialer'], $data['autodial'], $data['multi']
            , $data['maskname'], $data['masknum'], $data['id_desp']]);
    }

    private function microtime_float() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public function despachador_estatus_agendado_totales($id_desp) {
        $despachador = $id_desp;
        // $despachador = $data['despachador'];
        $tabla = 'disp_'.$despachador;
        $campo = 'since';

        // SI NO EXISTE EL CAMPO QUE LO CREE
        if (!$this->db->field_exists($campo, $tabla)) {
            // Temporal, todos los despachadores deben "nacer" con éste campo (ToDo)
            $this->db->query("ALTER TABLE $tabla ADD `since` datetime DEFAULT NULL");
        }

        if ( $this->db->table_exists($tabla) ) {
            $data['pag'] = 'x';
            $data['prequery'] = "SELECT
                IF((status = 0), 'Abiertas', 'Cerradas') AS estatus,
                COALESCE(SUM(IF((since IS NOT NULL OR since != ''), 1, 0)), 0) AS 'agendada',
                COALESCE(SUM(IF((since IS NULL OR since = ''), 1, 0)), 0) AS 'no_agendada'
                FROM $tabla
                GROUP BY IF((status = 0), 'Abiertas', 'Cerradas')
                ORDER BY status";

            return $this->datos_model->manejadorqueries($data);
        }

        return false;
    }

    /* regresa a null la columna busy de los despachadores */
    public function regresabusy() {
        $desps = $this->db->query("SELECT id from dispatcher")->result();
        $res = false;
        foreach ($desps as $desp) {
            $res = $this->db->query("UPDATE disp_" . $desp->id . " SET last_update = now(), busy=NULL WHERE busy IS NOT NULL");
        }

        return $res;
    }

}
