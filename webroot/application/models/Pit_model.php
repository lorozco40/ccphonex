<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pit_model extends CI_Model
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

    public function get_one($data=""){
        if ( is_array($data) ) {
            $query = [];
            if ( !empty($data["pin"]) ) $query = $this->db->query("SELECT * from pit_catalog where pin='".$data["pin"]."'");
            else if ( !empty($data["id"]) ) $query = $this->db->query("SELECT * from pit_catalog where id='".$data["id"]."'");

            $pit = $query->row();

            if( $query->num_rows() > 0 && $pit->active == "0" ) return array("success" => false, "action" => "danger", "msg" => "El contacto no está vigente.");
            if( $query->num_rows() == 0 ) return array("success" => false, "action" => "danger", "msg" => "No se encontró al contacto.");

            return ["success"=>true, "data"=>$pit];
        } else {
            $query = $this->db->query("SELECT * from pit_catalog ORDER BY name");
            return $query->result();
        }

    }

    public function buscar_pin_nombre() {
        $search = $this->input->post('pin');
        $id = $this->input->post('id');

        /*busqueda por ID*/
        if( $id > 0 ) {
            $query = $this->db->query("SELECT id, id_user, pin, phone, aviso, active, motivo, CONCAT(name,' ',COALESCE(last, '')) name FROM pit_catalog WHERE id=? LIMIT 1; ", $id );
            $n = $query->num_rows();
            if( $n == 0 ) { //NO SE ENCONTRO NINGUNO
                return array("success" => false, "msg" => "No se encontró al contacto.");
            } else {
                $row = $query->row();

                if( $row->active == 0 ) {
                    return ['success' => false, "msg" => 'No activo: '.$row->motivo];
                }
                $data = $row;
                //Verificamos is hay redireccionamiento
                $redireccion = $this->redireccionamiento_phone($row->id);
                if( $redireccion->redirect == 'SI' ) {
                    $data->name = 'Redireccionado a:'.$redireccion->pin.' '.$redireccion->name;
                }

                return array("success"=>true, "data"=>$data);
            }
        }
        /*busqueda por PIN o Nombre*/
        $search = trim($search);
        $PIN = (int)$search;
        $table = [];
        if ( $search == '' )
            return array("success" => false, "msg" => "Error: Debes escribir algo.");

        if( $search == $PIN."" ) {//EL TEXTO A BUSCAR ES NUMERICO, SE ENTIENDE QUE SE ESTA BUSCANDO POR CLAVE
            $query = $this->db->query("SELECT * FROM pit_catalog WHERE pin=?; ", $search );
            $n = $query->num_rows();
            if( $n == 0 ) { //NO SE ENCONTRO NINGUNO
                return array("success" => false, "msg" => "No se encontró al contacto.");
            } else if( $n == 1 ) {//ENCONTRAMOS 1
                $row = $query->row();
                if( $row->active == 0 ) {
                    return ['success' => false, "msg" => 'No activo: '.$row->motivo];
                }
                else {//EL USUARIO ESTA ACTIVO, VALIDAMOS SI TIENE REDIRECCIONAMIENTO
                    $data = $row;
                    $redireccion = $this->redireccionamiento_phone($row->id);
                    if( $redireccion->redirect == 'SI' ) {
                        $data->name = 'Redireccionado a:'.$redireccion->pin.' '.$redireccion->name;
                    }
                    return array( "success"=>true, "data"=>$data );
                }
            } else {//NOS TRAE MAS DE UNO VIGENTE, PINTAREMOS UNA TABLA
                //Listamos los registros
                $data = [];
                foreach( $query->result() as $row ){
                    $class = $row->active == 1 ? 'primary' : 'danger';
                    $data[] = [
                        'id'    => $row->id,
                        'pin'   => $row->pin,
                        'name'  => $row->name,
                        'class' => $class,
                        'bus_id' => 1,
                    ];
                }
                return array("success"=>'tabla', "data"=>$data);
            }
        } else { //LA BUSQUEDA SE HACE POR NOMBRE
            if( strlen($search) < 3 ) return array("success" => false, "msg" => "Debes de escribir al menos 3 caracteres para hacer una busqueda por nombre.");

            $like = "%$search%";
            $query = $this->db->query("SELECT * FROM pit_catalog WHERE name LIKE ? ORDER BY name LIMIT 10; ", $like );
            $n = $query->num_rows();
            if( $n == 0 ) return array("success" => false, "msg" => "No se encontró ningun contacto que contenga '$search'.");
            //Listamos los registros
            $data = [];
            foreach( $query->result() as $row ){
                $class = $row->active == 1 ? 'primary' : 'danger';
                $data[] = [
                    'id'    => $row->id,
                    'pin'   => $row->pin,
                    'name'  => $row->name,
                    'class' => $class,
                    'bus_id' => 0,
                ];
            }
            return [
                "success"   => 'tabla',
                "data"      => $data
            ];
        }

    }

    public function pit_detalle($data) {
        $plus_select = "pc.pin Clave, CONCAT(COALESCE(pc.name, ''),' ',COALESCE(pc.last, '')) Receptor, ";
        $where_clave_nombre = '';
        $where = '';
        $clave_nombre = $this->input->post('clave_nombre');
        if(!empty($data['paraexcel'])) // Es exportacion a excel
            $plus_select = '';
        if( $this->udata['perfil'] == 'crm' ) {
            $where = ' AND pc.id_user = '.$this->udata['id'].' ';
        }
        if( !empty($clave_nombre) )
            $where_clave_nombre = "AND (pc.pin LIKE '%$clave_nombre%' OR pc.name LIKE '%$clave_nombre%' OR pc.last LIKE '%$clave_nombre%')";
        $data['prequery'] = "SELECT IFNULL( date_format(pe.datetime_init, '$this->dtfor'), '') Enviado,
            COALESCE(CONCAT(u.name,' ',u.last),'') Agente,
            pe.msg Mensaje,
            $plus_select
            CASE
                WHEN pe.status_desc = 'DELIVRD' THEN 'Envio Exitoso'
                ELSE pe.status_desc
            END AS estatus,
            redirected Redireccionado
            FROM pit_entry pe
            LEFT JOIN pit_catalog pc ON pc.id = pe.id_pit_catalog
            LEFT JOIN user u ON u.id = pe.id_user
            WHERE DATE(pe.datetime_init) BETWEEN '$data[min]' AND '$data[max]' $where $where_clave_nombre
            ORDER BY pe.id DESC";

        return $this->datos_model->manejadorqueries($data);
    }

    public function plantilla_crear($data) {
        $id_campana = $data["id_campaign"];
        $valor = $data["valor"];
        $name  = $data["name"];
        if ($this->db->query("INSERT INTO pit_template (id_campaign, `name`, valor) values (?,?,?)", [$id_campana, $name, $valor] )) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function plantilla_actualizar($data) {
        if( empty($data["name"]) ){// VIENE DE LA CONSOLA
            if ($this->db->query("UPDATE pit_template SET valor=? WHERE id=?", [$data['valor'], $data['id']] )) {
                return true;
            }
        }else{// VIENE DEL MENU PIT/PLANTILLA
            if ($this->db->query("UPDATE pit_template SET `name`=?, valor=?, id_campaign=? WHERE id=?", [$data['name'], $data['valor'], $data['id_campaign'], $data['id']] )) {
                return true;
            }
        }
        return false;
    }

    public function plantilla_borrar($data) {
        if ($this->db->query("DELETE FROM pit_template WHERE id=?", array($data))) {
            return true;
        }
        return false;
    }

    public function finalizar($p) {
        $sql = 'INSERT INTO pit_entry (id_user, id_pit_catalog, msg, resp, uid, status, status_desc, redirected) VALUES(?,?,?,?,?,?,?,?);';
        $this->db->query($sql, [ $p->id_user, $p->id_pit_catalog, $p->msg, $p->resp, $p->uid, $p->status, $p->status_desc, $p->redirected ]);

        return $this->db->insert_id();
    }

    public function actualiza($tabla = 'pit_entry') {
        $query = $this->db->query("SELECT *, date(datetime_init) fecha from $tabla
            where uid<>'' and json='' and datetime_init < now() - interval 2 minute"); // limit !?
        $res = ($query) ? $query->result() : [];
        foreach ($res as $row) {
            $sms = $this->datos_model->getParams('sms');
            $dir = 'https://smsrp.directo.com/rest/edr?message_id='.$row->uid.
            '&start_date='.$row->fecha.'_00:00:00'.
              '&end_date='.$row->fecha.'_23:59:59';
            $headers = array(
                'Authorization: Bearer '.$sms->token,
                'Content-Type: application/x-www-form-urlencoded'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $dir);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $data = curl_exec($ch);
            $ddata = json_decode($data);
            $ddata = $ddata[0];
            if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
                $ddata->edr_status = "Mensaje viejo";
                $ddata->operator = "";
            }
            curl_close($ch);

            $query = $this->db->query("UPDATE $tabla set `json`='".$data.
                "', `status`='".$ddata->reason_code.
                "', `status_desc`='".$ddata->edr_status.
                "', `operator`='' where id='".$row->id."'");
        }

        return " $tabla Actualizados: " . $query->num_rows();
    }

    public function sms_detalle($data) {
        $data['prequery'] = "SELECT CONCAT(u.name,' ',u.last) nombre, s.phone,
            concat(substring(s.msg, 1, 40), ' ... ') msg,
            date_format(s.datetime_init, '$this->dtfor') datetime_init, s.resp
            FROM sms_entry s INNER JOIN user u on u.id = s.id_user
            WHERE DATE(s.datetime_init) BETWEEN '$data[min]' AND '$data[max]'
            AND s.id_user IN ($data[agente])";

        $data = $this->datos_model->manejadorqueries($data);
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]->resp = (json_decode($value->resp)->resp=="200") ? "Enviado" : "No enviado";
        }
        return $data;
    }

    public function sms_indicadores($data) {
        $data['prequery'] = "SELECT date_format(se.datetime_init, '$this->dfor') fecha, CONCAT(u.name, ' ',u.last) nombre,
            coalesce(sum(if(se.type='Saliente',1 ,0)),0) enviados,
            coalesce(sum(if(se.type='Entrante',1,0)),0) recibidos,
            coalesce(sum(if(se.type='Saliente' and se.status='200',1 ,0)),0) env_exito
            FROM sms_entry se LEFT JOIN user u on u.id = se.id_user
            WHERE DATE(se.datetime_init) BETWEEN '$data[min]' AND '$data[max]'
            AND se.id_user in ($data[agente]) GROUP BY fecha, nombre";

        return $this->datos_model->manejadorqueries($data);
    }

    public function get_camps($data) {
        $prequery = "SELECT `camp`, count(`id`) regs,
            coalesce(sum(if(length(resp)>0, 1, 0)), 0) sent,
            coalesce(sum(if(length(resp)=0, 1, 0)), 0) tose,
            coalesce(sum(if(status_desc='Mensaje Enviado', 1, 0)), 0) menExi,
            coalesce(sum(if(status_desc='Envio Exitoso', 1, 0)), 0) senExi,
            coalesce(sum(if(status_desc='Destino invalido', 1, 0)), 0) desInv,
            coalesce(sum(if(status_desc='Error', 1, 0)), 0) otroError,
            coalesce(sum(if(status_desc='Error Interno', 1, 0)), 0) errorInt
            from sms_campaign
            group by camp";
        $query = $this->db->query($prequery);
        $data['cuenta'] = $query->num_rows();
        $query = $this->db->query($prequery. " limit ".$data['page'].", ".REGS_POR_PAG);
        $data['data'] = $query->result();

        return $data;
    }

    public function cargar_csv($csv, $form) {
        $this->csvreader->auto($csv);
        $colus = ["telefono", "nombre", "dato", "saludo", "mensaje", "cierre"];
        $buenas = 0;
        $colsencsv = "";
        foreach ($this->csvreader->titles as $key => $row) {
            $colsencsv .= " ".$row;
            if (in_array($row, $colus)) {
                $buenas++;
            } else {
                $buenas--;
            }
        }
        if ($buenas == 6) {
            // Ni más ni menos columnas que las requeridas
            $regs = $totregs = 0;
            $query = "INSERT into sms_campaign (`camp`, `phone`, `msg`) values ";
            foreach ($this->csvreader->data as $key => $row) {
                if (!empty($row['telefono']) && strlen($row['telefono']) == 10) {
                    $fila = array_values($row);
                    $msg = (empty($row['saludo'])) ? $form['saludo'] : $row['saludo'];
                    $msg .= (!empty($msg)) ? ' ' : '';
                    $msg .= (!empty($row['nombre'])) ? $row['nombre'] : '';
                    $msg .= (!empty($msg)) ? ' ' : '';
                    $msg .= (empty($row['mensaje'])) ? $form['msg'] : $row['mensaje'];
                    $msg .= (!empty($row['dato'])) ? ' '.$row['dato'] : '';
                    $msg .= (empty($row['cerre'])) ? (empty($form['cierre'])) ? '' : ' '.$form['cierre'] : ' '.$row['cierre'];
                    $query .= "('".$form['camp']."', '".$row['telefono']."', '".$msg."'),";
                    $regs++;
                    $totregs++;
                    if ($regs==500) {
                        $query = rtrim($query, ",");
                        $this->db->query($query);
                        $regs = 0;
                        $query = "INSERT into sms_campaign (`camp`, `phone`, `msg`) values ";
                    }
                }
            }
            $query = rtrim($query, ",");
            $this->db->query($query);
            if ($totregs>0) {
                $respu["status"] = "ok";
            } else {
                $respu["status"] = "error";
                $respu["msg"] = "Ningún registro valido agregado. No se crea campaña.";
            }
        } else {
            $respu["status"] = "error";
            $respu["msg"] = "Estructura incorrecta de archivo, validar 6 columnas.<br> en el archivo: ".$colsencsv;
        }

        return $respu;
    }

    public function camp_enviar($data) {
        $query = $this->db->query("SELECT * from sms_campaign where camp = ? AND resp = ''", array($data['camp']));
        if ($query->num_rows()>0){
            $sms = $this->datos_model->getParams('sms');
            $data = [
                "user"     => $sms->user,
                "password" => $sms->pass,
            ];
            foreach ($query->result() as $key => $row) {
                $data["number"]   = $row->phone;
                $data["message"] = $row->msg;
                $res = doReq(["url"=>$sms->server, "method"=>"POST", "body"=>$data, 'proto'=>'x-www-form-urlencoded']);
                $json = json_decode($res["data"]);
                $this->db->query("UPDATE sms_campaign
                    SET datetime_init=now(), resp='1', uid=?, status=?, status_desc=?
                    WHERE id='$row->id'", array($json->UID, $json->resp, $json->description)
                );
            }
            return array("status"=>"ok");
        }
        return array("status"=>"error", "msg"=>"No hay registros por enviar en la campaña.");
    }

    public function traerdata($post = []) {
        $pag = (isset($post['pag'])) ? $post['pag'] : 0;
        $rpp = (isset($post['rpp'])) ? $post['rpp'] : REGS_POR_PAG;
        $where = "";
        if (!empty($post['bus'])) {
            $where = "WHERE (pc.name like '%".$post['bus']."%' or
            pc.last like '%".$post['bus']."%' or
            pc.phone like '%".$post['bus']."%' or
            pc.pin like '%".$post['bus']."%')";
        }

        $query = $this->db->query("SELECT count(id) AS reg FROM pit_catalog pc $where");
        $data['reg'] = $query->row()->reg;
        $query = $this->db->query("SELECT pc.id, pc.id_user, CONCAT(pc.name,' ',COALESCE(pc.last, '')) nombre, pc.phone, pc.pin, pc.active FROM pit_catalog pc $where limit $pag, $rpp;");
        if ($query->num_rows()>0) {
            $data["data"]   = $query->result();
        }

        return $data;
    }

    public function edit($id = 0){
        $query = $this->db->query("SELECT pc.*, pr.id AS redi_id, pcr.pin AS redi_pin, CONCAT(pcr.name, ' ', COALESCE(pcr.last, '')) AS redi_nombre,
        pr.vigencia AS redi_vigencia, date_format(pr.vigencia_hora, '%H:%i:00') AS redi_vigencia_hora
        FROM pit_catalog pc
        LEFT JOIN pit_redirect pr ON pc.id = pr.id_pit_catalog
        LEFT JOIN pit_catalog pcr ON pr.id_pit_catalog_redirect = pcr.id
        WHERE pc.id=?;", $id);
        if( $query->num_rows() == 0 )
            return array("error" => "No se encontró ningun registro.");
        else {
            return $query->row();
        }
    }

    public function guardar(){
        $active_aux = $this->input->post('active');
        $id     = $this->input->post('id');
        $name   = $this->input->post('name');
        $last   = $this->input->post('last');
        $phone  = $this->input->post('phone');
        $pin    = $this->input->post('pin');
        $motivo = $this->input->post('motivo');
        $aviso  = $this->input->post('aviso');
        $aviso  = ( empty($aviso) ) ? null : $aviso;

        $id_pit_catalog = $this->input->post('id');
        $id_pit_catalog_redirect = (int) $this->input->post('id_pit_catalog_redirect');
        $vigencia = $this->input->post('vigencia');
        $vigencia_hora = $this->input->post('vigencia_hora');
        if( !empty($vigencia_hora) ) {
            $vigencia_hora[5] = ':';
            $vigencia_hora[6] = '5';
            $vigencia_hora[7] = '9';
        }

        $active = $active_aux == 1 ? true : false;
        if( $active )
            $motivo = null;

        if( $id == 0 ){
            $created_by = $this->udata['id'];
            $created_when = date("Y-m-d H:i:s");
            $sql = 'INSERT INTO pit_catalog (name, last, phone, pin, aviso, active, motivo, created_by, created_when) values (?,?,?,?,?,?,?,?,?);';
            if( !$this->db->query($sql,[$name, $last, $phone, $pin, $aviso, $active, $motivo, $created_by, $created_when]) ) {
                return ['error' => 'Error: No se pudo agregar el registro.' ];
            } else {
                return "El registro se agrego correctamente";
            }
        } else {
            $this->db->trans_start();
            $this->db->query("UPDATE pit_catalog SET name=?, last=?, phone=?, pin=?, aviso=?, active=?, motivo=? WHERE id=?;",[$name, $last, $phone, $pin, $aviso, $active, $motivo, $id]);
            if ( $id_pit_catalog_redirect > 0  )
                $this->db->query("INSERT INTO pit_redirect (id_pit_catalog, id_pit_catalog_redirect, vigencia, vigencia_hora) VALUES (?,?,?,?);",[$id_pit_catalog, $id_pit_catalog_redirect, $vigencia, $vigencia_hora]);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE){
                return ['error' => 'Error: No se pudo actualizar la informacion.' ];
            } else {
                return "El registro se actualizo correctamente";
            }
        }
    }

    public function mpc_guardar(){
        $id     = $this->input->post('mpc_id');
        $phone  = $this->input->post('mpc_phone');
        $aviso  = $this->input->post('mpc_aviso');
        $aviso  = ( empty($aviso) ) ? null : $aviso;

        $id_pit_catalog = $id;
        $id_pit_catalog_redirect = (int) $this->input->post('id_pit_catalog_redirect');
        $vigencia = $this->input->post('vigencia');
        $vigencia_hora = $this->input->post('vigencia_hora');
        if( !empty($vigencia_hora) ) {
            $vigencia_hora[5] = ':';
            $vigencia_hora[6] = '5';
            $vigencia_hora[7] = '9';
        }
        $this->db->trans_start();
        $this->db->query("UPDATE pit_catalog SET phone=?, aviso=? WHERE id=?;",[$phone, $aviso, $id]);
        if ( $id_pit_catalog_redirect > 0  )
            $this->db->query("INSERT INTO pit_redirect (id_pit_catalog, id_pit_catalog_redirect, vigencia, vigencia_hora) VALUES (?,?,?,?);",[$id_pit_catalog, $id_pit_catalog_redirect, $vigencia, $vigencia_hora]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            return ['error' => 'Error: No se pudo actualizar la informacion.' ];
        } else {
            return "El registro se actualizo correctamente";
        }
    }

    public function eliminar($id = 0) {
        if( $query = $this->db->query("DELETE FROM pit_catalog WHERE id = ?;", [$id]) )
            return "Registro eliminado correctamente";
        else
            return ['error'=> 'Error: no se puede eliminar este registro.'];
    }

    public function guardadoMasivo($rows) {
        $n = 0;
        $indicador = '';
        $this->db->trans_start();
        foreach( $rows as $row ) {
            $n++;
            if ( !$this->db->insert('pit_catalog', $row) ) {
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

    //obtiene todos los usuarios por perfil porporcionado "Aqui ira el nuevo perfil de los usuarios PIT"
    public function usuarios_pit($perfil = 'crm') {
        $sql = "SELECT u.id, concat(u.name,' ',u.last) name, ud2.val as campanas
        from user u
        left join user_data ud ON ud.id_user = u.id
        left join user_data ud2 ON ud2.id_user = u.id
        left join catalogs c   ON c.id = ud.id_catalog
        left join catalogs c2 ON c2.id = ud2.id_catalog
        where c.cat = 'userData' AND c.val = 'perfil'
        AND c2.cat = 'userData'  AND c2.val = 'campanas'
        AND ud.val = ?
        AND u.active = 1
        ORDER BY u.name ASC, u.last ASC;";
        $query = $this->db->query($sql,[$perfil]);

        return $query->result();
    }

    public function actualizar_usuario() {
        $id_user = (int) $this->input->post('id_user');
        $id      = (int) $this->input->post('id');
        if( $id_user == 0 )
            $id_user = null;
        $sql = "UPDATE pit_catalog SET id_user=? WHERE id=?;";

        if( $this->db->query($sql, [$id_user, $id]) )
            return 'Registro actualizado correctamente';
        else
            return ['error' => 'Error: No se pudo actualizar el usuario.'];
    }

    public function eliminar_redirect($id = 0) {
        if( $this->db->query("DELETE FROM pit_redirect WHERE id = ?;", [$id]) )
            return "Redireccionamiento eliminado correctamente.";
        else
            return ['error'=> 'Error: no se puede eliminar el redireccionado.'];
    }

    public function redireccionamiento_phone($id_pit_catalog = 0) {
        $query = $this->db->query("SELECT count(*) AS n, pcr.phone, IF(pr.id IS NULL, 'NO', 'SI') AS redirect, CONCAT(pcr.name,' ',COALESCE(pcr.last, '')) name, pcr.pin
        FROM pit_catalog pc
        LEFT JOIN pit_redirect pr ON pc.id = pr.id_pit_catalog AND pr.vigencia >= DATE_FORMAT(NOW( ), '%Y-%m-%d' )
        LEFT JOIN pit_catalog pcr ON pr.id_pit_catalog_redirect = pcr.id
        WHERE pc.id=?", $id_pit_catalog);

        return $query->row();
    }

    //Elimina todos los redireccionamientos del modulo PIT que ya caducaron
    public function delete_old_redirect() {
        $this->db->query("DELETE FROM pit_redirect WHERE vigencia < CURDATE() OR (vigencia = CURDATE() AND vigencia_hora < CURTIME());");
        return $this->db->affected_rows();
    }

    public function buscarPlantilla($data) {
        if( empty($data["id_campaign"]) ){
            $query = $this->db->query('SELECT * FROM pit_template WHERE id = ?',[ $data['id'] ]);
            return $query->row();
        }else{
            $query = $this->db->query('SELECT * FROM pit_template WHERE id_campaign = ?',[ $data['id_campaign'] ]);
            return $query->result();
        }
    }


}

?>
