<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migracion_model extends CI_Model
{
    private $admin_password = "";

    public function __construct() {
        parent::__construct();
        $this->backup_values();
        $this->load->dbforge();
    }

    private function backup_values() {
            $this->db->select('pass');
            $this->db->from('user');
            $this->db->where('id',1);
            $this->db->limit(1);
            $row = $this->db->get()->row();
            $this->admin_password = $row->pass;

    } 

    public function get_tables($details = false) {
        $tables = [];
        $query = $this->db->query("
            SELECT table_name, table_rows
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
            ORDER BY table_name ASC;
        ");
        $result = $query->result_array();

        foreach ($result as $row) {
            $table_name = $row['table_name'];
            $table_rows = $row['table_rows'];

            if( $details === true ) {
                $tables[] = "[$table_rows]".$table_name;
            }
            else {
                $tables[] = $table_name;
            }
        }

        return $tables;
    }

    public function delete_tables($tables) {
        foreach( $tables as $table ) {
            $this->dbforge->drop_table($table);
        }
        return true;
    }

    public function reset_tables($no_reset = []) {
        $this->load->dbforge();
        $tables = $this->get_tables();
        $n = 0;
        foreach( $tables as $table) {
            if( !in_array($table, $no_reset) ) {
                $n ++;
                $this->db->query("TRUNCATE TABLE $table;");
            }
        }
        return $n;
    }

    public function check_fk($value = 1) {
        $value = ($value == 0) ? 0 : 1;
        $this->db->query("SET FOREIGN_KEY_CHECKS = $value;");
        return true;
    }

    public function show_check_fk() {
        $query = $this->db->query("SHOW VARIABLES LIKE 'FOREIGN_KEY_CHECKS'");
        $row = $query->row();
        if ($row->Value == 1 || $row->Value == 'ON') {
            $msg = "FOREIGN_KEY_CHECKS está habilitado";
        } else {
            $msg = "FOREIGN_KEY_CHECKS está deshabilitado";
        }

        return $msg;
    }

    public function default_values() {
        $this->db->query("DELETE FROM user WHERE id >= 6 AND id != 9999");
        $this->db->query("DELETE FROM user_data WHERE id_user >= 6");
        $campaign = [
            'dids'      => '1111',
            'name'      => 'Demo',
            'script'    => 'Saludos desde campaña demo',
            'created_by'=> 1,
            'active'    => 1,
        ];
        $this->db->insert('campaign', $campaign);
        $camData = ['id_campaign'=>1,'atributo'=>'licencias','valor'=>10];
        $this->db->insert('campaign_data', $camData);
        for ($i=2; $i < 6; $i++) {
            $this->db->insert('campaign_licenses',['id_campaign'=>1,'id_user'=>$i]);
        }

        return $this->db->query("UPDATE user_data ud
            LEFT JOIN catalogs c ON ud.id_catalog = c.id
            SET ud.val=1 WHERE c.cat = 'userData' AND c.val = 'campanas'");
     }
}
