<?php

function colas() {
    $pruebas = "default has 0 calls (max unlimited) in 'ringall' strategy (0s holdtime, 0s talktime), W:0, C:0, A:0, SL:0.0%, SL2:0.0% within 0s
   No Members
   No Callers

2907 has 4 calls (max unlimited) in 'leastrecent' strategy (0s holdtime, 0s talktime), W:0, C:0, A:0, SL:0.0%, SL2:0.0% within 60s
   Members:
      Aldo (Local/5100@from-queue/n from hint:5100@ext-local) (ringinuse enabled) (dynamic) (Not in use) has taken no calls yet
      Kinon (Local/5090@from-queue/n from hint:5090@ext-local) (ringinuse enabled) (dynamic) (Not in use) has taken no calls yet
   No Callers

9809 has 3 calls (max unlimited) in 'leastrecent' strategy (0s holdtime, 0s talktime), W:2, C:1, A:1, SL:90.0%, SL2:98.0% within 60s
   Members:
      Aldo (Local/5100@from-queue/n from hint:5100@ext-local) (ringinuse enabled) (dynamic) (Not in use) has taken no calls yet
      Kinon (Local/5090@from-queue/n from hint:5090@ext-local) (ringinuse enabled) (dynamic) (Not in use) has taken no calls yet
      Genaro (Local/5091@from-queue/n from hint:5091@ext-local) (ringinuse enabled) (dynamic) (Not in use) has taken no calls yet
   No Callers";
   $data = shell_exec('/usr/sbin/asterisk -rx "queue show"');
    if (empty($data) && ENVIRONMENT=='development'){
        $data = $pruebas;
    }
    if (!empty($data)) {
        $data = explode("\n", $data);
        $queue = "default";
        $colas = array("wait"=>0, "answered"=>0, "hanged"=>0, "longestwait"=>"0:00");
        $miembro = $caller = "no";
        foreach ($data as $linea) {
            if(strlen($linea)>0 && $linea[0]!=" ") {
                $espacios = explode(' ',trim($linea));
                $espacios_2 = !empty($espacios[2]) ? $espacios[2] : 0;
                $espacios_14 = !empty($espacios[14]) ? $espacios[14] : 0;
                $espacios_15 = !empty($espacios[15]) ? $espacios[15] : 0;
                $espacios_16 = !empty($espacios[16]) ? $espacios[16] : 0;
                $queue = $espacios[0];
                $colas[$queue]['wait'] = $espacios_2;
                $colas['wait'] += (int)$espacios_2;
                $explode_14 = explode(':',trim($espacios_14));
                $explode_14 = count($explode_14) > 1 ? $explode_14[1] : "";
                $explode_15 = explode(':',trim($espacios_15));
                $explode_15 = count($explode_15) > 1 ? $explode_15[1] : "";
                $explode_16 = explode(':',trim($espacios_16));
                $explode_16 = count($explode_16) > 1 ? $explode_16[1] : "";
                $colas[$queue]['answered'] = rtrim($explode_14,",");
                $colas['answered'] += (int)rtrim($explode_14,",");
                $colas[$queue]['hanged'] = rtrim($explode_15,",");
                $colas['hanged'] += (int)rtrim($explode_15,",");
                $colas[$queue]['servicelevel'] = rtrim($explode_16,",");
            } elseif(strlen($linea)>0 && $miembro == "si") {
                if (substr($linea, 0, 5)=="   No" || substr($linea, 0, 5)=="   Ca") {
                    $miembro = $caller = "no";
                } else {
                    $espacios = explode('(',trim($linea));
                    $exten = explode('@',explode('/',trim($linea))[1])[0];
                    if(stripos($linea, 'Not in use') !== FALSE) {
                        $status = 'Not in use';
                    } elseif (stripos($linea, 'in call') !== FALSE) {
                        $status = 'In call';
                    } else {
                        $status = 'Unavailable';
                    }
                    $colas[$queue]['members'][$exten] = $status;
                }
            } elseif(strlen($linea)>0 && $caller == "si") {
                if (substr($linea, 0, 5)=="   No" || substr($linea, 0, 5)=="   Ca") {
                    $miembro = $caller = "no";
                } else {
                    if (stripos($linea, 'wait') !== FALSE) {
                        $uno = explode('wai',trim($linea));
                        $dos = explode(' ',$uno[1]);
                        $tre = explode(',',$dos[1]);
                        $colas[$queue]['waits'][] = $tre[0];
                        if ($tre[0]>$colas['longestwait']) $colas['longestwait'] = $tre[0];
                    }
                }
            }
            if (strlen($linea)>0 && substr($linea, 0, 6)=="   Mem") {
                $miembro = "si";
            }
            if (strlen($linea)>0 && substr($linea, 0, 5)=="   Ca") {
                $caller = "si";
            }
        }

        return $colas;
    }
    return;
}

