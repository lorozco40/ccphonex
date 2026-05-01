<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendario extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $this->load->library('pagination');
        $this->load->model('calendario_model');
        $data['pagination'] = $this->pagination->create_links();
        $data['agentes']    = $this->datos_model->getRelUsers(); //Envia los agentes a calendarizar de configurar en los select
        $data['data']       = $this->calendario_model->traecalendario();
        foreach ($data['agentes'] as $key => $value) {
            $data['agentes2'][$value->id] = $value->name;
        }

        $data['title'] = 'Calendario';
        $datos = array(
            'view' => 'config/calendario',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

    //Crea calendario desde configuracion calendario/crear
    public function crear() {
        if (empty($this->input->post('agentes'))) {
            $this->session->set_flashdata('errormsg', 'Debes seleccionar un agente.');
        } else {
            $this->load->model('calendario_model');
            if ($this->calendario_model->create($this->input->post())) {
                $this->session->set_flashdata('infomsg', 'Calendarización éxitosa.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error al crear la cita.');
            }
        }
        redirect('calendario');
    }

    //Modifica calenadario reagenda ó cancela desde configuracion calendario/modificar
    public function modificar() {
        $this->form_validation->set_rules('id', 'ID', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'No puedes actualizar la calendarización.');
        }else {
            $this->load->model('calendario_model');
            if (null !== $this->input->post('reagendar')) {
                if ($this->calendario_model->reagendar($this->input->post())) {
                    $this->session->set_flashdata('infomsg', 'Calendarización actualizada.');
                }else {
                    $this->session->set_flashdata('errormsg'. 'Error al actualizar.');
                }
            } else {
                if ($this->calendario_model->cancelar($this->input->post())) {
                    $this->session->set_flashdata('infomsg', 'Calendarización actualizada.');
                }else {
                    $this->session->set_flashdata('errormsg'. 'Error al actualizar.');
                }
            }
        }
        redirect('calendario');
    }

    //Envia los datos a la consola de calendario
    public function traer_calendario() {
        $this->load->model('calendario_model');
        $data = $this->calendario_model->traecalendario($this->input->post('uid'));

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    //Modifica calenadario reagenda ó cancela desde consola calendario/umodificar
    public function umodificar() {
        $this->load->model('calendario_model');
        if (null !== $this->input->post('reagendar')) {
            $data = $this->calendario_model->reagendar($this->input->post());
        } elseif(null !== $this->input->post('cancelar')) {
            $data = $this->calendario_model->cancelar($this->input->post());
        } elseif(null !== $this->input->post('terminar')) {
            $data = $this->calendario_model->terminar($this->input->post());
        } else {
            $data = false;
        }

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    //FUNCIONA PARA CREAR CALENDARIO DESDE MODAL
    public function creacalendar() {
        $this->load->model('calendario_model');
        $data = $this->calendario_model->create($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function reporte_eventos() {
        $data['title']   = 'Reporte eventos calendario';
        $data['cual']    = 'Reporte eventos';
        $data['modelo']  = 'calendario';
        $data['jscript'] = 'reportes/reporte';
        $data['massel']['Estatus'] = [
            (object)['id'=>'0','name'=>'Todos'],
            (object)['id'=>'Activo','name'=>'Activo'],
            (object)['id'=>'Cancelado','name'=>'Cancelado'],
            (object)['id'=>'Reagendado','name'=>'Reagendado'],
            (object)['id'=>'Terminado','name'=>'Terminado'],
        ];
        $datos          = array(
          'view' => 'reportes/reporte',
          'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

}

?>
