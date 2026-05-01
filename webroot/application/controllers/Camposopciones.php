<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Camposopciones extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model('camposopciones_model');
    }

    public function index() {
        $data['cds']     = $this->camposopciones_model->get_branch($this->udata['campanas']);
        $data['campanas']   = $this->datos_model->getCampanas();
        $data['title']   = 'Campos dependientes';
        $data['jscript'] = 'camposopciones';
        $datos = array(
            'data' => $data,
            'view' => 'camposopciones',
        );
        $this->armado->mostrar($datos);
    }

    public function eliminar() {
        $res = $this->camposopciones_model->guardar($this->input->post());

        if (empty($res['error'])) {
            $this->session->set_flashdata('infomsg', 'Cambios aplicados con éxito.');
        } else {
            $this->session->set_flashdata('errormsg', $res['error']);
        }
        redirect("camposopciones");
    }

    public function guardar() {
        $success = false;
        $mensaje   = "";
        $res = $this->camposopciones_model->guardar($this->input->post());

        if ( empty($res['error']) ) {
            $success = true;
            $mensaje = "Cambios aplicados con éxito.";
        } else {
            $mensaje = $res['error'];
        }

        $jsondata = [];
        $jsondata["success"] = $success;
        $jsondata["mensaje"] = $mensaje;

        header('Content-Type: application/json');
        echo json_encode($jsondata);
    }

    public function buscar() {
        $data = $this->input->post(null, true);
        $campanas = !empty($data['campanas']) ? $data['campanas'] : $this->udata['campanas'];
        $cds  = $this->camposopciones_model->get_branch($campanas);

        $jsondata        = [];
        $jsondata["cds"] = $cds;

        header('Content-Type: application/json');
        echo json_encode($jsondata);
    }

    public function actualizar() {
        $data = $this->input->post();
        $res  = $this->camposopciones_model->actualizar($data);

        if ( empty($res['error']) ) {
            $success = true;
            $mensaje = "Cambios aplicados con éxito.";
        } else {
            $mensaje = $res['error'];
        }

        $jsondata = [];
        $jsondata["success"] = $success;
        $jsondata["mensaje"] = $mensaje;

        header('Content-Type: application/json');
        echo json_encode($jsondata);
    }

}

?>
