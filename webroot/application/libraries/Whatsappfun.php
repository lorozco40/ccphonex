<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Whatsappfun
{

    private $ci;
    private $especiales;

    public function __construct() {
        $this->ci = &get_instance();
        $this->especiales = [
            "{nada}"    =>"",
            "{espacio}" =>" ",
            "{coma}"    =>",",
            "{punto}"   =>".",
            "{true}"    =>true,
            "{false}"   =>false,
        ];
    }

    /* Generales:
     * Tipos de variables: string, int, bool
     * Tipos de campo: permanent, temporal, secure, file
     */

    /**
     * Guardar una variable en json tabla whatsapp_cont_data
     *
     * @param array $par cid(req), var(req), val(req), tip(def=string), cam(def=permanent)
     * @return bool
     */
    public function guardaVar($par) {
        $par = [
            "cid" => (empty($par['cid'])) ? "" : $par['cid'], // Whatsapp contact ID
            "var" => (empty($par['var'])) ? "" : $par['var'],
            "val" => ($par['val'] === null) ? "" : $par['val'],
            "tip" => (empty($par['tip'])) ? "string" : $par['tip'],
            "cam" => (empty($par['cam'])) ? "permanent" : $par['cam'],
        ];
        if (empty($par['cid']) || empty($par['var'])) { return false; }
        $var = $par['var'];
        $data = $this->traeCampo($par['cid'], $par['cam']);
        if (empty($data->$var)) $data->$var = (object)[];
        $data->$var->valor = $par['val'];
        $data->$var->tipo  = $par['tip'];

        return $this->guardaCampo($data, $par['cid'], $par['cam']);
    }

    /**
     * Traer una variable de la tabla whatsapp_cont_data
     *
     * @param string $var Nombre de la variable
     * @param int $cid ID del contacto de whatsapp
     * @param string $cam columna campo de la tabla default permanent
     * @return object nombre, valor, tipo
     */
    public function traeVar($var, $cid, $cam = "permanent") {
        $data = $this->traeCampo($cid, $cam);

        return (empty($data->$var)) ? (object)['nombre'=>$var,'valor'=>'','tipo'=>'string'] : $data->$var;
    }

    /**
     * Borra una variable completa de la tabla whatsapp_cont_data
     *
     * @param string $var Nombre de la variable
     * @param int $cid ID del contacto de whats
     * @param string $cam Columna campo de la tabla default permanent
     * @return bool
     */
    public function borraVar($var, $cid, $cam = "permanent") {
        $ret  = true;
        $data = $this->traeCampo($cid, $cam);
        if (isset($data->$var)) {
            unset($data->$var);
            $ret = $this->guardaCampo($data, $cid, $cam);
        }

        return $ret;
    }

    /**
     * Guarda un objeto en la base de datos en formato JSON. Si el campo es 'secure', el objeto se cifra antes de guardarlo.
     *
     * @param object $objeto El objeto que se va a guardar.
     * @param mixed $cid El ID del contacto al que se asocia el objeto.
     * @param string $campo El nombre del campo en el que se guarda el objeto. Por defecto es 'permanent'.
     * @return bool Retorna true si la operación fue exitosa, false si hubo un error.
     */
    public function guardaCampo($objeto, $cid, $campo = "permanent") {
        $rawdata = json_encode($objeto);
        if ($campo == "secure") {
            $rawdata = esconde($rawdata, $cid);
        }
        $query = $this->ci->db->query("INSERT INTO whatsapp_cont_data (id_contact, $campo)
            VALUES (?, ?) ON DUPLICATE KEY UPDATE $campo = ?", [$cid, $rawdata,  $rawdata]);
        $ret = true;
        if (!$query) {
            file_put_contents(APPPATH . "logs/debug.log", $this->ci->db->error(), FILE_APPEND);
            $ret = false;
        }

        return $ret;
    }

    /**
     * Recupera un objeto de la base de datos. Si el campo es 'secure', el objeto se descifra después de recuperarlo.
     *
     * @param mixed $cid El ID del contacto asociado al objeto.
     * @param string $campo El nombre del campo del que se recupera el objeto. Por defecto es 'permanent'.
     * @return object Retorna el objeto recuperado de la base de datos.
     */
    public function traeCampo($cid, $campo = "permanent") {
        $query = $this->ci->db->query("SELECT $campo FROM whatsapp_cont_data WHERE id_contact = ?", [$cid]);
        if ($query->num_rows()>0) {
            $rawdata = $query->row()->$campo;
        }
        if ($campo == "secure" && !empty($rawdata)) {
            $rawdata = encuentra($rawdata, $cid);
        }
        $rawdata = (empty($rawdata)) ? json_encode((object)[]) : $rawdata;

        return json_decode($rawdata);
    }

    /**
     * Ejecuta un script asociado a un ID de script específico. El script se compone de varios pasos, cada uno de los cuales se ejecuta en orden.
     *
     * @param object $ses Contiene información sobre la sesión actual.
     * @param mixed $msgRec El mensaje recibido que se procesará en el script.
     * @param object $antOp Contiene el ID del script que se va a ejecutar.
     * @return bool Retorna true si todos los pasos del script se ejecutan correctamente, false si alguno de los pasos falla.
     */
    public function runScript($ses, $msgRec, $antOp) {
        $query = $this->ci->db->query("SELECT bs.id, bs.sibien, bs.simal, bs.siespera,
            bss.paso, bss.camp, bss.varb, bss.tipo, bss.modi, bss.cond
            FROM whatsapp_bot_script bs
            JOIN whatsapp_bot_scr_steps bss on bss.id_whatsapp_bot_script = bs.id
            WHERE bs.id = ? AND bs.active = 1 AND bss.active = 1
            ORDER BY bss.orden", [$antOp->id_script]);
        $pasos  = $query->result();
        $getbas = $continua = true;
        $sibien = $simal = false;
        foreach ($pasos as $paso) {
            if ($getbas) {
                $sibien = $paso->sibien;
                $simal = $paso->simal;
                if ($paso->siespera != "") {
                    $this->ci->load->model("whatsapp_model");
                    $this->ci->whatsapp_model->sale_texto(["ses"=>$ses, "watext"=>$paso->siespera]);
                }
                $getbas = false;
            }
            switch ($paso->paso) {
                case 'variable':
                    $continua = $this->pasoVariable($paso, $ses->id_contact, $msgRec);
                    break;
                case 'borravar':
                    $continua = $this->pasoBorravar($paso, $ses->id_contact, $msgRec);
                    break;
                case 'concat':
                    $continua = $this->pasoConcat($paso, $ses->id_contact, $msgRec);
                    break;
                case 'request':
                    $continua = $this->pasoRequest($paso, $ses->id_contact, $msgRec);
                    break;
                case 'mensaje':
                    $continua = $this->pasoMensaje($paso, $ses->id_contact, $msgRec, $ses->id, $antOp->id);
                    break;
                case 'pasavar':
                    $continua = $this->pasoPasavar($paso, $ses->id_contact, $msgRec);
                    break;
                case 'transformar':
                    $continua = $this->pasoTransformar($paso, $ses->id_contact, $msgRec);
                    break;
                case 'redir':
                    // Si hay redirección se ignoran TODOS los pasos posteriores del script
                    $ret = $this->pasoRedir($paso, $ses->id_contact, $msgRec);
                    if (!empty($ret)) { return $ret; }
                    break;
                default:
                    $continua = false;
                    break;
            }
            if (!$continua) {
                return $simal ? (object)['id'=>'existe','action'=>7,'redirect'=>$simal] : $simal;
            }
        }

        return $sibien ? (object)['id'=>'existe','action'=>7,'redirect'=>$sibien] : $sibien;
    }

    /**
     * Evalúa las condiciones y, si se cumplen, guarda un valor en una variable específica.
     *
     * @param object $ps Contiene las condiciones a evaluar, el nombre de la variable y la información para guardar el valor.
     * @param mixed $cid Se utiliza para evaluar las condiciones y para identificar el registro donde guardar el valor.
     * @param mixed $msgRec El mensaje recibido que se procesará y se guardará en la variable si se cumplen las condiciones.
     * @return bool Retorna true si las condiciones no se cumplen o si el valor se guarda correctamente, false si hay un error al guardar el valor.
     */
    private function pasoVariable($ps, $cid, $msgRec) {
                if ($this->condiciones($ps->cond, $cid, $msgRec)) {
            $varval = $msgRec;
            if (strlen($ps->modi)>0) {
                $anterior = $this->traeVar($ps->varb, $cid, $ps->camp);
                $varval = $this->modifAct($anterior->valor, $ps->modi, $varval);
            }
            return $this->guardaVar([
                'cid'=>$cid,
                'cam'=>$ps->camp,
                'var'=>$ps->varb,
                'tip'=>$ps->tipo,
                'val'=>$varval,
            ]);
        }

        return true;
    }

    /**
     * Evalúa las condiciones y, si se cumplen, borra el valor de una variable específica.
     *
     * @param object $ps Contiene las condiciones a evaluar y el nombre de la variable cuyo valor se borrará.
     * @param mixed $cid Se utiliza para evaluar las condiciones y para identificar el registro donde se borrará el valor.
     * @param mixed $msgRec El mensaje recibido que se procesará.
     * @return bool Retorna true si las condiciones no se cumplen o si el valor se borra correctamente, false si hay un error al borrar el valor.
     */
    private function pasoBorravar($ps, $cid, $msgRec) {
        if ($this->condiciones($ps->cond, $cid, $msgRec)) {
            return $this->borraVar($ps->varb, $cid, $ps->camp);
        }

        return true;
    }

    /**
     * Evalúa las condiciones y, si se cumplen, concatena los valores de varias variables y guarda el resultado en una variable específica.
     *
     * @param object $ps Contiene las condiciones a evaluar, el nombre de la variable donde se guardará el resultado y la información para concatenar los valores.
     * @param mixed $cid Se utiliza para evaluar las condiciones y para identificar el registro donde se guardará el valor.
     * @param mixed $msgRec El mensaje recibido que se procesará.
     * @return bool Retorna true si las condiciones no se cumplen o si el valor se guarda correctamente, false si hay un error al guardar el valor.
     */
    private function pasoConcat($ps, $cid, $msgRec) {
        if ($this->condiciones($ps->cond, $cid, $msgRec)) {
            $tosend = $this->triadasArray($ps->modi);
            $varval = "";
            foreach ($tosend as $uno) {
                $varval .= $this->valorTriada($uno, $cid)[1];
            }
            return $this->guardaVar([
                'cid'=>$cid,
                'cam'=>$ps->camp,
                'var'=>$ps->varb,
                'tip'=>$ps->tipo,
                'val'=>$varval,
            ]);
        }
    }

    /**
     * Si se cumplen las condiciones especificadas, realiza una solicitud HTTP y guarda la respuesta.
     *
     * @param object $ps Contiene las condiciones a evaluar, la configuración de la solicitud y la información para guardar la respuesta.
     * @param mixed $cid Se utiliza para evaluar las condiciones y para identificar el registro donde guardar la respuesta.
     * @param mixed $msgRec Mensaje recibido del contacto.
     * @return bool Retorna false si la solicitud falla, o el resultado de guardar el código de respuesta de la solicitud.
     */
    private function pasoRequest($ps, $cid, $msgRec) {
        if ($this->condiciones($ps->cond, $cid, $msgRec)) {
            $basemet = $this->baseMetodo($ps->tipo, $cid, $ps->modi);
            if (!$basemet) { return false; }
            $data    = $this->joinData($basemet["data"], $basemet['modi'], $cid);
            $headers = $this->firmar($basemet["headers"], $data, $basemet["sign"]);
            if (!empty($basemet['auth'])) {
                $reqdata['auth'] = $basemet['auth'];
            }
            if (!empty($headers)) {
                $reqdata['heads'] = $headers;
            }
            $response = doReq(["url"=>$basemet["url"], "method"=>$basemet["prot"], "proto"=>$basemet["xtype"], "body"=>$data]);
            if (!empty($ps->varb)) {
                // Guarda el json recibido y se extraen los valores necesarios en pasos posteriores
                $this->guardaVar(['cid'=>$cid,'var'=>$ps->varb,'val'=>$response['data'],'cam'=>$ps->camp]);
            }

            // Último request code guardo en varible para futuras pruebas y validaciones
            return $this->guardaVar(['cid'=>$cid,'var'=>'ulreqcod',
                'val'=>$response['code'],'cam'=>'temporal','tip'=>'int']);
        }

        return true;
    }
    /**
     * Si se cumplen las condiciones redirige el flujo del bot
     *
     * @param object $ps Contiene las condiciones a evaluar, la configuración de la solicitud y la información para guardar la respuesta.
     * @param mixed $cid Se utiliza para evaluar las condiciones y para identificar el registro donde guardar la respuesta.
     * @param mixed $msgRec Mensaje recibido del contacto.
     * @return object Retorna false si las condiciones no se cumplen o el objeto que redirige el flujo del bot.
     */
    private function pasoRedir($ps, $cid, $msgRec) {
        $ret = false;
        if ($this->condiciones($ps->cond, $cid, $msgRec)) {
            if ($ps->varb == 'terminar') {
                $ret = (object)['id'=>'existe','action'=>3];
            } else {
                $ret = (object)['id'=>'existe','action'=>7,'redirect'=>$ps->varb];
            }
        }

        return $ret;
    }

    /**
     * Evalúa las condiciones especificadas y, si se cumplen, carga el modelo 'whatsapp_model' y envía un mensaje de texto.
     *
     * @param object $ps Contiene las condiciones a evaluar y el mensaje a enviar.
     * @param mixed $cid Se utiliza para evaluar las condiciones.
     * @param mixed $msgRec Mensaje recibido del contacto.
     * @param mixed $sid Se pasa al modelo 'whatsapp_model' para enviar el mensaje.
     * @return bool Retorna true después de enviar el mensaje.
     */
    private function pasoMensaje($ps, $cid, $msgRec, $sid) {
        if ($this->condiciones($ps->cond, $cid, $msgRec)) {
            $this->ci->load->model("whatsapp_model");
            $this->ci->whatsapp_model->sale_texto(["sid"=>$sid, "watext"=>$ps->modi]);
        }

        return true;
    }

    /**
     * This private method is responsible for performing the pasoPasavar operation.
     * ToDo puede mejorar para reutilizar en otros pasos
     *
     * @param mixed $ps Contiene las condiciones a evaluar.
     * @param mixed $cid Se utiliza para evaluar las condiciones.
     * @param mixed $msgRec Mensaje recibido del contacto.
     * @return void
     */
    private function pasoPasavar($ps, $cid, $msgRec) {
        if ($this->condiciones($ps->cond, $cid, $msgRec)) {
            $arrval = $this->valorTriada($ps->modi, $cid);
            return $this->guardaVar(['cid'=>$cid,'var'=>$ps->varb,'val'=>$arrval[1],'tip'=>$ps->tipo,'cam'=>$ps->camp]);
        }

        return true;
    }

    /**
     * Si las condiciones se cumplen, transforma el valor de una variable y lo guarda en otra
     * Las condiciones se evalúan en el valor de la variable especificada en la condición
     *
     * @param object $ps Paso del script
     * @param int $cid ID Contacto whatsapp
     * @param string $msgRec Mensaje recibido
     * @return bool
     */
    private function pasoTransformar($ps, $cid, $msgRec) {
        $ret = true;
        if ($this->condiciones($ps->cond, $cid, $msgRec)) {
            $partes = explode(" ", $ps->modi);
            $triada = $partes[0] . " " . $partes[1];
            $from = $this->valorTriada($triada, $cid);
            $tip = "string";
            switch ($partes[2]) {
                case 'fydmtdmy':
                    $to = substr($from[1],8,2)."-".substr($from[1],5,2)."-".substr($from[1],0,4);
                    $tip = $ps->tipo;
                    break;
                case 'string':
                    $to = strval($from[1]);
                    break;
                case 'minusc':
                    $to = strtolower($from[1]);
                    break;
                default:
                    $to = $from[1];
                    break;
            }
            $ret = $this->guardaVar(['cid'=>$cid,'cam'=>$ps->camp,'var'=>$ps->varb,'val'=>$to,'tip'=>$tip]);
        }

        return $ret;
    }

    /**
     * Trae datos base para request de api rest externa y los acomoda según se necesrio en el body o header
     *
     * @param int $mid ID del método
     * @param int $cid ID del cliente
     * @param mixed $modi Datos modificadores para el método, string
     * @return array contiene todos los datos separados en valores para el request
     */
    private function baseMetodo($mid, $cid = null, $modi = null) {
        $query = $this->ci->db->query("SELECT e.*, em.prot, em.met, em.xtype
            FROM extapi_met em JOIN extapi e on e.id = em.id_extapi
            WHERE em.id = ? AND e.active = 1", [$mid]);
        if ($query->num_rows() < 1) { return false; }
        // ToDo: va a faltar traer los campos del método y validar que se envíen todos los requeridos
        $met = $query->row();
        $url = $met->url . $met->met;
        $tags = $this->tagContents($url, "{{", "}}");
        $retmodi = $modi;
        if (count($tags)>0 && !empty($cid)) {
            $tris = $this->triadasArray($modi);
            $retmodi = "";
            foreach ($tris as $tri) {
                $vtr = $this->valorTriada($tri, $cid);
                $nom = "{{" . $vtr[0] . "}}";
                if (stripos($url, $nom) === false) { $retmodi .= "," . $tri; }
                $url = strtr($url, [$nom=>$vtr[1]]);
            }
            $retmodi = trim($retmodi, ",");
        }
        if (stripos($url, "{{") !== false) {
            debug_log("Url para request no válida", "Whatsappfun baseMetodo");
            return false;
        }
        // 0 headers, 1 data
        $ret = [
            "headers"=>[],
            "data"=>[],
            "url"=>$url,
            "prot"=>$met->prot,
            "xtype"=>$met->xtype,
            "sign"=>$met->sign,
            "modi"=>$retmodi,
        ];
        foreach (['user','pass','token','xhash'] as $value) { // No creo que haya más de 4 en una mugrosa api
            if (!empty($met->$value) && strpos($met->$value,':')) {
                $cond = preg_replace('/\s+/', '', $met->$value);
                $cond = trim($cond);
                list($etiq, $val) = explode(':',$cond);
                if ($etiq == "auth" && $value == "user") {
                    $ret["auth"] = $val . ":";
                } elseif ($etiq == "auth" && $value == "pass") {
                    $ret["auth"] = $ret["auth"] . $val;
                } elseif ($met->logloc == 0) { // 0 = headers, 1 = data
                    $ret["headers"][] = $etiq . ": " . $val;
                } else {
                    $ret["data"][$etiq] = $val;
                }
            }
        }

        return $ret;
    }

    /**
     * Agrega la data del body a lo que ya viene de base desde el método de la api
     *
     * @param array $data variables del request body
     * @param string $mody definición de varias triadas de info para enviar
     * @param int $cid ID Contacto whatsapp
     * @return array $data recibida junto con variables especificadas en $mody
     */
    private function joinData($data, $modi, $cid) {
        if (!empty($modi)) { // Triadas que se envían como data en el request
            $tosend = $this->triadasArray($modi);
            foreach ($tosend as $par) {
                $arrval = $this->valorTriada($par, $cid);
                $piezas = explode(' ',$par);
                $camb   = end($piezas);
                if (stripos($camb, ".") !== false) {
                    $subs = explode(".", $camb);
                    $valfin = (is_object($arrval[1])) ? (array)$arrval[1] : $arrval[1];
                    switch (count($subs)) {
                        case 2:
                            $data[$subs[0]][$subs[1]] = $valfin;
                            break;
                        case 3:
                            $data[$subs[0]][$subs[1]][$subs[2]] = $valfin;
                            break;
                        case 4:
                            $data[$subs[0]][$subs[1]][$subs[2]][$subs[3]] = $valfin;
                            break;
                        default:
                            $data["Error"] = "Err snd"; // Error, subcampo nivel desconocido (muy loco)
                            break;
                    }
                } else {
                    $data[$arrval[0]] = $arrval[1];
                }
            }
        }

        return $data;
    }

    /**
     * Recibe texto separado por espacios (1 a 3 pedazos) que son la definición de una variable con un valor
     *
     * @param string $triada Definición de variable
     * @param int $cid ID Contácto Whatsapp
     * @return array [nombre, valor] de la variable extraída
     */
    public function valorTriada($triada, $cid) {
        $baserr = ['Error','Err mdc'];  // mala definición de campo o campo inexistente
        if (empty($triada) || empty($cid)) { return $baserr; }
        $campos = ["permanent","temporal","secure","file"];
        $triada = preg_replace('/\s+/', ' ', $triada);
        $triada = trim($triada);
        $triada = str_replace(". ", ".", $triada);
        $triada = str_replace(" .", ".", $triada);
        $pre    = explode(" ",$triada);
        $valor  = $pre[0];
        $nombre = (empty($pre[1])) ? "" : $pre[1];
        $como   = end($pre);
        $ret    = [$como, $valor];
        if (stripos($como, ".") !== false) {
            $parcomo = explode(".", $como);
            $ret[0] = end($parcomo);
        }
        // valor fijo se toma literal
        if (stripos($valor, ".") !== false) {
            $pasoval = explode(".", $valor);
            $ret[1]  = end($pasoval);
        } elseif (array_key_exists($valor, $this->especiales)) {
            // valor en propiedad especiales
            $ret[1] = $this->especiales[$valor];
        } elseif (in_array($valor, $campos)) {
            // Valor en campo y variable
            if (empty($nombre)) {
                return $baserr;
            }
            $ret[1] = $this->valorDesdeCampo($valor, $nombre, $cid);
        }

        return $ret;
    }

    /**
     * Firma los datos que se enviarán, si es necesario.
     *
     * @param array $headers Los encabezados que se enviarán junto con los datos.
     * @param array $data Los datos que se firmarán y enviarán.
     * @param string $sign Contiene la etiqueta y el tipo de firma que se utilizará.
     * @return array Retorna los encabezados con la firma agregada, si es necesario.
     */
    private function firmar(array $headers, array $data, $sign) {
        if (!empty($sign)) { // Firmar la data que se envía en caso de ser necesario
            list($etiq, $tipofirma) = explode(':',$sign);
            // Así se podrán ir agregando tipos de firma empezamos sólo con un tipo para círculo de crédito
            if ($tipofirma == 'jsfirma') {
                // jsfirma sólo puede existir en headers o se invalida la data
                $jsreq = json_encode($data);
                $esreq = escapeshellarg($jsreq);
                $rutex = APPPATH . "signing/"; // Ruta aplicación de firma
                // $rutcr = $met[2]; // Ruta certificado, rprv
                $rutcr = $rutex;
                $comando = "node " . $rutex . "firmar.js --req=" . $esreq . " --rprv=" . $rutcr;
                $val = exec($comando, $execres);
                $headers[] = $etiq . ": " . $val;
            }
        }

        return $headers;
    }

    /**
     * Extrae el valor de una variable de la tabla whatsapp_cont_data
     *
     * @param string $desde Nombre de la columna de la tabla whatsapp_cont_data
     * @param string $partes Nombre de la variable
     * @param int $cid ID del contacto de whatsapp
     * @return mixed Valor de la variable extraída
     */
    private function valorDesdeCampo($desde, $partes, $cid) {
        if (!is_array($partes)) {
            $partes = explode('.', $partes);
        }
        $varnam = array_shift($partes);
        $var    = $this->traeVar($varnam, $cid, $desde);
        $varobj = (isJson($var->valor)) ? json_decode($var->valor) : $var->valor;
        foreach ($partes as $par) {
            if (is_object($varobj)) {
                $varobj = $varobj->$par;
            } elseif (is_array($varobj)) {
                $varobj = $varobj[$par];
            } else {
                $varobj = ""; // No hay nivel siguiente, termina en variable
            }
        }

        return $varobj;
    }

    /**
     * Modifica el valor de una variable según la acción especificada.
     *
     * @param mixed $an Valor actual de la variable
     * @param string $ac Acción a realizar
     * @param mixed $varval Valor que se utilizará para modificar la variable
     * @return mixed Retorna el valor modificado de la variable.
     */
    private function modifAct($an, $ac, $varval) {
        switch ($ac) {
            case '++':
                $ret = (int)$an + 1;
                break;
            case '+=':
                $ret = (int)$an + (int)$varval;
                break;
            case '.=':
                $ret = $an . $varval;
                break;
            default:
                $ret = $ac;
                break;
        }

        return $ret;
    }

    /**
     * Extrae los valores entre dos etiquetas de apertura y cierre de un string.
     *
     * @param string $string El string del que se extraerán los valores.
     * @param string $tag_open La etiqueta de apertura.
     * @param string $tag_close La etiqueta de cierre.
     * @return array Retorna un array con los valores extraídos.
     */
    private function tagContents($string, $tag_open, $tag_close){
        $result = [];
        foreach (explode($tag_open, $string) as $value) {
            if(strpos($value, $tag_close) !== false){
                $result[] = substr($value, 0, strpos($value, $tag_close));
            }
        }

        return $result;
    }

    /**
     * Extrae los valores de una cadena de texto separados por comas.
     *
     * @param string $tris La cadena de texto que se va a separar.
     * @return array Retorna un array con los valores extraídos.
     */
    private function triadasArray($tris) {
        $tris = preg_replace('/\s+/', ' ', $tris);
        $tris = trim($tris);
        $tris = str_replace(", ", ",", $tris);
        $tris = str_replace(" ,", ",", $tris);
        $tris = str_replace(". ", ".", $tris);
        $tris = str_replace(" .", ".", $tris);

        return explode(',',$tris);
    }

    /**
     * Evalúa las condiciones especificadas.
     *
     * @param string $cond Las condiciones que se evaluarán.
     * @param int $cid ID del contacto de whatsapp.
     * @param mixed $msgRec El mensaje recibido que se procesará.
     * @return bool Retorna true si las condiciones no se cumplen, false si alguna de las condiciones no se cumple.
     */
    private function condiciones($cond, $cid, $msgRec) {
        if (!empty($cond)) {
            $cond = $this->triadasArray($cond);
            foreach ($cond as $estacond) {
                $par = explode(" ",$estacond);
                // Valor buscado
                $valbus = end($par);
                $neg = false;
                if (substr($valbus,0,1) == "!") {
                    $neg = true;
                    $valbus = substr($valbus,1);
                }
                // A comparar es por default el mensaje recibido del contacto
                $acom = $msgRec;
                if (count($par) > 1) {
                    // A comparar cambia por el valor de la viriable especificada
                    $tri = $par[0] . " " . $par[1];
                    $val = $this->valorTriada($tri, $cid);
                    $acom = $val[1];
                }
                if (is_bool($acom)) {
                   $acom = var_export($acom, true);
                }
                if (is_bool($valbus)) {
                   $valbus = var_export($valbus, true);
                }
                $valbus = (array_key_exists($valbus, $this->especiales)) ? $this->especiales[$valbus] : $valbus;
                $valbus = strtoupper($valbus);
                $acom   = strtoupper($acom);
                if (($neg && $acom == $valbus) || (!$neg && $acom != $valbus)) {
                    return false;
                }
            }
        }

        return true;
    }

}
