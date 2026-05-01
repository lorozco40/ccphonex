<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chatinterno extends MY_Controller
{

    public function __construct(){
        parent::__construct();
        $this->load->model('chatinterno_model');
    }

    public function index() {
        $data['title']   = 'Configuración del chat interno';
        $data['jscript'] = 'config/chatinterno';
        $datos = array(
            'view' => 'config/chatinterno',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

    //Guardamos los permisos de todos los usuarios en el formulario
    public function guardarPermisos(){
        $data = [];
        $permisos = [
            'pc' =>  0,
            'emd' => 1,
            'ems' => 2,
            'emu' => 3,
            'rmu' => 4,
        ];
        foreach ( $this->input->post() as $key => $val ){
            $send = explode('-', $key);
            if( count($send) == 2 ){
                $user_id = $send[1];
                $permiso = $send[0];

                if( isset($data[$user_id]) ){
                    if( array_key_exists($permiso, $permisos) ){
                        $indice = $permisos[$permiso];
                        $data[$user_id][$indice] = 1;
                    }
                }
                else{
                    $data[$user_id] = [0,0,0,0,0];
                    if( array_key_exists($permiso, $permisos) ){
                        $indice = $permisos[$permiso];
                        $data[$user_id][$indice] = 1;
                    }
                }
            }
        }
        $data_update = [];
        foreach($data as $key => $row){
            $data_update[] = [
                'id_user' => $key,
                'val' => implode(',',$row)
            ];
        }
        $this->load->model('chatinterno_model');
        $response = $this->chatinterno_model->updatePermisos($data_update);

        Header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function lista() {
        header('Content-Type: application/json');
        echo json_encode($this->chatinterno_model->getData($this->input->post()));
    }
}
?>
