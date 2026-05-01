<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chatinterno_model extends CI_Model
{

    public function getData($data) {
        $pag = (empty($data['pag'])) ? 0  : (int)$data['pag'];
        $rpp = (empty($data['rpp'])) ? 20 : (int)$data['rpp'];
        $bus = "";

        if ( !empty($data['bus']) ) {
            $bus = "AND CONCAT(u.name, ' ', u.last) like '%".$data['bus']."%'";
        }

        $campanas = $this->datos_model->getCampanas(false);
        $query = $this->db->query("SELECT DISTINCT u.id,
            CONCAT(u.name, ' ', u.last) AS nombre, ud1.val AS perfil,
            COALESCE(IF(ud2.val='','0,0,0,0,0',ud2.val),'0,0,0,0,0') AS permisos,
            ud3.val AS campanas
            FROM user u
            LEFT JOIN user_data ud1 ON ud1.id_user = u.id
            LEFT JOIN user_data ud2 ON ud2.id_user = u.id
            LEFT JOIN user_data ud3 ON ud3.id_user = u.id
            LEFT JOIN catalogs c1 ON c1.id = ud1.id_catalog
            LEFT JOIN catalogs c2 ON c2.id = ud2.id_catalog
            LEFT JOIN catalogs c3 ON c3.id = ud3.id_catalog
            WHERE u.id>1 AND u.active=1
            AND c1.cat = 'userData' AND c1.val = 'perfil'
            AND c2.cat = 'userData' AND c2.val = 'chatinterno'
            AND c3.cat = 'userData' AND c3.val = 'campanas'
            $bus
            ORDER by perfil, u.name, u.last");
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
        $query = $this->db->query("SELECT id FROM catalogs c WHERE c.cat = 'userData' AND c.val = 'chatinterno';");
        if( $query->num_rows() > 0 ){
            $id_calog = $query->row()->id;
            foreach ($data_update as $row){
                $id_user = $row['id_user'];
                $val     = $row['val'];
                if( !$this->db->query("UPDATE user_data SET  val=? WHERE id_user = ? AND id_catalog = ?;", [$val, $id_user, $id_calog]) )
                    return ["error" => "Ocurrio un error al intentar actualizar uno de los valores."];
            }
        }
        else{
            return ["error" => "Error: no se encontro un catalogo valido para los permisos"];
        }


        return 'Datos actualizados correctamente.';
    }
}

?>
