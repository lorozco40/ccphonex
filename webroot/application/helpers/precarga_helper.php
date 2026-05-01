<?php

function slugify($text) {
    $text = str_replace(array('á','à','ä','â','ª','Á','À','Â','Ä'), 'a', $text);
    $text = str_replace(array('é','è','ë','ê','É','È','Ê','Ë'), 'e', $text);
    $text = str_replace(array('í','ì','ï','î','Í','Ì','Ï','Î'), 'i', $text);
    $text = str_replace(array('ó','ò','ö','ô','Ó','Ò','Ö','Ô'), 'o', $text);
    $text = str_replace(array('ú','ù','ü','û','Ú','Ù','Û','Ü'), 'u', $text);
    $text = str_replace(array('ñ','Ñ'), 'n', $text);
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '_', $text);
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function beautify(string $uglytxt, array $posibles = []) {
    $res = $uglytxt;
    if (key_in_array($res, $posibles)) {
        $res = $posibles[$res];
    }
    if ($res == $uglytxt) {
        $res = traduce($uglytxt);
    }
    if ($res == $uglytxt) {
        $res = str_replace("_", " ", $uglytxt);
        $res = ucwords($res);
    }

    return $res;
}

function getUserIP() {
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];
    if(filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    return $ip;
}

function dd($var) {
    echo "<pre>";
    die(var_dump($var));
}

function de($var) {
    die("<pre>" . highlight_string("<?php\n\$var =\n" . var_export($var, true) . ";\n?>") . "</pre>");
}

function isJson($string) {
    $primero = substr($string, 0, 1);
    if($primero != "{" && $primero != "[") return false;
    json_decode($string);

    return (json_last_error() == JSON_ERROR_NONE);
}

