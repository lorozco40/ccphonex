<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Reader\ReaderFactory;

class Despachador extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model('desp_model');
    }

    public function index() {
        $this->load->helper('fun_helper');
        $this->load->model('colas_model');
        $data['name_colas']= $this->colas_model->traeColasNamePorCampanas();
        $data['campanas'] = $this->datos_model->getCampanas();
        $data['users']    = $this->desp_model->get_users($data['campanas']);
        $campsids         = $this->datos_model->getIdsFromArr($data['campanas']);
        $data['camps']    = $this->desp_model->get_all($campsids);
        $data['selcamp']  = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data['colas']    = colas();
        $data['title']    = 'Despachador';
        $data['jscript']  = 'config/despachador';

        $this->armado->mostrar(array(
            'view' => 'config/despachador',
            'data' => $data,
        ));
    }

    public function accion() {
        $data = $this->input->post();
        if (!empty($data['activar'])) {
            $respu = $this->desp_model->activar($data);
            if ($respu === true) {
                $this->session->set_flashdata('infomsg', 'Campaña despachador iniciada.');
            } else {
                $this->session->set_flashdata('errormsg', $respu);
            }
        } elseif (!empty($data['archivar'])) {
            if ($this->desp_model->archivar($data)) {
                $data['id_desp'] = "";
                $this->session->set_flashdata('infomsg', 'Campaña despachador archivada.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error desconocido en despachador.');
            }
        } elseif (!empty($data['detener'])) {
            if ($this->desp_model->detener($data['id_desp'])) {
                $this->session->set_flashdata('infomsg', 'Campaña despachador detenida.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error desconocido en despachador.');
            }
        } elseif (!empty($data['eliminar'])) {
            if ($this->desp_model->eliminar($data['id_desp'])) {
                $data['id_desp'] = "";
                $this->session->set_flashdata('infomsg', 'Despachador eliminado completamente del sistema.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error desconocido en despachador.');
            }
        } elseif (!empty($data['reactivar'])) {
            if ($this->desp_model->desarchivar($data['id_desp'])) {
                $this->session->set_flashdata('infomsg', 'Despachador reactivado.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error desconocido en despachador.');
            }
        } elseif (!empty($data['actualizarcola'])) {
            if ($this->desp_model->actualizarcola($data)) {
                $this->session->set_flashdata('infomsg', 'Cola cambiada.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error desconocido en despachador.');
            }
        } else {
            $this->session->set_flashdata('errormsg', 'Error desconocido en despachador.');
        }
        $id_desp = (!empty($data['id_desp'])) ? "/".$data['id_desp'] : "";
        redirect("despachador".$id_desp);
    }

    public function updtipo() {
        $data = $this->input->post();
        return $this->desp_model->updtipo($data);
    }

    public function updvueltas() {
        $data = $this->input->post();
        return $this->desp_model->updvueltas($data);
    }

    public function addcond() {
        $return = $this->desp_model->addcond($this->input->post());
        if ($return) {
            $this->session->set_flashdata('infomsg', 'Condición agregada.');
        } else {
            $this->session->set_flashdata('errormsg', 'Datos incorectos.');
        }
        redirect("despachador/".$this->input->post('id_desp'));
    }

    public function delcond() {
        $return = $this->desp_model->delcond($this->input->post());
        if ($return) {
            $this->session->set_flashdata('infomsg', 'Condición quitada.');
        } else {
            $this->session->set_flashdata('errormsg', 'Datos incorectos.');
        }
        redirect("despachador/".$this->input->post('id_desp'));
    }

    public function adddesp() {
        if ($this->desp_model->adddesp($this->input->post())) {
            $this->session->set_flashdata('infomsg', 'Campaña despachador agregada con éxito.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al agregar.');
        }
        redirect("despachador");
    }

    public function subir() {
        if (!file_exists('/var/www/upload')) mkdir('/var/www/upload', 0755, true);
        $data['upload_path']   = '/var/www/upload/';
        $data['allowed_types'] = 'csv';
		$data['file_name']     = md5(uniqid(rand(), true)).'.csv';

        $this->load->library('upload', $data);
        $pdata = $this->input->post();
        if (!$this->upload->do_upload('elcsv')) {
            $this->session->set_flashdata("errormsg", $this->upload->display_errors());
		} else {
            $this->desp_model->csvname($pdata['id_desp'], $data['file_name']);
            $this->session->set_flashdata("infomsg", "Archivo subido con éxito");
            if (!empty($pdata['masregs'])) {
                $this->addcsvregs($pdata['id_desp'], $data['file_name']);
            }
        }
        redirect("despachador/".$this->input->post('id_desp'));
	}

    public function adduser() {
        if ($this->desp_model->adduser($this->input->post())) {
            $this->session->set_flashdata('infomsg', 'Usuario agregado con éxito.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al agregar al usuario.');
        }
        redirect("despachador/".$this->input->post('id_desp'));
    }

    public function deluser() {
        if ($this->desp_model->deluser($this->input->post())) {
            $this->session->set_flashdata('infomsg', 'Usuario quitado con éxito.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al quitar al usuario.');
        }
        redirect("despachador/".$this->input->post('id_desp'));
    }

    public function addqualif() {
        if ($this->desp_model->addqualif($this->input->post())) {
            $this->session->set_flashdata('infomsg', 'Tipificación agregada con éxito.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al agregar la tipificación.');
        }
        redirect("despachador/".$this->input->post('id_desp'));
    }

    public function delqualif() {
        if ($this->desp_model->delqualif($this->input->post())) {
            $this->session->set_flashdata('infomsg', 'Tipificación quitada con éxito.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al quitar la tipificación.');
        }
        redirect("despachador/".$this->input->post('id_desp'));
    }

    public function pasarcsvamysql() {
        $this->load->library('csvreader');
        if ($this->desp_model->pasarcsv($this->input->post())) {
            $this->session->set_flashdata('infomsg', 'Se ha cargado el csv.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al cargar el archivo.');
        }
        redirect("despachador/".$this->input->post('id_desp'));
	}

    public function delcsv() {
        if ($this->desp_model->delcsv($this->input->post('id_csv'), $this->input->post('csv'))) {
            $this->session->set_flashdata('infomsg', 'Se ha eliminado el csv.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al eliminar el archivo.');
        }
        redirect("despachador");
    }

    private function addcsvregs($id_disp, $fname) {
        $query = $this->db->query("SELECT slug, type from disp_field
            where id_dispatcher = ? and typedb=0 order by `order`, `id`", [$id_disp]);
        $cols = $query->result();
        $id_camp = "";
        $query = $this->db->query("SELECT id_campaign from dispatcher where id = ?", [$id_disp]);
        if ($query->num_rows()==1) $id_camp = $query->row()->id_campaign;
        $prefijo = "";
        $query = $this->db->query("SELECT valor from campaign_data where id_campaign = ? and atributo = 'prefijo'", [$id_camp]);
        if ($query->num_rows()==1) $prefijo = $query->row()->valor;
        $reader = ReaderFactory::create(Type::CSV);
        $reader->open('/var/www/upload/'.$fname);
        foreach ($reader->getSheetIterator() as $sheet) {
            $query = "INSERT INTO `disp_".$id_disp."` values ";
            $rcount = 0;
            $contador = 1;
            foreach ($sheet->getRowIterator() as $key => $row) {
                if ($rcount>0 || slugify($row[0])!=$cols[0]->slug) {
                    $query .= "(0, ";
                    foreach ($cols as $keyc => $campo) {
                        $val = $row[$keyc];
                        if ($campo->slug == 'telefono') {
                            $val = $prefijo . trim(preg_replace('/[^0-9]+/', '', $val));
                        } elseif ($campo->type=='datetime') {
                            if (!empty($val)) {
                                $val = date("Y-m-d H:i:s", strtotime($val));
                            } else {
                                $val = 'null';
                            }
                        }
                        $val = str_replace("'", "", $val);
                        $query .= "'".$val."', ";
                    }
                    $query = rtrim($query, ", ");
                    $query .= ", '', 0, 0, 0, null, 0, null, now(), null), ";
                    $contador++;
                    if ($contador == 200) {
                        $query = rtrim($query, ", ");
                        $res = $this->db->query($query);
                        $contador = 1;
                        $query = "INSERT INTO `disp_".$id_disp."` values ";
                    }
                }
                $rcount++;
            }
            $query = rtrim($query, ", ");
            $res = $this->db->query($query);
        }
        $reader->close();

        $this->db->query("DELETE from disp_csv where name = ?", [$fname]);
        unlink('/var/www/upload/'.$fname);

        return true;
    }

    public function traer_despachador() {
        $res = array();
        $res['desp'] = $this->desp_model->getDespInfo($this->input->post('id_desp'));
        if ($res['desp']) {
            $data = $this->desp_model->getForm($this->input->post(), $res['desp']);
            if (!empty($data)) {
                $data->uniqueid = '';
                $res['histo'] = $this->desp_model->histo($res["desp"]->id, $data->id);
                $res['phone'] = $data->telefono;
                $res['form'] = $this->load->view("despachador/desp_form_".$res["desp"]->id, $data, true);
            } else {
                $res['desp'] = null;
            }
        }

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function actualiza_registro() {
        $respu = $this->desp_model->actualiza_registro($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function buscar() {
        $respu = $this->desp_model->buscar($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($respu);
    }

    public function get_field() {
        Header('Content-Type: application/json');
        echo json_encode($this->desp_model->get_field($this->input->post('id')));
    }

    public function add_field() {
        $respu = $this->desp_model->add_field($this->input->post());
        if ($respu === TRUE) {
            $this->session->set_flashdata('infomsg', 'Se ha guardado el campo.');
        } else {
            $this->session->set_flashdata('errormsg', $respu);
        }
        redirect("despachador/".$this->input->post('id_desp'));
    }

    public function upd_field() {
        $respu = $this->desp_model->upd_field($this->input->post());
        if ($respu === TRUE) {
            $this->session->set_flashdata('infomsg', 'Se ha guardado el campo.');
        } else {
            $this->session->set_flashdata('errormsg', $respu);
        }
        redirect("despachador/".$this->input->post('id_desp'));
    }

    public function del_field() {
        $respu = $this->desp_model->del_field($this->input->post());
        if ($respu) {
            $this->session->set_flashdata('infomsg', 'Se ha eliminado la columna de la tabla.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al eliminar.');
        }
        redirect("despachador/".$this->input->post('id_desp'));
    }

    public function reporte_detalle() {
        $campanas        = $this->datos_model->getCampanas(false, false);
        $data['modelo']  = 'desp';
        $data['title']   = 'Despachador detalle';
        $data['jscript'] = 'reportes/reporte';
        $data['nodates'] = 'nodates';
        $data['filtro_estatus'] = 'si';
        $data['filtro_agendar'] = 'si';
        $data['massel']['Despachador'] = $this->desp_model->get_despachadores($campanas);

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function reporte_llamadas() {
        $cams            = $this->datos_model->getCampanas(false, false);
        $data['agentes'] = $this->datos_model->getRelUsers(["cam"=>$cams, "act"=>FALSE]);
        $data['modelo']  = 'desp';
        $data['title']   = 'Despachador llamadas';
        $data['jscript'] = 'reportes/reporte';
        $data['nojoinags'] = true;
        $data['massel']['Despachador']  = $this->desp_model->get_despachadores($cams);
        $data['massel']['Tipificación'] = [
            '0' => 'Todas ...',
            'Buzón' => 'Buzón',
            'No contesta' => 'No contesta',
            'Seguimiento' => 'Seguimiento',
            'Número incorrecto' => 'Número incorrecto',
            'No le interesa' => 'No le interesa',
        ];

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function reporte_llamadas_full() {
        $cams            = $this->datos_model->getCampanas(false, false);
        $data['agentes'] = $this->datos_model->getRelUsers(["cam"=>$cams, "act"=>FALSE]);
        $data['modelo']  = 'desp';
        $data['title']   = 'Despachador llamadas full';
        $data['jscript'] = 'reportes/reporte';
        $data['nojoinags'] = true;
        $data['massel']['Despachador']  = $this->desp_model->get_despachadores($cams);
        $data['massel']['Tipificación'] = [
            '0' => 'Todas ...',
            'Buzón' => 'Buzón',
            'No contesta' => 'No contesta',
            'Seguimiento' => 'Seguimiento',
            'Número incorrecto' => 'Número incorrecto',
            'No le interesa' => 'No le interesa',
        ];

        $this->armado->mostrar(array(
            'view' => 'reportes/reporte',
            'data' => $data,
        ));
    }

    public function reporte_indicador() {
        $campanas              = $this->datos_model->getCampanas(false, false);
        $data['despachadores'] = $this->desp_model->get_despachadores($campanas);
        $data['title']         = 'Reporte preview indicador';
        $data['jscript']       = 'reportes/despachador_indicador';

        $this->armado->mostrar(array(
            'view' => 'reportes/despachador_indicador',
            'data' => $data,
        ));
    }

    public function reporte_indicador_data() {
        $id_desp = $this->input->post("id_desp");
        $data = [];
        $data["indicador"] = $this->desp_model->reporte_indicador_data($id_desp);
        $data["estatus_agendado_totales"] = $this->desp_model->despachador_estatus_agendado_totales($id_desp);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function preddata() {
        $data = $this->desp_model->preddata($this->input->post());

        if ($data === TRUE) {
            $this->session->set_flashdata('infomsg', 'Actualizado.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al guardar el gateway.');
        }
        redirect("despachador/".$this->input->post('id_desp'));
    }

    public function monitor() {
        $campanas              = $this->datos_model->getCampanas(false, false);
        $data['despachadores'] = $this->desp_model->get_despachadores($campanas);
        $data['title']         = 'Monitoreo despachadores';
        $data['jscript']       = 'reportes/despachador_monitor';

        $this->armado->mostrar(array(
            'view' => 'despachador/monitor_desp',
            'data' => $data,
        ));
    }

    public function monitor_data() {
        $id_desp = $this->input->post("id_desp");
        $this->load->helper('fun_helper');
        $data = $this->desp_model->monitor_data($id_desp);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    function postponer() {
        $id_desp = $this->input->post("id_desp");
        $id_reg  = $this->input->post("id_reg");
        $nfecha  = $this->input->post("nfecha");
        if (empty($id_desp) || empty($id_reg) || empty($nfecha)) {
            $respu = ['error' => 'Faltan datos'];
        }
        $postp = $this->db->query("UPDATE `disp_".$id_desp."` set `last_update`=now(), `since`=? where `id`=?", [$nfecha, $id_reg]);
        if ($postp) {
            $respu = ['msg' => 'Se ha pospuesto el registro'];
        } else {
            $respu = ['error' => 'Error al posponer el registro: ' . $this->db->error()];
        }
        Header('Content-Type: application/json');
        echo json_encode($respu);
        return;
    }

    public function ver_cats() {
        $data = $this->input->post();
        $cats = $this->db->query("SELECT * FROM `disp_" . $data['did'] . "_cats` WHERE `name` = ?", [$data['name']])->result();
        $this->load->view("despachador/cats_list", ["cats"=>$cats,"did"=>$data['did']]);
    }

    public function add_cat() {
        $data = $this->input->post();
        $did = $data['did'];
        unset($data['did']);
        $this->db->insert("disp_" . $did . "_cats", $data);
        $cats = $this->db->query("SELECT * FROM `disp_" . $did . "_cats` WHERE `name` = ?", [$data['name']])->result();
        $this->load->view("despachador/cats_list", ["cats"=>$cats,"did"=>$did]);
    }

    public function edit_cat() {
        $data = $this->input->post();
        $this->db->update("disp_" . $data["did"] . "_cats",
            ["parent"=>$data["parent"],"name"=>$data["name"],"eti"=>$data["eti"],"val"=>$data["val"],"seq"=>$data["seq"]],
            ["id"=>$data["cid"]]);
        $cats = $this->db->query("SELECT * FROM `disp_" . $data["did"] . "_cats` WHERE `name` = ?", [$data['name']])->result();
        $this->load->view("despachador/cats_list", ["cats"=>$cats,"did"=>$data["did"]]);
    }

    public function del_cat() {
        $data = $this->input->get();
        $reg = $this->db->query("SELECT * FROM `disp_" . $data['did'] . "_cats` WHERE `id` = ?", [$data['cid']])->row();
        $this->db->delete("disp_" . $data['did'] . "_cats", ["id"=>$data['cid']]);
        $cats = $this->db->query("SELECT * FROM `disp_" . $data['did'] . "_cats` WHERE `name` = ?", [$reg->name])->result();
        $this->load->view("despachador/cats_list", ["cats"=>$cats,"did"=>$data['did']]);
    }

}

?>
