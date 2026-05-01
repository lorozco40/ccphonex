<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Facerecon extends CI_Controller
{

    public function index()
    {
        $data = $this->input->get();
        if (empty($data['sid'])) { show_404(); }
        $query = $this->db->query("SELECT * FROM whatsapp_session
            WHERE id = ?", [$data['sid']]); //  AND datetime_received > now() - interval 5 minute
        $row = $query->num_rows();
        if ($row == 1) {
            $ses = $query->row_array();
            $this->armado->mostrar(array(
                'view'     => 'facerecon',
                'title'    => 'ExternoBot',
                'template' => 'empty',
                'data'     => $ses,
            ));
            return;
        }

        show_404();
    }

    public function confirma() {
        $data  = $this->input->post();
        if (empty($data['sid'])) { show_404(); }
        $query = $this->db->query("SELECT * FROM whatsapp_session WHERE id = ?", [$data['sid']]);
        $ses = $query->row();

        // Ejecutar Whatsapp script con datos recibidos

        $this->load->library('whatsappfun');
        $this->whatsappfun->guardaVar([
            "cid" => $ses->id_contact,
            "var" => "wara",
            "val" => $data['wara'],
            "tip" => "string",
            "cam" => "temporal",
        ]);
        $this->load->model("wabot_model");

        // Con esto manda al submenú del único hijo del menú actual
        $this->wabot_model->respondeBot($ses, "Fr0M3x7");

        $ret['msg'] = "Gracias por tu cooperación";
        if ($data['sid'] == 0) {
            $ret['msg'] = "Que pasó aquí?";
        }

        $this->load->view('facereconconf', $data);
    }

}
