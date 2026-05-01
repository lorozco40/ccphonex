<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lhc_model extends CI_Model
{

    private $dtfor; // date time format
    private $dfor; // date format
    private $idfor; // input date format

    public function __construct() {
        $sispar = $this->datos_model->getParams("sistema");
        $this->dtfor = $sispar->FormatoFechaMysql;
        $this->idfor = $sispar->FormatoFechaInput;
        $this->dfor = explode(" ", $this->dtfor)[0];
    }

    public function insert_chat($data) {
        $chausers = $this->datos_model->getChatUsers();

        foreach ($data->list as $key => $row) {

            $time = date('Y-m-d H:i:s', $row->time);
            $utyp = date('Y-m-d H:i:s', $row->user_typing);
            $lastmsg = date('Y-m-d H:i:s', $row->last_user_msg_time);
            $lsync = date('Y-m-d H:i:s', $row->lsync);
            $lastmsgtime = date('Y-m-d H:i:s', $row->last_op_msg_time);
            $userclose = date('Y-m-d H:i:s', $row->user_closed_ts);

            $id_user = $chausers[$row->user_id];

            $query = $this->db->query("INSERT IGNORE hc_chats (`id`, `id_chat_user`, `id_user`, `nick`, `status`, `time`, `ip`, `referrer`, `email`,
                `user_status`, `country_code`, `user_typing`, `user_typing_text`, `operator_typing`, `has_unread_messages`, `last_user_msg_time`,
                `last_msg_id`, `wait_time`, `chat_duration`, `lsync`, `last_op_msg_time`, `user_closed_ts`) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                array ($row->id, $row->user_id, $id_user, $row->nick, $row->status, $time, $row->ip, $row->referrer, $row->email,
                $row->user_status, $row->country_code, $utyp, $row->user_typing_txt, $row->operator_typing, $row->has_unread_messages, $lastmsg,
                $row->last_msg_id, $row->wait_time, $row->chat_duration, $lsync, $lastmsgtime, $userclose)
            );
        }
        return;
        // dd($row);
    }

    public function insert_message($data) {

        foreach ($data->result->messages as $key => $row) {
            $time = date('Y-m-d H:i:s', $row->time);

            $query = $this->db->query("INSERT IGNORE hc_messages (`id`, `id_chat`, `id_chat_user`, `msg`, `datetime`, `name_support`, `meta_msg`)
            values (?,?,?,?,?,?,?)", array ($row->id, $row->chat_id, $row->user_id, $row->msg, $time, $row->name_support, $row->meta_msg));
        }
        return;
        // dd($row);
    }

    public function chats($data) {
        $data['prequery'] = "SELECT h.time, h.id, h.id_chat_user, concat(u.name,' ',u.last) agente, h.nick, h.status, h.ip,
            h.user_status, SEC_TO_TIME(h.wait_time) espera, SEC_TO_TIME(h.chat_duration) duracion, h.last_op_msg_time,
            h.user_closed_ts, h.last_user_msg_time
            -- time 1er mensaje ó inicio de chats
            -- wait_time espera antes de aceptar el chats
            -- chat_duration duración del chat
            -- last_op_msg_time ultimo mensaje del operador
            -- user_closed_ts cierre del chat por el usuario
            -- last_user_msg_time cierre del chat por operador
            FROM hc_chats h
            INNER JOIN user u ON u.id=h.id_user
            WHERE DATE(h.time) BETWEEN '$data[min]' AND '$data[max]' AND h.id_user in ($data[agente])
            ORDER BY h.time DESC";

        return $this->datos_model->manejadorqueries($data);
    }

    public function mensajes($data) {
        $data['prequery'] = "SELECT m.`datetime`, m.id_chat, m.id, concat(u.name,' ',u.last) agente,
            m.name_support, m.id_chat_user, m.msg
            FROM hc_messages m
            LEFT JOIN hc_chats c ON c.id=m.id_chat
            LEFT JOIN user u ON u.id=c.id_user
            WHERE DATE(m.`datetime`) BETWEEN '$data[min]' AND '$data[max]' AND c.id_user in ($data[agente])
            ORDER BY m.id_chat DESC, m.id ASC";

        return $this->datos_model->manejadorqueries($data);
    }

    public function espera_chat($min, $max) {
        $min = convierte($min, $this->idfor);
        $max = convierte($max, $this->idfor);
        $fini = "$min 00:00:00";
        $ffin = "$max 23:59:59";
        $prequery = "SELECT concat(u.name,' ',u.last) agente,
                COALESCE(SUM(IF(h.wait_time<=10,1,0)),0) diez,
                COALESCE(SUM(IF((h.wait_time>=11 AND h.wait_time<=20),1,0)),0) veinte,
                COALESCE(SUM(IF((h.wait_time>=21 AND h.wait_time<=30),1,0)),0) treinta,
                COALESCE(SUM(IF((h.wait_time>=31 AND h.wait_time<=40),1,0)),0) cuarenta,
                COALESCE(SUM(IF((h.wait_time>=41 AND h.wait_time<=50),1,0)),0) cincuenta,
                COALESCE(SUM(IF((h.wait_time>=51 AND h.wait_time<=60),1,0)),0) sesenta,
                COALESCE(SUM(IF((h.wait_time>=61 AND h.wait_time<=90),1,0)),0) noventa,
                COALESCE(SUM(IF((h.wait_time>=91 AND h.wait_time<=120),1,0)),0) ciento_veinte,
                COALESCE(SUM(IF(h.wait_time>=121,1,0)),0) ciento_veintiuno,
                COALESCE(COUNT(h.wait_time),0) sumtotal,
                SEC_TO_TIME(ROUND(AVG(h.wait_time))) avg,
                SEC_TO_TIME(MAX(h.wait_time)) maxespera
            FROM hc_chats h
            LEFT JOIN user u ON u.id = h.id_user
            WHERE DATE(h.`time`) BETWEEN '$fini' AND '$ffin'";

        $query = $this->db->query($prequery. "  GROUP BY agente");
        $respu['data'] = $query->result();
        $query = $this->db->query($prequery);
        $respu['tot'] = $query->row();

        return $respu;
    }

    public function reportes_lhc($data) {
        $data['min'] = convierte($data['min'], $this->idfor);
        $data['max'] = convierte($data['max'], $this->idfor);
        if($data['reporte']=="lhc_chats"){
            $agente = ($data['agente']!="0") ? " AND u.id = '".$data['agente']."'" : "";
            $query = $this->db->query("SELECT h.time 'Inicio', h.id 'Chat', h.id_chat_user 'User chat', concat(u.name,' ',u.last) 'Agente', h.nick 'Nick',
                    if(h.status = '0', 'Pendiente', if(h.status = '1', 'Activo', if(h.status = '2', 'Cerrado', if(h.status = '3', 'Chatbox', 'Operadores')))) 'Status chat',
                    h.ip 'Ip', h.user_status 'Status usuario', SEC_TO_TIME(h.wait_time) 'Espera', SEC_TO_TIME(h.chat_duration) 'Duración',
                    h.last_op_msg_time 'Último mensaje', h.user_closed_ts 'Cierre usuario', h.last_user_msg_time 'Cierre operador'
                    -- time 1er mensaje ó inicio de chats
                    -- wait_time espera antes de aceptar el chats
                    -- chat_duration duración del chat
                    -- last_op_msg_time ultimo mensaje del operador
                    -- user_closed_ts cierre del chat por el usuario
                    -- last_user_msg_time cierre del chat por operador
                    FROM hc_chats h
                    INNER JOIN user u ON u.id=h.id_user
                WHERE DATE(h.time) BETWEEN '$data[min]' AND '$data[max]' $agente
                ORDER BY h.time DESC");
        }

        if($data['reporte']=="lhc_mensajes"){
            $agente = ($data['agente']!="0") ? " AND u.id = '".$data['agente']."'" : "";
            $query = $this->db->query("SELECT m.`datetime` 'Fecha', m.id_chat 'Chat', m.id 'No. mensaje', concat(u.name,' ',u.last) 'Agente',
                    m.name_support 'Operador', if(m.id_chat_user = '-1', 'Sistema', if(m.id_chat_user = '0', 'Usuario', 'Operador')) 'Respuesta',
                    m.msg 'Mensajes'
                FROM hc_messages m
                LEFT JOIN hc_chats c ON c.id=m.id_chat
                LEFT JOIN user u ON u.id=c.id_user
                WHERE DATE(m.`datetime`) BETWEEN '$data[min]' AND '$data[max]'
                ORDER BY m.id_chat DESC, m.id ASC");
        }

        if ($data['reporte']=="lhc_espera") {
            $query = $this->db->query("SELECT concat(u.name,' ',u.last) 'Agente',
                    COALESCE(SUM(IF(h.wait_time<=10,1,0)),0) '0-10',
                    COALESCE(SUM(IF((h.wait_time>=11 AND h.wait_time<=20),1,0)),0) '11-20',
                    COALESCE(SUM(IF((h.wait_time>=21 AND h.wait_time<=30),1,0)),0) '21-30',
                    COALESCE(SUM(IF((h.wait_time>=31 AND h.wait_time<=40),1,0)),0) '31-40',
                    COALESCE(SUM(IF((h.wait_time>=41 AND h.wait_time<=50),1,0)),0) '41-50',
                    COALESCE(SUM(IF((h.wait_time>=51 AND h.wait_time<=60),1,0)),0) '51-60',
                    COALESCE(SUM(IF((h.wait_time>=61 AND h.wait_time<=90),1,0)),0) '61-90',
                    COALESCE(SUM(IF((h.wait_time>=91 AND h.wait_time<=120),1,0)),0) '91-120',
                    COALESCE(SUM(IF(h.wait_time>=121,1,0)),0) '>121',
                    COALESCE(COUNT(h.wait_time),0) 'Chats',
                    SEC_TO_TIME(ROUND(AVG(h.wait_time))) 'Promedio',
                    SEC_TO_TIME(MAX(h.wait_time)) 'Max espera'
                FROM hc_chats h
                LEFT JOIN user u ON u.id = h.id_user
                WHERE DATE(h.`time`) BETWEEN '$data[min]' AND '$data[max]' GROUP BY agente");
        }

        $result["registros"] = $query->result_array();
        $result["campos"] = $query->list_fields();

        return $result;
    }

}

?>
