<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Desconexion extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['agentes']  = $this->datos_model->getRelUsers(["cam"=>$data['campanas'], "act"=>FALSE]);
        $data['title']    = 'Desconexión de llamadas';
        $data['modelo']   = 'desconexion';
        $data['grande']   = 'grande';
        $data['jscript']  = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

}
