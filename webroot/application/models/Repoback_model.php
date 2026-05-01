<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repoback_model extends CI_Model
{

    public function sesion($fecha=NULL) {
        $fecha = (empty($fecha)) ? date('Y-m-d') : $fecha;
        $query = $this->db->query("SELECT distinct(`user_id`) AS 'id_user', '$fecha' AS 'fecha',
            'NULL' AS 'login', 'NULL' AS 'primero', '0' AS 'endescanso', '0' AS 'enllamada', 'NULL' AS 'ultimo',
            'NULL' AS 'logout', '0' AS 'ensesion', '0' AS 'ocupacion', '0' AS 'pondescanso', '0' AS 'disponibilidad'
            from `user_log` where `user_id` > 1 AND date(`evento`) = '$fecha'");
        $agens = $query->result();
        $final = array();
        foreach ($agens as $row) {
            $uid = $row->id_user;
            $final[$uid] = $row;
            $iniciar = "date(`evento`) = '$fecha'";
            $query = $this->db->query("SELECT if(`logout` is not null, `logout`, `ultimo`) 'ultimo'
                from `rep_sesion` where `fecha` = subdate('$fecha', 1) and `id_user` = '$uid'");
            if ($query->num_rows()>0 && $query->row()->ultimo !== null) $iniciar = "evento > '" . $query->row()->ultimo . "'";

            // Primer evento no login ni entra consola
            $query = $this->db->query("SELECT `evento` FROM `user_log`
                WHERE `user_id` = '$uid'
                AND $iniciar
                AND `detalle` NOT IN ('Login','Entra consola','Sale consola','Logout') ORDER BY `id` LIMIT 1");
            if ($query->num_rows()==1) {
                $final[$uid]->primero = $query->row()->evento;
                $iniciar .= " AND `evento` < '".$final[$uid]->primero."'";
            }

            // Login evento
            $query = $this->db->query("SELECT `evento` FROM `user_log` where `user_id` = '$uid'
                AND $iniciar AND `detalle` = 'Login' order by id limit 1");
            if ($query->num_rows()==1) $final[$uid]->login = $query->row()->evento;

            // Último evento
            if ($final[$uid]->login != 'NULL') {
                $usarhasta = "AND `evento` BETWEEN '".$final[$uid]->login."' AND date_add('".$final[$uid]->login."', interval 14 hour)";
                $paraul = " AND date_add('".$final[$uid]->login."', interval 14 hour)";
            } elseif($final[$uid]->primero != 'NULL') {
                $usarhasta = "AND `evento` BETWEEN '".$final[$uid]->primero."' AND date_add('".$final[$uid]->primero."', interval 14 hour)";
                $paraul = " AND date_add('".$final[$uid]->primero."', interval 14 hour)";
            } else {
                $usarhasta = "AND date(`evento`) = '$fecha'";
                $paraul = "No";
            }
            $query = $this->db->query("SELECT `evento` from `user_log` where `user_id` = '$uid'
                $usarhasta AND `detalle` NOT IN ('Login','Entra consola','Sale consola','Logout') ORDER BY `id` DESC LIMIT 1");
            if ($query->num_rows()==1) {
                $final[$uid]->ultimo = $query->row()->evento;
                $usarhasta = "AND `evento` BETWEEN '".$final[$uid]->ultimo."' $paraul";
                if ($paraul == "No") $usarhasta = "AND date(`evento`) = '$fecha'";
            }

            // Logout evento
            $query = $this->db->query("SELECT `evento` from `user_log` where `user_id` = '$uid'
                $usarhasta AND `detalle` = 'Logout' ORDER BY `id` DESC LIMIT 1");
            if ($query->num_rows()==1) $final[$uid]->logout = $query->row()->evento;

            // Demás tiempos
            $desde = ($final[$uid]->login == 'NULL') ? (($final[$uid]->primero == 'NULL') ? false : $final[$uid]->primero) : $final[$uid]->login;
            $hasta = ($final[$uid]->logout == 'NULL') ? (($final[$uid]->ultimo == 'NULL') ? false : $final[$uid]->ultimo) : $final[$uid]->logout;
            if($desde && $hasta) {
                $rango = "BETWEEN '$desde' AND '$hasta'";
                $final[$uid]->ensesion = strtotime($hasta) - strtotime($desde);
            } else {
                $rango = "= '$fecha'";
            }
            $query = $this->db->query("SELECT sum(`duration`) 'cuanto' from `break_entry`
                where `id_user`='$uid' and `datetime_init` ".$rango);
            $subres = $query->row();
            if (!empty($subres->cuanto)) $final[$uid]->endescanso = $subres->cuanto;
            $query = $this->db->query("SELECT sum(`duration`) 'cuanto' from `call_entry`
                where `id_user`='$uid' and `status`='Terminada'
                and `datetime_received` ".$rango);
            $subres = $query->row();
            if(!empty($subres->cuanto)) $final[$uid]->enllamada = $subres->cuanto;
            if (!empty($final[$uid]->ensesion)) {
                $final[$uid]->ocupacion = round($final[$uid]->enllamada / $final[$uid]->ensesion * 100);
                $final[$uid]->pondescanso = round($final[$uid]->endescanso / $final[$uid]->ensesion * 100);
            }
            $final[$uid]->disponibilidad = 100-$final[$uid]->ocupacion-$final[$uid]->pondescanso;
        }

        $meter = "";
        foreach ($final as $key => $row) {
            $login = ($row->login=='NULL') ? 'NULL' : "'".$row->login."'";
            $primero = ($row->primero=='NULL') ? 'NULL' : "'".$row->primero."'";
            $ultimo = ($row->ultimo=='NULL') ? 'NULL' : "'".$row->ultimo."'";
            $logout = ($row->logout=='NULL') ? 'NULL' : "'".$row->logout."'";
            $meter.= "($row->id_user, '$row->fecha', $login, $primero,
            $row->endescanso, $row->enllamada, $ultimo, $logout,
            $row->ensesion, $row->ocupacion, $row->pondescanso, $row->disponibilidad),";
        }
        $meter = rtrim($meter, ",");
        if (!empty($meter)) {
            $this->db->query("INSERT into rep_sesion (id_user, fecha, login, primero, endescanso,
                enllamada, ultimo, logout, ensesion, ocupacion, pondescanso, disponibilidad)
                values $meter ON DUPLICATE KEY UPDATE
                login=VALUES(login), endescanso=VALUES(endescanso), enllamada=VALUES(enllamada),
                primero=VALUES(primero), ultimo=VALUES(ultimo), logout=VALUES(logout),
                ensesion=VALUES(ensesion), ocupacion=VALUES(ocupacion),
                pondescanso=VALUES(pondescanso), disponibilidad=VALUES(disponibilidad)");
        }

        return count($final);
    }

    public function inbound($fecha=NULL) {
        $fecha = (empty($fecha)) ? date('Y-m-d') : $fecha;
        $query = $this->db->query("INSERT IGNORE INTO rep_inbound 
            SELECT c.id, c.datetime_received fecha, c.cid_num numero,
            c.uniqueid linkedid, coalesce(camp.id,'') id_campana,
            coalesce(camp.name,'') campana, c.did did,
            coalesce(ext.val,'') extension, COALESCE(u.id,'') id_agente,
            coalesce(CONCAT(u.name,' ',u.last),'') agente,
            c.duration_wait espera, c.duration duracion,
            time_to_sec(TIMEDIFF(COALESCE(c.datetime_init, c.datetime_end), c.datetime_received)) espera_total,
            c.status estatus, c.grabacion, tcv.calidad, tcc.comentario AS calidad_comentario
            FROM call_entry c
            LEFT JOIN user u ON c.id_user=u.id
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            LEFT JOIN (
                SELECT q.id_call_entry, SUM(q.value) AS calidad
                FROM quality_values q
                JOIN quality_fields qf ON qf.id = q.id_quality_fields
                WHERE qf.num_order != 30 AND qf.question != 'Comentario' AND qf.weight != 0
                GROUP BY q.id_call_entry
            ) tcv ON tcv.id_call_entry = c.id
            LEFT JOIN (
                SELECT q.id_call_entry,  if( q.value IS null, '', q.value) AS comentario
                FROM quality_values q
                JOIN quality_fields qfc ON qfc.id = q.id_quality_fields
                WHERE qfc.num_order = 30 AND qfc.question = 'Comentario' AND qfc.weight = 0
            ) tcc ON tcc.id_call_entry = c.id
            LEFT JOIN (select ud.id_user, ud.val FROM catalogs cat
            LEFT JOIN user_data ud ON ud.id_catalog = cat.id where cat.val = 'userask') ext ON ext.id_user = c.id_user
            WHERE date(c.datetime_received) = '$fecha'
            AND c.type='Entrante' AND (c.status='Abandonada' OR c.status='Terminada')"
        );

        return $query;
    }

    public function outbound($fecha=NULL) {
        $fecha = (empty($fecha)) ? date('Y-m-d') : $fecha;
        $q = $this->db->query("INSERT IGNORE INTO rep_outbound
            (id, fecha, numero, linkedid, id_campaign, campana, did, extension, id_agente, agente, duracion, hangup, estatus, grabacion, calidad, calidad_comentario)
            SELECT c.id, c.datetime_received fecha, c.cid_num numero, c.uniqueid linkedid, coalesce(camp.id,'') id_campana, coalesce(camp.name,'') campana,
            c.did did, coalesce(ext.val,'') extension, COALESCE(u.id,'') id_agente, coalesce(CONCAT(u.name,' ',u.last),'') agente, c.duration duracion,
            c.hangup, if(c.status='Abandonada Troncal','Abandonada', c.status) estatus, c.grabacion, tcv.calidad, tcc.comentario AS calidad_comentario
            FROM call_entry c
            LEFT JOIN user u ON c.id_user=u.id
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            LEFT JOIN (
                SELECT ud.id_user, ud.val 
                FROM catalogs cat
                LEFT JOIN user_data ud ON ud.id_catalog = cat.id
                WHERE cat.val = 'userask'
            ) ext ON ext.id_user = c.id_user
            LEFT JOIN (
                SELECT q.id_call_entry, SUM(q.value) AS calidad
                FROM quality_values q
                JOIN quality_fields qf ON qf.id = q.id_quality_fields
                WHERE qf.num_order != 30 AND qf.question != 'Comentario' AND qf.weight != 0
                GROUP BY q.id_call_entry
            ) tcv ON tcv.id_call_entry = c.id
            LEFT JOIN (
                SELECT q.id_call_entry,  if( q.value IS null, '', q.value) AS comentario
                FROM quality_values q
                JOIN quality_fields qfc ON qfc.id = q.id_quality_fields
                WHERE qfc.num_order = 30 AND qfc.question = 'Comentario' AND qfc.weight = 0
            ) tcc ON tcc.id_call_entry = c.id
            WHERE date(c.datetime_received) = '$fecha'
            AND c.type='Saliente' AND c.status<>'En curso'"
        );

        return $q;
    }

    public function abandono($fecha=NULL) {
        $fecha = (empty($fecha)) ? date('Y-m-d') : $fecha;
        $query = $this->db->query("INSERT IGNORE INTO rep_abandono
            SELECT c.id, id_campaign, coalesce(camp.name,''), did, cid_name, cid_num,
            datetime_received, datetime_queued, datetime_end, c.duration_wait,
            time_to_sec(timediff(datetime_end,datetime_received)) total_wait, status, uniqueid,
            CONCAT(date_format(c.datetime_received,'%H'),IF(MINUTE(c.datetime_received)<30,':00',':30')) c30
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE DATE(c.datetime_received) = '$fecha'
            AND c.status like 'Abandonada%' AND c.type='Entrante'"
        );

        return $query;
    }

    public function atendidas($fecha=NULL) {
        $fecha = (empty($fecha)) ? date('Y-m-d') : $fecha;
        $query = $this->db->query("INSERT IGNORE INTO rep_atendidas
            SELECT c.id, c.id_user, coalesce(concat(u.name,' ',u.last),''),
            c.id_campaign, coalesce(camp.name,''), c.did, c.cid_name, c.cid_num,
            c.datetime_received, c.datetime_queued, c.datetime_init, c.datetime_end,
            c.duration, c.uniqueid,
            CONCAT(date_format(c.datetime_received,'%H'),IF(MINUTE(c.datetime_received)<30,':00',':30')) c30
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            LEFT JOIN user u ON u.id = c.id_user
            WHERE DATE(c.datetime_received) = '$fecha'
            AND c.status = 'Terminada' AND c.type='Entrante'"
        );

        return $query;
    }

    public function acw($fecha=NULL) {
        $fecha = (empty($fecha)) ? date('Y-m-d') : $fecha;
        $this->db->query("DELETE from break_entry where duration = '0'");
        $query = $this->db->query("INSERT INTO rep_acw
            SELECT be.id_user, CONCAT(u.name,' ',u.last), ud.val,
            DATE(be.datetime_init), count(be.id),
            SUM(duration), ROUND(AVG(duration)), MAX(duration)
            FROM break_entry AS be
            LEFT JOIN user AS u ON u.id=be.id_user
            LEFT JOIN user_data ud ON ud.id_user = be.id_user
            LEFT JOIN catalogs cat ON cat.id = ud.id_catalog
            LEFT JOIN break b ON b.id = be.id_break
            WHERE DATE(be.datetime_init) = '$fecha' AND b.name='acw' AND cat.val='userask'
            GROUP BY be.id_user
            ORDER BY u.name
            ON DUPLICATE KEY UPDATE veces=VALUES(veces), total=VALUES(total),
            promedio=VALUES(promedio), largo=VALUES(largo)"
        );

        return $query;
    }

    public function poragente($fecha=NULL) {
        $fecha = (empty($fecha)) ? date('Y-m-d') : $fecha;
        $query = $this->db->query("INSERT into rep_poragente
            SELECT date(c.datetime_received), c.id_user,
            CONCAT(u.name,' ',u.last), ud.val, c.type,
            id_campaign, coalesce(camp.name,''),
            COALESCE(SUM(IF(c.status='Terminada',1,0)),0),
            COALESCE(SUM(IF(c.status like 'Abandonada%',1,0)),0),
            COALESCE(SUM(c.duration),0),
            COALESCE(ROUND(AVG(c.duration)),0),
            COALESCE(MAX(c.duration),0)
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            LEFT JOIN user u ON u.id=c.id_user
            LEFT JOIN user_data ud ON ud.id_user = c.id_user
            LEFT JOIN catalogs cat ON cat.id = ud.id_catalog
            WHERE DATE(c.datetime_received) = '$fecha' and cat.val='userask'
            group by id_user, type, id_campaign
            ON DUPLICATE KEY UPDATE exito=VALUES(exito), abandono=VALUES(abandono),
            duracion=VALUES(duracion), promedio=VALUES(promedio), larga=VALUES(larga)"
        );

        return $query;
    }

    public function vm() {
        if (!file_exists(APPPATH.'../../vm')) mkdir(APPPATH.'../../vm', 0755, true);
        $dir    = "/var/spool/asterisk/voicemail/default";
        if (!file_exists($dir)) return true;
        $extens = scandir($dir);
        foreach ($extens as $ekey => $exten) {
            if($exten != "." && $exten != "..") {
                $subdir = $dir."/".$exten."/INBOX";
                $files = scandir($subdir);
                if(count($files)>2){
                    $query = $this->db->query("SELECT * FROM campaign_vm");
                    $res = $query->result();
                    $camps = array();
                    foreach ($res as $key => $row) {
                        $camps[$row->extension] = $row->id_campaign;
                    }
                }
                foreach ($files as $fkey => $file) {
                    list($fname, $fext) = explode('.',$file);
                    if($fext=='txt' && file_exists($subdir.'/'.$fname.'.wav')) {
                        $data =	array('file'=>$file,'audio'=>$fname.'.wav');
                        $file =	file($subdir."/".$file, FILE_IGNORE_NEW_LINES);
                        foreach($file as $lkey => $line) {
                            $tmpline = explode('=',$line);
                            if(!empty($tmpline[1])) $data[$tmpline[0]]=$tmpline[1];
                        }
                        $graba = $data['msg_id'].'.wav';
                        $time = date("Y-m-d H:m:s", $data['origtime']);
                        $info = json_encode($data);
                        $prio = (!empty($data['priority'])) ? $data['priority'] : '';
                        $cate = (!empty($data['category'])) ? $data['category'] : '';
                        $query = $this->db->query("INSERT INTO vm_entry values (0,'".$camps[$data['origmailbox']]."',
                        '".$data['origmailbox']."', '$graba', '$time', '".$data['duration']."', '".$data['context']."',
                        '".$data['exten']."', '".$data['rdnis']."', '".$prio."', '".$data['callerchan']."',
                        '".$data['callerid']."', '".$data['origtime']."', '".$cate."', '".$data['msg_id']."',
                        '$info')");
                        if ($query) {
                            unlink($subdir.'/'.$data['file']);
                            list($fecha, $hora) = explode(' ',$time);
                            list($ano, $mes, $dia) = explode('-', $fecha);
                            if (!file_exists('/var/www/vm/'.$ano)) mkdir('/var/www/vm/'.$ano, 0755, true);
                            if (!file_exists('/var/www/vm/'.$ano.'/'.$mes)) mkdir('/var/www/vm/'.$ano.'/'.$mes, 0755, true);
                            if (!file_exists('/var/www/vm/'.$ano.'/'.$mes.'/'.$dia)) mkdir('/var/www/vm/'.$ano.'/'.$mes.'/'.$dia, 0755, true);
                            rename($subdir.'/'.$data['audio'], '/var/www/vm/'.$ano.'/'.$mes.'/'.$dia.'/'.$data['msg_id'].'.wav');
                            // var_dump('Exito: '.$data['file']);
                        }
                        // var_dump('Error: En el archivo de especificaciones del audio o en el query de inserción para '.$fname.'.'.$fext);
                    }
                    // var_dump('Error: '.$file);
                }
            }
        }

        return true;
    }

}
