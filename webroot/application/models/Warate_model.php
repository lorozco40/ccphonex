<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warate_model extends CI_Model
{

    // Guardar encuesta
    public function save(array $data = [])
    {
        if ($data['id'] == 0) { // Nueva encuesta
            $query = $this->db->query("INSERT INTO whatsapp_rate
                (id_wacta, `name`, comment, active, created_by) values (?, ?, ?, 0, ?)",
                [$data['wid'], $data['name'], $data['comment'], $this->udata['id']]);
        } else { // Actualizar encuesta
            $active = (empty($data['active'])) ? 0 : 1;
            if ($active == 1) $this->db->query("UPDATE whatsapp_rate set active = 0
                WHERE id_wacta = ?", [$data['wid']]);
            $query = $this->db->query("UPDATE whatsapp_rate set `name`=?, comment=?, active=?
                WHERE id = ?", [$data['name'], $data['comment'], $active, $data['id']]);
        }

        return ($query) ? "Datos guardados" : $this->db->error();
    }

    // Traer reactivos de encuesta id
    public function getrctv(int $rid = 0)
    {
        $query = $this->db->query("SELECT * FROM whatsapp_rate_rctv WHERE id_wr = ?", [$rid]);

        return $query->result();
    }

    // Actualizar reactivo
    public function saverctv(array $data = [])
    {
        if (!isset($data['id']) || !isset($data['rid'])
        || !isset($data['tipo']) || !isset($data['reactivo'])) {
            return ["error"=>"Datos incompletos"];
        }
        // En caso de que se este desactivando el registro, tambien desactivaremos su visibilidad en reportes
        if( $data['tipo'] == 0 ) $reporte = 0;
        else $reporte = ( isset($data['reporte']) ) ? 1 : 0;

        if (empty($data['id'])) { // Nuevo reactivo
            $query = $this->db->query("INSERT INTO whatsapp_rate_rctv
                (id_wr, tipo, reporte, reactivo) values (?, ?, ?, ?)",
                [$data['rid'], $data['tipo'], $reporte, $data['reactivo']]);
                $this->genRctvTable($data['rid']);
        } else { // Actualizar reactivo
            $query = $this->db->query("UPDATE whatsapp_rate_rctv
                SET tipo = ?, reporte=?, reactivo = ? WHERE id = ? AND id_wr = ?",
                [$data['tipo'], $reporte, $data['reactivo'], $data['id'], $data['rid']]);
        }
        if ($query) return ["msg"=>"Guardado correctamente"];

        return ["error"=>$this->db->error()];
    }

    // Cambiar a private
    private function genRctvTable(int $rid = 0) // Rate ID (encuesta)
    {
        $query = $this->db->query("SELECT * FROM whatsapp_rate WHERE id = ?", [$rid]);
        $enc   = $query->row();
        $query = $this->db->query("SELECT * FROM whatsapp_rate_rctv WHERE id_wr = ?", [$rid]);
        if ($this->db->table_exists('warate_' . $rid)) {
            // Actualizar tabla
            $campos = $this->db->list_fields('warate_' . $rid);
            foreach ($query->result() as $rctv) {
                if (!in_array($rctv->id, $campos)) {
                    if ($rctv->tipo == 1) { // Numérico
                        $this->db->query("ALTER TABLE `" . 'warate_' . $rid . "`
                            ADD `" . $rctv->id . "` tinyint(2) NULL");
                    } elseif ($rctv->tipo == 2) { // Texto
                        $this->db->query("ALTER TABLE `" . 'warate_' . $rid . "`
                            ADD `" . $rctv->id . "` mediumtext NOT NULL DEFAULT ''");
                    } // Tipo cero se ignoran
                }
            }
        } else {
            // Crear tabla
            $sql = "CREATE TABLE warate_" . $rid .
                " (id_wases int(11) NOT NULL PRIMARY KEY, ";
            foreach ($query->result() as $rctv) {
                if ($rctv->tipo == "1") { // Numérico
                    $sql .= "`" . $rctv->id . "` tinyint(2) NULL, ";
                } elseif ($rctv->tipo == "2") { // texto
                    $sql .= "`" . $rctv->id . "` mediumtext NOT NULL, ";
                } // Tipo cero se ignoran
            }
            $sql .= "FOREIGN KEY (`id_wases`) REFERENCES `whatsapp_session` (`id`))
                CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            return $this->db->query($sql);
        }
    }

    public function encuesta(int $wid = 0, int $cid = 0, bool $rep = false, int $uid = null) {
        $ret = ["msg" => "Enviado"];
        $query = $this->db->query("SELECT * FROM whatsapp_rate
            WHERE id_wacta = ? AND active = 1 LIMIT 1", [$wid]);
        $enc = $query->row();
        if (empty($enc)) return(["error" => "Sin encuesta activa"]);
        $query = $this->db->query("SELECT * FROM whatsapp_contact WHERE id = ? AND id_wacta = ?",
            [$cid, $wid]);
        $con = $query->row();
        if (empty($con)) return(["error" => "Contácto desconocido"]);
        $this->load->model("whatsapp_model");
        $ses = $this->whatsapp_model->creases(["cid"=>$cid, "wid"=>$wid, "tipo"=>"Encuesta", "trans"=>$uid]);
        $offset = (empty($ses->paso)) ? 0 : $ses->paso;
        $newoffset = (int)$offset + 1;
        if ($rep) {
            $newoffset = $offset;
            $offset--;
        }
        $query = $this->db->query("SELECT reactivo FROM whatsapp_rate_rctv
            WHERE id_wr = ? LIMIT 1 OFFSET $offset", [$enc->id]);
        $rctv = $query->row();
        if (empty($rctv)) {
            $this->whatsapp_model->terminases(["sid"=>$ses->id, "cid"=>$ses->id_contact,
                "razon"=>"Encuesta sin reactivos"]);
                return(["error"=>"Encuesta sin reactivos"]);
        }
        $nvio = $this->whatsapp_model->sale_texto(["ses"=>$ses, "watext"=>$rctv->reactivo]);
        if (empty($nvio["error"]) && !$rep) {
            $this->db->query("UPDATE whatsapp_session SET paso = ? WHERE id = ?", [$newoffset, $ses->id]);
        } else {
            return($nvio["error"]);
        }

        return($ret);
    }

    public function guardaencuesta(stdClass $ses, string $txt)
    {
        if ($ses->subtipo != "Encuesta") {
            debug_log("Intento de guardado encuesta no encuesta", "Warate_model guardaencuesta");
        }
        $query = $this->db->query("SELECT * FROM whatsapp_rate WHERE id_wacta = ?
            AND `active` = '1'", [$ses->id_wacta]);
        $enc = $query->row();
        if (empty($enc)) {
            $this->load->model("whatsapp_model");
            $this->whatsapp_model->terminases(["sid"=>$ses->id,"cid"=>$ses->id_contact,
                "razon"=>"Error"]);
            debug_log("Encuesta WA desactivada antes de terminar", "Warate_model guardaencuesta");
            exit;
        }
        $offsetant = (int)$ses->paso - 1;
        $query = $this->db->query("SELECT * FROM whatsapp_rate_rctv
            WHERE id_wr = ? LIMIT 1 OFFSET ?", [$enc->id, $offsetant]);
        $paso = $query->row();
        // 1 = Numérico, 2 = Texto
        if ($paso->tipo == 1 && !is_numeric($txt)) {
            $this->encuesta($ses->id_wacta, $ses->id_contact, true, $ses->id_user);
        } else {
            if ($ses->paso == 1) {
                $this->db->query("INSERT INTO warate_" . $enc->id .
                    " (id_wases, `" . $paso->id . "`) values (?, ?)",
                    [$ses->id, $txt]);
            } else {
                $this->db->query("UPDATE warate_" . $enc->id . " SET `" .
                    $paso->id . "` = ? WHERE id_wases = ?", [$txt, $ses->id]);
            }
            $query = $this->db->query("SELECT count(*) regs FROM whatsapp_rate_rctv WHERE id_wr = ?", [$enc->id]);
            $rctvs = $query->row()->regs;
            if ($rctvs == $ses->paso) {
                $this->whatsapp_model->sale_texto(["ses"=>$ses, "watext"=>"Gracias"]);
                $this->whatsapp_model->terminases(["sid"=>$ses->id, "cid"=>$ses->id_contact,
                    "razon"=>"Completa"]);
            } else {
                $this->encuesta($ses->id_wacta, $ses->id_contact, false, $ses->id_user);
            }
        }
    }

    public function select_encuestas_wactas($wid = false, $act = 1) {   
        $ids = ($wid) ? $wid : $this->udata['whatsapp'];
        if (empty($ids)) return false;
        $query = $this->db->query("SELECT wr.id, wr.name 
        FROM whatsapp_rate wr
        LEFT JOIN whatsapp_cuentas wc ON wr.id_wacta = wc.id
        WHERE wc.id in ($ids) 
        AND wc.active = '$act'");

        return $query->result();
    }

    public function encuesta_whatsapp_detalle($data) {
        $id_wr  = (int) $this->input->post('encuesta');
        $cuenta = (int) $this->input->post('cuenta');
        if( $id_wr > 0 ) {
            //obtenemos los nombres de los campos de la encuesta
            $sql = "SELECT id, id_wr, tipo, reactivo 
            FROM whatsapp_rate_rctv 
            WHERE id_wr = $id_wr";
            $preguntas = $this->db->query($sql)->result();
            //Validamos que exista la tabla de encuesta
            if( $this->db->table_exists('warate_'.$id_wr) ) {
                //obtenemos los campos de la encuesta
                $campos = $this->db->list_fields('warate_'.$id_wr);
                //Hacemos el merge
                $sql_select_array = [];
                foreach( $campos AS $campo) {
                    foreach($preguntas AS $pregunta) {
                        if( (int)$campo == (int)$pregunta->id && $pregunta->tipo != '0' ) {
                            $alias = $this->eliminarEmoticones($pregunta->reactivo);
                            $sql_select_array[] =  "COALESCE(p.`$campo`, '') AS `$alias`";
                        }
                    }
                }
                $sql_select_string = '';
                //Comprobamos si el array tiene registros y creamos el sql del select
                if( count($sql_select_array) > 0 )
                    $sql_select_string = ',';
                $sql_select_string .= implode(',', $sql_select_array);
                $sql_where_contac = (empty($data['wacto'])) ? "" : "AND wc.id = '$data[wacto]'";
                $data['prequery'] = "SELECT p.id_wases,
                date_format(ws.datetime_end, '$this->dfor') 'Fecha cierre',
                time(ws.datetime_end) 'Hora cierre',
                ifnull(wc.name, '') 'Contacto',
                ifnull(wc.account, '') 'Número',
                if(ws.id_user = '9999', 'Bot', 'Agente') AS 'Tipo'
                $sql_select_string
                FROM warate_$id_wr AS p
                LEFT JOIN whatsapp_session ws ON ws.id = p.id_wases
                LEFT JOIN whatsapp_contact wc ON wc.id = ws.id_contact
                WHERE DATE(ws.datetime_end) BETWEEN '$data[min]' AND '$data[max]'
                AND ws.id_wacta = $cuenta
                $sql_where_contac
                ";
                $data = $this->datos_model->manejadorqueries($data);
            }
        }

        return $data;
    }

    public function encuesta_indicador_data() {
        $data = [
            'n'         => 0,
            'msg'       => '',
            'success'   => true,
        ];
        $id_wr  = (int) $this->input->post('id_encuesta');
        $max    = $this->input->post('max');
        $min    = $this->input->post('min');
        $min    = (empty($min)) ? date('Y-m-d') : convierte($min, $this->idfor);
        $max    = (empty($max)) ? date('Y-m-d') : convierte($max, $this->idfor);
        $sql_where = "WHERE DATE(ws.datetime_end) BETWEEN '$min' AND '$max'";
        // Valiamos que el id de la encuesta sea valido
        if( $id_wr <= 0 ) {
            $data['success'] = false;
            $data['msg'] = 'La encuesta seleccionada no tiene un valor valido';
        }
        // Obtenemos los nombres de los campos de la encuesta
        if( $data['success'] ) {
            $sql = "SELECT id, id_wr, tipo, reactivo, reporte 
            FROM whatsapp_rate_rctv 
            WHERE id_wr = $id_wr";
            $preguntas = $this->db->query($sql)->result();
        }
        // Validamos que exista la tabla de encuesta
        if( $data['success'] ) {
            if( $this->db->table_exists('warate_'.$id_wr) ) {
                // Obtenemos los campos de la encuesta
                $campos = $this->db->list_fields('warate_'.$id_wr);
                // Hacemos el merge
                $sql_select_array = [];
                $array_campos = [];
                foreach( $campos AS $campo) {
                    foreach($preguntas AS $pregunta) {
                        if( (int)$campo == (int)$pregunta->id && $pregunta->tipo != 0 && $pregunta->reporte == 1 ) {
                            $alias = ($pregunta->reactivo);
                            $sql_select_array[] =  "COALESCE(p.`$campo`, '') AS `$alias`";
                            $array_campos[] = ['id' => $campo ,  'name' => $alias];
                        }
                    }
                }
                if( count( $array_campos ) == 0 ) {
                    $data['success'] = false;
                    $data['msg'] = 'No hay preguntas disponibles para este reporte';
                }
            } else {
                $data['success'] = false;
                $data['msg'] = "No hay registros para mostrar";
            }
        }
        // Obtenemos la cantidad de registros
        if( $data['success'] ) {
            $n = $this->db->query("SELECT * 
                FROM warate_$id_wr wr
                LEFT JOIN whatsapp_session ws ON ws.id = wr.id_wases
                $sql_where
            ")->num_rows();
            $data['n'] = $n;
            if( $n == 0 ) {
                $data['success'] = false;
                $data['msg'] = "No hay registros para mostrar";
            }
        }
        // Crearemos el array de resultados para las tablas
        if( $data['success'] ) {
            $q = [];
            // Recorremos las preguntas
            foreach( $array_campos as $campo ) {
                $select_campo = 'wr.'.$campo['id'];
                $respuestas = $this->db->query("SELECT count(*) AS n, COALESCE($select_campo, '') AS resp
                    FROM warate_$id_wr wr
                    LEFT JOIN whatsapp_session ws ON ws.id = wr.id_wases
                    $sql_where
                    GROUP BY resp
                    ORDER BY resp ASC
                ")->result();
                $ans = [];
                $ans[] = ['Respuesta','#'];
                $ans_tot = 0;
                foreach( $respuestas as $row ) {
                    $head_value = ($row->resp == '') ? 'Deserción' : $row->resp;
                    $grap[] = [$head_value, (int)$row->n];
                    $ans[] = [$head_value, (int)$row->n];
                    $ans_tot += $row->n;
                }
                $q[] = [
                    'id' => $campo['id'],
                    'pregunta' => $campo['name'],
                    'ans_tot' => $ans_tot,
                    'ans' => $ans 
                ];
            }
            $data['q'] = $q;
        }

        return $data;
    }

    private function eliminarEmoticones($cadena) {
        // Expresion regular para emoticones
        $patronEmoticon = '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{2300}-\x{23FF}\x{2B50}]/u';
        $cadenaLimpia = preg_replace($patronEmoticon, '?', $cadena);
    
        return $cadenaLimpia;
    }
}
