<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public $dtfor; // date time format
    public $dfor; // date format
    public $idfor; // input date format
    public $udata; // Los datos del usuario que está conectado
    private $unprotected = array("acceso", "welcome", "videollamada", "pde", "debug");

    protected $pagi = array(
        'per_page'        => REGS_POR_PAG,
        'uri_segment'     => 2,
        'full_tag_open'   => '<ul class="pagination">',
        'full_tag_close'  => '</ul>',
        'first_link'      => 1,
        'last_link'       => 'Fin',
        'first_tag_open'  => '<li>',
        'first_tag_close' => '</li>',
        'prev_link'       => '&laquo',
        'prev_tag_open'   => '<li class="prev">',
        'prev_tag_close'  => '</li>',
        'next_link'       => '&raquo',
        'next_tag_open'   => '<li>',
        'next_tag_close'  => '</li>',
        'attributes'      => array('class' => 'page-link'),
        'last_tag_open'   => '<li>',
        'last_tag_close'  => '</li>',
        'cur_tag_open'    => '<li class="page-item disabled"><a class="page-link" href="#">',
        'cur_tag_close'   => '</a></li>',
        'num_tag_open'    => '<li class="page-item">',
        'num_tag_close'   => '</li>'
    );

    public function __construct() {
        parent::__construct();
        $cont = $this->router->class;
        $uid  = $this->session->userdata("uid");
        if ($uid) {
            if ($this->session->userdata("inter") && $cont != "inter") {
                redirect("inter");
            }
            $sispar      = $this->datos_model->getParams("sistema");
            $this->dtfor = $sispar->FormatoFechaMysql;
            $this->idfor = $sispar->FormatoFechaInput;
            $this->dfor  = explode(" ", $this->dtfor)[0];
            $this->udata = $this->datos_model->getUserData($uid);
            if (empty($this->udata['campanas']) && !in_array($cont, $this->unprotected)) {
                $this->session->set_flashdata('errormsg', 'No tienes campañas asignadas.');
                redirect('acceso/logout');
            }
        }
        if (!in_array($cont, $this->unprotected)) {
            if (!$uid) {
                $this->session->set_flashdata('errormsg', 'Por favor ingresa con tu usuario y contraseña.');
                redirect();
            }
            $this->load->model("usuario_model");
            $this->usuario_model->get_permiso();
        }
    }

}
