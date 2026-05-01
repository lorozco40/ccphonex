<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Encuesta_model extends CI_Model
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

    public function encuesta_detalle($data) {
        $maswere = "AND c.id_campaign IN ($data[campana])";
        $maswere .= (empty($data['agente'])) ? " AND c.id_user < 0" : " AND c.id_user IN ($data[agente])";

        include_once('valores.php');

        if (empty($data['campana']) || empty($valores) || empty($valores[$data['campana']])) {
            $data['cuenta'] = 0;
            $data['data'] = $data['registros'] = [];

            return $data;
        }
        $preguntas = $valores[$data['campana']];
        $data['prequery'] = "SELECT c.cid_num telefono, e.linkedid 'Caller ID',
            date_format(c.datetime_received, '$this->dtfor') 'Fecha',
            CONCAT(u.name,' ',u.last) 'Agente', ";
        foreach ($preguntas as $keyp => $ops) {
            $cierre = "''";
            foreach ($ops as $keyo => $val) {
                $data['prequery'] .= "if(SUM(IF(e.archivo='pregunta" . $keyp . "', e.respuesta, 0))=" .
                    $keyo . ", '" . $val . "', ";
                $cierre .= ")";
            }
            $data['prequery'] .= $cierre . " 'Pregunta " . $keyp . "', ";
        }
        $data['prequery'] = rtrim($data['prequery'], ', ');
        $data['prequery'] .= " FROM encuestas e
            LEFT JOIN call_entry c ON c.uniqueid = e.linkedid
            LEFT JOIN user u ON u.id = c.id_user
            WHERE DATE(c.datetime_received) BETWEEN '$data[min]' AND '$data[max]' $maswere GROUP BY e.linkedid ORDER BY e.linkedid";
        $data = $this->datos_model->manejadorqueries($data);

        return $data;
    }


    public function reportes_encuesta($data) {
        if($data['reporte']=="encuesta_detalle") {
            $fini = convierte($data['min'], $this->idfor)." 00:00:00";
            $ffin = convierte($data['max'], $this->idfor)." 23:59:59";
            $agente = ($data['agente']!="0") ? "AND u.id IN (".$data['agente'].")" : "";
            $query = $this->db->query("SELECT e.linkedid 'Linkedid', date_format(c.datetime_received, '$this->dtfor') 'Fecha', CONCAT(u.name,' ',u.last) 'Agente',
                SUM(IF(e.archivo='pregunta1', e.respuesta, 0)) 'Pregunta 1',
                SUM(IF(e.archivo='pregunta2', e.respuesta, 0)) 'Pregunta 2',
                SUM(IF(e.archivo='pregunta3', e.respuesta, 0)) 'Pregunta 3'
                FROM encuestas e
                LEFT JOIN call_entry c ON c.uniqueid = e.linkedid
                LEFT JOIN user u ON u.id = c.id_user
                WHERE DATE(c.datetime_received) BETWEEN '$fini' AND '$ffin' $agente GROUP BY e.linkedid ORDER BY e.linkedid");
        }
        $result["registros"] = $query->result_array();
        $result["campos"] = $query->list_fields();

        return $result;
    }

}
