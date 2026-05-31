<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analisis extends MY_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('analisis_model');
    }

    public function detalle() {
        $this->assertAllowed();

        $callId = (int)$this->input->post('call_id');
        $callType = $this->normalizeCallType($this->input->post('call_type'));
        if (!$this->validInput($callId, $callType)) {
            return;
        }

        $result = $this->analisis_model->detalle($callId, $callType, $this->session->userdata('uid'));
        $this->respond($result);
    }

    public function solicitar() {
        $this->assertAllowed();

        $callId = (int)$this->input->post('call_id');
        $callType = $this->normalizeCallType($this->input->post('call_type'));
        if (!$this->validInput($callId, $callType)) {
            return;
        }

        $result = $this->analisis_model->solicitar($callId, $callType, $this->session->userdata('uid'));
        $this->respond($result);
    }

    public function reprocesar() {
        $this->assertAllowed();

        $callId = (int)$this->input->post('call_id');
        $callType = $this->normalizeCallType($this->input->post('call_type'));
        if (!$this->validInput($callId, $callType)) {
            return;
        }

        $result = $this->analisis_model->reprocesar($callId, $callType, $this->session->userdata('uid'));
        $this->respond($result);
    }

    public function reprocesar_alta_calidad() {
        $this->assertAllowed();

        $callId = (int)$this->input->post('call_id');
        $callType = $this->normalizeCallType($this->input->post('call_type'));
        if (!$this->validInput($callId, $callType)) {
            return;
        }

        $result = $this->analisis_model->reprocesarAltaCalidad($callId, $callType, $this->session->userdata('uid'));
        $this->respond($result);
    }

    public function reprocesar_maxima_calidad() {
        $this->assertAllowed();

        $callId = (int)$this->input->post('call_id');
        $callType = $this->normalizeCallType($this->input->post('call_type'));
        if (!$this->validInput($callId, $callType)) {
            return;
        }

        $result = $this->analisis_model->reprocesarMaximaCalidad($callId, $callType, $this->session->userdata('uid'));
        $this->respond($result);
    }

    private function assertAllowed() {
        if (empty($this->udata['perfil']) || $this->udata['perfil'] !== 'admin') {
            $this->respond(['error' => 'Tu perfil no tiene acceso al analisis de llamadas a demanda.']);
            exit;
        }
    }

    private function normalizeCallType($callType) {
        return strtolower(trim((string)$callType));
    }

    private function validInput($callId, $callType) {
        if ($callId <= 0 || !in_array($callType, ['inbound', 'outbound'])) {
            $this->respond(['error' => 'Parametros invalidos para el analisis.']);
            return false;
        }

        return true;
    }

    private function respond($result) {
        Header('Content-Type: application/json; charset=utf-8');
        if (isset($result['error'])) {
            echo json_encode(['ok' => false, 'error' => $result['error']]);
            return;
        }

        echo json_encode(['ok' => true, 'data' => $result['data']]);
    }
}