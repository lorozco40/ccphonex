<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
    }

    function _remap($ins) {
        $this->index($ins);
    }

    public function index($ins = null) {
        if (empty($ins)) {
            die('Inserción incorrecta');
        }
        $this->load->view('chat', array('ins' => $ins));
    }
    
}