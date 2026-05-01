<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Consola_model extends CI_Model
{

    public function getPlantillas($tabla = 'sms_template') {
        $query = $this->db->query("SELECT st.*, c.name AS campana
            FROM $tabla st
            INNER JOIN campaign c ON st.id_campaign = c.id
            WHERE st.id_campaign IN ?
            ORDER BY c.name",[explode(",",$this->udata['campanas'])]);
        return $query->result();
    }

    public function getChatParams() {
        $query = $this->db->query("SELECT * FROM catalogs WHERE cat = 'chat'");
        return $query->row();
    }

    public function getAgente() {
        $data = $this->udata;
        if ($data['ruta']=='consola') {
            $consola = (array)$this->datos_model->getParams("consola");
            $data = array_merge($data, $consola);
            $data['vcRoom'] = "pnx_" . $this->udata['id'] . "_" . md5($this->udata['email']);
            $basdata = $this->datos_model->getBasUdata($this->udata['id']);
            if (!empty($basdata['chatId'])) {
                $resto = $this->generateAutoLoginLink(array('r' => 'chat/chattabs', 'u' => $basdata['chatId'], 't' => time() + 60, 'secret_hash' => 'ph0n3xch4ts3cr3t'));
            } else {
                $resto = "index.php/site_admin";
            }
            $data['chatUrl'] .= $resto;
        }
        $query = $this->db->query("SELECT `eti`, `val` from catalogs where `cat`='sistema'");
        foreach ($query->result() as $row) {
            $data[$row->eti] = $row->val;
        }

        return $data;
    }

    public function getBreaks() {
        $query = $this->db->query("SELECT * from break where status='1' order by description");
        return $query->result();
    }

    public function getDatosLlamada() {
        $exten = $this->udata['exten'];
        $data  = array("script"=>"Sin datos de campaña.", "campaign"=>["name"=>"","id"=>""], "uniqueid"=>"", "number"=>"", "form"=>"", "agenda"=>"");

        // Llamada
        sleep(1);
        $ctxOps = ["ssl"=>["verify_peer"=>false,"verify_peer_name"=>false]];
        $ariserv = "https://" . getenv('ARI_USER') . ":" . getenv('ARI_PASS') . "@" . getenv('ASS_DB_HOST') . ":8089";
        $extenstat = json_decode(file_get_contents($ariserv . "/ari/endpoints/PJSIP/".$exten, false, stream_context_create($ctxOps)));
        foreach ($extenstat->channel_ids as $chan) {
            $chanstat = json_decode(file_get_contents($ariserv . "/ari/channels/".$chan, false, stream_context_create($ctxOps)));
            if (!empty($chanstat->connected->number)) {
                $data['uniqueid'] = $chanstat->id;
                $data['name'] = $chanstat->connected->name;
                $csnumber = $chanstat->connected->number;
                break;
            }
        }

        $did = $query = $abuscar = "";

        if (!empty($csnumber)) {
            if (strpos($csnumber,"|") && strpos($csnumber, ":")) {
                $pre = explode(":",trim($csnumber));
                $did = $pre[0];
                $abuscar = explode("|",$pre[1])[1];
                $data['number'] = explode("|",$pre[1])[0];
            } elseif (substr_count($csnumber, '|') == 1) {
                $pre = explode("|",trim($csnumber));
                $data['number'] = $pre[0];
                $abuscar = $pre[1];
            } elseif (substr_count($csnumber, '|') == 2) {
                $pre = explode("|",trim($csnumber));
                $did = $pre[0];
                $data['number'] = $pre[1];
                $abuscar = $pre[2];
            } elseif (strpos($csnumber, ":")) {
                $pre = explode(":",trim($csnumber));
                $did = $pre[0];
                $abuscar = $data['number'] = $pre[1];
            } else {
                $data['number'] = $abuscar = trim($csnumber);
            }
            if (empty($did) && !empty($data['name']) && strpos($data['name'], ":")) {
                $pre = explode(":",trim($data['name']));
                $did = $pre[0];
            } elseif (empty($did) && !empty($data['name'])) {
                $did = $data['name'];
            }
            if ($abuscar=='0000' || $abuscar=='00000') {
                $abuscar=$data['number'];
            }
            $campos = $this->db->list_fields('client');
            if(isset($campos["ext1"])) unset($campos["ext1"]);
            if(isset($campos["ext2"])) unset($campos["ext2"]);
            if(isset($campos["ext3"])) unset($campos["ext3"]);
            if(isset($campos["out_num"])) unset($campos["out_num"]);
            if(isset($campos["int_num"])) unset($campos["int_num"]);
            $lewere = "";
            foreach ($campos as $key => $value) {
                if (!strpos($value, "id")) $lewere .= "`".$value."`='". $abuscar ."' or ";
            }
            $lewere = rtrim($lewere, " or ");
            $query = $this->db->query("SELECT * from client where $lewere limit 1");
            if ($query->num_rows() == 1) {
                foreach ($query->row() as $key => $col) {
                    if (!empty($col)) {
                        $data['agenda'] .= "<p><strong>$key:</strong> $col</p>";
                    }
                }
            }
            if (!empty($did)) {
                $query = $this->db->query("SELECT * from campaign
                    where dids like '%".$did."%' and active=1 order by ID DESC limit 1");
                $row = $query->row();
                if(!empty($row)) {
                    // Campaña
                    $data['script'] = $row->script;
                    $data['campaign'] = $this->getCampaingAttrs($row->id); // campaign attributes
                    $data['campaign']['name'] = $row->name;
                    $data['campaign']['id'] = $row->id; // campaign ID
                    $data['campaign']['acw'] = $data['campaign']['acw'] ?? 0;
                    // formulario
                    if(!empty(strpos($data['name'], '-pred-'))) {
                        list($num, $pred, $id_desp, $id_reg) = explode("-", $data['name']);
                        $this->load->model('desp_model');
                        $despdata = $this->desp_model->getForm(array('id_desp'=>$id_desp, 'id_reg'=>$id_reg, 'subiraccess'=>'NO'));
                        $data['desp']  = $this->desp_model->getDespInfo($id_desp);
                        $data['histo'] = $this->desp_model->histo($id_desp, $id_reg);
                        $data['form']  = $this->load->view("despachador/desp_form_".$id_desp, $despdata, true);
                    } else {
                        $this->load->model('form_model');
                        $data = $this->form_model->get_form($row->id, $data, 'entrante');
                    }
                }
            }
        }

        return $data;
    }

    public function getDatosLlamadaSal() {
        $data = array("script"=>"Sin datos de campaña.", "campaign"=>["name"=>"","id"=>""], "uniqueid"=>"", "number"=>"", "form"=>"", "agenda"=>"");
        $data["cid"] = $this->input->post('formIdCam'); // Campaign ID

        if (!empty($data["cid"])) {
            $query = $this->db->query("SELECT * from campaign where id='".$data["cid"]."'");
            $row = $query->row();
            if(!empty($row)) {
                // Campaña
                $data['script'] = $row->script;
                $data['campaign'] = $this->getCampaingAttrs($row->id); // campaign attributes
                $data['campaign']['name'] = $row->name;
                $data['campaign']['id'] = $row->id;
                $data['campaign']['acw'] = $data['campaign']['acw'] ?? 0;
                // formulario
                $this->load->model('form_model');
                $data = $this->form_model->get_form($data["cid"], $data, 'saliente');
            }
        }

        return $data;
    }

    public function getDespachador() {
        $query = $this->db->query("SELECT d.*, du.id_user from dispatcher d
        inner join disp_user du on du.id_dispatcher = d.id
        where d.active = 1 and d.running = 1 and du.id_user = ".$this->session->userdata('uid')." limit 1;");
        $fila = $query->row();
        if (!empty($fila)) {
            return $fila->id;
        }
        return false;
    }

    private function generateAutoLoginLink($params) { // generar el link de auto login para Live Helper Chat
        $dataRequest = array();
        $dataRequestAppend = array();
        // Destination ID
        if (isset($params['r'])){
            $dataRequest['r'] = $params['r'];
            $dataRequestAppend[] = '/(r)/'.rawurlencode(base64_encode($params['r']));
        }
        if (isset($params['u']) && is_numeric($params['u'])){ // User ID
            $dataRequest['u'] = $params['u'];
            $dataRequestAppend[] = '/(u)/'.rawurlencode($params['u']);
        } else if (isset($params['l'])){ // Username
            $dataRequest['l'] = $params['l'];
            $dataRequestAppend[] = '/(l)/'.rawurlencode($params['l']);
        } else {
            throw new Exception('Username or User ID has to be provided');
        }
        // Expire time for link
        if (isset($params['t'])){
            $dataRequest['t'] = $params['t'];
            $dataRequestAppend[] = '/(t)/'.rawurlencode($params['t']);
        }
         $hashValidation = sha1($params['secret_hash'].sha1($params['secret_hash'].implode(',', $dataRequest)));
         return "index.php/user/autologin/{$hashValidation}".implode('', $dataRequestAppend);
    }

    public function getIndexData() {
        $res['campanas']   = $this->datos_model->getCampanas();
        $res['agentes']    = $this->datos_model->getRelUsers(["cam"=>$res['campanas']]);
        $this->load->model("form_model");
        $res['forms']      = $this->form_model->getbycam(reset($res['campanas'])->id);
        $res['id_desp']    = $this->getDespachador();
        if (!empty($this->udata['exten'])) {
            $res['jscript'][]  = 'sip.min';
            $res['jscript'][]  = 'app';
        }
        if (!empty($this->udata['exten'])) {
            $res['jscript'][]  = 'consola/despachador';
        }
        $res['jscript'][]  = 'consola/consola';
        $res['jscript'][]  = 'consola/form';
        if (in_array("home", $this->udata['permisoSec'])) {
            $res['jscript'][]  = 'consola/agenda';
        }
        $res['camposagen'] = $this->getCamposAgenda();
        if (in_array('sms', $this->udata['permisoSec'])) {
            $res['plantiSms'] = $this->getPlantillas();
            $res['jscript'][] = 'consola/sms';
        }
        if (in_array('chat', $this->udata['permisoSec'])) {
            $res['jscript'][] = 'consola/chat';
        }
        if (in_array('pit', $this->udata['permisoSec'])) {
            $res['plantiPit'] = $this->getPlantillas('pit_template');
            $res['jscript'][] = 'consola/pit';
        }
        if (in_array('email', $this->udata['permisoSec']) && !empty($this->udata['ctas_email'])) {
            $res['jscript'][] = 'ckeditor/ckeditor';
            $res['jscript'][] = 'consola/email';
        }
        if (in_array('calendario', $this->udata['permisoSec'])) {
            $res['jscript'][] = 'consola/calendario';
        }
        if (in_array('videocall', $this->udata['permisoSec'])) {
            $res['extjs'][] = $this->udata['vcServer']."external_api.js";
            $res['jscript'][] = 'consola/videollamada';
        }
        if (in_array('auxiliares', $this->udata['permisoSec'])) {
            $res['breaks'] = $this->getBreaks();
        }
        if (in_array('whatsapp', $this->udata['permisoSec']) && !empty($this->udata['whatsapp'])) {
            $query = $this->db->query("SELECT wc.id, wc.id_campaign, wc.cuenta, wc.nombre, wc.almacen,
                '0' AS 'idContactoActivo', ifnull(wr.cta, 0) AS 'encuesta'
                FROM `whatsapp_cuentas` wc
                LEFT JOIN (SELECT id_wacta, count(*) cta FROM `whatsapp_rate` WHERE active = 1 and id_wacta IN (".
                $this->udata['whatsapp'].") group by id_wacta) wr ON wr.id_wacta = wc.id
                WHERE wc.active = '1' AND wc.id IN (".$this->udata['whatsapp'].
                ") AND wc.id_campaign in (".$this->udata['campanas'].") GROUP BY wc.id");
            $wactas = $query->result();
            $res["wactas"] = (object)[];
            foreach ($wactas as $key => $wacta) {
                $id = $wacta->id;
                $res["wactas"]->$id = $wacta;
                $res["wactas"]->$id->idsAsigned = [];
                $query = $this->db->query("SELECT `id`, `account`, `name` from `whatsapp_contact`
                    where `id_wacta` = ? order by `name`, `account` limit 100", [$wacta->id]);
                $res["wactas"]->$id->contactos = $query->result();
                $query = $this->db->query("SELECT u.id, concat(u.name,' ',u.last) 'nombre', uud.val 'perfil', IFNULL(sa.uid, 0) as conectado
                    from user_data ud
                    left join catalogs c on c.id = ud.id_catalog
                    left join user u on u.id = ud.id_user
                    LEFT JOIN ses_ab sa on sa.uid = u.id
                    left join user_data uud on uud.id_user = u.id
                    left join catalogs uc on uc.id = uud.id_catalog
                    where u.active = '1' and c.cat = 'userData' and c.eti = 'whatsapp'  and uc.cat = 'userData' and uc.val = 'perfil'
                    and uud.val in ('supervisor','agente') and $wacta->id in (ud.val) and u.id <> '".$this->session->userdata('uid')."'
                    order by conectado DESC, uud.val, u.name");
                $res["wactas"]->$id->agentes = $query->result();
            }
            $res['jscript'][] = 'consola/whatsapp';
        }
        return $res;
    }

    public function getCamposAgenda() {
        $campos = $this->db->list_fields('client');

        return $campos;
    }

    public function getCampaingAttrs($cid) {
        $query = $this->db->query("SELECT * FROM campaign_data
            WHERE id_campaign = ? ORDER BY atributo, sub", [$cid]);
        $ret = [];
        foreach ($query->result() as $row) {
            if ($row->sub != "") {
                $ret[$row->atributo][$row->sub] = $row->valor;
            } else {
                $ret[$row->atributo] = $row->valor;
            }
        }

        return $ret;
    }

}
?>
