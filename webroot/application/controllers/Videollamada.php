<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class Videollamada extends MY_Controller
{

    public function __construct() {
        parent::__construct();
    }

/* Funciones para el llamante */

    public function index() {
        $data = $this->input->get();
        if (empty($data['c'])) $data['c'] = '1';
        $this->load->model('videollamada_model');
        $vcServ = $this->videollamada_model->escrip($data['c']);
        $vcServA = explode('/', $vcServ);
        $data['vcServ'] = $vcServA[2];
        $this->armado->mostrar(array(
            'data' => array(
                'data'    => $data,
                'title'   => 'Videollamada',
                'extjs'   => $vcServ . 'external_api.js',
                'jscript' => 'videollamada',
            ),
            'view' => 'videollamada',
            'template'  => 'empty'
        ));
    }

    public function llamar() {
        $data = $this->input->post();
        $this->load->model('videollamada_model');
        $ret = [];
        if (empty($data["idconv"])) {
            $ret['idconv'] = $this->videollamada_model->nuevaVideollamada($data['data']);
        } else {
            $ret['idconv'] = $data["idconv"];
        }
        $ret = $this->videollamada_model->checkroomasig($ret);

        Header('Content-Type: application/json');
        echo json_encode($ret);
    }

    public function chekresp() {
        $data = $this->input->post();
        $this->load->model('videollamada_model');
        $res = $this->videollamada_model->chekresp($data);

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

/* Funciones para el agente */

    public function hadis() { // Hacerme Disponible
        if (!in_array('videocall', $this->udata['permisoSec'])) return false;
        $this->load->model('videollamada_model');

        Header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->hadis());
    }

    public function hanodis() { // Hacerme NO disponible
        if (!in_array('videocall', $this->udata['permisoSec'])) return false;
        $this->load->model('videollamada_model');

        Header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->hanodis());
    }

    public function oirtimbre() {
        if (!in_array('videocall', $this->udata['permisoSec'])) return false;
        $this->load->model('videollamada_model');

        Header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->oirtimbre());
    }

    public function aceptar() {
        $this->load->model('videollamada_model');

        Header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->aceptar());
    }

    public function rechazar() {
        $this->load->model('videollamada_model');

        Header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->rechazar());
    }

    public function ocupado() {
        $this->load->model('videollamada_model');

        Header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->ocupado($this->input->post()));
    }

    public function fin() {
        $this->load->model('videollamada_model');

        Header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->fin($this->input->post()));
    }

    public function tranp1() {
        $this->load->model('videollamada_model');

        Header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->tranp1());
    }

    public function tranp2() {
        $this->load->model('videollamada_model');

        Header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->tranp2($this->input->post()));
    }

    public function tranp3() {
        $this->load->model('videollamada_model');

        Header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->tranp3($this->input->post()));
    }


/* Funciones extras */

    public function reporte_detalle() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['agentes']  = $this->datos_model->getRelUsers(["act"=>FALSE,"cam"=>$data['campanas']]); // Mostrar activos e inactivos
        $data['title']    = 'Videollamada detalle';
        $data['modelo']   = 'videollamada';
        $data['jscript']  = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function reporte_indicador() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['agentes']  = $this->datos_model->getRelUsers(["act"=>FALSE,"cam"=>$data['campanas']]); // Mostrar activos e inactivos
        $data['title']    = 'Videollamada indicadores';
        $data['modelo']   = 'videollamada';
        $data['jscript']  = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    function excel() {
        $this->load->model('videollamada_model');
        $data = $this->videollamada_model->reportes_video($this->input->post());
        if (count($data["registros"])>0) {
            $style = (new StyleBuilder())
                ->setFontBold()
                ->setShouldWrapText()
                ->build();
            $writer = WriterFactory::create(Type::CSV);
            $filename = 'informe.csv';
            $writer->openToBrowser($filename);
            $writer->addRowWithStyle($data["campos"], $style);
            $writer->addRows($data["registros"]);
            $writer->close();
        } else {
            echo "Sin datos con esos parámetros, cierra la ventana e intenta nuevamente";
        }
    }

/* Funciones para los permisos */

    public function config(){
        $data['title']   = 'Configuración de Videollamadas';
        $data['jscript'] = 'config/videollamada';
        $datos = array(
            'view' => 'config/videollamada',
            'data' => $data,
        );

        $this->armado->mostrar($datos);
    }

    //Guardamos los permisos de todos los usuarios en el formulario
    public function guardarPermisos(){
        $data = [];
        $permisos = [
            'ad' => 0,
            'ac' => 1,
            'gb' => 2,
            'tr' => 3,
            'cg' => 4,
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
        $this->load->model('videollamada_model');
        $response = $this->videollamada_model->updatePermisos($data_update);

        Header('Content-Type: application/json');
        echo json_encode($response);
    }

    //PAGINACION
    public function lista() {
        $this->load->model('videollamada_model');
        header('Content-Type: application/json');
        echo json_encode($this->videollamada_model->getData($this->input->post()));
    }
}

?>
