#!/usr/bin/env python3
import argparse
import os
import re
import shutil
import subprocess
import tempfile
import time
import unicodedata
from collections import defaultdict
from pathlib import Path


NEGATIVE_TERMS = {
    "molesto", "mala", "malo", "problema", "queja", "cancelar", "cancelacion",
    "demora", "tarde", "pesimo", "inconforme", "enojado", "reclamo", "falla",
}
POSITIVE_TERMS = {
    "gracias", "perfecto", "excelente", "bien", "correcto", "apoyo", "listo",
    "solucion", "resuelto", "ayuda", "agradable", "confirmado",
}


def env(name, default=None):
    value = os.getenv(name, default)
    if value is None:
        raise RuntimeError(f"Falta variable de entorno requerida: {name}")
    return value


def env_int(name, default):
    value = os.getenv(name)
    if value is None or str(value).strip() == "":
        return default
    return int(value)


def env_float(name, default):
    value = os.getenv(name)
    if value is None or str(value).strip() == "":
        return default
    return float(value)


def env_bool(name, default=False):
    value = os.getenv(name)
    if value is None:
        return default
    return str(value).strip().lower() in {"1", "true", "yes", "on"}


def env_text(name, default=None):
    value = os.getenv(name)
    if value is None:
        return default
    value = str(value).strip()
    return value if value else default


