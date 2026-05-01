<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colas extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $datahead['title'] = 'Configuración';
		$datos = array(
			'data' => $datahead,
			'view' => 'config/configagent'
		);
		$this->armado->mostrar($datos);
    }

    public function ver() {
        $colas = $this->datos_model->colas();
        echo "<pre>";
        var_dump($colas);
        return;
    }

    public function config() {
        $this->load->model('colas_model');
        $data['data']  = $this->colas_model->traeColasPorCampanas();//Extraemos las colas a las que el usuario tiene acceso
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title'] = 'Colas_conf';

        $datos = array(
            'view' => 'config/colas_conf',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

    public function actualizar() {
        $this->form_validation->set_rules('name', 'Nombre', 'required');
        $this->form_validation->set_rules('id_campaign', 'Campaña', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'No puedes actualizar la cola.');
        } else {
            $this->load->model('colas_model');
            if ($this->colas_model->update()) {
                $this->session->set_flashdata('infomsg', 'Cola actualizada con éxito.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error al actualizar la cola.');
            }
        }
        redirect('colas/config');
    }
}
