<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting( E_ALL );
ini_set("display_errors", 1);

class Debug extends CI_Controller
{

    public function __construct(){
        parent::__construct();
    }

    // public function index() {
    //     $this->load->model("debug_model");
    //     $this->debug_model->llamadas();
    // }

    public function ping() {
        // $res = doReq(["url"=>getenv('BAGO_URL')."licencia", "nossl"=>true]);
        $ret = 0;
        if (!empty($this->session->userdata('uid'))) { //  && $res["code"] == 200
            $ret = 1;
        }

        Header('Content-Type: application/json');
        echo json_encode($ret);
    }

}
