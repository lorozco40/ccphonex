<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Norma035_model extends CI_Model
{

    public function dashindicadores($campana, $min, $max) {
        if(stripos($campana, ",") === FALSE) {
            $val_select = 0;
        } else {
            $val_select = 1;
        }

        $query = $this->db->query("SELECT id, name FROM campaign WHERE active=1 AND id IN ($campana) ");
        $data["campanas"] = $query->result();
        $maswere = ($val_select!=0) ? "AND camp.id IN ($campana)" : "AND camp.id = '".$campana."' ";
        $query = $this->db->query("SELECT if(MINUTE(c.datetime_received)<30,CONCAT(date_format(c.datetime_received,'%H'),':00'),
                CONCAT(date_format(c.datetime_received,'%H'),':30')) as medias_horas, COUNT(c.datetime_received) as ate
            FROM vm_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE DATE(c.datetime_received) BETWEEN '$min' AND '$max' $maswere
            GROUP BY medias_horas ORDER BY medias_horas");
        $ate = $query->result();

        $data["area"] = array();
        foreach($ate as $row) {
            $data["area"][$row->medias_horas]['ate'] = $row->ate;
        }
        ksort($data["area"]);

        return $data;
    }


    public function dashdiasemana($campana, $min, $max) {
        if( stripos($campana, ",") === FALSE ) {
            $val_select = 0;
        } else {
            $val_select = 1;
        }
        $query = $this->db->query("SELECT id, name FROM campaign WHERE active=1 AND id IN ($campana) ");
        $data["campanas"] = $query->result();
        $maswere = ($val_select!=0) ? "AND camp.id IN ($campana)" : "AND camp.id = (".$campana.") ";
        $query = $this->db->query("SELECT (
                CASE
                    WHEN WEEKDAY(c.datetime_received)=0 THEN 'Lunes'
                    WHEN WEEKDAY(c.datetime_received)=1 THEN 'Martes'
                    WHEN WEEKDAY(c.datetime_received)=2 THEN 'Miércoles'
                    WHEN WEEKDAY(c.datetime_received)=3 THEN 'Jueves'
                    WHEN WEEKDAY(c.datetime_received)=4 THEN 'Viernes'
                    WHEN WEEKDAY(c.datetime_received)=5 THEN 'Sábado'
                    WHEN WEEKDAY(c.datetime_received)=6 THEN 'Domingo'
                END
                ) AS diasemana,
                COUNT(c.datetime_received) as ate,
                WEEKDAY(c.datetime_received) AS dianumerico
            FROM vm_entry c
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE  DATE(c.datetime_received) BETWEEN '$min' AND '$max' $maswere
            GROUP BY diasemana ORDER BY dianumerico");
        $ate = $query->result();
        $data["area"] = array();
        $diab = 0;
        $diasarray = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
        foreach ($diasarray as $key => $value) {
            $data["area"][$value]['ate'] = 0;
        }
        foreach($ate as $row) {
                $data["area"][$row->diasemana]['ate'] = $row->ate;
            $diab++;
        }
        //ksort($data["area"]);
        return $data;
    }

    public function call_abandono($min, $max, $campana, $llamadas, $ini, $lim) {
        $min = convierte($min, $this->idfor);
        $max = convierte($max, $this->idfor);
        $this->load->model("usuario_model");

        if( stripos($campana, ",") === FALSE ) {
            $val_select = 0;
        } else {
            $val_select = 1;
        }

        $perm = $this->usuario_model->get_permisos($this->session->userdata('uid'));
        $calificar = false;
        foreach ($perm as $permiso) {
            if ($permiso->cat == 'permiso' && $permiso->data=='calidad/guardareval' && $permiso->val==1) $calificar = true;
        }
        $fini="$min 00:00:00";
        $ffin="$max 23:59:59";
        $maswere = ($val_select!=0) ? " AND camp.id IN ($campana)" : "";
        if ($llamadas!='0')  {
            $maswere .=" AND c.status='".$llamadas."'";
        }
        $prequery = "SELECT c.id, qv.total, date_format(c.datetime_received, '$this->dtfor') fecha,
            c.cid_num numero, c.uniqueid CallerId, if(c.did<>'',camp.name,'') campana, c.did did,
            SEC_TO_TIME(if(c.datetime_queued is not null, c.duration_wait, 0)) duration_wait, SEC_TO_TIME(c.duration) duracion,
            TIMEDIFF(COALESCE(c.datetime_init, c.datetime_end), c.datetime_received) duration_tot,
            c.status estatus, c.grabacion
            FROM vm_entry c
            LEFT JOIN user u ON c.id_user=u.id
            left join campaign camp ON camp.id = c.id_campaign
            LEFT JOIN (
                SELECT qv.id_call_entry, sum(qf.weight) total FROM quality_values qv
                INNER JOIN quality_fields qf ON qf.id = qv.id_quality_fields
                GROUP BY qv.id_call_entry) qv ON qv.id_call_entry = c.id
            WHERE c.datetime_received BETWEEN '$fini' AND '$ffin' $maswere
                AND c.type='Entrante' AND (c.status<>'En curso' and c.status<>'Terminada')
            ORDER BY c.datetime_received DESC";
        $query = $this->db->query($prequery);
        $data["cuenta"] = $query->num_rows();
        $query = $this->db->query($prequery . " limit $ini, $lim");
        $data["data"] = $query->result();
        foreach ($data["data"] as $key => $row) {
            $boton = "";
            if ($row->total != null) {
                if ($row->total <= 69.4) {
                    $clase = "esrojo";
                } elseif ($row->total <= 79.4) {
                    $clase = "esnaranja";
                } elseif ($row->total <= 89.4) {
                    $clase = "esamarillo";
                } else {
                    $clase = "esverde";
                }
                $estrellas = (int)round($row->total/20);
                $vacias = 5 - $estrellas;
                for ($i = 0; $i < $estrellas; $i++) {
                    $boton .= "<i class='fas fa-star ".$clase."'></i>";
                }
                for ($i = 0; $i < $vacias; $i++) {
                    $boton .= "<i class='far fa-star ".$clase."'></i>";
                }
            } else {
                if ($row->estatus == "Terminada" && $row->duracion >= "00:00:09" && $calificar) {
                    $boton = "<button type='button' class='btn btn-success lanzamodal' data-src='".
                    $data["data"][$key]->grabacion."' data-toggle='modal' data-target='#exampleModalLong' data-id='".
                    $row->id."' data-cola='".$row->campana."'>Evaluación</button>";
                }
            }
            $data["data"][$key]->calidad = $boton;
            $data["data"][$key]->grabacion = "<button id='aud".$key.
                "' data-toggle='modal' data-target='#escuchaudio' class='btn btn-info dinau' data-id='".
                $row->id."' data-src='".$data["data"][$key]->grabacion."'>Audio</button>";
        }

        return $data;
    }
}
?>
