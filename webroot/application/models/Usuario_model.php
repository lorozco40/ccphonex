<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model
{

    public function valida_usuario($email, $password) {
        $query = $this->db->query("SELECT uf.*, u.pass
            FROM user_full uf JOIN user u ON u.id = uf.id
            WHERE u.user=? and u.active='1'", array($email));

        if($query->num_rows() == 1) {
            $res = $query->row();
            $this->load->model("campanas_model");
            if ($res->id > 1 && $res->perfil != 'admin') {
                // Usuario no admin
                if (empty($res->campanas)) {
                    return ['error'=>'Sin campañas asignadas.'];
                }
                $query = $this->db->query("SELECT c.id, c.name, ifnull(cd.valor,0) licenses,
                        ifnull(ul.used_licenses, 0) used_licenses,
                        (ifnull(cd.valor, 0) - ifnull(ul.used_licenses, 0)) available_licenses
                    FROM campaign c
                    LEFT JOIN campaign_data cd ON c.id = cd.id_campaign AND cd.atributo = 'licencias'
                    LEFT JOIN (SELECT id_campaign, COUNT(id_campaign) used_licenses
                    FROM campaign_licenses GROUP BY id_campaign) ul ON ul.id_campaign = c.id
                    WHERE c.id IN (" . $res->campanas . ")
                    HAVING available_licenses < 1");
                if($query->num_rows()>0) {
                    return ['error'=>'Límite de usuarios simultáneos alcanzado.'];
                }
            }
            $lapas = "";
            if (function_exists("mcrypt_encrypt")) {
                $this->load->library('encrypt');
                $lapas = $this->encrypt->decode($res->pass);
            }
            $opas = encuentra($res->pass, $res->email);
            if ($lapas == $password || $opas == $password) {
                // Eliminar licencias que esten en uso por el usuario
                $this->db->delete('campaign_licenses', array('id_user' => $res->id));
                // Cerrar todas las sesiones abiertas por el usuario
                $this->db->query("DELETE from ses_ab where uid=?", array($res->id));
                $this->db->query("UPDATE user_trans_opts set busy = '' WHERE busy not in (SELECT DISTINCT(uid) FROM ses_ab WHERE uid <> '0')");
                if ($res->id != 1) {
                    $cids = explode(',',$res->campanas);
                    $this->campanas_model->usar_licencias_campana($res->id, $cids);
                }
                if (empty($res->token)) {
                    $token = $this->savetoken($res->id);
                    $res->token = $token;
                }
                $query = $this->db->query("SELECT id_user FROM user_trans WHERE id_user = ?", array($res->id));
                $res->inter = ($query->num_rows() > 0) ? true : false;
                return $res;
            } else {
                return ['error'=>'Datos incorrectos, favor de verificar.'];
            }
        }

        return ['error'=>'Datos incorrectos, favor de verificar.'];
    }

    public function checkautolog($uid, $token) {
        $query   = $this->db->query("SELECT count(*) as can from ses_ab where uid>1");
        $data    = $query->row();
        $res     = doReq(["url"=>getenv('BAGO_BURL') . "licencia", 'nossl'=>true]);
        $lic     = bagoLicenciaDecode($res);
        if(!$lic) {
            $lic = (object)['usuarios' => 9999];
        }
        if($data->can >= $lic->usuarios) {
            die("Límite de usuarios simultáneos alcanzado.");
        }
        $udata = $this->datos_model->getBasUdata($uid);
        if ($udata['token'] == $token) return $udata['pagini'];

        return false;
    }

    public function get_by_email($email) {
        $query = $this->db->where('user', $email);
        $query = $this->db->get('user');

        return $query->row();
    }

    public function get_by_id($id_user) {
        $res = false;
        $query = $this->db->query("SELECT * FROM user WHERE id = ?", [$id_user]);
        if ($query->num_rows()==1) {
            $res = $query->row();
            unset($res->pass);
            $query = $this->db->query("SELECT c.val 'eti', ud.val
                FROM user_data ud JOIN catalogs c ON c.id = ud.id_catalog
                WHERE c.cat = 'userData' and ud.id_user = ?", [$id_user]);
            foreach ($query->result() as $key => $row) {
                $res->udata[$row->eti] = $row->val;
            }
        }

        return $res;
    }

    public function guardar_user($data) {
        $pass  = 'N4d4u7171z4873un3rr0r';
        $activ = (empty($data['active'])) ? 0 : 1;
        if (!empty($data['pass'])) {
            if(!preg_match('@[A-Z]@', $data['pass']) || !preg_match('@[a-z]@', $data['pass'])
                || !preg_match('@[0-9]@', $data['pass']) || strlen($data['pass']) < 8) {
                return ['error' => 'Contraseña no segura, no se guardan cambios (Largo >= 8, May, Min, Num).'];
            }
            $pass = esconde($data['pass'], $data['user']);
        }
        if ($data['id']==0) {
            $query = $this->db->query("INSERT into `user` (`user`, `pass`, `name`, `last`, `active`, `created_by`, `created_when`)
                values (?,?,?,?,1,?,now())",
                [$data['user'], $pass, $data['name'], $data['last'], $this->session->userdata('uid')]);
            $uid = $this->db->insert_id();
            if ($query) {
                $this->db->query("INSERT INTO videocall_chans (id_user) values ('$uid')");
                return $uid;
            }
        } else if ($data['id']!=0 && !empty($data['pass'])) {
            $query = $this->db->query("UPDATE `user` set `user`=?, `pass`=?, `name`=?, `last`=?, `active`=? where `id`=?",
                [$data['user'], $pass, $data['name'], $data['last'], $activ, $data['id']]);
            if ($query) return $data['id'];
        } else {
            $query = $this->db->query("UPDATE `user` set `user`=?, `name`=?, `last`=?, `active`=? where `id`=?",
                [$data['user'], $data['name'], $data['last'], $activ, $data['id']]);
            if ($query) return $data['id'];
        }
        return ['error'=>'Error 54223, consulta con soporte técnico.'];
    }

    public function guardar_udata($eti, $uid, $val) {
        $query = $this->db->query("UPDATE user_data ud LEFT JOIN catalogs c on c.id=ud.id_catalog
            set ud.val=? where c.cat=? and c.val=? and ud.id_user=?", [$val, 'userData', $eti, $uid]);
        if (!$query) return ['error'=>'Error 54223, consulta con soporte técnico.'];
        return true;
    }

    public function get_permisos($id_user) {
        $query = $this->db->query("SELECT id, cat, val from catalogs
            where cat = 'permiso' or cat = 'permisoSec' or cat ='permisoRepo'
            or cat ='permisoEsp' or cat ='userData' order by cat, val");
        $cats = $query->result();
        if ($id_user==1) {
            $query = $this->db->query("SELECT id, cat, eti, val data, if(cat='userData','',1) val
                from catalogs
                where cat='permiso' or cat='permisoSec' or cat='permisoRepo'
                or cat ='permisoEsp' or cat='userData'
                order by cat, num_order, eti");
        } else {
            $query = $this->db->query("SELECT cat.id, cat.cat, cat.eti, cat.val data, ud.val
                from user_data ud
                join catalogs cat on cat.id = ud.id_catalog
                where ud.id_user = ? order by cat.cat, cat.num_order, cat.eti", array($id_user));
        }
        $udata = $query->result();
        if (count($cats) != count($udata)) {
            $inserts = "";
            foreach ($cats as $key => $row) {
                $val = $this->defaults($row->cat, $row->val);
                $inserts .= "('".$id_user."', '".$row->id."', '$val'), ";
            }
            $inserts = rtrim($inserts, ", ");
            $this->db->query("INSERT IGNORE into user_data values $inserts");
            $query = $this->db->query("SELECT cat.id, cat.cat, cat.eti, cat.val data, ud.val
                from user_data ud
                join catalogs cat on cat.id = ud.id_catalog
                where ud.id_user = ? order by cat.cat, cat.eti", array($id_user));
            $udata = $query->result();
        }

        return $udata;
    }

    private function defaults($v1,$v2) {
        $test = $v1.$v2;
        switch ($test) {
            case 'permisoSecautoanswer':
            case 'permisoSecauxiliares':
            case 'permisoSeccalendario':
            case 'permisoSecconsola':
            case 'permisoSechome':
            case 'permisoSecmanualusuario':
            case 'permisoSecnav':
                $ret = '1';
                break;
            case 'userDatachatinterno':
                $ret = '0,0,0,0,0';
                break;
            case 'userDatagenero':
                $ret = 'N';
                break;
            case 'userDatapagini':
                $ret = 'consola';
                break;
            case 'userDataperfil':
                $ret = 'agente';
                break;
            case 'userDatapervidllam':
                $ret = '0,0,1,1,1';
                break;

            default:
                $ret = '';
                break;
        }

        return $ret;
    }

    public function get_permiso() {
        $prot = $this->getProtected();
        $pera = (!empty($this->udata['permiso'])) ? $this->udata['permiso'] : [];
        $pera = (!empty($this->udata['permisoRepo'])) ? array_merge($pera, $this->udata['permisoRepo']) : $pera;
        $pera = (!empty($this->udata['permisoEsp'])) ? array_merge($pera, $this->udata['permisoEsp']) : $pera;
        $ruta = $this->udata['ruta'];
        if (in_array($ruta, $prot) && !in_array($ruta, $pera)) {
            $this->session->set_flashdata('errormsg', 'Sin permiso suficiente! '.$ruta);
            redirect();
        }

        return true;
    }

    public function save_permisos() {
        $id_user = $this->input->post('id_user');
        $perm = $this->input->post();
        $query = $this->db->query("SELECT id FROM catalogs WHERE cat='userData' AND val='userask'");
        $idext = $query->row()->id;
        if ($perm[$idext] != '') {
            $query = $this->db->query("SELECT * FROM user_data
                WHERE id_user <> '$id_user' AND id_catalog = '$idext' and val = ?", [$perm[$idext]]);
            if ($query->num_rows()>0) {
                return ['error'=>'Extensión duplicada, favor de verificar.'];
            }
        }
        $valores = [];
        foreach ($perm as $key => $val) {
            if($key!="id_user") {
                $valores[] = "('".$id_user."','".$key."','".trim($val,',')."')";
            }
        }
        $valores = implode(",", $valores);
        $query = $this->db->query("INSERT INTO user_data values $valores
            on duplicate key update val=VALUES(val)");
        if ($query) {
            return true;
        }
        return ['error'=>'Error 54223, consulta con soporte técnico.'];
    }

    public function buscar($texto) {
        if (!empty($texto)) {
            $query = $this->db->query("SELECT u.*, ud.val extension, IFNULL(sa.uid, 0) AS uid
                FROM user u
                LEFT JOIN user_data ud on ud.id_user = u.id
                LEFT JOIN catalogs c on c.id = ud.id_catalog
                LEFT JOIN ses_ab sa on sa.uid = u.id
                where c.val = 'userask'
                AND (u.name like '%".$texto."%' or user like '%".$texto."%' or last like '%".$texto."%' or ud.val like '%".$texto."%')
                order by if(uid!=0,0,1), if(extension = '' or extension is null,1,0), extension, last, name");
            return $query->result();
        }
        return false;
    }

    public function desloguear($id) {
        $query = $this->db->query("SELECT uid from ses_ab where uid = ? limit 1", array($id));
        if ($query->num_rows() >= 1) {
            $this->db->query("DELETE FROM ses_ab WHERE uid = ?", [$id]);
            $this->db->query("UPDATE user_trans_opts set busy = '' WHERE busy not in (SELECT DISTINCT(uid) FROM ses_ab WHERE uid <> '0')");
            $salida = "El Usuario fue desconectado.";
            $this->db->query("INSERT INTO user_log values (0, ?, now(), ?, ?)",
                array($id, 'Logout', $this->session->userdata('uid'))
            );
        } else {
            $salida = array("error"=>"Usuario ya se había desconectado.");
        }

        return $salida;
    }

    private function getProtected() {
        $query = $this->db->query("SELECT val from catalogs where cat='permiso' or cat='permisoRepo' or cat='permisoEsp' order by eti, val");
        $res = $query->result();
        foreach ($res as $row) {
            $ret[] = $row->val;
        }
        return $ret;
    }

    public function getOnline() {
        $query = $this->db->query("SELECT count(*) cuenta from ses_ab where uid > 1");
        $res = $query->row();

        return (int)$res->cuenta;
    }

    public function savetoken($uid) {
        $res = sha1(md5(microtime(true)));
        $query = $this->db->query("SELECT id from catalogs where cat='userData' && eti='Token'");
        $cat = $query->row();
        if ($this->db->query("INSERT into user_data (id_user, id_catalog, val)
            values (?,?,?)
            on duplicate key update val=?", [$uid, $cat->id, $res, $res]
        )) { return $res; }

        return false;
    }

    // funciones para el API Rest

    public function lista($d, $udata) {
        $cams = explode(",", $udata['campanas']);
        $wercams = "AND (";
        $union = "";
        foreach ($cams as $key => $val) {
            $wercams .= $union . "find_in_set('$val', ud.val)";
            $union = " OR ";
        }
        $wercams .= ")";
        $query = $this->db->query("SELECT u.id AS 'ID', u.user AS 'Email', u.name AS 'Nombre',
            u.last AS 'Apellido', ud2.val AS 'Perfil', ud3.val AS 'Genero',
            if(ud4.val = 1, 'mp', 'auxiliar') AS 'Rol'
            FROM user AS u
            INNER JOIN user_data AS ud ON ud.id_user=u.id
            INNER JOIN user_data AS ud2 ON ud2.id_user=u.id
            INNER JOIN user_data AS ud3 ON ud3.id_user=u.id
            INNER JOIN user_data AS ud4 ON ud4.id_user=u.id
            LEFT JOIN catalogs preud ON preud.id = ud.id_catalog
            LEFT JOIN catalogs preud2 ON preud2.id = ud2.id_catalog
            LEFT JOIN catalogs preud3 ON preud3.id = ud3.id_catalog
            LEFT JOIN catalogs preud4 ON preud4.id = ud4.id_catalog
            WHERE u.id > 5 $wercams
            AND preud.cat='userData' AND preud.val = 'campanas'
            AND preud2.cat='userData' AND preud2.val = 'perfil'
            AND preud3.cat='userData' AND preud3.val = 'genero'
            AND preud4.cat='permisoSec' AND preud4.val = 'autoanswer'
            AND ud2.val <> 'admin'
            ORDER BY u.name, u.last");
        $res['cuenta'] = $query->num_rows();
        $res['data'] = $query->result();
        return $res;
    }

    public function setPars($d, $udata, $toset) {
        $tosave = $toset;
        $query = $this->db->query("SELECT ud.val FROM user_data ud JOIN catalogs c
            ON c.id = ud.id_catalog WHERE ud.id_user = ? AND c.cat = 'permisoSec'
            AND c.val = 'autoanswer'", [$d['id']]);
        $rol = ($query->row()->val == 1) ? 'mp' : 'auxiliar';
        $toret = ['ID'=>$toset['id'], 'Email'=>$toset['user'], 'Nombre'=>$toset['name'],
            'Apellido'=>$toset['last'], 'Perfil'=>$toset['udata']['perfil'],
            'Genero'=>$toset['udata']['genero'], 'Rol'=>$rol];
        $guardar = false;
        if (isset($d['perfil'])) {
            if(!in_array(strtolower($d['perfil']), ['agente', 'supervisor', 'superior'])) {
                    return ['success'=>false, 'msg'=>'Intento de cambio prohibido'];
            }
            $this->db->query("UPDATE user_data ud JOIN catalogs c ON c.id = ud.id_catalog
                SET ud.val = ? WHERE ud.id_user = ? AND c.cat = 'userData'
                AND c.val = 'perfil'", [strtolower($d['perfil']), $d['id']]);
            $toret['Perfil'] = $d['perfil'];
        }
        if (isset($d['vcpermisos'])) {
            if(!is_array($d['vcpermisos']) || count($d['vcpermisos']) != 5) {
                return ['success'=>false, 'msg'=>'Formato incorrecto'];
            }
            $this->db->query("UPDATE user_data ud JOIN catalogs c ON c.id = ud.id_catalog
                SET ud.val = ? WHERE ud.id_user = ? AND c.cat = 'userData'
                AND c.val = 'pervidllam'", [$d['vcpermisos'], $d['id']]);
            $toret['VCPermisos'] = $d['vcpermisos'];
        }
        if (isset($d['genero'])) {
            $this->db->query("UPDATE user_data ud JOIN catalogs c ON c.id = ud.id_catalog
                SET ud.val = ? WHERE ud.id_user = ? AND c.cat = 'userData'
                AND c.val = 'genero'", [strtoupper($d['genero']), $d['id']]);
            $toret['Genero'] = $d['genero'];
        }
        if (isset($d['rol'])) {
            $valfin = (strtolower($d['rol']) == 'mp') ? 1 : 0;
            $this->db->query("UPDATE user_data ud JOIN catalogs c ON c.id = ud.id_catalog
                SET ud.val = ? WHERE ud.id_user = ? AND c.cat = 'permisoSec'
                AND c.val = 'autoanswer'", [$valfin, $d['id']]);
            $toret['Rol'] = $d['rol'];
        }
        if (isset($d['st']) && ($d['st'] == 'r' || $d['st'] == 'g')) { // setear token: r => recuperar, g => (re)generar
            if ($d['st']=='g') {
                $this->savetoken($d['id']);
                $toret['Token'] = $res;
            } else {
                $toret['Token'] = $toset['udata']['token'];
            }
        }
        if (isset($d['nombre'])) {
            $tosave['name'] = $toret['Nombre'] = $d['nombre'];
            $guardar = true;
        }
        if (isset($d['apellido'])) {
            $tosave['last'] = $toret['Apellido'] = $d['apellido'];
            $guardar = true;
        }
        if (isset($d['email'])) {
            $tosave['user'] = $toret['Email'] = $d['email'];
            $guardar = true;
        }
        if (isset($d['activo']) && ($d['activo'] == '0' || $d['activo'] == '1')) {
            $tosave['active'] = $toret['Activo'] = $d['activo'];
            $guardar = true;
        }
        if (isset($d['contra'])) {
            $tosave['pass'] = $d['contra'];
            $toret['Contra'] = 'actulizada';
            $guardar = true;
        }
        if ($guardar) {
            $ret = $this->guardar_user($tosave);
            if (isset($ret['error'])) return $ret;
        }

        return $toret;
    }

    public function status($cams) {
        $query = $this->db->query("SELECT u.id, concat(u.name,' ',u.last) name,
            coalesce(tpe.val,'') perfil,
            coalesce(if(ss.uid, 'online', 'offline'), 'offline') 'log',
            coalesce(ac.evto, '') evento, coalesce(ac.evtime, '') horaev
            from user u
            left join (SELECT user_data.id_user, user_data.val from catalogs
                inner join user_data on user_data.id_catalog = catalogs.id
                where catalogs.cat='userData' and catalogs.val='perfil') tpe
                on tpe.id_user = u.id
            left join (SELECT user_id, if((UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(evento)) < 36000, detalle, '') 'evto',
                if((UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(evento)) < 36000, TIMEDIFF(now(), evento), '') 'evtime'
                from user_log where id in (select max(id) id
                from user_log group by user_id) order by user_id) ac
                on ac.user_id = u.id
            left join ses_ab ss on ss.uid = u.id
            where u.id > 1 and u.active = 1
            having perfil <> 'admin'
            ORDER BY name ASC");
        return $query->result();
    }

    public function getUsuarios() {
        if ($this->udata["perfil"] != "admin") {
            return [];
        }
        $query = $this->db->query("SELECT u.id, u.email, u.name, u.last, u.perfil, u.campanas
            FROM user_full u WHERE u.id > 1 AND u.id <> 9999 ORDER BY u.name, u.last");
        return $query->result();
    }

}

?>
