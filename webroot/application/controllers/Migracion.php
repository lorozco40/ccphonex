<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migracion extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model('migracion_model');
    }

    public function index() {
        $data['title']      = "Migracion";
        $data['jscript']    = 'config/migracion';
        $this->armado->mostrar(array(
            'view' => 'config/migracion',
            'data' => $data,
        ));
    }

    public function hard_reset() {
        $json = [];
        $fin  = false;
        if($this->udata['id'] == 1) {
            $no_reset   = ['break', 'catalogs', 'ses_ab', 'menu', 'user', 'user_data'];

            //TABLAS
            $tablas = $this->migracion_model->get_tables();
            $n = count($tablas);
            $json['tablas']     = $tablas;
            $json['n_tablas']   = $n;

            //DESHABILITAMOS LA VALIDACION DE LLAVES FORANEAS
            $this->migracion_model->check_fk(0);

            //SELECCIONAMOS LAS TABLAS A ELIMINAR Y LAS ELIMINAMOS
            $tablas_delete = $this->tablesDelete($json['tablas']);
            $this->migracion_model->delete_tables($tablas_delete);
            $n = count($tablas_delete);
            $json['tablas_del'] = $tablas_delete;
            $json['n_del']      = $n;

            //LIMPIEZA DE REGISTROS
            $this->migracion_model->reset_tables($no_reset);

            //INSERTAMOS VALORES DEFAULT
            $this->migracion_model->default_values();
            $this->migracion_model->check_fk(1);

            //TABLAS RESULTADO
            $tablas              = $this->migracion_model->get_tables(true);
            $n                   = count($tablas);
            $json['resultado']   = $tablas;
            $json['n_resultado'] = $n;
            $json['fk']          = $this->migracion_model->show_check_fk();
            unlink(APPPATH . 'views/form/form_*.php');
            $fin = true;
        } else {
            $json['error'] = "Error: No estas autorizado para realizar esta acción";
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        if ($fin) $this->db->query("TRUNCATE TABLE ses_ab");
    }

    private function tablesDelete($tables) {
        $tables_delete  = [];
        $expresiones    = [];
        //Tablas a eliminar
        $expresiones[] = '/^disp_\d+.*$/';
        $expresiones[] = '/^formd_\d+.*$/';
        $expresiones[] = '/^warate_\d+.*$/';

        foreach( $tables as $table ) {
            foreach ($expresiones as $expresion) {
                if (preg_match($expresion, $table)) {
                    $tables_delete[] = $table;
                    break;
                }
            }
        } 

        return $tables_delete;
    }
}



?>
