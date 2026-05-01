<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Desconexion_model extends CI_Model
{

    public function desconexion_de_llamadas($data) {
        $data['prequery'] = "SELECT c.uniqueid call_id,
            DATE(CONVERT_TZ(c.datetime_received,'-05:00','+00:00')) call_date,
            TIME(CONVERT_TZ(c.datetime_received,'-05:00','+00:00')) call_time, c.duration_wait call_waiting_time,
            c.duration call_duration, c.cid_num phone, COALESCE(u.user, '') agent_email,
            COALESCE(concat(u.name,' ',u.last), '') agent_name,
            '8' agent_working_total_hours, if(c.type='Entrante', 'inbound', 'outbound') call_direction,
            if(c.hangup='Local','agent','client') disconnected_by, if(c.status='Terminada','attended_by_agent','lost_in_queue') call_type
            FROM call_entry c LEFT JOIN user u ON u.id = c.id_user
            LEFT JOIN campaign camp ON camp.id = c.id_campaign
            WHERE DATE(c.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
            AND camp.id IN ($data[campana]) AND c.status <> 'Abandonada troncal' AND status <> 'En curso'
            ORDER BY c.datetime_received DESC";

        return $this->datos_model->manejadorqueries($data);
    }

}
