<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extapi_model extends CI_Model
{

    public function list($data) {
        $pag = (empty($data['pag'])) ? 0  : (int)$data['pag'];
        $rpp = (empty($data['rpp'])) ? 20 : (int)$data['rpp'];
        $campanas = (empty($data['campanas'])) ? $this->udata['campanas'] : $data['campanas'];
        $mascam = ($this->udata['perfil'] == "admin") ? "OR id_campaign IS NULL" : "";
        $query = $this->db->query("SELECT COUNT(*) tot FROM extapi WHERE id_campaign in (" . $campanas . ") $mascam");
        $data['tot'] = $query->row()->tot;
        $query = $this->db->query("SELECT ea.*, ifnull(c.name, '') campana from extapi ea
            LEFT JOIN campaign c ON c.id = ea.id_campaign
            where `id_campaign` in (" . $campanas . ") $mascam
            order by `name` LIMIT ?, ?", [$pag, $rpp]);
        $data['data'] = $query->result();
        foreach ($data['data'] as $key => $row) {
            $query = $this->db->query("SELECT * FROM extapi_met WHERE id_extapi = $row->id");
            $data['data'][$key]->mets = $query->result();
            foreach ($data['data'][$key]->mets as $key2 => $row2) {
                $query = $this->db->query("SELECT * FROM extapi_fields WHERE id_extapi_met = $row2->id");
                $data['data'][$key]->mets[$key2]->fields = $query->result();
            }
        }

        return $data;
    }

    public function save(array $data) {
        $activ  = (empty($data['active'])) ? 0 : 1;
        $valid_crt = (empty($data['valid_crt'])) ? 0 : 1;
        $cam = $data['campana'] ?: null;
        if (empty($data['id'])) {
            // Registro nuevo, alta
            $query = $this->db->query("INSERT INTO extapi (`id_campaign`, `name`, `url`, `user`,
                `pass`, `token`, `xhash`, `sign`, `info`, `logloc`, `valid_crt`, `active`,
                `created_by`) values (?,?,?,?,?,?,?,?,?,?,?,?,?)", 
                [$cam, $data['name'], $data['url'], $data['user'], $data['pass'], $data['token'],
                $data['xhash'], $data['sign'], $data['info'], $data['logloc'], $valid_crt, $activ,
                $this->udata['id']]);
        } else {
            // Actualizar registro
            $query = $this->db->query("UPDATE extapi 
                SET `id_campaign`=?, `name`=?, `url`=?, `user`=?, `pass`=?, `token`=?, `xhash`=?,
                `sign`=?, `info`=?, `logloc`=?, `valid_crt`=?, `active`=?
                WHERE `id`=?",
                [$cam, $data['name'], $data['url'], $data['user'], $data['pass'], $data['token'],
                $data['xhash'], $data['sign'], $data['info'], $data['logloc'], $valid_crt, $activ,
                $data['id']]);
        }
        if ($query) {
            return ["tipo"=>"ok","msg"=>"Registro guardado"];
        } else {
            return ["tipo"=>"error","msg"=>$this->db->error()];
        }
    }

    public function saveMet(array $data) {
        if ( $data['id'] == 0 ) {//Insertamos
            $query = $this->db->query("INSERT INTO extapi_met (id_extapi, prot, met, xtype, info)
                VALUES (?,?,?,?,?)", [ $data['id_extapi'], $data['prot'], $data['met'], $data['xtype'], $data['info'] ]);
        } else {// Actualizar registro
            $query = $this->db->query("UPDATE extapi_met 
            SET id_extapi=?, prot=?, met=?, xtype=?, info=?
            WHERE id=?", [ $data['id_extapi'], $data['prot'], $data['met'], $data['xtype'], $data['info'], $data['id'] ]
            );
        }
        if ($query) {
            return ["tipo"=>"ok","msg"=>"Registro guardado"];
        } else {
            return ["tipo"=>"error","msg"=>$this->db->error()];
        }
    }

    public function saveFields(array $data) {
        $dir    = (empty($data['dir'])) ? 0 : 1;
        $req    = (empty($data['req'])) ? 0 : 1;
        if ( $data['id'] == 0 ) {
            $msg = "Registro creado";
            $query = $this->db->query("INSERT INTO extapi_fields (id_extapi_met, field, ftype, dir, req, descript)
                values (?,?,?,?,?,?)", [ $data['id_extapi_met'], $data['field'], $data['ftype'], $dir, $req, $data['descript'] ]);
        } else {
            $msg = "Registro actualizado";
            // Actualizar registro
            $query = $this->db->query("UPDATE extapi_fields 
            SET id_extapi_met=?, field=?, ftype=?, dir=?, req=?, descript=?
            WHERE `id`=?",
                [ $data['id_extapi_met'], $data['field'], $data['ftype'], $dir, $req, $data['descript'],
                $data['id']]
            );
        }
        if ($query) {
            return ["tipo"=>"ok","msg"=>$msg];
        } else {
            return ["tipo"=>"error","msg"=>$this->db->error()];
        }
    }

    public function delete($id) {
        $this->db->trans_start();
            // ELIMINAR DE EXTAPI_FIELDS
            $sql_fields = "DELETE 
            FROM extapi_fields 
            WHERE id IN (
                SELECT f.id
                FROM extapi_met m
                JOIN extapi_fields f ON m.id = f.id_extapi_met
                WHERE id_extapi = ?
            )";
            $this->db->query($sql_fields, $id);
            // ELIMINAR DE EXTAPI_MET
            $this->db->where('id_extapi', $id);
            $this->db->delete('extapi_met');            
            // ELIMINAR DE TABLA EXTAPI
            $this->db->where('id', $id);
            $this->db->delete('extapi');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['tipo' => 'error', 'msg' => $this->db->error()];
        } else {
            $this->db->trans_commit();
            return ['tipo' => 'ok', 'msg' => 'Registro eliminado'];
        }
    }

    public function deleteMet($id) {
        $this->db->trans_start();
            // ELIMINAR DE EXTAPI_FIELDS
            $this->db->where('id_extapi_met', $id);
            $this->db->delete('extapi_fields');
            // ELIMINAR DE EXTAPI_MET
            $this->db->where('id', $id);
            $this->db->delete('extapi_met');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['tipo' => 'error', 'msg' => $this->db->error()];
        } else {
            $this->db->trans_commit();
            return ['tipo' => 'ok', 'msg' => 'Registro eliminado'];
        }
    }

    public function deleteFields($id) {
        $sql = "DELETE FROM extapi_fields WHERE id = ?;";
        $query = $this->db->query($sql, [$id]);
        if( $query ) {
            return ['tipo' => 'ok', 'msg' => 'Registro eliminado'];
        } else {
            return ['tipo' => 'error', 'msg' => $this->db->error()];
        }
    }

}
