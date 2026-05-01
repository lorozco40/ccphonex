<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Panel extends MY_Controller
{

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model("crm_model");
        $data['forms']      = $this->crm_model->getFormsXAgente();
        $data['title']      = "CRM Panel";
        $data['jscript']    = ['consola/form','consola/consola','crmpanel'];
        $data['campanas']   = $this->datos_model->getCampanas();
        $data['agentes']    = $this->datos_model->getRelUsers(["cam"=>$data['campanas']]);
        $this->armado->mostrar(array(
            'view' => 'crmpanel',
            'data' => $data,
        ));
    }

    public function getickets() {
        $this->load->model('crm_model');
        $res = $this->crm_model->getickets($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function geticket() {
        $data = $this->input->post();
        $this->load->model("form_model");
        $data = $this->form_model->get_form($data["cid"], $data, 'form', $data["fid"]);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function guardar() {
        $this->load->model("crm_model");

        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->guardar());
    }

    public function guardarcali() {
        $this->load->model("crm_model");

        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->guardarcali());
    }

    public function guardararchivo() {
        $this->load->model("crm_model");

        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->guardararchivo());
    }

}
