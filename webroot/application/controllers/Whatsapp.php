<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class Whatsapp extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->library('whatsappfun');
        $this->load->model("whatsapp_model");
    }

    public function index() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['cuentas']  = $this->datos_model->getWaCtas(FALSE, FALSE); // activas = FALSE (todas)
        $data['title']    = 'Whatsapp cuentas';
        $data['jscript']  = 'config/whatsapp';
        $this->armado->mostrar([
            'view' => 'config/whatsapp',
            'data' => $data,
        ]);
    }

    public function enviar() {
        $data = $this->input->post();
        $respu = "Sin permisos suficientes";
        if (isset($data['cid']) && $data['cid'] == 0) {
            if (in_array("wa_masivos", $this->udata['permisoSec'])) {
                $respu = $this->whatsapp_model->masivo_inicia($data);
            }
        } else {
            $respu = $this->whatsapp_model->sale_texto($data);
        }

        Header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function enviar_wamedia() {
        $uploadconfig = array(
            'file_ext_tolower' => true,
            'allowed_types'    => 'gif|jpg|jpeg|png|pdf|xls|xlsx|doc|docx|zip|csv|tar|tgz|avi|mp3|mp4|ogg|ogv|3gp|wmv|wma|webm|flv',
            'upload_path'      => FCPATH . 'wafiles/',
            'file_name'        => str_replace('.','',(string)microtime(true))
        );
        if (!is_dir($uploadconfig['upload_path'])) {
            mkdir($uploadconfig['upload_path'], 0777, true);
        }
        $this->load->library('upload', $uploadconfig);
        if (!$this->upload->do_upload('file')) {
            $respu["error"] = $this->upload->display_errors('','');
        } else {
            $data = $this->input->post();
            $data['archivo'] = $uploadconfig['file_name'].$this->upload->data('file_ext');
            $data['ext'] = $this->upload->data('file_ext');
            $data['size'] = $this->upload->data('file_size')*1024;
            $data['mtype'] = $this->upload->data('file_type');
            $respu = $this->whatsapp_model->sale_media($data);
        }
        Header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function auco() {
        Header('Content-Type: application/json');
        echo json_encode($this->whatsapp_model->auco($this->input->post()));
    }

    public function traer_asign() {
        Header('Content-Type: application/json');
        echo json_encode($this->whatsapp_model->traer_asignacion());
    }

    public function traer_data() {
        Header('Content-Type: application/json');
        echo json_encode($this->whatsapp_model->traer_data($this->input->post()));
    }

    function traersesconv() {
        Header('Content-Type: application/json');
        echo json_encode($this->whatsapp_model->traersesconv($this->input->post()));
    }

    public function traer_masivos() {
        Header('Content-Type: application/json');
        echo json_encode($this->whatsapp_model->traer_masivos($this->input->post()));
    }

    public function traer_nuewa() {
        Header('Content-Type: application/json');
        echo json_encode($this->whatsapp_model->traer_nuewa($this->input->post()));
    }

    public function terminases() {
        Header('Content-Type: application/json');
        echo json_encode($this->whatsapp_model->terminases($this->input->post()));
    }

    public function buscacontacto() {
        Header('Content-Type: application/json');
        echo json_encode($this->whatsapp_model->buscacontacto($this->input->post()));
    }

    public function reporte_detalle() {
        $data['agentes']           = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']             = 'Whatsapp detalle';
        $data['modelo']            = 'whatsapp';
        $data['jscript']           = 'reportes/reporte';
        $data['massel']['Cuenta']  = $this->whatsapp_model->mis_wactas();
        $data['aucos'][]           = (object)["lab"=>"Contacto", "nam"=>"wacto",
            "mod"=>"whatsapp", "dep"=>"cuenta", "met"=>"auco"];

        $this->armado->mostrar(array(
            'view' => 'reportes/whatsapp_reporte',
            'data' => $data,
        ));
    }

    public function reporte_indicador() {
        $data['agentes']          = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']            = 'Whatsapp indicador';
        $data['modelo']           = 'whatsapp';
        $data['jscript']          = 'reportes/reporte';
        $data['massel']['Cuenta'] = $this->whatsapp_model->mis_wactas();
        $data['aucos'][]          = (object)["lab"=>"Contacto", "nam"=>"wacto",
            "mod"=>"whatsapp", "dep"=>"cuenta", "met"=>"auco"];

        $datos = array(
            'view' => 'reportes/whatsapp_reporte',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

    public function reporte_sesion() {
        $data['agentes']          = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']            = 'Whatsapp sesion';
        $data['modelo']           = 'whatsapp';
        $data['jscript']          = 'reportes/whatsapp_sesion';
        $data['massel']['Cuenta'] = $this->whatsapp_model->mis_wactas();
        $data['aucos'][]          = (object)["lab"=>"Contacto", "nam"=>"wacto",
            "mod"=>"whatsapp", "dep"=>"cuenta", "met"=>"auco"];

        $this->armado->mostrar(array(
            'view' => 'reportes/whatsapp_sesion',
            'data' => $data,
        ));
    }

    public function reporte_global_sesion() {
        $data['title']            = 'Whatsapp reporte global sesión';
        $data['modelo']           = 'whatsapp';
        $data['jscript']          = 'reportes/reporte';
        $data['massel']['Cuenta'] = $this->whatsapp_model->mis_wactas();

        $this->armado->mostrar(array(
            'view' => 'reportes/whatsapp_reporte',
            'data' => $data,
        ));
    }

    public function transferir(){
        Header('Content-Type: application/json');
        echo json_encode($this->whatsapp_model->transferir($this->input->post()));
    }

    public function estatus() {
        $query = $this->db->query("SELECT * from `whatsapp_cuentas` where `id` = ?", [$this->input->post('wid')]);
        $wacta = $query->row();
        $res = (object)['success'=>false];
        if (empty($wacta->idchatapi)) {
            $query = $this->db->query("SELECT * FROM extapi WHERE id = 2"); // ToDo relacionar en tabla cuentas Whatsapp
            $wabox = $query->row();
            $result = doReq(["url"=>$wabox->url.'status/'.$wabox->user, "body"=>["token"=>$wabox->token]]);
            $result = json_decode($result["data"]);
            if(is_object($result) && !empty($result->success)) {
                $res = $result;
            }
        } else {
            $query = $this->db->query("SELECT * FROM extapi WHERE id = 1"); // ToDo relacionar en tabla cuentas Whatsapp
            $onemsg = $query->row();
            $result = doReq(["url"=>$wabox->url.$onemsg->xhash.'/me', "body"=>["token"=>$wabox->token]]);
            $result = json_decode($result["data"]);
            if(is_object($result) && empty($result->error)) {
                $res = $result;
                $res->success = true;
            }
        }

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function guardarcta() {
        $res = $this->whatsapp_model->guardarCta($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function wa_contactos() {
        Header('Content-Type: application/json');
        echo json_encode($this->whatsapp_model->wa_contactos($this->input->post('wid')));
    }

    public function encyterm(Type $var = null) {
        $data = $this->input->post();
        $uid  = null;
        if (!empty($data['sid'])) {
            $uid = $this->db->query("SELECT id_user FROM whatsapp_session WHERE id = ?",
            [$data['sid']])->row()->id_user;
        }
        $data['razon'] = 'Terminar y encuesta';
        $terminada = $this->whatsapp_model->terminases($data);
        $res = ["tipo"=>"danger", "msg"=>"Error al terminar la sesión"];
        if ($terminada) {
            $this->load->model("warate_model");
            $res = $this->warate_model->encuesta($data['wid'], $data['cid'], false, $uid);
        }
        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function encuesta_reporte_detalle() {
        $data['title']             = 'Encuesta whatsapp detalle';
        $data['modelo']            = 'warate';
        $data['grande']            = true;
        $data['jscript'][]         = 'reportes/reporte';
        $data['jscript'][]         = 'reportes/whatsapp_encuesta';
        $data['massel']['Cuenta']  = $this->whatsapp_model->mis_wactas();
        $data['massel']['Encuesta']= [];
        $data['aucos'][]           = (object)[
            "lab"=>"Contacto", 
            "nam"=>"wacto",
            "mod"=>"whatsapp", 
            "dep"=>"cuenta", 
            "met"=>"auco"
        ];

        $this->armado->mostrar(array(
            'view' => 'reportes/whatsapp_reporte',
            'data' => $data,
        ));
    }

    public function encuesta_reporte_indicadores() {
        $data['title']    = 'Encuesta Whatsapp indicadores';
        $data['extjs']    = "//www.gstatic.com/charts/loader.js";
        $data['jscript'][]    = "jspdf.min";
        $data['jscript'][]  = 'reportes/whatsapp_indicadores';
        $data['cuentas']  = $this->whatsapp_model->mis_wactas();

        $this->armado->mostrar(array(
            'view' => 'reportes/whatsapp_indicador',
            'data' => $data,
        ));
    }

    public function encuesta_indicador_data() {
        $this->load->model("warate_model");
        $data = $this->warate_model->encuesta_indicador_data();

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    // Obtiene las encuestas de una cuenta especifica
    public function get_rate() {
        $this->load->model("warate_model");
        $id_cuenta = (int)$this->input->post('id_cuenta');
        $data = $this->warate_model->select_encuestas_wactas($id_cuenta);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }
}
