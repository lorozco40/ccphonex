<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Campanas extends MY_Controller
{

    private $tipoDato = [
        'acw'       => 'int',
        'prefijo'   => 'int',
        'tlocal'    => 'float',
        'tin'       => 'float',
        'tcell'     => 'float',
        'outboun'   => 'int',
        'licencias' => 'int',
        'expira'    => 'date',
    ];

    public function __construct(){
        parent::__construct();
        $this->load->model("campanas_model");
    }

    public function index() {
        $data['title']      = 'Campañas';
        $data['atributos']  = $this->atributos_permitidos('full');
        $data['jscript']    = 'config/campanas';

        $datos = array(
            'view' => 'config/campanas',
            'data' => $data,
        );
        $this->armado->mostrar($datos);
    }

    public function lista() {
        Header('Content-Type: application/json');
        echo json_encode($this->campanas_model->lista($this->input->post()));
    }

    public function guardar() {
        $this->form_validation->set_rules('id', 'ID', 'required');
        $this->form_validation->set_rules('name', 'Nombre', 'required');
        if ($this->form_validation->run()==FALSE) {
            $res = ['error'=>'información incorrecta o incompleta'];
        } else {
            $res = $this->campanas_model->guardar($this->input->post());
        }

        Header('Content-Type: application/json');
        echo json_encode($res);
    }

    public function traehorario() {
        Header('Content-Type: application/json');
        echo json_encode($this->campanas_model->get_horario($this->input->post("id")));
    }

    public function act_horario() {
        Header('Content-Type: application/json');
        echo json_encode($this->campanas_model->acthorario($this->input->post()));
    }

    public function atrilista() { // Listado de atributos de una campaña
        Header('Content-Type: application/json');
        echo json_encode($this->campanas_model->atrilista($this->input->post()));
    }

    public function atriguardar() { // Guardar todos los atributos de una campaña
        Header('Content-Type: application/json');
        echo json_encode($this->campanas_model->atriguardar($this->input->post()));
    }

    //Agrega un nuevo atributo dinamico, siempre que este no exista aun para la campana seleccionada
    public function atributo_agregar() { // Agrega un nuevo atributo
		$this->load->library('form_validation');
        $list = $this->atributos_permitidos('text');

		$this->form_validation->set_rules('atributo',  		"'Atributo'",  	'required|min_length[1]|max_length[20]|in_list['.$list.']');
        $this->form_validation->set_rules('id_campaign',    "'Capania'",    'required|numeric|integer|greater_than_equal_to[1]');
		$this->form_validation->set_rules('valor',      	"'Valor'",      'required|min_length[1]|max_length[250]');


		if ($this->form_validation->run() == FALSE){
			$errors = $this->form_validation->error_array();
            $fields = array_keys($errors);
            $response = [
                "error" => $errors[ $fields[0] ]
            ];
		}
		else{
            $response = $this->campanas_model->atriagregar($this->input->post());
		}

        Header('Content-Type: application/json');
        echo json_encode($response);
    }

    //Muestra la lista de atributos dinamicos
    public function atributos_campana() {
        $id_campaign = (int)$this->input->post('id_campaign');
        //obtenemos los atributos fijos y los dinamicos
        $atributos_campana      = $this->campanas_model->atrilista(['id' => $id_campaign]);
        $atributos              = $atributos_campana['attrs'];
        $response["atributos"]  = $this->atributos_permitidos('filter', $atributos);
        $response["tipoDato"]   = $this->tipoDato;

        Header('Content-Type: application/json');
        echo json_encode($response);
    }

    //Elimina un atributo dinamico
    public function atributo_eliminar() {
        $id = (int)$this->input->post('id');
        //eliminamos el atributo
        $response = $this->campanas_model->atributo_eliminar($id);

        Header('Content-Type: application/json');
        echo json_encode($response);
    }

    //Actualizamos los valores de la lista de atributos dinamicos
    public function atributos_guardar(){
        $recaltarifa = $this->input->post('recaltarifa');
        $id_campaign = $this->input->post('id_campaign');
        //creamos el array de datos
        $datos = [];
        foreach($this->input->post() as $id => $valor){
            if( !in_array($id, ['recaltarifa', 'id_campaign'])){
                $datos[] = [
                    'id'    => $id,
                    'valor' => $valor,
                ];
            }
        }

        if( count($datos) > 0 )
            $response = $this->campanas_model->atributos_guardar($datos, $id_campaign, $recaltarifa);
        else
            $response = ["error" => "Error: No hay datos que actualizar"];

        Header('Content-Type: application/json');
        echo json_encode($response);
    }

    private function atributos_permitidos($tipo = 'full', $filtro = []) {
        $full = [
            'acw'       => 'After Call Work',
            'expira'    => 'Expira',
            'licencias' => 'Licencias',
            'outbound'  => 'Outbound',
            'prefijo'   => 'Prefijo',
            'tcell'     => 'Tarifa celular',
            'tin'       => 'Tarifa entrante',
            'tlocal'    => 'Tarifa local',
        ];
        switch( $tipo ){
            case 'full':
                $response = $full;
            break;
            case 'text':
                $array_text = '';
                foreach ($full as $key => $value){
                    $array_text .= $key.',';
                }
                $response = $array_text;
            break;
            case 'filter':
                $aux = [];
                foreach($filtro as $key => $value){
                    $aux[$key] = [
                        'id'        => $value->id,
                        'atributo'  => $value->atributo,
                        'valor'     => $value->valor,
                        'text'      => $full[$value->atributo],
                    ];
                }
                $response = $aux;
            break;
            default:
                $response = $full;
            break;
        }

        return $response;
    }

}
