<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* Procesador de Datos Externos */
class Pde extends CI_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $data = $this->input->get();
        $this->session->sess_destroy();
        redirect('pde/sli?u='.$data['u'].'&token='.$data['token']);
    }

    public function sli() {
        $data = $this->input->get();
        if (isset($data['u']) && isset($data['token'])) {
            if (is_numeric($data['u']) && (int)$data['u'] > 1) {
                $this->load->model('usuario_model');
                $pagini = $this->usuario_model->checkautolog($data['u'], $data['token']);
                if ($pagini) {
                    $nuevosdatos = array('uid' => $data['u']);
                    $this->db->query("DELETE from `ses_ab` where `uid`=?", array($data['u']));
                    $this->session->set_userdata($nuevosdatos);
                    $this->datos_model->user_activity();
                    redirect($pagini);
                } else {
                    die('Intento de logueo prohibido, al tercer intento se bloquea la ip.');
                }
            }
        }
        die ("Hola!?");
    }

    public function gtwa($token='') { // Gnosis Two Way Access
        if(empty($token)) show_404();
        $url = 'https://onitri2.intelligo.com.mx/info_cliente'; // pruebas
        // $url = 'http://onitri2.crmgnosis.mx/info_cliente'; // producción
        $rec = doReq(["url"=>$url,"body"=>["token"=>$token]]);
        if ($rec['error']) {
            debug_log($rec, 'pde->gtwa');
            die('Error54223 consulta con soporte técnico');
        }
        $info = json_decode($rec['data']);
        $pass = "";
        if (!empty($info->data->usuario->password)) {
            $pass = $info->data->usuario->password;
            unset($info->data->usuario->password);
        }
        $this->load->model("pde_model");
        if (!$this->pde_model->gtwaSave($url, $token, $rec['data'])) die('Error 54223, consulta con soporte técnico.'); // SAVE to DB (22 = indice de v en el alfabeto)
        if(isset($info->error)) die($info->error);
        if (empty($info->data->usuario->username) || empty($pass) || empty($info->data->usuario->uid)) {
            die('Información incompleta o incorrecta');
        }
        $info->data->contacto->telefono = '';
        if(!empty($info->data->contacto->numero_movil)) $info->data->contacto->telefono = $info->data->contacto->numero_movil;
        if (count($info->data->contacto->numeros_fijos)>=1) $info->data->contacto->telefono = $info->data->contacto->numeros_fijos[0];
        $pasar = $info->data->contacto;
        unset($pasar->organizacion_id);
        unset($pasar->usuario_id);
        unset($pasar->cliente_id);
        unset($pasar->tipo_cliente);
        unset($pasar->domicilio_id);
        unset($pasar->visita_id);
        if($this->session->userdata('uid') && $this->session->userdata('uid') == $info->data->usuario->uid) {
            $this->session->set_flashdata('contacto', $pasar);
            redirect('consola');
        }
        $this->load->model('usuario_model');
        $val = $this->usuario_model->valida_usuario($info->data->usuario->username, $pass);
        if ($val && $val->id == $info->data->usuario->uid) {
            $nuevosdatos = array('uid' => $val->id);
            $this->db->query("DELETE from `ses_ab` where `uid`=?", array($val->id));
            $this->session->set_userdata($nuevosdatos);
            $this->datos_model->user_activity();
            $this->session->set_flashdata('contacto', $pasar);
            redirect('consola');
        }
        die("Credenciales incorrectas!");
    }

}

?>
