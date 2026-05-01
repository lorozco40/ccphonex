<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Norma035 extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model("norma035_model");
    }

    public function index() {
        $data["campaigns"]    = $this->datos_model->getCampanas();
        $data["campaigns_id"] = $this->datos_model->getCampanas(false, false);
        $data['title']        = 'Norma 035';

        $this->armado->mostrar(array(
            'view' => 'norma/dashboard',
            'data' => $data,
        ));
    }

    public function reportes(){
        $this->armado->mostrar([
            'data' => ['title' => 'Norma 035', "campaigns" => $this->datos_model->getCampanas()],
            'view' => 'norma/reportes'
        ]);
    }

    public function dashboard() {
        $campana = (NULL !== $this->input->post("campana")) ? $this->input->post("campana") : 0;
        $min     = convierte($this->input->post('min'), $this->idfor);
        $max     = convierte($this->input->post('max'), $this->idfor);
        $data    = $this->norma035_model->dashindicadores($campana, $min, $max);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function dashdiasemana() {
        $campana = (NULL !== $this->input->post("campana")) ? $this->input->post("campana") : 0;
        $min     = convierte($this->input->post('min'), $this->idfor);
        $max     = convierte($this->input->post('max'), $this->idfor);
        $data    = $this->norma035_model->dashdiasemana($campana, $min, $max);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

}

?>