function colasabase() {
   $data = shell_exec('/usr/sbin/asterisk -rx "queue show"');
    if (!empty($data)) {
        $data = explode("\n", $data);
        $queue = "default";
        $colas = array("wait"=>0, "answered"=>0, "hanged"=>0, "longestwait"=>"0:00");
        $miembro = $caller = "no";
        foreach ($data as $linea) {
            if(strlen($linea)>0 && $linea[0]!=" ") {
                $espacios = explode(' ',trim($linea));
                $queue = $espacios[0];
                $colas[$queue]['wait'] = $espacios[2];
                $colas['wait'] += (int)$espacios[2];
                $colas[$queue]['answered'] = rtrim(explode(':',trim($espacios[14]))[1],",");
                $colas['answered'] += (int)rtrim(explode(':',trim($espacios[14]))[1],",");
                $colas[$queue]['hanged'] = rtrim(explode(':',trim($espacios[15]))[1],",");
                $colas['hanged'] += (int)rtrim(explode(':',trim($espacios[15]))[1],",");
                $colas[$queue]['servicelevel'] = rtrim(explode(':',trim($espacios[16]))[1],",");
            } elseif(strlen($linea)>0 && $miembro == "si") {
                if (substr($linea, 0, 5)=="   No" || substr($linea, 0, 5)=="   Ca") {
                    $miembro = $caller = "no";
                } else {
                    $espacios = explode('(',trim($linea));
                    $exten = explode('@',explode('/',trim($linea))[1])[0];
                    if(stripos($linea, 'Not in use') !== FALSE) {
                        $status = 'Not in use';
                    } elseif (stripos($linea, 'in call') !== FALSE) {
                        $status = 'In call';
                    } else {
                        $status = 'Unavailable';
                    }
                    $colas[$queue]['members'][$exten] = $status;
                }
            } elseif(strlen($linea)>0 && $caller == "si") {
                if (substr($linea, 0, 5)=="   No" || substr($linea, 0, 5)=="   Ca") {
                    $miembro = $caller = "no";
                } else {
                    if (stripos($linea, 'wait') !== FALSE) {
                        $uno = explode('wai',trim($linea));
                        $dos = explode(' ',$uno[1]);
                        $tre = explode(',',$dos[1]);
                        $colas[$queue]['waits'][] = $tre[0];
                        if ($tre[0]>$colas['longestwait']) $colas['longestwait'] = $tre[0];
                    }
                }
            }
            if (strlen($linea)>0 && substr($linea, 0, 6)=="   Mem") {
                $miembro = "si";
            }
            if (strlen($linea)>0 && substr($linea, 0, 5)=="   Ca") {
                $caller = "si";
            }
        }

        $ci =& get_instance();
        $ci->load->database();

        $query = $ci->db->query("SELECT * from queue");
        $queues = $query->result();

        $activas = array();
        foreach ($colas as $key => $camp) {
            if ($key != 'default') {
                $activas[] = $key;
            }
        }
        foreach ($queues as $row) {
            if (!in_array($row->name, $activas)) {
                $ci->db->query("UPDATE queue set active = 0 where id = $row->id");
            } else {
                $ci->db->query("UPDATE queue set active = 1 where id = $row->id");
            }
            $dids[] = $row->name;
        }
        foreach ($colas as $key => $camp) {
            if (is_numeric($key) && !in_array($key, $dids)) {
                $ci->db->query("INSERT into queue (name) values (?)", array($key));
            }
        }

        return $colas;
    }
    return;
}

?>
