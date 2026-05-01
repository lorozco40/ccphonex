<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Generales extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model('generales_model');
    }

    public function index() {
        $data['title'] = 'Generales';
        // $data['jscript'] = 'config/generales';
        // $params        = array("consola", "sistema", "whatsapp", "sms");
        // foreach ($params as $param) {
        //     $data['data'][$param] = $this->datos_model->getParamsFull($param);
        // }

        $this->armado->mostrar(array(
            'view' => 'config/generales',
            'data' => $data,
        ));
    }

    public function guardar() {
        if ($this->generales_model->guardar($this->input->post())) {
            $this->session->set_flashdata('infomsg', 'Datos guardados.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al guardar.');
        }
        redirect('generales');
    }

    public function emergencia() {
        $this->armado->mostrar(array(
            'data' => array('title' => 'Emergencia'),
            'view' => 'config/emergencia'
        ));
    }

    public function emergencia_ejecutar() {
        ignore_user_abort(true);
        set_time_limit(0);
        if (file_exists("emer.log")) {
            unlink("emer.log");
        }

        $data = $this->generales_model->emergencia();
        $data = (empty($data)) ? "Error" : $data;

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function horarios() {
        $query = $this->db->query("SELECT id from campaign");
        $camps = $query->result();
        foreach ($camps as $key => $camp) {
            $query = $this->db->query("SELECT id from campaign_hour where id_campaign = '$camp->id' limit 1");
            if ($query->num_rows() == 0) {
                $this->db->query("INSERT into campaign_hour (`id_campaign`,`dia`,`inicio`,`fin`) values
                    ('$camp->id', '1', null, null),
                    ('$camp->id', '2', '09:00:00', '17:59:59'),
                    ('$camp->id', '3', '09:00:00', '17:59:59'),
                    ('$camp->id', '4', '09:00:00', '17:59:59'),
                    ('$camp->id', '5', '09:00:00', '17:59:59'),
                    ('$camp->id', '6', '09:00:00', '17:59:59'),
                    ('$camp->id', '7', null, null)
                ");
                echo "<p>campaña $camp->id horarios</p>";
            } else {
                echo "<p>campaña $camp->id ya tenía</p>";
            }
        }
    }

    public function llamadas() {
        $llamadas = $this->datos_model->llamadas();
        dd($llamadas);
    }

    public function llamadascdr() {
        return $this->generales_model->llamadascdr();
    }

    public function amireader() {
        $this->load->view('config/amireader');
    }

}

?>
