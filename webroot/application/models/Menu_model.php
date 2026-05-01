<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model
{
    private $abc = ["-","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"];

    public function buscar($data) {
        $pag = (isset($data['pag'])) ? $data['pag'] : 0;
        $rpp = (isset($data['lim'])) ? $data['lim'] : REGS_POR_PAG;
        $bus = (isset($data['bus'])) ? $data['bus'] : '';
        $where = strlen($bus) > 0 ? "WHERE m2.etiqueta LIKE '%".$bus."%' OR m2.permiso LIKE '%".$bus."%' OR m2.nivel LIKE '%".$bus."%' " : "";
        $sql = "SELECT menu.*, ifnull(m2.etiqueta, '') as n2etiqueta FROM menu
            LEFT JOIN menu m2 ON m2.id = menu.pertenece
            $where ORDER BY orden_lista";

        if ( $data['pag'] !== 'x') {
            $queryTot = $this->db->query($sql);
            $data["regs"] = $queryTot->num_rows();
            $sql .= " LIMIT $pag, $rpp ";
        }
        $query = $this->db->query($sql);
        $data["data"] = $query->result();
        
        //SE PUEDE BORRAR ESTA LINEA DE CODIGO PUES CON QUE SE EJECUTE UNA VEZ ES SUFICIENTE PARA ORDENAR EL MENU EXISTENTE, SI NO SE ELIMINA NO PASA NADA MAS QUE ORDENAR EL MENU SEGUN EL CAMPO orden_litsa
        // Cristopher: Ningú proceso innecesario debe correr nunca, son procesos y recursos desperdiciados, y menos como éste que es demasiado pesado.
        // En un sistema profesional, un bit desperdiciado es un error.
        // $this->ordenPorPadre();

        return $data;
    }

    public function guardar($data) {
        $campo = "";
        $valor = "";

        $datos = [];
        $datos[] = $data["etiqueta"];
        $datos[] = $data["submenu"];
        $datos[] = $data["orden"];
        $datos[] = $data["nivel"];

        if( strlen($data["icono"]) > 0 ) {
            $campo .= ", icono ";
            $valor .= ", ? ";
            $datos[] = $data["icono"];
        }

        if( $data["nivel"] > 1 ) { // SI EL NIVEL EL MAYOR A 1, TIENE UN PADRE
            $campo .= ", pertenece ";
            $valor .= ", ? ";
            $datos[] = $data["pertenece"];


            $campo .= ", orden_lista ";
            $valor .= ", ? ";
            $padre = $this->getOne(["id"=>$data["pertenece"]]);
            $datos[] = $padre["data"]->orden_lista.$data["nivel"].$this->abc[$data["orden"]];
        }else{
            $campo .= ", orden_lista ";
            $valor .= ", ? ";
            $datos[] = $data["nivel"].$this->abc[$data["orden"]];
        }

        if( $data["submenu"] == 0 ){// SI NO ES UN SUB MENU, ASIGNALE UN PERMISO
            $campo .= ", permiso ";
            $valor .= ", ? ";
            $datos[] = $data["permiso"];
        }

        $sql = "INSERT INTO menu(etiqueta, submenu, orden, nivel $campo)
                VALUES(?,?,?,? $valor);";
        $query = $this->db->query($sql, $datos);

        if (!$query) return ['error'=>'Ocurrió un error al guardar los datos.'];

        return true;
    }

    public function actualizar($data) {
        $set = "";

        $datos = [];
        $datos[] = $data["etiqueta"];
        $datos[] = $data["submenu"];
        $datos[] = $data["orden"];
        $datos[] = $data["nivel"];

        $set .= ", active = ? ";
        $datos[] = !empty($data["active"]) && $data["active"] == 1 ? $data["active"] : 0;

        $set .= ", icono = ? ";
        $datos[] = strlen($data["icono"]) > 0 ? $data["icono"] : null;

        $set .= ", pertenece = ? ";
        $set .= ", orden_lista = ? ";

        if( $data["nivel"] > 1 ){// SI EL NIVEL EL MAYOR A 1, TIENE UN PADRE
            $datos[] = $data["pertenece"];

            $padre = $this->getOne(["id"=>$data["pertenece"]]);
            $datos[] = $padre["data"]->orden_lista.$data["nivel"].$this->abc[$data["orden"]];
        } else {
            $datos[] = null;
            $datos[] = $data["nivel"].$this->abc[$data["orden"]];
        }

        $set .= ", permiso = ? ";
        // SI NO ES UN SUB MENU, ASIGNALE UN PERMISO
        $datos[] = $data["submenu"] == 0 ? $data["permiso"] : null;

        $datos[] = $data["id"];

        $sql = "UPDATE menu
                SET etiqueta = ?, submenu = ?, orden = ?, nivel = ? $set
                WHERE id = ?;";
        $query = $this->db->query($sql, $datos);

        if (!$query) return ['error'=>'Ocurrió un error al actualizar los datos.'];

        return true;
    }

    public function menuUsuario() {
        $pera = (!empty($this->udata['permiso'])) ? $this->udata['permiso'] : [];
        $pera = (!empty($this->udata['permisoRepo'])) ? array_merge($pera, $this->udata['permisoRepo']) : $pera;
        $pera = (!empty($this->udata['permisoEsp'])) ? array_merge($pera, $this->udata['permisoEsp']) : $pera;
        $pera = (!empty($this->udata['permisoSec'])) ? array_merge($pera, $this->udata['permisoSec']) : $pera;
        $pera = "'" . implode("','", $pera) . "'";
        $query = $this->db->query("SELECT m.*, m2.etiqueta padre FROM menu m
            LEFT JOIN menu m2 ON m2.id = m.pertenece
            WHERE m.active = '1' AND (m.permiso IN ($pera) || m.permiso IS NULL)
            ORDER BY m.orden_lista");
        $filas = $query->result();
        $res = [];
        foreach ($filas as $fila) {
            $res[$fila->id] = $fila;
        }

        return $res;
    }

    public function buscarPertenece($data) {
        // BUSCO LOS MENUS PADRES DEL NIVEL ANTERIOR AL QUE SE VA AGREGAR
        // EJ. SI SE VA AGREGAR UN MENU NIVEL 3, BUSCARA TODOS LOS SUBMENU NIVEL 2 PARA ASIGNARLE AL NUEVO MENU
        $nivel = $data["nivel"] - 1;

        $sql = "SELECT n1.id, CONCAT(IFNULL(CONCAT(n2.etiqueta,'/'), ''), n1.etiqueta) AS etiqueta
                FROM menu n1
                LEFT JOIN menu n2 ON n2.id = n1.pertenece
                WHERE n1.active = 1
                AND n1.submenu = 1
                AND n1.nivel = ?
                ORDER BY n1.pertenece, n1.orden, n1.etiqueta;";
        $query = $this->db->query($sql, [$nivel]);

        $data["data"] = $query->result();

        return $data;
    }

    public function buscarOrden($data) {
        //BUSCO EL MAYOR CAMPO ORDEN DE LOS HIJOS DEL PADRE(PERTENECE) PARA VER CUAL SERÁ EL SIGUIENTE ORDEN, A MENOS QUE SEA UN MENU NIVEL 1, POR QUE ESE NO TIENE PADRE Y POR TANTO SOLO TRAERÁ EL ORDEN DEL ULTIMO MENU NIVEL 1

        $campo = $data["campo"];
        $id    = $data["id"];

        if( $campo == "pertenece" ) $and = " AND pertenece = ? ";//SI NO ES NIVEL 1
        else $and = " AND nivel = ? ";//SI ES NIVEL 1

        $sql = "SELECT IFNULL(MAX(orden)+1, 1) AS orden
                FROM menu
                WHERE active = 1
                $and
                ORDER BY orden, etiqueta;";
        $query = $this->db->query($sql, [$id]);

        if( $query->num_rows() > 0 ) $data["data"] = $query->row();
        else $data["data"] = (object)["orden" => 1];

        return $data;
    }

    private function getOne($data) {
        $where = "";
        $datos = [];
        if( !empty($data["id"]) ){
            $where   = "WHERE id = ?";
            $datos[] = $data["id"];
        }
        $query = $this->db->query("SELECT * FROM menu $where LIMIT 1", $datos);
        $data["data"] = $query->row();

        return $data;
    }

    public function ordenPorPadre() {
        $query = $this->db->query("SELECT * FROM menu ORDER BY nivel, orden;");
        $registros = [];
        $maxnivel = 1;
        // Nivel 1
        foreach ($query->result() as $data){
            $registros[$data->id] = $data;
            if ($data->nivel > $maxnivel) { $maxnivel = $data->nivel; }
            if ($data->nivel == 1) {
                $registros[$data->id]->orden_lista = $data->nivel . $this->abc[$data->orden];
            }
        }
        if (count($registros) > 500) { return ['error'=>'Demasiados registros.']; }
        // Niveles posteriores
        if (!empty($registros)) {
            foreach ($registros as $data){
                if ($data->nivel > 1) {
                    $registros[$data->id]->orden_lista = $registros[$data->pertenece]->orden_lista .
                    $data->nivel . $this->abc[$data->orden];
                }
            }
            // Rellenar los campos orden_lista con ceros dependiendo del nivel
            foreach ($registros as $data){
                $ceros = str_repeat("0", ($maxnivel - $data->nivel) * 2);
                $registros[$data->id]->orden_lista = $registros[$data->id]->orden_lista . $ceros;
            }
            // Preparar query
            $sqlentries = "";
            foreach ($registros as $data) {
                $sqlentries .= "(" . $data->id . ", '" . $data->orden_lista . "'),";
            }
            $sqlentries = rtrim($sqlentries, ",");
            $query = $this->db->query("INSERT INTO menu (id, orden_lista) VALUES " . $sqlentries .
                " ON DUPLICATE KEY UPDATE orden_lista = VALUES(orden_lista)");

            if (!$query) { return ['error'=>$this->db->error_message()]; }
        }

        return true;
    }
}

?>