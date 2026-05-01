<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permisos extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model("permisos_model");
    }

    public function index() {
        $this->load->library('pagination');
        $pagi               = $this->pagi;
        $pagi['base_url']   = site_url('permisos');
        $pagi['total_rows'] = $this->permisos_model->cuenta();
        $this->pagination->initialize($pagi);

        $data['page']       = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data['permisos']   = $this->permisos_model->getPage((int)$data['page'], $pagi['per_page']);
        $data['pagination'] = $this->pagination->create_links();
        $data["title"]      = "Permisos";

        $this->armado->mostrar(array(
            'view' => 'config/permisos',
            'data' => $data,
        ));
    }

    public function crear() {
        $this->form_validation->set_rules('eti', 'Etiqueta', 'required');
        $this->form_validation->set_rules('val', 'Valor', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'Error al crear el permiso. Datos no válidos.');
        } else {
            if ($this->permisos_model->create($_POST)) {
                $this->session->set_flashdata('infomsg', 'Acción <strong>'.$_POST['eti'].'</strong> protegida.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error al protetger la ruta.');
            }
        }
        redirect("permisos");
    }

    public function actualizar() {
        if ($this->permisos_model->update($this->input->post())) {
            $this->session->set_flashdata('infomsg', 'Registro actualizado.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al actualizar.');
        }
        redirect("permisos");
    }

    public function borrar() {
        if ($this->permisos_model->delete($this->input->post("id"))) {
            $this->session->set_flashdata('infomsg', 'Acción desprotegida.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al quitar la protección.');
        }
        redirect("permisos");
    }

}

?>
