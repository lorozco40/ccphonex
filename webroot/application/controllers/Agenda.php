<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Reader\ReaderFactory;

class Agenda extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    function index() {
        $this->load->model("agenda_model");
        $data['camposagen'] = $this->agenda_model->getCamposAgenda();
        $data['campanas']   = $this->datos_model->getCampanas();
        $data['campanas_aux'] = $data['campanas'];
        $data['title']      = "Agenda";
        $data['jscript']    = 'config/agenda';
        $data['agentes']    = $this->datos_model->getRelUsers(["cam"=>$data['campanas']]);
        $this->armado->mostrar(array(
            'view' => 'config/agenda',
            'data' => $data,
        ));
    }

    public function guardar() {
        $this->load->model("agenda_model");
        $data = $this->input->post();
        if(!empty($data['id'])) {
            $res = $this->agenda_model->actualizar($data);
        } else {
            $res = $this->agenda_model->crear($data);
        }

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function guardarArchivo() {
        $items = [];
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
            $this->load->model('agenda_model');
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
                                $id_campaign = trim($this->input->post('id_campaign'));
                                $id_user = trim($this->input->post('id_user'));
                                $id_user = ($id_user == "") ? null : $id_user;
                                $name = trim($row[0]);
                                $last = trim($row[1]);
                                $phone = trim($row[2]);
                                $email = trim($row[3]);
                                $calle = trim($row[4]);
                                $numero = trim($row[5]);
                                $interior = trim($row[6]);
                                $colonia = trim($row[7]);
                                $dele_muni = trim($row[8]);
                                $ciudad = trim($row[9]);
                                $cp = trim($row[10]);
                                $pais = trim($row[11]);
                                $facebook = trim($row[12]);
                                $twitter = trim($row[13]);
                                $linkedin = trim($row[14]);
                                //Validamos
                                if( $sucess && $id_campaign == "" ) { $sucess = false; $message = "El campo Campaña es requerido"; }
                                if( $sucess && strlen($name) > 25 ) { $sucess = false; $message = "El campo 'Nombre' no debe contener mas de 25 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($last) > 50 ) { $sucess = false; $message = "El campo 'Apellido' no debe contener mas de 50 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($phone) > 30 ) { $sucess = false; $message = "El campo 'Telefono' no debe contener mas de 30 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($email) > 90 ) { $sucess = false; $message = "El campo 'e-mail' no debe contener mas de 90 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($calle) > 50 ) { $sucess = false; $message = "El campo 'Calle' no debe contener mas de 50 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($numero) > 10 ) { $sucess = false; $message = "El campo 'Numero' no debe contener mas de 10 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($interior) > 10 ) { $sucess = false; $message = "El campo 'Interior' no debe contener mas de 10 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($colonia) > 100 ) { $sucess = false; $message = "El campo 'Colonia' no debe contener mas de 100 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($dele_muni) > 50 ) { $sucess = false; $message = "El campo 'Municipio' no debe contener mas de 50 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($ciudad) > 30 ) { $sucess = false; $message = "El campo 'Ciudad' no debe contener mas de 30 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($cp) > 5 ) { $sucess = false; $message = "El campo 'C.P.' no debe contener mas de 5 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($pais) > 20 ) { $sucess = false; $message = "El campo 'Pais' no debe contener mas de 20 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($facebook) > 40 ) { $sucess = false; $message = "El campo 'Facebook' no debe contener mas de 40 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($twitter) > 40 ) { $sucess = false; $message = "El campo 'Twitter' no debe contener mas de 40 caracteres. Renglon $rowcount"; }
                                if( $sucess && strlen($linkedin) > 40 ) { $sucess = false; $message = "El campo 'Linkedin' no debe contener mas de 40 caracteres. Renglon $rowcount"; }
                                $items[] = [
                                    'id_campaign' => $id_campaign,
                                    'id_user'   =>  $id_user,
                                    'name'      => $name,
                                    'last'      => $last,
                                    'phone'     => $phone,
                                    'email'     => $email,
                                    'calle'     => $calle,
                                    'numero'    => $numero,
                                    'interior'  => $interior,
                                    'colonia'   => $colonia,
                                    'dele_muni' => $dele_muni,
                                    'ciudad'    => $ciudad,
                                    'cp'        => $cp,
                                    'pais'      => $pais,
                                    'facebook'  => $facebook,
                                    'twitter'   => $twitter,
                                    'linkedin'  => $linkedin,
                                ];
                            }
                        }
                    }
                }
            }
            unlink($this->upload->data('full_path'));
            if( $n > 0 ) {
                if ( $sucess )
                    $res = $this->agenda_model->guardadoMasivo($items);
                else
                    $res = ['error' => "Error: ".$message];
            } else {
                $res = ['error' => 'No se encontro ningun valor valido en el archivo.' ];
            }
        }

        Header('Content-Type: application/json');
        echo json_encode($res);
	}

    public function borrar() {
        $this->load->model("agenda_model");
        if ($this->agenda_model->borrar($this->input->post())) {
            $this->session->set_flashdata('infomsg', 'Entrada en agenda eliminada.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al eliminar entrada.');
        }
        redirect('agenda');
    }

    public function traerporid() {
        $this->load->model("agenda_model");

        Header('Content-Type: application/json');
        echo json_encode($this->agenda_model->traerporid($this->input->post("id")));
    }

    public function acsv() {
        $this->load->model('agenda_model');
        $data = $this->agenda_model->get_all();
        $data['campos'] = array_map("traduce", $data['campos']);
        if (count($data["data"])>0) {
            $writer = WriterFactory::create(Type::CSV);
            $filename = 'agenda.csv';
            $writer->openToBrowser($filename);
            $writer->addRow($data["campos"]);
            $writer->addRows($data["data"]);
            $writer->close();
        } else {
            echo "Sin datos con esos parámetros, cierra la ventana e intenta nuevamente";
        }
    }

    public function buscarAgenda() {
        $data = $this->input->post();

        $this->load->model("agenda_model");
        $data = $this->agenda_model->buscarAgenda($data);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

}
