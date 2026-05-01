<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agenda_model extends CI_Model
{

    public function get_all() {
        $were = "";
        $vals = [];
        if ($this->udata['perfil']!='admin') {
            $cams = explode(",", $this->udata['campanas']);
            $were = "WHERE (c.id_user is null or c.id_user = ?) AND
                (c.id_campaign is null or c.id_campaign in ?)";
            $vals = [$this->udata['id'], $cams];
        }
        $query = $this->db->query("SELECT c.id, coalesce(concat(u.name, ' ', u.last), 'Pública') Propiedad, ca.name AS campaign, c.name, c.last, c.phone, c.email, c.active, c.available, c.calle, c.numero, c.interior, 
        c.colonia, c.dele_muni, c.ciudad, c.cp, c.pais, c.facebook, c.twitter, c.linkedin
            from client c left join user u on u.id = c.id_user
            left join campaign ca on ca.id = c.id_campaign
            $were order by c.name, c.last", $vals);
        $data["campos"] = $query->list_fields();
        $data["data"] = $query->result_array();

        return $data;
    }

    public function get_list($data) {
        $were = "WHERE (c.id_user is null or c.id_user='".$this->udata['id']."') AND
            (c.id_campaign is null or c.id_campaign in (".$this->udata['campanas']."))";
        if ($this->udata['perfil']=='superior') {
            $were = "WHERE c.id_campaign is null or c.id_campaign in (".$this->udata['campanas'].")";
        } else if ($this->udata['perfil']=='admin') {
            $were = "";
        }
        $query = $this->db->query("SELECT c.id, coalesce(concat(u.name,' ',u.last), 'Pública') Agenda,
            c.name, c.last, c.phone, c.email, c.active, c.available
            from client c left join user u on u.id = c.id_user
            $were
            order by c.id_user, c.name, c.last limit ".$data['page'].", ".REGS_POR_PAG);
        $data['data'] = $query->result();
        $data['campos'] = $query->list_fields();
        return $data;
    }

    public function crear($data) {
        $campos  = "`id`, ";
        $valmark = "?, ";
        $valores = array('0');
        foreach ($data as $key => $value) {
            if ($key=="id_user" and $value=='') {
                $campos .= "`".$key."`, ";
                $valmark .= "NULL, ";
            } else if ($key!="id") {
                $campos .= "`".$key."`, ";
                $valmark .= "?, ";
                $valores[] = $value;
            }
        }
        $campos = rtrim($campos, ", ");
        $valmark = rtrim($valmark, ", ");
        $this->db->query("INSERT into client ($campos) values ($valmark)", $valores);
        return $this->db->insert_id();
    }

    public function actualizar($data) {
        $elset = "";
        $query = $this->db->query("SELECT * from client where id = ?", [$data['id']]);
        if ($reg = $query->row()) {
            foreach ($data as $key => $value) {
                if ($key=="id_user") {
                    // En caso de querer modificar la propiedad de un registro de agenda
                    if( in_array($this->udata['perfil'], ['admin', 'superior', 'supervisor']) ) { // Permisos avanzados
                        if ($value == '0' || $value == '') {
                            $elset .= "`id_user`=NULL, ";
                        } else {
                            $elset .= "`".$key."`='".$value."', ";
                        }
                    } else { // Permisos Agente/CRM
                        if( $reg->id_user == NULL ) {
                            // Un agente o un crm intentando cambiar agenda pública a privada!
                            // Nada, como decirle al códito que no haga nada? jajaja
                        } else {
                            // El agente o crm si podran cambiar la propiedad solo si es la suya
                            if( $reg->id_user == $this->udata['id'] ) {
                                // Y solo la podrian hacer publica por que ya les pertenece al usuario
                                if ($value == '0' || $value == '') {
                                    $elset .= "`id_user`=NULL, ";
                                }
                            }
                        }
                    }
                } else if ($key=="id_campaign" && $value=='') {
                    // Otro no hacer nada
                } else if ($key!="id") {
                    $elset .= "`".$key."`='".$value."', ";
                }
            }
            $elset = rtrim($elset, ", ");
            return $this->db->query("UPDATE client set $elset where id = ?", array($data['id']));
        } else {
            return false;
        }
    }

    public function borrar($data) {
        return $this->db->query("DELETE from client where id = ?", array($data['id']));
    }

    public function regxid($id) {
        $query = $this->db->query("SELECT * from client where id=?", array($id));
        return $query->row();
    }

    public function traerporid($id) {
        $query = $this->db->query("SELECT * from client where id=?", array($id));
        $pre = $query->row_array();
        $sal = [];
        foreach ($pre as $key => $value) {
            if($key != 'id_user') {
                $sal[traduce($key)] = $value;
            }
        }
        $res['cliente'] = (object)$sal;
        $query = $this->db->query("SELECT * from form where active = '1'");
        $forms = $query->result();
        $res['tics'] = array();
        foreach ($forms as $form) {
            if ($this->db->table_exists('formd_'.$form->id.'_crm')) {
                $query = $this->db->query("SELECT '".$form->id_campaign."' cid, '".$form->id."' fid, '".$form->name."' fname, id from `formd_".$form->id."`
                    where `id_cliente`='".$id."' and estatus != 'Cerrado'");
                $regs = $query->result();
                foreach ($regs as $row) {
                    array_push($res['tics'], $row);
                }
            }
        }
        return $res;
    }

    public function buscarAgenda($data) {
        $pag = (isset($data['pag'])) ? $data['pag'] : 0;
        $rpp = (isset($data['rpp'])) ? $data['rpp'] : REGS_POR_PAG;
        $were = "";
        if (!empty($data['bus'])) {
            $were = "WHERE (c.name like '%".$data['bus']."%' or
            c.last like '%".$data['bus']."%' or
            c.phone like '%".$data['bus']."%' or
            c.email like '%".$data['bus']."%')";
        }
        if (empty($were)) {
            $maswere = "WHERE (c.id_user is null or c.id_user='".$this->udata['id']."') AND
            (c.id_campaign is null or c.id_campaign in (".$this->udata['campanas']."))";
        } else {
            $maswere = $were." AND (c.id_user is null or c.id_user='".$this->udata['id']."') AND
            (c.id_campaign is null or c.id_campaign in (".$this->udata['campanas']."))";
        }
        if ($this->udata['perfil']=='superior' || $this->udata['perfil']=='supervisor') {
            $maswere = (empty($were)) ? "WHERE c.id_campaign is null or c.id_campaign in (".$this->udata['campanas'].")" : $were." AND (c.id_campaign is null or c.id_campaign in (".$this->udata['campanas']."))";
        } else if ($this->udata['perfil']=='admin') {
            $maswere = (empty($were)) ? "" : $were;
        }
        //Filtramos los clientes activos siempre que resivamos por data la variable "client_active" con el valor 1
        if( isset($data['client_active']) && $data['client_active'] == 1 )
            $maswere = $maswere." AND c.active = 1";
        $query = $this->db->query("SELECT count(id) AS reg from client c $maswere");
        $data['reg'] = $query->row()->reg;
        $query = $this->db->query("SELECT c.*, coalesce(concat(u.name,' ',u.last), 'Pública') agenda, coalesce(cam.name, 'Todas') campana
            from client c left join user u on u.id = c.id_user left join campaign cam on cam.id = c.id_campaign
            $maswere order by u.name, cam.name, c.name limit $pag, $rpp");
        if ($query->num_rows()>0) {
            $data["data"]   = $query->result();
        } else {
            $data['error'] = "No se encontraron datos";
        }

        return $data;
    }

    public function getCamposAgenda() {
        $campos = $this->db->list_fields('client');

        return $campos;
    }

    public function guardadoMasivo($rows) {
        $n = 0;
        $indicador = '';
        $this->db->trans_start();
        foreach( $rows as $row ) {
            $n++;
            if ( !$this->db->insert('client', $row) ) {
		        $indicador = "Renglon $n del con nombre ".$row['name'];
                break;
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return [ 'error'=> "Error: No se pudo completar el guardado, por favor revisa la informacion ".$indicador];
        } else {
            return "$n Registro(s) agregados correctamente";
        }
    }

}