function arrayDepth(array $array) {
    $max_depth = 1;
    foreach ($array as $value) {
        if (is_array($value)) {
            $depth = arrayDepth($value) + 1;
            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }

    return $max_depth;
}

// String de valores separados por comas a JSON
function strToJson($data) {
    $data = trim($data); // quita espacios inicial y final
    $data = preg_replace('/( ){2,}/u',' ',$data); // quita espacios repetidos
    $data = rtrim($data, ','); // quita coma final
    $data = str_replace(', ', ',', $data); // dejar solo comas para separar valores
    $data = explode(',', $data);

    return json_encode(array_values($data), JSON_UNESCAPED_UNICODE);
}

// convierte fecha de $formato_in a $formato_out
function convierte($fecha, $formato_in, $formato_out = "Y-m-d") {
    if ($formato_out != $formato_in) {
        $ff = explode("-", $formato_in);
        $yk = array_search("Y", $ff);
        $mk = array_search("m", $ff);
        $dk = array_search("d", $ff);
        $fecha = explode("-", $fecha);
        if ($formato_out == "d-m-Y") {
            $fecha = $fecha[$dk]."-".$fecha[$mk]."-".$fecha[$yk];
        } elseif ($formato_out == "m-d-Y") {
            $fecha = $fecha[$mk]."-".$fecha[$dk]."-".$fecha[$yk];
        } elseif ($formato_out == "Y-d-m") {
            $fecha = $fecha[$yk]."-".$fecha[$dk]."-".$fecha[$mk];
        } else { // Y-m-d
            $fecha = $fecha[$yk]."-".$fecha[$mk]."-".$fecha[$dk];
        }
    }

    return $fecha;
}

/**
 * doReq peticiones http con curl, autenticación y métodos.
 *
 * @param  array $data string url (requerida) y array heads,
 * (txt, xml, html; json, urlencoded o get params se convierten desde array, array file abajo de body) body,
 * string method (def GET), string proto(def json, form), string auth, bool nossl (opcionales)
 * @return array bool error, data, int code
 */
function doReq($data = []) : array {
    $okmethods = ["GET","POST","PUT","DELETE","PATCH"];
    $okprotos  = [
        "json"=>"application/json",
        "x-www-form-urlencoded"=>"application/x-www-form-urlencoded",
        "form-data"=>"multipart/form-data",
        "text"=>"text/plain",
        "xml"=>"application/xml",
        "html"=>"text/html",
        "javascript"=>"application/javascript",
    ];
    $ret = ["error"=>true,"data"=>"Bad request","code"=>0];
    $toUseURL = $data["url"];
    if (empty($toUseURL)) return $ret;
    $toUseMet = "GET";
    if (!empty($data["method"])) {
        if (in_array($data["method"], $okmethods)) { $toUseMet = $data["method"]; } else { return $ret; }
    }
    $bodyType = "json";
    if (!empty($data["proto"])) {
        if (array_key_exists($data["proto"], $okprotos)) { $bodyType = $data["proto"]; } else { return $ret; }
    }
    $toUseBody = (empty($data["body"])) ? "" : $data["body"];
    if ($toUseMet == "GET" && !empty($toUseBody)) {
        $toUseURL .= "?" . http_build_query($toUseBody);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $toUseURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $toUseMet);
    // curl_setopt($ch, CURLOPT_VERBOSE, true);
    // $data["auth"] string user:password
    if (!empty($data["auth"])) { curl_setopt($ch, CURLOPT_USERPWD, $data["auth"]); }
    if (!empty($toUseBody) && $toUseMet != "GET") {
        if ($bodyType == "json") {
            $toUseBody = json_encode($toUseBody);
        } elseif ($bodyType == "x-www-form-urlencoded") {
            $toUseBody = http_build_query($toUseBody);
        } elseif (!empty($data["file"])) {
            if ($bodyType != "form-data") return $ret;
            foreach ($data["file"] as $key => $file) {
                if (!empty($toUseBody[$key]) || !file_exists($file)) return $ret;
                $toUseBody[$key] = new CURLFILE($file);
            }
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $toUseBody);
    }
    $headers = ["Content-Type: " . $okprotos[$bodyType]];
    // OJO !!! heads Array con headers extra (ej. "Authorization: abcd")
    if (!empty($data["heads"])) $headers = array_merge($headers, $data["heads"]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (!empty($data["nossl"])) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    }
    $ret["data"] = curl_exec($ch);
    $ret["code"] = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    if(curl_errno($ch)) {
        $ret["data"] = curl_error($ch);
        debug_log("CURL error: " . $toUseURL . ", " . $ret["data"]);
    } else {
        $ret["error"] = false;
    }
    curl_close($ch);

    return $ret;
}

function bagoLicenciaFecha($expira) {
    $expira = trim((string)$expira);
    if ($expira === '') {
        throw new Exception('Fecha de licencia vacia');
    }
    $expira = preg_replace_callback('/\.(\d+)(Z|[+-]\d{2}:\d{2})$/', function($match) {
        return '.' . substr(str_pad($match[1], 6, '0'), 0, 6) . $match[2];
    }, $expira);

    return new DateTime($expira);
}

function bagoLicenciaDecode($response) {
    $raw = $response;
    if (is_array($response) && array_key_exists('data', $response)) {
        $raw = $response['data'];
    }
    if (!is_string($raw) || trim($raw) === '') {
        return false;
    }
    $lic = json_decode($raw);
    if (empty($lic)) {
        return false;
    }
    if (empty($lic->tipo)) {
        $lic->tipo = 'CT';
    }
    if (empty($lic->usuarios)) {
        $lic->usuarios = 9999;
    }
    if (empty($lic->cliente)) {
        $lic->cliente = 'contingencia';
    }
    if (empty($lic->expira)) {
        $lic->expira = date('Y-m-d H:i:s', strtotime('+365 days'));
        return $lic;
    }
    try {
        $lic->expira = bagoLicenciaFecha($lic->expira)->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        $lic->expira = date('Y-m-d H:i:s', strtotime('+365 days'));
    }

    return $lic;
}

// formatea un número telefónico para que sea entendible simple vista
function formatel($value='') {
    $result = substr($value, 0 , -10).' '.substr($value, -10 , 2).' '.substr($value, -8 , 4).' '.substr($value, -4 , 4);

    return $result;
}

function val_in_array($needle, array $arr) {
    if(in_array($needle, $arr)) return true;
    foreach ($arr as $sub) {
        if(is_array($sub)) {
            if(val_in_array($needle, $sub)) return true;
        }
    }

    return false;
}

function key_in_array($needle, array $arr) {
    if(array_key_exists($needle, $arr)) return true;
    foreach ($arr as $sub) {
        if(is_array($sub)) {
            if(key_in_array($needle, $sub)) return true;
        }
    }

    return false;
}

function make_thumb($img, $type = "base64") {
    $fileContents = file_get_contents($img);
    $nueva = $image = imagecreatefromstring($fileContents);
    imagealphablending($nueva, false);
    imagesavealpha($nueva, true);

    $anchoOrig = imagesx($image);
    $altoOrig  = imagesy($image);

    if ($anchoOrig > 100 || $altoOrig > 100) {
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $ancho = 100;
        $alto  = 100;

        $ratioOrig = $anchoOrig/$altoOrig;

        if ($ratioOrig < 1) {
           $ancho = $alto*$ratioOrig;
        } else {
           $alto = $ancho/$ratioOrig;
        }

        $nueva = imagecreatetruecolor($ancho, $alto);
        imagecopyresampled($nueva, $image, 0, 0, 0, 0, $ancho, $alto, $anchoOrig, $altoOrig);
    }

    imagedestroy($image);
    ob_start();
    imagepng($nueva);
    $sale = ob_get_clean();
    if ($type == "base64") {
        return base64_encode($sale);
    } else {
        return $sale;
    }
}

function esconde($quesconde, $lasal = '4ss3rt1v3') { // contra encuentra
    $method = "AES-256-CBC";
    $key = hash('sha256', $lasal, true);
    $iv = openssl_random_pseudo_bytes(16);
    $ciftxt = openssl_encrypt($quesconde, $method, $key, OPENSSL_RAW_DATA, $iv);
    $hash = hash_hmac('sha256', $ciftxt . $iv, $key, true);

    return base64_encode($iv . $hash . $ciftxt);
}

function encuentra($escondido, $lasal = '4ss3rt1v3') { // contra esconde
    $escondido = base64_decode($escondido);
    $method = "AES-256-CBC";
    $iv = substr($escondido, 0, 16);
    $hash = substr($escondido, 16, 32);
    $ciftxt = substr($escondido, 48);
    $key = hash('sha256', $lasal, true);
    if (!hash_equals(hash_hmac('sha256', $ciftxt . $iv, $key, true), $hash)) return null;

    return openssl_decrypt($ciftxt, $method, $key, OPENSSL_RAW_DATA, $iv);
}

function traduce($que) {
    if (!empty(lang($que))) return lang($que);

    return $que;
}

// Orden anidado de query result en filas por columna parent y si existe por columna sub
function orderbyparentsub($data, $sub = '', $par = 'parent', $newa = [], $padre = 0) {
    if (empty($sub)) {
        array_multisort(
            array_column($data, $par), SORT_ASC,
        $data);
    } else {
        array_multisort(
            array_column($data, $par), SORT_ASC,
            array_column($data, $sub), SORT_ASC,
        $data);
    }
    foreach ($data as $key => $row) {
        if ($row['parent']==$padre) {
            $newa[] = $row;
            unset($data[$key]);
            $newa = orderbyparentsub($data, $sub, $par, $newa, $row['id']);
        }
    }

    return $newa;
}

// Muestra un objeto o un array en forma de lista UL de texto
function enlista($que, $numrcs = false, $keyan = false) {
    $ret = "";
    if(is_array($que) || is_object($que)) {
        $ret .= "<ul>";
        foreach ($que as $key => $value) {
            if(is_array($value) || is_object($value)) {
                $ret .= "<li>$key:</li>";
                $ret .= enlista($value, $numrcs, $key);
            } else {
                $ret .= "<li>";
                $ret .= (is_numeric($key) && $numrcs === false) ?
                    addbuttons($keyan, $value) :
                    $key . " -> " . addbuttons($key, $value);
                $ret .= "</li>";
            }
        }
        $ret .= "<ul>";
    } else {
        $ret .= addbuttons($keyan, $que);
    }

    return $ret;
}

function addbuttons($eti, $val) {
    $eti = strtolower($eti);
    if (strlen($val) >= 3) {
        if (strpos($eti, "numero") !== false || strpos($eti, "número") !== false || strpos($eti, "telefono") !== false || strpos($eti, "teléfono") !== false) {
            return addphone($val);
        } elseif (strpos($eti, "mail") !== false) {
            return addemail($val);
        }
    }

    return $val."<br>";
}

function addemail($val) {
    $ema = strtolower(trim($val));

    return $val . ' <span class="btn btn-primary emailacliente" data-email="' .
        $ema . '" data-toggle="tooltip" title="Llamar"><i class="far fa-envelope"></i></span><br>';
}

function addphone($val) {
    $num = preg_replace("/[^0-9]/", "", $val);
    return $val . ' <span class="btn btn-primary llamarcliente" data-numero="' .
        $num . '" data-toggle="tooltip" title="Llamar"><i class="fas fa-phone"></i></span> ' .
        ' <span class="btn btn-primary smsacliente" data-numero="' .
        $num . '" data-toggle="tooltip" title="SMS"><i class="fas fa-sms"></i></span><br>';
}

function options_select_campaign($campaigns = [], $value = '', $print = false) {
    $show = true;
    $options = '';
    foreach ($campaigns as $campaign) {
        $selected = ($campaign->id == $value) ? "selected" : "";
        if ( $campaign->active == 0 && $show ) {
            $show = false;
            $options .= "<option disabled>──── Inactivas ────</option>";
        }
        $options .= "<option value='".$campaign->id."' $selected>".$campaign->name."</option>";
    }
    if( $print )
        echo $options;
    else
        return $options;
}

/**
 * Despliega un select con categorías array de objetos con id, name y cat
 */
function selectCats(array $vals, array $specs = []) {
    $label = (empty($specs['label'])) ? "Select con categorías" : $specs['label'];
    $name  = (empty($specs['name'])) ? "selectcats" : $specs['name'];
    $cat   = (empty($specs['cat'])) ? "cat" : $specs['cat'];
    $ind   = (empty($specs['ind'])) ? "id" : $specs['ind'];
    $eti   = (empty($specs['eti'])) ? "name" : $specs['eti'];
    $elemen = "<label for='$name'>$label</label><select name='$name' class='form-control'>";
    $camact = "";
    $select = " selected='selected'";
    $siabre = true;
    $sicier = false;
    foreach ($vals as $val) {
        if (empty($val->$cat) || empty($val->$ind) || empty($val->$eti)) {
            return "<div class='text-danger'>Campo incorrecto</div>";
        }
        if ($camact != $val->$cat) {
            $siabre = true;
            $sicier = ($camact != "") ? true : false;
            $camact = $val->$cat;
        }
        if ($sicier) {
            $elemen .= "</optgroup>";
        }
        if ($siabre) {
            $elemen .= "<optgroup label='" . $camact ."'>";
        }
        $elemen .= "<option value=" . $val->$ind . "$select>" . $val->$eti . "</option>";
        $siabre = $sicier = false;
        $select = "";
    }
    if ($camact != "") {
        $elemen .= "</optgroup>";
    }
    $elemen .= "</select>";

    return $elemen;
}

/**
 * Escribe en logs/debug.log lo que se mande (útil para debuguear)
 *
 * @param mixed $data Se convertirá a string con json_encode en caso de objeto o array
 * @param string $info Descripción adicional para grabar
 * @return void
 */
function debug_log($data, $info = '') {
    $tolog = $info . ' | ';
    $tolog .= (is_string($data)) ? $data : json_encode($data);
    file_put_contents(APPPATH . 'logs/debug.log', date("Y-m-d h:i:s") . " " . $tolog . "\n", FILE_APPEND);
}

/**
 * Crear firma de email
 *
 * @param   string  $txt
 * @param   string  $img
 * @param   integer $id_cuenta
 * @return  string
 */
function signature_email($txt='', $img='', $id_cuenta = 0)
{
    $id_cuenta = (int)$id_cuenta;
    $body = ( $txt != '' ) ? "<br/>".$txt : ''; 
    if( !empty($img) ) {
        if (file_exists(FCPATH."../files/$img")) {
            $CI = &get_instance();
            $CI->load->helper('file');
            $mime = get_mime_by_extension($img);
            $img = file_get_contents(FCPATH."../files/$img");
            $img = base64_encode($img);
            $body .= '<br/><img src="data:'.$mime.';base64,'.$img.'" alt="Firma" />';
        }
    } else { //No existe registro de firma en la base de datos, pero falta validar que se haya agregado sin registrarla
        if (file_exists(FCPATH."../files/firma_email_".$id_cuenta.".jpg")) {
            $img = file_get_contents(FCPATH."../files/firma_email_".$id_cuenta.".jpg");
            $img = base64_encode($img);
            $mime = "image/jpg";
            $body .= '<br/><img src="data:'.$mime.';base64,'.$img.'" alt="Firma" />';
        }
    }

    return $body;
}

function html_rating($calif = 0, $de = 5, $rojo = 3.4, $naranja=3.9, $amarillo=4.4) {
    $html = '';
    for( $i = 1; $i <= $de; $i++ ) {
        if ($calif <= $rojo) {
            $clase = "esrojo";
        } elseif ($calif <= $naranja) {
            $clase = "esnaranja";
        } elseif ($calif <= $amarillo) {
            $clase = "esamarillo";
        } else {
            $clase = "esverde";
        }
        if( $calif >= $i ) {
            $html .= '<i class="fas fa-star '.$clase.'"></i>';
        }
        else {
            $html .= '<i class="far fa-star '.$clase.'"></i>';
        }
    }

    return $html;
}