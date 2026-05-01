<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Crm extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model('crm_model');
    }

    public function index() {
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['agentes']  = $this->datos_model->getRelUsers(["cam"=>$data['campanas']]);
        $data['jscript']  = "config/crm";
        $data['title']    = "Tickets";

        $this->armado->mostrar(array(
            'view' => 'config/crm',
            'data' => $data,
        ));
    }

    public function crear() {
        $this->form_validation->set_rules('name', 'Nombre', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'El nombre del CRM es requerido para identificarlo.');
        } else {
            if ($this->crm_model->crear($this->input->post())) {
                $this->session->set_flashdata('infomsg', 'CRM creado con éxito.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error al crear el CRM.');
            }
        }

        redirect('crm/admin');
    }

    public function eliminar(int $fid = 0) {
        $this->load->model("form_model");
        $res = $this->form_model->eliminaForm($fid);
        if ($res) {
            $this->session->set_flashdata('infomsg', 'CRM ' . $fid . ' eliminado');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al eliminar el CRM ' . $fid);
        }

        redirect('crm/admin');
    }

    public function actualizar() {
        $this->form_validation->set_rules('id', 'ID', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'No puedes actualizar un formulario inexistente.');
        } else {
            $this->load->model('form_model');
            if ($this->form_model->update()) {
                $this->session->set_flashdata('infomsg', 'Fomulario actualizado con éxito.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error al actualizar el formulario.');
            }
        }

        redirect('crm/admin');
    }

    public function admin() {
        $data['campaigns']   = $this->datos_model->getCampanas();
        $this->load->model('form_model');
        $this->load->model('email_model');
        $pagi                = $this->pagi;
        $pagi['base_url']    = site_url('crm/admin');
        $pagi['uri_segment'] = 3;
        $cams                = $this->datos_model->getIdsFromArr($data['campaigns']);
        $pagi['total_rows']  = $this->form_model->countByCams($cams, 1);
        $this->load->library('pagination', $pagi);
        $data['pagination']  = $this->pagination->create_links();
        $data['page']        = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['data']        = $this->form_model->get_list($cams, (int)$data['page'], 1);
        $data['cuentas']     = $this->email_model->getCuentas($cams, 1);
        $data['title']       = 'Formularios';
        $data['jscript']     = 'config/crm_admin';

        $this->armado->mostrar(array(
            'view' => 'config/form',
            'data' => $data,
        ));
    }

    public function campos(int $idForm = 0) {
        if (empty($idForm)) {
            $this->session->set_flashdata('infomsg', 'Elige un formulario para modificar.');
            redirect("crm/admin");
            exit;
        }
        $this->load->model('form_model');
        $data            = $this->form_model->get_one($idForm);
        $data['data']    = $this->form_model->get_fields($idForm);
        $data['title']   = 'Campos de formulario';
        $data['jscript'] = 'config/form_campos';

        $this->armado->mostrar(array(
            'view' => 'config/form_campos',
            'data' => $data,
        ));
    }

    public function guardar() {
        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->guardar());
    }

    public function guardarcali() {
        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->guardarcali());
    }

    public function guardararchivo() {
        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->guardararchivo());
    }

    public function cerrar() {
        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->cerrar());
    }

    public function reabrir() {
        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->reabrir());
    }

    public function transferir() {
        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->transferir());
    }

    public function traerforms() {
        $data = $this->input->post();

        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->traerforms($data));
    }

    public function crmstatus_data($value='') {
        $data = $this->input->post();

        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->crmstatus($data));
    }

    public function traerdata() {
        $data               = $this->crm_model->traerdata();
        $pagi               = $this->pagi;
        $pagi['total_rows'] = $data['cuenta'];
        $pagi['cur_page']   = $data['pag'];
        $pagi['last_link']  = ceil($data['cuenta']/REGS_POR_PAG);
        $this->load->library('pagination', $pagi);
        $data['pagination'] = $this->pagination->create_links();

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function activasem() {
        $data = $this->input->post();
        if (empty($data['fid'])) {
            $this->session->set_flashdata('errormsg', 'Error de datos del CRM.');
        } else {
            $query = $this->db->query("INSERT INTO crm_light (id_form, created_by) values (?, ?)",
                [$data['fid'], $this->session->userdata('uid')]
            );
            if ($query) {
                $this->session->set_flashdata('infomsg', 'Semáforo activado');
            } else {
                $this->session->set_flashdata('errormsg', 'Error al activar el semáforo');
            }
            redirect('form/campos/'.$data['fid']);
            exit;
        }

        redirect('crm/admin');
    }

    public function savesem() {
        Header('Content-Type: application/json');
        echo json_encode($this->crm_model->savesem());
    }

    public function reporte_tickets() {
        $data['cid']                  = $this->udata['campanas'];
        $data['massel']['Formulario'] = $this->crm_model->traerforms($data);
        $data['massel']['Estatus'] = [
            (object)['id'=>'0',          'name'=>'Todos ...'],
            (object)['id'=>'Abierto',    'name'=>'Abiertos'],
            (object)['id'=>'En proceso', 'name'=>'En proceso'],
            (object)['id'=>'Pausado',    'name'=>'Pausados'],
            (object)['id'=>'Cerrado',    'name'=>'Cerrados'],
        ];
        $data['agentes']              = $this->datos_model->getRelUsers(["act"=>FALSE]);
        $data['modelo']               = 'crm';
        $data['title']                = 'Tickets';
        $data['jscript']             = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    function preview_email($enviar = 0) {
        // ['fid'=>$data['id_form'], 'id'=>$data['id'], 'pid'=>$pid]
        $data = $this->input->get();
        $data['previewem'] = $enviar == 1 ? false : true;
        $this->load->model('crm_model');
        $res = $this->crm_model->informar($data);

        echo $res['error'] ?? $res['msg'] ?? '';
    }

    public function reporte_status() {
        $data['title']     = 'Tickets status';
        $data['cid']       = $this->udata['campanas'];
        $data['forms']     = $this->crm_model->traerforms($data);
        $data['jscript'][] = "jspdf.min";
        $data['jscript'][] = "html2canvas.min";
        $data['jscript'][] = "reportes/crmstatus";
        $data['extjs']     = "//www.gstatic.com/charts/loader.js";
        $this->armado->mostrar(array(
            'view' => 'reportes/crmstatus',
            'data' => $data,
        ));
    }

    public function reporte_tickapdf() {
        $data['jscript'] = "reportes/tickapdf";
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['forms']    = $this->crm_model->traerforms();
        $data['plant']    = $this->crm_model->traerplantillas();
        $data['title']    = 'Tickets a PDF';
        $this->armado->mostrar(array(
            'view' => 'reportes/crm_tickapdf',
            'data' => $data,
        ));
    }

    public function reporte_detalle() {
        $data['cid']     = $this->udata['campanas'];
        $data['massel']['Formulario'] = $this->crm_model->traerforms($data);
        $data['massel']['Estatus'] = [
            (object)['id'=>'0',          'name'=>'Todos ...'],
            (object)['id'=>'Abierto',    'name'=>'Abiertos'],
            (object)['id'=>'En proceso', 'name'=>'En proceso'],
            (object)['id'=>'Pausado',    'name'=>'Pausados'],
            (object)['id'=>'Cerrado',    'name'=>'Cerrados'],
        ];
        $data['masinput'] = ['TicketID'=>'bus'];
        $data['modelo']   = 'crm';
        $data['title']    = 'Tickets detalle';
        $data['jscript']  = 'reportes/reporte';

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function tickapdf() {
        $data = $this->input->post();
        $data['tickets'] = $this->crm_model->get_tickapdf($data);

        //Evaluamos la ubicacion de las plantillas
        if( strpos($data['plantilla'], 'crm/') !== false )
            $ruta = '';
        else
            $ruta = 'reportes/';

        if (file_exists(APPPATH."views/$ruta".$data['plantilla'].".php") && !empty($data['tickets'])) {
            $this->load->view($ruta.$data['plantilla'], $data);
        } else {
            if (!file_exists(APPPATH."views/$ruta".$data['plantilla'].".php") )
                die("No esta disponible esta plantilla!");
            die("No existen datos, cerrar ventana e intentar de nuevo!");
        }
    }

    public function actualizarCuentaEmail() {
        $success = false;
        $mensajes = "";
        $this->form_validation->set_rules('id', 'ID', 'required');
        if ( $this->form_validation->run() == FALSE ) {
            $mensajes = "No puedes actualizar un CRM inexistente.";
        } else {
            $data = $this->input->post();
            $this->load->model('crm_model');
            if ( $this->crm_model->actualizarCuentaEmail($data) ) {
                $success  = true;
                $mensajes = "CRM actualizado con éxito.";
            } else {
                $mensajes = "Error al actualizar el CRM.";
            }
        }


        $json = [];
        $json["success"]  = $success;
        $json["mensajes"] = $mensajes;

        header('Content-Type: application/json');
        echo json_encode($json);
    }

}
