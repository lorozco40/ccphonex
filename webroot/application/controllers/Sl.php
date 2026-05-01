<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class Sl extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function reporte_inbound() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['agentes']  = $this->datos_model->getRelUsers(["cam"=>$data['campanas'], "act"=>FALSE]);
        $data['sl']       = $this->datos_model->getParams('sl', 'array');
        $data['title']    = 'Nivel de Servicio Inb';

        $this->armado->mostrar(array(
            'view' => 'reportes/slin',
            'data' => $data,
        ));
    }

    public function reporte_outbound() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Nivel de Servicio Out';

        $this->armado->mostrar(array(
            'view' => 'reportes/slout',
            'data' => $data,
        ));
    }

    public function grafico() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Nivel de Servicio Graf';

        $this->armado->mostrar(array(
            'view' => 'reportes/sla_graph',
            'data' => $data,
        ));
    }

    public function reporte_acumensual() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Acumulado mensual';
        $data['sl']       = $this->datos_model->getParams('sl', 'array');

        $this->armado->mostrar(array(
            'view' => 'reportes/acum_mensual',
            'data' => $data,
        ));
    }

    public function reporte_acumdiario() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Acumulado diario';
        $data['sl']       = $this->datos_model->getParams('sl', 'array');

        $this->armado->mostrar(array(
            'view' => 'reportes/acum_diario',
            'data' => $data,
        ));
    }

    public function slindata() {
        $this->load->model('sl_model');
        $data = $this->input->post();
        if (empty($data['campana'])) {
            $data = ["data"=>""];
        } else {
            $data = $this->sl_model->slin($data);
        }

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function sloutdata() {
        $this->load->model('sl_model');
        $data = $this->sl_model->slout($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function slingrafdata() {
        $this->load->model('sl_model');
        $data = $this->sl_model->slingraf($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function acum_mensual_data() {
        $this->load->model('sl_model');
        $data = $this->sl_model->acumensual($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function acum_diario_data() {
        $this->load->model('sl_model');
        $data = $this->sl_model->acumdiario($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    function excel() {
        $this->load->model('sl_model');
        $data = $this->sl_model->reporte($this->input->post());
        if (count($data["registros"])>0) {
            $style = (new StyleBuilder())
            ->setFontBold()
            ->setShouldWrapText()
            ->build();
            $writer = WriterFactory::create(Type::CSV);
            $filename = 'sl.csv';
            $writer->openToBrowser($filename);
            $writer->addRowWithStyle($data["campos"], $style);
            $writer->addRows($data["registros"]);
            $writer->close();
        } else {
            echo "Sin datos con esos parámetros, cierra la ventana e intenta nuevamente";
        }
    }

}

?>
