<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Permisos_model extends CI_Model
{

    public function getAll() {
        $query = $this->db->query("SELECT * from catalogs where cat='permiso' order by val");

        return $query->result();
    }

    public function cuenta() {
        $query = $this->db->where('cat', 'permiso')->get('catalogs');
        return $query->num_rows();
    }

    public function getPage($ini, $fin) {
        $query = $this->db->query("SELECT * from catalogs where cat='permiso' order by val limit ?, ?",
        array($ini, $fin));
        return $query->result();
    }

    public function create($data) {
        return $this->db->query("INSERT into catalogs (cat, eti, val, num_order) values (?, ?, ?, ?)",
        array('permiso', $data['eti'], $data['val'], $data['num_order']));
    }

    public function update($data) {
        return $this->db->query("UPDATE catalogs set eti=?, val=?, num_order=? where id=?",
        array($data['eti'], $data['val'], $data['num_order'], $data['id']));
    }

    public function delete($id) {
        return $this->db->query("DELETE from catalogs where id=?", array($id));
    }

}

?>
