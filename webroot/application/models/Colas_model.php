<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Colas_model extends CI_Model
{

    public function traecolas() {
        $query = $this->db->query("SELECT * FROM queue ORDER BY id");
        return $query->result();
    }

    //Consulta las colas ligadas a las campanas a las que el usuario tiene acceso. En el caso de admin, trae todas
    public function traeColasPorCampanas() {
        if( $this->udata['perfil'] == 'admin')
            $query = $this->db->query("SELECT * FROM queue ORDER BY id");
        else 
            $query = $this->db->query("SELECT * FROM queue WHERE id_campaign IN (".$this->udata['campanas'].") ORDER BY id");
        return $query->result();
    }

    //Retorna un array con los nombres de las colas que pertenecen a las campanas que se pasaron como parametro
    //Se espera una cadena de texto separada por , con los ids de campanas ej, 2,4,5
    //Si no se pasa ningun parametro traera los nombres de las colas a las que el usuario tiene acceso, en caso de ser admin, trae todas
    public function traeColasNamePorCampanas($campanas = NULL) {
        $campanas_in = ($campanas == NULL) ? $this->udata['campanas'] : $campanas;
        $campanas_in = ($campanas_in != '') ? $campanas_in : 0; 
        $result = [];
        if( $this->udata['perfil'] == 'admin' && $campanas == NULL)
            $query = $this->db->query("SELECT name FROM queue ORDER BY id");
        else 
            $query = $this->db->query("SELECT name FROM queue WHERE id_campaign IN ($campanas_in) ORDER BY id");
        foreach ($query->result() as $row)
            $result[] = $row->name; 

        return $result;
    }

    public function update() {
        $data = $this->input->post();
        $id_campaign = (isset($data['id_campaign'])) ? $data['id_campaign'] : NULL;
        $show = (isset($data['show'])) ? 1 : 0;
        if( $id_campaign == NULL )
            $result = $this->db->query("UPDATE queue set `desc`=?,                name=?, `show`=? where id=?", array($data['desc'],               $data['name'], $show, $data['id']));
        else
            $result = $this->db->query("UPDATE queue set `desc`=?, id_campaign=?, name=?, `show`=? where id=?", array($data['desc'], $id_campaign, $data['name'], $show, $data['id']));

        return $result;
    }

}
?>
