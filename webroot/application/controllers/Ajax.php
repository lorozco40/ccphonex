<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller
{

	// Show view Page
    public function cities_by_state() {
        $state = $this->input->post("birthstate");
        $city = $this->input->post("birthcity");
        $data['data'] = $this->datos_model->get_cities_by_state($state);
        $data['city'] = $city;
        $data = $this->load->view("ajax_cities", $data, true);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function save_form() {
        $this->load->model('form_model');
        if ($this->form_model->save_form()) {
            $data = array("status"=>"ok", "msg"=>"Registro guardado con éxito.");
        } else {
            $data = array("status"=>"error", "msg"=>"Error al guardar el registro.");
        }

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function tmpaudio() {
        ignore_user_abort(true);
        set_time_limit(0); // disable the time limit for this script

        $dl_file = $this->input->post('src');
        if (substr($dl_file, 0, 2)=='vm') {
            list($vm, $fecha, $audio) = explode("_", $dl_file);
            list($ano, $mes, $dia) = explode("-", $fecha);
            $path = "/var/www/vm/";
            $file = $path.$ano."/".$mes."/".$dia."/".$audio;
        } else {
            $path = "/var/spool/asterisk/monitor/";
            $parts = explode(".", $dl_file);
            $partp = explode("-", $parts[0]);
            $ano = substr($partp[3], 0, 4);
            $mes = substr($partp[3], 4, 2);
            $dia = substr($partp[3], 6, 2);

            $file = $path.$ano."/".$mes."/".$dia."/".$parts[0].".".$parts[1].".wav";
        }

        $data = "La grabación NO se encuentra en éste servidor.";
        if (!file_exists(site_url('files/'.$dl_file))) {
            if (file_exists($file)) {
                if(copy($file, "/var/www/html/files/".$dl_file)) {
                    $data = "OK";
                }
            }
        } else {
            $data = "OK";
        }

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function licinfo() {
        $this->load->model("usuario_model");
        $respu['ensis'] = $this->usuario_model->getOnline();
        $respu['dispo'] = (int)$this->udata['lic']['usuarios'] - (int)$respu['ensis'];

        Header('Content-Type: application/json; charset=utf-8');
        echo json_encode($respu);
    }

    public function logerror() {
        $this->datos_model->logerror($this->input->post());

        return true;
    }

    public function traecuid() {
        $cid = $this->input->post("cid");
        $exten = $this->udata['exten'];
        $data['uniqueid'] = false;
        $ctxOps = ["ssl"=>["verify_peer"=>false,"verify_peer_name"=>false]];
        // Asterisk por fuerza debe ser HTTPS o no pasa la voz (webrtc), no debería existir http://...:8088
        $ariserv = "https://" . getenv('ARI_USER') . ":" . getenv('ARI_PASS') . "@" . getenv('ASS_DB_HOST') . ":8089";
        $extenstat = json_decode(file_get_contents($ariserv . "/ari/endpoints/PJSIP/".$exten, false, stream_context_create($ctxOps)));
        foreach ($extenstat->channel_ids as $chan) {
            $chanstat = json_decode(file_get_contents($ariserv . "/ari/channels/".$chan, false, stream_context_create($ctxOps)));
            if (!empty($chanstat->id) && !empty($chanstat->dialplan->exten) &&
                $chanstat->dialplan->exten!="*78" && $chanstat->dialplan->exten!="*79") {
                $data['uniqueid'] = $chanstat->id;
                break;
            }
        }

        Header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    public function des_dep() {
        $data = $this->datos_model->get_des_dep($this->input->post());

        Header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    public function auco() {
        $data = $this->input->post();
        $mod = $data['mod'] . "_model";
        $met = (empty($data['met'])) ? 'auco' : $data['met'];
        $this->load->model($mod);
        $ret = $this->$mod->$met($data);

        Header('Content-Type: application/json; charset=utf-8');
        echo json_encode($ret);
    }

    public function upduserstatus() {
        $ret = $this->datos_model->upduserstatus($this->input->post());

        Header('Content-Type: application/json; charset=utf-8');
        echo json_encode($ret);
    }

}

?>
