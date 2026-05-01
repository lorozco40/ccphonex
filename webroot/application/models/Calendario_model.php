<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendario_model extends CI_Model
{

    //Query para crear calendarizacion en configuracion
    public function create($data) {
        return $this->db->query("INSERT INTO schedule (id_user, name, last, type, scheduled, observations,
        created_by, created_when) VALUES (?,?,?,?,?,?,?,now())", array($data['agentes'],
        $data['name'], $data['last'], $data['type'], $data['scheduled'], $data['observations'],
        $this->session->userdata('uid')));
    }

    //Query para ragendar calendarizacion desde configuracion calendario/modificar
    //Query para ragendar calendarizacion desde configuracion calendario/umodificar
    public function reagendar($data) {
        $this->db->query("UPDATE schedule
            SET status='Reagendado', modificated_by=?, modificated_when=now()
            WHERE id=?", array($this->session->userdata('uid'), $data['id'])
        );
        return $this->db->query("INSERT INTO schedule (id_user, name, last, type, scheduled, observations, parent,
            created_by, created_when) VALUES (?,?,?,?,?,?,?,?,now())", array($data['agentes'],
            $data['name'], $data['last'], $data['type'], $data['scheduled'], $data['observations'], $data['id'],
            $this->session->userdata('uid')));
    }

    //Query para cancelar calendarizacion en configuracion
    public function cancelar($data) {
        return $this->db->query("UPDATE schedule
            SET status='Cancelado', modificated_by=?, modificated_when=now()
            WHERE id=?", array($this->session->userdata('uid'), $data['id']));

    }

    //Envia los datos del calendario al controlador calendario/ueventos
    public function traecalendario($uid = null) {
        $masguer = "";
        if ($uid != null) $masguer = "AND id_user = '$uid'";
        $query = $this->db->query("SELECT * FROM schedule WHERE status='Activo' $masguer ORDER BY scheduled");

        return $query->result();
    }

    public function terminar($data) {
        return $this->db->query("UPDATE schedule
            SET status='Terminado', modificated_by=?, modificated_when=now()
            WHERE id=?", array($this->session->userdata('uid'), $data['id']));
    }

    public function reporte_eventos($data) {
        $maswere = '';
        if ($data['estatus']!='0') {
            $maswere = 'AND status = ?';
            $data['ops'] = [$data['estatus']];
        }
        $data['prequery'] = "SELECT s.id AS 'ID', COALESCE(s.parent,'') AS ReAg, s.created_when AS Agendado,
            CONCAT(u.name, ' ', u.last) AS Agente, CONCAT(s.type, ', ', s.name, ' ', s.last, ', ',
              IF(length(s.observations)>30, CONCAT(SUBSTRING(s.observations,1,30),' ...'), s.observations)) AS Evento,
            s.scheduled AS Para, s.status as Estatus, s.modificated_when AS 'Fecha status'
            FROM schedule s
            LEFT JOIN user u on u.id = s.id_user
            WHERE DATE(s.created_when) BETWEEN '$data[min]' AND '$data[max]' AND u.id in ($data[agente]) $maswere";

        return $this->datos_model->manejadorqueries($data);
    }

}


?>
