<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Datos_model extends CI_Model
{

    public function user_activity($activity = "Login", $status = 1) { // revisar controladores por esta fun
        $uid = $this->session->userdata("uid");
        if (!empty($uid)) {
            $lastus = (object)array("user_id"=>$uid, "evento"=>"", "detalle"=>"");
            $query = $this->db->query("SELECT * from `user_log` where `user_id` = '$uid' order by `id` DESC limit 1");
            if ($query->num_rows() > 0) {
                $lastus = $query->row();
            }
            $ip = getUserIP();
            if (substr($activity, 0, 8) == "Descanso") {
                $breakdesc = substr($activity, 10);
                $query = $this->db->query("SELECT * from break where name = '$breakdesc'
                    order by id desc limit 1");
                $break = $query->row();
                $this->db->query("INSERT into break_entry (id_user, id_break, datetime_init)
                    values (?, ?, now())", [$uid, $break->id]);
            } elseif ($activity == "Sale consola") {
                $this->AgenteSaleConsola($uid);
            }
            if (substr($lastus->detalle, 0, 8) == "Descanso") {
                $this->db->query("UPDATE break_entry set datetime_end=now(),
                    duration=TIME_TO_SEC(TIMEDIFF(now(), COALESCE(datetime_init, NOW())))
                    where id_user='$uid' and datetime_end is null");
            }
            if ($this->db->query("INSERT INTO user_log values (0, ?, now(), ?, ?)",
                array($uid, $activity, $ip))) {
                return true;
            }
            if ($activity == 'Sale consola') {
                $this->db->query("UPDATE user_status set val = '0', cuando = now() where id_user = '$uid'");
                $this->db->query("UPDATE videocall_chans set sala = '', cuando = now() where id_user = '$uid'");
            }
        }

        return $this->db->error();
    }

    public function ses_ab() {
        $hace10m = time() - 600;
        $hace1h  = $hace10m - 3000;
        $this->db->query("DELETE from `ses_ab` where `timestamp` < '$hace10m' and `uid` = '0'");
        $this->db->query("DELETE from `ses_ab` where `timestamp` < '$hace1h' and `uid` = '1'");
        $query = $this->db->query("SELECT * from `ses_ab` where `timestamp` < '$hace10m' and `uid` > 1");
        $res = $query->result();
        $todel = array();
        foreach ($res as $row) {
            $todel[] = $row->uid;
            $ultimo = date("Y-m-d H:i:s", $row->timestamp);
            $this->db->query("INSERT IGNORE INTO `user_log` (`user_id`, `evento`, `detalle`, `ip`)
                values (?, ?, 'Logout', '1')", [$row->uid, $ultimo]);
        }
        $cuantodel = count($todel);
        $todel = implode(",", $todel);
        if ($cuantodel>0) {
            $this->db->query("DELETE from `ses_ab` where `uid` in ($todel)");
        }
        /* Liberamos licencias de campanas por usuarios desconectados */
        $this->db->query('DELETE FROM campaign_licenses WHERE id_user NOT IN (SELECT distinct(uid) FROM ses_ab WHERE uid <> "0")');
        $this->db->query('UPDATE user_trans_opts SET busy = "" WHERE busy NOT IN (SELECT distinct(uid) FROM ses_ab WHERE uid <> "0")');

        return $cuantodel;
    }

    public function getAgenteDisponible($para, $cuenta = 0) {
        try {
            $data = doReq(["url"=>getenv('ARI_BURL')."ari/endpoints","auth"=>getenv('ARI_USER').":".getenv('ARI_PASS'),"nossl"=>true]);
        } catch(Exception $e) {
            $data = false;
        }
        $extvivas = $extocupa = [];
        if (!empty($data)) {
            $endpoints = json_decode($data["data"]);
            foreach ($endpoints as $exten) {
                if ($exten->state == "online") {
                    $extvivas[] = $exten->resource;
                    if (count($exten->channel_ids)>0) {
                        $extocupa[] = $exten->resource;
                    }
                }
            }
        }
        $extvivas = (count($extvivas)>0) ? "'" . implode("','", $extvivas) . "'" : "'0'";
        $extocupa = (count($extocupa)>0) ? "'" . implode("','", $extocupa) . "'" : "'0'";
        $query = $this->db->query("SELECT coalesce(group_concat(ud.id_user),'0') as uo from user_data ud
            left join catalogs c on c.id = ud.id_catalog
            where c.cat = 'userData' and c.val = 'userask'
            and ud.val in ($extocupa)");
        $idsusrsenllamada = $query->row()->uo;
        $logeados = [];
        $query = $this->db->query("SELECT DISTINCT(uid) uid
            FROM ses_ab WHERE uid <> '0'");
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $key => $row) {
                $logeados[] = $row->uid;
            }
        }
        if (empty($extvivas) && empty($logeados)) {
            return false;
        }
        switch ($para) {
            case 'email':
                $query = $this->db->query("SELECT u.id, u.name,
                    cast(group_concat(if(cat.cat = 'userData' and cat.val = 'userask', ud.val, null)) as unsigned) extension,
                    cast(sum(if(cat.cat = 'permisoSec' and cat.val = 'email', ud.val, 0)) as unsigned) permiso,
                    count(e.id) asignados
                    from user u
                    left join user_data ud on ud.id_user = u.id
                    left join catalogs cat on cat.id = ud.id_catalog
                    left join email_entry e on e.id_user = u.id
                    where u.id not in (
                    select id_user from email_entry
                    where datetime_asigned is not null and datetime_reply is null group by id_user)
                    group by u.id
                    having extension in ($extvivas) and permiso=1");
                break;

            case 'whatsapp':
                $query = $this->db->query("SELECT u.`id`, concat(u.`name`, ' ', u.`last`) AS 'nombre',
                    if(perfil.`perfil` is null OR perfil.`perfil` = '','desconocido',perfil.`perfil`) AS 'perfil',
                    ext.ext, wacs.wacs, pw.wap, e.`ev` AS 'ultimo'
                    FROM `user` AS u
                    LEFT JOIN (SELECT ud.id_user, ud.val wap
                        FROM user_data ud LEFT JOIN catalogs c on c.id = ud.id_catalog
                        WHERE c.cat = 'permisoSec' and c.val = 'whatsapp') pw on pw.id_user = u.id
                    LEFT JOIN (SELECT ud.id_user, ud.val perfil
                        FROM user_data ud LEFT JOIN catalogs c on c.id = ud.id_catalog
                        WHERE c.cat = 'userData' and c.val = 'perfil') perfil on perfil.id_user = u.id
                    LEFT JOIN (SELECT ud.id_user, ud.val wacs
                        FROM user_data ud LEFT JOIN catalogs c on c.id = ud.id_catalog
                        WHERE c.cat = 'userData' and c.val = 'whatsapp') wacs on wacs.id_user = u.id
                    LEFT JOIN (SELECT ud.id_user, ud.val ext
                        FROM user_data ud LEFT JOIN catalogs c on c.id = ud.id_catalog
                        WHERE c.cat = 'userData' and c.val = 'userask') ext on ext.id_user = u.id
                    LEFT JOIN (SELECT `id_user`, MAX(`datetime_end`) AS ev
                        FROM `whatsapp_session` group by `id_user`) AS e ON e.`id_user` = u.`id`
                    WHERE u.`id` > 1 AND pw.`wap` = '1'
                    AND FIND_IN_SET('$cuenta', wacs.`wacs`) > 0
                    AND ext.`ext` IN ($extvivas)
                    AND (perfil.`perfil` = 'agente' or perfil.`perfil` = 'supervisor')
                    ORDER BY e.`ev` LIMIT 1");
                break;

            case 'videollamada':
                $sexo = (empty($cuenta['sexo'])) ? '0' : $cuenta['sexo'];
                $subp = (empty($cuenta['perfil'])) ? '0' : $cuenta['perfil'];
                $soli = '' . $sexo . $subp;
                switch ($soli) {
                    case 'F2':
                        $cond = "AND vc.tipo = '1'";
                        break;
                    case 'M2':
                        $cond = "AND vc.tipo = '2'";
                        break;
                    case 'F1':
                        $cond = "AND vc.tipo = '3'";
                        break;
                    case 'M1':
                        $cond = "AND vc.tipo = '4'";
                        break;
                    case 'F0':
                        $cond = "AND (vc.tipo = '1' || vc.tipo = '3')";
                        break;
                    case 'M0':
                        $cond = "AND (vc.tipo = '2' || vc.tipo = '4')";
                        break;
                    case '01':
                        $cond = "AND (vc.tipo = '3' || vc.tipo = '4')";
                        break;
                    case '02':
                        $cond = "AND (vc.tipo = '1' || vc.tipo = '2')";
                        break;
                    default:
                        $cond = "AND vc.tipo < 5";
                        break;
                }
                $query  = $this->db->query("SELECT vc.id_user, vc.sala from videocall_chans vc
                    LEFT JOIN (SELECT ud.id_user, ud.val perfil
                        FROM user_data ud LEFT JOIN catalogs c on c.id = ud.id_catalog
                        WHERE c.cat = 'userData' and c.val = 'perfil') perfil on perfil.id_user = vc.id_user
                    LEFT JOIN (SELECT ud.id_user, ud.val campanas
                        FROM user_data ud LEFT JOIN catalogs c on c.id = ud.id_catalog
                        WHERE c.cat = 'userData' and c.val = 'campanas') cam on cam.id_user = vc.id_user
                    WHERE vc.sala <> '' AND vc.estatus = '1' $cond
                    AND perfil.perfil = 'agente'
                    AND $cuenta[c] in (cam.campanas)
                    AND vc.id_user not in ($idsusrsenllamada) ORDER BY vc.cuando ASC LIMIT 1");
                break;

            default:
                return false;
        }

        return $query->row();
    }

    public function getUserData($id_user = '') {
        $id_user = ($id_user == '') ? $this->session->userdata('uid') : $id_user;
        $query = $this->db->query("SELECT * FROM user_full where id = ?", array($id_user));
        $data = $query->row_array();
        $data = $this->getMoreUserData($data);
        $data['bago'] = getenv('BAGO_FURL');
        $data['servask'] = getenv('ARI_FURL');
        if (empty($data['campanas'])) {
            die('Sin campaña activa asignada. Consulta con el administrador');
        }
        $data['ruta'] = ($this->router->method=='index') ? $this->router->class : $this->router->class . '/' . $this->router->method;
        $data['lic'] = $this->getLicencia();
        // las colas relacionadas a las campañas asignadas al usuario
        $query = $this->db->query("SELECT IFNULL(group_concat(`name`),'') mostrar FROM `queue` WHERE `show` = 1");
        $queues = explode(",", $query->row()->mostrar);
        $query = $this->db->query("SELECT dids from campaign where id in (" . $data['campanas'] . ")");
        $camps = $query->result();
        $data['queues'] = array();
        foreach ($camps as $row) {
            $ecdids = explode(",", $row->dids);
            foreach ($ecdids as $value) {
                if (in_array($value, $queues)) {
                    $data['queues'][] = $value;
                }
            }
        }
        if(in_array("videocall", $data['permisoSec'])) {
            $query = $this->db->query("SELECT url FROM videocall_serv
                WHERE activ=1 AND id_campaign in ($data[campanas]) LIMIT 1");
            $data['vcServer'] = ($query->num_rows()==1) ? $query->row()->url : "";
        }
        unset($data["pass"]);

        return $data;
    }

    // getFullu Trae un usuario por su id desde la vista user_full
    // queda pendiente validar si el usuario que lo solicita coincide en alguna campaña
    public function getFullu($id) : object {
        $query = $this->db->query("SELECT * FROM user_full WHERE id = ?", [$id]);

        return $query->row();
    }

    public function getChatUsers() {
        $query = $this->db->query('SELECT ud.val, id_user
            FROM user_data ud
            left join catalogs cat on cat.id = ud.id_catalog
            where cat.val = "chatId" AND cat.cat = "userData" AND ud.val!=""');
        $res = $query->result();
        foreach ($res as $row) {
            $data[$row->val] = $row->id_user;
        }
        return $data;
    }

    public function getParams($cat, $tipo='objeto') {
        if ($cat == 'sms') {
            $query = $this->db->query("SELECT *, `url` as 'server' FROM extapi WHERE name = 'Directo'");
            if ($tipo == 'objeto') {
                $respu = $query->row();
            } else {
                $respu = $query->row_array();
            }
        } else {
            if ($cat == 'whatsapp') $cat .= "' or `cat` = 'wabox";
            $query = $this->db->query("SELECT * FROM `catalogs` WHERE `cat` = '$cat'");
            $respu = array();
            foreach ($query->result() as $row) {
                $respu[$row->eti] = $row->val;
            }
            if ($tipo == 'objeto') {
                return (object)$respu;
            }
        }

        return $respu;
    }

    public function getParamsFull($cat) {
        $query = $this->db->query("SELECT * FROM catalogs WHERE cat = ? order by cat, num_order, id", array($cat));
        return $query->result();
    }

    public function llamadas() {
        $start_time = microtime(true);
        /* Inicializa todas las llamadas que no estén en la tabla call_entry y las deja "En curso" */
        $query = $this->db->query("SELECT DATE_SUB(`datetime_received`, INTERVAL 1 MINUTE) `ultimo`
            FROM `call_entry` ORDER BY `id` DESC LIMIT 1");
        $res = $query->row();
        $ultimo = (!empty($res)) ? $res->ultimo : '2000-01-01 00:00:00';
        $query = $this->db->query("SELECT * from asteriskcdrdb.cel
            where eventtime>'$ultimo' and `uniqueid` = `linkedid` and eventtype='CHAN_START' and (length(exten)>=4 OR exten='s') order by eventtime ASC limit 100");
        $nuevas = $query->result();
        $valores = "";
        foreach ($nuevas as $key => $row) {
            if (false !== strpos($row->cid_name, '-pred-')) {
                list($num, $pred, $id_desp, $id_reg) = explode("-", $row->cid_name);
                $query = $this->db->query("SELECT * from disp_sent where id_dispatcher=?
                    AND id_registro=? order by id DESC limit 1", [$id_desp, $id_reg]);
                if ($query->row()->actu == 0) {
                    $this->db->query("UPDATE disp_sent set actu = 1, fechahora_regreso = now(),
                        tiempo_ret = TIME_TO_SEC(TIMEDIFF(now(), fechahora_lanzada))
                        where id_dispatcher = ? and id_registro = ?",
                        array($id_desp, $id_reg)
                    );
                    $this->db->query("UPDATE disp_".$id_desp." set status = if(access >= 5, 1, 0),
                        last_update=now(), llamadas=access where id='".$id_reg."'");
                }
            }
            if (false !== strpos($row->context, 'from-trunk') && $row->exten=='s') {
                $type = 'Saliente';
                $cid_name = "";
                $cid_num = "";
                $queue = "";
            } elseif(false !== strpos($row->context, 'from-trunk') || false !== strpos($row->cid_name, 'pred')) {
                $type = 'Entrante';
                $cid_name = addslashes($row->cid_name);
                $cid_num = $row->cid_num;
                $queue = $row->exten;
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
            $query = $this->db->query("INSERT IGNORE INTO call_entry (uniqueid, datetime_received, cid_name, cid_num, queue, type, status) values " . $valores);
        }
        /* Proceso para completar las llamadas "En curso" */
        $query = $this->db->query("SELECT * from call_entry where status='En curso' order by id DESC limit 50");
        $encurso = $query->result();
        foreach ($encurso as $row) {
            $query = $this->db->query("SELECT * from asteriskcdrdb.cel
                where `uniqueid` = '".$row->uniqueid."' and `eventtype` = 'CHAN_END' order by `id` desc limit 1");
            $callend = $query->row();
            if (!empty($callend)) {
                $this->terminaLlamada($row, $callend);
            }
        }
        /* completar formulario llamada con linkedid */
        $query = $this->db->query("SELECT table_name FROM information_schema.columns
            WHERE table_name REGEXP '^formd_([0-9]){1,4}$' OR table_name REGEXP '^formd_([0-9]){1,4}_crm$'
            GROUP BY table_name ORDER BY table_name");
        $tablas = $query->result();
        foreach ($tablas as $tabla) {
            if (stripos($tabla->table_name, '_crm')) {
                $this->db->query("UPDATE ".$tabla->table_name." as lt set linkedid = (SELECT c.linkedid
                    FROM asteriskcdrdb.cel as c
                    WHERE c.uniqueid = lt.uniqueid limit 1)
                    WHERE (lt.linkedid is null OR lt.linkedid = '') AND lt.uniqueid IS NOT NULL AND lt.uniqueid <> ''
                    AND DATE(fecha) >= DATE(subdate(NOW(), 1))");
            } else {
                $this->db->query("UPDATE ".$tabla->table_name." as lt set linkedid = (SELECT c.linkedid
                    FROM asteriskcdrdb.cel as c
                    WHERE c.uniqueid = lt.uniqueid limit 1)
                    WHERE (lt.linkedid is null OR lt.linkedid = '')  AND lt.uniqueid IS NOT NULL AND lt.uniqueid <> ''
                    and DATE(apertura) >= DATE(subdate(NOW(), 1))");
            }
        }
        $end_time = microtime(true);
        $duration = $end_time - $start_time;
        return $duration;
    }

    public function terminaLlamada($reg, $end) {
        $grabacion = '';
        $exten = (!empty($reg->cid_name)) ? addslashes($reg->cid_name) : '0';
        $datetime_init = 'NULL';
        $cola = $reg->queue;
        $did = $reg->cid_name;
        $dbas = $this->load->database('asterisk', true);
        $query = $dbas->query("SELECT * FROM cel WHERE uniqueid = '".$reg->uniqueid."'
            AND eventtype='APP_START' AND appname='MixMonitor' ORDER BY id ASC LIMIT 1");
        $graba = $query->row();
        if(!empty($graba)) {
            $grabacion = explode("/", explode(",", $graba->appdata)[0]);
            $grabacion = end($grabacion);
            $did = explode("-", $grabacion)[1];
            $cola = (empty($cola)) ? $did : $cola;
        } else {
            $query = $dbas->query("SELECT recordingfile from cdr where linkedid = ? and recordingfile <> '' limit 1",
            array($reg->uniqueid));
            $cdrgraba = $query->row();
            if(!empty($cdrgraba)) {
                $grabacion = $cdrgraba->recordingfile;
                $grabacion = explode("/", $grabacion);
                $grabacion = end($grabacion);
                $did = explode("-", $grabacion)[1];
                $cola = (empty($cola)) ? $did : $cola;
            }
        }
        $query = $dbas->query("SELECT `eventtime`, substring(`peer`,7,4) as 'peer' FROM `cel`
            WHERE `uniqueid` = '".$reg->uniqueid."' AND `eventtype`='BRIDGE_ENTER' ORDER BY `id` ASC LIMIT 1");
        $bridge = $query->row();
        if(!empty($bridge)) {
            $datetime_init = "'".$bridge->eventtime."'";
            if (empty($exten) || $reg->type =="Entrante") $exten = $bridge->peer;
        }
        if (empty($exten)) {
            $query = $dbas->query("SELECT cid_num from `cel`
                WHERE `linkedid` = '".$reg->uniqueid."' AND `eventtype`='BRIDGE_ENTER' ORDER BY `id` ASC LIMIT 1");
            $row = $query->row();
            if (is_numeric($row->cid_num) && strlen($row->cid_num) <= 5) $exten = $row->cid_num;
        }
        $query = $dbas->query("SELECT `time` AS 'eventtime', `data2` AS 'cid_num'
            from `queue_log` where `callid` = '".$reg->uniqueid."' and `event` = 'ENTERQUEUE'");
        if ($query->num_rows()<1) {
            $query = $dbas->query("SELECT `eventtime`, `cid_num` FROM `cel`
                WHERE `linkedid` = '".$reg->uniqueid."' AND ((`eventtype`='APP_START' AND `appname`='Queue')
                OR `context` like '%queue%') ORDER BY `id` ASC LIMIT 1");
        }
        $queue = $query->row();
        $queued = "";
        $inicio = $end->eventtime;
        if (!empty($queue->eventtime)) {
            $queued = "datetime_queued='".$queue->eventtime."',";
            $inicio = $queue->eventtime;
        }
        if ($datetime_init=="NULL") {
            if (false !== strpos($reg->cid_name, '-pred-')) {
                list($num, $pred, $id_desp, $id_reg) = explode("-", $reg->cid_name);
                $this->db->query("INSERT INTO disp_".$id_desp."_qualif
                    (`id_disp_data`,`uniqueid`,`tipificacion`,`saved_by`,`saved_when`) values
                    ('".$id_reg."','".$reg->uniqid.",'CC',''1',now())");
                $this->db->query("UPDATE disp_" . $id_desp .
                  " set qualif='CC', last_update=now() where id='".$id_reg."'");
            }
            if (!empty($queue->eventtime)) {
                $status = "Abandonada";
            } else {
                $status = "Abandonada Troncal";
            }
        } else {
            $status = "Terminada";
        }
        $query = $dbas->query("SELECT `eventtime` FROM `cel` WHERE `uniqueid` = '".$reg->uniqueid."'
            AND `eventtype`='APP_START' AND `appname`='VoiceMail' ORDER BY `id` ASC LIMIT 1");
        $vm = $query->num_rows();
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
        $agent_id = (empty($exten) || empty($res)) ? 'NULL' : "'".$res->id."'";
        $id_campaign = 'null';
        // Para todas las opciones de ivr debe existir una campaña o al relacionar fallará el did a 5 dígitos.
        if (!empty($did)) {
            $query = $this->db->query("SELECT `id` from `campaign` where FIND_IN_SET('$did', `dids`) limit 1");
            $campa = $query->row();
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
        $res = $this->getParams('sl');
        if ($status == "Abandonada" && $diff <= $res->segundos) {
            $status = "Abandonada nosl";
        }
        $hangby = "Remoto";
        $query = $dbas->query("SELECT `extra` FROM `cel` WHERE `linkedid` = '".$reg->uniqueid."'
            AND `eventtype`='HANGUP' AND (`context`='ext-local' OR `context`='from-trunk') ORDER BY `id` DESC LIMIT 1");
        $cuelgareg = $query->row();
        if (!empty($cuelgareg)) {
            $cuelgareg = json_decode($cuelgareg->extra);
            if (stripos($cuelgareg->hangupsource, "PJSIP") !== false) $hangby = "Local";
        }
        $this->db->query("UPDATE call_entry set id_user=".$agent_id.", ".$masset."
            $queued
            datetime_init=".$datetime_init.",
            datetime_end='".$end->eventtime."',
            duration=TIME_TO_SEC(TIMEDIFF(datetime_end, COALESCE(datetime_init, datetime_end))),
            duration_wait=TIME_TO_SEC(TIMEDIFF(COALESCE(datetime_init, datetime_end), COALESCE(datetime_queued, datetime_received))),
            status='".$status."', hangup='".$hangby."' where id='".$reg->id."'");
    }

    public function truncacel() { // ADVERTENCIA! este proceso vacía totalmente la tabla asteriskcdrdb.cel
        $this->db->query("TRUNCATE asteriskcdrdb.queue_log");
        return $this->db->query("TRUNCATE asteriskcdrdb.cel");
    }

    public function logerror($data) {
        return $this->db->query("INSERT INTO error_log (id_user, datetime_init, extension, type) values (?,now(),?,?)",
            array($data['id_user'], $data['extension'], $data['error']));
    }

    // Función que devuelve Campañas en el sistema
    // $activas BOOLEAN // Todas las campañas o solo las activas actualmente
    // $user BOOLEAN // Campañas relacionadas al usuario que hace la solicitud, o todas.
    // $tabla BOOLEAN // Array de objetos con una campaña por fila o solo el listado de id's
    // $array BOOLEAN // formato de respuesta, array o cadena de id's sin espacios separada por comas
    // Esta es una gran función diseñada por KINON
    public function getCampanas($tabla = true, $array = true) {
        $user = ($this->udata['perfil'] == 'admin') ? false : true;
        $filtro = ($user) ? "WHERE `id` in (" . $this->udata['campanas'] . ") AND `active` = '1'" : "";
        $res = [];
        $query = $this->db->query("SELECT * from `campaign` $filtro order by `active` DESC, `name`");
        if ($query->num_rows()>0) {
            $resul = $query->result();
            if ($tabla) {
                foreach ($resul as $row) {
                    $res[$row->id] = $row;
                }
            } else {
                foreach ($resul as $row) {
                    $res[] = $row->id;
                }
                if(!$array) $res = implode(",", $res);
            }
        } else {
            if ($user) {
                $this->session->set_flashdata('errormsg', 'Sin campaña(s) asignada(s).');
                redirect();
            }
            $this->session->set_flashdata('errormsg', 'Se debe crear por lo menos una campaña.');
            redirect();
        }
        return $res;
    }

    // Traer listado de usuarios de determinada campaña para usar en select
    public function getUsuariosCampana($cid = 0) {
        $query = $this->db->query("SELECT uf.id, concat(uf.last, ' ', uf.name, ' (', uf.perfil, ')',
            IF(uf.active = 0, ' -inactivo-', '')) nombre
            FROM user_full uf
            WHERE uf.id > 1 AND uf.id <> '9999' AND (uf.perfil = 'agente' OR uf.perfil = 'supervisor')
            AND find_in_set(?, uf.campanas) > 0
            ORDER BY uf.perfil, uf.last", [$cid]);

        return $query->result();
    }

    // Recibe "Tabla" de campañas, array de objetos campañas, query->result() de tabla campaign
    // o getCampanas de datos_model, perfil admin recibe TODOS los usuarios
    public function getRelUsers($data = []) {
        $maswhere = (empty($data["bus"])) ? '' :
            "AND (u.name like '%" .
            $data["bus"] . "%' or u.user like '%" . $data["bus"] . "%' or u.last like '%" .
            $data["bus"]."%' or ud3.val like '%".$data["bus"]."%')";
        $maswhere .= ($this->udata['perfil'] == 'admin' ||
            (isset($data["act"]) && $data["act"] === false)) ? '' : ' AND u.active = \'1\'';
        $req_cams = [];
        $admreq = false;
        if (empty($data["cam"])){
            $req_cams = $this->getCampanas(false);
            if($this->udata['perfil']=="admin") {
                $admreq = true;
            }
        } else {
            if (is_array($data["cam"]) && !empty($data["cam"][key($data["cam"])]->id)) {
                $req_cams = explode(',',$this->getIdsFromArr($data["cam"]));
            } elseif (!is_array($data["cam"])) {
                $req_cams = explode(',',$data["cam"]);
            }
        }
        $valid_cams = explode(",", $this->udata["campanas"]);
        $filcams = array_intersect($req_cams, $valid_cams);
        if (count($filcams) > 0) {
            $maswhere .= " AND (find_in_set(" . implode(", ud.val) > 0 || find_in_set(", $filcams) . ", ud.val) > 0";
            if ($admreq) { $maswhere .= " || ud.val=''"; }
            $maswhere .= ")";
        } else {
            return true;
        }

        if (empty($data["per"])) {
            switch ($this->udata['perfil']) {
                case 'admin':
                    break;
                case 'superior':
                    $maswhere .= " AND ud2.val in ('agente','supervisor','crm','superior')";
                    break;
                default:
                    $maswhere .= " AND ud2.val in ('agente','supervisor','crm')";
                    break;
            }
        } else {
            $maswhere .= ' AND ud2.val in (' . $data["per"] . ')';
        }

        $query = $this->db->query("SELECT u.id, u.name, u.last, concat(u.name,' ',u.last) nombre,
            if(ud.val is null OR ud.val = '',0,ud.val) AS campanas,
            if(ud2.val is null OR ud2.val = '','agente',ud2.val) AS perfil,
            ud3.val AS extension
            FROM user AS u
            INNER JOIN user_data AS ud ON ud.id_user=u.id
            INNER JOIN user_data AS ud2 ON ud2.id_user=u.id
            INNER JOIN user_data AS ud3 ON ud3.id_user=u.id
            LEFT JOIN catalogs preud ON preud.id = ud.id_catalog
            LEFT JOIN catalogs preud2 ON preud2.id = ud2.id_catalog
            LEFT JOIN catalogs preud3 ON preud3.id = ud3.id_catalog
            WHERE u.id > 1 $maswhere
            AND preud.cat='userData' AND preud.val = 'campanas'
            AND preud2.cat='userData' AND preud2.val = 'perfil'
            AND preud3.cat='userData' AND preud3.val = 'userask'
            ORDER BY u.name, u.last");
        $regs = $query->num_rows();
        $salida = $query->result();

        if(isset($data["pag"]) && $regs>0) {
            $lim = (empty($data["lim"])) ? REGS_POR_PAG : $data["lim"];
            $query = $this->db->query("SELECT u.id, IFNULL(sa.uid, 0) as uid, u.active, u.user, u.name, u.last,
                concat(u.name,' ',u.last) nombre,
                if(ud.val is null OR ud.val = '',0,ud.val) AS campanas,
                if(ud2.val is null OR ud2.val = '','agente',ud2.val) AS perfil,
                ud3.val AS extension
                FROM user AS u
                INNER JOIN user_data AS ud ON ud.id_user=u.id
                INNER JOIN user_data AS ud2 ON ud2.id_user=u.id
                INNER JOIN user_data AS ud3 ON ud3.id_user=u.id
                LEFT JOIN catalogs preud ON preud.id = ud.id_catalog
                LEFT JOIN catalogs preud2 ON preud2.id = ud2.id_catalog
                LEFT JOIN catalogs preud3 ON preud3.id = ud3.id_catalog
                LEFT JOIN ses_ab sa on sa.uid = u.id
                WHERE u.id > 1 $maswhere
                AND preud.cat='userData' AND preud.val = 'campanas'
                AND preud2.cat='userData' AND preud2.val = 'perfil'
                AND preud3.cat='userData' AND preud3.val = 'userask'
                ORDER BY if(sa.uid>0,0,1), if(ud3.val<>'',0,1), ud3.val, u.name, u.last
                LIMIT $data[pag], $lim");
            $res = $query->result();
            $salida = ["regs"=>$regs,"data"=>$res,"pag"=>$data["pag"],"rpp"=>$lim];
        }

        return $salida;
    }

    // Función que devuelte Cuentas de Whatsapp en el sistema
    // $activas BOOLEAN // Todas las campañas o solo las activas actualmente
    // $user BOOLEAN // Campañas relacionadas al usuario que hace la solicitud, o todas.
    // $tabla BOOLEAN // Array de objetos con una campaña por fila o solo el listado de id's
    // $array BOOLEAN // formato de respuesta, array o cadena de id's sin espacios separada por comas
    public function getWaCtas($activas = true, $user = true, $tabla = true, $array = true) {
        $uid = $this->udata['id'];
        $upf = $this->udata['perfil'];
        if ($uid == 1 || $upf == 'admin') {
            $activas = false;
            $filtro = "";
        } else {
            $filtro = "WHERE `id_campaign` in (".$this->udata['campanas'].")";
            $filtro .= ($activas) ? " AND `active` = '1'" : "";
        }
        $res = [];
        $query = $this->db->query("SELECT * from `whatsapp_cuentas` $filtro order by `nombre`");
        if ($query->num_rows()>0) {
            $resul = $query->result();
            if ($tabla) {
                foreach ($resul as $key => $row) {
                    $res[$row->id] = $row;
                }
            } else {
                foreach ($resul as $row) {
                    $res[] = $row->id;
                }
                if(!$array) $res = implode(",", $res);
            }
        } else {
            if(!$array) $res = "0";
        }

        return $res;
    }

    // Función que devuelte Cuentas de Email en el sistema
    // $activas BOOLEAN // activas únicamente o todas
    public function getEmailCtas($activas = true, $uso = null) {
        $uid = $this->udata['id'];
        $upf = $this->udata['perfil'];
        $uso = (int)$uso;
        if ($uid == 1 || $upf == 'admin') {
            $activas = false;
            $filtro = "";
            if( $uso != null ) { // 1.- CRM Remitente 2.- Modulo Emai
                $filtro = "WHERE `use` = $uso"; 
            }
        } else {
            $filtro = "WHERE `id_campaign` in (".$this->udata['campanas'].")";
            $filtro .= ($activas) ? " AND `activa` = '1'" : "";
            if( $uso != null ) { // 1.- CRM Remitente 2.- Modulo Emai
                $filtro .= " AND `use` = $uso";
            }
        }

        $query = $this->db->query("SELECT * from `email_account` $filtro order by `nombre`");
        $resul = $query->result();
        return $resul;
    }

    public function get_des_dep($data) {
        $precol = $data['col'] - 1;
        $query = $this->db->query("SELECT distinct(`val".$data['col']."`) ops from disp_depend where campo=? and `val".
            $precol."`=?", array($data['campo'], $data['esteval']));
        $res = $query->result();
        $respu = '<option val="">-- Elige --</option>';
        foreach ($res as $key => $row) {
            $respu .= '<option val="'.$row->ops.'">'.$row->ops.'</option>';
        }

        return $respu;
    }

    // regresa un string que contiene los ids de un array de objetos
    public function getIdsFromArr(array $array = []) {
        $res = [];
        foreach ($array as $key => $row) {
            $res[] = $row->id;
        }

        return implode(",", $res);
    }

    public function getCatalogs() {
        $query = $this->db->query("SELECT * from catalogs where cat not like 'permiso%'");
        $result = $query->result();
        $res = array();
        foreach ($result as $key => $row) {
            $res[$row->cat][$row->eti] = $row->val;
        }
        return $res;
    }

    public function colas($dids = array()) {
        $pruebas = "default has 0 calls (max unlimited) in 'ringall' strategy (0s holdtime, 0s talktime), W:0, C:0, A:0, SL:0.0%, SL2:0.0% within 0s
   No Members
   No Callers

2907 has 0 calls (max unlimited) in 'leastrecent' strategy (0s holdtime, 0s talktime), W:0, C:2, A:0, SL:0.0%, SL2:0.0% within 60s
   Members:
      Aldo (Local/5100@from-queue/n from hint:5100@ext-local) (ringinuse enabled) (dynamic) (Not in use) has taken no calls yet
      Kinon (Local/5090@from-queue/n from hint:5090@ext-local) (ringinuse enabled) (dynamic) (Not in use) has taken no calls yet
   No Callers

9809 has 0 calls (max unlimited) in 'leastrecent' strategy (0s holdtime, 0s talktime), W:2, C:1, A:1, SL:90.0%, SL2:98.0% within 60s
   Members:
      Aldo (Local/5100@from-queue/n from hint:5100@ext-local) (ringinuse enabled) (dynamic) (Not in use) has taken no calls yet
      Kinon (Local/5090@from-queue/n from hint:5090@ext-local) (ringinuse enabled) (dynamic) (Not in use) has taken no calls yet
      Genaro (Local/5091@from-queue/n from hint:5091@ext-local) (ringinuse enabled) (dynamic) (Not in use) has taken no calls yet
   No Callers";
       $data = shell_exec('/usr/sbin/asterisk -rx "queue show"');
        if (empty($data) && ENVIRONMENT=='development'){
            $data = $pruebas;
        }
        if (!empty($data)) {
            $data = explode("\n", $data);
            $colas = array("wait"=>0, "answered"=>0, "hanged"=>0, "longestwait"=>"0:00");
            $miembro = $caller = "no";
            $queue = [];
            foreach ($data as $linea) {
                if(strlen($linea)>0 && $linea[0]!=" ") {
                    $espacios = explode(' ',trim($linea));
                    $queue = $espacios[0];
                    if (in_array($queue, $dids)) {
                        $colas[$queue]['wait'] = $espacios[2];
                        $colas['wait'] += (int)$espacios[2];
                        $colas[$queue]['answered'] = rtrim(explode(':',trim($espacios[14]))[1],",");
                        $colas['answered'] += (int)rtrim(explode(':',trim($espacios[14]))[1],",");
                        $colas[$queue]['hanged'] = rtrim(explode(':',trim($espacios[15]))[1],",");
                        $colas['hanged'] += (int)rtrim(explode(':',trim($espacios[15]))[1],",");
                        $colas[$queue]['servicelevel'] = rtrim(explode(':',trim($espacios[16]))[1],",");
                    }
                } elseif(strlen($linea)>0 && $miembro == "si") {
                    if (substr($linea, 0, 5)=="   No" || substr($linea, 0, 5)=="   Ca") {
                        $miembro = $caller = "no";
                    } else {
                        if (in_array($queue, $dids)) {
                            $espacios = explode('(',trim($linea));
                            $exten = explode('@',explode('/',trim($linea))[1])[0];
                            if(stripos($linea, 'Not in use') !== false) {
                                $status = 'Not in use';
                            } elseif (stripos($linea, 'in call') !== false) {
                                $status = 'In call';
                            } else {
                                $status = 'Unavailable';
                            }
                            $colas[$queue]['members'][$exten] = $status;
                        }
                    }
                } elseif(strlen($linea)>0 && $caller == "si") {
                    if (substr($linea, 0, 5)=="   No" || substr($linea, 0, 5)=="   Ca") {
                        $miembro = $caller = "no";
                    } else {
                        if (in_array($queue, $dids)) {
                            if (stripos($linea, 'wait') !== false) {
                                $uno = explode('wai',trim($linea));
                                $dos = explode(' ',$uno[1]);
                                $tre = explode(',',$dos[1]);
                                $colas[$queue]['waits'][] = $tre[0];
                                if ($tre[0]>$colas['longestwait']) $colas['longestwait'] = $tre[0];
                            }
                        }
                    }
                }
                if (strlen($linea)>0 && substr($linea, 0, 6)=="   Mem") {
                    $miembro = "si";
                }
                if (strlen($linea)>0 && substr($linea, 0, 5)=="   Ca") {
                    $caller = "si";
                }
            }
            return $colas;
        }
        return;
    }

    public function manejadorqueries($data) {
        // utilizar ops para valores de tipo ? en los queries
        if (empty($data['prequery'])) {
            return $data;
        }
        if (empty($data['ops'])) {
            $query = $this->db->query($data['prequery']);
        } else {
            $query = $this->db->query($data['prequery'], $data['ops']);
        }
        if(!$query) {
            return $data;
        }
        $data["cuenta"] = $query->num_rows();
        $data["campos"] = $query->list_fields();
        $data["data"]   = $query->result_array();
        if ($data['pag'] !== 'x') {
            $data['totales'] = (!empty($data['totales_query'])) ? $this->db->query($data['totales_query'])->row() : false;
            $filas           = array_slice($data["data"], $data['pag'], $data['rpp']);
            $data['data']    = [];
            foreach ($filas as $fila) {
                $data['data'][] = (object)$fila;
            }
        }
        unset($data['prequery']);
        unset($data['totales_query']);
        unset($data['ops']);

        return $data;
    }

    public function upduserstatus($data) {
        $uid = $this->udata['id'];
        if ($this->db->query("INSERT INTO user_status (id_user, sec, val, cuando) values ($uid, ?, ?, now())
            ON DUPLICATE KEY UPDATE val=?, cuando=now()", [$data['sec'], $data['val'], $data['val']])) {
            return true;
        } else {
            return ["tipo"=>"error", "msg"=>"Error al guardar en la base de datos"];
        }
    }

    public function AgenteSaleConsola($uid) {
        // $this->db->query("UPDATE videocall_entry set datetime_end=now(),
        //     duration=TIME_TO_SEC(TIMEDIFF(now(), COALESCE(datetime_init, NOW()))),
        //     duration_wait=TIME_TO_SEC(TIMEDIFF(COALESCE(datetime_init, NOW()), datetime_entry_queue)),
        //     status='Agente cuelga' where id_user='$uid' and datetime_end is null");
    }

    public function getBasUdata($uid) {
        if (empty($uid)) return false;
        $query = $this->db->query("SELECT c.val cat, ud.val FROM user_data ud
            LEFT JOIN catalogs c ON ud.id_catalog = c.id
            WHERE ud.id_user = '$uid'");
        $res = [];
        foreach ($query->result() as $key => $row) {
            $res[$row->cat] = $row->val;
        }

        return $res;
    }

    public function baneaip($ip) {
        $this->db->query("INSERT INTO banned_ips (ip, fecha) VALUES (?, NOW())", [$ip]);
    }

    public function ip_baneada($ip) {
        $query = $this->db->query("SELECT * FROM banned_ips WHERE ip = ?", [$ip]);
        return $query->num_rows();
    }

    private function getMoreUserData($data) {
        if ($data['id'] == 1) { $data['perfil'] = 'admin'; }
        if ($data['perfil'] == 'admin') {
            $data['pervl'] = $data['perci'] = "1,1,1,1,1";
            $query = $this->db->query("SELECT group_concat(id) cams from campaign");
            $data['campanas'] = trim($query->row()->cams, ',');
            $query = $this->db->query("SELECT id, cat, eti, val 'data', 1 'val'
                from catalogs
                where cat like 'permiso%'
                order by cat, eti");
        } else {
            $query = $this->db->query("SELECT cat.cat, cat.eti, cat.val 'data', ud.val
                FROM user_data ud JOIN catalogs cat on cat.id = ud.id_catalog
                WHERE id_user = ? AND cat.cat like 'permiso%' AND ud.val = '1'", [$data['id']]);
        }
        foreach ($query->result() as $row) {
            $data[$row->cat][] = $row->data;
        }
        if ($data['perfil'] != 'admin' && !empty($data['campanas'])) {
            $query = $this->db->query("SELECT group_concat(id) cams from campaign where id in (".trim($data['campanas'],',').") and active = '1'");
            $data['campanas'] = $query->row()->cams;
        }

        return $data;
    }

    private function getLicencia() {
        $this->idfor = (empty($this->idfor)) ? 'd-m-Y' : $this->idfor;
        $hoy = new DateTime();
        $ret = ['tipo'=>'lc', 'usuarios'=>0, 'cliente'=>'Assertive', 'expira'=>$hoy->format($this->idfor), 'queda'=>0, 'ltt'=>'Básica'];
        $res = doReq(["url"=>getenv('BAGO_BURL')."licencia", "nossl"=>true]);
        $res = bagoLicenciaDecode($res);
        if (!empty($res) && !empty($res->tipo)) {
            $ret['tipo'] = $res->tipo;
            $ret['usuarios'] = $res->usuarios;
            $ret['cliente'] = $res->cliente;
            $ret['ltt'] = ($res->tipo == "la") ? "Premium" : (($res->tipo == "lb") ? "Extendida" : "Básica");
            $fin = bagoLicenciaFecha($res->expira);
            $ret['expira'] = $fin->format($this->idfor);
            $interval = $hoy->diff($fin);
            if ($hoy <= $fin) {
                $ret['queda'] = (int)$interval->format('%a');
            } else {
                die('Tu LICENCIA ha expirado!');
            }
        } else {
            $ret['tipo'] = 'CT';
            $ret['usuarios'] = 9999;
            $ret['cliente'] = 'contingencia';
            $ret['expira'] = date($this->idfor, strtotime('+365 days'));
            $ret['queda'] = 365;
        }
        if ($ret['queda'] == 0) {
            $this->session->set_flashdata('errormsg', 'Hoy es tu último día de licencia, RENUEVA.');
        } elseif($ret['queda']<=2) {
            $this->session->set_flashdata('errormsg', 'Solo queda(n) '.$ret['queda'].' día(s) y tu licecia habrá expirado, RENUEVA.');
        }

        return $ret;
    }

    public function gimejson($data) {
        // t = tabla, c = campos (sin espacios, separados por comas), i = id, iv = id valor
        $campos = (empty($data['c'])) ? "*" : "`" . str_replace(",","`,`",$data['c']) . "`";
        $query = $this->db->query("SELECT $campos from `$data[t]` where `$data[i]` = ? limit 1", [$data['iv']]);
        $res = $query->row();
        return $res;
    }

}
