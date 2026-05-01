<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repoesp_model extends CI_Model
{
    //Modelo para la vista de formularios aicm
    public function tickets($data) {
        $formulario = (empty($data['formulario'])) ? 0 : $data['formulario'];
        list($dateformat, $timeformat) = explode(" ", $this->dtfor);
        $data['prequery'] = "";
        switch ($formulario) {
            case '25': // Voti Garret PROVETECNIA
            case '26': // Leidos Ceia PROVETECNIA
            case '44': // Voti Garret AICM
            case '43': // Leidos Ceia AICM
            case '31': // Provisión
                $prequery = "SELECT t.id AS 'Ticket', concat(c.name, ' ', c.last) AS 'Persona que reporta',
                    t.area_que_reporta AS 'Area de la persona que reporta', t.ing_resp AS 'Ingeniero responsable',
                    t.no_serie AS 'Serie', t.marca AS 'Marca', t.modelo AS 'Modelo',
                    t.ubicacion AS 'Ubicación', t.detalle AS 'Descripción',
                    COALESCE(date_format(t.creacion, '$dateformat'), '') AS 'Fecha apertura',
                    COALESCE(TIME(t.creacion), '') AS 'Hora apertura', COALESCE(date_format(t.llegada, '$dateformat'), '') AS 'Fecha llegada',
                    COALESCE(TIME(t.llegada), '') AS 'Hora llegada',
                    COALESCE(ROUND(time_to_sec(TIMEDIFF(t.llegada, t.creacion)) / 60),'') as 'Tiempo de arribo',
                    t.estado_final AS 'Estado final', t.acciones AS 'Acciones realizadas',
                    COALESCE(date_format(t.termino, '$dateformat'), '') AS 'Fecha cierre', COALESCE(TIME(t.termino), '') AS 'Hora cierre',
                    if(t.estatus <> 'Cerrado','Abierto',t.estatus) AS 'Estatus' FROM formd_".$data['formulario']." t LEFT JOIN client c on c.id = t.id_cliente";

                $elwhere  = " WHERE DATE(t.creacion) BETWEEN '$data[min]' AND '$data[max]'";
                if (empty($data['estatus'])) {
                } elseif ($data['estatus'] == 'Cerrado') {
                    $elwhere .= " AND t.estatus = 'Cerrado'";
                } else {
                    $elwhere .= " AND t.estatus <> 'Cerrado'";
                }
                $elwhere .= (!empty($data['tid'])) ? " AND (t.id = '".
                    $data['tid']."' OR c.name LIKE '%".$data['tid']."%' OR c.last LIKE '%".
                    $data['tid']."%' OR t.ing_resp LIKE '%".$data['tid']."%' OR t.area_que_reporta LIKE '%".
                    $data['tid']."%' OR t.no_serie LIKE '%".$data['tid']."%' OR t.marca LIKE '%".
                    $data['tid']."%' OR t.modelo LIKE '%".$data['tid']."%' OR t.ubicacion LIKE '%".
                    $data['tid']."%' OR t.detalle LIKE '%".$data['tid']."%' OR t.estado_final LIKE '%".
                    $data['tid']."%' OR t.acciones LIKE '%".$data['tid']."%')" : "";
                $elwhere .= (!empty($data['orden'])) ? " ORDER BY ".$data['orden'] : "";
                $data['prequery'] = $prequery . $elwhere;
                break;

            case '32': // ing_op
                $orderBy = '';
                if ( !empty($data['orden']) ) {
                    $ordenArray = explode(" ", $data['orden']);
                    $field = $ordenArray[0];
                    $sort  = $ordenArray[1];
                    if ( $field == 't.acciones') $field = 't.accion_realizada';
                    $orderBy = " ORDER BY $field $sort";
                }
                $prequery = "SELECT t.id AS 'Ticket',
                    COALESCE(date_format(fecha_contacto, '$dateformat'), '') AS 'Fecha contacto',
                    COALESCE(date_format(fecha_contacto, '$timeformat'), '') AS 'Hora contacto',
                    no_serie AS Serie, marca AS Marca, modelo AS Modelo, ubicacion AS Ubicación, aduana AS Aduana, detalle AS Detalle,
                    nom_contacto AS 'Nombre contacto', ing_contactado AS 'Ingeniero contactado',
                    COALESCE(date_format(llegada, '$dateformat'), '') AS 'Fecha llegada', COALESCE(date_format(llegada, '$timeformat'), '') AS 'Hora llegada',
                    COALESCE(date_format(f_hora_de_llamada, '$dateformat'), '') AS 'Fecha de llamada', COALESCE(date_format(f_hora_de_llamada, '$timeformat'), '') AS 'Hora de llamada',
                    accion_realizada AS 'Acciones realizadas', estado_final AS 'Estado final',
                    COALESCE(date_format(termino, '$dateformat'), '') AS 'Fecha termino', COALESCE(date_format(termino, '$timeformat'), '') AS 'Hora termino', if(t.estatus<>'Cerrado','Abierto',t.estatus) AS 'Estatus'
                    FROM formd_".$data['formulario']." t
                    LEFT JOIN client c on c.id = t.id_cliente";
                $elwhere  = " WHERE DATE(t.fecha_contacto) BETWEEN '$data[min]' AND '$data[max]'";
                if (empty($data['estatus'])) {
                } elseif ($data['estatus'] == 'Cerrado') {
                    $elwhere .= " AND t.estatus = 'Cerrado'";
                } else {
                    $elwhere .= " AND t.estatus <> 'Cerrado'";
                }
                $elwhere .= (!empty($data['tid'])) ? " AND (t.id = '".
                    $data['tid']."%' OR t.no_serie LIKE '%".$data['tid']."%' OR t.marca LIKE '%".
                    $data['tid']."%' OR t.modelo LIKE '%".$data['tid']."%' OR t.ubicacion LIKE '%".
                    $data['tid']."%' OR t.detalle LIKE '%".$data['tid']."%' OR t.estado_final LIKE '%".
                    $data['tid']."%' OR t.accion_realizada LIKE '%".$data['tid']."%')" : "";
                $elwhere .= $orderBy;
                $data['prequery'] = $prequery . $elwhere;
                break;

            case '33': // sedena_leidos (Asipona manzanillo)
                $orderBy = '';
                if ( !empty($data['orden']) ) {
                    $ordenArray = explode(" ", $data['orden']);
                    $field = $ordenArray[0];
                    $sort  = $ordenArray[1];
                    if ( $field == 't.no_serie') $field = 't.serie';
                    $orderBy = " ORDER BY $field $sort";
                }
                $prequery = "SELECT t.id AS 'Ticket', 
                    COALESCE(date_format(creacion, '$dateformat'), '') AS 'Fecha reporte',
                    COALESCE(date_format(creacion, '$timeformat'), '') AS 'Hora reporte',
                    serie AS Serie, modelo AS Modelo, marca AS Marca, ubicacion AS Ubicación, tema_de_ayuda AS Mantenimiento, quien_reporta AS 'Persona que reporta',
                    telefono AS Teléfono, cargo AS Cargo, detalle AS 'Descripción de la falla',
                    ing_resp AS 'Ingeniero responsable', 
                    COALESCE(date_format(llegada, '$dateformat'), '') AS 'Fecha llegada', COALESCE(date_format(llegada, '$timeformat'), '') AS 'Hora llegada',
                    acciones AS 'Acciones realizadas',
                    COALESCE(date_format(termino, '$dateformat'), '') AS 'Fecha cierre', COALESCE(date_format(termino, '$timeformat'), '') AS 'Hora cierre', 
                    estado_final AS 'Estado final', observaciones AS Observaciones, if(t.estatus<>'Cerrado','Abierto',t.estatus) AS 'Estatus'
                    FROM formd_".$data['formulario']." t
                    LEFT JOIN client c on c.id = t.id_cliente";
                $elwhere  = " WHERE DATE(t.creacion) BETWEEN '$data[min]' AND '$data[max]'";
                if ($data['estatus'] == '') {
                } elseif ($data['estatus'] == 'Cerrado') {
                    $elwhere .= " AND t.estatus = 'Cerrado'";
                } else {
                    $elwhere .= " AND t.estatus != 'Cerrado'";
                }
                $elwhere .= (!empty($data['tid'])) ? " AND (t.id = '".
                    $data['tid']."%' OR t.serie LIKE '%".$data['tid']."%' OR t.marca LIKE '%".
                    $data['tid']."%' OR t.modelo LIKE '%".$data['tid']."%' OR t.ubicacion LIKE '%".
                    $data['tid']."%' OR t.detalle LIKE '%".$data['tid']."%' OR t.estado_final LIKE '%".
                    $data['tid']."%' OR t.acciones LIKE '%".$data['tid']."%')" : "";
                $elwhere .= $orderBy;
                $data['prequery'] = $prequery . $elwhere;
                break;

            case '34': // Provetecnia Cargo
                $orderBy = '';
                if ( !empty($data['orden']) ) {
                    $ordenArray = explode(" ", $data['orden']);
                    $field = $ordenArray[0];
                    $sort  = $ordenArray[1];
                    if ( $field == 't.acciones') $field = 't.accion_realizada';
                    if ( $field == 't.no_serie') $field = 't.serie';
                    if ( $field == 't.accion_realizada') $field = 't.acciones';
                    $orderBy = " ORDER BY $field $sort";
                }
                $prequery = "SELECT t.id AS 'Ticket',
                    COALESCE(date_format(creacion, '$dateformat'), '') AS 'Fecha reporte',
                    COALESCE(date_format(creacion, '$timeformat'), '') AS 'Hora reporte',
                    fuente AS 'Fuente', serie AS Serie, modelo AS Modelo, marca AS Marca, ubicacion AS 'Ubicación', contrato AS 'Contrato',
                    ing_a_cargo AS 'Ingeniero a cargo', tema_de_ayuda AS Mantenimiento, quien_reporta AS 'Quien reporta', telefono AS 'Teléfono',
                    cargo AS Cargo, detalle AS 'Descripción de la falla', resolucion AS 'Resolución',
                    COALESCE(date_format(llegada, '$dateformat'), '') AS 'Fecha llegada',
                    COALESCE(date_format(llegada, '$timeformat'), '') AS 'Hora llegada',
                    acciones AS 'Acciones realizadas',
                    COALESCE(date_format(termino, '$dateformat'), '') AS 'Fecha cierre',
                    COALESCE(date_format(termino, '$timeformat'), '') AS 'Hora cierre', 
                    estado_final AS 'Estado final', observaciones AS Observaciones, t.estatus AS 'Estatus'
                    FROM formd_".$data['formulario']." t
                    LEFT JOIN client c on c.id = t.id_cliente";
                $elwhere  = " WHERE DATE(t.creacion) BETWEEN '$data[min]' AND '$data[max]'";
                $elwhere .= (isset($data['estatus']) && $data['estatus'] != '' ) ? " AND t.estatus = '".$data['estatus']."'" : "";
                $elwhere .= (!empty($data['tid'])) ? " AND (t.id = '".$data['tid']."%'
                    OR t.serie LIKE '%".$data['tid']."%'
                    OR t.marca LIKE '%".$data['tid']."%'
                    OR t.modelo LIKE '%".$data['tid']."%'
                    OR t.ubicacion LIKE '%".$data['tid']."%'
                    OR t.detalle LIKE '%".$data['tid']."%'
                    OR t.estado_final LIKE '%".$data['tid']."%'
                    OR t.acciones LIKE '%".$data['tid']."%')" : "";
                $elwhere .= $orderBy;
                $data['prequery'] = $prequery . $elwhere;
                break;

            case '35': case '41': case '48': case '49': //segtec
                $orderBy = '';
                if ( !empty($data['orden']) ) {
                    $ordenArray = explode(" ", $data['orden']);
                    $field = $ordenArray[0];
                    $sort  = $ordenArray[1];
                    if ( $field == 't.llegada')  $field = 't.fhora_llegada';
                    if ( $field == 't.termino')  $field = 't.cierre';
                    if ( $field == 't.creacion') $field = 't.apertura';
                    if ( $field == 't.acciones') $field = 't.actividad_realizada';
                    $orderBy = " ORDER BY $field $sort";
                }
                $prequery = "SELECT t.id AS 'Ticket',
                    concat(c.name, ' ', c.last) AS 'Persona que reporta', area_reporta AS 'Área que reporta',
                    concat(ua.name, ' ', ua.last) AS 'Responsable SEGTEC', no_serie AS Serie,
                    COALESCE(marca, '') AS Marca, modelo AS Modelo, ubicacion AS Ubicación, detalle AS 'Descripción',
                    COALESCE(date_format(apertura, '$dateformat'), '') AS 'Fecha apertura',
                    COALESCE(date_format(apertura, '$timeformat'), '') AS 'Hora apertura',
                    COALESCE(date_format(fhora_llegada, '$dateformat'), '') AS 'Fecha llegada', 
                    COALESCE(date_format(fhora_llegada, '$timeformat'), '') AS 'Hora llegada',
                    COALESCE(
                        CONCAT(
                            IF(TIMESTAMPDIFF(HOUR, apertura, fhora_llegada) >= 0 AND TIMESTAMPDIFF(HOUR, apertura, fhora_llegada) <= 9,'0', ''), 
                            TIMESTAMPDIFF(HOUR, apertura, fhora_llegada),
                            ':',
                            IF( (TIMESTAMPDIFF(MINUTE, apertura, fhora_llegada)%60) >= 0 AND (TIMESTAMPDIFF(MINUTE, apertura, fhora_llegada)%60) <= 9,'0', ''),
                            (TIMESTAMPDIFF(MINUTE, apertura, fhora_llegada)%60)
                        ), ''
                    ) AS 'Tiempo de arribo',
                    estado_final AS 'Estado final', descripcion AS 'Tipo de reporte', actividad_realizada AS 'Acciones realizadas',
                    COALESCE(date_format(fhora_termino, '$dateformat'), '') AS 'Fecha termino',
                    COALESCE(date_format(fhora_termino, '$timeformat'), '') AS 'Hora termino',
                    if(estatus<>'Cerrado','Abierto',estatus) AS 'Estatus'
                    FROM formd_".$data['formulario']." t
                    LEFT JOIN user ua on ua.id = t.asignar_a
                    LEFT JOIN client c on c.id = t.id_cliente";
                $elwhere  = " WHERE DATE(t.apertura) BETWEEN '$data[min]' AND '$data[max]'";
                if (empty($data['estatus'])) {
                } elseif ($data['estatus'] == 'Cerrado') {
                    $elwhere .= " AND estatus = 'Cerrado'";
                } else {
                    $elwhere .= " AND estatus <> 'Cerrado'";
                }
                $elwhere .= (!empty($data['tid'])) ? " AND (t.id = '".$data['tid']."%'
                    OR t.no_serie LIKE '%".$data['tid']."%'
                    OR t.modelo LIKE '%".$data['tid']."%'
                    OR t.detalle LIKE '%".$data['tid']."%'
                    OR t.estado_final LIKE '%".$data['tid']."%'
                )" : "";
                $elwhere .= $orderBy;
                $data['prequery'] = $prequery . $elwhere;
                break;

            case '28': // otros_clientes
                $orderBy = '';
                if ( !empty($data['orden']) ) {
                    $ordenArray = explode(" ", $data['orden']);
                    $field = $ordenArray[0];
                    $sort  = $ordenArray[1];
                    if ( $field == 't.llegada') $field = 't.fhora_llegada';
                    if ( $field == 't.acciones') $field = 't.accion_realizada';
                    $orderBy = " ORDER BY $field $sort";
                }
                $prequery = "SELECT t.id AS 'Ticket',
                COALESCE(date_format(t.creacion, '$dateformat'), '') AS 'Fecha de contacto', COALESCE(date_format(t.creacion, '$timeformat'), '') AS 'Hora de contacto',
                t.no_serie AS Serie, t.marca AS Marca, t.modelo AS Modelo, t.ubicacion AS Ubicación, t.direccion AS Dirección, t.cliente AS Cliente,
                t.no_inventario AS Inventario, t.version AS Version, t.detalle AS Detalle, t.nombre_contacto AS 'Nombre contacto',
                COALESCE(date_format(t.llegada, '$dateformat'), '') AS 'Fecha de llegada', COALESCE(date_format(t.llegada, '$timeformat'), '') AS 'Hora de llegada',
                t.ing_contactado AS 'Ing. Contactado', t.como_se_encontro AS 'Como se encontró', t.accion_realizada AS 'Acciones realizadas', t.estatus_final AS 'Estado final',
                COALESCE(date_format(t.termino, '$dateformat'), '') AS 'Fecha de termino', COALESCE(date_format(t.termino, '$timeformat'), '') AS 'Hora de termino'
                    FROM formd_".$data['formulario']." t
                ";
                $elwhere  = " WHERE DATE(t.apertura) BETWEEN '$data[min]' AND '$data[max]'";
                $elwhere .= (isset($data['estatus']) && $data['estatus'] != '' ) ? " AND t.estatus = '".$data['estatus']."'" : "";
                $elwhere .= (!empty($data['tid'])) ? " AND (t.id = '".$data['tid']."%'
                    OR t.nombre_contacto LIKE '%".$data['tid']."%'
                    OR t.serie LIKE '%".$data['tid']."%'
                    OR t.modelo LIKE '%".$data['tid']."%'
                    OR t.marca LIKE '%".$data['tid']."%'
                    OR t.detalle LIKE '%".$data['tid']."%'
                    OR t.accion_realizada LIKE '%".$data['tid']."%'
                    OR t.ing_contactado LIKE '%".$data['tid']."%'
                    OR t.estatus_final LIKE '%".$data['tid']."%'
                )" : "";
                $elwhere .= $orderBy;
                $data['prequery'] = $prequery . $elwhere;
                break;

            case '42': // IOSA RX 42 en .113 cc_phonex, 50 en .34
                $orderBy = '';
                if ( !empty($data['orden']) ) {
                    $ordenArray = explode(" ", $data['orden']);
                    $field = $ordenArray[0];
                    $sort  = $ordenArray[1];
                    if ( $field == 't.acciones') $field = 't.accion_realizada';
                    if ( $field == 't.no_serie') $field = 't.serie';
                    if ( $field == 't.accion_realizada') $field = 't.acciones';
                    $orderBy = " ORDER BY $field $sort";
                }
                $prequery = "SELECT t.id AS 'Ticket',
                    COALESCE(date_format(creacion, '$dateformat'), '') AS 'Fecha reporte',
                    COALESCE(date_format(creacion, '$timeformat'), '') AS 'Hora reporte',
                    fuente AS 'Fuente', serie AS Serie, modelo AS Modelo, marca AS Marca, ubicacion AS 'Ubicación', contrato AS 'Contrato',
                    ing_a_cargo AS 'Ingeniero a cargo', coordinador AS 'Coordinador', tema_de_ayuda AS Mantenimiento, quien_reporta AS 'Quien reporta', telefono AS 'Teléfono',
                    cargo AS Cargo, detalle AS 'Descripción de la falla', resolucion AS 'Resolución',
                    COALESCE(date_format(llegada, '$dateformat'), '') AS 'Fecha llegada',
                    COALESCE(date_format(llegada, '$timeformat'), '') AS 'Hora llegada',
                    acciones AS 'Acciones realizadas',
                    COALESCE(date_format(termino, '$dateformat'), '') AS 'Fecha cierre',
                    COALESCE(date_format(termino, '$timeformat'), '') AS 'Hora cierre', 
                    estado_final AS 'Estado final', observaciones AS Observaciones, t.estatus AS 'Estatus'
                    FROM formd_".$data['formulario']." t
                    LEFT JOIN client c on c.id = t.id_cliente";
                $elwhere  = " WHERE DATE(t.creacion) BETWEEN '$data[min]' AND '$data[max]'";
                $elwhere .= (isset($data['estatus']) && $data['estatus'] != '' ) ? " AND t.estatus = '".$data['estatus']."'" : "";
                $elwhere .= (!empty($data['tid'])) ? " AND (t.id = '".$data['tid']."%'
                    OR t.serie LIKE '%".$data['tid']."%'
                    OR t.marca LIKE '%".$data['tid']."%'
                    OR t.modelo LIKE '%".$data['tid']."%'
                    OR t.ubicacion LIKE '%".$data['tid']."%'
                    OR t.detalle LIKE '%".$data['tid']."%'
                    OR t.estado_final LIKE '%".$data['tid']."%'
                    OR t.acciones LIKE '%".$data['tid']."%')" : "";
                $elwhere .= $orderBy;
                $data['prequery'] = $prequery . $elwhere;
                break;

            case '50': // ANAM-Provetecnia
            case '51': // ANAM-IOSA
                $cambio = "t.direccion AS 'Dirección'";
                if ($formulario == 51) {
                    $cambio = "t.aduana AS 'Aduana'";
                }
                $prequery = "SELECT t.id AS 'Ticket', t.id_externo as 'ID Anam', COALESCE(DATE_FORMAT(t.apertura, '$dateformat'), '') AS 'Fecha contacto',
                    COALESCE(TIME(t.apertura), '') AS 'Hora contacto',
                    t.serie AS 'Serie', t.marca AS 'Marca', t.modelo AS 'Modelo', t.ubicacion AS 'Ubicación', $cambio,
                    t.cliente AS 'Cliente', t.no_inventario AS 'Inventario', t.version AS 'Versión', t.detalle AS 'Detalle',
                    t.quien_reporta AS 'Nombre contacto', COALESCE(DATE_FORMAT(t.f_hora_llegada, '$dateformat'), '') AS 'Fecha llegada',
                    COALESCE(TIME(t.f_hora_llegada), '') AS 'Hora llegada', t.ing_contactado as 'Ing. Contactado',
                    t.como_se_encontro AS 'Cómo se encontró', t.accion_realizada AS 'Acciones realizadas',
                    t.estatus_final AS 'Estado final', COALESCE(date_format(t.cierre, '$dateformat'), '') AS 'Fecha cierre',
                    COALESCE(TIME(t.cierre), '') AS 'Hora cierre',
                    if(t.estatus <> 'Cerrado','Abierto',t.estatus) AS 'Estatus' FROM formd_".$data['formulario']." t ";
                $elwhere  = " WHERE DATE(t.apertura) BETWEEN '$data[min]' AND '$data[max]'";
                if (empty($data['estatus'])) {
                } elseif ($data['estatus'] == 'Cerrado') {
                    $elwhere .= " AND t.estatus = 'Cerrado'";
                } else {
                    $elwhere .= " AND t.estatus <> 'Cerrado'";
                }
                $elwhere .= (!empty($data['tid'])) ? " AND (t.id = '".
                    $data['tid']."'  OR t.id_externo LIKE '%".$data['tid']."%' OR t.cliente LIKE '%".
                    $data['tid']."%' OR t.quien_reporta LIKE '%".
                    $data['tid']."%' OR t.serie LIKE '%".$data['tid']."%' OR t.marca LIKE '%".
                    $data['tid']."%' OR t.modelo LIKE '%".$data['tid']."%' OR t.ubicacion LIKE '%".
                    $data['tid']."%' OR t.detalle LIKE '%".$data['tid']."%' OR t.estatus_final LIKE '%".
                    $data['tid']."%')" : "";
                $elwhere .= (!empty($data['orden'])) ? " ORDER BY ".$data['orden'] : "";
                $data['prequery'] = $prequery . $elwhere;
                break;

            default:
                break;
        }

        return $data;
    }

}
