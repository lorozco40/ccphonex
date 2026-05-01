<?php

defined('BASEPATH') || exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class Repodin extends MY_Controller
{

    public function index() {
        $this->load->model('form_model');
        $data['title']   = 'Administrar reportes dinámicos';
        $data['jscript'] = 'repodin/main';
        $data['forms']   = $this->form_model->get_list_active();

        $this->armado->mostrar(array(
            'view' => 'repodin/main',
            'data' => $data
        ));
    }

    public function guardar() { // Crear y actualizar depende de id 0 = crear
        $this->load->model("repodin_model");
        $res = $this->repodin_model->guardar($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function formfields() {
        $this->load->model("repodin_model");
        $res = $this->repodin_model->formfields($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

}
