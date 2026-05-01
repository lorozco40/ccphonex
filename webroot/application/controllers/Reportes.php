<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class Reportes extends MY_Controller
{

    public $posibles_titulos = [
        'campana'    => 'Campaña',
        'id_campana' => 'ID Campaña',
        'grabacion'  => 'Grabación',
        'duracion'   => 'Duración',
        'numero'     => 'Número',
        'id_agente'  => 'ID Agente',
    ];

    public function __construct() {
        parent::__construct();
        $this->load->model('reportes_model');
    }

    public function index() {
        $this->load->helper("fun_helper");
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Dashboard';
        $data['colas']    = colas();
        $data['extjs']    = '//www.gstatic.com/charts/loader.js';
        $data['jscript']  = 'reportes/dashboard';

        $this->armado->mostrar(array(
            'view' => 'reportes/dashboard',
            'data' => $data,
        ));
    }

    private function traerdata($data) {
        if (empty($data['reporte'])) {
            return ['Error'=>'Especificar el reporte'];
        }
        $modelo           = (empty($data['modelo'])) ? 'reportes_model' : $data['modelo']."_model";
        $func             = $data['reporte'];
        if($modelo != 'reportes_model') $this->load->model($modelo);
        $data['campanas'] = (empty($data['campana'])) ? $this->udata['campanas'] : $data['campana'];
        if (empty($data['agente'])) {
            $agentes        = $this->datos_model->getRelUsers(["cam"=>$data['campanas'], "act"=>FALSE]);
            $data['agente'] = $this->datos_model->getIdsFromArr($agentes);
        }
        $data['min']     = (empty($data['min'])) ? date('Y-m-d') : $data['min'];
        $data['max']     = (empty($data['max'])) ? date('Y-m-d') : $data['max'];
        $data['min']     = convierte($data['min'], $this->idfor);
        $data['max']     = convierte($data['max'], $this->idfor);
        $data['rpp']     = (empty($data['rpp'])) ? REGS_POR_PAG : $data['rpp'];
        $data['pag']     = (empty($data['pag'])) ? 0 : $data['pag'];
        $data            = $this->$modelo->$func($data);
        $data['data']    = (isset($data["data"])) ? $data["data"] :
            ((isset($data["registros"])) ? $data["registros"] : []);
        if (count($data['data'])>0) {
            $data['tits'] = array_map(function($item){
                return beautify($item, $this->posibles_titulos);
            }, $data['campos']);
        }
        $data['min']  = convierte($data['min'], 'Y-m-d', $this->idfor);
        $data['max']  = convierte($data['max'], 'Y-m-d', $this->idfor);

        return $data;
    }

    public function data() {
        $data = $this->traerdata($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function data_aicm($simdata = false) {
        $data = $this->input->post();
        if ($simdata) {
            $data['pag'] = 'x';
        }
        $success = true;
        if (empty($data['reporte'])) {
            $success = false;
            $data = ['error'=>'Seleccionar el Reporte'];
        }
        if($success === true && empty($data['formulario'])) {
            $success = false;
            $data = ['error'=>'Seleccionar el Formulario'];
        }
        if ($success === true) {
            $data['campanas'] = (empty($data['campana'])) ? $this->udata['campanas'] : $data['campana'];
            if (empty($data['agente'])) {
                $agentes        = $this->datos_model->getRelUsers(["cam"=>$data['campanas'], "act"=>FALSE]);
                $data['agente'] = $this->datos_model->getIdsFromArr($agentes);
            }
            $data['min']     = (empty($data['min'])) ? date('Y-m-d') : convierte($data['min'], $this->idfor);
            $data['max']     = (empty($data['max'])) ? date('Y-m-d') : convierte($data['max'], $this->idfor);
            $data['rpp']     = (empty($data['rpp'])) ? REGS_POR_PAG : $data['rpp'];
            $data['pag']     = (empty($data['pag'])) ? 0 : $data['pag'];
            $this->load->model("repoesp_model");
            $data            = $this->repoesp_model->tickets($data);
            $data = $this->datos_model->manejadorqueries($data);
            if ($simdata) {
                return $data;
            }
            if (!isset($data['data'])) {
                $data = ['error'=>'Formulario incompatible'];
            } else {
                $pagi = $this->pagi;
                $pagi['total_rows'] = $data['cuenta'];
                $pagi['cur_page']   = $data["pag"];
                $pagi['last_link']  = ceil($data['cuenta']/(REGS_POR_PAG));
                $this->load->library('pagination', $pagi);
                $data['pagination'] = $this->pagination->create_links();
                $data['min']  = convierte($data['min'], 'Y-m-d', $this->idfor);
                $data['max']  = convierte($data['max'], 'Y-m-d', $this->idfor);
            }
        }

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function excel_aicm() {
        $data = $this->data_aicm(true);
        if (count($data['data'])>0) {
            $data['tits'] = array_map(function($item){
                return beautify($item, $this->posibles_titulos);
            }, $data['campos']);
            $style = (new StyleBuilder())
                ->setFontBold()
                ->setShouldWrapText()
                ->build();
            $writer = WriterFactory::create(Type::CSV);
            $filename = $data['reporte'] . '_' . date('Ymd_His') . '.csv';
            $writer->openToBrowser($filename);
            $writer->addRowWithStyle($data["tits"], $style);
            // Necesario $query->result_array()
            $writer->addRows($data['data']);
            $writer->close();
        } else {
            echo "Sin datos con esos parámetros, cierra la ventana e intenta nuevamente";
        }
    }

    public function excel() {
        $data = $this->input->post();
        $data['pag'] = 'x';
        $data['paraexcel'] = true;
        $data = $this->traerdata($data);

        if (count($data['data'])>0) {
            $style = (new StyleBuilder())
                ->setFontBold()
                ->setShouldWrapText()
                ->build();
            $writer = WriterFactory::create(Type::CSV);
            $filename = $data['reporte'] . '_' . date('Ymd_His') . '.csv';
            $writer->openToBrowser($filename);
            $writer->addRowWithStyle($data["tits"], $style);
            // Necesario $query->result_array()
            $writer->addRows($data['data']);
            $writer->close();
        } else {
            echo "Sin datos con esos parámetros, cierra la ventana e intenta nuevamente";
        }
    }

    public function dashini() {
        $data = $this->reportes_model->dashindicadores($this->input->post("campana"));
        $this->load->model('colas_model');
        $this->load->helper("fun_helper");
        $data["colas"]=colas();
        $name_colas = $this->colas_model->traeColasNamePorCampanas($this->input->post("campana"));
        foreach ($data["colas"] as $key => $value) {
            $enllamada = 0;
            if (!is_numeric($key) || !in_array($key, $name_colas) ) {
                unset($data["colas"][$key]);
            } else {
                if(isset($value["members"])) {
                    foreach ($value["members"] as $agente) {
                        if ($agente == "In use") $enllamada++;
                    }
                }
                $data["colas"][$key]['enllamada'] = $enllamada;
            }
        }

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function inbound() {
        $this->load->model("repoback_model");
        $this->repoback_model->inbound();
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['agentes']  = $this->datos_model->getRelUsers(["cam"=>$data['campanas'], "act"=>FALSE]);
        $data['title']    = 'Inbound';
        $data['jscript']  = 'reportes/reporte';
        $data['grande']   = true;
        $data['massel']['Llamadas'] = ['0'=>'Todas ...','Terminada'=>'Terminadas','Abandonada'=>'Abandonadas'];
        $data['massel']['Calidad'] = ['0'=>'Todas ...','Evaluadas'=>'Evaluadas','Noevaluadas'=>'No evaluadas'];
        $data['masinput']['CallerId o Número'] = 'busc';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function abandono() {
        $this->load->model("repoback_model");
        $this->repoback_model->abandono();
        $data['campanas']           = $this->datos_model->getCampanas();
        $data['title']              = 'Abandono';
        $data['jscript']            = 'reportes/reporte';
        $data['massel']['Llamadas'] = [
            '0'=>'Todas ...',
            'Abandonada'=>'Abandonadas',
            'Abandonada nosl'=>'Abandonadas No SL',
            'Abandonada Troncal'=>'Abandonadas Troncal'
        ];

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function abandonototal() {
        $this->load->model("repoback_model");
        $this->repoback_model->abandono();
        $data['title']              = 'Abandono total';
        $data['jscript']            = 'reportes/reporte';
        $data['massel']['Llamadas'] = [
            '0'=>'Todas ...',
            'Abandonada'=>'Abandonadas',
            'Abandonada nosl'=>'Abandonadas No SL',
            'Abandonada Troncal'=>'Abandonadas Troncal'
        ];

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function formulario() {
        $this->load->model("form_model");
        $data['massel']['Formulario'] = $this->form_model->get_list_active(0);
        $data['modelo']               = "form";
        $data['title']                = "Formulario";
        $data['jscript']              = "reportes/reporte";

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function espera() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Tiempo de espera';
        $data['jscript']  = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function exito() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Éxito en Llamadas';
        $data['cual']     = "exito";
        $data['tipo']     = "Si"; // Entrante o Saliente preoaradi select en vista reporte
        $data['jscript']  = 'reportes/reporte';
        $data['massel']['tipo'] = ['0'=>'Todas ...', 'Entrante'=>'Entrante', 'Salientes'=>'Salientes'];

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function poragente() {
        $this->load->model("repoback_model");
        $this->repoback_model->poragente();
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['agentes']  = $this->datos_model->getRelUsers(["cam"=>$data['campanas'], "act"=>FALSE]);
        $data['title']    = 'Llamadas por Agente';
        $data['jscript']  = 'reportes/reporte';
        $data['massel']['tipo'] = ['0'=>'Todas ...', 'Entrante'=>'Entrantes', 'Saliente'=>'Salientes'];

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function distribucion_grafico() {
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']   = 'Grafico por Agentes';

        $this->armado->mostrar(array(
            'view' => 'reportes/poragentegra',
            'data' => $data,
        ));
    }

    public function listarporagentegra() {
        $min    = $this->input->post("min");
        $max    = $this->input->post("max");
        $agente = $this->input->post("agente");

        $data   = $this->reportes_model->callporagentegra($min, $max, $agente);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function acw() {
        $this->load->model("repoback_model");
        $this->repoback_model->acw();
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['cual']    = 'acw';
        $data['title']   = 'After Call Work';
        $data['jscript'] = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function atendidas30() {
        $this->load->model("repoback_model");
        $this->repoback_model->atendidas();
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Atendidas cada media hora';
        $data['cual']     = "atendidas30";
        $data['jscript']  = "reportes/reporte";

        $datos = array(
            'view' => 'reportes/reporte',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

    public function abandono30() {
        $this->load->model("repoback_model");
        $this->repoback_model->abandono();
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Abandonadas cada media hora';
        $data['cual']     = "abandono30";
        $data['jscript']  = "reportes/reporte";

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function compara30() {
        $this->load->model("repoback_model");
        $this->repoback_model->atendidas();
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Comparativo cada media hora';

        $this->armado->mostrar(array(
            'view' => 'reportes/pormediasgra',
            'data' => $data,
        ));
    }

    public function ivr() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Opciones IVR';
        $data['jscript']  = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function buzonvoz() {
        $this->load->model("repoback_model");
        $this->repoback_model->vm();
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['cual']     = 'vm';
        $data['title']    = 'Buzón de Voz';
        $data['jscript']  = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function sesion() {
        $this->load->model("repoback_model");
        $this->repoback_model->sesion();
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']   = 'Tiempo de Sesión';
        $data['jscript'] = 'reportes/reporte';
        $data['grande']  = 'grande';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function outbound() {
        $this->load->model("repoback_model");
        $this->repoback_model->outbound();
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['agentes']  = $this->datos_model->getRelUsers(["cam"=>$data['campanas'], "act"=>FALSE]);
        $data['title']    = 'Outbound';
        $data['jscript']  = 'reportes/reporte';
        $data['grande']   = 'grande';
        $data['massel']['Calidad']  = ['0'=>'Todas ...','Evaluadas'=>'Evaluadas','Noevaluadas'=>'No evaluadas'];
        $data['massel']['Llamadas'] = ['0'=>'Todas ...','Terminada'=>'Terminadas','Abandonada'=>'Abandonadas'];
        $data['masinput']['CallerId o Número'] = 'busc';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function descansos() {
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']   = "Descansos";
        $data['jscript'] = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function por_campana() {
        $this->load->model("agentes_model");
        $data['title']     = 'Analisis por Colas';
        $data['agentes']   = $this->agentes_model->lista();
        $data['campaigns'] = $this->datos_model->getCampanas();

        $this->armado->mostrar(array(
            'view' => 'reportes/porcolasgra',
            'data' => $data,
        ));
    }

    public function listarporcolasgra() {
        $data    = $this->reportes_model->callporcolasgra($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    function historico() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['title']    = 'Histórico';

        $this->armado->mostrar(array(
            'view' => 'reportes/historico',
            'data' => $data,
        ));
    }

    public function log_usuarios() {
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['title']   = 'Log de usuarios';
        $data['jscript'] = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function buscarHorasCampana() {
        $success = false;
        $msg     = "";
        $data    = [];

        if ( !empty($this->input->post('cid')) ) {
            $this->load->model('campanas_model');

            $cid = $this->input->post("cid");
            $query = $this->campanas_model->get_horario_min_max($cid);
            if( isset($query) ){
                $success = true;
                $data    = $query;
            }else{
                $msg = "No se encontraron horas para la campaña.";
            }
        }else{
            $msg = "No se envió la campaña.";
        }

        $jsondata            = [];
        $jsondata["success"] = $success;
        $jsondata["msg"]     = $msg;
        $jsondata["data"]     = $data;

        header('Content-Type: application/json');
        echo json_encode($jsondata);
    }

}

?>
