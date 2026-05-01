<?php
defined('BASEPATH') || exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api2 extends REST_Controller {

    private $incheads;
    private $salir = true;

    public function __construct() {
        parent::__construct();
        $this->incheads = getallheaders();
        $this->salir = !$this->validaheads();
    }

    public function wabots_get() {
        if ($this->salir) return;
        $pars = $this->input->get();
        $bid = $pars['bid'] ?: 'malo';
        if ($bid == 'malo') {
            $this->set_response(['success'=>false,'message'=>'bid no válido'],
            REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $this->load->model('wabot_model');
        $res = $this->wabot_model->writeRouteId($bid);
        if ($res['success']) {
            $this->set_response($res, REST_Controller::HTTP_OK);
        } else {
            $this->set_response($res, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

// internas --->

    private function validaheads() {
        if (empty($this->incheads['u']) || empty($this->incheads['token'])) {
            $this->set_response(['success'=>false, 'msg'=>'Sin permiso'], REST_Controller::HTTP_UNAUTHORIZED);
            return false;
        }
        $query = $this->db->query("SELECT `u`.`id` AS `id`, `u`.`user` AS `email`, `u`.`name` AS `name`,
            `u`.`last` AS `last`,`u`.`active` AS `active`, ifnull(`ud`.`val`,'') AS `token`
            FROM `user` `u`
            LEFT JOIN `user_data` `ud` on `ud`.`id_user` = `u`.`id`
            LEFT JOIN `catalogs` `c` on `c`.`id` = `ud`.`id_catalog`
            WHERE `c`.`cat` = 'userData' and `c`.`val` = 'token' AND `u`.`active` = 1
            AND `u`.`id` = ? LIMIT 1", $this->incheads['u']);
        $data = $query->row();
        if (empty($data) || empty($data->token) || $data->token != $this->incheads['token']) {
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
        $res = doReq(["url" => getenv('BAGO_BURL')."licencia", "nossl" => true]);
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
