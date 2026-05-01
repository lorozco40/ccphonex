<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Videollamada_model extends CI_Model
{

    // Tipos:
    // 1 Recibe x segmentación (agente, femenino)
    // 2 Recibe x segmentación (agente, masculino o indefinido)
    // 3 Recibe x segmentación (agente rol mp, femenino)
    // 4 Recibe x segmentación (agente rol mp, masculino o indefinido)
    // 5 Transfer              (supervisor)

    // Estatus:
    // 0 No disponible,
    // 1 disponible,
    // 2 Llamada entrante, generar sala
    // 3 Sala generada
    // 4 Llamada en curso

    public function escrip($cam = '1') {
        $query = $this->db->query("SELECT url from videocall_serv where id_campaign = ? and activ = 1", [$cam]);

        return $query->row()->url;
    }

    public function nuevaVideollamada($data) {
        $cid = $data['c']; // Campaign ID
        $folio = (isset($data['folio'])) ? $data['folio'] : '';
        $prior = (isset($data['prioridad'])) ? $data['prioridad'] : '0';
        $name  = (isset($data['nombre'])) ? $data['nombre'] : '';
        unset($data['c']);
        unset($data['vcServ']);
        $incdata = json_encode($data); // Incoming data GET
        $this->db->query("INSERT into videocall_entry (ip, id_campaign, incdata,
            folio, name, priority, status, datetime_entry_queue)
            values (?, ?, ?, ?, ?, ?, 'En cola', now())",
            array(getUserIP(), $cid, $incdata, $folio, $name, $prior));
        $nid = $this->db->insert_id();

        return $nid;
    }

    public function checkroomasig($ret) { // Registro ID
        $query = $this->db->query("SELECT * FROM videocall_entry WHERE id = ?", [$ret['idconv']]);
        $reg = $query->row();
        if ($reg->id_user != null && $reg->status == 'En cola') {
            $ret["room"] = $reg->grabacion;
        } elseif ($reg->status == 'Rechazada') {
            $ret["close"] = true;
        } else {
            $newroom = $this->eligeagente($reg);
            if ($newroom) {
                $ret["room"] = $newroom;
            }
        }

        return $ret;
    }

    public function eligeagente($vce) {
        // ToDo: Agregar segmentación
        $query = $this->db->query("SELECT * FROM videocall_chans WHERE sala <> '' AND estatus = '1'
            ORDER BY cuando LIMIT 1");
        if ($query->num_rows() == 0) {
            return false;
        }
        $this->db->query("UPDATE videocall_chans SET estatus = '2', vcreg = '$vce->id'
            WHERE id_user = ?", [$query->row()->id_user]);
        $this->db->query("UPDATE videocall_entry SET id_user = ?, grabacion = ?
            WHERE id = ?", [$query->row()->id_user, $query->row()->sala, $vce->id]);

        return $query->row()->sala;
    }

    public function chekresp($data) {
        $query = $this->db->query("SELECT * from videocall_entry where id = ?", [$data['idconv']]);
        $reg = $query->row();
        $query = $this->db->query("SELECT estatus from videocall_chans where id_user = '$reg->id_user'");
        if ($query->row()->estatus == 3) {
            $this->db->query("UPDATE videocall_entry set status = 'En curso', datetime_init = now()
                WHERE id = ?", [$data['idconv']]);
            return ['room'=>true];
        } elseif ($query->row()->estatus != 2) {
            return ['close'=>true];
        }

        return false;
    }

    public function fin($data) {
        $query = $this->db->query("SELECT * from videocall_entry
            WHERE id = ? AND datetime_end IS NULL", [$data['idconv']]);
        if ($query->num_rows() == "1") {
            $reg = $query->row();
            $this->db->query("UPDATE videocall_chans SET sala = '', estatus = '0', vcreg = '0'
                WHERE id_user = '$reg->id_user'");
            $this->db->query("UPDATE videocall_entry set datetime_end=now(),
                duration=TIME_TO_SEC(TIMEDIFF(now(), COALESCE(datetime_init, NOW()))),
                duration_wait=TIME_TO_SEC(TIMEDIFF(COALESCE(datetime_init, NOW()), datetime_entry_queue)),
                status=? where id=?",
                array($data['tipo'], $data['idconv'])
            );
        }

        return true;
    }

    public function tranp1() { // Transferencia paso 1 checar disponibles
        $res = [];
        $query = $this->db->query("SELECT u.id, vc.tipo, vc.sala, concat(u.name, ' ', u.last) 'nombre'
            FROM videocall_chans vc
            JOIN user u ON u.id = vc.id_user WHERE vc.estatus = '1' ORDER BY vc.cuando");
        if ($query->num_rows()>=1){
            $disp = $query->result();
            $res['form'] = "<div class='container mt-5 mb-5'><h3>Disponibles:</h3><br />";
            $tipos = [1=>'Auxiliar, Mujer',2=>'Auxiliar, Hombre',3=>'MP, Mujer',4=>'MP, Hombre',5=>'Supervisor'];
            foreach ($disp as $key => $row) {
                $res['form'] .= "<a href='#' class='tranp2' data-id='" . $row->id . "' data-sala='" .
                $row->sala . "'>" . $tipos[$row->tipo] . ", " . $row->nombre . "</a><br />";
            }
            $res['form'] .= "</div>";
        } else {
            $res['msg'] = "No hay nadie disponible para transferir";
        }

        return $res;
    }

    public function tranp2($in) { // Transferencia paso 2 apartar usuario
        $query = $this->db->query("SELECT * FROM videocall_chans WHERE id_user = ?", [$in['tid']]);
        if ($query->row()->estatus == '1') {
            $query = $this->db->query("SELECT * FROM videocall_entry WHERE id = ?", [$in['tfrom']]);
            $tfrom = $query->row();
            $this->db->query("INSERT INTO videocall_entry (id_user, id_campaign, ip, incdata, callerid,
                folio, `name`, priority, `status`, `transfer`, datetime_entry_queue) values (?,?,?,?,?,?,?,?,?,?,NOW())",
                [$in['tid'],$tfrom->id_campaign,$tfrom->ip,$tfrom->incdata,$tfrom->callerid,$tfrom->folio,
                $tfrom->name,$tfrom->priority,'En curso',$tfrom->id]);
            $nid = $this->db->insert_id();
            $this->db->query("UPDATE videocall_chans SET estatus = '2', vcreg = '$nid' WHERE id_user = ?", [$in['tid']]);
            $this->db->query("UPDATE videocall_entry SET `transfer` = '$nid' WHERE id = ?", [$in['tfrom']]);
            return true;
        }

        return ['error' => 'El usuario ya esta ocupado o se ha ido!'];
    }

    public function tranp3($in) {
        $query = $this->db->query("SELECT estatus FROM videocall_chans WHERE id_user = ?", [$in['tid']]);
        $row = $query->row();
        $ret = [];
        switch ($row->estatus) {
            case '0':
            case '1':
                $ret['rechazada'] = true;
                break;

            case '3':
                $ret['aceptada'] = true;
                break;

            default:
                break;
        }

        return $ret;
    }


    public function videollamada_detalle($data){
        $mascams = "";
        $maswer  = (empty($data['campana'])) ? "AND v.id_campaign = '0'" : " AND v.id_campaign in ($data[campana])";
        $maswer .= (empty($data['agente']) || stripos($data['agente'], ',')) ? "" : " AND u.id = '$data[agente]'";
        if(!empty($data['folio'])) {
            $maswer  .= "AND v.folio = '$data[folio]'";
        }
        if ($data['pag'] === 'x') {
            $mascams = "COALESCE(v.id_user, '') 'id Agente', v.ip 'IP', COALESCE(v.datetime_init, '') 'Inicio',
                COALESCE(v.datetime_end, '') 'Fin',";
        }
        $prequery = "SELECT v.datetime_entry_queue 'Fecha', v.folio 'Folio', $mascams
            COALESCE(concat(u.name,' ',u.last), '') 'Agente', if(instr(grabacion, 'mp4') > 0, grabacion, '') 'Grabación', v.name 'Cliente',
            SEC_TO_TIME(v.duration_wait) 'Espera', SEC_TO_TIME(v.duration) 'Duración', v.status 'Estatus'
            FROM videocall_entry v
            LEFT JOIN user u on u.id=v.id_user
            WHERE DATE(v.datetime_entry_queue) BETWEEN '$data[min]' AND '$data[max]' $maswer
            AND v.status <> 'En curso' AND v.status <> 'En cola'
            ORDER BY v.id DESC";
        $query = $this->db->query($prequery);
        if (!$query) {
            return false;
        }
        $data['cuenta'] = $query->num_rows();
        $data["campos"] = $query->list_fields();
        if ($data['pag'] === 'x') {
            $data['data'] = $query->result_array();
            return $data;
        }
        $query = $this->db->query($prequery. " limit $data[pag], $data[rpp]");
        $data['data'] = $query->result();

        return $data;
    }

    public function videollamada_indicadores($data) {
        $maswer  = (empty($data['campana'])) ? "AND v.id_campaign = '0'" : " AND v.id_campaign in ($data[campana])";
        $maswer .= (stripos($data['agente'], ',')) ? "" : " AND u.id = '$data[agente]'";
        $prequery = "SELECT DATE(v.datetime_entry_queue) 'Fecha', count(v.id) 'Total',
            coalesce(count(if(v.status='Terminada', 1, null)), 0) 'Terminadas',
            coalesce(count(if(v.status='Abandonada', 1, null)), 0) 'Abandonadas',
            coalesce(count(if(v.status='Sin agente', 1, null)), 0) 'Sin agente',
            coalesce(count(if(v.status='Agente no contesta', 1, null)), 0) 'Agente no contesta',
            coalesce(count(if(v.status not in ('Agente no contesta', 'Sin agente', 'Abandonada', 'Terminada'), 1, null)), 0) 'Otras',
            SEC_TO_TIME(ROUND(AVG(v.duration_wait))) 'Promedio de espera',
            SEC_TO_TIME(max(v.duration_wait)) 'Espera más larga'
            FROM videocall_entry v
            LEFT JOIN user u on u.id=v.id_user
            WHERE DATE(v.datetime_entry_queue) BETWEEN '$data[min]' AND '$data[max]'
            AND v.status not in ('En cola', 'En curso') $maswer
            GROUP BY DATE(v.datetime_entry_queue)
            ORDER BY v.datetime_entry_queue DESC";
        $query = $this->db->query($prequery);
        $data['cuenta'] = $query->num_rows();
        $data["campos"] = $query->list_fields();
        if ($data['pag'] === 'x') {
            $data['data'] = $query->result_array();
            return $data;
        }
        $query = $this->db->query($prequery . " limit $data[pag], $data[rpp]");
        $data['data'] = $query->result();

        return $data;
    }

    public function hadis() {
        $tipo = 5; // no recibe videollamadas directas, sólo transferencias
        if ($this->udata['perfil'] == 'agente') {
            $tipo = (in_array('autoanswer', $this->udata['permisoSec'])) ? 4 : 2; // 2 auxiliar, 4 MP
            if($this->udata['genero'] == 'F') $tipo--;
        }
        $cams = $this->udata['campanas'];
        $query = $this->db->query("SELECT url FROM videocall_serv
            WHERE id_campaign IN ($cams) AND activ = '1' LIMIT 1");
        if ($query->num_rows() == 0) {
            return ['error', 'No hay un servidor de videoconferencia programado para esta campaña.'];
        }
        $url = $query->row()->url;
        $url_arr = explode("/", $url);
        $ret['serv'] = $url_arr[2];
        $ret['sala'] = "pnx_" . $this->udata['id'] . "_" . md5(microtime(true));
        $query = $this->db->query("UPDATE videocall_chans SET sala = ?, estatus = '1', tipo = '$tipo'
            WHERE id_user = ?", [$ret['sala'], $this->udata['id']]);
        if ($query) {
            return $ret;
        }

        return ['error', 'Error 54223, consulta con soporte técnico.'];
    }

    public function hanodis() {
        $query = $this->db->query("UPDATE videocall_chans SET sala = '', estatus = '0'
            WHERE id_user = ?", [$this->udata['id']]);
        if ($query) {
            return true;
        }

        return ['error', 'Error 54223, consulta con soporte técnico.'];
    }

    public function oirtimbre() {
        $query = $this->db->query("SELECT estatus, vcreg from videocall_chans where id_user = ?",
            [$this->udata['id']]);
        $stat = $query->row();
        if($stat->estatus == '2') {
            $query = $this->db->query("SELECT incdata, `transfer` FROM videocall_entry where id = '$stat->vcreg'");
            $reg = $query->row();
            $ret = json_decode($reg->incdata, TRUE);
            if (is_array($ret)) {
                $ret['idconv'] = $stat->vcreg;
            } else {
                $ret = ['idconv'=>$stat->vcreg];
            }
            $ret['tran'] = (empty($reg->transfer)) ? false : substr($reg->transfer,1);

            return $ret;
        }

        return false;
    }

    public function aceptar() {
        return $this->db->query("UPDATE videocall_chans set estatus = '3' where id_user = ?",
            [$this->udata['id']]
        );
    }

    public function rechazar() {
        $query = $this->db->query("SELECT vcreg FROM videocall_chans where id_user = ?",
            [$this->udata['id']]
        );
        $reg = $query->row();
        $query = $this->db->query("SELECT * from videocall_entry WHERE id = ? AND datetime_end IS NULL", [$reg->vcreg]);
        if ($query->num_rows() == "1") {
            $this->db->query("UPDATE videocall_chans SET sala = '', estatus = '0', vcreg = '0'
                WHERE id_user = ?", [$this->udata['id']]);
            $this->db->query("UPDATE videocall_entry set datetime_end=now(),
                duration=TIME_TO_SEC(TIMEDIFF(now(), COALESCE(datetime_init, NOW()))),
                duration_wait=TIME_TO_SEC(TIMEDIFF(COALESCE(datetime_init, NOW()), datetime_entry_queue)),
                id_user=? `status`=? where id=?",
                array($this->udata['id'], 'Rechazada', $reg->vcreg)
            );
            return true;
        }

        return false;
    }

    public function ocupado($data) {
        $this->db->query("UPDATE videocall_entry set callerid = ?
            WHERE id = ?", [$data['callerid'], $data['idconv']]);

        return $this->db->query("UPDATE videocall_chans set estatus = '4' where id_user = ?",
            [$this->udata['id']]
        );
    }

    // Completar el nombre de la grabación
    public function cron() {
        $this->db->query("UPDATE videocall_entry SET status='Error'
            WHERE status='En cola' AND datetime_entry_queue < (NOW() - INTERVAL 70 MINUTE)");
        echo date("Y-m-d h:i:s")." ".$this->db->affected_rows()." Videollamadas En cola mas viejas de 70 minutos cambiadas a Error\n";
        $query = $this->db->query("SELECT id, grabacion FROM videocall_entry
            WHERE grabacion <> '' AND grabacion NOT LIKE '%.mp4'
            ORDER BY id DESC LIMIT 20");
        foreach ($query->result() as $key => $row) {
            $subq = $this->db->query("SELECT archivo FROM test
                where archivo like '%".$row->grabacion."%'");
            if ($subq->num_rows() == 1) {
                $graba = $subq->row()->archivo;
                $fin = explode("/", $graba);
                $fin = end($fin);
                $this->db->query("UPDATE videocall_entry SET grabacion = '$fin'
                    where id = '$row->id'");
                $this->db->query("DELETE FROM test WHERE archivo = '$graba'");
                echo date("Y-m-d h:i:s")." $fin actualizada.\n";
            }
        }
    }

    public function asign_age_llam() { // Asignar agentes disponibles a llamadas en cola
        $query = $this->db->query("SELECT * from videocall_entry WHERE status = 'En cola'
            AND id_user IS NULL AND datetime_entry_queue > now() - interval 1 HOUR
            ORDER BY priority DESC, datetime_entry_queue ASC");
        $asignadas = 0;
        $encola = ($query) ? $query->result() : [];
        foreach ($encola as $row) {
            $data = json_decode($row->incdata, true);
            $data['c'] = $row->id_campaign;
            $agente = $this->datos_model->getAgenteDisponible("videollamada", $data);
            if ($agente) {
                $this->db->query("UPDATE videocall_entry set id_user = '$agente->id_user',
                grabacion = '$agente->sala' WHERE id = '$row->id'");
                $this->db->query("UPDATE videocall_chans SET estatus = '2', vcreg = ?
                    WHERE id_user = ?", [$row->id, $agente->id_user]);
                $asignadas++;
            }
        }

        return "Videollamadas: " + count($encola) + " En cola, $asignadas Asignadas.";
    }

    // funciones para el API Rest

    public function getChans() {
        return ['3'=>'Bien'];
    }

    public function getRepo($data) {
        $hoy = new Datetime();
        $data['pag'] = 'x';
        if (empty($data['folio'])) {
            $data['min'] = (empty($data['min'])) ? $hoy->format('Y-m-d') : $data['min'];
        } else {
            $tya = new Datetime();
            $tya->sub(new DateInterval('P2Y'));
            $data['min'] = (empty($data['min'])) ? $tya->format('Y-m-d') : $data['min'];
        }
        $data['max'] = (empty($data['max'])) ? $hoy->format('Y-m-d') : $data['max'];
        $data = $this->videollamada_detalle($data);
        if ($data) {
            unset($data['pag']);
            unset($data['campos']);
            return $data;
        }
        $this->set_response(['success'=>false, 'msg'=>'Datos incorrectos'], REST_Controller::HTTP_BAD_REQUEST);

        return false;
    }

    // funciones para los permisos

    public function getData($data) {
        $pag = (empty($data['pag'])) ? 0  : (int)$data['pag'];
        $rpp = (empty($data['rpp'])) ? 20 : (int)$data['rpp'];
        $bus = "";

        if ( !empty($data['bus']) ) {
            $bus = "AND CONCAT(u.name, ' ', u.last) like '%".$data['bus']."%'";
        }

        $campanas = $this->datos_model->getCampanas(false);
        $query = $this->db->query("SELECT DISTINCT u.id,
        concat(u.name, ' ', u.last) AS nombre, ud1.val AS perfil,
        COALESCE(IF(ud2.val='','0,0,0,0,0',ud2.val),'0,0,0,0,0') AS permisos,
        ud3.val AS campanas
        FROM user u
        LEFT JOIN user_data ud1 on ud1.id_user = u.id
        LEFT JOIN user_data ud2 on ud2.id_user = u.id
        LEFT JOIN user_data ud3 on ud3.id_user = u.id
        LEFT JOIN catalogs c1 on c1.id = ud1.id_catalog
        LEFT JOIN catalogs c2 on c2.id = ud2.id_catalog
        LEFT JOIN catalogs c3 on c3.id = ud3.id_catalog
        WHERE u.id>1 AND u.active=1
        AND c1.cat = 'userData' AND c1.val = 'perfil'
        AND c2.cat = 'userData' AND c2.val = 'pervidllam'
        AND c3.cat = 'userData' AND c3.val = 'campanas'
        $bus
        ORDER by perfil, u.name, u.last;");
        $res = [];
        foreach ($query->result() as $key => $row) {
            $tmparr = explode(",", $row->campanas);
            if (array_intersect($campanas, $tmparr)) {
                $res[] = $row;
            }
        }

        $data['regs'] = count($res);

        $result = [];
        $limit = $pag+$rpp > $data["regs"] ? $data["regs"] : $pag+$rpp;
        for ($i=$pag; $i < $limit; $i++) {
            $result[] = $res[$i];
        }

        $data['data'] = $result;

        return $data;
	}

    public function updatePermisos( $data_update = [] ){
        $query = $this->db->query("SELECT id FROM catalogs c WHERE c.cat = 'userData' AND c.val = 'pervidllam';");
        if( $query->num_rows() > 0 ){
            $id_calog = $query->row()->id;
            foreach ($data_update as $row){
                $id_user = $row['id_user'];
                $val     = $row['val'];
                if( !$this->db->query("UPDATE user_data SET  val=? WHERE id_user = ? AND id_catalog = ?;", [$val, $id_user, $id_calog]) )
                    return ["error" => "Ocurrio un error al intentar actualizar uno de los valores."];
            }
        } else {
            return ["error" => "Error: no se encontro un catalogo valido para los permisos"];
        }

        return 'Datos actualizados correctamente.';
    }

}

?>
