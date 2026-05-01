<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Armado
{

    private $ci;

    public function __construct() {
        $this->ci = &get_instance();
    }

    private function header($datos = array()) {
        $errormsg = $this->ci->session->flashdata('errormsg');
		if ($errormsg) $datos['errormsg'] = $errormsg;
        $infomsg = $this->ci->session->flashdata('infomsg');
		if ($infomsg) $datos['infomsg'] = $infomsg;
        $datos['title'] = (empty($datos['title'])) ? "" : "".$datos['title'] ;
        $datos['css'] = (!empty($this->ci->udata['tema'])) ? $this->ci->udata['tema'] : "default";
        $datos['bago_url'] = getenv('BAGO_JS_URL');
        if ($datos['tmpl'] == 'empty') return $this->ci->load->view('header', $datos, true);
        if ($this->ci->udata) {
            $this->ci->load->model('consola_model');
            $this->ci->load->model('campanas_model');
            $datos['licenciasxcampana'] = $this->ci->campanas_model->licencias_por_campana($this->ci->udata["campanas"]);
            $datos['agente'] = $this->ci->consola_model->getAgente();
            $datos['uname'] = trim($datos['agente']['name'].' '.$datos['agente']['last']);
            $datos['seg'] = uri_string();
            $datos['menuops'] = $this->getMenu();
        }

        return $this->ci->load->view('header', $datos, true);
    }

    public function mostrar($datos = array()) {
        if (empty($datos)) redirect();
        $dat             = (isset($datos['data']))       ? $datos['data']       : [] ;
        $view_main       = (isset($datos['view']))       ? $datos['view']       : "empty";
        $view_left       = (isset($datos['view_left']))  ? $datos['view_left']  : "";
        // recuerda setear el template cuando es a dos columnas, 3 columnas o vacío (empty)
        $template        = (isset($datos['template']))   ? $datos['template']   : "one_col";
        if ($template == 'empty') { $this->ci->udata['tema'] = 'claro'; }
        $dat['tmpl']     = $template;
        $data['header']  = $this->header($dat);
        $data['colleft'] = (!empty($view_left)) ? $this->ci->load->view($view_left, "", true) : "";
        $data['nav']     = "";
        if ($this->ci->udata === null || (isset($this->ci->udata['permisoSec']) &&
            in_array('nav', $this->ci->udata['permisoSec']) && $template != 'empty')) {
            $data['nav'] = $this->ci->load->view("nav", "", true);
        }
        $data['colmain'] = $this->ci->load->view($view_main, "", true);
        $data['footer']  = $this->ci->load->view("footer", "", true);
        $this->ci->load->view($template, $data);
    }

    // Preparado para un máximo de 3 niveles de profundidad
    private function getMenu() {
        $this->ci->load->model('menu_model');
        $menu = $this->ci->menu_model->menuUsuario();
        $ret = [];
        foreach ($menu as $menux) {
            if ($menux->nivel == 1 && empty($menux->submenu)) {
                $ret[$menux->permiso] = $menux->etiqueta;
            } elseif($menux->nivel == 1) {
                $ret[$menux->etiqueta] = [];
            } elseif ($menux->nivel == 2 && empty($menux->submenu)) {
                $ret[$menux->padre][$menux->permiso] = $menux->etiqueta;
            } elseif ($menux->nivel == 2) {
                $ret[$menux->padre][$menux->etiqueta] = [];
            } else {
                $index = $menu[$menu[$menux->pertenece]->pertenece]->etiqueta;
                $ret[$index][$menux->padre][$menux->permiso] = $menux->etiqueta;
            }
        }
        // filtrar niveles 2 vacíos
        foreach ($ret as $key1 => $lvl1) {
            if (is_array($lvl1)) {
                foreach ($lvl1 as $key2 => $lvl2) {
                    if (is_array($lvl2)) {
                        if (count($lvl2) == 0) {
                            // eliminar este nivel
                            unset($ret[$key1][$key2]);
                        }
                    }
                }
            }
        }
        // filtrar niveles 1 vacíos
        foreach ($ret as $key1 => $lvl1) {
            if (is_array($lvl1)) {
                if (count($lvl1) == 0) {
                    // eliminar este nivel
                    unset($ret[$key1]);
                }
            }
        }

        return $ret;
    }

}