def get_connection():
    import pymysql

    return pymysql.connect(
        host=env("AI_DB_HOST", os.getenv("ASS_DB_HOST")),
        user=env("AI_DB_USER"),
        password=env("AI_DB_PASS"),
        database=env("AI_DB_NAME"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False,
    )


def resolve_audio_path(grabacion):
    if not grabacion:
        return None
    if grabacion.startswith("vm"):
        parts = grabacion.split("_")
        if len(parts) < 3:
            return None
        _, fecha, audio = parts[0], parts[1], parts[2]
        year, month, day = fecha.split("-")
        return Path("/var/www/vm") / year / month / day / audio

    parts = grabacion.split(".")
    if len(parts) < 2:
        return None
    prefix_parts = parts[0].split("-")
    if len(prefix_parts) < 4:
        return None
    stamp = prefix_parts[3]
    year = stamp[0:4]
    month = stamp[4:6]
    day = stamp[6:8]
    filename = f"{parts[0]}.{parts[1]}.wav"
    return Path("/var/spool/asterisk/monitor") / year / month / day / filename


def mark_processing(connection, row_id):
    with connection.cursor() as cursor:
        cursor.execute(
            """
            UPDATE call_ai_analysis
            SET processing_status = 'procesando',
                processing_attempts = processing_attempts + 1,
                processing_error = NULL,
                processing_started_at = NOW(),
                updated_at = NOW()
            WHERE id = %s
            """,
            (row_id,),
        )
    connection.commit()


def mark_error(connection, row_id, message, duration_seconds=None, seconds_per_minute=None):
    with connection.cursor() as cursor:
        cursor.execute(
            """
            UPDATE call_ai_analysis
            SET processing_status = 'error',
                processing_error = %s,
                processing_duration_seconds = %s,
                transcription_seconds_per_minute = %s,
                updated_at = NOW()
            WHERE id = %s
            """,
            (message[:65535], duration_seconds, seconds_per_minute, row_id),
        )
    connection.commit()


def mark_ready(connection, row_id, text, confidence, sentiment_label, sentiment_score, summary, duration_seconds=None, seconds_per_minute=None):
    with connection.cursor() as cursor:
        cursor.execute(
            """
            UPDATE call_ai_analysis
            SET processing_status = 'listo',
                transcription_text = %s,
                transcription_confidence = %s,
                sentiment_label = %s,
                sentiment_score = %s,
                sentiment_summary = %s,
                processing_duration_seconds = %s,
                transcription_seconds_per_minute = %s,
                processed_at = NOW(),
                processing_error = NULL,
                updated_at = NOW()
            WHERE id = %s
            """,
            (text, confidence, sentiment_label, sentiment_score, summary, duration_seconds, seconds_per_minute, row_id),
        )
    connection.commit()


def build_timing_metrics(row, started_at):
    duration_seconds = max(int(round(time.monotonic() - started_at)), 0)
    seconds_per_minute = None
    audio_seconds = int(row.get("duracion_segundos") or 0)
    if audio_seconds > 0:
        seconds_per_minute = round((duration_seconds * 60) / audio_seconds, 2)
    return duration_seconds, seconds_per_minute


def fetch_pending(connection, limit):
    with connection.cursor() as cursor:
        cursor.execute(
            """
            SELECT *
            FROM call_ai_analysis
                        WHERE processing_status IN ('pendiente', 'error')
              AND processing_attempts < 3
            ORDER BY created_at ASC
            LIMIT %s
            """,
            (limit,),
        )
        return cursor.fetchall()


def resolve_effective_model(row, requested_model):
    row_model_name = str(row.get("model_name") or "").strip().lower()
    if row_model_name:
        return row_model_name

    model_name = (requested_model or "tiny").strip().lower()
    if model_name != "tiny":
        return model_name

    audio_seconds = int(row.get("duracion_segundos") or 0)
    long_audio_seconds = int(os.getenv("FW_LONG_AUDIO_SECONDS", "90") or 90)
    long_audio_model = os.getenv("FW_LONG_AUDIO_MODEL", "small").strip().lower()

    if audio_seconds >= long_audio_seconds and long_audio_model:
        return long_audio_model

    return model_name


def split_transcription_profile(profile_name):
    raw_profile = (profile_name or "tiny").strip().lower()
    preprocess_audio = False
    if raw_profile.endswith("-clean"):
        preprocess_audio = True
        raw_profile = raw_profile[:-6]

    model_name = raw_profile or "tiny"
    return model_name, preprocess_audio


def build_preprocessed_audio(audio_path):
    ffmpeg_path = shutil.which("ffmpeg")
    if not ffmpeg_path:
        raise RuntimeError("No se encontro ffmpeg para limpiar el audio antes de transcribir.")

    filters = os.getenv(
        "FW_AUDIO_FILTERS",
        "highpass=f=120,lowpass=f=3800,afftdn=nf=-20,volume=1.8",
    )
    temp_file = tempfile.NamedTemporaryFile(prefix="fw_clean_", suffix=".wav", delete=False)
    temp_file.close()
    output_path = Path(temp_file.name)

    command = [
        ffmpeg_path,
        "-y",
        "-i",
        str(audio_path),
        "-ac",
        "1",
        "-ar",
        "16000",
        "-af",
        filters,
        "-c:a",
        "pcm_s16le",
        str(output_path),
    ]
    result = subprocess.run(command, capture_output=True, text=True)
    if result.returncode != 0:
        if output_path.exists():
            output_path.unlink(missing_ok=True)
        raise RuntimeError(f"Fallo la limpieza de audio con ffmpeg: {result.stderr.strip()[:800]}")

    return output_path


def transcribe_audio(audio_path, profile_name):
    from faster_whisper import WhisperModel

    model_name, preprocess_audio = split_transcription_profile(profile_name)
    device = os.getenv("FW_DEVICE", "cpu")
    compute_type = os.getenv("FW_COMPUTE_TYPE", "int8")
    vad_filter = env_bool("FW_VAD_FILTER", False)
    beam_size = env_int("FW_BEAM_SIZE", 5)
    best_of = env_int("FW_BEST_OF", 5)
    temperature = env_float("FW_TEMPERATURE", 0.0)
    patience = env_float("FW_PATIENCE", 1.0)
    condition_on_previous_text = env_bool("FW_CONDITION_ON_PREVIOUS_TEXT", True)
    initial_prompt = env_text("FW_INITIAL_PROMPT")
    temp_audio_path = None
    source_audio_path = audio_path

    if preprocess_audio:
        temp_audio_path = build_preprocessed_audio(audio_path)
        source_audio_path = temp_audio_path

    model = WhisperModel(model_name, device=device, compute_type=compute_type)
    try:
        segments, info = model.transcribe(
            str(source_audio_path),
            language="es",
            vad_filter=vad_filter,
            beam_size=beam_size,
            best_of=best_of,
            temperature=temperature,
            patience=patience,
            condition_on_previous_text=condition_on_previous_text,
            initial_prompt=initial_prompt,
        )
        texts = []
        for segment in segments:
            texts.append(segment.text.strip())
        text = " ".join(filter(None, texts)).strip()
        confidence = getattr(info, "language_probability", None)
        return text, confidence
    finally:
        if temp_audio_path is not None and temp_audio_path.exists():
            temp_audio_path.unlink(missing_ok=True)


def analyze_sentiment(text):
    words = {token.strip(".,:;!?()[]{}\"'").lower() for token in text.split() if token.strip()}
    positive_hits = len(words & POSITIVE_TERMS)
    negative_hits = len(words & NEGATIVE_TERMS)
    if positive_hits == 0 and negative_hits == 0:
        return "neutro", 0.0, "Sin indicadores fuertes de sentimiento en el texto transcrito."
    score = (positive_hits - negative_hits) / max(positive_hits + negative_hits, 1)
    if score > 0.2:
        label = "positivo"
    elif score < -0.2:
        label = "negativo"
    else:
        label = "mixto"
    summary = f"Sentimiento {label} calculado con heuristica local sobre {positive_hits + negative_hits} indicadores."
    return label, round(score, 4), summary


def normalize_text(text):
    if not text:
        return ""
    normalized = unicodedata.normalize("NFKD", text)
    normalized = normalized.encode("ascii", "ignore").decode("ascii")
    normalized = normalized.lower()
    normalized = re.sub(r"\s+", " ", normalized).strip()
    return normalized


def split_terms(raw_terms):
    if not raw_terms:
        return []
    return [term for term in (normalize_text(item) for item in raw_terms.split("|")) if term]


def fetch_protocol_rules(connection, campaign_id, call_type):
    with connection.cursor() as cursor:
        cursor.execute(
            """
            SELECT *
            FROM call_ai_protocol_rule
            WHERE id_campaign = %s
              AND active = 1
              AND call_type IN ('both', %s)
            ORDER BY rule_group, id
            """,
            (campaign_id, call_type),
        )
        return cursor.fetchall()


def evaluate_rule(text, rule):
    normalized_text = normalize_text(text)
    expected_terms = split_terms(rule.get("expected_terms"))
    forbidden_terms = split_terms(rule.get("forbidden_terms"))

    if not expected_terms and not forbidden_terms:
        return "incierto", None, None, "La regla no tiene terminos configurados."

    forbidden_hits = [term for term in forbidden_terms if term in normalized_text]
    if forbidden_hits:
        return "no_cumple", 0.0, ", ".join(forbidden_hits[:5]), "Se detectaron terminos prohibidos."

    matched_terms = [term for term in expected_terms if term in normalized_text]
    mode = (rule.get("evaluation_mode") or "contains_any").lower()
    if mode == "contains_all":
        matched = bool(expected_terms) and len(matched_terms) == len(expected_terms)
    else:
        matched = len(matched_terms) > 0

    if matched:
        return "cumple", float(rule.get("weight") or 0), ", ".join(matched_terms[:5]), None

    if expected_terms:
        return "no_cumple", 0.0, None, "No se detectaron terminos esperados."

    return "incierto", None, None, "No hubo evidencia suficiente para evaluar la regla."


def reset_protocol_outputs(connection, analysis_id):
    with connection.cursor() as cursor:
        cursor.execute("DELETE FROM call_ai_protocol_result WHERE id_call_ai_analysis = %s", (analysis_id,))
        cursor.execute("DELETE FROM call_ai_recommendation WHERE id_call_ai_analysis = %s", (analysis_id,))
        cursor.execute("DELETE FROM call_ai_score WHERE id_call_ai_analysis = %s", (analysis_id,))
    connection.commit()


def build_recommendations(row, evaluations, sentiment_label):
    recommendations = []
    for evaluation in evaluations:
        if evaluation["result_status"] != "no_cumple":
            continue
        priority = "alta" if evaluation["required"] else "media"
        recommendations.append(
            {
                "recommendation_type": evaluation["rule_group"],
                "priority": priority,
                "message": f"Reforzar {evaluation['rule_name'].replace('_', ' ')}: {evaluation['rule_description']}",
                "source": "rule-engine",
                "status": "nueva",
            }
        )

    if sentiment_label == "negativo":
        recommendations.append(
            {
                "recommendation_type": "empatia",
                "priority": "alta",
                "message": "La llamada mostro tono negativo; revisar apertura, empatia y confirmacion de siguiente paso.",
                "source": "sentiment",
                "status": "nueva",
            }
        )
    elif sentiment_label == "mixto":
        recommendations.append(
            {
                "recommendation_type": "seguimiento",
                "priority": "media",
                "message": "La llamada tuvo señales mixtas; conviene revisar el manejo de objeciones y el cierre.",
                "source": "sentiment",
                "status": "nueva",
            }
        )

    return recommendations


def consolidate_protocol_score(evaluations):
    totals = defaultdict(lambda: {"weight": 0.0, "score": 0.0})
    total_weight = 0.0
    total_score = 0.0
    critical_fail_count = 0

    for evaluation in evaluations:
        if evaluation["result_status"] not in {"cumple", "no_cumple"}:
            continue
        weight = float(evaluation["weight"] or 0)
        total_weight += weight
        group = evaluation["rule_group"] or "general"
        totals[group]["weight"] += weight
        if evaluation["result_status"] == "cumple":
            total_score += weight
            totals[group]["score"] += weight
        elif evaluation["required"]:
            critical_fail_count += 1

    protocol_score = round((total_score / total_weight) * 100, 2) if total_weight > 0 else None
    opening_score = round((totals["apertura"]["score"] / totals["apertura"]["weight"]) * 100, 2) if totals["apertura"]["weight"] > 0 else None
    validation_score = round((totals["validacion"]["score"] / totals["validacion"]["weight"]) * 100, 2) if totals["validacion"]["weight"] > 0 else None
    offer_score = round((totals["gestion"]["score"] / totals["gestion"]["weight"]) * 100, 2) if totals["gestion"]["weight"] > 0 else None
    closing_score = round((totals["cierre"]["score"] / totals["cierre"]["weight"]) * 100, 2) if totals["cierre"]["weight"] > 0 else None

    return {
        "protocol_score": protocol_score,
        "opening_score": opening_score,
        "validation_score": validation_score,
        "offer_score": offer_score,
        "closing_score": closing_score,
        "critical_fail_count": critical_fail_count,
    }


def determine_risk_level(protocol_score, critical_fail_count, sentiment_label):
    if critical_fail_count >= 2 or (sentiment_label == "negativo" and (protocol_score is None or protocol_score < 75)):
        return "alto"
    if critical_fail_count == 1 or sentiment_label in {"negativo", "mixto"} or (protocol_score is not None and protocol_score < 90):
        return "medio"
    if protocol_score is not None:
        return "bajo"
    return "sin_dato"


def save_protocol_outputs(connection, row, evaluations, score_payload, recommendations):
    with connection.cursor() as cursor:
        for evaluation in evaluations:
            cursor.execute(
                """
                INSERT INTO call_ai_protocol_result (
                    id_call_ai_analysis, call_id, call_type, id_campaign, id_rule,
                    rule_group, rule_name, result_status, score, evidence_text, notes
                ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                """,
                (
                    row["id"],
                    row["call_id"],
                    row["call_type"],
                    row["id_campaign"],
                    evaluation["id_rule"],
                    evaluation["rule_group"],
                    evaluation["rule_name"],
                    evaluation["result_status"],
                    evaluation["score"],
                    evaluation["evidence_text"],
                    evaluation["notes"],
                ),
            )

        for recommendation in recommendations:
            cursor.execute(
                """
                INSERT INTO call_ai_recommendation (
                    id_call_ai_analysis, call_id, call_type, recommendation_type,
                    priority, message, source, status
                ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
                """,
                (
                    row["id"],
                    row["call_id"],
                    row["call_type"],
                    recommendation["recommendation_type"],
                    recommendation["priority"],
                    recommendation["message"],
                    recommendation["source"],
                    recommendation["status"],
                ),
            )

        cursor.execute(
            """
            INSERT INTO call_ai_score (
                id_call_ai_analysis, protocol_score, opening_score, validation_score,
                offer_score, closing_score, customer_experience_score, risk_level,
                compliance_summary, critical_fail_count, recommendation_count,
                first_negative_moment_second, script_version
            ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE
                protocol_score = VALUES(protocol_score),
                opening_score = VALUES(opening_score),
                validation_score = VALUES(validation_score),
                offer_score = VALUES(offer_score),
                closing_score = VALUES(closing_score),
                customer_experience_score = VALUES(customer_experience_score),
                risk_level = VALUES(risk_level),
                compliance_summary = VALUES(compliance_summary),
                critical_fail_count = VALUES(critical_fail_count),
                recommendation_count = VALUES(recommendation_count),
                first_negative_moment_second = VALUES(first_negative_moment_second),
                script_version = VALUES(script_version),
                updated_at = NOW()
            """,
            (
                row["id"],
                score_payload["protocol_score"],
                score_payload["opening_score"],
                score_payload["validation_score"],
                score_payload["offer_score"],
                score_payload["closing_score"],
                score_payload["customer_experience_score"],
                score_payload["risk_level"],
                score_payload["compliance_summary"],
                score_payload["critical_fail_count"],
                len(recommendations),
                None,
                score_payload["script_version"],
            ),
        )
    connection.commit()


def run_protocol_pipeline(connection, row, text, sentiment_label):
    rules = fetch_protocol_rules(connection, row["id_campaign"], row["call_type"])
    reset_protocol_outputs(connection, row["id"])
    if not rules:
        return

    evaluations = []
    script_version = rules[0].get("script_version") or "segtec087-v1"
    for rule in rules:
        result_status, score, evidence_text, notes = evaluate_rule(text, rule)
        evaluations.append(
            {
                "id_rule": rule["id"],
                "rule_group": rule["rule_group"],
                "rule_name": rule["rule_name"],
                "rule_description": rule.get("rule_description") or rule["rule_name"],
                "result_status": result_status,
                "score": score,
                "evidence_text": evidence_text,
                "notes": notes,
                "required": int(rule.get("required") or 0) == 1,
                "weight": float(rule.get("weight") or 0),
            }
        )

    score_payload = consolidate_protocol_score(evaluations)
    score_payload["risk_level"] = determine_risk_level(
        score_payload["protocol_score"],
        score_payload["critical_fail_count"],
        sentiment_label,
    )
    score_payload["customer_experience_score"] = score_payload["protocol_score"]
    score_payload["script_version"] = script_version
    score_payload["compliance_summary"] = (
        f"Cumplimiento {score_payload['protocol_score']} con "
        f"{score_payload['critical_fail_count']} falla(s) critica(s)."
        if score_payload["protocol_score"] is not None
        else "Sin score de cumplimiento disponible."
    )
    recommendations = build_recommendations(row, evaluations, sentiment_label)
    save_protocol_outputs(connection, row, evaluations, score_payload, recommendations)


def process_row(connection, row, model_name):
    mark_processing(connection, row["id"])
    started_at = time.monotonic()
    audio_path = resolve_audio_path(row["grabacion"])
    effective_model = resolve_effective_model(row, model_name)
    if audio_path is None or not audio_path.exists():
        duration_seconds, seconds_per_minute = build_timing_metrics(row, started_at)
        mark_error(connection, row["id"], "No fue posible localizar el archivo de audio fuente.", duration_seconds, seconds_per_minute)
        return
    try:
        text, confidence = transcribe_audio(audio_path, effective_model)
        if not text:
            duration_seconds, seconds_per_minute = build_timing_metrics(row, started_at)
            mark_error(connection, row["id"], "La transcripcion regreso sin contenido.", duration_seconds, seconds_per_minute)
            return
        sentiment_label, sentiment_score, sentiment_summary = analyze_sentiment(text)
        duration_seconds, seconds_per_minute = build_timing_metrics(row, started_at)
        mark_ready(connection, row["id"], text, confidence, sentiment_label, sentiment_score, sentiment_summary, duration_seconds, seconds_per_minute)
        run_protocol_pipeline(connection, row, text, sentiment_label)
    except Exception as exc:
        duration_seconds, seconds_per_minute = build_timing_metrics(row, started_at)
        mark_error(connection, row["id"], str(exc), duration_seconds, seconds_per_minute)


def process_pending(limit, model_name):
    connection = get_connection()
    try:
        rows = fetch_pending(connection, limit)
        for row in rows:
            process_row(connection, row, model_name)
    finally:
        connection.close()


def main():
    parser = argparse.ArgumentParser(description="Worker asincrono para transcripcion y sentimiento de llamadas.")
    parser.add_argument("--limit", type=int, default=5, help="Numero maximo de llamadas por corrida.")
    parser.add_argument("--model", default=os.getenv("FW_MODEL", "tiny"), help="Modelo Faster-Whisper base a utilizar; llamadas largas pueden escalar automaticamente segun entorno.")
    args = parser.parse_args()
    process_pending(args.limit, args.model)


if __name__ == "__main__":
    main()