<?php

class Email_model extends CI_Model
{

    private $dtfor; // date time format
    private $dfor; // date format
    private $idfor; // input date format

    public function __construct() {
        $sispar = $this->datos_model->getParams("sistema");
        $this->dtfor = $sispar->FormatoFechaMysql;
        $this->idfor = $sispar->FormatoFechaInput;
        $this->dfor = explode(" ", $this->dtfor)[0];
    }

    public function getCuentas($campanas, $uso = null) {
        $where_uso = '';
        if ( $uso != null ) {
            $uso = (int) $uso;
            $where_uso = "AND `use` = $uso";
        }
        $query = $this->db->query("SELECT ea.*, c.`name` AS 'campana' from `email_account` ea
            join `campaign` c on c.`id` = ea.`id_campaign` where ea.`id_campaign` in ($campanas) $where_uso");
        return $query->result();
    }

    public function getCta($id) {
        $query = $this->db->query("SELECT * from email_account where id = ?", [$id]);
        return $query->row();
    }

    public function guardarEntrante($email) {
        $paraguardar = $email;
        unset($paraguardar->attachments);
        $json = json_encode($paraguardar);
        $this->db->query("INSERT INTO `email_data` (`fecha`, `json`) values (now(), ?)", [$json]);
        $idemaildata = $this->db->insert_id();
        $date = date('Y-m-d H:i:s', strtotime($email->headerInfo->MailDate));
        $seaddr = "";
        $sename = "";
        $to = [];
        if (!empty($email->headerInfo->to) && count($email->headerInfo->to) > 0) {
            foreach ($email->headerInfo->to as $key => $value) {
                if (!empty($value->mailbox)) {
                    $to[] = $value->mailbox."@".$value->host;
                }
            }
        }
        $to = implode(", ", $to);

        $cc = [];
        if (!empty($email->headerInfo->cc) && count($email->headerInfo->cc) > 0) {
            foreach ($email->headerInfo->cc as $key => $value) {
                if (!empty($value->mailbox)) {
                    $cc[] = $value->mailbox."@".$value->host;
                }
            }
        }
        $cc = implode(", ", $cc);

        if (!empty($email->headerInfo->from[0]->mailbox)) {
            $seaddr = $email->headerInfo->from[0]->mailbox."@".$email->headerInfo->from[0]->host;
            $sename = $email->headerInfo->from[0]->personal;
        }
        if (empty($seaddr) && !empty($email->headerInfo->sender[0]->mailbox)) {
            $seaddr = $email->headerInfo->sender[0]->mailbox."@".$email->headerInfo->sender[0]->host;
            $sename = $email->headerInfo->sender[0]->personal;
        }
        if (!empty($email->headerInfo->reply_to[0]->mailbox)) {
            $replyt = $email->headerInfo->reply_to[0]->mailbox."@".$email->headerInfo->reply_to[0]->host;
        } else {
            $replyt = $seaddr;
        }
        $txtmsg = (empty($email->plainText)) ? addslashes(strip_tags($email->htmlText)) : $email->plainText;
        if($this->db->query("INSERT INTO `email_entry` (`id_account`, `htmlmsg`, `textmsg`, `charset`, `sender`,
            `from`, `replyto`, `rawdate`, `date`, `subject`, `to`, `cc`, `attachments`, `datetime_received`, `type`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), 'entrante')",
                [$email->id_cta, $email->htmlText, $txtmsg, $email->charset, $sename, $seaddr, $replyt,
                $email->headerInfo->MailDate, $date, $email->headerInfo->subject, $to, $cc, $email->attstexto])) {
            $idemailentry = $this->db->insert_id();
            $this->db->query("UPDATE `email_data` set `id_email_entry` = ? where `id` = ?", [$idemailentry, $idemaildata]);
            return $idemailentry;
        } else {
            echo date("Y-m-d h:i:s")." Error guardando el correo entrante: " . $this->db->error."\n".$email."\n";
        }
    }

    public function guardarSaliente($data, $emcta) {
        return $this->db->query("INSERT into `email_entry` (`id_account`, `id_user`, `htmlmsg`,
            `textmsg`, `charset`, `sender`, `from`, `to`, `cc`, `cco`, `date`, `subject`, `attachments`, `type`, `status`)
            VALUES (?, ?, ?, ?, 'utf-8', ?, ?, ?, ?, ?, now(), ?, ?, 'Saliente', 1)",
            array($data["id_cuenta"], $this->udata['id'], addslashes($data['body']), strip_tags($data['body']),
            $emcta->email, $emcta->nombre, $data['to'], $data['cc'], $data['cco'], $data['subject'], $data['file'])
        );
    }

    public function cerrarEmail($id_inmsg) {
        return $this->db->query("UPDATE `email_entry` set `datetime_reply`=now(),
            `duration` = TIME_TO_SEC(TIMEDIFF(now(), COALESCE(`datetime_received`, NOW()))),
            `duration_wait` = TIME_TO_SEC(TIMEDIFF(COALESCE(`datetime_startre`, NOW()), COALESCE(`datetime_received`, NOW()))),
            `duration_asa` = TIME_TO_SEC(TIMEDIFF(COALESCE(`datetime_startre`, NOW()), COALESCE(`datetime_reply`, NOW())))
            where `id`='$id_inmsg' and `datetime_reply` is null");
    }

    public function asignar($id_email, $id_cta, $online) {
        $query = $this->db->query("SELECT ud.id_user
            from user_data ud left join catalogs c on c.id = ud.id_catalog
            left join (select id_user, id_account, datetime_asigned from email_entry where id in
            (select max(id) id from email_entry
            where type = 'entrante' group by id_user)) t on t.id_user = ud.id_user
            where c.cat = 'userData' and c.val = 'email' AND ud.val = '$id_cta' and ud.id_user in ($online)
            order by t.datetime_asigned ASC limit 1");
        if ($query->num_rows()>0) {
            $a_quien = $query->row()->id_user;
            return $this->db->query("UPDATE email_entry set datetime_asigned=now(), id_user=? where id=?",
                array($a_quien, $id_email));
        }
        return FALSE;
    }

    public function email_agente() {
        $uid = $this->session->userdata("uid");
        $query = $this->db->query("SELECT * from email_entry where id_user = ? and status = 0 limit 1", array($uid));
        if ($query->num_rows()>0) {
            $respu = $this->desenredarEmail($query->row_array());
            $query = $this->db->query("SELECT * from email_entry where `from` = '$respu[from]'");
            $previos = $query->result_array();
            foreach ($previos as $email) {
                $respu["previos"][] = $this->desenredarEmail($email);
            }
            return $respu;
        }
        return false;
    }

    public function transferirEmail($data) {
        return $this->db->query("UPDATE email_entry set id_user = ?, transfer = ? where id = ?",
            array($data['to_agent'], $this->session->userdata('uid'), $data['id_inmsg']));
    }

    private function desenredarEmail($arrayemail) {
        $arrayemail["textmsg"] = "<pre>".$arrayemail["textmsg"]."</pre>";
        if (empty($arrayemail["htmlmsg"])) {
            $arrayemail["msg"] = $arrayemail["textmsg"];
        } else {
            $date = strtotime($arrayemail['rawdate']);
            $date = date('Y-m-d H:i:s', $date);
            $esteano = substr($date, 0, 4);
            $estemes = substr($date, 5, 2);
            if (!empty($arrayemail["attachments"])) {
                $attachments = explode(",", $arrayemail["attachments"]);
                foreach ($attachments as $key => $val) {
                    $attachments[$key] = site_url("files")."/".$esteano."/".$estemes."/".$val;
                }
                $arrayemail["attachments"] = $attachments;
            }
            $arrayemail["msg"] = stripslashes($arrayemail["htmlmsg"]);
        }

        $arrayemail["replayto"] = (empty($arrayemail["replayto"])) ? $arrayemail["from"] : $arrayemail["replayto"];

        return $arrayemail;
    }

    public function reemail($id) {
        $this->db->query("UPDATE email_entry set datetime_startre=now() where id='".$id."'");
    }

    public function consulta_mails() {
        $respu = "No hay usuarios con cuenta email asignada logueados";
        $query = $this->db->query("SELECT group_concat(distinct(`uid`)) as online from `ses_ab` where uid <> '0'");
        if ($query->num_rows() > 0) {
            $online = $query->row()->online;
            if (!empty($online)) {
                $query = $this->db->query("SELECT * FROM email_account WHERE activa = 1 AND id IN
                    (SELECT DISTINCT(ud.val) FROM user_data ud
                    LEFT JOIN catalogs c ON c.id = ud.id_catalog
                    WHERE c.cat='userData' AND c.val='email' AND ud.id_user IN ($online))");
                if ($query->num_rows()>0) {
                    $respu = "Cuentas email revisadas";
                    $ctas = $query->result();
                    $this->load->library('imapclient');
                    foreach ($ctas as $cta) {
                        $emails = $this->imapclient->revisaCuenta($cta);
                        $count  = count((array)$emails);
                        if($count > 0) {
                            foreach($emails as $email) {
                                $id = $this->guardarEntrante($email);
                                $this->asignar($id, $email->id_cta, $online);
                            }
                        }
                        $respu .= ", " . $cta->email . "(" . $count . ")";
                    }
                }
            }
        }
        return $respu;
    }

    public function email_detalle($data) {
        $maswer = (!empty($data['campanas'])) ? "AND ea.id_campaign in ($data[campanas])" : "";
        $maswer.= (!empty($data['de'])) ? " AND e.from = '$data[de]' " : "";
        if (empty($data['paraexcel'])) {
            $data['prequery'] = "SELECT IFNULL( date_format(e.datetime_received, '$this->dtfor'), '') Recibido,
                COALESCE(CONCAT(u.name,' ',u.last),'') Agente,
                e.sender De, if(LENGTH(e.subject)>20,concat(SUBSTRING(e.subject, 1, 20), ' ...'),e.subject) Asunto,
                COALESCE(date_format(e.datetime_asigned, '$this->dtfor'), '') Asignación,
                COALESCE(SEC_TO_TIME(e.duration_wait), '') Espera,
                COALESCE(date_format(e.datetime_reply, '$this->dtfor'),'') Respuesta,
                COALESCE(SEC_TO_TIME(e.duration), '') Duración,
                e.type AS Tipo
                FROM email_entry e
                LEFT JOIN user u ON u.id = e.id_user
                LEFT JOIN email_account ea ON ea.id = e.id_account
                WHERE DATE(e.date) BETWEEN '$data[min]' AND '$data[max]'
                $maswer AND u.id in ($data[agente])
                ORDER BY e.id DESC";
        } else {
            $data['prequery'] = "SELECT e.date Enviado, e.datetime_received Recibido, concat(u.name,' ',u.last) Agente,
                e.sender De, e.from Desde, e.subject Asunto, e.to Para, e.cc Copia,
                e.datetime_asigned Asignación, e.duration_wait Espera, e.datetime_reply Respuesta, e.duration Duración,
                e.type Tipo, e.textmsg Mensaje, e.attachments Adjuntos
                FROM email_entry e
                LEFT JOIN user u ON u.id = e.id_user
                LEFT JOIN email_account ea ON ea.id = e.id_account
                WHERE DATE(e.date) BETWEEN '$data[min]' AND '$data[max]'
                $maswer AND u.id in ($data[agente])
                ORDER BY e.id DESC";
        }

        return $this->datos_model->manejadorqueries($data);
    }

    public function email_indicadores($data) {
        $data['prequery'] = "SELECT date_format(e.datetime_received, '$this->dfor') Fecha, concat(u.name,' ',u.last) Agente,
            count(e.id) Recibidos, coalesce(count(if(e.datetime_reply is not null, 1, null)),0) Respondidos,
            coalesce(count(if(e.datetime_reply is null, 1, null)),0) 'Sin responder',
            SEC_TO_TIME(COALESCE(ROUND(avg(case when e.datetime_reply is not null then e.duration else null end)),0)) AHT,
            SEC_TO_TIME(COALESCE(ROUND(avg(e.duration_wait)),0)) Espera,
            coalesce(SEC_TO_TIME(max(e.duration_wait)), '00:00:00') 'Mayor espera'
            FROM email_entry e LEFT JOIN user u ON u.id = e.id_user
            WHERE DATE(e.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
            AND u.id in ($data[agente]) GROUP BY e.id_user, date(e.datetime_received)";

        return $this->datos_model->manejadorqueries($data);
    }

    public function listarCorreos($data = []) {
        $campanas = $this->udata['campanas'];
        $pag = (isset($data['pag'])) ? $data['pag'] : 0;
        $rpp = (isset($data['rpp'])) ? $data['rpp'] : REGS_POR_PAG;
        $sender = $data['sender'];
        $where = "WHERE ea.id_campaign in ($campanas) AND ee.type = 'entrante'";
        if (!empty($sender)) {
            $where .= " AND ee.sender = '$sender'";
        }
        $query = $this->db->query("SELECT count(*) AS reg
        FROM email_entry ee
        LEFT JOIN email_account ea ON ea.id = ee.id_account
        $where");
        $data['reg'] = $query->row()->reg;
        $query = $this->db->query("SELECT ee.id, ee.id_account, CONCAT(u.name, ' ', u.last) AS asignado,
            date_format(ee.date, '$this->dfor %H:%i:%s') date, ee.subject, ee.sender, ee.cc, ee.cco,
            date_format(ee.datetime_reply, '$this->dfor %H:%i:%s') AS datetime_reply,
            date_format(ee.datetime_received, '$this->dfor %H:%i:%s') datetime_received
            FROM email_entry ee
            LEFT JOIN user u ON u.id = ee.id_user
            LEFT JOIN email_account ea ON ea.id = ee.id_account
            $where
            ORDER BY ee.date desc limit $pag, $rpp");
        if ($query->num_rows()>0) {
            $data["data"]  = $query->result();
        } else {
            $data['error'] = "No se encontraron datos";
        }

        return $data;
    }

    public function obtenerSenders() {
        $campanas = $this->udata['campanas'];
        $campanas_in = "AND ea.id_campaign in ($campanas)";
        $query = $this->db->query("SELECT ee.sender
            FROM email_entry ee
            LEFT JOIN email_account ea ON ea.id = ee.id_account
            WHERE ee.datetime_reply IS NULL AND ee.type = 'entrante'
            $campanas_in
            GROUP BY ee.sender
            ORDER BY ee.sender");
        if ($query->num_rows()>0) {
            $senders   = $query->result();
        } else {
            $senders = [];
        }

        return $senders;
    }

    public function verCorreo($data = []) {
        $id = (int) $data['id'];
        $query = $this->db->query("SELECT htmlmsg, subject, date_format(date, '$this->dfor %H:%i:%s') date, sender, `from`, cc
            from email_entry
            WHERE id = $id");
        if($query->num_rows()>0)
            $mail   = $query->row();
        else
            $mail = [];

        return $mail;
    }

    public function findEmailEntry($id = 0){
        $id = (int) $id;
        $query = $this->db->query("SELECT *
            FROM email_entry
            WHERE id = $id");
        if ($query->num_rows()>0) {
            $email_entry   = $query->row();
        } else {
            $email_entry = [];
        }

        return $email_entry;
    }

    public function reasignarAgente($id = 0, $id_user = null, $transfer = null){
        $data = [
            'id_user'  => $id_user,
            'transfer' => $transfer,
        ];
        $this->db->where('id', $id);
        $this->db->update('email_entry', $data);

        return $this->db->affected_rows() === 1 ? true : false;
    }

    //Obtiene la lista de remitentes para usar en los massel de reportes
    public function massel_senders() {
        $campanas = $this->udata['campanas'];
        $query = $this->db->query("SELECT DISTINCT(if( ee.type='Saliente', ee.to,ee.from)) sender
            FROM email_entry ee
            LEFT JOIN email_account ea ON ea.id = ee.id_account
            WHERE ea.id_campaign in ($campanas)
            GROUP BY sender
            ORDER BY sender");
        if ($query->num_rows()>0)
            $senders   = $query->result();
        else
            $senders = [];

        return $senders;
    }

    public function asignarFormulario($data){
        $query = $this->db->query("UPDATE email_account set in_tipo=null WHERE in_tipo=?", [ $data['in_tipo'] ] );
        $query = $this->db->query("UPDATE email_account set in_tipo=? WHERE id=?", [ $data['in_tipo'], $data['id'] ] );
        if ($query) return ['status'=>'Ok', 'msg'=>'Formulario asignado'];

        return ['status'=>'error', 'msg'=>'No se transfirio'];
    }

    public function reGuardarEntranteXId($ided) {
        $email  = $this->db->query("SELECT `json` FROM email_data WHERE id=?", [$ided])->row();
        $email  = json_decode($email->json);
        $email->id_cta = 1;
        $date   = date('Y-m-d H:i:s', strtotime($email->headerInfo->MailDate));
        $seaddr = "";
        $sename = "";
        $to = [];
        if (!empty($email->headerInfo->to) && count($email->headerInfo->to) > 0) {
            foreach ($email->headerInfo->to as $key => $value) {
                if (!empty($value->mailbox)) {
                    $to[] = $value->mailbox."@".$value->host;
                }
            }
        }
        $to = implode(", ", $to);

        $cc = [];
        if (!empty($email->headerInfo->cc) && count($email->headerInfo->cc) > 0) {
            foreach ($email->headerInfo->cc as $key => $value) {
                if (!empty($value->mailbox)) {
                    $cc[] = $value->mailbox."@".$value->host;
                }
            }
        }
        $cc = implode(", ", $cc);

        if (!empty($email->headerInfo->from[0]->mailbox)) {
            $seaddr = $email->headerInfo->from[0]->mailbox."@".$email->headerInfo->from[0]->host;
            $sename = $email->headerInfo->from[0]->personal;
        }
        if (empty($seaddr) && !empty($email->headerInfo->sender[0]->mailbox)) {
            $seaddr = $email->headerInfo->sender[0]->mailbox."@".$email->headerInfo->sender[0]->host;
            $sename = $email->headerInfo->sender[0]->personal;
        }
        if (!empty($email->headerInfo->reply_to[0]->mailbox)) {
            $replyt = $email->headerInfo->reply_to[0]->mailbox."@".$email->headerInfo->reply_to[0]->host;
        } else {
            $replyt = $seaddr;
        }
        $txtmsg = json_encode($email->plainText);
        $htmlmsg = json_encode($email->htmlText);
        $htmlmsg = $htmlmsg ?: $txtmsg;
        if($this->db->query("INSERT INTO `email_entry` (`id_account`, `htmlmsg`, `textmsg`, `charset`, `sender`,
            `from`, `replyto`, `rawdate`, `date`, `subject`, `to`, `cc`, `attachments`, `datetime_received`, `type`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), 'entrante')",
                [$email->id_cta, $htmlmsg, $txtmsg, $email->charset, $sename, $seaddr, $replyt,
                $email->headerInfo->MailDate, $date, $email->headerInfo->subject, $to, $cc, $email->attstexto])) {
            return $this->db->insert_id();
        } else {
            die(date("Y-m-d h:i:s")." Error guardando el correo entrante: " . $this->db->error."\n");
        }
    }

}

?>
