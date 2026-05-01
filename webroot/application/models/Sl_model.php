<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sl_model extends CI_Model
{

    public function slin($data) {
        $data['min'] = convierte($data['min'], $this->idfor);
        $data['max'] = convierte($data['max'], $this->idfor);
        $bet = "AND date(c.datetime_received) BETWEEN '".$data['min']."' AND '".$data['max']."'";
        $camps = explode(',', $data['campana']);
        $filtrote = "and (scamword";
        foreach ($camps as $id_camp) {
            $query = $this->db->query("SELECT * from campaign_hour where id_campaign =?", array($id_camp));
            $dayshours = $query->result();
            if(count($dayshours) < 1) {
                for($d=1;$d<8;$d++){
                    $h1 = "'09:00:00'";
                    $h2 = "'23:59:59'";
                    if($d==1 or $d==7){
                        $h1 = "NULL";
                        $h2 = "NULL";
                    }
                    $hquery = $this->db->query("INSERT INTO campaign_hour(id, id_campaign, dia, inicio, fin) VALUES(0,'$id_camp','$d',$h1,$h2);");
                }
                $query = $this->db->query("SELECT * from campaign_hour where id_campaign =?", array($id_camp));
                $dayshours = $query->result();
            }
            if($data['tipo']==1) { // Dentro del horario de servicio
                foreach ($dayshours as $key => $day) {
                    if ($day->inicio != null && $day->fin != null) {
                        $filtrote .= " or (camp.id = '$id_camp' AND time(c.datetime_received) BETWEEN '$day->inicio' AND '$day->fin' AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                    }
                }
            } elseif($data['tipo']==2) { // Fuera del horario de servicio
                foreach ($dayshours as $key => $day) {
                    if ($day->inicio != null && $day->fin != null) {
                        $filtrote .= " or (camp.id = '$id_camp' AND time(c.datetime_received) NOT BETWEEN '$day->inicio' AND '$day->fin' AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                    } else {
                        $filtrote .= " or (camp.id = '$id_camp' AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                    }
                }
            } else {
                $filtrote .= " or camp.id = '$id_camp'\n";
            }
        }
        $filtrote .= ")\n";
        $filtrote = str_replace("scamword or ", "", $filtrote);
        $agente = (empty($data['agente'])) ? "" : "AND c.id_user = '".$data['agente']."'";
        $sl = $this->datos_model->getParams('sl');

        $tsf = "COALESCE(ROUND(avg(case when c.status='Terminada' and c.duration_wait<=$sl->segundos then 100
            when c.status='Terminada' and c.duration_wait>$sl->segundos then 0 else null end)),100) as tsf,";
        $aba = "COALESCE(ROUND(avg(case when c.status='Abandonada' then 100
            when c.status='Terminada' then 0 else null end)),0) as aba,";
        $asa = "SEC_TO_TIME(COALESCE(ROUND(avg(case when c.status='Terminada' then c.duration_wait else null end)),0)) as asa,";
        $tat = "SEC_TO_TIME(COALESCE(ROUND(avg(case when c.status='Terminada' then c.duration else null end)),0)) as tat,";
        $tot = "COALESCE(SUM(IF(c.status='Abandonada' or c.status='Terminada',1,0)),0) AS llamadas,";
        $ate    = "COALESCE(SUM(IF(c.status='Terminada', 1,0)),0) AS atendidas,";
        $totaba = "COALESCE(SUM(IF(c.status='Abandonada',1,0)),0) AS abandonadas,";

        $preq = "SELECT IF(MINUTE(c.datetime_received)<30,CONCAT(date_format(c.datetime_received,'%H'),':00'),
            CONCAT(date_format(c.datetime_received,'%H'),':30')) AS hora,
            $tsf $aba $asa $tat $tot $ate $totaba
            SEC_TO_TIME(COALESCE(MAX(c.duration),0)) AS larga,
            COALESCE(COUNT(DISTINCT c.id_user),1) AS opers,
            $sl->segundos seg
            FROM call_entry c
            LEFT JOIN user AS u ON u.id=c.id_user
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Entrante' $bet $filtrote $agente GROUP BY hora";
        $query = $this->db->query($preq);
        $data['data'] = $query->result();
        $query = $this->db->query("SELECT $tsf $aba $asa $tat $tot $ate $totaba
            SEC_TO_TIME(COALESCE(MAX(c.duration),0)) AS larga,
            COALESCE(COUNT(DISTINCT c.id_user),1) AS opers
            FROM call_entry c
            LEFT JOIN user AS u ON u.id=c.id_user
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Entrante' $bet $filtrote $agente");
        $data['tot'] = $query->result();

        return $data;
    }

    public function slout($data) {
        $data['min'] = convierte($data['min'], $this->idfor);
        $data['max'] = convierte($data['max'], $this->idfor);
        $bet = "AND date(c.datetime_received) BETWEEN '".$data['min']."' AND '".$data['max']."'";

        $camps = explode(',', $data['campana']);
        $filtrote = "and (scamword";

        foreach ($camps as $id_camp) {
            $query = $this->db->query("SELECT * from campaign_hour where id_campaign =?", array($id_camp));
            $dayshours = $query->result();
            if($data['tipo']==1) { // Dentro del horario de servicio
                foreach ($dayshours as $key => $day) {
                    if ($day->inicio != null && $day->fin != null) {
                        $filtrote .= " or (camp.id IN ($id_camp) AND time(c.datetime_received) BETWEEN '$day->inicio' AND '$day->fin' AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                    }
                }
            } elseif($data['tipo']==2) { // Fuera del horario de servicio
                foreach ($dayshours as $key => $day) {
                    if ($day->inicio != null && $day->fin != null) {
                        $filtrote .= " or (camp.id IN ($id_camp) AND time(c.datetime_received) NOT BETWEEN '$day->inicio' AND '$day->fin' AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                    } else {
                        $filtrote .= " or (camp.id IN ($id_camp) AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                    }
                }
            } else {
                $filtrote .= " or camp.id IN ($id_camp)\n";
            }
        }
        $filtrote .= ")\n";
        $filtrote = str_replace("scamword or ", "", $filtrote);
        $prequery = "SELECT IF(MINUTE(c.datetime_received)<30,CONCAT(date_format(c.datetime_received,'%H'),':00'),
                CONCAT(date_format(c.datetime_received,'%H'),':30')) AS hora,
                count(c.id) AS llamadas,
                COALESCE(sum(IF(c.status='Terminada',1,0)),0) exito,
                COALESCE(sum(IF(c.status like 'Abandonada%',1,0)),0) abandono,
                SEC_TO_TIME(sum(c.duration)) AS ocupacion,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when c.status='Terminada' then c.duration else null end)),0)) as aht,
                SEC_TO_TIME(COALESCE(MAX(c.duration),0)) AS larga,
            COALESCE(COUNT(DISTINCT c.id_user),1) AS opers
            FROM call_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Saliente' $bet $filtrote";
        $query = $this->db->query($prequery . " GROUP BY hora");
        $data['data'] = $query->result();
        $query = $this->db->query($prequery);
        $data['tot'] = $query->result();

        return $data;
    }

    public function slingraf($data) {
        $data['min'] = convierte($data['min'], $this->idfor);
        $data['max'] = convierte($data['max'], $this->idfor);
        $bet = "AND date(c.datetime_received) BETWEEN '".$data['min']."' AND '".$data['max']."'";

        $camps = explode(',', $data['campana']);
        $filtrote = "and (scamword";

        foreach ($camps as $id_camp) {
            $query = $this->db->query("SELECT * from campaign_hour where id_campaign =?", array($id_camp));
            $dayshours = $query->result();
            if($data['tipo']==1) { // Dentro del horario de servicio
                foreach ($dayshours as $key => $day) {
                    if ($day->inicio != null && $day->fin != null) {
                        $filtrote .= " or (camp.id = '$id_camp' AND time(c.datetime_received) BETWEEN '$day->inicio' AND '$day->fin' AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                    }
                }
            } elseif($data['tipo']==2) { // Fuera del horario de servicio
                foreach ($dayshours as $key => $day) {
                    if ($day->inicio != null && $day->fin != null) {
                        $filtrote .= " or (camp.id = '$id_camp' AND time(c.datetime_received) NOT BETWEEN '$day->inicio' AND '$day->fin' AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                    } else {
                        $filtrote .= " or (camp.id = '$id_camp' AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                    }
                }
            } else {
                $filtrote .= " or camp.id = '$id_camp'\n";
            }
        }
        $filtrote .= ")\n";
        $filtrote = str_replace("scamword or ", "", $filtrote);
        $sl = $this->datos_model->getParams('sl');

        $tsf    = "COALESCE(ROUND(avg(case when c.status='Terminada' and c.duration_wait<=$sl->segundos then 100
            when c.status='Terminada' and c.duration_wait>$sl->segundos then 0 else null end)),100) as tsf,";
        $aba    = "COALESCE(ROUND(avg(case when c.status='Abandonada' then 100
            when c.status='Terminada' then 0 else null end)),0) as aba,";
        $asa    = "SEC_TO_TIME(COALESCE(ROUND(avg(case when c.status='Terminada' then c.duration_wait else null end)),0)) as asa,";
        $tat    = "SEC_TO_TIME(COALESCE(ROUND(avg(case when c.status='Terminada' then c.duration else null end)),0)) as tat,";
        $tot    = "COALESCE(SUM(IF(c.status='Abandonada' or c.status='Terminada',1,0)),0) AS llamadas,";
        $ate    = "COALESCE(SUM(IF(c.status='Terminada',1,0)),0) AS atendidas,";
        $totaba = "COALESCE(SUM(IF(c.status='Abandonada',1,0)),0) AS abandonadas,";

        $query = $this->db->query("SELECT IF(MINUTE(c.datetime_received)<30,CONCAT(date_format(c.datetime_received,'%H'),':00'),
            CONCAT(date_format(c.datetime_received,'%H'),':30')) AS hora,
            $tsf $aba $asa $tat $tot $ate $totaba
            SEC_TO_TIME(COALESCE(MAX(c.duration),0)) AS larga,
            COALESCE(COUNT(DISTINCT c.id_user),1) AS opers
            FROM call_entry c
            LEFT JOIN user AS u ON u.id=c.id_user
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Entrante' $bet $filtrote  GROUP BY hora");
        $data['data'] = $query->result();
        $query = $this->db->query("SELECT $tsf $aba $asa $tat $tot $ate $totaba
            SEC_TO_TIME(COALESCE(MAX(c.duration),0)) AS larga,
            COALESCE(COUNT(DISTINCT c.id_user),1) AS opers,
            $sl->segundos seg
            FROM call_entry c
            LEFT JOIN user AS u ON u.id=c.id_user
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE c.type='Entrante' $bet $filtrote ");
        $data['tot'] = $query->result();

        return $data;
    }

    public function acumensual($data) {
        $min = convierte($data['min'], $this->idfor);
        $max = convierte($data['max'], $this->idfor);
        $campana = $data["campana"];
        $sl = $this->datos_model->getParams('sl');
        $query = $this->db->query("SELECT t1.*,
            SEC_TO_TIME(ROUND(coalesce(t2.promacw,0)/if((t1.llamadas+t1.llamsalida) <> 0, t1.llamadas+t1.llamsalida, 1))) promacw
            from (SELECT date_format(c.datetime_received, '%m-%Y') fecha,
                COALESCE(cm.name, '') campana,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Terminada')
                then c.duration_wait else null end)),0)) promresp,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Abandonada')
                then c.duration_wait else null end)),0)) promaban,
                COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) llamadas,
                COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada' and c.id_user is not null,1,0)),0) ateporage,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Entrante')
                then c.duration else null end)),0)) promllam,
                COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0) llamaban,
                COALESCE(SUM(IF(c.type='Saliente',1,0)),0) llamsalida,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Saliente')
                then c.duration else null end)),0)) promllamsal,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when c.type='Entrante'
                then c.duration else null end)),0)) promllament,
                IF(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0)>0, ROUND(COALESCE(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) /
                    (COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                    COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0))*100,0), 0), '- ') porcontes,
                COALESCE(SUM(if(c.type='Entrante' and (c.status='Abandonada' and c.duration_wait <3), 1, 0)),0) llamaba3seg,
                COALESCE(ROUND(avg(case when c.status='Terminada' and c.duration_wait<=$sl->segundos then 100
                    when c.status='Terminada' and c.duration_wait>$sl->segundos then 0 else null end)),100) nivserv,
                    $sl->segundos seg
            FROM call_entry c
            left join campaign cm on cm.id = c.id_campaign
            WHERE YEAR(c.datetime_received) BETWEEN YEAR('$min') AND YEAR('$max') AND c.id_campaign IN ($campana)
                AND MONTH(c.datetime_received) BETWEEN MONTH('$min') AND MONTH('$max')
                AND c.id_campaign in ($data[campana])
            group by MONTH(c.datetime_received), c.id_campaign) t1
            left join (SELECT DATE(datetime_init) dia, sum(duration) promacw
            FROM break_entry LEFT JOIN break ON break.id = break_entry.id_break
            WHERE break.name = 'acw'
                AND YEAR(datetime_init) BETWEEN YEAR('$min') AND YEAR('$max')
                AND MONTH(datetime_init) BETWEEN MONTH('$min') AND MONTH('$max')
            GROUP BY MONTH(datetime_init)) t2
            on t2.dia = t1.fecha
            ORDER BY t1.fecha DESC");
        $data['data'] = $query->result();
        $query = $this->db->query("SELECT t1.*,
            SEC_TO_TIME(ROUND(coalesce(t2.promacw,0)/if((t1.llamadas+t1.llamsalida) <> 0, t1.llamadas+t1.llamsalida, 1))) promacw
            from (SELECT date_format(c.datetime_received, '$this->dfor') fecha,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Terminada')
                then c.duration_wait else null end)),0)) promresp,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Abandonada')
                then c.duration_wait else null end)),0)) promaban,
                COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) llamadas,
                COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada' and c.id_user is not null,1,0)),0) ateporage,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Entrante')
                then c.duration else null end)),0)) promllam,
                COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0) llamaban,
                COALESCE(SUM(IF(c.type='Saliente',1,0)),0) llamsalida,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Saliente')
                then c.duration else null end)),0)) promllamsal,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when c.type='Entrante'
                then c.duration else null end)),0)) promllament,
                IF(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0)>0, ROUND(COALESCE(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) /
                    (COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                    COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0))*100,0), 0), '- ') porcontes,
                COALESCE(SUM(if(c.type='Entrante' and (c.status='Abandonada' and c.duration_wait <3), 1, 0)),0) llamaba3seg,
                COALESCE(ROUND(avg(case when c.status='Terminada' and c.duration_wait<=$sl->segundos then 100
                    when c.status='Terminada' and c.duration_wait>$sl->segundos then 0 else null end)),100) nivserv
            FROM call_entry c
            WHERE YEAR(c.datetime_received) BETWEEN YEAR('$min') AND YEAR('$max')
            AND c.id_campaign IN ($campana)
                AND MONTH(c.datetime_received) BETWEEN MONTH('$min') AND MONTH('$max')
                AND c.id_campaign in ($data[campana])) t1,
            (SELECT DATE(datetime_init) dia, sum(duration) promacw
            FROM break_entry LEFT JOIN break ON break.id = break_entry.id_break
            WHERE break.name = 'acw'
            AND YEAR(datetime_init) BETWEEN YEAR('$min') AND YEAR('$max')
            AND MONTH(datetime_init) BETWEEN MONTH('$min') AND MONTH('$max')) t2");
        $data['tot'] = $query->row();

        return $data;
    }

    public function acumdiario($data) {
        $min = convierte($data['min'], $this->idfor);
        $max = convierte($data['max'], $this->idfor);
        $sl = $this->datos_model->getParams('sl');
        $query = $this->db->query("SELECT t1.*,
            SEC_TO_TIME(ROUND(coalesce(t2.promacw,0)/if((t1.llamadas+t1.llamsalida) <> 0, t1.llamadas+t1.llamsalida, 1))) promacw
            from (SELECT date_format(c.datetime_received, '$this->dfor') fecha,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Terminada')
                then c.duration_wait else null end)),0)) promresp,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Abandonada')
                then c.duration_wait else null end)),0)) promaban,
                COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) llamadas,
                COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada' and c.id_user is not null,1,0)),0) ateporage,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Entrante')
                then c.duration else null end)),0)) promllam,
                COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0) llamaban,
                COALESCE(SUM(IF(c.type='Saliente',1,0)),0) llamsalida,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Saliente')
                then c.duration else null end)),0)) promllamsal,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when c.type='Entrante'
                then c.duration else null end)),0)) promllament,
                IF(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0)>0, ROUND(COALESCE(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) /
                    (COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                    COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0))*100,0), 0), '- ') porcontes,
                COALESCE(SUM(if(c.type='Entrante' and (c.status='Abandonada' and c.duration_wait <3), 1, 0)),0) llamaba3seg,
                COALESCE(ROUND(avg(case when c.status='Terminada' and c.duration_wait<=$sl->segundos then 100
                    when c.status='Terminada' and c.duration_wait>$sl->segundos then 0 else null end)),100) nivserv,
                    $sl->segundos seg
            FROM call_entry c
            WHERE (c.status='Terminada' or c.status='Abandonada') and c.id_campaign in ($data[campana])
                and DATE(c.datetime_received) BETWEEN '$min' AND '$max' group by fecha) t1
            left join (SELECT DATE(datetime_init) dia, sum(duration) promacw
            FROM break_entry LEFT JOIN break ON break.id = break_entry.id_break
            WHERE break.name = 'acw' AND DATE(datetime_init) BETWEEN '$min' AND '$max'
            GROUP BY DATE(datetime_init)) t2
            on t2.dia = t1.fecha
            ORDER BY t1.fecha DESC");
        $data['data'] = $query->result();
        $query = $this->db->query("SELECT t1.*,
            SEC_TO_TIME(ROUND(coalesce(t2.promacw,0)/if((t1.llamadas+t1.llamsalida) <> 0, t1.llamadas+t1.llamsalida, 1))) promacw
            from (SELECT date_format(c.datetime_received, '$this->dfor') fecha,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Terminada')
                then c.duration_wait else null end)),0)) promresp,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Abandonada')
                then c.duration_wait else null end)),0)) promaban,
                COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) llamadas,
                COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada' and c.id_user is not null,1,0)),0) ateporage,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Entrante')
                then c.duration else null end)),0)) promllam,
                COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0) llamaban,
                COALESCE(SUM(IF(c.type='Saliente',1,0)),0) llamsalida,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Saliente')
                then c.duration else null end)),0)) promllamsal,
                SEC_TO_TIME(COALESCE(ROUND(avg(case when c.type='Entrante'
                then c.duration else null end)),0)) promllament,
                ROUND(COALESCE(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) /
                    (COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                    COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0))*100,0), 0) porcontes,
                COALESCE(SUM(if(c.type='Entrante' and (c.status='Abandonada' and c.duration_wait <3), 1, 0)),0) llamaba3seg,
                COALESCE(ROUND(avg(case when c.status='Terminada' and c.duration_wait<=$sl->segundos then 100
                    when c.status='Terminada' and c.duration_wait>$sl->segundos then 0 else null end)),100) nivserv
            FROM call_entry c
            WHERE DATE(c.datetime_received) BETWEEN '$min' AND '$max' and c.id_campaign in ($data[campana])) t1,
            (SELECT DATE(datetime_init) dia, sum(duration) promacw
            FROM break_entry LEFT JOIN break ON break.id = break_entry.id_break
            WHERE break.name = 'acw' AND DATE(datetime_init) BETWEEN '$min' AND '$max') t2");
        $data['tot'] = $query->row();

        return $data;
    }


    public function reporte($data) {
        if( !in_array($data['reporte'], ['sla']) ) {
            $data['min'] = convierte($data['min'], $this->idfor);
            $data['max'] = convierte($data['max'], $this->idfor);
        }

        if($data['reporte']=="sla") {
            // Obtenemos los datos
            $registros = [];
            $rows = $this->slin($data);
            //Obtenemos los segundos del reporte
            $seg = ( isset($rows['data'][0]->seg) ) ? $rows['data'][0]->seg."'s" : '-';
            // Definimos los campos a mostrar
            $campos = [
                "hora"=>"Hora", 
                "tsf"=>"SL($seg)", 
                "aba"=>"ABA",
                "asa"=>"ASA", 
                "tat"=>"AHT", 
                "llamadas"=>"Recibidas", 
                "atendidas"=>"Atendidas",
                "abandonadas"=>"Abandonadas", 
                "larga"=>"Llamada más larga", 
                "opers"=>"Agentes por intervalo",
                //"seg"=>"20"
            ];
            // Pasamos los valores a $registros en formato array
            foreach ($rows['data'] as $row) {
                // Convertir el objeto a un array
                $registros[] = [
                    'hora' => $row->hora,
                    'tsf' => $row->tsf,
                    'aba' => $row->aba,
                    'asa' => $row->asa,
                    'tat' => $row->tat,
                    'llamadas' => $row->llamadas,
                    'atendidas' => $row->atendidas,
                    'abandonadas' => $row->abandonadas,
                    'larga' => $row->larga,
                    'opers' => $row->opers,
                ];
            }
            // Agregamos los totales
            $registros[] = [
                "hora"          => 'TOTAL', 
                "tsf"           => $rows['tot'][0]->tsf,
                "aba"           => $rows['tot'][0]->aba,
                "asa"           => $rows['tot'][0]->asa,
                "tat"           => $rows['tot'][0]->tat,
                "llamadas"      => $rows['tot'][0]->llamadas,
                "atendidas"     => $rows['tot'][0]->atendidas,
                "abandonadas"   => $rows['tot'][0]->abandonadas,
                "larga"         => $rows['tot'][0]->larga,
                "opers"         => $rows['tot'][0]->opers,
            ];    
            $res = [
                'registros' => $registros, 
                'campos' => $campos
            ];

            return $res;
        }

        if($data['reporte']=="slou") {
                /*$data['min'] = convierte($data['min'], $this->idfor);
                $data['max'] = convierte($data['max'], $this->idfor);*/
                $bet = "AND date(c.datetime_received) BETWEEN '".$data['min']."' AND '".$data['max']."'";

                $camps = explode(',', $data['campana']);
                $filtrote = "and (scamword";

                foreach ($camps as $id_camp) {
                    $query = $this->db->query("SELECT * from campaign_hour where id_campaign =?", array($id_camp));
                    $dayshours = $query->result();
                    if($data['tipo']==1) { // Dentro del horario de servicio
                        foreach ($dayshours as $key => $day) {
                            if ($day->inicio != null && $day->fin != null) {
                                $filtrote .= " or (camp.id IN ($id_camp) AND time(c.datetime_received) BETWEEN '$day->inicio' AND '$day->fin' AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                            }
                        }
                    } elseif($data['tipo']==2) { // Fuera del horario de servicio
                        foreach ($dayshours as $key => $day) {
                            if ($day->inicio != null && $day->fin != null) {
                                $filtrote .= " or (camp.id IN ($id_camp) AND time(c.datetime_received) NOT BETWEEN '$day->inicio' AND '$day->fin' AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                            } else {
                                $filtrote .= " or (camp.id IN ($id_camp) AND DAYOFWEEK(c.datetime_received) = '$day->dia')\n";
                            }
                        }
                    } else {
                        $filtrote .= " or camp.id IN ($id_camp)\n";
                    }
                }
                $filtrote .= ")\n";
                $filtrote = str_replace("scamword or ", "", $filtrote);
                $prequery = "SELECT IF(MINUTE(c.datetime_received)<30,CONCAT(date_format(c.datetime_received,'%H'),':00'),
                        CONCAT(date_format(c.datetime_received,'%H'),':30')) AS hora,
                        COALESCE(sum(IF(c.status='Terminada',1,0)),0)+COALESCE(sum(IF(c.status='Abandonada' or c.status='Abandonada Troncal',1,0)),0) AS llamadas,
                        COALESCE(sum(IF(c.status='Terminada',1,0)),0) exito,
                        COALESCE(sum(IF(c.status='Abandonada' or c.status='Abandonada Troncal',1,0)),0) abandono,
                        SEC_TO_TIME(sum(c.duration)) AS ocupacion,
                        SEC_TO_TIME(COALESCE(ROUND(avg(case when c.status='Terminada' then c.duration else null end)),0)) as aht,
                        SEC_TO_TIME(COALESCE(MAX(c.duration),0)) AS larga,
                    COALESCE(COUNT(DISTINCT c.id_user),1) AS opers
                    FROM call_entry c
                    LEFT JOIN campaign camp ON camp.id = c.id_campaign
                    WHERE c.type='Saliente' $bet $filtrote ";
                $query = $this->db->query($prequery . " GROUP BY hora");


                $res["registros"] = $query->result_array();
                foreach ($res["registros"] as $key => $row) {
                    if ($row['llamadas'] == 0) {
                        unset($res["registros"][$key]);
                    }
                }
                $res["campos"] = array("hora"=>"Hora", "llamadas"=>"Llamadas", "exito"=>"Exitosas", "abandono"=>"Abandono", "ocupacion"=>" Tiempo Ocupacion",
                    "tat"=>"AHT", "larga"=>"Llamada más larga", "opers"=>"Agentes por intervalo");
                return $res;
        }

        if($data['reporte']=="acumensual") {
            $min = $data['min']." 00:00:00";
            $max = $data['max']." 23:59:59";
            $campana = $data['campana'];
            $sl = $this->datos_model->getParams('sl');
            $query = $this->db->query("SELECT t1.*,
                SEC_TO_TIME(ROUND(coalesce(t2.promacw,0)/if((t1.llamadas+t1.llamsalida) <> 0, t1.llamadas+t1.llamsalida, 1))) promacw
                from (SELECT date_format(c.datetime_received, '%m-%Y') fecha,
                    COALESCE(cm.name, '') campana,
                    SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Terminada')
                    then c.duration_wait else null end)),0)) promresp,
                    SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Abandonada')
                    then c.duration_wait else null end)),0)) promaban,
                    COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) llamadas,
                    COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada' and c.id_user is not null,1,0)),0) ateporage,
                    SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Entrante')
                    then c.duration else null end)),0)) promllam,
                    COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0) llamaban,
                    COALESCE(SUM(IF(c.type='Saliente',1,0)),0) llamsalida,
                    SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Saliente')
                    then c.duration else null end)),0)) promllamsal,
                    SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Entrante')
                    then c.duration else null end)),0)) promllament,
                    IF(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                    COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0)>0, ROUND(COALESCE(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) /
                        (COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                        COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0))*100,0), 0), '- ') porcontes,
                    COALESCE(SUM(if(c.type='Entrante' and (c.status='Abandonada' and c.duration_wait <3), 1, 0)),0) llamaba3seg,
                    COALESCE(ROUND(avg(case when c.status='Terminada' and c.duration_wait<=$sl->segundos then 100
                        when c.status='Terminada' and c.duration_wait>$sl->segundos then 0 else null end)),100) nivserv
                FROM call_entry c
                left join campaign cm on cm.id = c.id_campaign
                WHERE YEAR(c.datetime_received) BETWEEN YEAR('$min') AND YEAR('$max') AND c.id_campaign IN ($campana)
                    AND MONTH(c.datetime_received) BETWEEN MONTH('$min') AND MONTH('$max')
                group by MONTH(c.datetime_received), c.id_campaign) t1
                left join (SELECT DATE(datetime_init) dia, sum(duration) promacw
                FROM break_entry LEFT JOIN break ON break.id = break_entry.id_break
                WHERE break.name = 'acw'
                    AND YEAR(datetime_init) BETWEEN YEAR('$min') AND YEAR('$max')
                    AND MONTH(datetime_init) BETWEEN MONTH('$min') AND MONTH('$max')
                GROUP BY MONTH(datetime_init)) t2
                on t2.dia = t1.fecha
                ORDER BY t1.fecha DESC");

                $res["registros"] = $query->result_array();
                $res["campos"] = array("fecha"=>"Fecha","campana"=>"" ,"promresp"=>"ASA", "promaban"=>"ABA",
                    "llamadas"=>"Atendidas", "ateporage"=>"Por agentes", "promllam"=>"AHT",
                    "llamaban"=>"Abandonadas", "llamsalida"=>"Salientes", "promllamsal"=>"Prom salientes",
                    "promllament"=>"Prom entrantes", "porcontes"=>"% contestadas", "llamaba3seg"=>"Aba >3s",
                    "nivserv"=>"SL($sl->segundos's)", "promacw"=>"Prom ACW");
                return $res;
        }

        if($data['reporte']=="acumdiario") {
            $min = $data['min']." 00:00:00";
            $max = $data['max']." 23:59:59";
            $sl = $this->datos_model->getParams('sl');
            $query = $this->db->query("SELECT t1.*,
                SEC_TO_TIME(ROUND(coalesce(t2.promacw,0)/if((t1.llamadas+t1.llamsalida) <> 0, t1.llamadas+t1.llamsalida, 1))) promacw
                from (SELECT date_format(c.datetime_received, '$this->dfor') fecha,
                    SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Terminada')
                    then c.duration_wait else null end)),0)) promresp,
                    SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.type='Entrante' and c.status='Abandonada')
                    then c.duration_wait else null end)),0)) promaban,
                    COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) llamadas,
                    COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada' and c.id_user is not null,1,0)),0) ateporage,
                    SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Entrante')
                    then c.duration else null end)),0)) promllam,
                    COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0) llamaban,
                    COALESCE(SUM(IF(c.type='Saliente',1,0)),0) llamsalida,
                    SEC_TO_TIME(COALESCE(ROUND(avg(case when (c.status='Terminada' and c.type='Saliente')
                    then c.duration else null end)),0)) promllamsal,
                    SEC_TO_TIME(COALESCE(ROUND(avg(case when c.type='Entrante'
                    then c.duration else null end)),0)) promllament,
                    IF(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                    COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0)>0, ROUND(COALESCE(COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) /
                        (COALESCE(SUM(IF(c.type='Entrante' and c.status ='Terminada',1,0)),0) +
                        COALESCE(SUM(IF(c.type='Entrante' and c.status = 'Abandonada',1 ,0)), 0))*100,0), 0), '- ') porcontes,
                    COALESCE(SUM(if(c.type='Entrante' and (c.status='Abandonada' and c.duration_wait <3), 1, 0)),0) llamaba3seg,
                    COALESCE(ROUND(avg(case when c.status='Terminada' and c.duration_wait<=$sl->segundos then 100
                        when c.status='Terminada' and c.duration_wait>$sl->segundos then 0 else null end)),100) nivserv
                FROM call_entry c
                WHERE (c.status='Terminada' or c.status='Abandonada') and DATE(c.datetime_received) BETWEEN '$min' AND '$max' and c.id_campaign in ($data[campana]) group by fecha) t1
                left join (SELECT DATE(datetime_init) dia, sum(duration) promacw
                FROM break_entry LEFT JOIN break ON break.id = break_entry.id_break
                WHERE break.name = 'acw' AND DATE(datetime_init) BETWEEN '$min' AND '$max'
                GROUP BY DATE(datetime_init)) t2
                on t2.dia = t1.fecha
                ORDER BY t1.fecha DESC");

                $res["registros"] = $query->result_array();
                $res["campos"] = array("fecha"=>"Fecha", "promresp"=>"ASA", "promaban"=>"ABA", "llamadas"=>"Atendidas",
                    "ateporage"=>"Por agentes", "promllam"=>"AHT", "llamaban"=>"Abandonadas", "llamsalida"=>"Salientes",
                    "promllamsal"=>"Prom salientes", "promllament"=>"Prom entrantes", "porcontes"=>"% contestadas",
                    "llamaba3seg"=>"Aba >3s", "nivserv"=>"SL($sl->segundos's)", "promacw"=>"Prom ACW");
                return $res;
        }

        $res["registros"] = $query->result_array();
        $res["campos"] = $query->list_fields();
        return $res;
    }

}

?>
