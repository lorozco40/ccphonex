<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class Lhc extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model("lhc_model");
    }

    public function trae_chat() {
        $resp = doReq(['url'=>'https://chat.phonex-servicios.com/index.php/restapi/chats?limit=10', 'proto'=>'x-www-form-urlencoded',
            'method'=>'POST', 'body'=>['username'=>'kinon','password'=>'ph0n3x1'], 'auth'=>'kinon:ph0n3x1']);
        $this->lhc_model->insert_chat($resp["data"]);
    }

    public function extrae_id() {
        $query= $this->db->query("SELECT id, status FROM hc_chats where status<>0");

        return $query->result();
    }

    public function trae_message() {
        $data = $this->extrae_id();
        foreach ($data as $key => $row) {
            $resp = doReq(['url'=>'https://chat.phonex-servicios.com/index.php/restapi/fetchchatmessages', 'proto'=>'x-www-form-urlencoded',
                'body'=>['chat_id'=>$row->id,'username'=>'kinon','password'=>'ph0n3x1'], 'auth'=>'kinon:ph0n3x1']);
            $this->lhc_model->insert_message($resp["data"]);
        }
    }

    public function reporte_chat() {
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']   = 'Chats';
        $data['grande']  = 'grande';
        $data['modelo']  = 'lhc';
        $data['jscript'] = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function reporte_mensajes() {
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']   = 'Mensajes';
        $data['grande']  = 'grande';
        $data['modelo']  = 'lhc';
        $data['jscript'] = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function mensajes_detalle() {
        $min = $this->input->post("min");
        $max = $this->input->post("max");
        $agente = $this->input->post("agentes");
        $page = ($this->input->post("page")) ? $this->input->post("page") : 0;
        $pagi = $this->pagi;

        $data = $this->lhc_model->mensajes_detalle($min, $max, $agente, (int)$page, $pagi['per_page']);
        // dd($query);
        $data['page']= $page;

        $pagi['base_url'] = site_url('reportes/lhc_mensajes');
        $pagi['total_rows'] = $data['cuenta'];
        $pagi['cur_page'] = $data['page'];
        $pagi['last_link'] = ceil($data['cuenta']/(REGS_POR_PAG));

        $this->load->library('pagination', $pagi);
        $data['pagination'] = $this->pagination->create_links();

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function reporte_espera() {
        $this->armado->mostrar(array(
            'view' => 'reportes/lhc_espera',
            'data' => array('title'=>'Espera'),
        ));
    }

    public function espera_chat() {
        $min  = $this->input->post("min");
        $max  = $this->input->post("max");
        $data = $this->lhc_model->espera_chat($min, $max);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    function excel() {
        $data = $this->lhc_model->reportes_lhc($this->input->post());
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
}
?>
