<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Calidad_model extends CI_Model
{

    public function get_one($id) {
        $query = $this->db->query("SELECT * from quality where id=$id");
        return $query->row();
    }

    public function get_list($cams, $ini) {
        $query = $this->db->query("SELECT * from quality where id_campaign in ($cams) order by name limit ?, ".REGS_POR_PAG,
        array($ini));
        return $query->result();
    }

    public function create($data) {
        $activo = (isset($data['activo'])) ? 1 : 0;
        if( $activo == 1 ) {
            $this->db->query("UPDATE quality SET active=0 WHERE id_campaign=? AND `type`=?", array($data['campaign'], $data['type']));
        }
        return $this->db->query("INSERT into quality (id_campaign, name, `type`, active, created_by, created_when)
            values (?,?,?,?,?,now())", array($data['campaign'], $data['name'], $data['type'], $activo, $this->session->userdata('uid')));
    }

    public function update() {
        $success = true;
        $message = "La cédula se actualizo correctamente.";
        $data = $this->input->post();
        $active = (isset($data['active'])) ? 1 : 0;
        $this->db->trans_begin();
        if( $active == 1 ) { // Reiniciamos todas las cedulas del tipo en modificacion a desactivado
            $this->db->query("UPDATE quality SET active=0 WHERE id_campaign=? AND `type`=?", array($data['campaign'], $data['type']));
        }
        // Actualizamos la cedula
        $this->db->query("UPDATE quality 
        SET id_campaign=?, name=?, type=?, active=?
        WHERE id=?",
        array($data['campaign'], $data['name'], $data['type'], $active, $data['id']));
        if ($this->db->trans_status() === FALSE) {
            $success = false;
            $message = "Error: No se pudo actualizar la cédula.";
        }
        if( $success ) {
            $correct = $this->validate_is_correct($data['id']);
            if( !$correct ) {
                $success = false;
                $message = "Error: la cédula Activa debe cumplir con el 100% de la ponderación.";
            }
        }
        if( $success ) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    public function traercampos($cola, $type='llamadas', $id_campaign = false) {
        if( $id_campaign === false ) {
            // Buscar por el nombre de la campaña, pasado por la variable cola
            $query = $this->db->query("SELECT * from campaign where name = '$cola' and active=1 limit 1");
            $campana = $query->row();
        } else {
            //Se paso la campaña por ID
            $campana = (object)['id' => $id_campaign];
        }
        $query = $this->db->query("SELECT q.id FROM quality AS q
            LEFT JOIN campaign AS c ON c.id = q.id_campaign
            WHERE q.id_campaign=? and q.active=1 AND q.`type`=? limit 1", array($campana->id, $type));
        if ($quality = $query->row()) {
            $query = $this->db->query("SELECT * from quality_fields where id_quality=? order by num_order", array($quality->id));
            return $query->result();
        }

        return false;
    }

    public function info_cedula($type='llamadas', $id_campaign = 0 ) {
        $row = $this->db->query("SELECT `name`
        FROM `quality`
        WHERE id_campaign=?
        AND `type`=?
        AND active = 1
        LIMIT 1", [$id_campaign, $type])->row();

        return $row;
    }

    function traercedulawhats($cam) {
        $query = $this->db->query("SELECT * from quality where id_campaign = ? and active=1 and type='whatsapp'
            order by id DESC limit 1", [$cam]);
        if ($query->num_rows() > 0) {
            $quality = $query->row();
            $query = $this->db->query("SELECT * from quality_fields where id_quality=? order by num_order", [$quality->id]);
            return [
                'fields' => $query->result(),
                'quality' => $quality,
            ];
        }

        return ["error"=>"No hay cedula de calidad activa para esta campaña"];
    }

    public function get_fields($id_question, $ini, $fin){
        $query = $this->db->query("SELECT * from quality_fields where id_quality='$id_question' order by `num_order` limit ?, ?",
        array($ini, $fin));
        return $query->result();
    }

    // Crea siempre que no sea mayor a 100
    public function createc() {
        $success = true;
        $message = "Pregunta agregada correctamente.";
        $this->db->trans_begin();
        $data = $this->input->post();
        //Se insertan los datos
        $this->db->query("INSERT INTO quality_fields (id_quality, question, weight, num_order)
        values (?,?,?,?)", array($data['id_quality'], $data['question'], $data['weight'], $data['num_order']));
        // -----------------------------------------------------------
        if ($this->db->trans_status() === FALSE) {
            $success = false;
            $message = "Error: No se pudo actualizar la cédula.";
        }
        if( $success ) {
            $correct = $this->validate_is_correct($data['id_quality']);
            if( !$correct ) {
                $success = false;
                $message = "Error: la cédula Activa debe cumplir con el 100% de la ponderación.";
            }
        }
        if( $success ) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    // Actualiza siempre que no sea mayor a 100
    public function updatec() {
        $success = true;
        $message = "Pregunta actualizada correctamente.";
        $this->db->trans_begin();
        $data = $this->input->post();
        //Se actualizan los datos
        $this->db->query("UPDATE quality_fields set question=?, weight=?, num_order=? where id=?",
        array($data['question'], $data['weight'], $data['num_order'], $data['id']));
        // ----------------------------------------------------------
        if ($this->db->trans_status() === FALSE) {
            $success = false;
            $message = "Error: No se pudo actualizar la cédula.";
        }
        if( $success ) {
            $correct = $this->validate_is_correct($data['id_quality']);
            if( !$correct ) {
                $success = false;
                $message = "Error: la cédula Activa debe cumplir con el 100% de la ponderación.";
            }
        }
        if( $success ) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    public function delete_field($id, $id_quality) {
        $success = true;
        $message = "Pregunta eliminada correctamente.";
        $this->db->trans_begin();
        $this->db->query("DELETE FROM quality_fields WHERE id = ?", [$id]);
        // Validamos si la consulta se ejecuto correctamente
        if ($this->db->trans_status() === FALSE) {
            $success = false;
            $message = "Error: No se pudo eliminar la pregunta.";
        }
        if( $success ) {
            $correct = $this->validate_is_correct($id_quality);
            if( !$correct ) {
                $success = false;
                $message = "Error: la cédula Activa debe cumplir con el 100% de la ponderación.";
            }
        }
        if( $success ) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    //Valida que una cedula de calidad activa este con los datos correctos si esta esta activa
    private function validate_is_correct($id) {
        $success = true;
        $query = $this->db->query('SELECT q.*, SUM(qf.weight) AS weight
        FROM quality q
        LEFT JOIN quality_fields qf ON qf.id_quality = q.id
        WHERE q.id = ?
        GROUP BY q.id', [$id]);
        $quality = $query->row();
        if( !$quality ) {
            $success = false;
        }
        // Validamos que la cedula en caso de estar activa, cumpla con el 100% de la ponderacion
        if( $success ) {
            if( $quality->active == '1' && $quality->weight != 100 ) {
                $success = false;
            }
        }

        return $success;
    }

    public function traevaluacion() {
        $query = $this->db->query("SELECT id_campaign, name FROM quality  ");
        return $query->result();
    }
    //Reporte de calidad en excel
    public function excelrepo_cal($data) {
        if ($data['tipeval']==0) return false;
        $fini = convierte($data['min'], $this->idfor)." 00:00:00";
        $ffin = convierte($data['max'], $this->idfor)." 23:59:59";
        $tipo = ($data['tipo']!="0") ? " AND ce.type = '$data[tipo]'" : "";
        $tipeval = "AND qf.id_quality = '".$data['tipeval']."'";
        $query = $this->db->query("SELECT id, question FROM quality_fields WHERE id_quality=".$data['tipeval']);
        $tmp = $query->result();
        $lineas = '';
        foreach ($tmp as $key => $row) {
            if ($row->question=='Comentario') {
                $lineas .= "GROUP_CONCAT(IF(qv.id_quality_fields=$row->id,qv.value,null)) AS '$row->question', ";
            } else {
                $lineas .= "SUM(IF(qv.id_quality_fields=$row->id,qv.value,0)) AS '$row->question', ";
            }
        }

        $query = $this->db->query("SELECT date_format(ce.datetime_received, '$this->dtfor') AS Fecha, CONCAT(u.last,' ',u.name) AS Agente,
            ce.queue AS Campaña, ce.cid_num AS Número, SEC_TO_TIME(ce.duration) AS Duración, ce.uniqueid AS Callerid,
            ce.type AS Tipollamada,
            $lineas
            SUM(IF(qf.weight,qf.weight,0)) AS Efectividad,
            100 - SUM(IF(qv.value,qv.value,0)) AS Faltante,
            CONCAT(u2.last,' ',u2.name) AS Auditor,
            qu.datetime_calif AS 'Fecha auditoria'
            FROM call_entry ce
            LEFT JOIN user u ON u.id=ce.id_user
            LEFT JOIN quality_values qv ON qv.id_call_entry = ce.id
            LEFT JOIN quality_fields qf ON qf.id = qv.id_quality_fields
            LEFT JOIN quality_user qu ON qu.id_call_entry = ce.id
            LEFT JOIN user u2 ON u2.id=qu.id_user
            WHERE ce.datetime_received BETWEEN '$fini' AND '$ffin' $tipeval $tipo AND u.id IN (" . $data['agente'] . ")
            GROUP BY ce.id ORDER by Agente, Fecha");

        $res["registros"] = $query->result_array();
        $res["campos"] = $query->list_fields();
        return $res;
    }

    public function evalcalidad($post) {
        if (empty($post['tipeval'])) return array('cuenta'=>0);
        $min = convierte($post['min'], $this->idfor);
        $max = convierte($post['max'], $this->idfor);
        $tipo = ($post['tipo']!="0") ? " AND ce.type = '$post[tipo]'" : "";
        $query = $this->db->query("SELECT id, question FROM quality_fields WHERE id_quality=".$post['tipeval']);
        if ($query->num_rows() == 0) return false;
        $tmp = $query->result();
        $lineas = $lineastot = '';
        foreach ($tmp as $key => $row) {
            if ($row->question=='Comentario') {
                $lineas .= "GROUP_CONCAT(IF(qv.id_quality_fields=$row->id,qv.value,null)) AS '$row->question', ";
            } else {
                $lineas .= "SUM(IF(qv.id_quality_fields=$row->id,qv.value,0)) AS '$row->question', ";
                $lineastot .= "SUM(IF(qv.id_quality_fields=$row->id,1,0)) AS '$row->question', ";
            }
        }
        $lineas = rtrim($lineas, ", ");
        $lineastot = rtrim($lineastot, ", ");
        $prequery = "SELECT date_format(ce.datetime_received, '$this->dtfor') AS Fecha, CONCAT(u.name,' ',u.last) AS Agente,
            ce.queue AS Campaña, ce.cid_num AS Número, SEC_TO_TIME(ce.duration) AS Duración, ce.uniqueid AS Callerid,
            ce.type AS 'Tipo llamada',
            SUM(IF(qv.value,qv.value,0)) AS 'Efectividad',
            100 - SUM(IF(qv.value,qv.value,0)) AS Faltante,
            $lineas,
            CONCAT(u2.name,' ',u2.last) Auditor,
            qu.datetime_calif 'Fecha auditoria'
            FROM call_entry ce
            LEFT JOIN user u ON u.id=ce.id_user
            LEFT JOIN quality_values qv ON qv.id_call_entry = ce.id
            LEFT JOIN quality_fields qf ON qf.id = qv.id_quality_fields
            LEFT JOIN quality_user qu ON qu.id_call_entry = ce.id
            LEFT JOIN user u2 ON u2.id=qu.id_user
            WHERE date(ce.datetime_received) BETWEEN '$min' AND '$max'
            AND u.id IN (".$post['agente'].") AND qf.id_quality = '".$post['tipeval']."' ".$tipo;
        $query = $this->db->query($prequery. " GROUP BY ce.id ORDER by Agente, Fecha");
        $data["cuenta"] = $query->num_rows();
        $query = $this->db->query($prequery. " GROUP BY ce.id ORDER by Agente, Fecha");
        $data["campos"] = $query->field_data();
        $data["todos"] = $query->result();
        $query = $this->db->query($prequery. " GROUP BY ce.id ORDER by Agente, Fecha limit ".$post['page'].", ".$post['lim']."");
        $data["data"] = $query->result();
        $query = $this->db->query("SELECT
            SUM(IF(qv.value,qv.value,0)) AS Efectividad,
            SUM(IF(qv.value,qv.value,0)) as Faltante,
            $lineastot
            FROM call_entry ce
            LEFT JOIN user u ON u.id=ce.id_user
            LEFT JOIN quality_values qv ON qv.id_call_entry = ce.id
            LEFT JOIN quality_fields qf ON qf.id = qv.id_quality_fields
            WHERE date(ce.datetime_received) BETWEEN '$min' AND '$max'
            AND u.id IN (".$post['agente'].") AND qf.id_quality = '".$post['tipeval']."' ".$tipo);
        $data["campostot"] = $query->field_data();
        $data["tot"] = $query->result();
        $indice = 0;
        $last = "";
        $cuenta = 1;
        $sumaE = 0;
        $sumaF = 0;
        $data["graf"][$indice] = array('Genre', 'Efectividad', 'Faltante', array('role'=>'annotation'));
        foreach ($data["todos"] as $key => $row) {
            if ($row->Agente != $last) {
                $indice += 1;
                $cuenta = 1;
                $last = $row->Agente;
                $sumaE = (int)$row->Efectividad;
                $sumaF = (int)$row->Faltante;
                $data["graf"][$indice] = array($row->Agente, $sumaE, $sumaF, ''.$sumaE.'% / '.$sumaF.'%');
            } else {
                $cuenta += 1;
                $sumaE += $row->Efectividad;
                $sumaF += $row->Faltante;
                $data["graf"][$indice][1] = round($sumaE/$cuenta, 2);
                $data["graf"][$indice][2] = round($sumaF/$cuenta, 2);
                $data["graf"][$indice][3] = ''.$data["graf"][$indice][1].'% / '.$data["graf"][$indice][2].'%';
            }
        }

        return $data;
    }

    public function excelrepo_cal_whatsapp($data) {
        if ($data['tipeval']==0) return false;
        $fini = convierte($data['min'], $this->idfor)." 00:00:00";
        $ffin = convierte($data['max'], $this->idfor)." 23:59:59";
        $tipo = ($data['tipo']!="0") ? " AND ws.type = '$data[tipo]'" : "";
        $tipeval = "AND qf.id_quality = '".$data['tipeval']."'";
        $query = $this->db->query("SELECT id, question FROM quality_fields WHERE id_quality=".$data['tipeval']);
        $tmp = $query->result();
        $lineas = '';
        foreach ($tmp as $key => $row) {
            if ($row->question=='Comentario') {
                $lineas .= "GROUP_CONCAT(IF(qv.id_quality_fields=$row->id,qv.value,null)) AS '$row->question', ";
            } else {
                $lineas .= "SUM(IF(qv.id_quality_fields=$row->id,qv.value,0)) AS '$row->question', ";
            }
        }

        $query = $this->db->query("SELECT date_format(ws.datetime_received, '$this->dtfor') AS Fecha, CONCAT(u.name,' ',u.last) AS Agente,
            c.name AS Campaña, wco.account AS 'Teléfono', SEC_TO_TIME(ws.duration) AS Duración,
            ws.type AS 'Tipo conversación',
            ws.type AS 'Tipo llamada',
            SUM(IF(qv.value,qv.value,0)) AS 'Efectividad',
            100 - SUM(IF(qv.value,qv.value,0)) AS Faltante,
            $lineas
            CONCAT(u2.name,' ',u2.last) Auditor,
            qu.datetime_calif 'Fecha auditoria'
            FROM whatsapp_session ws
            INNER JOIN whatsapp_cuentas wc ON wc.id = ws.id_wacta
            INNER JOIN campaign c ON c.id = wc.id_campaign
            LEFT JOIN whatsapp_contact wco ON wco.id = ws.id_contact
            LEFT JOIN user u ON u.id=ws.id_user
            LEFT JOIN whatsapp_quality_values qv ON qv.id_session = ws.id
            LEFT JOIN quality_fields qf ON qf.id = qv.id_quality_fields
            LEFT JOIN whatsapp_quality_user qu ON qu.id_session = ws.id
            LEFT JOIN user u2 ON u2.id=qu.id_user
            WHERE ws.datetime_received BETWEEN '$fini' AND '$ffin'
            AND u.id IN (" . $data['agente'] . ")
            $tipeval $tipo 
            GROUP BY ws.id
            ORDER by Agente, Fecha");
        $res["registros"] = $query->result_array();
        $res["campos"] = $query->list_fields();
        return $res;
    }

    public function evalcalidad_whatsapp($post) {
        if (empty($post['tipeval'])) return array('cuenta'=>0);
        $min = convierte($post['min'], $this->idfor);
        $max = convierte($post['max'], $this->idfor);
        $tipo = ($post['tipo']!="0") ? " AND ws.type = '$post[tipo]'" : "";
        $query = $this->db->query("SELECT id, question FROM quality_fields WHERE id_quality=".$post['tipeval']);
        if ($query->num_rows() == 0) return false;
        $tmp = $query->result();
        $lineas = $lineastot = '';
        foreach ($tmp as $key => $row) {
            if ($row->question=='Comentario') {
                $lineas .= "GROUP_CONCAT(IF(qv.id_quality_fields=$row->id,qv.value,null)) AS '$row->question', ";
            } else {
                $lineas .= "SUM(IF(qv.id_quality_fields=$row->id,qv.value,0)) AS '$row->question', ";
                $lineastot .= "SUM(IF(qv.id_quality_fields=$row->id,1,0)) AS '$row->question', ";
            }
        }
        $lineas = rtrim($lineas, ", ");
        $lineastot = rtrim($lineastot, ", ");
        $prequery = "SELECT date_format(ws.datetime_received, '$this->dtfor') AS Fecha, CONCAT(u.name,' ',u.last) AS Agente,
            c.name AS Campaña, wco.account AS 'Teléfono', SEC_TO_TIME(ws.duration) AS Duración,
            ws.type AS 'Tipo conversación',
            SUM(IF(qv.value,qv.value,0)) AS 'Efectividad',
            100 - SUM(IF(qv.value,qv.value,0)) AS Faltante,
            $lineas,
            CONCAT(u2.name,' ',u2.last) Auditor,
            qu.datetime_calif 'Fecha auditoria'
            FROM whatsapp_session ws
            INNER JOIN whatsapp_cuentas wc ON wc.id = ws.id_wacta
            INNER JOIN campaign c ON c.id = wc.id_campaign
            LEFT JOIN whatsapp_contact wco ON wco.id = ws.id_contact
            LEFT JOIN user u ON u.id=ws.id_user
            LEFT JOIN whatsapp_quality_values qv ON qv.id_session = ws.id
            LEFT JOIN quality_fields qf ON qf.id = qv.id_quality_fields
            LEFT JOIN whatsapp_quality_user qu ON qu.id_session = ws.id 
            LEFT JOIN user u2 ON u2.id=qu.id_user
            WHERE date(ws.datetime_received) BETWEEN '$min' AND '$max'
            AND u.id IN (".$post['agente'].") AND qf.id_quality = '".$post['tipeval']."' ".$tipo;
        $query = $this->db->query($prequery. " GROUP BY ws.id ORDER by Agente, Fecha");
        $data["cuenta"] = $query->num_rows();
        $query = $this->db->query($prequery. " GROUP BY ws.id ORDER by Agente, Fecha");
        $data["campos"] = $query->field_data();
        $data["todos"] = $query->result();
        $query = $this->db->query($prequery. " GROUP BY ws.id ORDER by Agente, Fecha limit ".$post['page'].", ".$post['lim']."");
        $data["data"] = $query->result();
        $query = $this->db->query("SELECT
            SUM(IF(qv.value,qv.value,0)) AS Efectividad,
            SUM(IF(qv.value,qv.value,0)) as Faltante,
            $lineastot
            FROM whatsapp_session ws
            LEFT JOIN user u ON u.id=ws.id_user
            LEFT JOIN whatsapp_quality_values qv ON qv.id_session = ws.id
            LEFT JOIN quality_fields qf ON qf.id = qv.id_quality_fields
            WHERE date(ws.datetime_received) BETWEEN '$min' AND '$max'
            AND u.id IN (".$post['agente'].") AND qf.id_quality = '".$post['tipeval']."' ".$tipo);
        $data["campostot"] = $query->field_data();
        $data["tot"] = $query->result();
        $indice = 0;
        $last = "";
        $cuenta = 1;
        $sumaE = 0;
        $sumaF = 0;
        $data["graf"][$indice] = array('Genre', 'Efectividad', 'Faltante', array('role'=>'annotation'));
        foreach ($data["todos"] as $key => $row) {
            if ($row->Agente != $last) {
                $indice += 1;
                $cuenta = 1;
                $last = $row->Agente;
                $sumaE = (int)$row->Efectividad;
                $sumaF = (int)$row->Faltante;
                $data["graf"][$indice] = array($row->Agente, $sumaE, $sumaF, ''.$sumaE.'% / '.$sumaF.'%');
            } else {
                $cuenta += 1;
                $sumaE += $row->Efectividad;
                $sumaF += $row->Faltante;
                $data["graf"][$indice][1] = round($sumaE/$cuenta, 2);
                $data["graf"][$indice][2] = round($sumaF/$cuenta, 2);
                $data["graf"][$indice][3] = ''.$data["graf"][$indice][1].'% / '.$data["graf"][$indice][2].'%';
            }
        }

        return $data;
    }

    public function traertipeval($ids, $type="llamadas") {
        $query = $this->db->query("SELECT id, name from quality where active=1 AND id_campaign IN ($ids) AND `type`='$type'");
        return $query->result();
    }

    public function guardareval($data) {
        $uid = $this->session->userdata("uid");
        $values = '';
        $pintar = array();
        $tipo_reporte = '';
        $id_eval = $data['id_eval'];
        // Recorremos los datos para insertarlos
        foreach ($data as $key => $value) {
            if ($key != "id_eval" && $key != "evaltotal" && $key != "redir") {
                $values .= "(?, ?, ?),";
                $pintar[] = $id_eval;
                $pintar[] = $key;
                $pintar[] = $value;
            }
        }
        $values = rtrim($values,",");
        // Identificamos el tipo de reporte
        if (strpos($data['redir'], 'outbound') !== false) {
            $tipo_reporte = 'outbound';
        } else if (strpos($data['redir'], 'inbound') !== false) {
            $tipo_reporte = 'inbound';
        } else {
            return false;
        }
        // Insertamos la información en las tablas de Quality
        $this->db->query("INSERT INTO quality_user (id_call_entry, id_user, datetime_calif) VALUES ($id_eval, $uid, now())");
        $this->db->query("INSERT INTO quality_values (id_call_entry, id_quality_fields, value) VALUES $values", $pintar);
        $this->load->model("repoback_model");
        // Obtenemos los datos con los que se califico.
        $sql = "SELECT SUM(q.value) AS total, if( c.comentario IS null, '', c.comentario) AS comentario
        FROM quality_values q
        LEFT JOIN quality_fields qf ON qf.id = q.id_quality_fields
        LEFT JOIN (
            SELECT q.id_call_entry, q.value AS comentario
            FROM quality_values q
            JOIN quality_fields qfc ON qfc.id = q.id_quality_fields
            WHERE q.id_call_entry = ?
            AND (qfc.num_order = 30 AND qfc.question = 'Comentario' AND qfc.weight = 0)
            LIMIT 1
        ) c ON c.id_call_entry = q.id_call_entry
        WHERE q.id_call_entry = ?
        AND (qf.num_order != 30 AND qf.question != 'Comentario' AND qf.weight != 0);
        ";
        $query = $this->db->query($sql, [$id_eval, $id_eval]);
        $calidad = $query->row();
        if( !$calidad ) {
            return false;
        }
        switch($tipo_reporte) {
            case 'outbound':
                // Si se califica desde rep_outbound agregamos los datos de calificacion a la table de rep_outbound
                $sql = "UPDATE rep_outbound SET calidad = ?, calidad_comentario = ?
                WHERE id=?";
                $this->db->query($sql, [$calidad->total, $calidad->comentario, $id_eval]);
            break;
            case 'inbound':
                $sql = "UPDATE rep_inbound SET calidad = ?, calidad_comentario = ?
                WHERE id=?";
                $this->db->query($sql, [$calidad->total, $calidad->comentario, $id_eval]);
            break;
        }

        return true;
    }

    public function SumaWeight($id_quality, $and="") {
        $query = $this->db->query("SELECT SUM(weight) AS total_weight FROM quality_fields WHERE id_quality=? $and",[$id_quality]);
        $total_weight = $query->row()->total_weight;

        return $total_weight != null ? $total_weight : 0;
    }

    public function validaPonderacionMenorIgualaCien() {
        $post = $this->input->post();

        $and        = ( !empty($post['id']) ) ? " AND id != ".$post['id'] : "";
        $weight     = $post['weight'];
        $id_quality = $post['id_quality'];
        $total_weight = $this->SumaWeight($id_quality, $and);

        $total = $total_weight + $weight;

        return $total <= 100 ? true : false;
    }

    //WHATSAPP
    // Cuentas por campaña y por agente
    public function whatsapp_cuentas( $id_campaign = 0 ) {
        $id_campaign = (int) $id_campaign;
        $sql_campaign = '';
        $sql_campaign_default = '';
        if( $this->udata['perfil'] != 'admin' ) {
            $whatsapp_in = ($this->udata['whatsapp'] == '') ? 0 : $this->udata['whatsapp'];
            $sql_campaign_default = "AND id IN ($whatsapp_in)";
        }
        $sql_campaign = "AND id_campaign = $id_campaign";
        $cuentas = $this->db->query("SELECT id, cuenta, nombre
            FROM whatsapp_cuentas
            WHERE 1=1
            $sql_campaign
            $sql_campaign_default
        ")->result();

        return $cuentas;
    }

    public function whatsapp_agentes($id_campaign) {
        $agentes = $this->datos_model->getRelUsers(["cam"=>$id_campaign, "act"=>false]);

        return $agentes;
    }

    public function whatsapp_contactos($min, $max, $id_wc, $id_agente, $bus ='') {
        $msg = '';
        $rows = [];
        if( $id_agente == 0 ) {
            $msg = 'Seleccione un agente';
        } else if( $id_wc == 0 ){
            $msg = 'Seleccione una cuenta de whatsapp valida';
        } else {
            $sql_bus = ( $bus != "" ) ? "AND wco.name LIKE '%$bus%'" : '';
            $sql = "SELECT wen.id_contact, wco.name, wco.account
                FROM whatsapp_entry wen
                LEFT JOIN whatsapp_contact wco ON wen.id_contact = wco.id
                LEFT JOIN whatsapp_session wse ON wse.id = wen.id_session 
                WHERE wse.datetime_start BETWEEN ? AND ?
                AND wen.id_wacta = ?
                AND wen.id_user = ?
                $sql_bus
                GROUP BY wen.id_contact, wco.name
                ORDER BY wco.name ASC;
            ";
            $rows = $this->db->query($sql, [$min.' 00:00:00', $max.' 23:59:59', $id_wc, $id_agente])->result();
            if( count($rows) == 0 )
                $msg = 'No se encontraron registros';
        }

        return [
            'rows' => $rows,
            'msg' => $msg
        ];
    }

    public function wa_save_ecm($id, $data) {        
        $datos = [
            'id_whatsapp_entry' => $data['id_whatsapp_entry'],
            'rating'            => $data['rating'],
            'comment'           => $data['comment'],
        ];
        if( $id == 0 ){//INSERTAMOS
            $datos['created_by'] = $this->udata['id'];
            if ($this->db->insert('whatsapp_message_rating', $datos)) {
                $c_rating = '
                <div class="px-2 mt-n2 mb-3 bg-secondary">
                    Calificación: '.html_rating($data['rating'], 5).'
                    <br/><i>'.$data['comment'].'</i>
                </div>';
                return [
                    'msg'       => 'Calificación agregada correctamente.',
                    'c_rating'  => $c_rating,
                    'success'   => true
                ];
            } else {
                return [
                    'msg' => 'No se pudo guardar la calificación.',
                    'success' => false
                ];
            }
        } else {//ACTUALIZAMOS
            return [
                'msg' => "No se pude actualizar la calificación",
                'success' => false
            ];
        }
    }

    public function wa_save_ecs($data) {
        $uid = $this->session->userdata("uid");
        $success = true;
        $msg = '';
        $html = ''; 
        $values = '';
        $pintar = array();
        $id_eval = $data['id_eval'];
        foreach ($data as $key => $value) {
            if ($key != "id_eval" && $key != "evaltotal" && $key != "redir") {
                $values .= "(?, ?, ?),";
                $pintar[] = $id_eval;
                $pintar[] = $key;
                $pintar[] = $value;
            }
        }
        $values = rtrim($values,",");
        // Validamos que no se haya agregado la calificación para esa sesion
        $n = $this->db->query("SELECT id_session
        FROM whatsapp_quality_user
        WHERE id_session = ?", [$id_eval])->num_rows();
        // Insertamos los datos
        if( $n == 0 ) {
            $this->db->trans_start();
            $this->db->query("INSERT INTO whatsapp_quality_user (id_session, id_user, datetime_calif) VALUES ($id_eval, $uid, now())");
            $this->db->query("INSERT INTO whatsapp_quality_values (id_session, id_quality_fields, value) VALUES $values", $pintar);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $success = false;
                $msg = 'No se pudo guardar la evaluación.';
            } else {
                $this->db->trans_commit();
                $result = $this->result_evaluacion_ses($id_eval);
                $success = true;
                $msg     = 'La evaluación se guardo con exito.';
                $html    = '<span class="text-primary">Calificación: '.$result['calif'].'%</span>';
            }
        } else {
            $success = false;
            $msg = "Ya se habia evaluado esta conversación.";
        }

        return [
            'success' => $success,
            'msg' => $msg,
            'html' => $html,
        ];
    }

    public function get_quality_fields($id) {
        $query = $this->db->query("SELECT * FROM quality_fields WHERE id = ?", [$id]);
        return $query->row();
    }


    public function has_comment_field($id_quality) {
        $query = $this->db->query("SELECT id
        FROM quality_fields
        WHERE id_quality = ? AND question = 'Comentario'
        AND weight = 0
        AND num_order = 30", [$id_quality]);

        return $query->num_rows() > 0 ? true : false;
    }

    public function add_comment_field($id_quality) {
        $query = $this->db->query("INSERT INTO quality_fields (id_quality, question, weight, num_order)
        VALUES (?, 'Comentario', 0, 30)", [$id_quality]);

        return $query;
    }

    public function registros_calificados($quality) {
        $id_quality = $quality->id;
        if( $quality->type == 'llamadas' ) {
            $sql = 'SELECT qv.id_call_entry, COUNT(qv.id_call_entry) AS n
            FROM quality_values qv
            INNER JOIN quality_fields qf ON qf.id = qv.id_quality_fields
            WHERE qf.id_quality = ?
            GROUP BY qv.id_call_entry';
        } 
        else if( $quality->type == 'whatsapp' ) {
            $sql = 'SELECT qv.id_session, count(qv.id_session) as n
            FROM whatsapp_quality_values qv
            INNER JOIN quality_fields qf ON qf.id = qv.id_quality_fields
            WHERE qf.id_quality = ?
            GROUP BY qv.id_session';
        }
        $query = $this->db->query($sql, $id_quality);
        $result = $query->result();
        $n = count($result);

        return $n;
    }

    private function result_evaluacion_ses($id_session) {
        $row = $this->db->query('SELECT SUM(qv.value) AS calif 
        FROM whatsapp_quality_values qv
        INNER JOIN quality_fields qf ON qf.id = qv.id_quality_fields
        WHERE id_session = ?
        AND (qf.question != "Comentario" OR qf.num_order != 30)', [$id_session])->row();
        $calificacion = (int) $row->calif;

        return ['calif' => $calificacion];
    }
}

?>
