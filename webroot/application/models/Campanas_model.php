<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Campanas_model extends CI_Model
{

    public function lista($data) {
        $pag = (empty($data['pag'])) ? 0  : (int)$data['pag'];
        $rpp = (empty($data['rpp'])) ? 20 : (int)$data['rpp'];
        $bus = "";
        if (!empty($data['bus']) && $this->udata['perfil']=='admin') {
            $bus = "where `name` like '%".$data['bus']."%'";
        } elseif (!empty($data['bus'])) {
            $bus = "AND `name` like '%".$data['bus']."%'";
        }
        $campanas = (empty($this->udata['campanas'])) ? '0' : $this->udata['campanas'];
        if ($this->udata['perfil']=='admin') {
            $prequery = "SELECT * from `campaign` $bus order by `active` DESC, `name`";
        } else {
            $prequery = "SELECT * from `campaign` where `id` in (" . $campanas . ") $bus
                order by `active` DESC, `name`";
        }
        $query = $this->db->query($prequery);
        $data['regs'] = $query->num_rows();
        $query = $this->db->query("$prequery limit ?, ?", [$pag, $rpp]);
        $data['data'] = $query->result();

        return $data;
    }

    public function guardar($data) {
        $active = (empty($data['active'])) ? 0 : 1;
        if ($data['id']==0) {
            // Crear campaña porque id=0 es un registro nuevo
            if ($this->udata['perfil']!='admin') return ['error'=>'No tienes permisos suficientes'];
            $query = $this->db->query("INSERT INTO campaign (name, dids, script, active, created_by, created_when)
                VALUES (?,?,?,?,?,now())",
                array($data['name'], $data['dids'], $data['script'], $active, $this->session->userdata('uid'))
            );
            $idcamp = $this->db->insert_id();
            if ($query) {
                $query = $this->db->query("INSERT INTO campaign_hour (id_campaign, dia, inicio, fin)
                VALUES ($idcamp,'1',NULL, NULL), ($idcamp,'2','09:00:00', '17:59:59'), ($idcamp,'3','09:00:00', '17:59:59'),
                ($idcamp,'4','09:00:00', '17:59:59'), ($idcamp,'5','09:00:00', '17:59:59'), ($idcamp,'6','09:00:00', '17:59:59'),
                ($idcamp,'7',NULL, NULL)");
            }
        } else {
            // Editar campaña
            if (empty($active)) {
                $this->db->query("UPDATE user_data ud
                    JOIN catalogs c on c.id = ud.id_catalog
                    set ud.val = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', ud.val, ','), ?, ','))
                    where c.cat = 'userData' and c.val = 'campanas' and find_in_set(?, ud.val)", [','.$data['id'].',', $data['id']]
                );
            }
            $completa = "";
            $values = [$data['name'], $data['script'], $data['id']];
            if ($this->udata['perfil']=='admin') {
                $completa = ", dids=?, active=?";
                $values = [$data['name'], $data['script'], $data['dids'], $active, $data['id']];
            }
            $query = $this->db->query("UPDATE campaign set name=?, script=? $completa where id=?", $values);
        }

        if ($query) return 'Campaña guardada exitosamente';
        return ['error'=>'Error 54223, consulta con soporte técnico'];
    }

    public function get_horario($id) {
        $query = $this->db->query("SELECT ch.*
            FROM campaign_hour ch
            WHERE ch.id_campaign=? ORDER BY ch.dia", array($id));
        return $query->result();

    }

    public function acthorario($data) {
        $query = $this->db->query("SELECT id from campaign_hour where id_campaign = ? limit 1", $data["camp_id"]);
        if ($query->result()) {
            for ($i=1; $i < 8; $i++) {
                $keyin = "d".$i."in"; $keyout = "d".$i."out";
                $in = ($data[$keyin]!='') ? $data[$keyin] : null;
                $out = ($data[$keyout]!='') ? $data[$keyout] : null;
                $query = $this->db->query("UPDATE campaign_hour set inicio=?, fin=? where id_campaign=? and dia=?",
                array($in, $out, $data["camp_id"], $i));
            }
            if ($query) {
                return "ok";
            }
        }
        return "no";
    }

    public function delcampana($id) {
        // Cuidado !! Elimina todo lo relacionado con la campaña
        $query = $this->db->query("SELECT * from `form` where `id_campaign` = ?", [$id]);
        $res = $query->result();
        $this->load->model("form_model");
        foreach ($res as $key => $row) {
            $this->form_model->delform($row->id);
        }
        $query = $this->db->query("SELECT * from `dispatcher` where `id_campaign` = ?", [$id]);
        $res = $query->result();
        $this->load->model("desp_model");
        foreach ($res as $key => $row) {
            $this->desp_model->eliminar($row->id);
        }
        $query = $this->db->query("SELECT * FROM catalogs WHERE cat = 'userData' AND val = 'campanas'");
        $cid   = $query->row()->id;
        $query = $this->db->query("SELECT id_user from `user_data` where id_catalog = ? AND `val` = ?", [$cid, $id]);
        // Eliminar usuarios exclusivos de ésta campaña
        // $this->load->model("usuarios_model");
        // foreach ($query->result() as $usuario) {
        //     $this->usuarios_model->eliminausuario($usuario->id_user);
        // }
        // Quitar asignación de campaña a usuarios que la compartan
        $this->db->query("UPDATE user_data SET val =
            TRIM(BOTH ',' FROM REPLACE(CONCAT(',', val, ','), '," . $id . ",', ','))
            WHERE id_catalog = ? AND FIND_IN_SET('" . $id . "', val)", [$cid]);
        $this->db->query("DELETE FROM `call_entry` where `id_campaign` = ?", [$id]);
        $this->db->query("DELETE FROM `campaign_data` where `id_campaign` = ?", [$id]);
        $this->db->query("DELETE FROM `campaign_hour` where `id_campaign` = ?", [$id]);
        $this->db->query("DELETE FROM `campaign_licenses` where `id_campaign` = ?", [$id]);
        $this->db->query("DELETE FROM `campaign_vm` where `id_campaign` = ?", [$id]);
        $this->db->query("DELETE FROM `tarifas` where `id_campaign` = ?", [$id]);

        return $this->db->query("DELETE FROM `campaign` where `id` = ?", [$id]);
    }

    public function atrilista($data) {
        $query = $this->db->query("SELECT * from `campaign_data` where `id_campaign` = ?
            order by `orden`, `atributo`", [$data['id']]);
        $res['attrs'] = $query->result();
        $query = $this->db->query("SELECT `tlocal`, `tcell` FROM `campaign` WHERE `id` = ?", [$data['id']]);
        $res['tar']  = $query->row();

        return $res;
    }

    public function atriguardar($data) {
        $noattr = ['id', 'tcell', 'tlocal', 'recaltarifa'];
        foreach ($data as $key => $value) {
            if (!in_array($key, $noattr)) {
                $query = $this->db->query("INSERT INTO campaign_data (id_campaign, atributo, valor)
                VALUES (?,?,?) ON duplicate key update valor=?", [$data['id'], $key, $value, $value]);
            }
        }
        if ($query) {
            $query = $this->db->query("UPDATE `campaign` SET `tlocal` = ?, `tcell` = ?
                WHERE `id` = ?", [$data['tlocal'], $data['tcell'], $data['id']]);
            if ($query && isset($data['recaltarifa']) && $this->udata['perfil']=='admin') {
                $this->db->query("UPDATE `tarifas` SET `costo` = (`minutos` * " .
                    $data['tlocal'] . ")
                    WHERE `tipo_red` = 'FIJO' AND `id_campaign` = ?", [$data['id']]);
                $this->db->query("UPDATE `tarifas` SET `costo` = (`minutos` * " .
                    $data['tcell'] . ")
                    WHERE `tipo_red` = 'MOVIL' AND `id_campaign` = ?", [$data['id']]);
            }
        }
        if ($query) return "Atributos guardados";
        return ["error" => "Error 54223, consulta con soporte técnico"];
    }

    public function atriagregar($data) {
        $atributo       = $data['atributo'];
        $id_campaign    = $data['id_campaign'];
        $valor          = $data['valor'];
        //Validamos si este atributo existe
        $sql = "SELECT * FROM campaign_data WHERE id_campaign = ? AND atributo = ?;";
        $query = $this->db->query("SELECT * FROM campaign_data WHERE id_campaign = ? AND atributo = ?;", [$id_campaign, $atributo]);
        $n = $query->num_rows();
        if($n > 0){//insertamos
            return ["error" => "Ya se habia agregado el atributo '$atributo' anteriormente"];
        }
        else{
            $query = $this->db->query("INSERT INTO campaign_data (id_campaign, atributo, valor)VALUES (?,?,?)", [$id_campaign,$atributo,$valor]);
        }

        if ($query)return "Atributo $atributo agregado.";
        return ["error" => "Error 54224, consulta con soporte técnico"];
    }

    public function get_campana($id_campaign = 0){
        $query  = $this->db->query("SELECT id, name FROM campaign WHERE id = ?;", $id_campaign)->row();
        if( $query )
            return $query;
        else
            return [];
    }

    public function atributo_eliminar($id = 0){
		if($this->db->delete('campaign_data', array('id' => $id))){
			$response = 'El atributo se elimino correctamente';
		}
		else{
            $response = ['error' => 'Error: no se pudo eliminar el concepto, consulta con soporte técnico'];
		}
        return $response;
    }

    public function atributos_guardar($datos = [], $id_campaign=0, $recaltarifa = null){
        if( $this->db->update_batch('campaign_data', $datos, 'id') === false ){
            $response = ['error' => 'Error: no se pudieron actualizar los valores, consulta con soporte técnico'];
        }
        else{
            //aplicamos la funcion paa recalculacion tarifaria
            if ( $recaltarifa == 'ok' && $this->udata['perfil']=='admin' ){
                //consultamos los atributos tlocal y tcell de la campana actual
                $rows = $this->db->query("SELECT atributo, valor FROM campaign_data WHERE id_campaign = ? AND atributo in ('tlocal', 'tcell');", [$id_campaign])->result_array();
                $n = 0;
                foreach ($rows as $row){
                    $n++;
                    $atributo   = $row['atributo'];
                    $valor      = $row['valor'];
                    $tipo_red   = $atributo == 'tlocal' ? 'FIJO' : 'MOVIL';

                    $this->db->query("UPDATE tarifas SET costo = (minutos * $valor)
                    WHERE tipo_red = ? AND id_campaign = ?", [$tipo_red, $id_campaign]);
                }

                if($n == 0){
                    $response = 'Los valores de los atributos se actualizaron correctamente, no habian costos a recalcular.';
                }
                else{
                    $response = 'Los valores de los atributos se actualizaron correctamente y se recalcularon los costos.';
                }
            }
            else{
                $response = 'Los valores de los atributos se actualizaron correctamente.';

            }
        }
        return $response;
    }

    //Recibe una lista de campaign_id (ej: 1,3,4,27) y retorna unicamente aquellas que no tengan licencia
    public function valida_licencias_por_campanas($cids = '') {
		$query = $this->db->query("SELECT c.id, c.name, IFNULL(cd.valor,0) licenses,
                IFNULL(ul.used_licenses, 0) used_licenses,
                (IFNULL(cd.valor, 0) - IFNULL(ul.used_licenses, 0)) available_licenses
            FROM campaign c
            LEFT JOIN campaign_data cd ON c.id = cd.id_campaign AND cd.atributo = 'licencias'
            LEFT JOIN (SELECT id_campaign, COUNT(id_campaign) used_licenses
            FROM campaign_licenses) ul ON ul.id_campaign = c.id
            WHERE c.id IN (" . $cids . ")
            HAVING available_licenses < 1");

        return $query;
    }

    //Se agregan los registros de licencia para cada campana del usuario en curso
    public function usar_licencias_campana($id_user = null, $campanas = []) {
        $id_user = (int)$id_user;
        if( $id_user > 0 ) {
            foreach( $campanas as $id_campaign ) {
                $this->db->query('INSERT INTO campaign_licenses (id_campaign, id_user) VALUES (?,?)',
                    [(int)$id_campaign,$id_user]);
            }
        }

        return true;
    }

    //Elimina las licencias por campana del usuario logueado
    public function elimina_licencias_usuario($id_user = 0) {
        return $this->db->delete('campaign_licenses', array('id_user' => $id_user));
    }

    public function licencias_por_campana($id_campaigns = '') {
		$query = $this->db->query("SELECT c.id, c.name, cd.valor AS licenses,
            IF( cl.used_licenses IS NULL, 0, cl.used_licenses)  AS used_licenses,
            (cd.valor-  IF( cl.used_licenses IS NULL, 0, cl.used_licenses)  ) AS available_licenses
            FROM campaign c
            LEFT JOIN campaign_data cd ON c.id = cd.id_campaign AND cd.atributo = 'licencias'
            LEFT JOIN (
                SELECT id_campaign,  COUNT(id_campaign) AS used_licenses
                FROM campaign_licenses
                WHERE id_campaign  IN ($id_campaigns)
                GROUP BY id_campaign
            ) AS cl ON cl.id_campaign = c.id
            WHERE c.id IN ($id_campaigns)
            HAVING available_licenses > 0
            ORDER BY c.name;");

        return $query->result();
    }

    public function get_horario_min_max($id) {
        $query = $this->db->query("SELECT MIN(inicio) inicio, MAX(fin) fin
                                    FROM campaign_hour ch
                                    WHERE ch.id_campaign=?
                                    ORDER BY ch.dia", array($id));
        return $query->row();

    }
}

?>
