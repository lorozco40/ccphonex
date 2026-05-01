<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ari extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        if ($this->udata['perfil'] != 'admin' && $this->session->userdata('uid')!=3) redirect();
    }

    public function index() {
        $ariserv       = "https://" . getenv('ARI_USER') . ":" . getenv('ARI_PASS') . "@" . getenv('ASS_DB_HOST') . ":8089";
        $res           = doReq(["url"=>$ariserv . "/ari/endpoints", 'proto'=>'x-www-form-urlencoded', "nossl"=>true]);
        $data['data']  = json_decode($res["data"]);
        $data['cual']  = "Endpoints";
        $data['title'] = "Ari endpoints";

        $datos = array(
            'view' => 'tabla',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

}
