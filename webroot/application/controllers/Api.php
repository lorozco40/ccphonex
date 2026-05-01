<?php
// defined('BASEPATH') OR exit('No direct script access allowed');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
use Restserver\Libraries\REST_Controller;

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Api extends REST_Controller
{

    private $incheads;

    public function __construct() {
        parent::__construct();
        $this->incheads = getallheaders();
    }

    public function serve_post() {
        // gateway para Wabox
        if(empty($this->post()) ||
            (empty($this->post('uid')) && empty($this->post('instanceId')))
        ) return;
        if (empty($this->post('uid'))) {
            $tok = $this->post('instanceId');
            $url = "/api/chatapi";
        } else {
            $tok = $this->post('uid');
            $url = "/api/wabox";
        }
        $this->load->model("whatsapp_model");
        $ip = $this->whatsapp_model->getWaDestIp($tok);
        if (!empty($ip)) {
            $toupd['id'] = $this->whatsapp_model->gwlog($this->post(), $ip);
            $resp = doReq(["url"=>"https://".$ip.$url, "method"=>"POST", "body"=>$this->post(), "nossl"=>true]);
            $toupd['resp'] = $resp["data"];
            $this->whatsapp_model->updResp($toupd);
            $message = array(
                'success' => true,
                'message' => 'Datos recibidos'
            );
            $this->set_response($message, REST_Controller::HTTP_CREATED); // (201)
        } else {
            $message = array(
                'success' => false,
                'message' => 'Token no valido'
            );
            $this->set_response($message, REST_Controller::HTTP_UNAUTHORIZED); // (401)
        }
    }

    public function ping_get() {
        if ($this->validaheads()) {
            $this->set_response("pong", REST_Controller::HTTP_CREATED);
        }
    }

    public function db1ee2xfiri_get() {
        // desde bago 1 enviar email 2 x form id registro id
        if ($this->validaheads()) {
            $this->load->model('crm_model');
            $res = $this->crm_model->informar($this->input->get());
        }
    }

    public function gimejson_get() {
        // t = tabla, c = campos (sin espacios, separados por comas), i = id, iv = id valor
        $res = $this->datos_model->gimejson($this->input->get());
        $this->set_response($res, REST_Controller::HTTP_OK);
    }

    public function wabox_post() {
        // Mensaje directo de wabox para cuenta en éste servidor
        $this->load->library("whatsappfun");
        if(empty($this->post()) || empty($this->post('uid'))) {
            $this->set_response("Sin permiso", REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        $this->load->model("whatsapp_model");
        $wac = $this->whatsapp_model->get_cuenta($this->post('uid'));
        if (!empty($wac)) {
            $this->whatsapp_model->save_wabox($this->post(), $wac);
            $message = array(
                'success' => true,
                'message' => 'Datos recibidos'
            );
            $this->set_response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = array(
                'success' => false,
                'message' => 'Token no valido'
            );
            $this->set_response($message, REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function chatapi_post() {
        $this->load->library("whatsappfun");
        if(empty($this->post()) || empty($this->post('instanceId'))) {
            $this->set_response("Sin permiso", REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        $this->load->model("whatsapp_model");
        $wac = $this->whatsapp_model->get_cuenta($this->post('instanceId'));
        if (!empty($wac)) {
            $this->whatsapp_model->save_chatapi($this->post(), $wac);
            $message = array(
                'success' => true,
                'message' => 'Datos recibidos'
            );
            $this->set_response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = array(
                'success' => false,
                'message' => 'Token no valido'
            );
            $this->set_response($message, REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function vc_post() {
        $data = $this->post();
        $per = $this->getPermisos($data);
        if ($per) {
            $a = $data['a'];
            $aba = ['getChans', 'getRepo'];
            if (in_array($a, $aba)) {
                unset($data['u']);
                unset($data['token']);
                unset($data['a']);
                $data['campana'] = $per['campanas'];
                $this->load->model("videollamada_model");
                $res = $this->videollamada_model->$a($data);
                if ($res) {
                    $this->set_response($res, REST_Controller::HTTP_OK);
                }
            } else {
                $this->set_response(['success'=>false, 'msg'=>'Sin permiso'], REST_Controller::HTTP_UNAUTHORIZED);
            }
        }
    }

    public function user_post() {
        if (!$this->validaheads()) {
            return;
        }
        $data = $this->post();
        $per = $this->getPermisos($data);
        if ($per) {
            $this->load->model("usuario_model");
            $a = $data['a'];
            switch ($a) {
                case 'setPars':
                    if (empty($data['id']) || $data['id']<6) {
                        $this->set_response(['success'=>false, 'msg'=>'Intento de cambio prohibido']);
                        return;
                    }
                    $usacam = $this->usuario_model->get_by_id($data['id']);
                    if ($usacam) {
                        $sicami = array_intersect(explode(',',$usacam->udata['campanas']), explode(',',$per['campanas']));
                        if (count($sicami)<1) {
                            $this->set_response(['success'=>false, 'msg'=>'Usuario desconocido']);
                            return;
                        }
                        if ($usacam->udata['perfil'] == 'admin') {
                            $this->set_response(['success'=>false, 'msg'=>'Intento de cambio prohibido']);
                            return;
                        }
                    } else {
                        $this->set_response(['success'=>false, 'msg'=>'Usuario desconocido']);
                        return;
                    }
                    $usacam = json_decode(json_encode($usacam), true);
                    $res = $this->usuario_model->$a($data, $per, $usacam);
                    $this->set_response($res, REST_Controller::HTTP_OK);
                    break;
                case 'list':
                    $res = $this->usuario_model->$a($data, $per);
                    $this->set_response($res, REST_Controller::HTTP_OK);
                    break;
                case 'status':
                    $res = $this->usuario_model->$a($per['campanas']);
                    $this->set_response($res, REST_Controller::HTTP_OK);
                    break;

                default:
                    $this->set_response(['success'=>false, 'msg'=>'Sin permiso'], REST_Controller::HTTP_UNAUTHORIZED);
                    break;
            }
        }
        return;
    }

    private function validaheads() {
        // $myFile = "requestslog.txt";
        //     $fh = fopen($myFile, 'a') or die("can't open file");
        //     fwrite($fh, "\n---------------------------------------------------------------\n");
        //     fwrite($fh, "REQUEST_METHOD = $_SERVER[REQUEST_METHOD]\n");
        //     fwrite($fh, "QUERY_STRING = $_SERVER[QUERY_STRING]\n");
        //     fwrite($fh, "REQUEST_URI = $_SERVER[REQUEST_URI]\n");
        //     fwrite($fh, "REQUEST_TIME = $_SERVER[REQUEST_TIME]\n");
        //     fwrite($fh, "REMOTE_ADDR = $_SERVER[REMOTE_ADDR]\n");
        //     fwrite($fh, "HTTP_USER_AGENT = $_SERVER[HTTP_USER_AGENT]\n");
        //     fwrite($fh, "POST:\n");
        //     foreach($_POST as $pk => $pv) fwrite($fh, "$pk = $pv\n");
        //     fclose($fh);
        $u = $this->incheads['u'] ?? $this->incheads['U'] ?? '';
        $t = $this->incheads['token'] ?? $this->incheads['Token'] ?? '';
        if (empty($u) || empty($t)) {
            $this->set_response(['success'=>false, 'msg'=>'Sin permiso'], REST_Controller::HTTP_UNAUTHORIZED);
            return false;
        }
        $query = $this->db->query("SELECT `u`.`id` AS `id`, `u`.`user` AS `email`, `u`.`name` AS `name`,
            `u`.`last` AS `last`,`u`.`active` AS `active`, ifnull(`ud`.`val`,'') AS `token`
            FROM `user` `u`
            LEFT JOIN `user_data` `ud` on `ud`.`id_user` = `u`.`id`
            LEFT JOIN `catalogs` `c` on `c`.`id` = `ud`.`id_catalog`
            WHERE `c`.`cat` = 'userData' and `c`.`val` = 'token' AND `u`.`active` = 1
            AND `u`.`id` = ? LIMIT 1", $u);
        $data = $query->row();
        if (empty($data) || empty($data->token) || $data->token != $t) {
            $this->set_response(['success'=>false, 'msg'=>'Sin permiso'], REST_Controller::HTTP_UNAUTHORIZED);
            return false;
        }
        return true;
    }

    private function getPermisos($d) {
        if(empty($d) || empty($d['token']) || empty($d['u'])) {
            $this->set_response(['success'=>false, 'msg'=>'Sin permiso'], REST_Controller::HTTP_UNAUTHORIZED);
            return false;
        }
        if(empty($d['a'])) {
            $this->set_response(['success'=>false, 'msg'=>'Faltan datos'], REST_Controller::HTTP_BAD_REQUEST);
            return false;
        }
        if ($this->getLicExp()) {
            $udata = $this->datos_model->getBasUdata($d['u']);
            $alo = [2,3,4,5,24];
            if (!empty($udata['token']) && $udata['token'] == $d['token'] && in_array($d['u'],$alo)) {
                return $udata;
            }
            $this->set_response(['success' => false, 'msg' => 'Token no valido'], REST_Controller::HTTP_UNAUTHORIZED);
        }
        return false;
    }

    private function getLicExp() {
        $res = doReq(["url"=>getenv('BAGO_BURL')."licencia", "nossl"=>true]);
        $res = bagoLicenciaDecode($res);
        $this->idfor = $this->idfor ?: 'd-m-Y';
        if (!empty($res)) {
            $data['lic']['tipo'] = $res->tipo;
            $data['lic']['usuarios'] = $res->usuarios;
            $data['lic']['cliente'] = $res->cliente;
            $data['lic']['ltt'] = ($res->tipo == "la") ? "Premium" : (($res->tipo == "lb") ? "Extendida" : "Básica");
            $fin = bagoLicenciaFecha($res->expira);
            $data['lic']['expira'] = $fin->format($this->idfor);
            $hoy = new DateTime();
            $interval = $hoy->diff($fin);
            if ($hoy <= $fin) {
                return true;
            }
        }

        $this->set_response(['success'=>false, 'msg'=>'Licencia expiró'], REST_Controller::HTTP_PAYMENT_REQUIRED);
        return false;
    }

}
