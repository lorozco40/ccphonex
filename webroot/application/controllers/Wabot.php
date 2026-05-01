<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class Wabot extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->library('whatsappfun');
        $this->load->model("wabot_model");
        $this->load->model("whatsapp_model");
    }

    public function ver($id_wacta = 0) {
        $data             = [];
        $data['title']    = 'Whatsapp bots';
        $data['jscript']  = 'config/wabot';
        $data['id_wacta'] = $id_wacta;
        $this->load->model("wabot_model");
        $data['scripts'] = $this->wabot_model->get_scripts();
        if ($id_wacta == '0') {
            $this->load->model("whatsapp_model");
        }
        $data['list_extapi_met'] = $this->wabot_model->extapi_met_list();

        $this->armado->mostrar(array(
            'view' => 'config/wabot',
            'data' => $data,
        ));
    }

    public function list() {
        $data = $this->input->get();
        $data['pag'] = (!empty($this->input->get('pag'))) ? $this->input->get('pag') : 0;
        $data['rpp'] = (!empty($this->input->get('rpp'))) ? $this->input->get('rpp') : REGS_POR_PAG;
        Header('Content-Type: application/json');
        echo json_encode($this->wabot_model->lista($data));
    }

    public function guardar() {
        $this->load->library('form_validation');
		$this->form_validation->set_rules('id',  	        "'ID'",  	            'required|integer');
		$this->form_validation->set_rules('wid',  		    "'WID'",                'required|integer');
		$this->form_validation->set_rules('name',           "'Nombre'",             'required|max_length[30]');
		$this->form_validation->set_rules('label',          "'Grupo'",              'max_length[30]');
		$this->form_validation->set_rules('intro',  		"'Saludo'",             'required|max_length[254]');
		$this->form_validation->set_rules('bye',  		    "'Despedida'",          'required|max_length[254]');
        $this->form_validation->set_rules('out_of_time',    "'Fuera de Horario'",   'required|max_length[254]');
        $this->form_validation->set_rules('wait_time',  	"'Tiempo de espera'",   'required|integer');
        $this->form_validation->set_rules('ini_script',  	"'Script de inicio'",   'integer');
		if ($this->form_validation->run() == FALSE){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $r = [
                "error" => $errors[ $fields[0] ]
            ];
		} else {
            $r = $this->wabot_model->guardar($this->input->post());
        }

        Header('Content-Type: application/json');
        echo json_encode($r);
    }

    public function oplist() {
        $data = $this->input->get();
        Header('Content-Type: application/json');
        echo json_encode($this->wabot_model->oplist($data));
    }

    public function opguardar() {
        Header('Content-Type: application/json');
        echo json_encode($this->wabot_model->opguardar($this->input->post()));
    }

    public function opborrar() {
        Header('Content-Type: application/json');
        echo json_encode($this->wabot_model->opborrar($this->input->get('id')));
    }

    public function reporte_sesion() {
        $this->load->model("whatsapp_model");
        $data['title']            = 'Whatsapp sesiones de bot';
        $data['cual']             = 'reporte_sesion';
        $data['modelo']           = 'wabot';
        $data['jscript']          = 'reportes/reporte';
        $data['massel']['Cuenta'] = $this->whatsapp_model->mis_wactas();
        $data['aucos'][]          = (object)["lab"=>"Contacto", "nam"=>"wacto",
            "mod"=>"whatsapp", "dep"=>"cuenta", "met"=>"auco"];

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function reporte_indicador() {
        $this->load->model("whatsapp_model");
        $data['title']            = 'Whatsapp indicadores de bot';
        $data['cual']             = 'reporte_indicador';
        $data['modelo']           = 'wabot';
        $data['jscript']          = 'reportes/reporte';
        $data['massel']['Cuenta'] = $this->whatsapp_model->mis_wactas();
        $data['aucos'][]          = (object)["lab"=>"Contacto", "nam"=>"wacto",
            "mod"=>"whatsapp", "dep"=>"cuenta", "met"=>"auco"];

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function actualizar_campo_activo() {
        Header('Content-Type: application/json');
        echo json_encode($this->wabot_model->actualizar_campo_activo($this->input->post()));
    }

    public function opActualizar() {
        $this->form_validation->set_rules('id', 'ID', 'required');
        $this->form_validation->set_rules('option', 'Opción', 'required');
        $this->form_validation->set_rules('label', 'Texto', 'required');
        $this->form_validation->set_rules('action', 'Acción', 'required');
        if( $this->input->post('action') == 7 ) //Redirect
            $this->form_validation->set_rules('redirect', 'Redirección', 'required');
        if( $this->input->post('action') == 8 ) //Script
            $this->form_validation->set_rules('id_script', 'Script', 'required');

        if ($this->form_validation->run()==FALSE) {
            $jsondata['error']  = implode("<br>",$this->form_validation->error_array());
        }else{
            $jsondata = $this->wabot_model->opActualizar($this->input->post());
        }

        header('Content-Type: application/json');
        echo json_encode($jsondata);
    }

    //Scripts
    public function scripts_list() {
        $data = $this->input->post();

        Header('Content-Type: application/json');
        echo json_encode($this->wabot_model->scripts_list($data));
    }

    public function script_save() {
        $this->load->library('form_validation');
        $id = $this->input->post('id');
		$this->form_validation->set_rules('id_campaign',  	"'Campaña'",  	  'required|integer');
		$this->form_validation->set_rules('nombre',  		"'Nombre'",       'required|min_length[1]');
		$this->form_validation->set_rules('siespera',       "'Si espera'",    'max_length[100]');
		$this->form_validation->set_rules('sibien',  		"'Si bien'",      'max_length[15]');
		$this->form_validation->set_rules('simal',  		"'Si mal'",       'max_length[15]');
        $this->form_validation->set_rules('active',  		"'Activo'",       'integer|in_list[0,1]');
        //VALIDAMOS
		if ($this->form_validation->run() == FALSE){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $r = [
                "error" => $errors[ $fields[0] ]
            ];
		}
		else{
            $active = (empty($this->input->post('active'))) ? 0 : 1;
            $data = [
                'id_campaign'   => $this->input->post('id_campaign'),
                'nombre'        => $this->input->post('nombre'),
                'siespera'      => $this->input->post('siespera'),
                'sibien'        => $this->input->post('sibien'),
                'simal'         => $this->input->post('simal'),
                'active'        => $active,
            ];
            if( $id == 0 )
                $data['created_by'] = $this->udata['id'];
            $r = $this->wabot_model->script_save($id, $data);
            if( $r === FALSE )
                $r = ['error' => 'Error, No se pudo agregar el regisrtro'];
        } 

        Header('Content-Type: application/json');
        echo(json_encode($r));
    }

    public function script_delete() {
        $id = $this->input->post('id');
        $r = $this->wabot_model->script_delete($id);

        Header('Content-Type: application/json');
        echo(json_encode($r));
    }

    public function script_delete_step() {
        $id = (int) $this->input->post('id');
        $r = $this->wabot_model->script_delete_step($id);
        if( $r['success'] === true ) 
            $r = $r['msg'];
        else
            $r = ['error' => $r['msg'] ];

        Header('Content-Type: application/json');
        echo(json_encode($r));
    }

    public function script_step_save() {
        $list_camp = 'permanent,temporal,secure,file';
        $paso   = $this->input->post('paso');
        $this->load->library('form_validation');
		$this->form_validation->set_rules('id',  		            "'ID'",  	                'required|integer');
		$this->form_validation->set_rules('paso',  		            "'Paso'",  	                'required|in_list[borravar,mensaje,pasavar,redir,request,variable]');
		$this->form_validation->set_rules('orden',  		        "'Orden'",                  'integer');
		$this->form_validation->set_rules('id_whatsapp_bot_script', "'Whatsapp Bot Script'",    'required|integer');
		$this->form_validation->set_rules('active',  		        "'Activo'",                 'integer|in_list[0,1]');
        if( $paso == 'mensaje' ) {
            $this->form_validation->set_rules('cond',  	"'Condición'",      'max_length[254]');
        }
        if( $paso == 'request' ) {
            $this->form_validation->set_rules('camp',  	"'Tipo de campo'",  'in_list['.$list_camp.']');
            $this->form_validation->set_rules('varb',  	"'Variable'",       'max_length[64]');
            $this->form_validation->set_rules('tipo',  	"'Request'",        'integer');
            $this->form_validation->set_rules('cond',  	"'Condición'",      'max_length[254]');
        }
        if( $paso == 'pasavar' ) {
            $this->form_validation->set_rules('camp',  	"'Tipo de campo'",  'in_list['.$list_camp.']');
            $this->form_validation->set_rules('varb',  	"'Variable'",       'max_length[64]');
            $this->form_validation->set_rules('tipo',  	"'Tipo de dato'",   'in_list[string,bool,int]');
            $this->form_validation->set_rules('cond',  	"'Condición'",      'max_length[250]');
        }
        if( $paso == 'variable' ) {
            $this->form_validation->set_rules('camp',  	"'Tipo de campo'",  'in_list['.$list_camp.']');
            $this->form_validation->set_rules('varb',  	"'Variable'",       'max_length[64]');
            $this->form_validation->set_rules('tipo',  	"'Tipo de dato'",   'in_list[string,bool,int]');
            $this->form_validation->set_rules('cond',  	"'Condición'",      'max_length[254]');
        }
        if( $paso == 'redir' ) {
            $this->form_validation->set_rules('cond',  	"'Condición'",      'max_length[250]');
        }
        if( $paso == 'borravar' ) {
            $this->form_validation->set_rules('camp',  	"'Tipo de campo'",  'in_list['.$list_camp.']');
        }
        //VALIDAMOS
		if ($this->form_validation->run() == FALSE){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $response = [
                "error" => $errors[ $fields[0] ]
            ];
		}
		else{
            $id     = (int) $this->input->post('id');
            $active = (empty($this->input->post('active'))) ? 0 : 1;
            $data = [
                'id_whatsapp_bot_script'=> $this->input->post('id_whatsapp_bot_script'),
                'paso'                  => $this->input->post('paso'),
                'camp'                  => ($this->input->post('camp') == NULL ) ? '' : $this->input->post('camp'),
                'varb'                  => ($this->input->post('varb') == NULL ) ? '' : $this->input->post('varb'),
                'tipo'                  => ($this->input->post('tipo') == NULL ) ? '' : $this->input->post('tipo'),
                'modi'                  => ($this->input->post('modi') == NULL ) ? '' : $this->input->post('modi'),
                'cond'                  => ($this->input->post('cond') == NULL ) ? '' : $this->input->post('cond'),
                'orden'                 => $this->input->post('orden'),
                'active'                => $active,
                'lastupd'               => date("Y-m-d H:i:s"),
                'lastusr'               => $this->udata['id'],
            ];
            $response = $this->wabot_model->step_save($id, $data);
		}

        Header('Content-Type: application/json');
        echo json_encode($response);
    }
}
