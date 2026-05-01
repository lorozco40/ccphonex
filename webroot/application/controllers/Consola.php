<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Consola extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index($id_client = null) {
        $this->load->model('consola_model');
        $this->load->model('desp_model');
        $data = $this->consola_model->getIndexData();
        $data['title'] = 'Consola';
        $data['datos'] = $this->session->flashdata('contacto');
        if(!empty($data['datos']->telefono)) {
            $data['telefono'] = $data['datos']->telefono;
            unset($data['datos']->telefono);
        };
        //valida_columna_en_tabla($table, $column)
        $data['compl_text_busq'] = '';
        if( isset($data['id_desp']) && $this->desp_model->valida_columna_en_tabla('disp_'.$data['id_desp'], 'cliente') == 1 ) 
            $data['compl_text_busq'] = ', cliente';
        $armado = array(
            'view'      => 'consola/consola',
            'data'      => $data,
        );
        if (!empty($this->udata['exten'])) {
            $armado['template']  = 'two_col_left';
            $armado['view_left'] = 'sideNavPhone';
        }

        $this->armado->mostrar($armado);
    }

    public function formulario() {
        $data = $this->input->post();
        $this->load->model("form_model");
        $data = $this->form_model->get_form($data["cid"], $data, 'form', $data["fid"]);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function formsearch() {
        $cid = $this->input->post('cid');
        $fid = $this->input->post('fid');
        $bus = $this->input->post('bus');
        $pag = (int)$this->input->post('pag') ?: 0;
        $rpp = (int)$this->input->post('rpp') ?: 20;
        $this->load->model("form_model");
        $data = $this->form_model->get_list_forms($cid, $fid, $bus, $pag, $rpp);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    // Retorna true o false segun sea el caso, de que el formulario contenga o no campos searchables
    public function vsearchforms() {
        $fid = (int) $this->input->post('fid');
        $this->load->model("form_model");
        $rows = $this->form_model->searchable_fields($fid);
        $r = (count($rows) > 0) ? true : false;

        Header('Content-Type: application/json');
        echo json_encode($r);
    }

    public function get_client_form() {
        $this->load->model("agentes_model");
        $name = trim($this->input->post('name'));
        $id_form = $this->input->post('fid');
        $id_client = $this->agentes_model->getCliFormXName($id_form, $name);
        
        Header('Content-Type: application/json');
        echo json_encode($id_client);
    }

    public function datosLlamada() {
        set_time_limit(0);
        $this->load->model('consola_model');
        $data = $this->consola_model->getDatosLlamada();

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function datosLlamadaSal() {
        set_time_limit(0);
        $this->load->model('consola_model');
        $data = $this->consola_model->getDatosLlamadaSal();

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function user_activity() { // Ajax recibe por POST actividad y estatus
        if (null !== $this->input->post("activity")) {
            $status = (null !== $this->input->post('status')) ? (int)$this->input->post('status') : false;
            $this->datos_model->user_activity($this->input->post("activity"), $status);
            return true;
        }
        return false;
    }

    public function confbridge() {
        $data = shell_exec('/usr/sbin/asterisk -rx "confbridge list '.$this->input->post("room").'"');
        $data = (!empty($data)) ? $data : "Channel                        Flags  User Profile     Bridge Profile   Menu             CallerID
============================== ====== ================ ================ ================ ================";
        $data = explode("\n", $data);
        $res = "<h3>Conferencia</h3><table class='table table-responsive'>";
        if (count($data)>=3) {
            foreach ($data as $fila => $linea) {
                if ($fila>=2 && strlen($linea)>10) {
                    // quita todos los espacios 'sobrantes' dos o más y principio y fin
                    $linea = trim(preg_replace('/( ){2,}/u',' ',$linea));
                    $linea = explode(" ", $linea);
                    $ext = (isset($linea[5])) ? $linea[5] : $linea[4];
                    $res .= "<tr><td>".$ext."</td><td><button class='cbhangbtn btn btn-info' data-room='".
                        $this->input->post("room")."' data-chan='".$linea[0]."'>Colgar</button></td></tr>";
                }
            }
            $res .= "</table>";
        } else {
            $res = "<h3>Conferencia</h3><p>Ningun asistente, esperando</p><br /><button class='btn btn-primary' id='closeconfadmin'>Cerrar</button>";
        }

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function confbridgehang() {
        $data = shell_exec('/usr/sbin/asterisk -rx "confbridge kick '.
            $this->input->post("room").' '.
            $this->input->post("chan").'"');

        $data = $this->input->post("room")." ".$this->input->post("chan");

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function getcolas() {
        Header('Content-Type: application/json');
        echo json_encode($this->datos_model->colas($this->udata['queues']));
    }

}

?>
