<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warate extends MY_Controller
{

    public function ver(int $wac = 0)
    {
        $query = $this->db->query("SELECT * FROM whatsapp_cuentas WHERE id = ?", [$wac]);
        $data["wac"] = $query->row();
        if (!$data["wac"]) show_error("Cuenta Whatsapp requerida!");
        $query = $this->db->query("SELECT * FROM whatsapp_rate WHERE id_wacta = ?", [$wac]);
        $data["data"]  = $query->result();
        $data["title"] = "Encuestas wa";
        $data['jscript'] = 'config/warate';

        $this->armado->mostrar(array(
            'view' => 'config/warate',
            'data' => $data,
        ));
    }

    public function save()
    {
        $this->load->model('warate_model');
        $data = $this->input->post();
        $res = $this->warate_model->save($data);
        $tipo = ($res == "Datos guardados") ? "info" : "errormsg";

        $this->session->set_flashdata($tipo, $res);
        redirect('warate/ver/' . $data['wid']);
    }

    public function saverctv()
    {
        $this->load->model('warate_model');
        $data = $this->input->post();
        $res = $this->warate_model->saverctv($data);
        
        Header('Content-Type: application/json');
        echo json_encode($res);
    }
    
    public function getrctv()
    {
        $this->load->model('warate_model');
        $data = $this->input->post();
        $data['data'] = $this->warate_model->getrctv($data['rid']);
        
        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    // Desactivar reactivo
    public function darctv()
    {
        $this->load->model('warate_model');
        $data = $this->input->post();
        $res = $this->warate_model->darctv($data);

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

}
