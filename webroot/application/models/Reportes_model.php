<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use phpseclib\Net\SFTP;
use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class Reportes_model extends CI_Model
{

    public $posibles_titulos = [
        'campana'    => 'Campaña',
        'id_campana' => 'ID Campaña',
        'grabacion'  => 'Grabación',
        'duracion'   => 'Duración',
        'numero'     => 'Número Teléfono',
        'id_agente'  => 'ID Agente',
        'espera'     => 'Espera en cola',
        'hangup'     => 'Cuelga',
    ];

    public function dashindicadores($campana) {
        $maswere = (empty($campana)) ? "AND camp.id < 0" : "AND camp.id IN ($campana)";
        $query = $this->db->query("SELECT COALESCE(SUM(if(c.status='Terminada', 1, 0)), 0) AS atendidas,
                COALESCE(SUM(if((c.status='Abandonada'), 1, 0)), 0) AS abandonadas,
                COALESCE(SUM(if(c.status='Terminada', 1, 0)), 0)+COALESCE(SUM(if(c.status='Abandonada', 1, 0)), 0) as total
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Entrante' AND DATE(c.datetime_received)=DATE(NOW()) AND c.status<>'En curso' $maswere");
        $data["dona"] = $query->row();
        $query = $this->db->query("SELECT if(MINUTE(c.datetime_received)<30,CONCAT(date_format(c.datetime_received,'%H'),':00'),
                CONCAT(date_format(c.datetime_received,'%H'),':30')) as medias_horas, COUNT(c.datetime_received) as ate
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Entrante' $maswere AND c.status='Terminada' AND DATE(c.datetime_received)=DATE(CURDATE())
            GROUP BY medias_horas ORDER BY medias_horas");
        $ate = $query->result();
        $query = $this->db->query("SELECT if(MINUTE(c.datetime_received)<30,CONCAT(date_format(c.datetime_received,'%H'),':00'),
                CONCAT(date_format(c.datetime_received,'%H'),':30')) as medias_horas, COUNT(c.datetime_received) as aba
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Entrante' $maswere AND c.status='Abandonada' AND DATE(c.datetime_received)=DATE(CURDATE())
            GROUP BY medias_horas ORDER BY medias_horas");
        $aba = $query->result();
        $query = $this->db->query("SELECT COALESCE(SUM(if(c.status='Terminada', 1, 0)), 0) AS atendidas,
                COALESCE(SUM(if(c.status='Abandonada', 1, 0)), 0)+COALESCE(SUM(if(c.status='Abandonada Troncal', 1, 0)), 0) AS abandonadas,
                count(c.id) AS total
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Saliente' $maswere AND DATE(c.datetime_received)=DATE(NOW()) AND c.status<>'En curso'");
        $data["donaout"] = $query->row();
        $query = $this->db->query("SELECT if(MINUTE(c.datetime_received)<30,CONCAT(date_format(c.datetime_received,'%H'),':00'),
                CONCAT(date_format(c.datetime_received,'%H'),':30')) AS medias_horasout,
                COUNT(c.datetime_received) AS ateout
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Saliente' $maswere AND c.status='Terminada' AND DATE(c.datetime_received)=DATE(CURDATE())
            GROUP BY medias_horasout
            ORDER BY medias_horasout");
        $ateout = $query->result();
        $query = $this->db->query("SELECT if(MINUTE(c.datetime_received)<30,CONCAT(date_format(c.datetime_received,'%H'),':00'),
                CONCAT(date_format(c.datetime_received,'%H'),':30')) AS medias_horasout,
                COUNT(c.datetime_received) AS abaout
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Saliente' AND (c.status='Abandonada' or c.status='Abandonada Troncal') AND DATE(c.datetime_received)=DATE(CURDATE()) $maswere
            GROUP BY medias_horasout
            ORDER BY medias_horasout");
        $abaout = $query->result();

        $data["area"] = array();
        foreach($ate as $row) {
            $data["area"][$row->medias_horas]['ate'] = $row->ate;
        }
        foreach($aba as $row) {
            $data["area"][$row->medias_horas]['aba'] = $row->aba;
        }
        ksort($data["area"]);

        $data["areaout"] = array();
        foreach($ateout as $row) {
            $data["areaout"][$row->medias_horasout]['ateout'] = $row->ateout;
        }
        foreach($abaout as $row) {
            $data["areaout"][$row->medias_horasout]['abaout'] = $row->abaout;
        }
        ksort($data["areaout"]);

        return $data;
    }

    public function historico($data) {
        // exit( json_encode($data) );
        $success     = false;
        $hora_inicio = $data["hora_inicio"];
        $hora_fin    = $data["hora_fin"];

        $fecha_i = strtotime(date("Y-m-d ".$hora_inicio));
        $fecha_f = strtotime(date("Y-m-d ".$hora_fin));
        if( $fecha_i < $fecha_f ){

            $maswere  = (empty($data['campana'])) ? "AND id_campaign < 0" : "AND id_campaign IN ($data[campana])";
            $query = $this->db->query("SELECT count(id) ate, c30 FROM rep_atendidas
                WHERE DATE(datetime_received) BETWEEN '$data[min]' AND '$data[max]'
                AND TIME(datetime_received) BETWEEN '$data[hora_inicio]' AND '$data[hora_fin]'
                $maswere
                GROUP BY c30
                ORDER BY c30");
            $ate = $query->result();
            $query = $this->db->query("SELECT count(id) aba, c30 FROM rep_abandono
                WHERE status='Abandonada'
                AND DATE(datetime_received) BETWEEN '$data[min]' AND '$data[max]'
                AND TIME(datetime_received) BETWEEN '$data[hora_inicio]' AND '$data[hora_fin]'
                $maswere
                GROUP BY c30
                ORDER BY c30");
            $aba = $query->result();
            $query = $this->db->query("SELECT (SELECT count(id) FROM rep_atendidas
                WHERE DATE(datetime_received) BETWEEN '$data[min]' AND '$data[max]'
                AND TIME(datetime_received) BETWEEN '$data[hora_inicio]' AND '$data[hora_fin]'
                $maswere) atendidas,
                (SELECT count(id) FROM rep_abandono
                    WHERE status='Abandonada'
                    AND DATE(datetime_received) BETWEEN '$data[min]' AND '$data[max]'
                    AND TIME(datetime_received) BETWEEN '$data[hora_inicio]' AND '$data[hora_fin]'
                    $maswere) abandonadas,
                (SELECT count(id) FROM rep_atendidas
                    WHERE DATE(datetime_received) BETWEEN '$data[min]' AND '$data[max]'
                    AND TIME(datetime_received) BETWEEN '$data[hora_inicio]' AND '$data[hora_fin]'
                    $maswere) +
                (SELECT count(id) FROM rep_abandono
                    WHERE status='Abandonada'
                    AND DATE(datetime_received) BETWEEN '$data[min]' AND '$data[max]'
                    AND TIME(datetime_received) BETWEEN '$data[hora_inicio]' AND '$data[hora_fin]'
                    $maswere) as total");
            $data["dona"] = $query->row();
            $query = $this->db->query("SELECT COALESCE(SUM(if(c.status='Terminada', 1, 0)), 0) AS atendidas,
                COALESCE(SUM(if(c.status like 'Abandonada%', 1, 0)),0) AS abandonadas,
                count(c.id) as total
                from call_entry c
                LEFT JOIN campaign camp ON camp.id = c.id_campaign
                WHERE DATE(c.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
                AND TIME(c.datetime_received) BETWEEN '$data[hora_inicio]' AND '$data[hora_fin]'
                $maswere
                AND c.type='Saliente'
                AND c.status<>'En curso'");
            $data["donaout"] = $query->row();
            $query = $this->db->query("SELECT if(MINUTE(c.datetime_received)<30, CONCAT(date_format(c.datetime_received,'%H'),':00'),
                CONCAT(date_format(c.datetime_received,'%H'),':30')) as medias_horasout, COUNT(c.datetime_received) as ateout
                FROM call_entry c
                LEFT JOIN campaign camp ON camp.id = c.id_campaign
                WHERE c.type='Saliente'
                AND DATE(c.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
                AND TIME(c.datetime_received) BETWEEN '$data[hora_inicio]' AND '$data[hora_fin]'
                $maswere
                AND c.status='Terminada'
                GROUP BY medias_horasout
                ORDER BY medias_horasout");
            $ateout = $query->result();
            $query = $this->db->query("SELECT if(MINUTE(c.datetime_received)<30, CONCAT(date_format(c.datetime_received,'%H'),':00'),
                CONCAT(date_format(c.datetime_received,'%H'),':30')) as medias_horasout, COUNT(c.datetime_received) as abaout
                FROM call_entry c
                LEFT JOIN campaign camp ON camp.id = c.id_campaign
                WHERE date(c.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
                AND TIME(c.datetime_received) BETWEEN '$data[hora_inicio]' AND '$data[hora_fin]'
                $maswere
                AND c.type='Saliente'
                AND (c.status='Abandonada' or c.status='Abandonada Troncal')
                GROUP BY medias_horasout
                ORDER BY medias_horasout");
            $abaout = $query->result();

            $data["area"] = array();
            foreach($ate as $row) {
                $data["area"][$row->c30]['ate'] = $row->ate;
            }
            foreach($aba as $row) {
                $data["area"][$row->c30]['aba'] = $row->aba;
            }
            ksort($data["area"]);

            $data["areaout"] = array();
            foreach($ateout as $row) {
                $data["areaout"][$row->medias_horasout]['ateout'] = $row->ateout;
            }
            foreach($abaout as $row) {
                $data["areaout"][$row->medias_horasout]['abaout'] = $row->abaout;
            }
            ksort($data["areaout"]);
            $data['cuenta'] = 1;

            $success = true;
        }else{
            $data["error"] = "La fecha de inicio debe ser menor a la fecha de termino.";
        }

        $data["success"] = $success;

        return $data;
    }

    public function descansos($data) {
        $maswere  = "AND u.id IN ($data[agente])";
        $query    = $this->db->query("SELECT * from break order by name");
        $breaks   = $query->result();
        $prequery = "SELECT date_format(datetime_init, '$this->dfor') as Fecha, coalesce(ext.val,'') AS Extensión,
            CONCAT(u.name,' ',u.last) as Agente, ";
        foreach ($breaks as $key => $row) {
            $prequery .= "SEC_TO_TIME(COALESCE(SUM(IF(id_break=".$row->id.",duration,0)),0)) as '".$row->description."', ";
        }
        $prequery .= "SEC_TO_TIME(COALESCE(SUM(duration),0)) as Total
            FROM break_entry e
            INNER JOIN user u ON u.id=e.id_user
            LEFT JOIN (select ud.id_user, ud.val FROM catalogs cat
                LEFT JOIN user_data ud ON ud.id_catalog = cat.id where cat.val = 'userask') ext ON ext.id_user = u.id
            WHERE date(datetime_init) BETWEEN '$data[min]' AND '$data[max]' AND e.id_user>1  $maswere
             GROUP BY e.id_user, date(datetime_init) order by date(e.datetime_init) DESC, u.name, u.last";
        $data['prequery'] = $prequery;

        return $this->datos_model->manejadorqueries($data);
    }

    public function abandono30($data) {
        $maswere = (empty($data['campana'])) ? "AND id_campaign < 0" : "AND id_campaign IN ($data[campana])";
        $data['prequery'] = "SELECT c30 Hora, count(id) Llamadas, SEC_TO_TIME(round(avg(queue_wait))) 'Promedio espera',
            SEC_TO_TIME(max(queue_wait)) 'Espera más larga'
            from rep_abandono where status = 'Abandonada'
            AND datetime_received between '$data[min]' and '$data[max]' $maswere GROUP BY c30";
        $data = $this->datos_model->manejadorqueries($data);
        $query = $this->db->query("SELECT 'Total' Hora, count(id) Llamadas, SEC_TO_TIME(round(avg(queue_wait))) 'Promedio espera',
            SEC_TO_TIME(max(queue_wait)) 'Espera más larga'
            from rep_abandono where status = 'Abandonada'
            AND datetime_received between '$data[min]' and '$data[max]' $maswere");
        $data['tot'] = $query->result();

        return $data;
    }

    public function atendidas30($data) {
        $maswere = (empty($data['campana'])) ? "AND id_campaign < 0" : "AND id_campaign IN ($data[campana])";
        $data['prequery'] = "SELECT c30 Hora, count(id) Llamadas, SEC_TO_TIME(round(avg(duration))) 'Promedio atención',
            SEC_TO_TIME(max(duration)) 'Atención más larga' from rep_atendidas
            where datetime_received between '$data[min]' and '$data[max]' $maswere GROUP BY c30";
        $data  = $this->datos_model->manejadorqueries($data);
        $query = $this->db->query("SELECT 'Total' Hora, count(id) Llamadas,
            SEC_TO_TIME(round(avg(duration))) 'Promedio atención',
            SEC_TO_TIME(max(duration)) 'Atención más larga' from rep_atendidas
            where datetime_received between '$data[min]' and '$data[max]' $maswere");
        $data['tot'] = $query->result();

        return $data;
    }

    public function campanasCalidad() {
        $ids = $this->datos_model->getCampanas(false, false);
        $query = $this->db->query("SELECT id, name FROM quality where active=1 and id_campaign in (".$ids.")");
        return $query->result();
    }

    public function traeragentesgra(){
        $query = $this->db->query("SELECT id, CONCAT(name,' ',last) as name FROM user where active=1");
        return $query->result();
    }

    public function callporagentegra($min, $max, $agente) {
        $min = convierte($min, $this->idfor);
        $max = convierte($max, $this->idfor);
        $agent = " AND u.id in ($agente) ";
        $query = $this->db->query("SELECT CONCAT(u.name,' ',u.last) agente,
                COALESCE(SUM(IF((c.status='Terminada' AND c.type='Entrante'),1,0)),0) entrante,
                COALESCE(SUM(IF((c.status='Terminada' AND c.type='Saliente'),1,0)),0) saliente
            FROM call_entry AS c
            LEFT JOIN user AS u ON u.id=c.id_user
            LEFT JOIN queue AS a ON a.id=c.queue
            WHERE DATE(c.datetime_received) BETWEEN '$min' AND '$max' $agent AND c.status='Terminada'
            GROUP BY c.id_user
            ORDER BY u.name,c.datetime_received DESC");

        return $query->result();
    }

    public function tiempo_de_espera($data) {
        $data['prequery'] = "SELECT if(camp.name is null,'', camp.name) Campaña, c.did,
                COALESCE(SUM(IF(c.duration_wait<=10,1,0)),0) as '0-10',
                COALESCE(SUM(IF((c.duration_wait>=11 AND c.duration_wait<=20),1,0)),0) as '11-20',
                COALESCE(SUM(IF((c.duration_wait>=21 AND c.duration_wait<=30),1,0)),0) as '21-30',
                COALESCE(SUM(IF((c.duration_wait>=31 AND c.duration_wait<=40),1,0)),0) as '31-40',
                COALESCE(SUM(IF((c.duration_wait>=41 AND c.duration_wait<=50),1,0)),0) as '41-50',
                COALESCE(SUM(IF((c.duration_wait>=51 AND c.duration_wait<=60),1,0)),0) as '51-60',
                COALESCE(SUM(IF(c.duration_wait>=61,1,0)),0) as '61+',
                COALESCE(COUNT(c.duration_wait),0) as Total,
                SEC_TO_TIME(ROUND(AVG(c.duration_wait))) as Promedio,
                SEC_TO_TIME(MAX(c.duration_wait)) as Máximo
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.id_campaign IN ($data[campana])
            AND DATE(c.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
            AND c.type='Entrante' AND c.status='Terminada' GROUP BY c.did";

        return $this->datos_model->manejadorqueries($data);
    }

    public function exito($data) {
        $maswere  = ($data['tipo']!="0") ? "AND c.type = '".$data['tipo']."'" : "";
        $data['prequery'] = "SELECT camp.name Campaña, c.did,
                COALESCE(SUM(IF(c.status='Terminada',1,0)),0) Exitosas,
                COALESCE(SUM(IF((c.status='Abandonada' or (c.type='Saliente' AND c.status like 'Abandonada %')),1,0)),0) Abandonadas,
                SEC_TO_TIME(ROUND(AVG(c.duration_wait))) as Espera,
                COALESCE(SUM(IF(c.status='Terminada',1,0)),0)+COALESCE(SUM(IF((c.status='Abandonada'),1,0)),0)+
                COALESCE(SUM(IF((c.status='Abandonada Troncal' and c.type='Saliente'),1,0)),0) as Total
            FROM call_entry AS c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE DATE(c.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
            AND c.id_campaign IN ($data[campana]) $maswere GROUP BY c.did";

        return $this->datos_model->manejadorqueries($data);
    }

    public function llamadas_por_agente($data) {
        $maswere  = "AND id_campaign IN ($data[campana])";
        $maswere .= " AND id_user in ($data[agente])";
        $maswere .= (empty($data['tipo'])) ? "" : " AND tipo = '".$data['tipo']."'";
        $data['prequery'] = "SELECT fecha Fecha, agente Agente, tipo Tipo, campana 'Campaña',
            exito Exitosas, abandono Abandonadas, SEC_TO_TIME(duracion) 'Duración', SEC_TO_TIME(promedio) Promedio,
            SEC_TO_TIME(larga) 'Más larga' FROM rep_poragente
            WHERE fecha BETWEEN '$data[min]' AND '$data[max]' $maswere
            ORDER BY fecha DESC, agente, campana, tipo";
        $data  = $this->datos_model->manejadorqueries($data);
        $query = $this->db->query("SELECT '' AS Fecha, '' AS Agente, '' AS Tipo, 'Total' AS 'Campaña',
            sum(exito) Exitosas, sum(abandono) Abandonadas, SEC_TO_TIME(sum(duracion)) 'Duración',
            SEC_TO_TIME(round(avg(promedio))) Promedio,
            SEC_TO_TIME(max(larga)) 'Más larga' FROM rep_poragente
            WHERE fecha BETWEEN '$data[min]' AND '$data[max]' $maswere");
        $data['tot'] = $query->result();

        return $data;
    }

    public function acw($data) {
        $maswere  = "AND id in ($data[agente])";
        $data['prequery'] = "SELECT fecha 'Fecha', agente 'Agente', extension
            'Extensión', veces 'Eventos', SEC_TO_TIME(total) 'Total',
            SEC_TO_TIME(promedio) 'Promedio', SEC_TO_TIME(largo) 'Más largo'
            FROM rep_acw where fecha between '$data[min]' and '$data[max]' $maswere
            ORDER BY agente, fecha";
        $data = $this->datos_model->manejadorqueries($data);
        $query = $this->db->query("SELECT '' AS 'Fecha', '' AS 'Agente', '    Total' AS 'Extensión',
            SUM(veces) 'Eventos', SEC_TO_TIME(SUM(total)) 'Total',
            SEC_TO_TIME(ROUND(AVG(promedio))) 'Promedio', SEC_TO_TIME(MAX(largo)) 'Más largo'
            FROM rep_acw where fecha between '$data[min]' and '$data[max]' $maswere"
        );
        $data['tot'] = $query->result();

        return $data;
    }

    public function compara30($data) {
        $maswere = (empty($data['campana'])) ? "AND id_campaign < 0" : "AND id_campaign IN ($data[campana])";
        $query = $this->db->query("SELECT count(id) ate, c30 FROM rep_atendidas
            WHERE date(datetime_received) BETWEEN '$data[min]' AND '$data[max]' $maswere
            GROUP BY c30 ORDER BY c30");
        $ate = $query->result();
        $query = $this->db->query("SELECT count(id) aba, c30 FROM rep_abandono
            WHERE status='Abandonada' AND date(datetime_received) BETWEEN '$data[min]' AND '$data[max]' $maswere
            GROUP BY c30 ORDER BY c30");
        $aba = $query->result();
        foreach($ate as $row) {
            $data["area"][$row->c30]['ate'] = $row->ate;
        }
        foreach($aba as $row) {
            $data["area"][$row->c30]['aba'] = $row->aba;
        }
        ksort($data["area"]);
        $data['cuenta'] = count($data["area"]);

        return $data;
    }

    public function opciones_ivr($data) {
        // Aldo promete enviar las opciones en 5 dígitos como did
        $data['prequery'] = "SELECT date_format(datetime_received, '$this->dtfor') AS fecha, cid_num AS numero,
            uniqueid AS CallerId, substring(did, 5, 1) option, status FROM call_entry
            WHERE DATE(datetime_received) BETWEEN '$data[min]' AND '$data[max]' AND status<>'En curso'
            AND status<>'Abandonada nosl' AND status<>'Abandonada Troncal' AND LENGTH(did)=5
            AND id_campaign IN ($data[campana]) ORDER BY DATE(datetime_received) DESC";

        return $this->datos_model->manejadorqueries($data);
    }

    public function callporcolasgra($data) {
        if(empty($data['campana'])) { $data['campana'] = '0'; }
        $min = convierte($data['min'], $this->idfor);
        $max = convierte($data['max'], $this->idfor);
        $query = $this->db->query("SELECT did cola,
            COALESCE(SUM(IF(status='Terminada',1,0)),0) exito,
            COALESCE(SUM(IF(status='Abandonada',1,0)),0) abandono
            FROM call_entry
            WHERE date(datetime_received) BETWEEN '$min' AND '$max' AND type='Entrante'
            AND id_campaign IN ($data[campana])
            GROUP BY did ORDER BY did ASC");
        return $query->result();
    }

    public function vm($data) {
        $data['prequery'] = "SELECT vm.datetime_received Fecha, vm.callerid 'Caller ID',
            c.name Campaña, extension Extensión, SEC_TO_TIME(vm.duration) Duración,
            CONCAT('vm_',DATE(vm.datetime_received),'_',vm.grabacion) Grabación
            FROM vm_entry vm LEFT JOIN campaign c ON c.id = vm.id_campaign
            WHERE DATE(vm.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
            AND vm.id_campaign IN ($data[campana]) ORDER BY DATE(vm.datetime_received) DESC";
        $data = $this->datos_model->manejadorqueries($data);
        if ($data['pag'] !== 'x') {
            foreach ($data['data'] as $key => $row) {
                if (!empty($data['data'][$key]->Grabación)) {
                    $data['data'][$key]->Grabación = "<button data-id='aud".$key."' class='btn btn-info dinau'
                    data-target='#escuchaudio' data-toggle='modal'
                    data-src='".$data["data"][$key]->Grabación."'>Audio</button>";
                }
            }
        }

        return $data;
    }

    public function tiempo_de_sesion($data) {
        $maswere = "AND sl.id_user in ($data[agente])";
        $data['prequery'] = "SELECT fecha, concat(u.name,' ',u.last) agente,
            COALESCE(login,'') login, COALESCE(primero,'') primero,
            COALESCE(ultimo,'') ultimo, COALESCE(logout,'') logout,
            if(endescanso>0,sec_to_time(endescanso),'') endescanso,
            if(enllamada>0,sec_to_time(enllamada),'') enllamada,
            if(ensesion>0,sec_to_time(ensesion),'') ensesion,
            if(ocupacion>0,ocupacion,'') ocupacion,
            if(pondescanso>0,pondescanso,'') pondescanso,
            if(disponibilidad>0,disponibilidad,'') disponibilidad
            from rep_sesion sl inner join user u on u.id = sl.id_user
            where fecha between '$data[min]' and '$data[max]' $maswere order by agente, fecha";
        $data = $this->datos_model->manejadorqueries($data);
        $query   = $this->db->query("SELECT
            sec_to_time(SUM(endescanso)) endescanso,
            sec_to_time(SUM(enllamada)) enllamada,
            sec_to_time(sum(ensesion)) ensesion,
            round((SUM(enllamada)/sum(ensesion)*100),1) ocupacion,
            round((SUM(endescanso)/sum(ensesion)*100),1) pondescanso,
            100-round((SUM(enllamada)/sum(ensesion)*100),1)-round((SUM(endescanso)/sum(ensesion)*100),1) disponibilidad
            from rep_sesion sl
            where fecha between '$data[min]' and '$data[max]' $maswere");
        $data["tot"] = $query->result();

        return $data;
    }

    public function outbound($data) {
        $maswere  = " AND camp.id IN ($data[campana])";
        $maswere .= (strpos($data['agente'], ',') == false) ? " AND r.id_agente = '$data[agente]'" : "";
        if($data['calidad']!='0')  {
            if($data['calidad']=='Evaluadas'){
                $maswere .= " AND r.calidad != ''";
            } else {
                $maswere .= " AND r.calidad = '' AND SEC_TO_TIME(r.duracion) >= '00:00:09'";
            }
        }
        if ($data['llamadas']!='0') {
            if ($data['llamadas']=='Terminada') {
                $maswere .= " AND r.estatus='Terminada'";
            } else {
                $maswere .= " AND (r.estatus='Abandonada' or r.estatus='Abandonada Troncal')";
            }
        }
        if (!empty($data['busc'])) {
            $maswere .= " AND (r.numero like '%".$data['busc']."%' || r.linkedid like '%".$data['busc']."%')";
        }

        $data['prequery'] = "SELECT r.id, date_format(r.fecha, '$this->dtfor') AS fecha, r.numero, r.extension, r.agente, r.campana,
            r.did, r.linkedid AS `CallerId`, SEC_TO_TIME(r.duracion) as duracion, r.hangup, r.estatus, r.grabacion, r.calidad, IFNULL(r.calidad_comentario, '') comentario
        FROM rep_outbound r
        LEFT JOIN campaign camp ON camp.id = r.id_campaign
        WHERE date(r.fecha) BETWEEN '$data[min]' AND '$data[max]' $maswere 
        ORDER BY r.fecha DESC";
        $data = $this->datos_model->manejadorqueries($data);
        $calificar = in_array('calidad/guardareval', $this->udata['permiso']) ? true : false;
        if ($data['pag'] !== 'x') {
            foreach ($data["data"] as $key => $row) {
                $boton = "";
                if ($row->calidad != null) {
                    if ($row->calidad <= 69.4) {
                        $clase = "esrojo";
                    } elseif ($row->calidad <= 79.4) {
                        $clase = "esnaranja";
                    } elseif ($row->calidad <= 89.4) {
                        $clase = "esamarillo";
                    } else {
                        $clase = "esverde";
                    }
                    $estrellas = (int)round($row->calidad/20);
                    $vacias = 5 - $estrellas;
                    for ($i = 0; $i < $estrellas; $i++) {
                        $boton .= "<i class='fas fa-star ".$clase."'></i>";
                    }
                    for ($i = 0; $i < $vacias; $i++) {
                        $boton .= "<i class='far fa-star ".$clase."'></i>";
                    }
                    $data["data"][$key]->calidad = $row->calidad . "%";
                } else {
                    if ($row->estatus == "Terminada" && $row->duracion >= "00:00:09" && $calificar) {
                        $boton = "<button type='button' class='btn btn-success lanzamodal' data-src='".
                        $data["data"][$key]->grabacion."' data-toggle='modal' data-target='#evalModal' data-id='".
                        $row->id."' data-cola='".$row->campana."'>Evaluación</button>";
                    }
                }
                $data["data"][$key]->calidad .= " <span title='".$row->comentario."'>$boton</span>";
                $data["data"][$key]->grabacion = "<button id='aud".$key."' data-nameaudio='".$data["data"][$key]->grabacion.
                    "' data-toggle='modal' data-target='#escuchaudio' class='btn btn-info dinau' data-id='".
                    $row->id."' data-src='".$data["data"][$key]->grabacion."'>Audio</button>";
                unset($data["data"][$key]->comentario);
            }
        } else {
            // Agregamos el en encabezado "Comentario de calidad" al final, solo si es el reporte csv
            $data['campos'][] = 'Comentario de calidad';
        }
         //Recorremos los campos para eliminar el de star
        foreach($data['campos'] as $index => $row) {
            if( $row == 'comentario')
                array_splice($data['campos'], $index, 1);
        }

        return $data;
    }

    public function inbound($data) {
        $maswere  = " AND `id_campana` IN ($data[campana])";
        $maswere .= (strpos($data['agente'], ',') == false) ? " AND `id_agente` = '$data[agente]'" : "";
        $maswere .= ($data['llamadas']!='0') ? " AND ri.estatus = '$data[llamadas]'" : "";
        if($data['calidad']!='0')  {
            if($data['calidad']=='Evaluadas'){
                $maswere .= " AND `calidad` <> ''";
            } else {
                $maswere .= " AND `calidad` = '' AND SEC_TO_TIME(`duracion`) >= '00:00:09'";
            }
        }
        if (!empty($data['busc'])) {
            $maswere .= " AND (`numero` like '%".$data['busc']."%' || `linkedid` like '%".$data['busc']."%')";
        }
        $data['prequery'] = "SELECT ri.id, ri.fecha, ri.numero, ri.extension, ri.agente, c.name AS campana,
            ri.did, ri.linkedid Caller_ID, sec_to_time(ri.espera) espera_en_cola,
            sec_to_time(ri.espera_total) espera_total, sec_to_time(ri.duracion) duracion, ri.estatus,
            ce.hangup, ri.grabacion, ri.calidad, IFNULL(ri.calidad_comentario, '') comentario
            from rep_inbound ri
            join call_entry ce on ce.id = ri.id
            LEFT JOIN campaign c ON c.id = ri.id_campana
            WHERE date(ri.fecha) BETWEEN '$data[min]' AND '$data[max]' $maswere
            ORDER BY fecha DESC";
        $data = $this->datos_model->manejadorqueries($data);
        $calificar = in_array('calidad/guardareval', $this->udata['permiso']) ? true : false;
        if ($data['pag'] !== 'x') {
            foreach ($data["data"] as $key => $row) {
                $boton = "";
                if ($row->calidad != '') {
                    if ($row->calidad <= 69.4) {
                        $clase = "esrojo";
                    } elseif ($row->calidad <= 79.4) {
                        $clase = "esnaranja";
                    } elseif ($row->calidad <= 89.4) {
                        $clase = "esamarillo";
                    } else {
                        $clase = "esverde";
                    }
                    $estrellas = (int)round($row->calidad/20);
                    $vacias = 5 - $estrellas;
                    for ($i = 0; $i < $estrellas; $i++) {
                        $boton .= "<i class='fas fa-star ".$clase."'></i>";
                    }
                    for ($i = 0; $i < $vacias; $i++) {
                        $boton .= "<i class='far fa-star ".$clase."'></i>";
                    }
                    $data["data"][$key]->calidad = $row->calidad . "%";
                } else {
                    if ($row->duracion >= "00:00:09" && $calificar) {
                        $boton = "<button type='button' class='btn btn-success lanzamodal' data-src='".
                        $data["data"][$key]->grabacion."' data-toggle='modal' data-target='#evalModal' data-id='".
                        $row->id."' data-cola='".$row->campana."'>Evaluación</button>";
                    }
                }
                $data["data"][$key]->calidad .= " <span title='".$row->comentario."'>$boton</span>";
                $data["data"][$key]->grabacion = "<button id='aud".$key.
                    "' data-toggle='modal' data-target='#escuchaudio' class='btn btn-info dinau' data-id='".
                    $row->id."' data-src='".$data["data"][$key]->grabacion."'>Audio</button>";
            }
        } else {
            // Agregamos el en encabezado "Comentario de calidad" al final, solo si es el reporte csv
            $data['campos'][] = 'Comentario de calidad';
        }
         //Recorremos los campos para eliminar el de comentario
        foreach($data['campos'] as $index => $row) {
            if( $row == 'comentario')
                array_splice($data['campos'], $index, 1);
        }

        return $data;
    }

    public function abandono($data) {
        $maswere = " AND camp.id IN ($data[campana])";
        if ($data['llamadas']!='0')  {
            $maswere .=" AND c.status='".$data['llamadas']."'";
        }
        $data['prequery'] = "SELECT c.id, date_format(c.datetime_received, '$this->dtfor') fecha,
            c.cid_num numero, c.uniqueid linkedid, if(c.did<>'',camp.name,'') campana, c.did did,
            SEC_TO_TIME(if(c.datetime_queued is not null, c.duration_wait, 0)) duration_wait,
            SEC_TO_TIME(c.duration) duration,
            TIMEDIFF(COALESCE(c.datetime_init, c.datetime_end), c.datetime_received) duration_tot,
            c.status estatus, c.grabacion
            FROM call_entry c
            LEFT JOIN user u ON c.id_user=u.id
            left join campaign camp ON camp.id = c.id_campaign
            WHERE date(c.datetime_received) BETWEEN '$data[min]' AND '$data[max]' $maswere
                AND c.type='Entrante' AND (c.status<>'En curso' and c.status<>'Terminada')
            ORDER BY c.datetime_received DESC";
        $data = $this->datos_model->manejadorqueries($data);
        if ($data['pag'] !== 'x') {
            foreach ($data["data"] as $key => $row) {
                $data["data"][$key]->grabacion = "<button id='aud".$key.
                    "' data-toggle='modal' data-target='#escuchaudio' class='btn btn-info dinau' data-id='".
                    $row->id."' data-src='".$data["data"][$key]->grabacion."'>Audio</button>";
            }
        }

        return $data;
    }

    public function abandono_total($data) {
        $maswere = "";
        if ($data['llamadas']!='0')  {
            $maswere .=" AND c.status='".$data['llamadas']."'";
        }
        $data['prequery'] = "SELECT c.id, date_format(c.datetime_received, '$this->dtfor') fecha,
            c.cid_num numero, c.uniqueid linkedid, if(c.did<>'',camp.name,'') campana, c.did did,
            SEC_TO_TIME(if(c.datetime_queued is not null, c.duration_wait, 0)) duration_wait,
            SEC_TO_TIME(c.duration) duration,
            TIMEDIFF(COALESCE(c.datetime_init, c.datetime_end), c.datetime_received) duration_tot,
            c.status estatus, c.grabacion
            FROM call_entry c
            LEFT JOIN user u ON c.id_user=u.id
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE date(c.datetime_received) BETWEEN '$data[min]' AND '$data[max]' $maswere
                AND c.type='Entrante' AND (c.status<>'En curso' and c.status<>'Terminada')
            ORDER BY c.datetime_received DESC";
        $data = $this->datos_model->manejadorqueries($data);
        if ($data['pag'] !== 'x') {
            foreach ($data["data"] as $key => $row) {
                $data["data"][$key]->grabacion = "<button id='aud".$key.
                    "' data-toggle='modal' data-target='#escuchaudio' class='btn btn-info dinau' data-id='".
                    $row->id."' data-src='".$data["data"][$key]->grabacion."'>Audio</button>";
            }
        }

        return $data;
    }

    public function log_de_usuarios($data) {
        $agente = (empty($data['agente']))  ? "AND ul.user_id < 0" : "";
        $data['prequery'] = "SELECT u.id Id, concat(u.name,' ',u.last) Agente,
            date_format(ul.evento, '$this->dtfor') Fecha, ul.detalle Evento
            from user_log ul inner join user u on u.id=ul.user_id
            where ul.user_id > 1 AND date(ul.evento) between '$data[min]' and '$data[max]'
            AND ul.user_id in ($data[agente]) order by ul.user_id, ul.evento";

        return $this->datos_model->manejadorqueries($data);
    }

    public function inOutBoundForm($idsForm, $fecha = '', $subir = true) {
        include_once (APPPATH . 'third_party/vendor/phpseclib/Net/SFTP.php');
        $ret = "<br>";
        foreach ($idsForm as $key => $idForm) {
            $data['pag'] = 'x';
            $hoy         = $fecha == '' ? date("Y-m-d") : $fecha;
            $query       = $this->db->query("SELECT GROUP_CONCAT(CONCAT('ff.', slug)) AS campos FROM form_fields WHERE id_form = $idForm");

            if( $query->num_rows() > 0 ){
                $campos      = $query->row()->campos;

                $data['prequery'] = "SELECT if(c.type='Saliente','Outbound', 'Inbound') tipo, c.id,
                    date_format(c.datetime_received, '%d-%m-%Y %H:%i:%s') AS fecha, c.cid_num AS numero,
                    coalesce(ext.val,'') AS extension, coalesce(CONCAT(u.name,' ',u.last),'') AS agente,
                    if(c.did<>'',camp.name,'') campana, c.did AS did, c.uniqueid as CallerId,
                    SEC_TO_TIME(c.duration_wait) espera,
                    SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(COALESCE(c.datetime_init, c.datetime_end), c.datetime_received))) espera_total,
                    SEC_TO_TIME(c.duration) as duracion,
                    if(c.status='Abandonada Troncal','Abandonada', c.status) estatus,
                    c.hangup, c.grabacion, coalesce(qv.total, '') calidad,
                    $campos
                    FROM call_entry c
                    LEFT JOIN user u ON c.id_user=u.id
                    LEFT JOIN form f ON f.id = $idForm
                    LEFT JOIN campaign camp ON camp.id = f.id_campaign
                    LEFT JOIN (
                        SELECT qv.id_call_entry, sum(qf.weight) total FROM quality_values qv
                        INNER JOIN quality_fields qf ON qf.id = qv.id_quality_fields
                        GROUP BY qv.id_call_entry) qv ON qv.id_call_entry = c.id
                    LEFT JOIN (select ud.id_user, ud.val FROM catalogs cat
                        LEFT JOIN user_data ud ON ud.id_catalog = cat.id where cat.val = 'userask') ext ON ext.id_user = c.id_user
                    INNER JOIN formd_$idForm ff ON ff.linkedid = c.uniqueid
                    WHERE date(c.datetime_received) = '$hoy'
                        AND c.status<>'En curso'
                    ORDER BY camp.name, c.datetime_received DESC";

                $data = $this->datos_model->manejadorqueries($data);

                if (count($data['data'])>0) {
                    $data['tits'] = array_map(function($item){
                        return beautify($item, $this->posibles_titulos);
                    }, $data['campos']);
                    $nombreArchivo = 'assertive_' . str_replace("-","",$hoy) .date('_His') . '.csv';
                    // $nombreArchivo = 'assertive_' . date('Ymd_His') . '.csv';
                    $archivoLocal = APPPATH . $nombreArchivo;
                    $archivoRemoto = '/' . $nombreArchivo;

                    $style = (new StyleBuilder())
                    ->setFontBold()
                    ->setShouldWrapText()
                    ->build();
                    $writer = WriterFactory::create(Type::CSV);
                    $writer->openToFile($archivoLocal);
                    $writer->addRowWithStyle($data["tits"], $style);
                    $writer->addRows($data['data']);
                    $writer->close();

                    // Configurar la conexión SFTP
                    $config['hostname'] = '2c81732.online-server.cloud';
                    $config['username'] = 'PhonexSFTP';
                    $config['password'] = 'BbN0M93As%Z1';
                    $config['port']     = 22;

                    // Crear una instancia de SFTP
                    $sftp = new SFTP($config['hostname'], $config['port']);

                    if (!$sftp->login($config['username'], $config['password'])) {
                        unlink($archivoLocal);
                        show_error('No se pudo conectar al servidor SFTP');
                    }else{
                        if( !$subir ) $ret .= "Si se pudo conectar al servidor SFTP. ($idForm) <br>";
                    }

                    if( $subir ){
                        // Subir el archivo
                        if ($sftp->put($archivoRemoto, $archivoLocal, SFTP::SOURCE_LOCAL_FILE)) {
                            $ret .= "Archivo subido ".$nombreArchivo."<br>";
                            $RutaAudio = "/audios/";
                            if (!$sftp->is_dir($RutaAudio)) {
                                // Crear el directorio remoto
                                $sftp->mkdir($RutaAudio, -1, true);
                            }
                            foreach ($data['data'] as $key => $obj) {
                                $obj = (object)$obj;
                                $ExtraeFecha = explode("-",$obj->grabacion)[3];
                                $ExtraeFechaRuta = substr($ExtraeFecha,0,4)."/".substr($ExtraeFecha,4,2)."/".substr($ExtraeFecha,6,2)."/";
                                $grabacion = "/var/spool/asterisk/monitor/".$ExtraeFechaRuta.$obj->grabacion;
                                if( is_file($grabacion) ){
                                    if ($sftp->put($RutaAudio . $obj->grabacion, $grabacion, SFTP::SOURCE_LOCAL_FILE)) {
                                        $ret .= "Audio subido ".$obj->grabacion . "<br>";
                                    }else{
                                        $ret .= "Audio NO subido ".$obj->grabacion . "<br>";
                                    }
                                }
                            }
                        }else{
                            $ret .= "Archivo NO subido ".$nombreArchivo."<br>";
                        }
                    }
                    unlink($archivoLocal);
                } else $ret .= "Sin resultados para el formulario $idForm para el $hoy <br>";
            } else $ret .= "Formulario no encontrado. ($idForm) <br>";
        }

        return $ret;
    }

}

?>
