<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extapi extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model("extapi_model");
    }

    public function index() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['jscript']  = 'config/extapi';
        $data['title']    = 'API\'s externas';
        
        $this->armado->mostrar([
            'data' => $data,
            'view' => 'config/extapi',
        ]);
    }
    
    public function lista() {
        $data = $this->input->post();
        $resp = $this->extapi_model->list($data);

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function guardar() {
        $this->load->library('form_validation');
		$this->form_validation->set_rules('name',  		    "'Nombre API'", 'required|max_length[30]');
		$this->form_validation->set_rules('url',  		    "'URL'",        'required|max_length[100]');
		$this->form_validation->set_rules('sign',  		    "'Nombre tok'", 'max_length[50]');
		$this->form_validation->set_rules('user',  		    "'Usuario'",    'max_length[100]');
		$this->form_validation->set_rules('pass',  		    "'Contraseña'", 'max_length[50]');
		$this->form_validation->set_rules('logloc',  		"'Ubica tok'",  'integer|in_list[0,1,2,3]');
		$this->form_validation->set_rules('active',  		"'Activa'",  	'integer|in_list[0,1]');
		$this->form_validation->set_rules('valid_crt',  	"'Certif'",  	'integer|in_list[0,1]');

		if ($this->form_validation->run() == FALSE){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $resp = [
                "tipo"=>"error",
                "msg" => $errors[ $fields[0] ]
            ];
		}
        else {
            $data = $this->input->post();
            $resp = $this->extapi_model->save($data);
        }
        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function guardarMet() {
        $this->load->library('form_validation');
		$this->form_validation->set_rules('prot',  		    "'Prot'",  	    'required|in_list[DELETE,POST,PUT,GET]|max_length[10]');
		$this->form_validation->set_rules('met',  		    "'Método'",     'required|max_length[50]');
		$this->form_validation->set_rules('xtype',          "'X-Type'",     'required|max_length[15]');
		if ($this->form_validation->run() == FALSE) {
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $resp = [
                "tipo"=>"error",
                "msg" => $errors[ $fields[0] ]
            ];
		} else {
            $data = $this->input->post();
            $resp = $this->extapi_model->saveMet($data);
        }
        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function guardarFields()
    {
        $this->load->library('form_validation');
		$this->form_validation->set_rules('field',  		    "'Campo'",  	    'required|max_length[30]');
		$this->form_validation->set_rules('ftype',  		    "'Tipo'",           'required|in_list[int,string,bool]');
		$this->form_validation->set_rules('dir',                "'Dir'",            'integer|in_list[0,1]');
		$this->form_validation->set_rules('req',  		        "'Req'",            'integer|in_list[0,1]');
        $this->form_validation->set_rules('descript',  		    "'Descripción'",    'required|max_length[250]');
		if ($this->form_validation->run() == FALSE){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $resp = [
                "tipo"=>"error",
                "msg" => $errors[ $fields[0] ]
            ];
		}
        else {
            $data = $this->input->post();
            $resp = $this->extapi_model->saveFields($data);
        }

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function deleteFields() {
        $id = (int) $this->input->post('id');
        $resp = $this->extapi_model->deleteFields($id);

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function deleteMet() {
        $id = (int) $this->input->post('id');
        $resp = $this->extapi_model->deleteMet($id);

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function delete() {
        $id = (int) $this->input->post('id');
        $resp = $this->extapi_model->delete($id);

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

}
