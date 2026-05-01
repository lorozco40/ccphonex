<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dids_model extends CI_Model
{

    public function get_list($ini, $fin) {
        $query = $this->db->query("SELECT * from dids order by id_campaign limit ?, ?",
        array($ini, $fin));
        return $query->result();
    }

    public function campaign() {
        $query = $this->db->query("SELECT id, name from campaign where active=1 order by name");
        $res = $query->result_array();

        return $res;
    }

    public function create($data) {
        return $this->db->query("INSERT into dids (id_campaign, fullnum, name, `desc`, created_by, created_when)
            values (?,?,?,?,?,now())", array($data['campana'], $data['fullnum'], $data['name'], $data['desc'],
            $this->session->userdata('uid')));
    }

    public function update() {
        $data = $this->input->post();
        return $this->db->query("UPDATE dids set id_campaign=?, fullnum=?, name=?, `desc`=?, created_by=? where id=?",
            array($data['campana'], $data['fullnum'], $data['name'], $data['desc'], $this->session->userdata('uid'), $data['id']));
    }

}
?>
