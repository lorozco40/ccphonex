<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tarificacion_model extends CI_Model
{

    public function tarificacion($data) {
        $data['prequery'] = "SELECT t.id, date_format(t.calldate, '$this->dtfor') AS Fecha,
            t.cid_num AS 'Número', coalesce(e.val,'') AS 'Extensión',
            CONCAT(u.name,' ',u.last) AS Agente, c.name AS 'Campaña',
            t.uniqueid AS CallerId, t.minutos AS Minutos, t.status AS Estatus, t.tipo_red, t.costo
            FROM tarifas t
            LEFT JOIN user u ON u.id = t.id_user
            LEFT JOIN campaign c ON c.id = t.id_campaign
            LEFT JOIN (select ud.id_user, ud.val FROM user_data ud
                LEFT JOIN catalogs cat ON cat.id = ud.id_catalog where cat.val = 'userask') e ON e.id_user = t.id_user
            WHERE DATE(t.calldate) BETWEEN '$data[min]' AND '$data[max]'
            AND t.id_campaign IN ($data[campana]) AND u.id>1
            AND u.id IN ($data[agente])
            ORDER BY t.calldate DESC";
        $data['totales_query'] = "SELECT '' AS a, '' AS b, '' AS c, '' AS d, '' AS e,
            'Totales: ' AS 'Totales', SUM(t.minutos) AS Minutos, '' AS f, '' AS g, SUM(t.costo) AS Costo
            FROM tarifas t
            LEFT JOIN user u ON u.id = t.id_user
            LEFT JOIN campaign c ON c.id = t.id_campaign
            LEFT JOIN (select ud.id_user, ud.val FROM user_data ud
            LEFT JOIN catalogs cat ON cat.id = ud.id_catalog where cat.val = 'userask') e ON e.id_user = t.id_user
            WHERE DATE(t.calldate) BETWEEN '$data[min]' AND '$data[max]'
            AND t.id_campaign IN ($data[campana]) AND u.id>1
            AND u.id IN ($data[agente])
            ORDER BY t.calldate DESC";
        return $this->datos_model->manejadorqueries($data);
    }

    public function calcular() {
        $file = FCPATH.'ift.csv';
        if (file_exists($file)) {
            $this->subirIFTcsv($file);
        }
        /* Abandonar o eliminar llamadas con ERROR no identificable */
        $query = $this->db->query("UPDATE `call_entry` set `status` = 'Abandonada Troncal'
            WHERE `status` = 'En curso' and `datetime_received` < '" .
            date('Y-m-d H:i:s', strtotime("-12 hours")) . "'");
        //Nuevo query para obtener los datos de tcell y tlocal de campaign a campaign_data
        $query = $this->db->query("SELECT c.id,
        CASE
            WHEN tl.valor IS NULL THEN 0
            ELSE tl.valor
        END AS tlocal,
        CASE
            WHEN tc.valor IS NULL THEN 0
            ELSE tc.valor
        END AS tcell
        FROM campaign c
        LEFT JOIN (
            SELECT id_campaign, atributo, valor
            FROM campaign_data
            WHERE atributo = 'tlocal'
        ) tl ON c.id = tl.id_campaign
        LEFT JOIN (
            SELECT id_campaign, atributo, valor
            FROM campaign_data
            WHERE atributo = 'tcell'
        ) tc ON c.id = tc.id_campaign
        ORDER BY c.id;");

        if (!$query || $query->num_rows() == 0) return "Tar: No hay campañas en el sistema";
        $cams = [];
        foreach ($query->result() as $cam) {
            $cams[$cam->id] = $cam;
        }
        $query = $this->db->query("SELECT `id` FROM `tarifas` ORDER BY `id` DESC limit 1");
        $maswere = "";
        if ($query->num_rows()==1) {
            $ult = $query->row();
            $maswere .= " AND `id` > '$ult->id'";
        }
        $query = $this->db->query("SELECT `id` FROM `call_entry` WHERE `status` = 'En curso' limit 1");
        if ($query->num_rows()==1) {
            $ult = $query->row();
            $maswere .= " AND `id` < '$ult->id'";
        }
        $query = $this->db->query("SELECT `id`, `id_user`, `id_campaign`, `cid_num`, `uniqueid`,
            `duration`, `datetime_received`, `status` from `call_entry`
            where `type` = 'Saliente' AND `status` <> 'Abandonada Troncal' $maswere limit 2990");
        $tot = $query->num_rows();
        if (!$query || $tot == 0) return "Tar: No hay llamadas para calcular";
        $count = 0;
        $toin  = "INSERT IGNORE INTO `tarifas` VALUES ";
        $res = $query->result();
        foreach ($res as $fila) {
            $numa10 = substr($fila->cid_num, -10);
            $iftq = $this->db->query("SELECT * from `iftdata` where `nirie` = '".substr($numa10, 0, 6)."' limit 1");
            if (!$iftq || $iftq->num_rows()==0) {
                $tipo_red = 'DESCONOCIDO';
            } else {
                $ift = $iftq->row();
                $tipo_red = $ift->tipo_red;
            };
            $id_user   = (empty($fila->id_user)) ? 'NULL' : "'$fila->id_user'";
            $id_cam    = (empty($fila->id_campaign)) ? 'NULL' : "'$fila->id_campaign'";
            $tipo_llam = ($tipo_red == 'MOVIL') ? 'tcell' : (($tipo_red == 'FIJO') ? 'tlocal' : '');
            $tarifa    = (!empty($fila->id_campaign) && $tipo_red != 'DESCONOCIDO') ? $cams[$fila->id_campaign]->$tipo_llam : 0;
            $minutos   = ceil($fila->duration/60);
            $toin .= "('".$fila->id."',".$id_user.",".$id_cam.",'".$fila->uniqueid."','".
                $fila->cid_num."','".$fila->datetime_received."','".$fila->status.
                "','".$fila->duration."','".$tipo_red."','".$minutos."','".
                $minutos*$tarifa."'),";
            $count++;
            if($count==500) {
                $toin = rtrim($toin, ",").";";
                if(!$this->db->query($toin)) echo "Tar: " . var_dump($this->db->error());
                $toin = "INSERT IGNORE INTO `tarifas` VALUES ";
                $count = 0;
            }
        }
        if ($toin != "INSERT IGNORE INTO `tarifas` VALUES ") {
            $toin = rtrim($toin, ",").";";
            if(!$this->db->query($toin)) echo "Tar: " . var_dump($this->db->error());
        }
        return "Tar: Registros calculados: " . $tot;
    }

    private function subirIFTcsv($arch) {
        $this->db->query("TRUNCATE `iftdata`");
        $file = fopen($arch, "r");
        $toin = "INSERT INTO `iftdata` VALUES ";
        $count = 0;
        while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {
            $toin .= "(0,'".$row[0]."','".$row[1]."','".$row[2]."','".$row[3]."','".$row[4]."','".$row[5]."','".
                $row[6]."','".$row[7]."','".$row[8]."','".$row[9]."','".$row[10]."','".$row[11]."','".$row[12]."','".
                $row[13]."','".$row[14]."','".$row[15]."','".$row[16]."'),";
            $count++;
            if ($count == 500) {
                $toin = rtrim($toin, ",") . ";";
                if(!$this->db->query($toin)) {
                    die($this->db->error());
                }
                $toin = "INSERT INTO `iftdata` VALUES ";
                $count = 0;
            }
        }
        $toin = rtrim($toin, ",") . ";";
        $this->db->query($toin);
        fclose($file);
        unlink($arch);
        return;
    }

}

?>
