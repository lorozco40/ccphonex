<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Inter extends MY_Controller
{

    public function index() {
        if (empty($this->session->userdata('inter'))) {
            redirect();
        }
        $this->load->model('inter_model');
        $fu = $this->datos_model->getFullu($this->session->userdata('uid'));
        $this->armado->mostrar(array(
            'view'     => 'inter',
            'template' => 'empty',
            'data'     => [
                "opts"    =>$this->inter_model->getOpts(true),
                "uname"   =>$fu->name,
                "jscript" => 'inter',
            ],
        ));
    }

    public function continuar() {
        $data = $this->input->post();
        $this->load->model('inter_model');
        $trans = $this->inter_model->aplicatrans($data['tid']);
        if (empty($trans)) {
            $this->session->set_flashdata('errormsg', 'Error, ese rol ya se había tomado.');
            redirect('inter');
        }
        $this->session->set_userdata('inter', false);
        $fu = $this->datos_model->getFullu($this->session->userdata('uid'));

        redirect($fu->pagini);
    }

}
