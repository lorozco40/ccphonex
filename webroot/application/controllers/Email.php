<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model("email_model");
    }

    public function index() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['jscript']  = 'config/email';
        $data['title']    = 'Cuentas email';

        $this->armado->mostrar([
            'data' => $data,
            'view' => 'config/email',
        ]);
    }

    public function savecta() {
        $data = $this->input->post();
        $meth = ($data['id']==0) ? 'POST' : 'PUT';
        $data['uid'] = $this->udata['id'];
        $data['in_pass'] = (!empty($data['in_pass'])) ? esconde($data['in_pass'], $data['email']) : '';
        $data['out_pass'] = (!empty($data['out_pass'])) ? esconde($data['out_pass'], $data['email']) : '';
        $resp = doReq(["url"=>getenv('BAGO_URL')."email/cuenta", "method"=>$meth, "body"=>$data, "nossl"=>true]);

        Header('Content-Type: application/json');
        echo $resp["data"];
    }

    public function reporte_detalle() {
        $senders         = $this->email_model->massel_senders();
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']   = 'Email detalle';
        $data['modelo']  = 'email';
        $data['grande']  = true;
        $data['jscript'] = 'reportes/reporte';
        $data['massel']['De'][] = (object)['id'=>'','name'=>'Todos'];
        foreach($senders as $row)
            $data['massel']['De'][] = (object)['id'=>$row->sender,'name'=>$row->sender];

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function reporte_indicador() {
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']   = 'Email indicadores';
        $data['modelo']  = 'email';
        $data['jscript'] = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function actu() {
        $data = $this->input->post();
        $data['uid'] = $this->udata['id'];
        $resp = doReq(["url"=>getenv('BAGO_URL')."email/entrada", "method"=>"PUT", "body"=>$data, "nossl"=>true]);

        Header('Content-Type: application/json');
        echo $resp["data"];
    }

    public function reemail() { // Establece la hora en que el operador comienza a contestar un email.
        if (null !== $this->input->post('ini')) {
            $this->email_model->reemail($this->input->post('ini'));
        }
        return true;
    }

    public function enviar() {
        Header('Content-Type: application/json');
        $data = $this->input->post();
        $this->load->model("email_model");
        $emcta = $this->email_model->getCta($data["id_cuenta"]);
        if (empty($emcta)){
            echo json_encode(["error"=>"No tiene cuenta de email asignada"]);
            die();
        }
        if( $this->input->post('id') != 0 ){ //En caso de que el correo no sea nuevo
            $email_entry = $this->email_model->findEmailEntry($data['id']);
            if( $email_entry->id_user != $this->udata['id'] ){//Validamos que el usuario aun tenga asignado este correo
                echo json_encode(["error"=>"Error: Este correo ya fue asignado a otra persona"]);
                die();
            }
        }
        $data["file"] = [];
        if ( !empty($_FILES['attachment']) ) {
            $uploadconfig = array(
                'file_ext_tolower' => true,
                'allowed_types'    => 'bmp|tiff|gif|jpg|jpeg|png|pdf|ppt|pptx|xls|xlsx|doc|docx|csv|zip|tar|gz|tgz|aac|avi|mp3|mp4|oga|ogg|ogv|3gp|wmv|wma|webm|flv',
                'upload_path'      => FCPATH . 'emailfiles/'.$data['id_cuenta'].'/',
            );
            if (!is_dir($uploadconfig['upload_path'])) {
                mkdir($uploadconfig['upload_path'], 0777, true);
            }
            $this->load->library('upload', $uploadconfig);
            $Files = $_FILES['attachment'];
            foreach($Files['name'] as $key => $filename) {
                $uploadconfig["file_name"] = str_replace('.','',(string)microtime(true));
                $this->upload->initialize($uploadconfig, true);

                $_FILES['attachment']['name'] = $Files['name'][$key];
                $_FILES['attachment']['type'] = $Files['type'][$key];
                $_FILES['attachment']['tmp_name'] = $Files['tmp_name'][$key];
                $_FILES['attachment']['error'] = $Files['error'][$key];
                $_FILES['attachment']['size'] = $Files['size'][$key];
                if (!$this->upload->do_upload('attachment')) {
                    echo json_encode(["error"=>$this->upload->display_errors().$Files['name'][$key]]);
                    die();
                }
                $data["file"][] = $this->upload->data('file_name');
            }
        }
        if (!empty($data["existingFilesAtachList"])) {
            $data["file"] = array_merge($data["file"], explode(",", $data["existingFilesAtachList"]));
        }

        $lapas = encuentra($emcta->out_pass, $emcta->email);
        $emailconfig = array(
            'protocol'     => 'smtp',
            'smtp_host'    => $emcta->out_servidor,
            'smtp_port'    => $emcta->out_puerto,
            'smtp_user'    => $emcta->out_user,
            'smtp_pass'    => $lapas,
            'smtp_timeout' => 5,
            'mailtype'     => 'html',
            'charset'      => 'utf-8',
            'newline'      => "\r\n"
        );
        if (!empty($emcta->out_seguridad)) {
            $emailconfig['smtp_crypto'] = $emcta->out_seguridad;
        }
        $this->load->library("email");
        $this->email->initialize($emailconfig);

        $this->email->from($emcta->email, $emcta->nombre);
        $to = $this->divideTo($data["to"]);
        $cc = !empty($data["cc"]) ? $this->divideTo($data["cc"]) : [];
        $cco = !empty($data["cco"]) ? $this->divideTo($data["cco"]) : [];
        $this->email->to($to);
        if( count($cc) > 0 ) $this->email->cc($cc);
        if( count($cco) > 0 ) $this->email->bcc($cco);
        $this->email->subject($data["subject"]);
        $data["body"] .= signature_email($emcta->signature_text, $emcta->signature_img, $emcta->id);
        $this->email->message($data["body"]);
        if ( count($data["file"]) > 0 ) {
            foreach ($data["file"] as $key => $file) {
                $this->email->attach(FCPATH . 'emailfiles/'.$data['id_cuenta'].'/'.$file);
            }
        }
        if ($this->email->send()){
            $respu["msg"] = "Mensaje enviado.";
            $this->load->model('consola_model');
            if (!empty($data["id"])) {
                $this->email_model->cerrarEmail($data["id"]);
            }
            $data["file"] = implode(", ",$data["file"]);
            $this->email_model->guardarSaliente($data, $emcta);
            $this->datos_model->user_activity('Email enviado a '.implode(",", $to));
        } else {
            $fp = fopen(FCPATH . 'cron.log', 'a');
            fwrite($fp, $this->email->print_debugger(array('headers')));
            fclose($fp);
            $respu["error"] = "Se encontró un error al enviar el correo, intenta nuevamente.";
        }

        echo json_encode($respu);
    }

    private function divideTo($data) {
        $data = str_replace(' ', '', $data);
        $data = explode(",", $data);
        foreach ($data as $key => $value) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    public function asignacion() {
        $this->load->model("email_model");
        $data['usuarios'] = $this->datos_model->getRelUsers();
        $data['senders']  = $this->email_model->obtenerSenders();
        $data['jscript']  = 'config/email_administracion';
        $data['title']    = ' Asignación de Email';

        $this->armado->mostrar([
            'data' => $data,
            'view' => 'config/email_administracion',
        ]);
    }

    public function listarCorreos() {
        $data = $this->input->post();
        $this->load->model("email_model");
        $data = $this->email_model->listarCorreos($data);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function verCorreo(){
        $data = $this->input->post();
        $this->load->model("email_model");
        $data = $this->email_model->verCorreo($data);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function reasignarAgente() {
        $this->load->model("email_model");
        $data = $this->input->post();
        $error = '';
        //obtenemos los datos del correo
        $email_entry = $this->email_model->findEmailEntry($data['id']);
        //Validamos que ese correo no haya sido respondido aun
        if( $email_entry->datetime_reply != null )
            $error = 'Error: este correo ya se ha respondido, no puede ser reasignado';
        //Validamos que no se le este reasignando a la misma persona
        if( $error == '' && $data['id_user'] == $email_entry->id_user )
            $error = 'No puedes reasignar el correo a la misma persona';
        //guardamos el id_user y tranfer
        if( $error == '')
            if( $this->email_model->reasignarAgente($data['id'], $data['id_user'], $email_entry->id_user )  === false )
                $error = 'Ocurrio un error al intentar reasignar el agente';
        if( $error == '' )
            $data = 'Se realizo la reasignacion correctamente ';
        else
            $data['error'] = $error;

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function asignarFormulario() {
        $jsondata = [];
        $this->form_validation->set_rules('in_tipo', 'Formulario', 'required');
        $this->form_validation->set_rules('id', 'Identificador', 'required');
        if ($this->form_validation->run()==FALSE) {
            $jsondata['error']  = implode("<br>",$this->form_validation->error_array());
        } else {
            $data = $this->input->post();

            $this->load->model("email_model");

            $jsondata = $this->email_model->asignarFormulario($data);
        }

        header('Content-Type: application/json');
        echo json_encode($jsondata);
    }

    public function buscarInTipoCuenta() {
        $jsondata = [];
        $this->form_validation->set_rules('id', 'Identificador', 'required');
        if ($this->form_validation->run()==FALSE) {
            $jsondata['error']  = implode("<br>",$this->form_validation->error_array());
        } else {
            $id = $this->input->post("id");

            $this->load->model("email_model");

            $query = $this->email_model->getCta($id);
            if( isset($query) ){
                $jsondata["id_form"] = $query->in_tipo;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($jsondata);
    }
}
