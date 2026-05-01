<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $this->armado->mostrar(array(
            'view' => 'inicio',
            'data' => ['title' => 'Inicio'],
        ));
    }

    public function ver($page) {
        if (!file_exists(APPPATH.'/views/'.$page.'.php')) {
            show_404();
        }

        $this->armado->mostrar(array(
            'view' => $page,
            'data' => array('title'=>ucfirst($page)),
        ));
    }

}
