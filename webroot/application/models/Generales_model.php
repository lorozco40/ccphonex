<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Generales_model extends CI_Model
{

    public function emergencia() {
        ignore_user_abort(true);
        set_time_limit(0);

        $ip = getUserIP();
        $this->db->query("INSERT INTO user_log values (0, ?, now(), ?, ?)", array($this->session->userdata('uid'), 'Emergencia', $ip));
        $this->db->query("TRUNCATE ses_ab");
        exec(FCPATH.'emer.sh');
        return file_get_contents(FCPATH.'emer.log');
    }

    public function guardar($data) {
        $valores = array();
        foreach ($data as $key => $value) {
            $query = $this->db->query("UPDATE catalogs set val=? where id=?", array($value, $key));
            if (!$query) {
                return false;
            }
        }
        return true;
    }

    public function llamadascdr() {
        $desde = $this->input->get('desde');
        $hasta = $this->input->get('hasta');
        if (!empty($desde) && !empty($hasta)) {
            $query = $this->db->query("SELECT u.id, concat(u.name,' ',u.last) name,
                group_concat(if(cat.cat = 'userData' and cat.val = 'userask', ud.val, null)) ext
                from user u
                left join user_data ud on ud.id_user = u.id
                left join catalogs cat on cat.id = ud.id_catalog
                where u.id > 1
                group by u.id
                having ext <> ''
                ORDER BY ext ASC");
            $exts = [];
            foreach ($query->result() as $key => $row) {
                $exts[$row->ext] = $row->id;
            }
            $acdrdb = $this->load->database('asterisk', TRUE);
            $query = $acdrdb->query("SELECT distinct linkedid from cdr where calldate between '$desde' and '$hasta'");
            if ($query->num_rows()==0) {
                echo "No se encontraron registros.";
            }
            $llamadas = $query->result();
            foreach ($llamadas as $key => $row) {
                $query = $this->db->query("SELECT id from call_entry where uniqueid = '$row->linkedid'");
                if ($query->num_rows()==0) {
                    $fila = $this->generarfila($exts, $row->linkedid);
                    if (!empty($fila)) {
                        $query = $this->db->query("INSERT into call_entry values $fila");
                        echo $row->linkedid." Se insertó: ";
                        var_dump($query);
                        echo "<br />";
                    } else {
                        echo "sin datos<br />";
                    }
                } else {
                    echo $row->linkedid." Ya en base.<br />";
                }
            }
        } else {
            echo "No no no.";
        }
    }

    private function generarfila($exts, $linkedid) {
        $acdrdb = $this->load->database('asterisk', TRUE);
        $id='0';
        $id_user='null';
        $queue='';
        $did='';
        $cid_nam='';
        $cid_num='';
        $datetime_received='null';
        $datetime_queued='null';
        $datetime_init='null';
        $datetime_end='null';
        $duration='0';
        $duration_wait='0';
        $status='Abandonada Troncal';
        $grabacion='';
        $uniqueid=$linkedid;
        $type='Entrante';
        $rate='0.00';
        $rate_type='na';
        $query = $acdrdb->query("SELECT *, SUBSTRING(dstchannel, 1, 5) dst, SUBSTRING(dstchannel, 7, 4) ext from cdr where linkedid='$uniqueid' order by sequence");
        $regs = $query->result();
        $registrar = false;
        $fila = "";
        foreach ($regs as $key => $row) {
            if (strlen($row->src)>4) {
                if ($datetime_received=='null') {
                    $datetime_received = "'$row->calldate'";
                } else {
                    if ($datetime_queued=='null') $datetime_queued = "'$row->calldate'";
                }
                if (empty($cid_nam)) $cid_nam = $row->cnam;
                if (empty($cid_num)) $cid_num = $row->cnum;
                if (empty($queue) || empty($did)) $queue = $did = $row->did;
                if (empty($grabacion)) $grabacion = $row->recordingfile;
                if ($row->dst == 'PJSIP') {
                    $duration = $row->billsec;
                    $duration_wait = $row->duration - $row->billsec;
                    $datetime_init = "DATE_ADD($datetime_queued, INTERVAL $duration_wait SECOND)";
                    $datetime_end = "DATE_ADD($datetime_queued, INTERVAL $row->duration SECOND)";
                    if ($row->disposition == 'ANSWERED') {
                        $id_user = $exts[$row->ext];
                        $status = 'Terminada';
                    } else {
                        $status = 'Abandonada nosl';
                    }
                }
                $lastsrc = $row->src;
                $lasttime = "'$row->calldate'";
                $lastbillsec = $row->billsec;
                $registrar = true;
            }
        }
        if ($registrar) {
            if ($duration == '0' && $datetime_queued != 'null' && $status !="Abandonada nosl") {
                $datetime_end = $lasttime;
                $status = "Abandonada";
            }
            if ($status == "Abandonada Troncal") {
                $cid_num = $lastsrc;
                $duration_wait = $lastbillsec;
                $datetime_end = "DATE_ADD($datetime_received, INTERVAL $duration_wait SECOND)";
            } elseif ($status == "Abandonada nosl") {
                $datetime_init = 'null';
            } else {
                $duration_wait = "TIME_TO_SEC(TIMEDIFF(COALESCE($datetime_init, $datetime_end), COALESCE($datetime_queued, $datetime_received)))";
            }
            $fila = "('$id', $id_user,'$queue', '$did', '$cid_nam', '$cid_num', $datetime_received, $datetime_queued, $datetime_init, $datetime_end, '$duration',
            $duration_wait, '$status', '$grabacion', '$uniqueid', '$type', '$rate', '$rate_type')";
        }

        return $fila;
    }

}
