<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Session_database_driver extends CI_Session_database_driver
{

    public function write($session_id, $session_data) {
        // Prevent previous QB calls from messing with our queries
        $this->_db->reset_query();

        // Was the ID regenerated?
        if (isset($this->_session_id) && $session_id !== $this->_session_id) {
            if ( ! $this->_release_lock() OR ! $this->_get_lock($session_id)) {
                return $this->_failure;
            }

            $this->_row_exists = FALSE;
            $this->_session_id = $session_id;
        } elseif ($this->_lock === FALSE) {
            return $this->_failure;
        }

        $_uid = 0;
        if (!empty($session_data)) {
            $_uid = substr($session_data, stripos($session_data, "uid|s:")+6);
            $_uid = (int)trim(explode(":", $_uid)[1], '"');
        }

        if ($this->_row_exists === FALSE) {

            $insert_data = array(
                'id' => $session_id,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'timestamp' => time(),
                'data' => ($this->_platform === 'postgre' ? base64_encode($session_data) : $session_data),
                'uid' => $_uid
            );

            if ($this->_db->insert($this->_config['save_path'], $insert_data)) {
                $this->_fingerprint = md5($session_data);
                $this->_row_exists = TRUE;
                return $this->_success;
            }

            return $this->_failure;
        }

        $this->_db->where('id', $session_id);
        if ($this->_config['match_ip']) {
            $this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
        }

        $update_data = array('timestamp' => time(), 'uid' => $_uid);
        if ($this->_fingerprint !== md5($session_data)) {
            $update_data['data'] = ($this->_platform === 'postgre')
            ? base64_encode($session_data)
            : $session_data;
        }

        if ($this->_db->update($this->_config['save_path'], $update_data)) {
            $this->_fingerprint = md5($session_data);
            return $this->_success;
        }

        return $this->_failure;
    }

}
