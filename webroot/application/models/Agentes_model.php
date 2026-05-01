<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agentes_model extends CI_Model
{

    public function lista($camps = []){
        $query = $this->db->query("SELECT u.id, concat(u.name,' ',u.last) name,
            coalesce(tex.val,'') extension, coalesce(tca.val,'') campanas, coalesce(tpe.val,'') perfil
            FROM user u
            LEFT JOIN (SELECT user_data.id_user, user_data.val FROM catalogs
                INNER JOIN user_data ON user_data.id_catalog = catalogs.id
                WHERE catalogs.cat='userData' AND catalogs.val='userask') tex
                ON tex.id_user = u.id
            LEFT JOIN (SELECT user_data.id_user, user_data.val FROM catalogs
                INNER JOIN user_data ON user_data.id_catalog = catalogs.id
                WHERE catalogs.cat='userData' AND catalogs.val='campanas') tca
                ON tca.id_user = u.id
            LEFT JOIN (SELECT user_data.id_user, user_data.val FROM catalogs
                INNER JOIN user_data ON user_data.id_catalog = catalogs.id
                WHERE catalogs.cat='userData' AND catalogs.val='perfil') tpe
                ON tpe.id_user = u.id
            WHERE u.id > 1 AND u.active = 1
            HAVING perfil <> 'admin'
            ORDER BY if(extension = '' OR extension is null, 1, 0), extension, name ASC"
        );
        $res = $query->result();
        $camsbyid = [];
        foreach ($camps as $key => $cam) {
            $camsbyid[$cam->id] = $cam->name;
        }
        foreach ($res as $key => $row) {
            $csids = explode(",", $row->campanas);
            $sal = [];
            foreach ($csids as $ckey => $cval) {
                if(!empty($camsbyid[$cval])) {
                    $sal[] = $camsbyid[$cval];
                }
            }
            $res[$key]->camps = implode(" / ", $sal);
        }

        return $res;
    }

    public function getCliXCam($cid, $id = 0) {
        $query = $this->db->query("SELECT `id`,
            concat(`name`,if(LENGTH(`last`)>0,concat(' ',`last`),''),' ',if(`id_user` is null, '(P)', '(M)')) AS nombre
            FROM client where ((id_user is null or id_user = ?) and (id_campaign = ? or id_campaign is null)) or id = ? order by name, last",
            [$this->udata['id'], $cid, $id]
        );

        return $query->result();
    }

    //Encuentra un cliente de un formulario: por nombre
    public function getCliFormXName($id_form, $nombre = '') {
        $id_client = '';
        //obtenemos la campaña del formulario
        $form = $this->db->query("SELECT id_campaign FROM form WHERE id = ?", [$id_form])->row();
        if( $form ) {
            $id_campaign = $form->id_campaign;
            $client = $this->db->query("SELECT id, name
                FROM client
                WHERE (id_user IS NULL OR id_user = ?) AND (id_campaign = ? OR id_campaign IS NULL)
                AND concat(name, if(LENGTH(last)>0,concat(' ',last),''),' ') = ?
                ORDER BY id", [$this->udata['id'], $id_campaign, $nombre]
            )->row();
            if( $client )
                $id_client = $client->id;
        }
        
        return $id_client;
    }

    public function status($campana) {
        $camp = ",".str_replace(",", ",|,", $campana).",";
        $arraycolores = array(
            "Desconectado" => "#7D7D7D", // gris
            "En llamada"   => "#E92C26", // rojo
            "Llamando"     => "#FF9330", // naranja
            "comida"       => "#00972E", // verde
            "sanitario"    => "#BD7032", // cafe
            "acw"          => "#A30094", // morado
            "retro"        => "#0099C2", // turki
            "break"        => "#930017", // guinda
            "Disponible"   => "#3266C6", // azul
            "Otro"         => "#EA3976"  // Rosa
        );
        $ctxOps = ["ssl"=>["verify_peer"=>false,"verify_peer_name"=>false]];
        $ariserv = "https://" . getenv('ARI_USER') . ":" . getenv('ARI_PASS') . "@" . getenv('ASS_DB_HOST') . ":8089";
        $exts = json_decode(file_get_contents($ariserv . "/ari/endpoints/PJSIP", false, stream_context_create($ctxOps)));
        $salida = $canales = $eventos = $out = $outt = $colrs = array();
        foreach ($exts as $key => $value) {
            $salida[$value->resource]['stat'] = $value->state;
            $salida[$value->resource]['chans'] = $value->channel_ids;
        }
        $query = $this->db->query("SELECT user_id, detalle evto,
            time_format(TIMEDIFF(now(), evento), '%H:%i:%s') `time`,
            time_to_sec(TIMEDIFF(now(), evento)) `timecalc`
            from user_log where id in (select max(id) id from user_log group by user_id) order by user_id");
        foreach ($query->result() as $key => $row) {
            $eventos[$row->user_id]['time'] = $row->time;
            $eventos[$row->user_id]['timecalc'] = $row->timecalc;
            $eventos[$row->user_id]['evto'] = $row->evto;
        }
        $query = $this->db->query("SELECT u.id, concat(u.name,' ',u.last) name,
            coalesce(tex.val,'') extension, coalesce(tca.val,'') campanas, coalesce(tpe.val,'') perfil
            from user u
            left join (SELECT user_data.id_user, user_data.val from catalogs
                inner join user_data on user_data.id_catalog = catalogs.id
                where catalogs.cat='userData' and catalogs.val='userask') tex
                on tex.id_user = u.id
            left join (SELECT user_data.id_user, user_data.val from catalogs
                inner join user_data on user_data.id_catalog = catalogs.id
                where catalogs.cat='userData' and catalogs.val='campanas'
                and concat(\",\",user_data.val,\",\") regexp '".$camp."') tca
                on tca.id_user = u.id
            left join (SELECT user_data.id_user, user_data.val from catalogs
                inner join user_data on user_data.id_catalog = catalogs.id
                where catalogs.cat='userData' and catalogs.val='perfil') tpe
                on tpe.id_user = u.id
            where u.id > 1 and u.active = 1 and length(tca.val)>0
            having perfil <> 'admin'
            ORDER BY if(extension = '' or extension is null, 1, 0), extension, name ASC"
        );
        $res = $query->result();
        foreach ($res as $key => $row) {
            $valfin = "Desconectado";
            if (!empty($eventos[$row->id]) && $eventos[$row->id]['evto'] != "Logout") {
                $valfin = (substr($eventos[$row->id]['evto'], 0, 8) == "Descanso") ?
                    substr($eventos[$row->id]['evto'], 10) :
                    $eventos[$row->id]['evto'];
                $valfin = (substr($valfin, 0, 20) == "En llamada, llamando") ? "Llamando" : $valfin;
            }
            $res[$key]->statgraf = (array_key_exists($valfin, $arraycolores)) ? $valfin : "Otro";
            if ($valfin != "Desconectado" && empty($row->extension)) {
                $res[$key]->exstat = 'offline';
                $res[$key]->acttime = $eventos[$row->id]['time'];
                if (substr($eventos[$row->id]['evto'], 0, 8) == "Descanso") {
                    $res[$key]->estatus = 'Ocupado';
                    $res[$key]->act = ($eventos[$row->id]['evto'] == "Disponible" || $eventos[$row->id]['evto'] == "Entra consola") ? "" : $eventos[$row->id]['evto'];
                } else {
                    $res[$key]->estatus = 'Disponible';
                    $res[$key]->act = '';
                    $res[$key]->statgraf = 'Disponible';
                }
            } elseif ($valfin != "Desconectado" && $salida[$row->extension]['stat'] != 'offline') {
                $res[$key]->exstat = $salida[$row->extension]['stat'];
                $res[$key]->acttime = $eventos[$row->id]['time'];
                if (count($salida[$row->extension]['chans'])>0 || substr($eventos[$row->id]['evto'], 0, 8) == "Descanso") {
                    $res[$key]->estatus = 'Ocupado';
                    $res[$key]->act = ($eventos[$row->id]['evto'] == "Disponible" || $eventos[$row->id]['evto'] == "Entra consola") ? "" : $eventos[$row->id]['evto'];
                } else {
                    $res[$key]->estatus = 'Disponible';
                    $res[$key]->act = '';
                    $res[$key]->statgraf = 'Disponible';
                }
            } else {
                $res[$key]->estatus = 'Desconectado';
                $res[$key]->exstat = 'offline';
                $res[$key]->acttime = '';
                $res[$key]->act = '';
                $res[$key]->statgraf = 'Desconectado';
            }
            $res[$key]->color = $arraycolores[$res[$key]->statgraf];
        }
        foreach ($res as $key => $row){
            if ($row->statgraf != "Desconectado") {
                if (array_key_exists($row->statgraf, $out)){
                    $out[$row->statgraf]++;
                } else {
                    $out[$row->statgraf] = 1;
                }
            }
        }
        $outt[] = array("Estatus", "Cantidad");
        foreach ($out as $key => $value) {
            $outt[] = array($key, $value);
            $colrs[] = $arraycolores[$key];
        }

        $sl = $this->datos_model->getParams('sl');
        $sql = "SELECT
            coalesce(sum(if(type='Entrante',1,0)), 0) rec,
            coalesce(SUBSTR(sec_to_time(round(avg(if(type='Entrante' and status='Terminada',duration,null)))),4,5),'00:00') avgdura,
            coalesce(sum(if(type='Entrante' and status='Abandonada',1,0)), 0) aba,
            coalesce(SUBSTR(sec_to_time(round(avg(if(type='Entrante' and status='Abandonada',duration_wait,null)))),4,5),'00:00') avgaba,
            coalesce(round(sum(if(type='Entrante' and status='Terminada' and duration_wait <= " . $sl->segundos . ",1,0)) / sum(if(type='Entrante' and status = 'Terminada',1,0)) * 100, 0),'') porsl,
            coalesce(SUBSTR(sec_to_time(round(avg(if(type='Entrante' and status = 'Terminada', duration_wait, null)))),4,5),'00:00') avgwait
            from call_entry
            where date(datetime_received) = date(now())
            and (id_campaign in ($campana) ";
        if( strpos($campana, ",") !== false ) {
          $sql.= " or id_campaign is null ";
        }
          $sql.= ");";
        $query = $this->db->query($sql);
        $this->load->helper("fun_helper");

        $data['estad'] = $query->row();
        $data['colas'] = colas();
        $data['status'] = $res;
        $data['graf'] = $outt;
        $data['colores'] = $colrs;

        return $data;
    }

    private function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }

    private function random_color() {
        return "#" . $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
    }

}
