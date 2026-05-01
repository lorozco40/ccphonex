<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Acceso extends MY_Controller
{

    public function login() {
        if ($this->session->userdata('uid')) { redirect(); }
        if ($this->datos_model->ip_baneada($this->input->ip_address())) {
            $this->session->set_flashdata('errormsg', 'Tu IP ha sido baneada, favor de contactar con el administrador.');
            redirect();
        }
        if (!$this->session->userdata('login_attempts')) {
            $this->session->set_userdata('login_attempts', 1);
        } else {
            $attempts = $this->session->userdata('login_attempts') + 1;
            $this->session->set_userdata('login_attempts', $attempts);
        }
        $this->form_validation->set_rules('maillogin', 'e-mail', 'required|valid_email');
        $this->form_validation->set_rules('passwordlogin', 'password', 'required');
        if (!$this->form_validation->run() && $this->session->userdata('login_attempts') < 4) {
            $this->session->set_flashdata('errormsg', 'Datos incorrectos, favor de verificar.');
            redirect();
        } elseif (!$this->form_validation->run() && $this->session->userdata('login_attempts') > 4) {
            $this->session->set_flashdata('errormsg', 'Demasiados intentos, favor de intentar más tarde.');
            $this->datos_model->baneaip($this->input->ip_address());
            redirect();
        } else {
            $this->load->model('usuario_model');
            $valid_user = $this->usuario_model->valida_usuario($_POST['maillogin'], $_POST['passwordlogin']);
            if (is_object($valid_user)) {
                $inter = false;
                $donde = ($valid_user->id == '1') ? 'usuarios' : $valid_user->pagini;
                if (!empty($valid_user->inter)) {
                    $inter = true;
                    $donde = 'inter';
                }
                $nuevosdatos = array(
                   'uid'   => $valid_user->id,
                   'token' => $valid_user->token,
                   'inter' => $inter,
                );
                $this->session->set_userdata($nuevosdatos);
                $this->datos_model->user_activity();
                redirect($donde);
            } else {
                if ($this->session->userdata('login_attempts') > 4) {
                    $this->session->set_flashdata('errormsg', 'Demasiados intentos, favor de intentar más tarde.');
                    $this->datos_model->baneaip($this->input->ip_address());
                    redirect();
                } else {
                    $this->session->set_flashdata('errormsg', $valid_user['error']);
                    redirect();
                }
            }
        }
    }

    public function logout() {
        $this->datos_model->user_activity('Logout', 0);
        $uid = $this->session->userdata('uid') ?? 999999;
        $this->session->sess_destroy();
        $this->db->query("DELETE from ses_ab where uid = ?", [$uid]);
        $this->db->query("UPDATE user_trans_opts set busy = '' WHERE busy not in (SELECT DISTINCT(uid) FROM ses_ab WHERE uid <> '0')");
        // Licencias usadas por campaña de éste usuario
        $this->load->model("campanas_model");
        $this->campanas_model->elimina_licencias_usuario($uid);
        redirect();
    }

}

?>
