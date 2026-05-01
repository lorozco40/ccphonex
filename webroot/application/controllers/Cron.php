<?php

defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');
set_time_limit(0);

class Cron extends CI_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $hora = (int)date("H");
        $minuto = (int)date("i");
        echo "\n";

        if ($hora == 4 && $minuto == 4) {
            /* Vacía la tabla asteriskcdrdb.cel todos los días a las 04:04:00 hrs. después del reboot
               Vacía la tabla asteriskcdrdb.queue_log junto con la anterior */
            $this->datos_model->truncacel();
            echo date("Y-m-d h:i:s")." Tabla asteriskcdrdb.cel y ahora también asteriskcdrdb.queue_log truncadas.\n";
            /* Calcula tiempos de sesion del día anterior */
            $ayer = date('Y-m-d',strtotime('-1 days'));
            $this->load->model("repoback_model");
            $res = $this->repoback_model->sesion($ayer);
            echo date("Y-m-d h:i:s")." Registros Sesion del dia $ayer calculados.\n";
            $res = $this->repoback_model->inbound($ayer);
            echo date("Y-m-d h:i:s")." Registros Inbound del dia $ayer calculados.\n";
            $res = $this->repoback_model->outbound($ayer);
            echo date("Y-m-d h:i:s")." Registros Outbound del dia $ayer calculados.\n";
            $res = $this->repoback_model->abandono($ayer);
            echo date("Y-m-d h:i:s")." Registros Abandono del dia $ayer calculados.\n";
            $res = $this->repoback_model->atendidas($ayer);
            echo date("Y-m-d h:i:s")." Registros Atendidas del dia $ayer calculados.\n";
            $res = $this->repoback_model->acw($ayer);
            echo date("Y-m-d h:i:s")." Registros ACW del dia $ayer calculados.\n";
            $res = $this->repoback_model->poragente($ayer);
            echo date("Y-m-d h:i:s")." Registros Llamadas por agente del dia $ayer calculados.\n";
        }

        // SFTP send. Activar en producción según las necesidades, comentado default (por el momento, antes del ToDo)
        // ToDo Generalizar proceso creando formulario con hora, minuto, datos de servidor y reporte a enviar
        // if ($hora == 22 && $minuto == 60 ) {
        //     $this->load->model("reportes_model");
        //     $res = $this->reportes_model->inOutBoundForm([46,47]);// ID´s de los formularios
        //     echo date("Y-m-d h:i:s")." Cell Point: $res.\n";
        // }

        /* completa las sesiones del día anterior */
        if ($hora == 14 && $minuto == 1) {
            $ayer = date('Y-m-d',strtotime('-1 days'));
            $this->load->model("repoback_model");
            $this->repoback_model->sesion($ayer);
        }

        /* regresa a null la columna busy de los despachadores (liberar registros) */
        if ($hora == 3 && $minuto == 1) {
            $this->load->model("desp_model");
            $busyclean = $this->desp_model->regresabusy();
            if ($busyclean) echo date("Y-m-d h:i:s")." Registros liberados de despachadores.\n";
            else echo date("Y-m-d h:i:s")." Error al liberar registros de despachadores o no registros por liberar.\n";
        }

        /* Procesos que corren cada media hora */
        if (fmod($minuto, 30) == 0) {
            /* Eliminar grabaciones de carpeta /files que sea mas vieja de 2 horas */
            $files  = glob("/var/www/html/files/*.wav");
            $now    = time();
            $cuenta = 0;

            foreach ($files as $file) {
                if (is_file($file) && $now - filemtime($file) >= 7200) { // 2 horas 60*60*2
                    unlink($file);
                    $cuenta++;
                }
            }
            echo date("Y-m-d h:i:s")." Eliminadas ".$cuenta." grabaciones en carpeta pública.\n";
        }

        if (fmod($minuto, 5) == 0) {
            /* cada 5 minutos */
            /* cálculo de tarificación */
            //$this->load->model("tarificacion_model");
            //echo date("Y-m-d h:i:s")." ".$this->tarificacion_model->calcular()."\n";
        }

        /* A partir de este punto y hasta las repeticiones mas seguidas, todos los procesos corren una vez cada minuto */

        /* checar y asignar email cuentas relacionadas a cada agente logueado */
        $this->load->model("email_model");
        $log = $this->email_model->consulta_mails();
        echo date("Y-m-d h:i:s")." ".$log."\n";

        /* Elimina datos de sesión y cierra sesiones inactivas */
        $cuenta = $this->datos_model->ses_ab();
        echo date("Y-m-d h:i:s")." Cerradas ".$cuenta." sesiones inactivas.\n";

        /* Videollamada */
        /* Quitar llamadas "En cola" con más de un minuto de antigüedad y completar nombres de grabación */
        //$this->load->model("videollamada_model");
        //$this->videollamada_model->cron();

        /* WhatsApp */
        /* Asginar nuevos a agente disponible */
        $this->load->model("whatsapp_model");
        $asignacion = $this->whatsapp_model->asignar_agente();
        echo date("Y-m-d h:i:s")." ".$asignacion;
        /* Cuenta de session masivos para saber si enviar registros */
        $wamas = $this->whatsapp_model->masivo_activo();
        echo date("Y-m-d h:i:s")." WhatsApp campañas masivo: ".$wamas."\n";

        $this->load->model("wabot_model");
        $wabotcierre = $this->wabot_model->cierrabots();
        echo date("Y-m-d h:i:s")." ".$wabotcierre;

        /* Actualiza status de sms's mas viejos de 2 minutos */
        $this->load->model("sms_model");
        $smsactualiza = $this->sms_model->actualiza();
        $smsactualiza .= $this->sms_model->actualiza('sms_campaign');
        echo date("Y-m-d h:i:s").$smsactualiza."\n";

        /* Actualiza status de sms's por modulo PIT mas viejos de 2 minutos */
        $this->load->model("pit_model");
        $pitactualiza = $this->pit_model->actualiza();
        echo date("Y-m-d h:i:s").$pitactualiza."\n";

        /* Elimina todos los redireccionados del modulo PIT que ya no esten vigentes*/
        $num_deleted = $this->pit_model->delete_old_redirect();
        echo date("Y-m-d h:i:s").' '.$num_deleted." redirecciones eliminadas de PIT\n";

        /* Checar si hay predictivo activo y terminar las llamadas con "Error" (sin regreso desde Asterisk) */
        $this->load->model("desp_model");
        $preds = $this->desp_model->getActivo();
        echo date("Y-m-d h:i:s")." Despachador predictivo activo: ".count($preds)."\n";
        foreach ($preds as $pred) {
            $noreg = $this->desp_model->noregresa($pred->id);
            echo date("Y-m-d h:i:s") . " Predictivo $pred->id AMD: $noreg\n";
        }

        /* Semáforos CRM */
        //$this->load->model("crm_model");
        //$this->crm_model->croninformar();
        //$res = $this->crm_model->cron();
        //echo date("Y-m-d h:i:s") . " " . $res . "\n";

        /* actualiza la información de las colas Asterisk */
        $this->load->helper("fun_helper");
        colasabase();

        /* Procesos que corren 60/$veces por minuto, 2 a 5 veces, optimo 3, vigilar por desempeño de servidor! */
        $veces = 2;
        for ($i=1; $i <= $veces; $i++) {
            /* paso de llamadas a tabla call_entry */
            $setardo = $this->datos_model->llamadas();
            echo date("Y-m-d h:i:s")." Llamadas ($setardo).\n";

            //$pasovl = $this->videollamada_model->asign_age_llam();
            //echo date("Y-m-d h:i:s")." $pasovl\n";

            /* Envío masivo de Whatsapp, desactivado para prevenir baneos
                Reactivado bajo riesgo del usuario
            */
            if ($wamas > 0) {
                $wamasres = $this->whatsapp_model->masivo();
                echo date("Y-m-d h:i:s")." WhatsApp: ".$wamasres."\n";
            }

            /* Lanzador de llamadas predictivo */
            if (count($preds)>0) {
                $colas = colas();
            }
            foreach ($preds as $pred) {
                $lanzadas = $this->desp_model->lanzar($pred->id, $colas[$pred->queue]);
                echo date("Y-m-d h:i:s")." Despachador predictivo $pred->id lanzadas $lanzadas, cola $pred->queue\n";
            }

            if ($i < $veces) sleep(60/$veces);
        }
    }
}
?>
