<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debug_model extends CI_Model
{

    public function llamadas() {
        /* Inicializa todas las llamadas que no estén en la tabla call_entry y las deja "En curso" */
        $query = $this->db->query("SELECT datetime_received from call_entry order by id DESC limit 1");
        $res = $query->row();
        $ultimo = (!empty($res)) ? $res->datetime_received : '2000-01-01 00:00:00';
        $query = $this->db->query("SELECT * from asteriskcdrdb.cel
            where eventtime>'$ultimo' and uniqueid=linkedid and eventtype='CHAN_START' and (length(exten)>=4 OR exten='s') order by eventtime ASC limit 100");
        $nuevas = $query->result();
        echo "<pre>Nuevas: ";
        var_dump($nuevas); echo "<br>\nEn curso: ";
        $valores = "";
        foreach ($nuevas as $key => $row) {
            if ($row->context=='from-trunk' && $row->exten=='s') {
                $type = 'Saliente';
                $cid_name = "";
                $cid_num = "";
                $queue = "";
            } elseif($row->context=='from-trunk') {
                $type = 'Entrante';
                $cid_name = addslashes($row->cid_name);
                $cid_num = $row->cid_num;
                $queue = substr($row->exten, -4, 4);
            } else {
                $type = 'Saliente';
                $cid_name = addslashes($row->cid_num);
                $cid_num = $row->exten;
                $queue = "";
            }
            $valores .= "('$row->linkedid', '$row->eventtime', '$cid_name', '$cid_num', '$queue', '$type', 'En curso'),";
        }
        $valores = rtrim($valores,",");
        if (!empty($valores)) {
            $query = $this->db->query("INSERT INTO call_entry (uniqueid, datetime_received, cid_name, cid_num, queue, type, status) values " . $valores);
        }
        /* Proceso para completar las llamadas "En curso" */
        $query = $this->db->query("SELECT * from call_entry where status='En curso' order by id DESC limit 1");
        $encurso = $query->result();
        var_dump($encurso); echo "<br>\n";
        $this->load->model("consola_model");
        foreach ($encurso as $row) {
            $query = $this->db->query("SELECT * from asteriskcdrdb.cel where uniqueid='".$row->uniqueid.
                "' and eventtype='CHAN_END' order by id desc limit 1");
            $callend = $query->row();
            if (!empty($callend)) {
                $this->terminaLlamada($row, $callend);
            }
        }
        /* completar formulario llamada con linkedid */
        // $query = $this->db->query("SELECT TABLE_NAME from information_schema.columns
        //     where table_name like 'formd_%' group by TABLE_NAME");
        // $tablas = $query->result();
        // foreach ($tablas as $tabla) {
        //     $this->db->query("UPDATE ".$tabla->TABLE_NAME." as lt set linkedid = (SELECT c.linkedid from asteriskcdrdb.cel as c
        //         where c.uniqueid=lt.uniqueid limit 1) where lt.linkedid is null");
        // }
        /* Abandonar o eliminar llamadas con ERROR no identificable */
        // $query = $this->db->query("") // Continuará ...
        return true;
    }

    private function terminaLlamada($reg, $end) {
        var_dump("reg term llam ", $reg, $end); echo "<br>\n";
        $grabacion = '';
        $exten = (!empty($reg->cid_name)) ? addslashes($reg->cid_name) : '0';
        $datetime_init = 'NULL';
        $cola = $reg->queue;
        $dbas = $this->load->database('asterisk', TRUE);
        $query = $dbas->query("SELECT * FROM cel WHERE uniqueid = '".$reg->uniqueid.
        "' AND eventtype='APP_START' AND appname='MixMonitor' ORDER BY id ASC LIMIT 1");
        $graba = $query->row();
        var_dump("grabacion ", $graba); echo "<br>\n";
        if(!empty($graba)) {
            $grabacion = explode("/", explode(",", $graba->appdata)[0])[3];
            if ($reg->type=="Saliente") $cola = explode("-", $grabacion)[1];

        }
        $query = $dbas->query("SELECT eventtime, substring(peer,7,4) as peer FROM cel WHERE uniqueid = '".$reg->uniqueid.
        "' AND eventtype='BRIDGE_ENTER' ORDER BY id ASC LIMIT 1");
        $bridge = $query->row();
        var_dump("bridge ", $bridge); echo "<br/>\n";
        if(!empty($bridge)) {
            $datetime_init = "'".$bridge->eventtime."'";
            if (empty($exten) || $reg->type =="Entrante") $exten = $bridge->peer;
        }
        $query = $dbas->query("SELECT exten, eventtime, cid_num FROM cel WHERE uniqueid = '".$reg->uniqueid.
        "' AND eventtype='APP_START' AND appname='Queue' ORDER BY id ASC LIMIT 1");
        $queue = $query->row();
        var_dump("Queue ", $queue); echo "<br>\n";
        $queued = "";
        $inicio = $end->eventtime;
        if (!empty($queue->eventtime)) {
            $queued = "datetime_queued='".$queue->eventtime."',";
            $inicio = $queue->eventtime;
        }
        if ($datetime_init=="NULL") {
            if (!empty($queue->eventtime)) {
                $status = "Abandonada";
            } else {
                $status = "Abandonada Troncal";
            }
        } else {
            $status = "Terminada";
        }
        $query = $dbas->query("SELECT eventtime FROM cel WHERE uniqueid = '".$reg->uniqueid.
        "' AND eventtype='APP_START' AND appname='VoiceMail' ORDER BY id ASC LIMIT 1");
        $vm = $query->num_rows();
        var_dump("vm ", $vm); echo "<br>\n";
        if ($reg->type=="Entrante" && $vm == 1) {
            $status = "Terminada";
            $files = glob('/var/spool/asterisk/voicemail/default/9000/INBOX/*.wav');
            $files = array_combine($files, array_map('filectime', $files));
            arsort($files);
            $wav_file = key($files);
            $txt_file = rtrim($wav_file, "wav") . "txt";
            $txt_file_str = file_get_contents($txt_file);
            $numero = (!empty($queue->cid_num)) ? $queue->cid_num : $reg->cid_num;
            $setgraba = (is_numeric(strpos($txt_file_str, $numero))) ? "grabacion='".$wav_file."', " : "";
        }
        $query = $this->db->query("SELECT u.id from user u
            left join user_data ud on ud.id_user = u.id
            left join catalogs cat on cat.id = ud.id_catalog
            where cat.val = 'userask' and ud.val='".$exten."' order by u.active DESC limit 1");
        $res = $query->row();
        var_dump("user exten ", $res); echo "<br>\n";
        $agent_id = (!empty($res)) ? "'".$res->id."'" : 'NULL';
        if ($reg->type=="Entrante" && !empty($bridge->exten)) $cola = $bridge->exten;
        if (!empty($queue->exten)) $cola = $queue->exten;
        if (!empty($graba) && strpos($graba->cid_num, ':') !== false) {
            $did = explode(":", $graba->cid_num)[0];
        } else {
            $did = $cola;
        }
        var_dump("did ", $did);
        $id_campaign = 'null';
        if (!empty($did)) {
            $query = $this->db->query("SELECT id from campaign where dids LIKE CONCAT('%',$did,'%') limit 1");
            $campa = $query->row();
            var_dump("campaña ", $campa); echo "<br>\n";
            if (!empty($campa)) {
                $id_campaign = "'".$campa->id."'";
            }
        }
        $masset = "grabacion='".$grabacion."', id_campaign = ".$id_campaign.", queue='".$cola."', did='".$did."', ";
        if($reg->type=="Entrante") {
            if (empty($reg->cid_name) && $exten!="0" && $vm == 1) $masset .= "cid_name='".$exten."-vm', ".$setgraba;
            if (empty($reg->cid_name) && $exten!="0" && $vm == 0) $masset .= "cid_name='".$exten."', ";
            if (!empty($reg->cid_name) && $vm == 1) $masset .= "cid_name='".addslashes($reg->cid_name)."-vm', ".$setgraba;
            if (!empty($queue->cid_num)) $masset .= "cid_num='".$queue->cid_num."', ";
        } elseif (empty($reg->cid_name) && $reg->type=="Saliente") {
            $masset .= "cid_name='".addslashes($end->cid_name)."', cid_num='".$end->cid_num."', ";
        }
        $diff = strtotime($end->eventtime) - strtotime($inicio);
        var_dump("diff ", $diff); echo "<br>\n";
        $res = $this->datos_model->getParams('sl');
        if ($status == "Abandonada" && $diff <= $res->segundos) {
            $status = "Abandonada nosl";
        }
        var_dump("status ", $status); echo "<br>\n";
        var_dump("mas set ", $masset); echo "<br>\n";
        $query = $this->db->query("UPDATE call_entry set id_user=".$agent_id.", ".$masset."
            $queued
            datetime_init=".$datetime_init.",
            datetime_end='".$end->eventtime."',
            duration=TIME_TO_SEC(TIMEDIFF(datetime_end, COALESCE(datetime_init, datetime_end))),
            duration_wait=TIME_TO_SEC(TIMEDIFF(COALESCE(datetime_init, datetime_end), COALESCE(datetime_queued, datetime_received))),
            status='".$status."' where id='".$reg->id."'");
        dd($query);
    }

}
