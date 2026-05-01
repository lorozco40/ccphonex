<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repodin_model extends CI_Model
{

    public function repodin($data) {
        $data['prequery'] = "SELECT rd.*, concat(u.name,' ',u.last) creador, f.name form
            FROM repodin rd
            LEFT JOIN user u ON u.id = rd.created_by
            LEFT JOIN form f ON f.id = rd.id_form";

        return $this->datos_model->manejadorqueries($data);
    }

    public function formfields($data) {
        $query = $this->db->query("SELECT 'id' AS 'slug', 'ID/Ticket' AS 'nombre'
            UNION
            SELECT `slug`, `name` FROM form_fields
            WHERE id_form = ? AND slug NOT LIKE 'sep_%'
            AND `slug` NOT IN ('id_cliente','asignar_a','informar','semaforo')", [$data['fid']]);

        return $query->result();
    }

    function guardar($data) {
        if ($data['id'] == 0) { // Nuevo registro
            $query = $this->db->query("INSERT")
        } else {
            $query = $this->db->query("UPDATE")
        }
    }

}
