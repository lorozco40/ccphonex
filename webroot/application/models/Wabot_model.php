<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wabot_model extends CI_Model
{

    private $accs = [
        1 => "Submenu",
        2 => "Operador",
        3 => "Terminar",
        4 => "Sub/Terminar",
        6 => "Terminar/Encuesta",
        7 => "Redirigir",
        8 => "Script",
        5 => "Menú inicio",
    ];

    private $msgCmds = ['R', 'TRM123', 'R3D1R', 'FR0M3X7'];

    public function lista($data) {
        $were = ""; $dararr = [];
        if (!empty($data['wid'])) {
            $were = "WHERE id_wacta = ?";
            $dararr = [$data['wid']];
        }
        $query = $this->db->query("SELECT * FROM whatsapp_bot $were order by active DESC", $dararr);
        $data['regs'] = $query->num_rows();
        $dararr[] = (int)$data['pag'];
        $dararr[] = (int)$data['rpp'];
        $query = $this->db->query("SELECT wab.id, wab.id_wacta, wab.name, wab.intro, wab.bye, wab.out_of_time, wab.wait_time, wab.ini_script, wab.active, wab.created_by, wab.created_when, wab.label,
            concat(wa.nombre,' (',wa.cuenta,')') AS cuenta,
            concat(u.name,' ',u.last) AS creator
            FROM whatsapp_bot wab
            JOIN whatsapp_cuentas wa ON wa.id = wab.id_wacta
            JOIN user u on u.id = wab.created_by
            $were ORDER BY label ASC, wab.id_wacta, wab.active DESC LIMIT ?, ?",
            $dararr
        );
        $data['data'] = $query->result();
        return $data;
    }

    public function guardar($data) {
        $ret = "Error desconocido";
        $ini_script = ( empty($data['ini_script']) ) ? NULL : $data['ini_script'];
        $label = ( $data['label'] == NULL ) ? '' : $data['label'];
        if (empty($data['id'])) {
            $query = $this->db->query("INSERT INTO whatsapp_bot (id_wacta, name, label, intro, bye, out_of_time, wait_time, active, ini_script, created_by) values (?,?,?,?,?,?,?,0,?,?)",
                [$data['wid'], $data['name'], $label, $data['intro'], $data['bye'], $data['out_of_time'], $data['wait_time'], $ini_script, $this->udata['id']]
            );
            if ($query) {
                $ret = "Bot creado con éxito, agregar opciones!";
            }
        } else {
            $active = (empty($data['active'])) ? '0' : '1';
            if ($active == '1') {
                $this->db->query("UPDATE whatsapp_bot SET active = '0' WHERE id_wacta = ? and active = '1'", [$data['wid']]);
            }
            $query = $this->db->query("UPDATE whatsapp_bot set id_wacta=?, name=?, label=?, intro=?, bye=?, out_of_time=?, wait_time=?, active=?, ini_script=? where id=?",
                [$data['wid'], $data['name'], $label, $data['intro'], $data['bye'], $data['out_of_time'], $data['wait_time'], $active, $ini_script, $data['id']]
            );
            if ($query) {
                $ret = "Bot actualizado con éxito.";
            }
        }

        return $ret;
    }

    public function oplist($data) {
        $query = $this->db->query("SELECT *, routeId AS `path` FROM whatsapp_bot_op WHERE id_bot = ?
            ORDER BY routeId", [$data['bid']]);
        $data['data'] = $query->result();
        $data['accs'] = $this->accs;

        return $data;
    }

    public function opguardar($data) {
        $redirect  = (empty($data['redirect'])) ? 0 : $data['redirect'];
        $idScript = (empty($data['id_script'])) ? 0 : $data['id_script'];
        if ($data['id'] == 0) {
            $query = $this->db->query("INSERT INTO whatsapp_bot_op
                (`option`, `label`, `action`, `parent`, `redirect`, `id_script`, `id_wacta`, `id_bot`)
                values (?,?,?,?,?,?,?,?)",
                [$data['option'], $data['label'], $data['action'], $data['parent'], $redirect, $idScript, $data['wid'], $data['bid']]
            );
        } else {
            $query = $this->db->query("UPDATE whatsapp_bot_op
                SET `option`=?, `label`=?, `action`=?, `redirect`=?, `id_script`=? WHERE id = ?",
                [$data['option'], $data['label'], $data['action'], $redirect, $idScript, $data['id']]
            );
        }
        if ($query) {
            return true;
        }

        return ['error'=>$this->db->error_message()];
    }

    public function opborrar($id) {
        return $this->db->query("DELETE from whatsapp_bot_op WHERE id = ? OR parent = ?", [$id, $id]);
    }

    public function respondeBot($ses, $msgRec) {
        // Encontrar el ÚNICO bot activo para ésta cuenta de whatsapp
        $bot = $this->db->query("SELECT * FROM whatsapp_bot
            WHERE id_wacta = '$ses->id_wacta' AND active = 1
            ORDER BY id DESC LIMIT 1")->row();
        if (!$bot) {
            // El bot no existe o fue desactivado
            // OJO si se desactiva un bot y se activa otro en medio de una conversación
            // Habrá comportamiento inconsistente o error
            // ToDo: Cerrar todas las conversaciones de un bot al desactivarlo
            $this->pasaagente($ses);
        } else {
            // En caso de que sea la primer respuesta del bot
            // $msgRec no tendría ningún sentido, se descarta
            $bid      = $bot->id;
            $upMsgRec = strtoupper($msgRec);
            $idAntOp  = 0;
            $elec     = (object)['id'=>0,'action'=>1];
            $query    = $this->db->query("SELECT COUNT(*) AS sum FROM whatsapp_entry
                WHERE id_session = ? AND `type` = 'Saliente'", [$ses->id]);
            $sum      = $query->row()->sum;
            if ($sum > 0) {
                // Ya había respondido el bot
                $idAntOp = $ses->paso; // id de la última opción enviada, puede ser 0 primero bot
                $antOp   = (object)["id"=>$idAntOp];
                $xquery  = $elec = "";
                if (in_array($upMsgRec, $this->msgCmds)) {
                    $elec = $this->evalMsgCmd($ses, $upMsgRec, $idAntOp);
                }
                if ($upMsgRec !== 'R') {
                    $this->db->query("UPDATE whatsapp_bot_history SET xtra = '' WHERE id_session = ? AND xtra <> ''",
                    [$ses->id]);
                }
                if (empty($elec)) {
                    if (stripos($idAntOp,'-') !== false) {
                        list($bid, $idAntOp) = explode('-', $idAntOp);
                        $antOp->id           = $idAntOp;
                        $query               = $this->db->query("SELECT * FROM whatsapp_bot WHERE id = ?", [$bid]);
                        $oldname             = $bot->name;
                        $bot                 = $query->row();
                        $bot->name           = $oldname;
                    }
                    if (empty($idAntOp)) {
                        $xquery = "AND id_bot = '$bid'";
                        if ($bot->ini_script) {
                            $antOp->id_script = $bot->ini_script;
                            $elec = $this->whatsappfun->runScript($ses, $msgRec, $antOp);
                        }
                    } else {
                        $query = $this->db->query("SELECT * FROM whatsapp_bot_op
                            WHERE id = ? LIMIT 1", [$idAntOp]);
                        $antOp = $query->row();
                        if (!empty($antOp->id_script)) {
                            $elec = $this->whatsappfun->runScript($ses, $msgRec, $antOp);
                        }
                    }
                    if (empty($elec)) {
                        $elec = $this->db->query("SELECT * FROM whatsapp_bot_op
                            WHERE parent = ? $xquery AND `option` = ? AND `option` <> '-' LIMIT 1",
                            [$idAntOp, $msgRec]
                            )->row() ?: (object)['id'=>$idAntOp,'action'=>1,'x'=>'Err'];
                    }
                }
            }

            $this->enviaops($ses, $bot, $elec);
        }
    }

    private function evalMsgCmd($ses, $upMsgRec, $idAntOp) {
        $redirect = '';
        $elec     = [];
        if (substr($upMsgRec, 0, 5) == 'R3D1R') {
            list($upMsgRec, $redirect) = explode(' ', $upMsgRec, 2);
        }
        if ($upMsgRec == 'R') {
            $query = $this->db->query("SELECT id, paso FROM whatsapp_bot_history
                WHERE id_session = ? AND id <
                (SELECT IFNULL(t1.max,t2.max) FROM (SELECT MAX(id) max FROM whatsapp_bot_history
                WHERE id_session = ?
                AND xtra = '1') t1, (SELECT MAX(id) max FROM whatsapp_bot_history
                WHERE id_session = ?) t2) ORDER BY id DESC LIMIT 1", [$ses->id, $ses->id, $ses->id]);
            $regtoret = $query->row();
            if ($regtoret) {
                $elec['id'] = $regtoret->paso;
                $this->db->query("UPDATE whatsapp_bot_history
                    SET xtra = CASE id WHEN ? THEN '1' ELSE '' END WHERE id_session = ?",
                    [$regtoret->id, $ses->id]);
            } else {
                $elec['id'] = 0;
            }
            $elec['action'] = 1;
            // ---> Esto es exclusivo para uso interno, a menos que se quieran después implementar
        } elseif ($upMsgRec == 'TRM123') {
            $elec['action'] = 3;
        } elseif ($upMsgRec == 'R3D1R') {
            $elec['action']   = 7;
            $elec['redirect'] = $redirect ?: 0;
            // <--- Terminan funciones uso interno
        } elseif ($upMsgRec == 'FR0M3X7') {
            // Ojo, éste valor sólo puede venir de otra función interna
            $query = $this->db->query("SELECT * FROM whatsapp_bot_op
                WHERE parent = ? LIMIT 1", [$idAntOp]);
            $elec = $query->row();
            $elec['action'] = 1;
        }

        return (empty($elec)) ? "" : (object)$elec;
    }

    public function cierrabots() {
        $this->load->model("whatsapp_model");
        $this->cierraencuestas();
        $query = $this->db->query("SELECT ws.*, TIMESTAMPDIFF(SECOND, we.old, NOW()) AS 'antiq'
            FROM whatsapp_session ws JOIN
            (SELECT id_session, MAX(datetime_received) old from whatsapp_entry
            WHERE id_session IS NOT NULL GROUP BY id_session) we
            ON we.id_session = ws.id
            WHERE ws.subtipo = 'Bot' AND ws.datetime_end IS NULL");
        $abiertas = $query->num_rows();
        $cerradas = 0;
        if ($abiertas > 0) {
            $sesabs = $query->result(); // Sesiones de Bot abiertas
            $query = $this->db->query("SELECT id_wacta, wait_time * 60 wt FROM whatsapp_bot WHERE active = 1");
            $times = [];
            foreach ($query->result() as $bot) {
                $times[$bot->id_wacta] = $bot->wt;
            }
            foreach ($sesabs as $ses) {
                if (array_key_exists($ses->id_wacta, $times)) {
                    if ($ses->antiq >= $times[$ses->id_wacta]) {
                        $este = $this->whatsapp_model->terminases([
                            "sid"=>$ses->id,
                            "cid"=>$ses->id_contact,
                            "razon"=>"Abandono"
                        ]);
                        if ($este) { $cerradas++; }
                    }
                } else {
                    $este = $this->whatsapp_model->terminases([
                        "sid"=>$ses->id,
                        "cid"=>$ses->id_contact,
                        "razon"=>"Bot apagado"
                    ]);
                    if ($este) { $cerradas++; }
                }
            }
        }

        return "Cerradas " . $cerradas . ", de " . $abiertas . ", sesiones BOT.\n";
    }

    /**
     * Closes the surveys.
     * 
     * This private method is responsible for closing the surveys.
     * It is called internally within the Wabot_model class.
     */
    private function cierraencuestas() {
        $query = $this->db->query("SELECT id, id_contact FROM whatsapp_session
            WHERE datetime_end is null AND subtipo = 'Encuesta'
            AND datetime_start < (NOW() - INTERVAL 1 MINUTE)");
        foreach ($query->result() as $ses) {
            $this->whatsapp_model->terminases([
                "sid"=>$ses->id,
                "cid"=>$ses->id_contact,
                "razon"=>"Abandono"
            ]);
        }
    }

    public function pasaagente($ses) {
        /********************  V A L I D A C I O N   D E   H O R A R I O S   D E   C A M P A Ñ A  ********************/
        $query = $this->db->query("SELECT id, id_wacta, out_of_time, active FROM whatsapp_bot
        WHERE id_wacta = '$ses->id_wacta' AND active = 1");
        $bot = $query->row();
        // Traer registro de horario que coincida con dia y hora actual
        $query = $this->db->query("SELECT h.id
            FROM campaign_hour h
            JOIN whatsapp_cuentas wac on wac.id_campaign = h.id_campaign
            WHERE wac.id = ?
            AND h.dia = DAYOFWEEK(now())
            AND h.inicio <= TIME(now()) AND h.fin >= TIME(now());
        ", [$bot->id_wacta]);
        // Si hay un registro debe haber agentes disponibles continúa, si no, termina la sesión.
        if ($query->num_rows() == 0) {
            $this->load->model("whatsapp_model");
            $this->whatsapp_model->sale_texto(["ses"=>$ses, "watext"=>$bot->out_of_time]);
            $this->whatsapp_model->terminases(["sid"=>$ses->id,"cid"=>$ses->id_contact,"razon"=>'Fuera de Horario']);

            return;
        }
        /********************  T E R M I N A   V A L I D A C I O N   D E   H O R A R I O S  ********************/

        $watext = "Gracias.\n\nSerás atendido por un agente, espera por favor.";
        $this->load->model("whatsapp_model");
        $this->whatsapp_model->sale_texto(["ses"=>$ses, "watext"=>$watext]);
        $this->whatsapp_model->terminases(["sid"=>$ses->id, "cid"=>$ses->id_contact, "razon"=>"Pasa a agente"]);
        $this->whatsapp_model->creases(["cid"=>$ses->id_contact, "wid"=>$ses->id_wacta, "tipo"=>"Entrante",
            "flujo"=>"noBot", "trans"=>$ses->id_user
        ]);

        return;
    }

    // sesion, bot, elección de acuerdo a mensaje recibido
    public function enviaops($ses, $bot, $elec) {
        $pre = "*" . $bot->name . " :*\n\n";
        $par = $historia = $elec->id; // parent de opciones a enviar
        $ops = true; // Enviar las opciones
        $ter = $msg = $enc = false; // Terminar, mensaje y encuesta
        $bid = $bot->id;
        if (isset($elec->x)) {
            $pre .= "Lo siento, no entiendo tu mensaje ...\n\n";
        }
        switch ($elec->action) {
            case '2': // Pasar a agente
                $this->pasaagente($ses);
                return;
                break;
            case '3': // Terminar con despedida bot sin opciones
                $ter = "Terminar";
                $ops = false;
                $msg = $pre . $bot->bye;
                $historia = 'No';
                break;
            case '4': // Enviar submenú, sin seleccionador y terminar sesión
                $ter = "Info y terminar";
                $historia = 'No';
                break;
            case '5': // Regresar al inicio
                $par = $historia = 0;
                break;
            case '7': // Redirigir flujo normal a un punto específico
                (stripos($elec->redirect,'-') !== false) ?
                    list($bid, $par) = explode('-', $elec->redirect) :
                    $par = $elec->redirect;
                $historia = $elec->redirect;
                break;
            case '6': // Terminar sesión sin mensaje y pasar a preguntas de encuesta
                $ter = "Terminar y encuesta";
                $ops = false;
                $enc = true;
                $historia = 'No';
                break;
            default: // Enviar submenú y esperar respuesta
                break;
        }
        if ($historia !== 'No') {
            $this->db->query("UPDATE whatsapp_session SET paso = ? WHERE id = ?", [$historia, $ses->id]);
            $this->db->query("INSERT INTO whatsapp_bot_history (id_session, paso)
                VALUES (?, ?)", [$ses->id, $historia]);
        }
        if ($ops) {
            $valbot = ($par == 0) ? "AND id_bot = '$bid'" : "";
            $query  = $this->db->query("SELECT * FROM whatsapp_bot_op
                WHERE parent = '$par' $valbot ORDER BY CAST(`option` AS INTEGER), `option`"
            );
            $rows = $query->num_rows();
            if ($rows<1) {
                $msg = $pre . "No hay opciones para mostrar\n\n";
                $msg .= ($elec->id) ? "*R* egresa por favor." : "";
            } else {
                if ($ter) {
                    $pos = "\n\n" . $bot->bye;
                } else {
                    $pos = ($rows > 1) ? "\n\nElige una opción" : "";
                }
                if (empty($elec->id) && stripos($ses->paso,'-') === false && empty($elec->x)) {
                    $pre .= $bot->intro . "\n\n";
                } else {
                    if (empty($ter)) {
                        $pos .= "\n*R* Menú anterior.";
                    }
                }
                $msg = "";
                foreach ($query->result() as $row) {
                    $msg .= ($row->option == '-') ? "" : "*" . $row->option . "* -> ";
                    $msg .= (strpos($row->label, '{') !== false) ?
                        $this->repvar($ses->id_contact, $row->label) : $row->label;
                    $msg .=  ($row->option == '-') ? "\n\n" : "\n";
                }
                $msg = $pre . $msg . $pos;
            }
        }
        if ($msg || $ter) { $this->load->model("whatsapp_model"); }
        if ($msg) {
            $this->whatsapp_model->sale_texto(["ses"=>$ses, "watext"=>$msg]);
        }
        if ($ter) {
            $this->whatsapp_model->terminases([
                "sid"=>$ses->id,
                "cid"=>$ses->id_contact,
                "razon"=>$ter
            ]);
            if ($enc) {
                $this->load->model("warate_model");
                $this->warate_model->encuesta($ses->id_wacta, $ses->id_contact, false, $ses->id_user);
            }
        }
    }

    public function whatsapp_detalle($data) {
        if (empty($data['cuenta'])) return $data;
        $maswere = (empty($data['wacto'])) ? "" : "AND we.id_contact = '$data[wacto]'";
        $msg = ($data['pag'] === 'x') ? "we.message" :
            "CONCAT(SUBSTRING(we.message, 1, 31), IF(LENGTH(we.message)>30,' ...',''))";
        $data['prequery'] = "SELECT date_format(we.datetime_received, '$this->dfor') fecha,
            concat(u.name,' ', u.last) agente, wc.account contacto, wc.name nombre,
            $msg mensaje, we.watype tipo, we.type direccion, we.status estatus
            FROM whatsapp_entry we
            LEFT JOIN whatsapp_contact wc on wc.id=we.id_contact
            LEFT JOIN user u on u.id=we.id_user
            WHERE DATE(we.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
            AND we.id_wacta='$data[cuenta]' AND u.id IN($data[agente]) $maswere
            AND we.status <> 'Confirma' ORDER BY we.datetime_received DESC, wc.name";

        $data = $this->datos_model->manejadorqueries($data);
        foreach ($data["data"] as $key => $row) {
            $data["data"][$key]->mensaje = json_decode('"'.$row->mensaje.'"');
        }

        return $data;
    }

    public function reporte_sesion($data) {
        if (empty($data['cuenta'])) return $data;
        $maswere = ($data['wacto']!="") ? "AND ws.id_contact = '$data[wacto]'" : "";
        $data['prequery'] = "SELECT ws.id 'sesión', date_format(ws.datetime_received, '$this->dfor') fecha,
            ifnull(wc.name,'') contacto, ifnull(wc.account,'') numero,
            time(ws.datetime_assigned) 'inicio', coalesce(time(ws.datetime_end),'') 'fin',
            if(ws.datetime_end is null, '', sec_to_time(ws.duration)) duracion,
            (SELECT count(id) FROM whatsapp_entry WHERE id_session=ws.id and status <> 'Confirma') mensajes,
            ws.message 'conclusión', case when ws.paso LIKE '%-%' THEN ws.paso ELSE ifnull(wbo.routeLvl,'') END 'ruta'
            FROM whatsapp_session ws
            LEFT JOIN whatsapp_contact wc ON wc.id=ws.id_contact
            LEFT JOIN whatsapp_bot_op wbo ON wbo.id=ws.paso
            WHERE ws.id_wacta='$data[cuenta]'
            AND DATE(ws.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
            AND ws.id_user = '9999' $maswere
            ORDER BY ws.datetime_received DESC";
        $data = $this->datos_model->manejadorqueries($data);
        return $data;
    }

    public function reporte_indicador($data) {
        if (empty($data['cuenta'])) return $data;
        $maswere = ($data['wacto']!="") ? "AND ws.id_contact = '$data[wacto]'" : "";
        $data['prequery'] = "SELECT date_format(ws.datetime_received, '$this->dfor') fecha,
            sec_to_time(round(avg(ws.duration))) 'Promedio duración',
            sec_to_time(round(avg(ws.duration_wait))) 'Promedio espera',
            count(ws.id) total,
            SUM(CASE
                WHEN ws.message = 'Terminar' THEN 1
                ELSE 0
            END) AS 'Terminar',
            SUM(CASE
                WHEN ws.message = 'Info y terminar' THEN 1
                ELSE 0
            END) AS 'Info y terminar',
            SUM(CASE
                WHEN ws.message = 'Pasa a agente' THEN 1
                ELSE 0
            END) AS 'Pasa a agente',
            SUM(CASE
                WHEN ws.message = 'Abandono' THEN 1
                ELSE 0
            END) AS 'Abandono',
            SUM(CASE
                WHEN ws.message <> 'Terminar' AND ws.message <> 'Info y terminar'
                    AND ws.message <> 'Pasa a agente' AND ws.message <> 'Abandono' THEN 1
                ELSE 0
            END) AS 'Otro'
            FROM whatsapp_session ws
            WHERE ws.id_wacta='$data[cuenta]'
            AND DATE(ws.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
            AND ws.id_user = '9999' $maswere
            GROUP BY DATE(ws.datetime_received)
            ORDER BY ws.datetime_received DESC";
        $data['totales_query'] = "SELECT 'Totales:' AS 'tot',
            sec_to_time(round(avg(ws.duration))) 'Promedio duración',
            sec_to_time(round(avg(ws.duration_wait))) 'Promedio espera',
            count(ws.id) total,
            SUM(CASE
                WHEN ws.message = 'Terminar' THEN 1
                ELSE 0
            END) AS 'Terminar',
            SUM(CASE
                WHEN ws.message = 'Info y terminar' THEN 1
                ELSE 0
            END) AS 'Info y terminar',
            SUM(CASE
                WHEN ws.message = 'Pasa a agente' THEN 1
                ELSE 0
            END) AS 'Pasa a agente',
            SUM(CASE
                WHEN ws.message = 'Abandono' THEN 1
                ELSE 0
            END) AS 'Abandono',
            SUM(CASE
                WHEN ws.message <> 'Terminar' AND ws.message <> 'Info y terminar'
                    AND ws.message <> 'Pasa a agente' AND ws.message <> 'Abandono' THEN 1
                ELSE 0
            END) AS 'Otro'
            FROM whatsapp_session ws
            WHERE ws.id_wacta='$data[cuenta]'
            AND DATE(ws.datetime_received) BETWEEN '$data[min]' AND '$data[max]'
            AND ws.id_user = '9999' $maswere";
        $data = $this->datos_model->manejadorqueries($data);
        return $data;
    }

    public function actualizar_campo_activo($data) {
        $active = (empty($data['active'])) ? '0' : $data['active'];
        if ($active == '1') {
            $this->db->query("UPDATE whatsapp_bot SET active = '0' WHERE id_wacta = ? and active = '1'", [$data['wid']]);
        }
        $query = $this->db->query("UPDATE whatsapp_bot set active=? where id=?",
            [ $active, $data['id'] ]
        );
        if ($query) {
            return "Bot actualizado con éxito.";
        }
        return ['error' => $this->db->error_message()];
    }

    private function repvar($cid, $texto) : string {
        if (strpos($texto, '{#') !== false) { // Variables permanentes
            $campo = $this->whatsappfun->traeCampo($cid);
            $arrrep = [];
            foreach ($campo as $key => $var) {
                $str = '{#' . $key . '}';
                $arrrep[$str] = $var->valor;
            }
            $texto = strtr($texto, $arrrep);
        }
        if (strpos($texto, '{&') !== false) { // Variables temporales
            $campo = $this->whatsappfun->traeCampo($cid, 'temporal');
            $arrrep = [];
            foreach ($campo as $key => $var) {
                $str = '{&' . $key . '}';
                $arrrep[$str] = $var->valor;
            }
            $texto = strtr($texto, $arrrep);
        }
        $texto = preg_replace("/\{(.*?)\}/i", "", $texto);
        // OJO: las variables seguras JAMÁS se regresarán al usuario !!!

        return $texto;
    }

    public function get_scripts() {
        $campIn = $this->udata['campanas'];
        $where_campaign = ( $this->udata['perfil'] !== 'admin' ) ? '' : "AND bs.id_campaign IN ( $campIn )";
        $query = $this->db->query("SELECT bs.id, bs.nombre AS name
        FROM whatsapp_bot_script bs
        WHERE bs.active = 1 $where_campaign;");
        $scripts = $query->result_array();

        return $scripts;
    }

    //SCRIPTS
    public function scripts_list($data = []) {
        $pag = (empty($data['pag'])) ? 0 : (int)$data['pag'];
        $rpp = (empty($data['rpp'])) ? 5 : (int)$data['rpp'];

        $data['campanas'] = $this->datos_model->getCampanas();
        $campIn = array_column($data['campanas'], 'id');
        $campIn = implode(',', $campIn);
        // No puede haber usuarios en el sistema sin al menos una campaña asignada
        // $campIn = ($campanas == '') ? '0' : $campanas;
        $data['scripts_options'] = $this->get_scripts();
        $query = $this->db->query("SELECT COUNT(*) tot FROM whatsapp_bot_script WHERE id_campaign IN (" . $campIn . ")");
        $data['tot'] = $query->row()->tot;
        $query = $this->db->query("SELECT bs.id, bs.id_campaign, bs.nombre, bs.siespera,
            bs.sibien, bs.simal, bs.active, c.name as campaign
            FROM whatsapp_bot_script bs
            LEFT JOIN campaign c ON bs.id_campaign = c.id
            WHERE bs.id_campaign IN (" . $campIn . ")
            LIMIT ?, ?", [$pag, $rpp]);
        $scripts = $query->result_array();
        // //MODIFICAMOS EL ARRAY Y LO PASAMOS A USAR SUS IDS COMO INDICE
        $arrayScripts = [];
        $scrIn = 0;
        foreach ($scripts as $row) {
            $arrayScripts[$row['id']] = $row;
            $scrIn .= ','.$row['id'];
        }
        //CONSULTAMOS TODO DE LOS SCRIPS DEVUELTOS
        $query = $this->db->query("SELECT bss.id_whatsapp_bot_script, bss.id, bss.paso, bss.camp,
            bss.varb, bss.tipo, bss.modi, bss.cond, bss.orden, bss.active
            FROM whatsapp_bot_script bs
            JOIN whatsapp_bot_scr_steps bss on bss.id_whatsapp_bot_script = bs.id
            JOIN campaign c on c.id = bs.id_campaign
            WHERE c.id IN (" . $campIn . ")
            AND bs.id IN ($scrIn)
            ORDER BY bs.id ASC, bss.orden ASC
        ");
        $scrsActs = $query->result_array();
        //COLOCAMOS LAS FUNCIONES O LAS ACCIONES DENTRO DEL ARRAY DE SCRIPTS
        foreach ($scrsActs as $row) {
            $arrayScripts[$row['id_whatsapp_bot_script']]['steps'][] = $row;
        }
        $data['array_scripts'] = $arrayScripts;

        return $data;
    }

    public function script_save($id = 0, $data = []) {
        if( $id == 0 ){//INSERTAMOS
            if ($this->db->insert('whatsapp_bot_script', $data)) {
                return 'Script agregado correctamente.';
            } else {
                return FALSE;
            }
        } else {//ACTUALIZAMOS
            $where = ['id' => $id];
            $this->db->update('whatsapp_bot_script', $data, $where);
            return 'Script actualizado correctamente.';
        }
    }

    public function script_delete($id = 0) {
        $this->db->trans_start();
            //Si existe un bot con este script inicial, lo actualizamos antes de eliminar
            $this->db->update('whatsapp_bot', ['ini_script' => NULL], ['ini_script' => $id]);
            //Eliminamos todos los pasos de ese script
            $this->db->where('id_whatsapp_bot_script', $id);
            $this->db->delete('whatsapp_bot_scr_steps');
            //Eliminamos el script
            $this->db->where('id', $id);
            $this->db->delete('whatsapp_bot_script');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['tipo' => 'error', 'msg' => $this->db->error()];
        } else {
            $this->db->trans_commit();
            return ['tipo' => 'ok', 'msg' => 'Registro eliminado'];
        }
    }

    public function script_delete_step($id = 0) {
        $where = ['id' => $id];
        $success = true;
        if( $this->db->delete('whatsapp_bot_scr_steps', $where) ) {
            $msg = "Registro eliminado correctamente.";
        }
        else {
            $success = false;
            $msg = 'Error: No se pudo eliminar el registro';
        }

        return ['success' => $success, 'msg' => $msg];
    }

    public function step_save( $id = 0, $data = [] ) {
        if( $id == 0 ){//INSERTAMOS
            if ($this->db->insert('whatsapp_bot_scr_steps', $data)) {
                return 'Registro agregado correctamente';
            }
            else {
                return ['error' => 'Error: no se pudo guardar el registro'];
            }
        } else {//ACTUALIZAMOS
            $where = ['id' => $id];
            $this->db->update('whatsapp_bot_scr_steps', $data, $where);
            return 'Registro actualizado correctamente.';
        }
    }

    public function extapi_met_list() {
        $query = $this->db->query("SELECT b.id, a.name, b.info
        FROM extapi_met b
        LEFT JOIN extapi a ON a.id = b.id_extapi
        ORDER BY a.name ASC, b.info ASC
        ");
        return $query->result();
    }

    /**
     * Función para escribir la ruta de una tabla con parents
     * con todos los niveles de padres usando la tabla whatsapp_bot_op
     * escribiendo en la columna routeId
     *
     * @param int $bid id del bot
     * @return void
     */
    public function writeRouteId($bid = null) {
        if (empty($bid)) return ['success' => false, 'message' => 'Bot no válido'];
        $this->db->query("UPDATE whatsapp_bot_op m
            JOIN
            (WITH RECURSIVE cte AS
                (SELECT id, id_bot, CAST(id AS CHAR(200)) AS path, 0 as depth
                FROM whatsapp_bot_op WHERE parent = 0
                UNION ALL
                SELECT c.id, c.id_bot, CONCAT(cte.path, ',', c.id), cte.depth+1
                FROM whatsapp_bot_op c JOIN cte ON
                cte.id=c.parent)
            SELECT * FROM cte WHERE id_bot = ? ORDER BY path) AS rutas
            ON rutas.id = m.id
            SET m.routeId = rutas.path, m.depth = rutas.depth
        ", [$bid]);
        if ($this->writeRouteLvl($bid)) return ['success'=>true,'message'=>'Rutas escritas'];

        return ['success'=>false,'message'=>'Error al escribir rutas'];
    }

    /**
     * Función para escribir la ruta de una tabla con parents
     * con todos los niveles de padres usando la tabla whatsapp_bot_op
     * escribiendo en la columna routeLvl
     *
     * @param int $bid id del bot
     * @return void
     */
    private function writeRouteLvl($bid = 0) {
        $this->db->query("UPDATE whatsapp_bot_op SET routeLvl = '' WHERE id_bot = ?", [$bid]);
        $lvlSize = $this->db->query("SELECT MAX(depth) max FROM whatsapp_bot_op WHERE id_bot = ?", [$bid])->row()->max;
        $query = $this->db->query("SELECT id, parent, `option`, depth FROM whatsapp_bot_op
            WHERE id_bot = ? ORDER BY parent, `option`", [$bid]);
        $ops = $query->result();
        $ops = array_column($ops, NULL, 'id');
        $basic = [];
        for ($i=0; $i < ($lvlSize + 1); $i++) {
            $basic[$i] = 0;
        }
        foreach ($ops as $op) {
            $toSave = (empty($op->parent)) ? $basic : $ops[$op->parent]->tosaveArr;
            $toSave[$op->depth] = $op->option;
            $ops[$op->id]->tosaveArr = $toSave;
            if (empty($op->parent)) {
                $basic = $toSave;
            } else {
                $ops[$op->parent]->tosaveArr = $toSave;
            }
            $toSave = implode(',', $toSave);
            $ulq = $this->db->query("UPDATE whatsapp_bot_op SET routeLvl = ? WHERE id = ?",
            [$toSave, $op->id]);
            if (!$ulq) return false;
        }

        return true;
    }

}
