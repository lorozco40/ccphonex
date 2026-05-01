<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agentes extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $this->load->model("agentes_model");
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['agentes']  = $this->agentes_model->lista($data['campanas']);
        $data['sl']       = $this->datos_model->getParams('sl');
        $data['title']    = 'Agentes';
        $data['extjs']    = '//www.gstatic.com/charts/loader.js';
        $data['jscript']  = 'agentes';
        $datos            = array(
            'view' => 'agentes',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

    public function status() {
        $campana = $this->input->post('campana');
        $this->load->model("agentes_model");
        $data = $this->agentes_model->status($campana);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

}
?>
