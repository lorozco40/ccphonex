<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Reader\ReaderFactory;

class Pit extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model("pit_model");
    }

    public function index() {
        $data['title']      = "PIT";
        $data['jscript']    = 'config/pit';
        $this->armado->mostrar(array(
            'view' => 'config/pit',
            'data' => $data,
        ));
    }

    public function traerdata() {
        $this->load->model("pit_model");
        $data = $this->pit_model->traerdata($this->input->post());
        $data['usuarios_pit'] = $this->pit_model->usuarios_pit();
        $data['perfil'] = $this->udata['perfil'];

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function buscar_nombre() {
        $jsondata = $this->pit_model->buscar_pin_nombre();

        header('Content-Type: application/json');
        echo json_encode($jsondata);
    }

    public function editar() {
        $id = $this->input->post('id');
        $jsondata = $this->pit_model->edit($id);

        header('Content-Type: application/json');
        echo json_encode($jsondata);
    }

    public function guardar() {
        //Validamos la informacion
		$this->form_validation->set_rules('pin',  		"'PIN'",  	    'required|min_length[1]|max_length[10]|integer');
        $this->form_validation->set_rules('phone',      "'Telefono'",   'required|min_length[10]|max_length[15]');
		$this->form_validation->set_rules('name',      	"'Nombre'",     'required|min_length[3]|max_length[150]');
		$this->form_validation->set_rules('last',      	"'Apellidos'",  'min_length[3]|max_length[150]');
		$this->form_validation->set_rules('aviso',      "'Aviso'",      'min_length[3]|max_length[400]');
        if( $this->input->post('active') != '1' )
            $this->form_validation->set_rules('motivo', "'Motivo'",     'required|min_length[3]|max_length[50]');
        if( $this->input->post('id') > '0' && !empty($this->input->post('id_pit_catalog_redirect')) ) {//En caso de que sea edicion y tenga un redirect
            $this->form_validation->set_rules('vigencia',      	"'Vigencia Fecha'", 'required|min_length[10]|max_length[10]');
            $this->form_validation->set_rules('vigencia_hora',  "'Vigencia Hora'",  'required|callback_valida_hora');
        }

		if ($this->form_validation->run() == false){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $json = ["error" => $errors[ $fields[0] ]];
		} else {
            $json = $this->pit_model->guardar();
		}

        header('Content-Type: application/json');
        echo json_encode($json);
    }

    public function pmc_guardar() {
        //Validamos la informacion
		$this->form_validation->set_rules('mpc_id',  		"'ID'",  	    'required|integer');
        $this->form_validation->set_rules('mpc_phone',      "'Telefono'",   'required|min_length[10]|max_length[15]');
		$this->form_validation->set_rules('mpc_aviso',      "'Aviso'",      'min_length[3]|max_length[400]');

        if( !empty($this->input->post('id_pit_catalog_redirect')) ) {//En caso de que sea edicion y tenga un redirect
            $this->form_validation->set_rules('vigencia',      	"'Vigencia Fecha'", 'required|min_length[10]|max_length[10]');
            $this->form_validation->set_rules('vigencia_hora',  "'Vigencia Hora'",  'required|callback_valida_hora');
        }

		if ($this->form_validation->run() == false){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $json = ["error" => $errors[ $fields[0] ]];
		} else {
            $json = $this->pit_model->mpc_guardar();
		}

        header('Content-Type: application/json');
        echo json_encode($json);
    }

    public function eliminar() {
		$id = $this->input->post('id');
        $json = $this->pit_model->eliminar($id);

        header('Content-Type: application/json');
        echo json_encode($json);
    }

    public function enviar() {
        //Validamos datos de entrada
        $this->form_validation->set_rules('id_pit_catalog', "'Campaña'",    'required|integer');
        $this->form_validation->set_rules('pin',            "'Pin Clave'",  'required|max_length[10]');
        $this->form_validation->set_rules('msg',            "'Mensaje'",    'required|max_length[160]');
        if ($this->form_validation->run() == false){
            $errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $respu = [
                "action"=>"danger",
                "msg" => $errors[ $fields[0] ]
            ];
        } else {
            $id_pit_catalog  = preg_replace("/[^0-9]/", "", $this->input->post("id_pit_catalog"));
            $query = $this->pit_model->get_one(["id"=>$id_pit_catalog]);
            if( $query ) {
                $lnum = $query['data']->phone;
                $nop = array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ");
                $sip = array ("a","e","i","o","u","A","E","I","O","U","n","N");
                $ltxt  = str_replace($nop, $sip, $this->input->post("msg"));
                $ltxt  = preg_replace("/[^A-Za-z0-9% .:_?=,\/\-$@#]/", "", $ltxt);
                //Verificamos si existe un redireccionamiento, en caso de existir obtendremos el nuemro de ese redireccionamiento
                $redireccionamiento = $this->pit_model->redireccionamiento_phone($query['data']->id);
                if( $redireccionamiento->redirect == 'SI' )
                    $lnum = $redireccionamiento->phone;
                $envio = $this->enviarSMS($lnum, $ltxt);
                $params = (object) [
                    'id_pit_catalog' => $query['data']->id,
                    'msg' => $ltxt,
                    'id_user' => $this->session->userdata('uid'),
                    'resp' => json_encode($envio),
                    'uid' => $envio->message_id ?? '',
                    'status' => $envio->resp ?? '',
                    'redirected' => $redireccionamiento->redirect,
                    'status_desc' => $envio->description ?? ''
                ];
                if ($this->pit_model->finalizar($params)) {
                    $respu["action"] = "success";
                    $respu["msg"] = "Procesando SMS, gracias.";
                }
            } else {
                $respu = array("action" => "danger", "msg" => "No se encontró el PIN.");
            }
        }

        header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function pnueva() {
        // Validamos los datos de entrada
        $this->form_validation->set_rules('id_campaign',    "'Campaña'",            'required|integer');
        $this->form_validation->set_rules('name',           "'Nombre Plantilla'",   'required|max_length[100]');
        $this->form_validation->set_rules('valor',          "'Texto Plantilla'",    'required|max_length[160]');
        if ($this->form_validation->run() == false){
            $errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $respu = [
                "action"=>"danger",
                "msg" => $errors[ $fields[0] ]
            ];
        } else {
            if ($insertid = $this->pit_model->plantilla_crear($this->input->post())) {
                $respu["id"] = $insertid;
                $respu["action"] = "success";
                $respu["msg"] = "Plantilla agregada con éxito.";
            } else {
                $respu = [
                    "action"=>"danger",
                    "msg" => 'Error: no se pudo agregar la plantilla.'
                ];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function pactu() {
        // Validamos los datos de entrada
        $this->form_validation->set_rules('id',     "'ID'",             'required|integer');
        $this->form_validation->set_rules('valor',  "'Texto Plantilla'",'required|max_length[160]');
        if ($this->form_validation->run() == false){
            $errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $respu = [
                "action"=>"danger",
                "msg" => $errors[ $fields[0] ]
            ];
        } else {
            if ($this->pit_model->plantilla_actualizar($this->input->post())) {
                $respu["action"] = "success";
                $respu["msg"] = "Plantilla actualizada con éxito.";
            } else {
                $respu = [
                    "action"=>"danger",
                    "msg" => 'Error: No se pudo actualizar la plantilla.'
                ];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function pborrar() {
        $respu = array("action" => "danger", "msg" => "Hubo un error procesando la petición");
        if (!empty(!empty($this->input->post('id')))) {
            if ($this->pit_model->plantilla_borrar($this->input->post('id'))) {
                $respu["action"] = "success";
                $respu["msg"] = "Plantilla borrada con éxito.";
            }
        }
        header('Content-Type: application/json');
        echo json_encode($respu);
    }

    private function enviarSms($num, $msg) {
        $sms = $this->datos_model->getParams('sms');
        $data = [
            "to"      => (strlen($num) == 10) ? '52'.$num : $num,
            "message" => $msg,
            "from"    => 'pit',
        ];
        $respu = doReq(['url'=>$sms->server, 'method'=>'POST', 'body'=>$data, 'proto'=>'x-www-form-urlencoded', 'heads'=>['Authorization: Bearer '.$sms->token]]);

        return $respu;
    }

    public function reporte_detalle() {
        $data['modelo']   = 'pit';
        $data['title']    = 'PIT detalle';
        $data['grande']   = 'Si';
        $data['wrapable'] = ['mensaje'];
        if ( $this->udata['perfil'] != 'crm' ) {
            $data['masinput'] = ['Clave/Nombre'=>'clave_nombre'];
        }
        $data['jscript'] = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function reporte_indicador() {
        $data['agentes'] = $this->datos_model->getRelUsers(["act"=>false]);
        $data['modelo']  = 'pit';
        $data['title']   = 'PIT Indicadores';
        $data['jscript'] = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function actualiza() {
        $tabla = $this->input->get('tabla');
        if ($tabla!=null) {
            echo $this->pit_model->actualiza($tabla);
        } else {
            echo $this->pit_model->actualiza();
        }
    }

    public function camp_enviar() {
        $this->load->helper('fun_helper');

        Header('Content-Type: application/json');
        echo json_encode($this->pit_model->camp_enviar($this->input->post()));
    }

    public function guardarArchivo() {
        $items = [];
        $now = date("Y-m-d H:i:s");
        if (!file_exists(APPPATH . '../files')) mkdir(APPPATH . '../files', 0755, true);
        $data['file_ext_tolower'] = true;
        $data['allowed_types']    = 'csv|xls|xlsx|ods';
        $data['upload_path']      = APPPATH . '../files';
		$data['file_name']        = md5(uniqid(rand(), true));
        $sucess = true;
        $message = '';
        $this->load->library('upload', $data);
        if ( !$this->upload->do_upload('contactos') ) {
            $res = ["error"=>$this->upload->display_errors()];
		} else {
            $ext = ltrim($this->upload->data('file_ext'),'.');
            switch ($ext) {
                case 'ods':
                    $reader = ReaderFactory::create(Type::ODS);
                    break;
                case 'xls':
                    $reader = ReaderFactory::create(Type::XLS);
                    break;
                case 'xlsx':
                    $reader = ReaderFactory::create(Type::XLSX);
                    break;

                default:
                    $reader = ReaderFactory::create(Type::CSV);
                    break;
            }
            $reader->open($this->upload->data('full_path'));
            $this->load->model('pit_model');
            $n = 0;
            foreach ($reader->getSheetIterator() as $sheet) {
                $rowcount = 0;
                foreach ($sheet->getRowIterator() as $row) {
                    $rowcount++;
                    if( $sucess ) {
                        if ($rowcount == 1) {
                            //Encabezado
                        } else {
                            if( $row[0] != "" ) {
                                $n++;
                                $item = [
                                    'pin'           => trim($row[0]),
                                    'phone'         => trim($row[1]),
                                    'name'          => trim($row[2]),
                                    'last'          => trim($row[3]),
                                    'created_by'    => $this->udata['id'],
                                    'created_when'  => $now,
                                ];
                                //Validamos
                                $this->form_validation->set_data($item);
                                $this->form_validation->set_rules('pin',  		"'PIN'",  	    'required|min_length[1]|max_length[10]|integer');
                                $this->form_validation->set_rules('phone',      "'Telefono'",   'required|min_length[10]|max_length[15]');
                                $this->form_validation->set_rules('name',      	"'Nombre'",     'required|min_length[3]|max_length[150]');
                                $this->form_validation->set_rules('last',      	"'Apellidos'",  'min_length[3]|max_length[150]');
                                if ($this->form_validation->run() == false){
                                    $errors = $this->form_validation->error_array();
                                    $fields = array_keys($errors);
                                    $sucess = false;
                                    $message = $errors[ $fields[0] ]." Renglon $rowcount";
                                }
                                $items[] = $item;
                            }
                        }
                    }
                }
            }
            unlink($this->upload->data('full_path'));
            if( $n > 0 ) {
                if ( $sucess )
                    $res = $this->pit_model->guardadoMasivo($items);
                else
                    $res = ['error' => "Error: ".$message];
            } else {
                $res = ['error' => 'No se encontro ningun valor valido en el archivo.' ];
            }
        }

        Header('Content-Type: application/json');
        echo json_encode($res);
	}

    public function actualizar_usuario() {
        $this->load->model('pit_model');
        $res = $this->pit_model->actualizar_usuario();

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function eliminar_redirect() {
        $id = $this->input->post('id');
        $this->load->model('pit_model');
        $res = $this->pit_model->eliminar_redirect($id);

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function valida_hora ($hora) {
        if( $hora != null && $hora != '' && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $hora) ) {
            $this->form_validation->set_message('valida_hora', "El campo de {field} debe de tener un valor de hora valido");
            return false;
        }

        return true;
    }

    public function buscarPlantilla() {
        $respu = $this->pit_model->buscarPlantilla($this->input->post());

        header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function plantillas() {
        $this->load->model("consola_model");
        $data['title']     = 'PIT Plantillas';
        $data['campanas']  = $this->datos_model->getCampanas();
        $data['plantiPit'] = $this->consola_model->getPlantillas('pit_template');
        $data['jscript']   = 'config/pit_templates';

        $this->armado->mostrar(array(
            'view' => 'config/pit_templates',
            'data' => $data,
        ));
    }

}

?>
