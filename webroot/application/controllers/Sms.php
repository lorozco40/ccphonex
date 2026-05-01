<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class Sms extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model("sms_model");
    }

    public function index() {
        $data['title']       = 'SMS Config';
        $data['page']        = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data                = $this->sms_model->get_camps($data);

        $pagi                = $this->pagi;
        $pagi['base_url']    = site_url('sms');
        $pagi['total_rows']  = $data['cuenta'];
        $pagi['uri_segment'] = 2;
        $this->load->library('pagination', $pagi);
        $data['pagination']  = $this->pagination->create_links();

        $this->armado->mostrar(array(
            'view' => 'config/sms',
            'data' => $data,
        ));
    }

    public function subir_csv() {
        $data['upload_path']   = '/var/www/upload/';
        $data['allowed_types'] = 'csv';
		$data['file_name']     = md5(uniqid(rand(), true)).'.csv';
        $this->load->library('upload', $data);

        if (!$this->upload->do_upload('csv')) {
            $respu["status"] = "error";
            $respu["msg"] = $this->upload->display_errors();
		} else {
            $this->load->library("csvreader");
            $respu = $this->sms_model->cargar_csv($data['upload_path'].$data['file_name'], $this->input->post());
        }

        unlink($data['upload_path'].$data['file_name']);

        Header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function enviar() {
        //Validamos datos de entrada
        $this->form_validation->set_rules('num',    "'Número telefónico'",  'required');
        $this->form_validation->set_rules('msg',    "'Mensaje'",            'required|max_length[160]');
        if ($this->form_validation->run() == FALSE){
            $errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $respu = [
                "action"=>"danger",
                "msg" => $errors[ $fields[0] ]
            ];
        } else {
            $lnum  = preg_replace("/[^0-9]/", "", $this->input->post("num"));
            $nop = array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ");
            $sip = array ("a","e","i","o","u","A","E","I","O","U","n","N");
            $ltxt  = str_replace($nop, $sip, $this->input->post("msg"));
            $ltxt  = preg_replace("/[^A-Za-z0-9% .:_?=,\/\-$@#]/", "", $ltxt);
            $envio = $this->enviarSMS($lnum, $ltxt);
            $params = (object) [
                'msg' => $ltxt,
                'id_user' => $this->session->userdata('uid'),
                'resp' => json_encode($envio),
                'uid' => $envio->message_id,
                'phone' => (strlen($lnum) == 10) ? '52'.$lnum : $lnum,
                'status' => '',//$envio->resp,
                'status_desc' => ''//$envio->description,
            ];
            if ($this->sms_model->finalizar($params)) {
                $respu["action"] = "success";
                $respu["msg"] = "Procesando SMS, gracias.";
            }
        }

        Header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function pnueva() {
        // Validamos los datos de entrada
        $this->form_validation->set_rules('id_campaign',    "'Campaña'",            'required|integer');
        $this->form_validation->set_rules('name',           "'Nombre Plantilla'",   'required|max_length[100]');
        $this->form_validation->set_rules('valor',          "'Texto Plantilla'",    'required|max_length[160]');
        if ($this->form_validation->run() == FALSE){
            $errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $respu = [
                "action"=>"danger",
                "msg" => $errors[ $fields[0] ]
            ];
        } else {
            if ($insertid = $this->sms_model->plantilla_crear($this->input->post())) {
                $respu["id"] = $insertid;
                $respu["action"] = "success";
                $respu["msg"] = "Plantilla agregada con éxito.";
            } else {
                $respu = [
                    "action"=>"danger",
                    "msg" => 'Error: no se pudo agregar la plantilla.'
                ];
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function pactu() {
        // Validamos los datos de entrada
        $this->form_validation->set_rules('id',     "'ID'",             'required|integer');
        $this->form_validation->set_rules('valor',  "'Texto Plantilla'",'required|max_length[160]');
        if ($this->form_validation->run() == FALSE){
            $errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $respu = [
                "action"=>"danger",
                "msg" => $errors[ $fields[0] ]
            ];
        } else {
            if ($this->sms_model->plantilla_actualizar($this->input->post())) {
                $respu["action"] = "success";
                $respu["msg"] = "Plantilla actualizada con éxito.";
            } else {
                $respu = [
                    "action"=>"danger",
                    "msg" => 'Error: No se pudo actualizar la plantilla.'
                ];
            }
        }

        Header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function pborrar() {
        $respu = array("action" => "error", "msg" => "Hubo un error procesando la petición");
        if (!empty(!empty($this->input->post('id')))) {
            if ($this->sms_model->plantilla_borrar($this->input->post('id'))) {
                $respu["action"] = "success";
                $respu["msg"] = "Plantilla borrada con éxito.";
            }
        }
        Header('Content-Type: application/json');
        echo json_encode($respu);
    }

    private function enviarSms($num, $msg) {
        $sms = $this->datos_model->getParams('sms');
        $data = [
            "to"      => (strlen($num) == 10) ? '52'.$num : $num,
            "message" => $msg,
            "from"    => 'sms',
        ];
        $respu = doReq(['url'=>$sms->server, 'method'=>'POST', 'body'=>$data, 'proto'=>'x-www-form-urlencoded', 'heads'=>['Authorization: Bearer '.$sms->token]]);
        if ($respu["error"] == false) {
            $respu = ["msg"=>"Mensaje SMS enviado", "status"=>"success"];
        } else {
            $respu = ["msg"=>$respu["error"], "status"=>"danger"];
        }

        return $respu;
    }

    public function reporte_detalle() {
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['modelo']  = 'sms';
        $data['title']   = 'SMS detalle';
        $data['jscript'] = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function reporte_indicador() {
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['modelo']  = 'sms';
        $data['title']   = 'SMS Indicadores';
        $data['jscript'] = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function actualiza() {
        $tabla = $this->input->get('tabla');
        if ($tabla!=null) {
            echo $this->sms_model->actualiza($tabla);
        } else {
            echo $this->sms_model->actualiza();
        }
    }

    public function camp_enviar() {
        $this->load->helper('fun_helper');

        Header('Content-Type: application/json');
        echo json_encode($this->sms_model->camp_enviar($this->input->post()));
    }

    public function buscarPlantilla() {
        $respu = $this->sms_model->buscarPlantilla($this->input->post());

        header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function plantillas() {
        $this->load->model("consola_model");
        $data['title']     = 'SMS Plantillas';
        $data['campanas']  = $this->datos_model->getCampanas();
        $data['plantiSms'] = $this->consola_model->getPlantillas();
        $data['jscript']   = 'config/sms_templates';

        $this->armado->mostrar(array(
            'view' => 'config/sms_templates',
            'data' => $data,
        ));
    }

}

?>
