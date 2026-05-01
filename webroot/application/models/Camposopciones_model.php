<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Camposopciones_model extends CI_Model
{

    public function get_branch($campanas) {
        $campanas = explode(",", $campanas);

        $query = $this->db->query("SELECT campo, val1, val2, val3, val4
                                    FROM disp_depend
                                    WHERE id_campaign IN ?
                                    UNION
                                    SELECT campo, val1, val2, val3, val4
                                    FROM disp_depend
                                    WHERE id_campaign IS NULL
                                    ORDER BY campo, val1, val2, val3, val4
                                    ", [$campanas]);
		$res = $query->result();

        $ret = ['cams'=>'','l1'=>'','l2'=>'','l3'=>'','l4'=>''];
        $cam = $lvl1 = $lvl2 = $lvl3 = $lvl4 = [];
        $id = "primero";
        foreach ($res as $row) {
            $ec = trim(preg_replace('/\s+/', ' ',$row->campo));
            $e1 = trim(preg_replace('/\s+/', ' ',$row->val1));
            $e2 = trim(preg_replace('/\s+/', ' ',$row->val2));
            $e3 = trim(preg_replace('/\s+/', ' ',$row->val3));
            $e4 = trim(preg_replace('/\s+/', ' ',$row->val4));
            if (!empty($ec) && !in_array($ec, $cam)) {
                $cam[] = $ec;
                $ret['cams'] .= '<a href="#" class="adop list-group-item list-group-item-action" data-lvl="cam" id="'.$id.'">'.$ec.'</a>';
                $id = "";
            }
            $valor = $ec.$e1;
            if (!empty($e1) && !in_array($valor, $lvl1)) {
                $lvl1[] = $valor;
                $ret['l1'] .= '<a href="#" class="adop list-group-item list-group-item-action d-none" data-padre="'.$ec.'" data-cam="'.$ec.'" data-lvl="1">'.$e1.'</a>';
            }
            $valor = $valor.$e2;
            if (!empty($e2) && !in_array($valor, $lvl2)) {
                $lvl2[] = $valor;
                // btn btn-link
                $ret['l2'] .= '<a href="#" class="adop list-group-item list-group-item-action d-none" data-padre="'.$e1.'" data-cam="'.$ec.'" data-lvl="2">'.$e2.'</a>';
            }
            $valor = $valor.$e3;
            if (!empty($e3) && !in_array($valor, $lvl3)) {
                $lvl3[] = $valor;
                $ret['l3'] .= '<a href="#" class="adop list-group-item list-group-item-action d-none" data-padre="'.$e2.'" data-cam="'.$ec.'" data-lvl="3">'.$e3.'</a>';
            }
            $valor = $valor.$e4;
            if (!empty($e4) && !in_array($valor, $lvl4)) {
                $lvl4[] = $valor;
                $ret['l4'] .= '<a href="#" class="adop list-group-item list-group-item-action d-none" data-padre="'.$e3.'" data-cam="'.$ec.'" data-lvl="4">'.$e4.'</a>';
            }
        }

        return $ret;
	}

    public function guardar($data) {
        $id_campaign = $this->input->post('campanas', true);
        $cam = $this->input->post('cam', true);
        $lvl1 = $this->input->post('lvl1', true);
        $lvl2 = $this->input->post('lvl2', true);
        $lvl3 = $this->input->post('lvl3', true);
        $lvl4 = $this->input->post('lvl4', true);

        if(!empty($data['addbtn'])) {
            if ( strlen($lvl4) > 0 ) {
                $query = $this->db->query("SELECT * from disp_depend
                    where campo='$cam' AND val1='$lvl1' AND val2='$lvl2' AND val3='$lvl3' AND val4=''");
                $siup = $query->num_rows() > 0 ? true : false;
                $toset = "val4='$lvl4'";
                $elwer = "campo='$cam' AND val1='$lvl1' AND val2='$lvl2' AND val3='$lvl3' AND val4=''";
            } else if ( strlen($lvl3) > 0 ) {
                $query = $this->db->query("SELECT * from disp_depend
                    where campo='$cam' AND val1='$lvl1' AND val2='$lvl2' AND val3=''");
                $siup = $query->num_rows() > 0 ? true : false;
                $toset = "val3='$lvl3'";
                $elwer = "campo='$cam' AND val1='$lvl1' AND val2='$lvl2' AND val3=''";
            } else if ( strlen($lvl2) > 0 ) {
                $query = $this->db->query("SELECT * from disp_depend
                    where campo='$cam' AND val1='$lvl1' AND val2=''");
                $siup = $query->num_rows() > 0 ? true : false;
                $toset = "val2='$lvl2'";
                $elwer = "campo='$cam' AND val1='$lvl1' AND val2=''";
            } else {
                $siup = false;
            }
            if ($siup) {
                if( $id_campaign != "" && $id_campaign > 0 ) $toset .= ", id_campaign = $id_campaign";
                $query = $this->db->query("UPDATE disp_depend SET $toset WHERE $elwer");
            } else {
                $campo = $valor = "";
                $datos = [];
                if( $id_campaign != "" && $id_campaign > 0 ){
                    $campo = ", id_campaign";
                    $valor = ",?";
                    $datos = [$id_campaign];
                }
        		$query = $this->db->query("INSERT INTO disp_depend (campo, val1, val2, val3, val4 $campo) VALUES (?,?,?,?,? $valor)",
                array_merge([$cam,$lvl1,$lvl2,$lvl3,$lvl4],$datos)
                );
            }

            if ($query) return true;

        } elseif (!empty($data['delbtn'])) {
            $maswhere = "";
            if( $id_campaign != "" && $id_campaign > 0 ) $maswhere .= " AND id_campaign = $id_campaign ";
            if (!empty($lvl1)) $maswhere .= " AND val1='$lvl1'";
            if (!empty($lvl2)) $maswhere .= " AND val2='$lvl2'";
            if (!empty($lvl3)) $maswhere .= " AND val3='$lvl3'";
            if (!empty($lvl4)) $maswhere .= " AND val4='$lvl4'";

            $sql = "DELETE FROM disp_depend WHERE campo='$cam' $maswhere";

            $query = $this->db->query($sql);
        }

        if ($query) return true;

		return ['error'=>$this->db->error()];
	}

    public function actualizar($data) {
        $id_campaign = $this->input->post('campanas', true);
        $cam = $this->input->post('cam', true);

        if(!empty($data['actbtn'])) {
            if( $id_campaign == "" )//SI NO ENVIA UNA CAMPAÑA
                $query = $this->db->query("UPDATE disp_depend SET id_campaign = NULL WHERE campo = ?", [$cam]);
            else//SI ENVIA UNA CAMPAÑA
                $query = $this->db->query("UPDATE disp_depend SET id_campaign = ? WHERE campo = ? AND id_campaign IS NULL", [$id_campaign, $cam]);

            if ($query) return true;

        }

        if ($query) return true;

		return ['error'=>$this->db->error()];
	}

}
?>
