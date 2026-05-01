<?php
error_reporting( E_ALL );
ini_set("display_errors", 1);
defined('BASEPATH') OR exit('No direct script access allowed');

class Kdm extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        if ($this->udata['id']>8) { show_404(); }
    }

    public function index() {
        show_404();
    }

    public function grabadesdearchivos() {
        /* Busca las grabaciones en la carpeta astersik spool monitor por fecha
           y las guarda despedazando el nombre para buscar el linkedid en call_entry */
        $a = $this->input->get('a');
        $m = $this->input->get('m');
        $d = $this->input->get('d');
        if ($a && $m && $d) {
            $directorio = "/var/spool/asterisk/monitor/$a/$m/$d";
            $files      = scandir($directorio);
            echo "<pre>";
            foreach ($files as $key => $value) {
                $did = explode("-", $grabacion)[1];
                if($value != "." && $value != "..") {
                    $partes = explode('-', $value);
                    $linkedid = substr($partes[5],0,-4);
                    $respu = $this->db->query("UPDATE call_entry set grabacion='$value', did='$did'
                        where uniqueid = '$linkedid' and grabacion=''");
                    var_dump($linkedid, $respu);
                }
                var_dump($value);
            }
            echo "</pre>";
        } else {
            echo "Y la fecha señor olvidadizo?";
        }
    }

    public function ejemod() {
        if (!$this->input->get('m')) die("Sin modelo (m, f, p)");
        if (!$this->input->get('f')) die("Sin función (m, f, p)");
        $m = $this->input->get('m')."_model"; // modelo
        $f = $this->input->get('f');          // función
        $p = $this->input->get('p') ?: "NO"; // parámetro(s)
        $this->load->model($m);
        if (preg_match('/^20[0-9]{2}$/', $p) == 1) {
            // Ejecuta la función del modelo por cada día de cada mes del año especificado
            for ($i=1; $i < 13; $i++) {
                for ($j=1; $j < 32; $j++) {
                    $res = $this->$m->$f($p."-".$i."-".$j);
                    var_dump($res);
                    echo "<br/>\n";
                }
            }
        } elseif (preg_match('/^20[0-9]{2}-[0-9]{2}$/', $p) == 1) {
            // Ejecuta la función del modelo por cada día del mes y año especificados
            for ($j=1; $j < 32; $j++) {
                $res = $this->$m->$f($p."-".$j);
                var_dump($res);
                echo "<br/>\n";
            }
        } else {
            // Ejecuta la función del modelo una sola vez
            $indi = false;
            if(stripos($p, ":") !== FALSE) $indi = true;
            if(stripos($p, ",") !== FALSE) {
                $p = explode(',', $p);
                if ($indi) {
                    $res = [];
                    foreach ($p as $value) {
                        $tmp = explode(':', $value);
                        $res[$tmp[0]] = $tmp[1];
                    }
                    $p = $res;
                }
            }
            if ($p == "NO") {
                $res = $this->$m->$f();
            } else {
                $res = $this->$m->$f($p);
            }
            var_dump($res);
            echo "<br/>\n";
        }

    }

    public function cambiaUserId($de, $a) {
        $start_time = microtime(true);
        $this->db->query("INSERT INTO user (id, user, name, pass) values
            (991991, 'nada@tmp.co', 'tmpPase', 'tmpPase')");
        $this->pasaUserData($de, 991991);
        $this->db->query("UPDATE user set id = ? where id = ?", [$a, $de]);
        $this->pasaUserData(991991, $a);
        $this->db->query("DELETE FROM user WHERE id = 991991");
        $max = $this->db->query("SELECT max(id) as maxid FROM user")->row()->maxid;
        $this->db->query("ALTER TABLE user AUTO_INCREMENT = ?", [$max+1]);
        $end_time = microtime(true);
        $duration = $end_time - $start_time;
        echo "Duración en de proceso en segundos: " . $duration . "<br/>";

        dd($this->db->error());
    }

    private function pasaUserData($de, $a) {
        // Tablas id_user
        $tablas = ['break_entry','call_entry','email_entry','videocall_entry','sms_entry',
            'error_log','schedule','hc_chats','user_data','rep_sesion','videocall_chans',
            'whatsapp_entry','whatsapp_session','tarifas','campaign_licenses','client','user_status'
        ];
        // Formularios
        $query = $this->db->query("SELECT id FROM form WHERE crm = 1");
        foreach ($query->result() as $form) {
            $tablas[] = "formd_".$form->id."_crm";
        }
        foreach ($tablas as $tabla) {
            $this->db->query("UPDATE $tabla set id_user = ? where id_user = ?", [$a, $de]);
        }
        // Despachadores
        $tablas = [];
        $query = $this->db->query("SELECT id FROM dispatcher");
        foreach ($query->result() as $form) {
            if ($this->db->table_exists("disp_".$form->id."_qualif")) {
                $tablas[] = "disp_".$form->id."_qualif";
            }
        }
        foreach ($tablas as $tabla) {
            $this->db->query("UPDATE $tabla set saved_by = ? where saved_by = ?", [$a, $de]);
        }
        // Tablas created_by
        $tablas = ['crm_plant_pdf', 'crm_light', 'dispatcher', 'form', 'pit_catalog',
            'campaign', 'whatsapp_bot', 'whatsapp_bot_script','email_account','extapi'];
        foreach ($tablas as $tabla) {
            $this->db->query("UPDATE $tabla set created_by = ? where created_by = ?", [$a, $de]);
        }
        $this->db->query("UPDATE user_log set user_id = ? where user_id = ?", [$a, $de]);
        $this->db->query("UPDATE whatsapp_bot_scr_steps set lastusr = ? where lastusr = ?", [$a, $de]);
        $this->db->query("UPDATE chatinterno_entry set id_usuario_emite = ? where id_usuario_emite = ?", [$a, $de]);
        $this->db->query("UPDATE chatinterno_entry set id_usuario_recibe = ? where id_usuario_recibe = ?", [$a, $de]);
        $this->db->query("UPDATE whatsapp_contact set last_asigned_to = ? where last_asigned_to = ?", [$a, $de]);
        $this->db->query("UPDATE whatsapp_cuentas set alta_quien = ? where alta_quien = ?", [$a, $de]);
    }

    public function pru($val) {
        $this->load->library("whatsappfun");
        $this->load->model("wabot_model");
        $query = $this->db->query("SELECT * FROM whatsapp_session WHERE id = ?", [$val]);
        $ses = $query->row();
        $this->wabot_model->respondeBot($ses, 'kinon');
    }

    //Recorre todos los despachadores y actualiza la estructura de los mismos unicamente agregando los campos que posiblemente falten
    public function fix_dispatcher_structure() {
        // Estructura de tablas disp_##: id, x_campo, x_campo2, qualif, llamadas, invalid, access, busy, status, last_update, added, since
        // Obtenemos todas las tablas del despachador
        $rows = $this->db->query("SELECT * FROM dispatcher ORDER BY id")->result();
        $i = 0;
        echo "Actualización de tablas despachadores: <br/>";
        foreach ($rows as $row) {
            $i++;
            $agregados = [];
            // Revisamos que la tabla exista
            $table = 'disp_'.$row->id;
            $exist_table = $this->db->table_exists($table);
            if( $exist_table ) {
                $busy = $this->db->field_exists('busy', $table);
                if( !$busy ) {
                    $sql = "ALTER TABLE $table ADD `busy` DATETIME DEFAULT NULL AFTER access";
                    $agregados[] = 'busy';
                    $this->db->query($sql);
                }
                $since = $this->db->field_exists('since', $table);
                if( !$since ) {
                    $sql = "ALTER TABLE $table ADD `since` DATETIME DEFAULT NULL AFTER added";
                    $agregados[] = 'since';
                    $this->db->query($sql);
                }
                if( !empty($agregados) )
                    echo '['.$table.'] '.$row->name.': '.implode(', ', $agregados).'<br/>';
            }
            if ($this->db->table_exists($table."_qualif")) {
                if (!$this->db->field_exists('uniqueid', $table."_qualif")) {
                    $sql = "ALTER TABLE `".$table."_qualif` ADD `uniqueid` VARCHAR(50) NOT NULL AFTER id_disp_data";
                    $this->db->query($sql);
                    echo "[$table"."_qualif] uniqueid<br/>";
                }
                if (!$this->db->field_exists('linkedid', $table."_qualif")) {
                    $sql = "ALTER TABLE `".$table."_qualif` ADD `linkedid` VARCHAR(50) NOT NULL AFTER uniqueid";
                    $this->db->query($sql);
                    echo "[$table"."_qualif] linkedid<br/>";
                }
            }
            if (file_exists(APPPATH.'views/despachador/desp_form_'.$row->id.'.php')) {
                unlink(APPPATH.'views/despachador/desp_form_'.$row->id.'.php');
            }
        }
        echo "Todas las tablas de despachadores, estan actualizadas!";
    }

    public function fixes() {
        // Lo que sea necesario para la actualización
        echo "Iniciando ... <br />";
        $query = $this->db->query("SELECT id from dispatcher");
        foreach ($query->result() as $row) {
            if ($this->db->table_exists("disp_".$row->id."_qualif")) {
                $this->db->query("alter table disp_".$row->id."_qualif add column if not exists linkedid varchar(50) after id_disp_data");
                $this->db->query("alter table disp_".$row->id."_qualif add column if not exists uniqueid varchar(50) after id_disp_data");
                echo "Echa tabla " . $row->id . "</br>";
            }
        }
        echo "<h3>Terminado!</h3>";
    }

}
