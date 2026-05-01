<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_model extends CI_Model
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

    public function plantilla_crear($data) {
        $id_campana = $data["id_campaign"];
        $valor = $data["valor"];
        $name = $data["name"];
        if ($this->db->query("INSERT INTO sms_template (id_campaign, `name`, valor) VALUES (?,?,?)", [$id_campana, $name, $valor] )) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function plantilla_actualizar($data) {
        if( empty($data["name"]) ){// VIENE DE LA CONSOLA
            if ($this->db->query("UPDATE sms_template SET valor=? WHERE id=?", [$data['valor'], $data['id']] )) {
                return true;
            }
        }else{// VIENE DEL MENU SMS/PLANTILLA
            if ($this->db->query("UPDATE sms_template SET `name`=?, valor=?, id_campaign=? WHERE id=?", [$data['name'], $data['valor'], $data['id_campaign'], $data['id']] )) {
                return true;
            }
        }
        return false;
    }

    public function plantilla_borrar($data) {
        if ($this->db->query("DELETE from sms_template where id=?", array($data))) {
            return true;
        }
        return false;
    }

    public function finalizar($p) {
        $sql = "INSERT INTO sms_entry (id_user, phone, msg, datetime_init, resp, UID, status, status_desc, type)
        VALUES(?,?,?, NOW(), ?,?,?,?,'Saliente');";
        $this->db->query($sql, [ $p->id_user, $p->phone, $p->msg, $p->resp, $p->uid, $p->status, $p->status_desc ]);

        return $this->db->insert_id();
    }

    // Actualiza el estatus de mensajes enviados por "Directo" específicamente
    public function actualiza($tabla = 'sms_entry') {
        $query = $this->db->query("SELECT *, date(datetime_init) fecha from $tabla
            where uid<>'' and json='' and datetime_init < now() - interval 2 minute"); // limit !?
        $filas = $query->num_rows();
        if ($filas == 0) {
            return " $tabla Actualizados: ".$filas;
        }
        $sms = $this->datos_model->getParams('sms');
        foreach ($query->result() as $row) {
            $dir = 'https://smsrp.directo.com/rest/edr?message_id='.$row->uid.
                '&start_date='.$row->fecha.'_00:00:00'.
                '&end_date='.$row->fecha.'_23:59:59';
            $headers = array(
                'Authorization: Bearer '.$sms->token,
                'Content-Type: application/x-www-form-urlencoded'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $dir);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $data  = curl_exec($ch);
            $ddata = json_decode($data);
            $ddata = $ddata[0];
            if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
                $ddata->edr_status = "Error";
            }
            curl_close($ch);
            $stdes = ($ddata->edr_status == "DELIVRD") ? "Entregado" : ($ddata->edr_status != "SENT" ? "Error" : "Enviado");
            $this->db->query("UPDATE $tabla set `json`='" . $data .
                "', `status_desc`='" . $stdes .
                "'where id='" . $row->id . "'");
        }

        return " $tabla Actualizados: ".$filas;
    }

    public function sms_detalle($data) {
        $data['prequery'] = "SELECT CONCAT(u.name,' ',u.last) nombre, s.phone,
            concat(substring(s.msg, 1, 40), ' ... ') msg,
            date_format(s.datetime_init, '$this->dtfor') datetime_init, s.resp
            FROM sms_entry s INNER JOIN user u on u.id = s.id_user
            WHERE DATE(s.datetime_init) BETWEEN '$data[min]' AND '$data[max]'
            AND s.id_user IN ($data[agente])";

        $data = $this->datos_model->manejadorqueries($data);
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]->resp = (json_decode($value->resp)->resp=="200") ? "Enviado" : "No enviado";
        }
        return $data;
    }

    public function sms_indicadores($data) {
        $data['prequery'] = "SELECT date_format(se.datetime_init, '$this->dfor') fecha, CONCAT(u.name, ' ',u.last) nombre,
            coalesce(sum(if(se.type='Saliente',1 ,0)),0) enviados,
            coalesce(sum(if(se.type='Entrante',1,0)),0) recibidos,
            coalesce(sum(if(se.type='Saliente' and se.status='200',1 ,0)),0) env_exito
            FROM sms_entry se LEFT JOIN user u on u.id = se.id_user
            WHERE DATE(se.datetime_init) BETWEEN '$data[min]' AND '$data[max]'
            AND se.id_user in ($data[agente]) GROUP BY fecha, nombre";

        return $this->datos_model->manejadorqueries($data);
    }

    public function get_camps($data) {
        $prequery = "SELECT `camp`, count(`id`) regs,
            coalesce(sum(if(length(resp)>0, 1, 0)), 0) sent,
            coalesce(sum(if(length(resp)=0, 1, 0)), 0) tose,
            coalesce(sum(if(status_desc='Mensaje Enviado', 1, 0)), 0) menExi,
            coalesce(sum(if(status_desc='Envio Exitoso', 1, 0)), 0) senExi,
            coalesce(sum(if(status_desc='Destino invalido', 1, 0)), 0) desInv,
            coalesce(sum(if(status_desc='Error', 1, 0)), 0) otroError,
            coalesce(sum(if(status_desc='Error Interno', 1, 0)), 0) errorInt
            from sms_campaign
            group by camp";
        $query = $this->db->query($prequery);
        $data['cuenta'] = $query->num_rows();
        $query = $this->db->query($prequery. " limit ".$data['page'].", ".REGS_POR_PAG);
        $data['data'] = $query->result();

        return $data;
    }

    public function cargar_csv($csv, $form) {
        $this->csvreader->auto($csv);
        $colus = ["telefono", "nombre", "dato", "saludo", "mensaje", "cierre"];
        $buenas = 0;
        $colsencsv = "";
        foreach ($this->csvreader->titles as $key => $row) {
            $colsencsv .= " ".$row;
            if (in_array($row, $colus)) {
                $buenas++;
            } else {
                $buenas--;
            }
        }
        if ($buenas == 6) {
            // Ni más ni menos columnas que las requeridas
            $regs = $totregs = 0;
            $query = "INSERT into sms_campaign (`camp`, `phone`, `msg`) values ";
            foreach ($this->csvreader->data as $key => $row) {
                if (!empty($row['telefono']) && strlen($row['telefono']) == 10) {
                    $fila = array_values($row);
                    $msg = (empty($row['saludo'])) ? $form['saludo'] : $row['saludo'];
                    $msg .= (!empty($msg)) ? ' ' : '';
                    $msg .= (!empty($row['nombre'])) ? $row['nombre'] : '';
                    $msg .= (!empty($msg)) ? ' ' : '';
                    $msg .= (empty($row['mensaje'])) ? $form['msg'] : $row['mensaje'];
                    $msg .= (!empty($row['dato'])) ? ' '.$row['dato'] : '';
                    $msg .= (empty($row['cerre'])) ? (empty($form['cierre'])) ? '' : ' '.$form['cierre'] : ' '.$row['cierre'];
                    $query .= "('".$form['camp']."', '".$row['telefono']."', '".$msg."'),";
                    $regs++;
                    $totregs++;
                    if ($regs==500) {
                        $query = rtrim($query, ",");
                        $this->db->query($query);
                        $regs = 0;
                        $query = "INSERT into sms_campaign (`camp`, `phone`, `msg`) values ";
                    }
                }
            }
            $query = rtrim($query, ",");
            $this->db->query($query);
            if ($totregs>0) {
                $respu["status"] = "ok";
            } else {
                $respu["status"] = "error";
                $respu["msg"] = "Ningún registro valido agregado. No se crea campaña.";
            }
        } else {
            $respu["status"] = "error";
            $respu["msg"] = "Estructura incorrecta de archivo, validar 6 columnas.<br> en el archivo: ".$colsencsv;
        }

        return $respu;
    }

    public function camp_enviar($data) {
        $query = $this->db->query("SELECT * from sms_campaign where camp = ? AND resp = ''", array($data['camp']));
        if ($query->num_rows()>0){
            $sms = $this->datos_model->getParams('sms');
            foreach ($query->result() as $row) {
                $data = [
                    "to"        => (strlen($row->phone) == 10) ? '52'.$row->phone : $row->phone,
                    "message"   => $row->msg,
                    "from"      => 'sms',
                ];
                $res = doReq(["url"=>$sms->server, "method"=>'POST', "body"=>$data, 'proto'=>'x-www-form-urlencoded', "heads"=>'Authentication: Bearer '.$sms->token]);
                if (empty($res["code"]) || $res["code"] != 200) {
                    return array("status"=>"error", "msg"=>$res["data"]);
                }
                $json = json_decode($res["data"]);
                $this->db->query("UPDATE sms_campaign
                    SET datetime_init=now(), resp=?, uid=?, status=?, status_desc=?
                    WHERE id='$row->id'", array( json_encode($json), $json->message_id, '', '')
                );
            }
            return array("status"=>"ok");
        }
        return array("status"=>"error", "msg"=>"No hay registros por enviar en la campaña.");
    }

    public function buscarPlantilla($data) {
        if( empty($data["id_campaign"]) ){
            $query = $this->db->query('SELECT * FROM sms_template WHERE id = ?',[ $data['id'] ]);
            return $query->row();
        }else{
            $query = $this->db->query('SELECT * FROM sms_template WHERE id_campaign = ?',[ $data['id_campaign'] ]);
            return $query->result();
        }
    }

}

?>
