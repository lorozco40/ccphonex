<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repoesp extends MY_Controller
{

    public function __construct() {
        parent::__construct();
    }

    public function tickets() {
        $this->load->model('crm_model');
        $data['cid']                  = $this->udata['campanas'];
        $data['massel']['Formulario'] = $this->crm_model->traerforms($data);
        $data['massel']['Estatus'] = [
            (object)['id'=>'',        'name'=>'Todos'],
            (object)['id'=>'Abierto', 'name'=>'Abierto'],
            (object)['id'=>'Cerrado', 'name'=>'Cerrado'],
        ];
        $data['modelo']               = 'repoesp';
        $data['title']                = 'Tickets';
        $data['jscript']              = 'reportes/tickets';

        $this->armado->mostrar(array(
            'view' => 'reportes/tickets',
            'data' => $data,
        ));
    }

}

?>
