<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analisis_model extends CI_Model
{
    private $defaultModel = 'tiny';

    public function detalle($callId, $callType, $requestedBy = null) {
        $call = $this->findCall($callId, $callType);
        if (empty($call)) {
            return ['error' => 'No se encontro la llamada solicitada.'];
        }

        $eligibilityError = $this->validateCall($call);
        if ($eligibilityError !== true) {
            return ['error' => $eligibilityError];
        }

        $analysis = $this->getAnalysisRow($callId, $callType);
        if (empty($analysis)) {
            return ['data' => $this->buildDraftResponse($call)];
        }

        return ['data' => $this->formatResponse($analysis)];
    }

    public function solicitar($callId, $callType, $requestedBy = null) {
        $call = $this->findCall($callId, $callType);
        if (empty($call)) {
            return ['error' => 'No se encontro la llamada solicitada.'];
        }

        $eligibilityError = $this->validateCall($call);
        if ($eligibilityError !== true) {
            return ['error' => $eligibilityError];
        }

        $analysis = $this->getAnalysisRow($callId, $callType);
        if (!empty($analysis)) {
            return ['data' => $this->formatResponse($analysis)];
        }

        $created = $this->createPending($call, $requestedBy);
        if (isset($created['error'])) {
            return $created;
        }

        return ['data' => $this->formatResponse($created)];
    }

    public function reprocesar($callId, $callType, $requestedBy = null) {
        $analysis = $this->getAnalysisRow($callId, $callType);
        if (empty($analysis)) {
            return ['error' => 'No existe un analisis previo para reprocesar.'];
        }

        return $this->queueReprocess($callId, $callType, $requestedBy, $this->defaultModel);
    }

    public function reprocesarAltaCalidad($callId, $callType, $requestedBy = null) {
        $analysis = $this->getAnalysisRow($callId, $callType);
        if (empty($analysis)) {
            return ['error' => 'No existe un analisis previo para reprocesar en alta calidad.'];
        }

        return $this->queueReprocess($callId, $callType, $requestedBy, 'base-clean');
    }

    public function reprocesarMaximaCalidad($callId, $callType, $requestedBy = null) {
        $analysis = $this->getAnalysisRow($callId, $callType);
        if (empty($analysis)) {
            return ['error' => 'No existe un analisis previo para reprocesar en maxima calidad.'];
        }

        return $this->queueReprocess($callId, $callType, $requestedBy, 'large-v3-clean');
    }

    private function queueReprocess($callId, $callType, $requestedBy, $modelName) {
        $modelName = trim((string)$modelName) ?: $this->defaultModel;

        $payload = [
            'processing_status' => 'pendiente',
            'processing_error' => null,
            'model_name' => $modelName,
            'processing_started_at' => null,
            'processing_duration_seconds' => null,
            'transcription_seconds_per_minute' => null,
            'transcription_text' => null,
            'transcription_confidence' => null,
            'sentiment_label' => 'sin_dato',
            'sentiment_score' => null,
            'sentiment_summary' => null,
            'processed_at' => null,
            'requested_by' => $requestedBy,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('call_id', $callId);
        $this->db->where('call_type', $callType);
        $ok = $this->db->update('call_ai_analysis', $payload);
        if (!$ok) {
            return ['error' => 'No fue posible marcar el analisis para reproceso.'];
        }

        $analysis = $this->getAnalysisRow($callId, $callType);

        return ['data' => $this->formatResponse($analysis)];
    }

    private function findCall($callId, $callType) {
        if ($callType === 'inbound') {
            $query = $this->db->query("SELECT ri.id call_id,
                    'inbound' call_type,
                    ri.id_campana id_campaign,
                    c.name campaign_name,
                    ri.id_agente,
                    ri.agente,
                    ri.numero,
                    ri.fecha fecha_llamada,
                    ri.duracion duracion_segundos,
                    ri.grabacion
                FROM rep_inbound ri
                LEFT JOIN campaign c ON c.id = ri.id_campana
                WHERE ri.id = ?
                LIMIT 1", [$callId]);
        } else {
            $query = $this->db->query("SELECT ro.id call_id,
                    'outbound' call_type,
                    ro.id_campaign,
                    c.name campaign_name,
                    ro.id_agente,
                    ro.agente,
                    ro.numero,
                    ro.fecha fecha_llamada,
                    ro.duracion duracion_segundos,
                    ro.grabacion
                FROM rep_outbound ro
                LEFT JOIN campaign c ON c.id = ro.id_campaign
                WHERE ro.id = ?
                LIMIT 1", [$callId]);
        }

        return $query->row();
    }

    private function validateCall($call) {
        if (empty($call->grabacion)) {
            return 'La llamada no es elegible para analisis: no tiene grabacion disponible.';
        }

        return true;
    }

    private function createPending($call, $requestedBy = null) {
        $payload = [
            'call_id' => $call->call_id,
            'call_type' => $call->call_type,
            'id_campaign' => (int)$call->id_campaign,
            'campaign_name' => (string)$call->campaign_name,
            'id_agente' => empty($call->id_agente) ? null : (int)$call->id_agente,
            'agente' => (string)$call->agente,
            'numero' => (string)$call->numero,
            'fecha_llamada' => $call->fecha_llamada,
            'duracion_segundos' => (int)$call->duracion_segundos,
            'grabacion' => (string)$call->grabacion,
            'processing_status' => 'pendiente',
            'model_name' => $this->defaultModel,
            'requested_by' => $requestedBy,
        ];

        $ok = $this->db->insert('call_ai_analysis', $payload);
        if (!$ok) {
            return ['error' => 'No fue posible registrar la llamada para procesamiento.'];
        }

        return $this->getAnalysisRow($call->call_id, $call->call_type);
    }

    private function getAnalysisRow($callId, $callType) {
        $query = $this->db->get_where('call_ai_analysis', [
            'call_id' => $callId,
            'call_type' => $callType,
        ], 1);

        return $query->row();
    }

    private function formatResponse($analysis) {
        return [
            'call_id' => (int)$analysis->call_id,
            'call_type' => $analysis->call_type,
            'campaign_name' => $analysis->campaign_name,
            'agente' => $analysis->agente,
            'numero' => $analysis->numero,
            'fecha_llamada' => $analysis->fecha_llamada,
            'duracion_segundos' => (int)$analysis->duracion_segundos,
            'grabacion' => $analysis->grabacion,
            'processing_status' => $analysis->processing_status,
            'provider' => $analysis->provider,
            'model_name' => $analysis->model_name,
            'transcription_language' => $analysis->transcription_language,
            'transcription_confidence' => is_null($analysis->transcription_confidence) ? null : (float)$analysis->transcription_confidence,
            'sentiment_label' => $analysis->sentiment_label,
            'sentiment_score' => is_null($analysis->sentiment_score) ? null : (float)$analysis->sentiment_score,
            'sentiment_summary' => $analysis->sentiment_summary,
            'transcription_text' => $analysis->transcription_text,
            'transcription_length' => empty($analysis->transcription_text) ? 0 : mb_strlen($analysis->transcription_text, 'UTF-8'),
            'processing_started_at' => $analysis->processing_started_at,
            'processing_duration_seconds' => is_null($analysis->processing_duration_seconds) ? null : (int)$analysis->processing_duration_seconds,
            'transcription_seconds_per_minute' => is_null($analysis->transcription_seconds_per_minute) ? null : (float)$analysis->transcription_seconds_per_minute,
            'processed_at' => $analysis->processed_at,
            'processing_error' => $analysis->processing_error,
            'status_message' => $this->statusMessage($analysis->processing_status),
            'can_request' => false,
            'can_reprocess' => in_array($analysis->processing_status, ['error', 'listo'], true),
            'can_reprocess_high_quality' => in_array($analysis->processing_status, ['error', 'listo'], true),
            'can_reprocess_max_quality' => in_array($analysis->processing_status, ['error', 'listo'], true),
        ];
    }

    private function buildDraftResponse($call) {
        return [
            'call_id' => (int)$call->call_id,
            'call_type' => $call->call_type,
            'campaign_name' => $call->campaign_name,
            'agente' => $call->agente,
            'numero' => $call->numero,
            'fecha_llamada' => $call->fecha_llamada,
            'duracion_segundos' => (int)$call->duracion_segundos,
            'grabacion' => $call->grabacion,
            'processing_status' => 'no_solicitado',
            'provider' => 'faster-whisper-local',
            'model_name' => $this->defaultModel,
            'transcription_language' => 'es',
            'transcription_confidence' => null,
            'sentiment_label' => 'sin_dato',
            'sentiment_score' => null,
            'sentiment_summary' => '',
            'transcription_text' => '',
            'transcription_length' => 0,
            'processing_started_at' => null,
            'processing_duration_seconds' => null,
            'transcription_seconds_per_minute' => null,
            'processed_at' => null,
            'processing_error' => null,
            'status_message' => 'La llamada aun no ha sido enviada a analisis. Solicitala a demanda desde este modal.',
            'can_request' => true,
            'can_reprocess' => false,
            'can_reprocess_high_quality' => false,
            'can_reprocess_max_quality' => false,
        ];
    }

    private function statusMessage($status) {
        switch ($status) {
            case 'no_solicitado':
                return 'La llamada aun no ha sido enviada a analisis.';
            case 'pendiente':
                return 'La llamada esta en cola para procesamiento.';
            case 'procesando':
                return 'La llamada se esta procesando en este momento.';
            case 'listo':
                return 'El analisis esta disponible.';
            case 'error':
                return 'Ocurrio un error durante el procesamiento.';
            case 'omitido':
                return 'La llamada fue omitida por regla de negocio.';
            default:
                return 'Estado no identificado.';
        }
    }
}