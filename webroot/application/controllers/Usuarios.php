<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $data['jscript']  = 'config/user';
        $data['title']    = 'Usuarios';
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['perfiles'] = [
            (object)['value'=>'agente',    'text'=>'Agente'],
            (object)['value'=>'crm',       'text'=>'CRM'],
            (object)['value'=>'supervisor','text'=>'Supervisor'],
            (object)['value'=>'superior',  'text'=>'Superior'],
        ];

        $this->armado->mostrar([
            'data' => $data,
            'view' => 'config/users',
        ]);
    }

    private function _microtime(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public function lista() {
        $time_ini = $this->_microtime();
        $memo_ini=memory_get_peak_usage();
        $pag = (!empty($this->input->get('pag'))) ? $this->input->get('pag') : 0;
        $lim = (!empty($this->input->get('lim'))) ? $this->input->get('lim') : REGS_POR_PAG;
        $bus = (!empty($this->input->get('bus'))) ? $this->input->get('bus') : false;
        $cam = (!empty($this->input->get('cam'))) ? $this->input->get('cam') : "";
        $resp = $this->datos_model->getRelUsers(["pag"=>$pag,"lim"=>$lim,"bus"=>$bus,"cam"=>$cam]);
        $memo_fin=memory_get_peak_usage();
        $resp = (empty($resp)) ? ["regs"=>0,"data"=>[],"pag"=>$pag,"rpp"=>$lim] : $resp;
        $time_fin = $this->_microtime();
        $resp["devdata"]["Mem"] = round(($memo_fin - $memo_ini)/(1024*1024),2). "Mb";
        $resp["devdata"]["Tim"] = round(($time_fin - $time_ini),2). " segs";

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function guardar() {
        $this->form_validation->set_rules('user', 'Email', 'required');
        $this->form_validation->set_rules('name', 'Nombre', 'required');
        $data = $this->input->post();
        if (($data['id']==0 and !in_array('usuarios/crear', $this->udata['permiso'])) ||
            ($data['id']!=0 and !in_array('usuarios/actualizar', $this->udata['permiso']))) {
                $res = ['error' => 'Sin permisos suficientes!'];
        } else if ($this->form_validation->run()==FALSE ||
            ($data['id']==0 && (empty($data['campana'] || empty($data['pass']))))) {
            $res = ['error' => 'Datos no válidos o incompletos.'];
        } else {
            $res = $this->usuario_model->guardar_user($data);
            if (empty($res['error'])) {
                $uid = $res;
                // Se asignan los permisos base al usuario y el perfil agente
                // retorna un array con los permisos asignados y datos de: userData, permisoSec, permisoRepo, permiso 
                $uper = $this->usuario_model->get_permisos($uid);
                $exten = '';
                // Recorremos el resultado del query anterior y obtenemos la extension del usuario
                foreach ($uper as $obj) {
                    if($obj->cat == 'userData' && $obj->data == 'userask') $exten = $obj->val;
                }
                if ($data['id']==0) {
                    $guarda = $this->usuario_model->guardar_udata('campanas', $uid, $data['campana']);
                    if (!empty($guarda['error'])) $res = $guarda;
                }
                if ($data['extension'] != $exten && empty($res['error'])) {
                    $guarda = $this->usuario_model->guardar_udata('userask', $uid, $data['extension']);
                    if (!empty($guarda['error'])) $res = $guarda;
                }
                // Modificamos el perfil del usuario por el perfil proporcionado
                if (!empty($data['perfil']) && empty($res['error'])) {
                    
                    if( $this->udata['perfil'] == 'admin' && in_array($data['perfil'], ['admin', 'agente', 'crm', 'supervisor', 'superior'])
                        || $this->udata['perfil'] != 'admin' && in_array($data['perfil'], ['agente', 'crm', 'supervisor', 'superior'])) {
                        $guarda = $this->usuario_model->guardar_udata('perfil', $uid, $data['perfil']);
                    } else {
                        $guarda = ['error'=>'No puedes asignarle este perfil al usuario.'];
                    }
                    if (!empty($guarda['error'])) $res = $guarda;
                }
                if (empty($data['active'])) $this->usuario_model->desloguear($data['id']);
                if (empty($res['error'])) $res = "Usuario guardado.";
            }
        }

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function permisos($id_user) {
        if (empty($id_user)) {
            $this->session->set_flashdata('infomsg', 'Elige un usuario para modificar.');
            redirect("usuarios");
        }
        if ($id_user==1) {
            $this->session->set_flashdata('errormsg', 'Usuario no modificable');
            redirect("usuarios");
        }
        $data["campanas"]  = $this->datos_model->getCampanas();
        $data["wactas"]    = $this->datos_model->getWaCtas();
        $data["emailctas"] = $this->datos_model->getEmailCtas(true, 2);
        $data['miper']     = $this->udata['perfil'];
        $permisos          = $this->usuario_model->get_permisos($id_user);
        foreach ($permisos as $key => $row) {
            $data[$row->cat][] = $row;
        }
        $data["jscript"]        = "config/user_permiso";
        $data["id_user"]        = $id_user;
        $usuario                = $this->usuario_model->get_by_id($id_user);
        $data["usuario_nombre"] = $usuario->name." ".$usuario->last;
        $data['title']          = "Permisos de Usuario";

        $this->armado->mostrar(array(
            'view' => 'config/user_permisos',
            'data' => $data,
        ));
    }

    public function permisos_guardar() {
        $respu = $this->usuario_model->save_permisos();
        if (empty($respu['error'])) {
            $this->session->set_flashdata('infomsg', 'Permisos guardados.');
        } else {
            $this->session->set_flashdata('errormsg', $respu['error']);
        }
        redirect("usuarios");
    }

    public function misdatos() {
        $data = $this->input->post();
        $data['active'] = 1;
        if (!empty($data['pass'])) {
            $valid_user = $this->usuario_model->valida_usuario($data['user'], $data['oldpass'], 9999);
            if (!$valid_user || ($data['pass']!=$data['confpass'])) {
                $this->session->set_flashdata('errormsg', 'Datos incorrectos, por favor vuelve a intentar.');
                redirect();
            }
        }
        $query = $this->db->query("SELECT id from catalogs where cat='userData' and val='theme'");
        $idcat = $query->row()->id;
        $this->db->query("INSERT INTO user_data (id_user, id_catalog, val) values (?,?,?)
            ON DUPLICATE KEY UPDATE val=?", [$this->session->userdata('uid'), $idcat, $data['tema'], $data['tema']]);
        $guarda = $this->usuario_model->guardar_user($data);
        $ret = (empty($guarda['error'])) ? "ok" : $guarda['error'];

        Header('Content-Type: application/json');
        echo json_encode($ret);
    }

    public function desloguear() {
        $id = $this->input->post("id");
        $respu = $this->usuario_model->desloguear($id);
        $this->load->model("campanas_model");
        $this->campanas_model->elimina_licencias_usuario($id);

        Header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function gentoken() {
        $data = $this->input->post();
        $thisuid = $this->session->userdata('uid');
        if (!empty($data['uid']) && ($this->udata['perfil']=='agente' || $this->udata['perfil']=='crm') && $data['uid']!=$thisuid) {
            $res = ['error'=>'No autorizado'];
        } else {
            $uid = (!empty($data['uid'])) ? $data['uid'] : $thisuid;
            $res = ($this->usuario_model->savetoken($uid)) ? $res : ['error'=>'No existe el catálogo'];
        }

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

}

?>
