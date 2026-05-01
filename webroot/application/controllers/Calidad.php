<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class Calidad extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $data['campaigns'] = $this->datos_model->getCampanas();
        $this->load->library('pagination');
        $this->load->model('calidad_model');
        $pagi = $this->pagi;
        $pagi['base_url'] = site_url('calidad');
        $pagi['total_rows'] = $this->db->count_all('quality');
        $this->pagination->initialize($pagi);
        $data['page'] = $this->uri->segment(2) ?: 0;
        $cams = $this->datos_model->getIdsFromArr($data['campaigns']);
        $data['data'] = $this->calidad_model->get_list($cams, (int)$data['page']);
        $data['pagination'] = $this->pagination->create_links();
        $data['title'] = 'Calidad';
        $data['tipos'] = [
            (object) ['value' => 'llamadas', 'text' => 'Llamadas' ],
            (object) ['value' => 'whatsapp', 'text' => 'Whatsapp' ]
        ];

        $datos = array(
            'view' => 'config/calidad',
            'data' => $data,
        );
        $this->armado->mostrar($datos);

    }

    public function crear() {
        $this->form_validation->set_rules('campaign', '"Campaña"', 'required');
        $this->form_validation->set_rules('name', '"Nombre evaluación"', 'required');
        if ($this->form_validation->run()==FALSE) {
            $errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $error = $errors[$fields[0]];
            $this->session->set_flashdata('errormsg', $error);
        } else {
            $this->load->model('calidad_model');
            if ($this->calidad_model->create($this->input->post())) {
                $this->session->set_flashdata('infomsg', 'Formulario creado con éxito.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error al crear el formulario.');
            }
        }
        redirect('calidad');
    }

    public function actualizar() {
        $this->form_validation->set_rules('id', 'ID', 'required',  array('required' => 'No puedes actualizar un formulario inexistente.'));
        $this->form_validation->set_rules('name', '"Nombre evaluación"', 'required');
        if ($this->form_validation->run()==FALSE) {
            $errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $error = $errors[$fields[0]];
            $this->session->set_flashdata('errormsg', $error);
        } else {
            $this->load->model('calidad_model');
            $update = $this->calidad_model->update();
            if( $update['success'] ) {
                $this->session->set_flashdata('infomsg', $update['message']);
            } else {
                $this->session->set_flashdata('errormsg', $update['message']);
            }
        }
        redirect('calidad');
    }

    public function campos($id_quality, $pagina = "nada") {
        if ($pagina == "nada") redirect("calidad/campos/$id_quality/0");
        if (empty($id_quality)) {
            $this->session->set_flashdata('infomsg', 'Elige formulario para modificar.');
            redirect("calidad");
        }
        $this->load->library('pagination');

        $pagi = $this->pagi;
        $pagi['base_url'] = site_url('calidad/campos/'.$id_quality);
        $this->db->where('id_quality', $id_quality);
        $pagi['total_rows'] = $this->db->count_all_results('quality_fields');

        $this->pagination->initialize($pagi);
        $data['page'] = $pagina;
        $this->load->model('calidad_model');
        $data['data'] = $this->calidad_model->get_fields($id_quality, (int)$data['page'], $pagi['per_page']);
        $data['quality'] = $this->calidad_model->get_one($id_quality);
        $data['is_blocked'] = $this->is_blocked($data['quality']);
        $data['has_comment_field'] = $this->calidad_model->has_comment_field($id_quality);
        $data['suma_weight'] = $this->calidad_model->SumaWeight($id_quality);
        $data['pagination'] = $this->pagination->create_links();
        $data['title'] = 'Preguntas';
        $data['jscript'] = 'config/calidad_campos';
        $datos = array(
            'view' => 'config/calidad_campos',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

    public function addfieldcoment() {
        $success = true;
        $id_quality = (int)$this->input->post('id_quality');
        $this->load->model('calidad_model');
        // Validamos que la cedula exista
        $quality = $this->calidad_model->get_one($id_quality);
        if( !$quality ) {
            $success = false;
            $this->session->set_flashdata('errormsg', 'Error: no se encontro la cédula seleccionada.');
        }
        // Validamos que la cedula no este bloqueada
        if( $success && $this->is_blocked($quality) ) {
            $success = false;
            $this->session->set_flashdata('errormsg', 'Error: esta cédula esta bloqueada. Ya no puede ser modificada.');
        }
        // Validamos si la cédula tiene el campo Comentario
        if( $success ) {
            $has_coment = $this->calidad_model->has_comment_field($id_quality);
            if($has_coment) {
                $success = false;
                $this->session->set_flashdata('errormsg', 'Error: ya existe un campo de "Comentario".');
            }
        }
        // Agregamos el campo Comentario
        if( $success ) {
            if( $this->calidad_model->add_comment_field($id_quality) )
                $this->session->set_flashdata('infomsg', 'Campo "Comentario" agregado con éxito.');
            else
                $this->session->set_flashdata('errormsg', 'Error: no se pudo agregar el campo "Comentario".');
        }

        redirect('calidad/campos/'.$this->input->post('id_quality')."/0");
    }

    private function is_blocked($quality) {
        $this->load->model('calidad_model');
        $n = $this->calidad_model->registros_calificados($quality);

        return ( $n == 0 ) ? false : true;
    }

    public function deletefield() {
        $success = true;
        $id_quality = 0;
        $this->load->model('calidad_model');
        $id = (int)$this->input->post('id_quality_fields');
        //Obtenemos el registro de la pregunta
        $quality_fieds = $this->calidad_model->get_quality_fields($id);
        if( !$quality_fieds ) {
            $success = false;
            $this->session->set_flashdata('errormsg', 'Error: no se encontro la pregunta seleccionada.');
        }
        // Validamos que la cedula exista
        if( $success ) {
            $id_quality = $quality_fieds->id_quality;
            $quality = $this->calidad_model->get_one($id_quality);
            if( !$quality ) {
                $success = false;
                $this->session->set_flashdata('errormsg', 'Error: no se encontro la cédula seleccionada.');
            }
        }
        // Validamos que la cedula no este bloqueada
        if( $success && $this->is_blocked($quality) ) {
            $success = false;
            $this->session->set_flashdata('errormsg', 'Error: esta cédula esta bloqueada. Ya no puede ser modificada.');
        }
        // Eliminamos el campo
        if( $success ) {
            $del = $this->calidad_model->delete_field($id, $id_quality);
            if( $del['success'] )
                $this->session->set_flashdata('infomsg', $del['message']);
            else
                $this->session->set_flashdata('errormsg', $del['message']);
        }

        redirect('calidad/campos/'.$id_quality."/0");
    }

    public function crearc() {
        $success = true;
        $this->form_validation->set_rules('question', 'Pregunta', 'required');
        $this->form_validation->set_rules('weight', 'Valor', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'Debe ingresar una pregunta.');
        } else {
            $this->load->model('calidad_model');
            // Validamos que la cedula exista
            $id_quality = (int)$this->input->post('id_quality');
            $quality = $this->calidad_model->get_one($id_quality);
            if( !$quality ) {
                $success = false;
                $this->session->set_flashdata('errormsg', 'Error: no se encontro la cédula seleccionada.');
            }
            // Validamos que la cedula no este bloqueada
            if( $success && $this->is_blocked($quality) ) {
                $success = false;
                $this->session->set_flashdata('errormsg', 'Error: esta cédula esta bloqueada. Ya no puede ser modificada.');
            }
            if( $success ) {
                if( $this->calidad_model->validaPonderacionMenorIgualaCien() ){
                    $ins = $this->calidad_model->createc();
                    if ( $ins['success'] ) {
                        $this->session->set_flashdata('infomsg',  $ins['message']);
                    } else {
                        $this->session->set_flashdata('errormsg', $ins['message']);
                    }
                }else{
                    $this->session->set_flashdata('errormsg', 'La sumatoria de la ponderación es mayor de 100.');
                }
            }
        }
        redirect('calidad/campos/'.$this->input->post('id_quality')."/0");
    }

    public function actualizarc() {
        $success = true;
        $this->form_validation->set_rules('question', 'Pregunta', 'required');
        $this->form_validation->set_rules('weight', 'Valor', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'Pregunta requerida.');
        } else {
            $this->load->model('calidad_model');
            // Validamos que la cedula exista
            $id_quality = (int)$this->input->post('id_quality');
            $quality = $this->calidad_model->get_one($id_quality);
            if( !$quality ) {
                $success = false;
                $this->session->set_flashdata('errormsg', 'Error: no se encontro la cédula seleccionada.');
            }
            // Validamos que la cedula no este bloqueada
            if( $success && $this->is_blocked($quality) ) {
                $success = false;
                $this->session->set_flashdata('errormsg', 'Error: esta cédula esta bloqueada. Ya no puede ser modificada.');
            }
            if( $success ) {
                if( $this->calidad_model->validaPonderacionMenorIgualaCien() ){
                    $upd = $this->calidad_model->updatec();
                    if( $upd['success'] ) {
                        $this->session->set_flashdata('infomsg', $upd['message']);
                    } else {
                        $this->session->set_flashdata('errormsg', $upd['message']);
                    }
                } else {
                    $this->session->set_flashdata('errormsg', 'La sumatoria de la ponderación es mayor de 100.');
                }
            }
        }
        redirect('calidad/campos/'.$this->input->post('id_quality')."/0");
    }

    public function traercampos() {
        $this->load->model("calidad_model");
        $datos = $this->calidad_model->traercampos($this->input->post("cola"));

        Header('Content-Type: application/json');
        echo json_encode($datos);
    }

    function traercedulawhats() {
        $this->load->model("calidad_model");
        $datos = $this->calidad_model->traercedulawhats($this->input->post("cam"));

        Header('Content-Type: application/json');
        echo json_encode($datos);
    }

    public function reporte() {
        $this->load->model('calidad_model');
        $data['campanas']   = $this->datos_model->getCampanas();
        $idsCampaigns       = $this->datos_model->getIdsFromArr($data['campanas']);
        $data['agentes']    = $this->datos_model->getRelUsers(["cam"=>$data['campanas'], "act"=>FALSE]);
        $data['tipeval']    = $this->calidad_model->traertipeval($idsCampaigns);
        $data['evaluacion'] = $this->calidad_model->traevaluacion();
        $data['title']      = 'Calidad';

        $datos = array(
            'view' => 'reportes/calidad_reporte',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

    public function reporte_whatsapp() {
        $this->load->model('calidad_model');
        $data['campanas']   = $this->datos_model->getCampanas();
        $idsCampaigns       = $this->datos_model->getIdsFromArr($data['campanas']);
        $data['agentes']    = $this->datos_model->getRelUsers(["cam"=>$data['campanas'], "act"=>FALSE]);
        $data['tipeval']    = $this->calidad_model->traertipeval($idsCampaigns, 'whatsapp');
        $data['evaluacion'] = $this->calidad_model->traevaluacion();
        $data['title']      = 'Calidad';

        $datos = array(
            'view' => 'reportes/calidad_reporte_whatsapp',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

    public function listcalidadwhatsapp() {
        $data['page'] = $this->input->post("page") ?: 0;
        $pagi = $this->pagi;

        $this->load->model('calidad_model');
        $data = $this->input->post();
        $data['ini'] = (int)$data['page'];
        $data['lim'] = $pagi['per_page'];

        $data = $this->calidad_model->evalcalidad_whatsapp($data);

        $pagi['base_url'] = site_url('calidad/reporte');
        $pagi['total_rows'] = $data['cuenta'];
        $pagi['cur_page'] = $this->input->post("page");
        $pagi['last_link'] = ceil($data['cuenta']/(REGS_POR_PAG));

        $this->load->library('pagination', $pagi);
        $data['pagination'] = $this->pagination->create_links();

        Header('Content-Type: application/jason');
        echo json_encode($data);
    }

    public function guardareval() {
        $this->load->model('calidad_model');
        if ($this->calidad_model->guardareval($this->input->post())) {
            $this->session->set_flashdata('infomsg', 'La evaluación se guardo con exito.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al crear la cuenta. Registro duplicado.');
        }
        redirect($this->input->post('redir'));
    }

    function excelcalidad() {
        $this->load->model('calidad_model');
        $data = $this->calidad_model->excelrepo_cal($this->input->post());

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
            echo "Parámetros sin datos, cierra e intenta nuevamente";
        }
    }

    function excelcalidadwa() {
        $this->load->model('calidad_model');
        $data = $this->calidad_model->excelrepo_cal_whatsapp($this->input->post());
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
            echo "Parámetros sin datos, cierra e intenta nuevamente";
        }
    }

    public function listcalidad() {
        $data['page'] = $this->input->post("page") ?: 0;
        $pagi = $this->pagi;

        $this->load->model('calidad_model');
        $data = $this->input->post();
        $data['ini'] = (int)$data['page'];
        $data['lim'] = $pagi['per_page'];

        $data = $this->calidad_model->evalcalidad($data);

        $pagi['base_url'] = site_url('calidad/reporte');
        $pagi['total_rows'] = $data['cuenta'];
        $pagi['cur_page'] = $this->input->post("page");
        $pagi['last_link'] = ceil($data['cuenta']/(REGS_POR_PAG));

        $this->load->library('pagination', $pagi);
        $data['pagination'] = $this->pagination->create_links();

        Header('Content-Type: application/jason');
        echo json_encode($data);
    }

    public function whatsapp() {
        $data['campanas']   = $this->datos_model->getCampanas();
        $data['javascript'][] = 'calid_camposadwa';
        $data['title'] = 'Evaluación Whatsapp';
        $datos = array(
            'view' => 'calidad/whatsapp',
            'data' => $data,
        );

        $this->armado->mostrar($datos);
    }

    /* Se aplican los filtros para obtener los contactos */
    public function wa_filter() {
        $this->load->model('calidad_model');
        
        $evento      = $this->input->post('evento'); //cuenta, agente, wc_id
        $min         = $this->standard_date($this->input->post('min'));
        $max         = $this->standard_date($this->input->post('max'));
        $id_campaign = $this->input->post('id_campaign');
        $id_wc       = $this->input->post('id_wc'); //id Whatsapp cuenta
        $id_agente   = $this->input->post('id_agente'); //id Whatsapp cuenta
        $bus         = $this->input->post('bus'); //palabra a buscar de contactos
        // Obtenemos los datos de whatsapp cuentas
        if( in_array($evento, ['id_campaign']) ) {
            $cuentas = $this->calidad_model->whatsapp_cuentas($id_campaign);
            if( count($cuentas) == 0 ) {
                $cuentas = [
                    (object)['id' => '0', 'cuenta' => 0, 'nombre' => 'No hay cuentas disponibles']
                ];
                $id_wc = '0';
            } else {
                // Validamos si la cuenta preseleccionado existe en esta nueva lista
                $encontrado = false;
                foreach($cuentas as $row) {
                    if( $row->id == $id_wc) { $encontrado = true; break; }
                }
                // No se encontro la cuenta preseleccionada en esta nueva lista, asi que seleccionamos la primera
                if( !$encontrado )
                    $id_wc = $cuentas[0]->id;
            }
        } else {
            $cuentas = false;
        }
        // Obtenemos los datos de agentes
        if( in_array($evento, ['id_campaign', 'id_wc']) ) {
            $agentes = $this->calidad_model->whatsapp_agentes($id_campaign);
            if( count($agentes) == 0 ) {
                $agentes = [
                    (object)['id' => '0', 'nombre' => 'Sin agentes']
                ];
                $id_agente = '0';
            } else {
                // Validamos si el agente preseleccionado existe en esta nueva lista
                $encontrado = false;
                foreach($agentes as $row) {
                    if( $row->id == $id_agente) { $encontrado = true; break; }
                }
                // No se encontro el agente preseleccionado en esta nueva lista, asi que seleccionamos el primero
                if( !$encontrado )
                    $id_agente = $agentes[0]->id;
            }
        } else {
            $agentes = false;
        }
        $contactos = $this->calidad_model->whatsapp_contactos($min, $max, $id_wc, $id_agente, $bus);
        $this->json([
            'evento'        => $evento,
            'min'           => $min,
            'max'           => $max,
            'id_campaign'   => $id_campaign,
            'id_agente'     => $id_agente,
            'id_wc'         => $id_wc,
            'cuentas'       => $cuentas,
            'agentes'       => $agentes,
            'contactos'     => $contactos,
        ]);
    }

    // Convierte la fecha capturada desde el datepicker al estandar para poder ser procesado, en caso de no llegar vacio se le asigna la fecha actual
    public function standard_date($fecha) {
        $fecha = $fecha ?: date('Y-m-d');
        
        return convierte($fecha, $this->idfor);
    }

    public function wa_conversation() {
        $this->load->model('whatsapp_model');
        $data = [
            'wc'              => $this->input->post('id_wc'),
            'cid'             => $this->input->post('id_contacto'),
            'id_agente'       => $this->input->post('id_agente'),
            'max'             => $this->standard_date($this->input->post('max')),
            'min'             => $this->standard_date($this->input->post('min')),
            'current_session' => $this->input->post('current_session'),
            'toid'            => false,
        ];
        $this->json($this->whatsapp_model->calidad_traer_data($data));
    }

    public function wa_save_ecm() {
        $this->load->model('calidad_model');
        $data = $this->input->post();
		$this->form_validation->set_rules('id',  	            "'id'",  	        'required|integer');
		$this->form_validation->set_rules('id_whatsapp_entry',  "'whatsapp entry'", 'required|integer');
		$this->form_validation->set_rules('rating',             "'Calificación'",   'required|integer|max_length[100]');
		$this->form_validation->set_rules('comment',  		    "'Comentario'",     'max_length[250]');
        //VALIDAMOS
		if ($this->form_validation->run() == FALSE){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $resp = [
                "msg" => $errors[ $fields[0] ],
                "success" => false,
            ];
		}
		else{
            $id = (int)$this->input->post('id');
            $resp = $this->calidad_model->wa_save_ecm($id, $data);
        }

        $this->json($resp);
    }

    public function wa_traercampos() {
        $this->load->model("calidad_model");
        $id_campaign = $this->input->post('id_campaign');
        $resp = [];
        $resp['data'] = $this->calidad_model->traercampos('', 'whatsapp', $id_campaign);
        $resp['info'] = $this->calidad_model->info_cedula('whatsapp', $id_campaign);

        $this->json($resp);
    }

    public function wa_save_ecs() {
        $this->load->model('calidad_model');
        $resp = $this->calidad_model->wa_save_ecs($this->input->post());
        if (!empty($this->input->post('redir'))) {
            redirect($this->input->post('redir'));
        } else {
            $this->json($resp);
        }
    }
    
    private function json($data) {
        Header('Content-Type: application/json');
        echo json_encode($data);
    }
}
