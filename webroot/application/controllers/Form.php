<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;

class Form extends MY_Controller
{

    private $reservados = ["id","id_user","detalle","id_cliente","asignar_a","cierre","apertura","estatus","informar","semaforo","tipo","prioridad"];

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $data['campaigns']   = $this->datos_model->getCampanas();
        $this->load->model('form_model');
        $pagi                = $this->pagi;
        $pagi['base_url']    = site_url('form');
        $pagi['uri_segment'] = 2;
        $cams                = $this->datos_model->getIdsFromArr($data['campaigns']);
        $pagi['total_rows']  = $this->form_model->countByCams($cams);
        $this->load->library('pagination', $pagi);
        $data['pagination']  = $this->pagination->create_links();
        $data['page']        = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data['data']        = $this->form_model->get_list($cams, (int)$data['page']);
        $data['title']       = 'Formularios';
        // $data['jscript']     = 'config/form';

        $this->armado->mostrar(array(
            'view' => 'config/form',
            'data' => $data,
        ));
    }

    public function crear() {
        // $this->form_validation->set_rules('short_name', 'Nombre corto', 'required|max_length[10]|is_unique[form.short_name]');
        $this->form_validation->set_rules('name', 'Nombre', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'El nombre del formulario es requerido para identificarlo.');
        } else {
            $this->load->model('form_model');
            if ($this->form_model->create($this->input->post())) {
                $this->session->set_flashdata('infomsg', 'Fomulario creado con éxito.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error al crear el formulario.');
            }
        }

        redirect('form');
    }

    public function eliminar(int $fid = 0) {
        $this->load->model("form_model");
        $res = $this->form_model->eliminaForm($fid);
        if ($res) {
            $this->session->set_flashdata('infomsg', 'Formulario ' . $fid . ' eliminado');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al eliminar el Formulario ' . $fid);
        }

        redirect('form');
    }

    public function actualizar() {
        $this->form_validation->set_rules('id', 'ID', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'No puedes actualizar un formulario inexistente.');
        } else {
            $this->load->model('form_model');
            if ($this->form_model->update()) {
                $this->session->set_flashdata('infomsg', 'Fomulario actualizado con éxito.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error al actualizar el formulario.');
            }
        }

        redirect('form');
    }

    public function campos(int $fid) {
        $this->load->model('form_model');
        $data            = $this->form_model->get_one($fid);
        $data['data']    = $this->form_model->get_fields($fid);
        $data['fields_form_by_table']    = $this->form_model->get_fields_form_by_table($fid);
        $data['fields_form'] = $this->form_model->get_fields_form($fid);
        $data['tbrs']    = $this->form_model->get_tbr($fid);
        $data['ftr_selects'] = $this->form_model->ftr_selects($fid);
        $data['consem']  = $this->form_model->get_sem($fid);
        $data['oct_selects'] = $this->form_model->oct_base_fields_and_depends($fid);
        $data['title']   = 'Campos de formulario';
        $data['jscript'] = 'config/form_campos';

        $this->armado->mostrar(array(
            'view' => 'config/form_campos',
            'data' => $data,
        ));
    }

    public function crearc() {
        $this->form_validation->set_rules('name', 'Nombre', 'required|max_length[35]');
        $this->form_validation->set_rules('type', 'Tipo', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'El nombre es requerido.');
        } else {
            $slug = slugify($this->input->post('name'));
            if (in_array($slug, $this->reservados)) {
                $this->session->set_flashdata('errormsg', 'Campo reservado, usa otro nombre.');
            } else {
                $this->load->model('form_model');
                if ($this->form_model->createc()) {
                    $this->session->set_flashdata('infomsg', 'Campo agregado con éxito.');
                } else {
                    $this->session->set_flashdata('errormsg', 'Error al agregar el campo.');
                }
            }
        }

        redirect('form/campos/'.$this->input->post('id_form'));
    }

    public function actualizarc() {
        // Puede causar error "nombre y tipo requeridos"
        $this->form_validation->set_rules('id', 'Id', 'required');
        $this->form_validation->set_rules('name', 'Nombre', 'required|max_length[35]');
        $this->form_validation->set_rules('type', 'Tipo', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'Nombre y tipo requeridos');
        } else {
            $data = $this->input->post();
            $slug = slugify($data['name']);
            $this->load->model('form_model');
            $oldfield = $this->form_model->get_field($data['id']);
            if ($slug != $oldfield->slug && in_array($slug, $this->reservados)) {
                $this->session->set_flashdata('errormsg', 'Campo reservado, usa otro nombre.');
            } else {
                $this->load->model('form_model');
                if( isset($data['borrar']) and $data['borrar']='Borrar' ) {
                    $this->borrarc($data['id_form'], $data['name']);
                    return true;
                }
                if ($this->form_model->updatec()) {
                    $this->session->set_flashdata('infomsg', 'Campo actualizado con éxito.');
                } else {
                    $this->session->set_flashdata('errormsg', 'Error al actualizar el campo.');
                }
            }
        }

        redirect('form/campos/'.$this->input->post('id_form'));
    }

    public function getdatatbr() {
        $fid    = (int) $this->input->post('fid');
        $id     = (int) $this->input->post('id');
        $this->load->model('form_model');
        $data   = $this->form_model->getdatatbr($fid, $id);

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function crearctbr() {
        $this->form_validation->set_rules('name', 'Nombre', 'required|max_length[35]');
        $this->form_validation->set_rules('type', 'Tipo', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'El nombre es requerido.');
        } else {
            $slug = slugify($this->input->post('name'));
            if (in_array($slug, $this->reservados)) {
                $this->session->set_flashdata('errormsg', 'Campo reservado, usa otro nombre.');
            } else {
                $this->load->model('form_model');
                if ($this->form_model->createctbr()) {
                    $this->session->set_flashdata('infomsg', 'Campo agregado con éxito.');
                } else {
                    $this->session->set_flashdata('errormsg', 'Error al agregar el campo.');
                }
            }
        }

        redirect('form/campos/'.$this->input->post('id_form'));
    }

    public function actualizarctbr() {
        $this->form_validation->set_rules('name', 'Nombre', 'required|max_length[35]');
        $this->form_validation->set_rules('type', 'Tipo', 'required');
        if ($this->form_validation->run()==FALSE) {
            $this->session->set_flashdata('errormsg', 'El nombre es requerido.');
        } else {
            $data = $this->input->post();
            $slug = slugify($data['name']);
            if (in_array($slug, $this->reservados)) {
                $this->session->set_flashdata('errormsg', 'Campo reservado, usa otro nombre.');
            } else {
                $this->load->model('form_model');
                if( isset($data['borrar']) and $data['borrar']='Borrar' ) {
                    $this->borrarctbr($data['id'], $data['id_form'], $data['name']);
                    return true;
                }
                if ($this->form_model->updatectbr()) {
                    $this->session->set_flashdata('infomsg', 'Campo actualizado con éxito.');
                } else {
                    $this->session->set_flashdata('errormsg', 'Error al actualizar el campo.');
                }
            }
        }

        redirect('form/campos/'.$this->input->post('id_form'));
    }

    public function borrarctbr($id, $fid, $name) {
        $this->load->model('form_model');
        if ($this->form_model->deletectbr($id, $fid, $name)) {
            $this->session->set_flashdata('infomsg', 'Campo eliminado con éxito.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al eliminar el campo.');
        }

        redirect('form/campos/'.$this->input->post('id_form'));
    }

    public function borrarc($fid, $name) {
        $data = $this->input->post();
        $cid = $data['id'] ?: 0;
        $fid = $data['id_form'] ?: $fid;
        $this->load->model('form_model');
        if ($this->form_model->deletec($fid, $name, $cid)) {
            $this->session->set_flashdata('infomsg', 'Campo eliminado con éxito.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al eliminar el campo.');
        }

        redirect('form/campos/'.$this->input->post('id_form'));
    }

    public function hacercrm() {
        $id_form = $this->input->post('id_form');
        $this->load->model('form_model');
        if ($this->form_model->addCrmFields($id_form)) {
            $this->db->query("UPDATE form SET crm = 1 WHERE id = ?", [$id_form]);
            $this->session->set_flashdata('infomsg', 'Formulario convertido a CRM con éxito.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al convertir el Formulario.');
        }

        redirect('form/campos/'.$id_form);
    }

    public function generar() {
        if (empty($this->input->post('id'))) {
            $this->session->set_flashdata('errormsg', 'Debes generar un formulario desde la sección de campos del mismo.');
            redirect('form');
            exit;
        }
        $this->load->model('form_model');
        if ($this->form_model->generate()) {
            $this->session->set_flashdata('infomsg', 'Formualrio generado.');
        } else {
            $this->session->set_flashdata('errormsg', 'Error al generar el formulario.');
        }

        redirect('form');
    }

    public function getbycam() {
        $this->form_validation->set_rules('cid', 'ID Campaña', 'required');
        if ($this->form_validation->run()==FALSE) {
            $data['error'] = 'El ID de la campaña es requerido.';
        } else {
            $this->load->model("form_model");
            $data = $this->form_model->getbycam($this->input->post("cid"));
        }

        Header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function addtabladep() {
        if (!file_exists(APPPATH . '../files')) mkdir(APPPATH . '../files', 0755, true);
        $data['file_ext_tolower'] = true;
        $data['allowed_types']    = 'csv';
        $data['upload_path']      = APPPATH . '../files';
		$data['file_name']        = md5(uniqid(rand(), true));

        $this->load->library('upload', $data);
        $pdata = $this->input->post();
        if (!$this->upload->do_upload('archivo')) {
            $this->session->set_flashdata('errormsg', $this->upload->display_errors());
		} else {
            $archivo = $this->upload->data('full_path');
            if (($handle = fopen($archivo, "r")) !== FALSE) {
                $this->load->model('form_model');
                // Cantidad de registros que tienen tablas dependientes para el formulario actual
                $hay = $this->form_model->get_number_table_dep($pdata['fid']);
                $fdt = ($hay == 0) ? "" : $hay;
                $tabla = "CREATE TABLE `formd_" . $pdata["fid"] . "_dep" . $fdt . "` (";
                $row = 1;
                $insert = "INSERT IGNORE INTO `formd_" . $pdata["fid"] . "_dep" . $fdt . "` values ";
                $id_form_field = $this->input->post('id_form_field');
                while (($fila = fgetcsv($handle)) !== FALSE) {
                    if ($row == 1) {
                        foreach ($fila as $key => $cell) {
                            $slug = slugify($cell);
                            if ($key == 0) {//creamos el campo y con restricion PK por ser el primero
                                $tabla .= $slug . " varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL, ";
                                $fintabla = "PRIMARY KEY (`$slug`)
                                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                                $data = array('id_form'=>$pdata['fid'], 'type'=>'text', 'name'=>$cell,
                                    'values'=>'', 'depend'=>$hay, 'order'=>'0', 'descen'=>'ddeepp');
                                    if($id_form_field) $data['id_form_field'] = $id_form_field;
                            } else {
                                $tabla .= $slug . " varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, ";
                                $data = array('id_form'=>$pdata['fid'], 'type'=>'text', 'name'=>$cell,
                                    'values'=>'', 'depend'=>'0', 'order'=>'0');
                                if($id_form_field) $data['id_form_field'] = $id_form_field;
                            }
                            if($id_form_field) {
                                $this->form_model->createctbr($data);//Agregamos cada campo creado a la tabla de ese formulario form_#
                            } else {
                                $this->form_model->createc($data);
                            }
                        }
                        $tabla .= $fintabla;//Se crea la tabla dependiente form_#_dep#
                        $this->db->query($tabla);
                    } else {
                        $insert .= "(";
                        foreach ($fila as $cell) {
                            $insert .= "'$cell',";
                        }
                        $insert = rtrim($insert, ",") . "),";
                    }
                    if (fmod($row, 300) == 0) {
                        $insert = rtrim($insert, ",") . ";";
                        $this->db->query($insert);
                        $insert = "INSERT IGNORE into `formd_" . $pdata["fid"] . "_dep" . $fdt . "` values ";
                    }
                    $row++;
                }

                $insert = rtrim($insert, ",") . ";";
                $this->db->query($insert);
                fclose($handle);
                unlink($archivo);
                //Agregamos la columna que falta si es necesario
                $table_name = "formd_".$pdata["fid"]."_dep".$fdt;
                $this->form_model->dep_structure_update($table_name, $pdata['fid']);
                $this->session->set_flashdata('infomsg', 'Tabla creada con éxito.');
            }
        }

        redirect('form/campos/'.$pdata["fid"]);
	}

    public function deltabladep() {
        $data = $this->input->post();
        $id_form_fields = $this->input->post('id_form_fields');
        $depend = $this->input->post('depend');
        $fid = $this->input->post('fid');
        //Validamos que el formulario no tenga registros
        if ($this->db->count_all('formd_'.$fid)==0) {
            //Obtenemos el nombre de la tabla dependiente y obtenemos sus campos
            $n = ($depend == 0) ? "" : $depend;
            $tabla_dep = 'formd_'.$fid.'_dep'.$n;
            $fields = $this->db->list_fields($tabla_dep);
            //Recorremos y eliminamos cada campo que le pertenece
            $this->load->model('form_model');
            if( $id_form_fields == 0 ) { //Eliminamos los campos de form_fields y del formulario
                foreach ($fields as $key => $field) {
                    $this->form_model->deletec($fid, $field);
                }
            } else { //Elimina la tabla dependiente de una tabla relacional
                foreach ($fields as $key => $field) {
                    $this->form_model->deletectbr_by_iff($id_form_fields, $fid, $field);
                }
            }
            //Ahora eliminamos la tabla dependiente
            if ($this->db->query('DROP table '.$tabla_dep)) {
                $this->session->set_flashdata('infomsg', 'Tabla eliminada con éxito.');
            } else {
                $this->session->set_flashdata('errormsg', 'Error al eliminar la tabla.');
            }
        } else {
            $this->session->set_flashdata('errormsg', 'Ya existen registros no puedes eliminar la tabla dependiente.');
        }

        redirect('form/campos/'.$data['fid']);
    }

    public function addtotabladep() {
        $data['file_ext_tolower'] = true;
        $data['allowed_types']    = 'csv|xls|xlsx|ods';
        $data['upload_path']      = APPPATH . '../files';
		$data['file_name']        = md5(uniqid(rand(), true));

        $this->load->library('upload', $data);
        if (!$this->upload->do_upload('archivo')) {
            $this->session->set_flashdata('errormsg', $this->upload->display_errors());
		} else {
            $ext = ltrim($this->upload->data('file_ext'), '.');
            switch ($ext) {
                case 'ods':
                    $reader = ReaderFactory::create(Type::ODS);
                    break;
                case 'xls':
                    $reader = ReaderFactory::create(Type::XLS);
                    break;
                case 'xlsx':
                    $reader = ReaderFactory::create(Type::XLSX);
                    break;

                default:
                    $reader = ReaderFactory::create(Type::CSV);
                    break;
            }
            $reader->open($this->upload->data('full_path'));
            $pdata = $this->input->post();
            if (empty($pdata['tipo']) || empty($pdata["fid"])) return ['error' => 'Falta información'];
            $query = $this->db->query("SELECT *
            FROM(
                SELECT depend, `name`, id_form, descen FROM form_fields
                UNION
                SELECT depend, `name`, id_form, descen FROM form_fields_tbr
            ) a
            WHERE a.name = ? 
            AND id_form = ?
            AND descen = 'ddeepp'", [$pdata['did'], $pdata['fid']]);
            $dep = $query->row()->depend;
            $fdt = ($dep == 0) ? "" : $dep;
            if ($pdata['tipo']==2) $this->db->truncate("formd_" . $pdata["fid"] . "_dep" . $fdt);
            //En caso de que la tabla no tenga la conlumna active_system_row, se la agregamos
            $table_name = "formd_".$pdata["fid"]."_dep".$fdt;
            $this->load->model('form_model');
            $this->form_model->dep_structure_update($table_name, $pdata['fid']);
            foreach ($reader->getSheetIterator() as $sheet) {
                $rowcount = 1;
                $insert = "INSERT IGNORE INTO `formd_" . $pdata["fid"] . "_dep" . $fdt . "` values ";
                foreach ($sheet->getRowIterator() as $row) {
                    if ($rowcount > 1) {
                        $insert .= "(";
                        foreach ($row as $cell) {
                            $insert .= "'$cell',";
                        }
                        $insert .= '1,';
                        $insert = rtrim($insert, ",") . "),";
                    }
                    if (fmod($rowcount, 300) == 0) {
                        $insert = rtrim($insert, ",") . ";";
                        $this->db->query($insert);
                        $insert = "INSERT IGNORE INTO `formd_" . $pdata["fid"] . "_dep" . $fdt . "` values ";
                    }
                    $rowcount++;
                }
                $insert = rtrim($insert, ",") . ";";
                if ($insert != "INSERT IGNORE INTO `formd_" . $pdata["fid"] . "_dep" . $fdt . "` values ;") {
                    $this->db->query($insert);
                }
                unlink($this->upload->data('full_path'));
                $reader->close();
            }
        }

        redirect('form/campos/'.$pdata["fid"]);
    }

    public function ddeepp() {
        $this->load->model('form_model');
        $ret = $this->form_model->getddeepp($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($ret);
    }

    public function getdep() {
        $data = $this->input->post();
        if (empty($data["parent"]) || empty($data["tabla"])) {
            $ret = "";
        } else {
            $this->load->model('form_model');
            $ret = $this->form_model->getdep($data);
        }

        Header('Content-Type: application/json');
        echo json_encode($ret);
    }

    //ADMINISRACION DE FORMULARIO DEPENDIENTE DE ASIGNACION
    //listamos los dep asig
    public function depasig_list() {
        $this->load->model('form_model');
        $id_form = $this->input->post('id_form');
        $registros = $this->form_model->depasig_list($id_form);

        Header('Content-Type: application/json');
        echo json_encode($registros);
    }

    //Listamos los campos del formulario y activador seleccionado
    public function depasig_fields() {
        $this->load->model('form_model');
        $id_form  = $this->input->post('id_form');
        $slug_key = $this->input->post('activator');
        $table = $this->form_model->get_name_table_depen($id_form, $slug_key);
        if( $table !== false ) {
            $resp = $this->form_model->depasig_fields($table);
        } else {
            $resp = ['error' => 'Error, no se pueden obtener los campos de esta tabla dependiente'];
        }

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function depasig_save() {
        $this->load->model('form_model');
        //Validamos la información entrante
        $this->form_validation->set_rules('activador',  'Activador',    'required');
        $this->form_validation->set_rules('campo',      'Campo',        'required');
        $this->form_validation->set_rules('copia',      'Copia',        'required');
		if ($this->form_validation->run() == FALSE){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $resp = ["error" => $errors[$fields[0]]];
		}
        else {
            //Comprobamos si existe la tabla
            $id_form = (int) $this->input->post('id_form');
            $id = (int) $this->input->post('id');
            $table_name = 'formd_'.$id_form.'_depasign';
            $table_ok = true;
            if( !$this->form_model->table_exist($table_name) ) {
                //Creamos la tabla
                $table_ok = $this->form_model->depasig_createtable($table_name);
            }
            //Guardamos el registro
            if( $table_ok === true ) {
                $data = [
                    'activador' => $this->input->post('activador'),
                    'campo' => $this->input->post('campo'),
                    'copia' => $this->input->post('copia'),
                ];
                $resp = $this->form_model->depasig_save($id_form, $data, $id);
            }
            else
                $resp = ['error' => 'Error: No se pueden guardar los datos, tabla inexistente'];
        }
        
        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function depasig_delete() {
        $this->load->model('form_model');
        $id_form = (int) $this->input->post('id_form');
        $id = (int) $this->input->post('id');
        $resp = $this->form_model->depasig_delete($id_form, $id);
        
        Header('Content-Type: application/json');
        echo json_encode($resp);
    }
    //ADMINISRACION DE FORMULARIO DEPENDIENTE DE ASIGNACION

    //ADMINISTRACION DE FORMULARIOS DEPENDIENTES
    public function datadep() {
        $this->load->model('form_model');
        $data['campaigns'] = $this->datos_model->getCampanas();
        $data['title']   = 'Campos de formulario';
        $data['jscript'] = 'config/form_datadep';

        $this->armado->mostrar(array(
            'view' => 'config/form_datadep',
            'data' => $data,
        ));
    }

    public function datadep_loadforms() {
        $this->load->model('form_model');
        $id_campaign = (int) $this->input->post('id_campaign');        
        $forms = $this->form_model->loadforms($id_campaign);

        Header('Content-Type: application/json');
        echo json_encode($forms);
    }

    public function datadep_loaddepend() {
        $this->load->model('form_model');
        $id_form = (int) $this->input->post('id_form');
        $depend = $this->form_model->loaddepend($id_form);

        Header('Content-Type: application/json');
        echo json_encode($depend);
    }

    public function datadep_list() {
        $this->load->model('form_model');
        $id_form  = $this->input->post('id_form');
        $slug_key = $this->input->post('slug_key');
        $pag = (int)$this->input->post('pag');
        $rpp = (int)$this->input->post('rpp');
        $bus = $this->input->post('bus');
        $pag = (empty($pag)) ? 0  : $pag;
        $rpp = (empty($rpp)) ? 20 : $rpp;
        $resp = [
            'rows' => [],
            'head' => [],
            'pk'   => '',
        ];
        $table = $this->form_model->get_name_table_depen($id_form, $slug_key);
        if( $table !== false) {
            $data['table'] = $table;
            $array = [
                'table'     => $table,
                'pag'       => $pag,
                'rpp'       => $rpp,
                'bus'       => $bus,
                'slug_key'  => $slug_key,
            ];
            $this->load->model('form_model'); $resp =  $this->form_model->dep_structure_update($table, $id_form);
            $datadep = $this->form_model->datadep_list($array);
            $resp = $datadep;
            $resp['head'] = $this->db->list_fields($table);
            $resp['pk'] = $resp['head'][0];
        }

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function datadep_save() {
        $this->load->model('form_model');
        //Obtenemos los datos necesarios para obtener el nombre de la tabla
        $id_form        = $this->input->post('param___id_form');
        $slug_key       = $this->input->post('param___slug_key');
        $accion         = $this->input->post('param___accion');
        $original_key   = $this->input->post('param___original_key');
        $data_form      = $this->input->post();
        $data_form['active_system_row'] = ($this->input->post('active_system_row') == 1) ? '1' : '0';
        //Eliminamos esos valores para que solo queden los registros a insertar
        unset($data_form['param___id_form']);
        unset($data_form['param___slug_key']);
        unset($data_form['param___original_key']);
        unset($data_form['param___accion']);
        //Continuamos con el proceso de guardado
        $table = $this->form_model->get_name_table_depen($id_form, $slug_key);
        if( $table !== false ) {
            if( $accion == 'Agregar' ) {
                $resp = $this->form_model->datadep_insert($table, $data_form);
            } else if( $accion == 'Actualizar' ) {
                $where = [$slug_key => $original_key];
                $resp = $this->form_model->datadep_update($table, $data_form, $where);
            }
        }
        else {
            $resp = ['error' => 'No tienes permisos para modificar esta información'];
        }

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function datadep_delete() {
        $this->load->model('form_model');
        $id_form        = $this->input->post('id_form');
        $slug_key       = $this->input->post('slug_key');
        $key_value      = $this->input->post('key_value');
        $table = $this->form_model->get_name_table_depen($id_form, $slug_key);
        if( $table !== false ) {
            $where = [$slug_key => $key_value];
            $resp = $this->form_model->datadep_delete($table, $where);
        }
        else {
            $resp = ['error' => 'No tienes permisos para modificar esta información'];
        }

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }
    //Administracion de formularios dependientes

    //Configuracion de campos calculados
    public function calc_field_list() {
        $fid = (int) $this->input->post('id_form');
        $this->load->model('form_model');
        $resp['campos_detalle']     = $this->form_model->get_fields_form_global($fid);
        $resp['campos_calculados']  = $this->form_model->calc_field_list($fid);

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function calc_field_save() {
        $fid = $this->input->post('id_form');
		$this->form_validation->set_rules('id',  		    "'ID'",             'required|integer|max_length[100]');
		$this->form_validation->set_rules('id_form',  		"'ID Form'",        'required|integer|max_length[100]');
        $this->form_validation->set_rules('activator',      "'Activador'",      'required|regex_match[/^(tbr-)?\d+$/]');
		$this->form_validation->set_rules('field_r',  		"'Campo Resultado'",'required|regex_match[/^(tbr-)?\d+$/]');
		$this->form_validation->set_rules('field_a',  		"'Campo A'",        'required|regex_match[/^(tbr-)?\d+$/]');
		$this->form_validation->set_rules('operator',  		"'Operador'",       'required|max_length[1]|in_list[+,-,/,*]');
		$this->form_validation->set_rules('field_b',  		"'Campo B'",        'required|regex_match[/^(tbr-)?\d+$/]');
		if ($this->form_validation->run() == FALSE){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $resp = [
                "error" => $errors[ $fields[0] ]
            ];
		}
        else {
            $this->load->model('form_model');
            $result = $this->form_model->calc_field_save( $this->input->post() );
            if( $result === FALSE )
                $resp = 'Error: no se pudo guardar la información';
            else
                $resp = $result;
        }
        if( $resp !== FALSE ) {
            if (file_exists(APPPATH.'/views/form/form_'.$fid.'.php'))
                unlink(APPPATH.'/views/form/form_'.$fid.'.php');
        }

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function calc_field_delete() {
        $id = (int) $this->input->post('id');
        $this->load->model('form_model');
        $row = $this->form_model->calc_fields_find($id);
        $fid = ( isset($row->id_form) ) ? $row->id_form : 0;
        if( $fid > 0 ) {
            $resp = $this->form_model->calc_field_delete($id);
            if( $resp !== FALSE ) {
                if (file_exists(APPPATH.'/views/form/form_'.$fid.'.php'))
                unlink(APPPATH.'/views/form/form_'.$fid.'.php');
            }
        } else {
            $resp = "Registro eliminado";
        }
        
        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function get_operations_fcf() {
        $this->load->model('form_model');
        $id_form_field_global = $this->input->post('id_form_field_global');
        //obtenemos el formulario del activador
        $form_field = $this->form_model->get_field_form_global($id_form_field_global);
        $id_form = (isset( $form_field->id_form )) ? $form_field->id_form : 0;
        if( $id_form > 0 ) {
            $resp = $this->form_model->get_op_calc_field($id_form, $id_form_field_global);
        } else {
            $resp['error'] = 'Error: Parametros incorrectos';
        }

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }
    //Configuracion de campos calculados

    public function oct_list() {
        $this->load->model("form_model");
        $id_form = (int) $this->input->post('id_form');
        $oct = $this->form_model->oct_list($id_form);
        $resp = [
            'oct' => $oct,
        ];

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function oct_save() {
        $data = [];
        $this->load->model("form_model");
        $this->form_validation->set_rules('id',  		"'ID'",         'required|integer');
		$this->form_validation->set_rules('id_form',  	"'ID Form'",    'required|integer');
        $this->form_validation->set_rules('field_r',    "'Campo R'",    'required|min_length[1]');
		$this->form_validation->set_rules('field_a',  	"'Campo A'",    'required|min_length[1]');
		$this->form_validation->set_rules('operator',  	"'Operador'",   'required|max_length[3]|in_list[+,-,/,*,.,N/A]');
		$this->form_validation->set_rules('field_b',  	"'Campo B'",    'required|min_length[1]');
		$this->form_validation->set_rules('order',  	"'Orden'",      'required|integer');
		if ($this->form_validation->run() == FALSE){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $resp = [
                "error" => $errors[ $fields[0] ]
            ];
		}
        else {
            $this->load->model('form_model');
            $id = (int) $this->input->post('id');
            if($this->input->post('field_a') != '0') $_POS['custom_a'] = '';
            if($this->input->post('field_b') != '0') $_POS['custom_b'] = '';
            $operator   = $this->input->post('operator');
            $field_r    = $this->input->post('field_r');
            $field_a    = $this->input->post('field_a');
            $custom_a   = $this->input->post('custom_a');
            $field_b    = $this->input->post('field_b');
            $custom_b   = $this->input->post('custom_b');
            if( $field_a != '0' ) $custom_a = '';
            if( $field_b != '0' ) $custom_b = '';
            if( $operator == 'N/A' ) {
                $custom_b = '';
                $field_b = 'N/A';
            }
            if( $field_b == 'N/A' ) {
                $custom_b = '';
                $operator = 'N/A';
            }
            $data = [
                'id_form'   => $this->input->post('id_form'),
                'order'     => $this->input->post('order'),
                'operator'  => $operator,
                'field_r'   => $field_r,
                'field_a'   => $field_a,
                'custom_a'  => $custom_a,
                'field_b'   => $field_b,
                'custom_b'  => $custom_b,
            ];
            $result = $this->form_model->oct_save( $id, $data );
            if( $result === FALSE )
                $resp = 'Error: no se pudo guardar la información';
            else
                $resp = $result;
        }

        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function oct_delete() {
        $id = (int) $this->input->post('id');
        $this->load->model('form_model');
        $resp = $this->form_model->oct_delete($id);
        
        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    // Filtros dependientes
    public function ftr_ddeepp() {
        $this->load->model('form_model');
        $ret = $this->form_model->get_data_dep_filter($this->input->post());

        Header('Content-Type: application/json');
        echo json_encode($ret);
    }

    public function ftr_list() {
        $this->load->model("form_model");
        $id_form = (int) $this->input->post('id_form');
        $ftr = $this->form_model->ftr_list($id_form);

        Header('Content-Type: application/json');
        echo json_encode($ftr);
    }

    public function ftr_save() {
        $success = true;
        $message = '';
        $data = [];
        $this->load->model("form_model");
        // Validamos los datos de entrada
        $this->form_validation->set_rules('id',  		        "'IDs'",                'required|integer');
		$this->form_validation->set_rules('id_form',  	        "'ID Form'",            'required|integer');
        $this->form_validation->set_rules('activator',          "'Activador'",          'required');
		$this->form_validation->set_rules('field_to_filter',    "'Campo a filtrar'",    'required');
        $this->form_validation->set_rules('field_to_compare',  	"'Campo a comparar'",   'required');
        if( !empty( $this->input->post('union_field_a') ) || !empty( $this->input->post('union_field_b') ) ) {
            $this->form_validation->set_rules('union_field_a',  "'Campo A Tabla Union'",'required');
            $this->form_validation->set_rules('union_field_b',  "'Campo B Tabla Union'",'required');
        }
		if ($this->form_validation->run() == FALSE){
            $errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $success = false;
            $message = $errors[ $fields[0] ];
		}
        // Guardamos los datos del registro
        if( $success ) {
            $this->load->model('form_model');
            $id = (int) $this->input->post('id');
            $row_data = [
                'id_form'           => $this->input->post('id_form'),
                'activator'         => $this->input->post('activator'),
                'field_to_filter'   => $this->input->post('field_to_filter'),
                'field_to_compare'  => $this->input->post('field_to_compare'),
            ];
            $result = $this->form_model->ftr_save( $id, $row_data );
            if( $result === FALSE ) {
                $success = false;
                $message = 'Error: no se pudo guardar la información';
            } else {
                $form_filter_dep_id = ($id == 0) ? $result : $id;
                $message = 'Los datos se guardaron correctamente';
            }
        }
        // OPCIONES AVANZADAS
        // Verificamos si estan intentando subir un archivo de tabla union
        if( $success ) {
            if( !empty( $_FILES['archivo_tbu']['name'] ) ) {
                $resp = $this->lectura_de_archivo('archivo_tbu');
                if( isset($resp['error']) ) {
                    $success = false;
                    $message = $resp['error'];
                } else {
                    $data = $resp;
                    $n = $resp['data']['n'];
                    $row_data = [
                        'id_form'       => $this->input->post('id_form'),
                        'union_table'   => $n
                    ];
                    $result = $this->form_model->ftr_save( $form_filter_dep_id, $row_data );
                    if( !$result ) {
                        $success = false;
                        $message = 'Error: no se pudo guardar la información';
                    }
                }
            } else { // Validamos y quiza solo tengamos que actualizar los datos de las opciones avanzadas
                $row_data = [
                    'union_field_a'  => $this->input->post('union_field_a'),
                    'union_field_b'  => $this->input->post('union_field_b'),
                ];
                $result = $this->form_model->ftr_save( $form_filter_dep_id, $row_data );
                if( !$result ) {
                    $success = false;
                        $message = 'Error: no se pudo guardar la información';
                }
            }
        }
        //Obtenemos los datos del renglon en curso
        if( $success ) {
            $row = $this->form_model->ftr_one( $form_filter_dep_id );
            $data['row'] = $row;
        }
        if( $success )
            $message = 'La información se guardo correctamente';

        Header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    public function ftr_delete_table_union() {
        $success = true;
        $message = '';
        $data    = [];
        // Validamos los datos de entrada
        $this->form_validation->set_rules('id',  		        "'ID'",                'required|integer');
        $this->form_validation->set_rules('id_form',  	        "'ID Form'",            'required|integer');
        $this->form_validation->set_rules('union_table',  	"'union_table'",        'required');
        if ($this->form_validation->run() == FALSE){
            $errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $success = false;
            $message = $errors[ $fields[0] ];
        }
        //Procedemos a la eliminacion de la tabla
        if( $success ) {
            $data = [
                'id'            => (int) $this->input->post('id'),
                'id_form'       => (int) $this->input->post('id_form'),
                'union_table'   => (int) $this->input->post('union_table'),
            ];
            $this->load->model('form_model');
            $result = $this->form_model->ftr_delete_table_union( $data );
            $success = $result['success'];
            $message = $result['message'];
        }
        //Obtenemos los datos del renglon en curso
        if( $success ) {
            $row = $this->form_model->ftr_one( $data['id'] );
            $data['row'] = $row;
        }

        Header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    public function ftr_delete() {
        $id = (int) $this->input->post('id');
        $this->load->model('form_model');
        $resp = $this->form_model->ftr_delete($id);
        if( $resp['success'] ) {
            $resp = $resp['message'];
        } else {
            $resp = ['error' => $resp['message']];
        }
        
        Header('Content-Type: application/json');
        echo json_encode($resp);
    }

    private function lectura_de_archivo($archivo) {
        $success = true;
        $message = '';
        $data = [];
        // Creamos la carpeta de archivos si no existe
        if (!file_exists(APPPATH . '../files')) mkdir(APPPATH . '../files', 0755, true);
        // Configuramos la subida del archivo
        $file_data['file_ext_tolower'] = true;
        $file_data['allowed_types']    = 'csv|xls|xlsx|ods';
        $file_data['upload_path']      = APPPATH . '../files';
		$file_data['file_name']        = md5(uniqid(rand(), true));
        $this->load->library('upload', $file_data);
        if (!$this->upload->do_upload($archivo)) {
            $this->session->set_flashdata('errormsg', $this->upload->display_errors());
        } else {
            $data['op1'] ='inicia';
            $ext = ltrim($this->upload->data('file_ext'),'.');
            switch ($ext) {
                case 'ods':  $reader = ReaderFactory::create(Type::ODS);  break;
                case 'xls':  $reader = ReaderFactory::create(Type::XLS);  break;
                case 'xlsx': $reader = ReaderFactory::create(Type::XLSX); break;
                default:     $reader = ReaderFactory::create(Type::CSV);  break;
            }
            $reader->open($this->upload->data('full_path'));
            if( !empty($this->input->post('id_form')) ) {
                $data['op2'] ='lectura de archivo';
                $id_form = $this->input->post('id_form');
                //Obtenemos el numero que tendra la tabla union
                $sql = "SELECT IF( COALESCE(MAX(union_table), '') = '', 0, MAX(union_table)+1) AS n
                    FROM form_filter_dep
                    WHERE id_form = ?;";
                $query = $this->db->query($sql, [$id_form]);
                $n = $query->row()->n;
                $data['n'] = $n;
                $tabla = 'formd_'.$id_form.'_tb_union'.$n;
                $tabla_create = "CREATE TABLE `".$tabla."` (";
                //Recorremos las hojas del documento
                foreach ($reader->getSheetIterator() as $sheet) {
                    $rowcount = 1;
                    $insert = "INSERT IGNORE INTO `".$tabla."` values ";
                    //Recorremos cada renglon de la hoja del documento
                    foreach ($sheet->getRowIterator() as $row) { 
                        //Primer fila, encabezados: Se crea la estructura de la tabla.
                        if ($rowcount == 1) { 
                            foreach ($row as $key => $cell) {//Creamos cada campo de la tabla dependiente
                                $slug = slugify($cell);
                                if ($key == 0) {//creamos el campo y con restricion PK por ser el primero
                                    $tabla_create .= $slug . " varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL, ";
                                    $fintabla = "PRIMARY KEY (`$slug`)
                                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                                } else {
                                    $tabla_create .= $slug . " varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, ";
                                }
                            }
                            $tabla_create .= $fintabla;
                            $this->db->query($tabla_create);
                        } else { // Primer en adelante, datos: Ahora se agregaran los registros
                            $insert .= "(";
                            foreach ($row as $cell) {
                                $insert .= "'$cell',";
                            }
                            $insert = rtrim($insert, ",") . "),";
                        }
                        if (fmod($rowcount, 300) == 0) {
                            $insert = rtrim($insert, ",") . ";";
                            $this->db->query($insert);
                            $insert = "INSERT IGNORE INTO `".$tabla."` values ";
                        }
                        $rowcount++;
                    }
                    $insert = rtrim($insert, ",") . ";";
                    $this->db->query($insert);
                    unlink($this->upload->data('full_path'));
                    $reader->close();
                }
            }
        }

        return [
            'success' => $success,
            'message' => $message,
            'data' => $data
        ];
    }
}

?>
