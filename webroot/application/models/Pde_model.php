<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pde_model extends CI_Model
{

    public function gtwaSave($url, $token, $data) {
        if (!$this->db->table_exists('client_gtwa')) {
            $this->db->query("CREATE TABLE `client_gtwa` (
                `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `ip` varchar(15) NOT NULL,
                `fecha` timestamp NOT NULL,
                `url` varchar(255) NOT NULL,
                `token` varchar(255) NOT NULL,
                `json` text NOT NULL
            )");
        }
        return $this->db->query("INSERT INTO `client_gtwa` (`ip`, `url`, `token`, `json`) values (?, ?, ?, ?)",
        [getUserIP(), $url, $token, $data]);
    }

}
