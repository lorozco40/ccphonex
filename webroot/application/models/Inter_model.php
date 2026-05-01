<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inter_model extends CI_Model
{

    public function getOpts($available = false) {
        $maswhere = '';
        if ($available) {
            $maswhere = " AND uto.busy = ''";
        }
        $query = $this->db->query("SELECT uto.id, uto.eti, uto.`des` FROM user_trans_opts uto
            JOIN user_trans ut ON ut.grupo = uto.grupo WHERE ut.id_user = ? $maswhere
            ORDER BY uto.eti ASC",
            $this->session->userdata('uid'));
        return $query->result();
    }

    public function aplicatrans($tid) {
        $query = $this->db->query("SELECT * FROM user_trans_opts WHERE id = ?", [$tid]);
        $tr = $query->row();
        if ($tr->busy) {
            return false;
        }
        $tr = json_decode($tr->trans);
        // para cada uno de de los elementos del objeto $tr hacer un update a la bd
        foreach ($tr as $key => $value) {
            $this->db->update('user_data', ['val' => $value], ['id_catalog' => $key, 'id_user' => $this->session->userdata('uid')]);
        }
        $this->db->update('user_trans_opts', ['busy' => $this->session->userdata('uid')], ['id' => $tid]);
        $error = $this->db->error();
        if (empty($error['code'])) {
            return true;
        }

        return false;
    }

}
