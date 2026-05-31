CREATE TABLE IF NOT EXISTS call_ai_protocol_rule (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_campaign INT(11) NOT NULL,
    call_type ENUM('inbound', 'outbound', 'both') NOT NULL DEFAULT 'both',
    rule_group VARCHAR(80) NOT NULL DEFAULT '',
    rule_name VARCHAR(120) NOT NULL,
    rule_description TEXT NULL,
    expected_terms TEXT NULL,
    forbidden_terms TEXT NULL,
    applies_when TEXT NULL,
    evaluation_mode ENUM('contains_any', 'contains_all', 'regex', 'semantic', 'manual_override') NOT NULL DEFAULT 'contains_any',
    weight DECIMAL(6,2) NOT NULL DEFAULT 1.00,
    required TINYINT(1) NOT NULL DEFAULT 1,
    active TINYINT(1) NOT NULL DEFAULT 1,
    script_version VARCHAR(40) NOT NULL DEFAULT 'segtec087-v1',
    created_by INT(11) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_call_ai_protocol_rule_name (id_campaign, call_type, rule_name, script_version),
    KEY idx_call_ai_protocol_rule_campaign (id_campaign, call_type, active),
    KEY idx_call_ai_protocol_rule_group (rule_group),
    CONSTRAINT fk_call_ai_protocol_rule_campaign FOREIGN KEY (id_campaign) REFERENCES campaign (id),
    CONSTRAINT fk_call_ai_protocol_rule_user FOREIGN KEY (created_by) REFERENCES user (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS call_ai_protocol_result (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_call_ai_analysis BIGINT UNSIGNED NOT NULL,
    call_id BIGINT UNSIGNED NOT NULL,
    call_type ENUM('inbound', 'outbound') NOT NULL,
    id_campaign INT(11) NOT NULL,
    id_rule BIGINT UNSIGNED NOT NULL,
    rule_group VARCHAR(80) NOT NULL DEFAULT '',
    rule_name VARCHAR(120) NOT NULL DEFAULT '',
    result_status ENUM('cumple', 'no_cumple', 'no_aplica', 'incierto') NOT NULL DEFAULT 'incierto',
    score DECIMAL(6,2) NULL,
    evidence_text TEXT NULL,
    position_start INT NULL,
    position_end INT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_call_ai_protocol_result_rule (id_call_ai_analysis, id_rule),
    KEY idx_call_ai_protocol_result_call (call_id, call_type),
    KEY idx_call_ai_protocol_result_campaign (id_campaign, result_status),
    KEY idx_call_ai_protocol_result_rule (id_rule),
    CONSTRAINT fk_call_ai_protocol_result_analysis FOREIGN KEY (id_call_ai_analysis) REFERENCES call_ai_analysis (id) ON DELETE CASCADE,
    CONSTRAINT fk_call_ai_protocol_result_campaign FOREIGN KEY (id_campaign) REFERENCES campaign (id),
    CONSTRAINT fk_call_ai_protocol_result_rule FOREIGN KEY (id_rule) REFERENCES call_ai_protocol_rule (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS call_ai_recommendation (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_call_ai_analysis BIGINT UNSIGNED NOT NULL,
    call_id BIGINT UNSIGNED NOT NULL,
    call_type ENUM('inbound', 'outbound') NOT NULL,
    recommendation_type VARCHAR(80) NOT NULL DEFAULT '',
    priority ENUM('alta', 'media', 'baja') NOT NULL DEFAULT 'media',
    message TEXT NOT NULL,
    source ENUM('rule-engine', 'sentiment', 'supervisor') NOT NULL DEFAULT 'rule-engine',
    status ENUM('nueva', 'revisada', 'aplicada', 'descartada') NOT NULL DEFAULT 'nueva',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INT(11) NULL,
    reviewed_at DATETIME NULL,
    PRIMARY KEY (id),
    KEY idx_call_ai_recommendation_analysis (id_call_ai_analysis, priority, status),
    KEY idx_call_ai_recommendation_call (call_id, call_type),
    CONSTRAINT fk_call_ai_recommendation_analysis FOREIGN KEY (id_call_ai_analysis) REFERENCES call_ai_analysis (id) ON DELETE CASCADE,
    CONSTRAINT fk_call_ai_recommendation_user FOREIGN KEY (reviewed_by) REFERENCES user (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS call_ai_score (
    id_call_ai_analysis BIGINT UNSIGNED NOT NULL,
    protocol_score DECIMAL(6,2) NULL,
    opening_score DECIMAL(6,2) NULL,
    validation_score DECIMAL(6,2) NULL,
    offer_score DECIMAL(6,2) NULL,
    closing_score DECIMAL(6,2) NULL,
    customer_experience_score DECIMAL(6,2) NULL,
    risk_level ENUM('alto', 'medio', 'bajo', 'sin_dato') NOT NULL DEFAULT 'sin_dato',
    compliance_summary TEXT NULL,
    critical_fail_count INT UNSIGNED NOT NULL DEFAULT 0,
    recommendation_count INT UNSIGNED NOT NULL DEFAULT 0,
    first_negative_moment_second INT UNSIGNED NULL,
    script_version VARCHAR(40) NOT NULL DEFAULT 'segtec087-v1',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_call_ai_analysis),
    KEY idx_call_ai_score_risk (risk_level),
    KEY idx_call_ai_score_protocol (protocol_score),
    CONSTRAINT fk_call_ai_score_analysis FOREIGN KEY (id_call_ai_analysis) REFERENCES call_ai_analysis (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO call_ai_protocol_rule (
    id_campaign,
    call_type,
    rule_group,
    rule_name,
    rule_description,
    expected_terms,
    forbidden_terms,
    applies_when,
    evaluation_mode,
    weight,
    required,
    active,
    script_version,
    created_by
)
SELECT 102, 'both', 'apertura', 'saludo_inicial',
    'Validar que la llamada abra con un saludo basico del agente.',
    'buenos dias|buenas tardes|buenas noches|hola',
    NULL,
    'primeros_20_segundos',
    'contains_any',
    1.50,
    1,
    1,
    'segtec087-v1',
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM call_ai_protocol_rule
    WHERE id_campaign = 102 AND call_type = 'both' AND rule_name = 'saludo_inicial' AND script_version = 'segtec087-v1'
);

INSERT INTO call_ai_protocol_rule (
    id_campaign, call_type, rule_group, rule_name, rule_description, expected_terms, forbidden_terms,
    applies_when, evaluation_mode, weight, required, active, script_version, created_by
)
SELECT 102, 'both', 'apertura', 'identificacion_empresa',
    'Validar que el agente se identifique con la empresa o el servicio.',
    'segtec|phonex|le habla|mi nombre es|habla',
    NULL,
    'primeros_30_segundos',
    'contains_any',
    1.50,
    1,
    1,
    'segtec087-v1',
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM call_ai_protocol_rule
    WHERE id_campaign = 102 AND call_type = 'both' AND rule_name = 'identificacion_empresa' AND script_version = 'segtec087-v1'
);

INSERT INTO call_ai_protocol_rule (
    id_campaign, call_type, rule_group, rule_name, rule_description, expected_terms, forbidden_terms,
    applies_when, evaluation_mode, weight, required, active, script_version, created_by
)
SELECT 102, 'inbound', 'validacion', 'confirmacion_motivo_contacto',
    'En inbound, validar que el agente pida o confirme el motivo del contacto.',
    'en que le puedo ayudar|motivo de su llamada|en que le apoyo|cual es su reporte|cual es su problema',
    NULL,
    'primeros_60_segundos',
    'contains_any',
    1.25,
    1,
    1,
    'segtec087-v1',
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM call_ai_protocol_rule
    WHERE id_campaign = 102 AND call_type = 'inbound' AND rule_name = 'confirmacion_motivo_contacto' AND script_version = 'segtec087-v1'
);

INSERT INTO call_ai_protocol_rule (
    id_campaign, call_type, rule_group, rule_name, rule_description, expected_terms, forbidden_terms,
    applies_when, evaluation_mode, weight, required, active, script_version, created_by
)
SELECT 102, 'outbound', 'apertura', 'explicacion_motivo_llamada',
    'En outbound, validar que el agente explique el motivo del contacto.',
    'le llamo|motivo de la llamada|me comunico|le contacto|le marco',
    NULL,
    'primeros_45_segundos',
    'contains_any',
    1.50,
    1,
    1,
    'segtec087-v1',
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM call_ai_protocol_rule
    WHERE id_campaign = 102 AND call_type = 'outbound' AND rule_name = 'explicacion_motivo_llamada' AND script_version = 'segtec087-v1'
);

INSERT INTO call_ai_protocol_rule (
    id_campaign, call_type, rule_group, rule_name, rule_description, expected_terms, forbidden_terms,
    applies_when, evaluation_mode, weight, required, active, script_version, created_by
)
SELECT 102, 'both', 'validacion', 'confirmacion_datos_cliente',
    'Validar que el agente confirme al menos un dato de cliente, equipo o serie antes de cerrar.',
    'numero de serie|folio|domicilio|equipo|nombre del cliente|titular|telefono de contacto',
    NULL,
    'durante_llamada',
    'contains_any',
    1.75,
    1,
    1,
    'segtec087-v1',
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM call_ai_protocol_rule
    WHERE id_campaign = 102 AND call_type = 'both' AND rule_name = 'confirmacion_datos_cliente' AND script_version = 'segtec087-v1'
);

INSERT INTO call_ai_protocol_rule (
    id_campaign, call_type, rule_group, rule_name, rule_description, expected_terms, forbidden_terms,
    applies_when, evaluation_mode, weight, required, active, script_version, created_by
)
SELECT 102, 'both', 'gestion', 'empatia_basica',
    'Detectar frases minimas de empatia o acompanamiento en la gestion.',
    'con gusto|permiteme|le apoyo|le ayudo|una disculpa|entiendo',
    NULL,
    'durante_llamada',
    'contains_any',
    1.00,
    0,
    1,
    'segtec087-v1',
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM call_ai_protocol_rule
    WHERE id_campaign = 102 AND call_type = 'both' AND rule_name = 'empatia_basica' AND script_version = 'segtec087-v1'
);

INSERT INTO call_ai_protocol_rule (
    id_campaign, call_type, rule_group, rule_name, rule_description, expected_terms, forbidden_terms,
    applies_when, evaluation_mode, weight, required, active, script_version, created_by
)
SELECT 102, 'both', 'cierre', 'confirmacion_siguiente_paso',
    'Validar que el agente deje claro el siguiente paso o resolucion.',
    'se genera reporte|queda registrado|le contactaran|siguiente paso|se canaliza|se agenda|se da seguimiento',
    NULL,
    'ultimo_30_por_ciento',
    'contains_any',
    1.50,
    1,
    1,
    'segtec087-v1',
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM call_ai_protocol_rule
    WHERE id_campaign = 102 AND call_type = 'both' AND rule_name = 'confirmacion_siguiente_paso' AND script_version = 'segtec087-v1'
);

INSERT INTO call_ai_protocol_rule (
    id_campaign, call_type, rule_group, rule_name, rule_description, expected_terms, forbidden_terms,
    applies_when, evaluation_mode, weight, required, active, script_version, created_by
)
SELECT 102, 'both', 'cierre', 'despedida_cordial',
    'Validar que la llamada cierre con agradecimiento o despedida.',
    'gracias|excelente dia|buen dia|buena tarde|hasta luego|estamos para servirle',
    NULL,
    'ultimo_20_por_ciento',
    'contains_any',
    1.25,
    1,
    1,
    'segtec087-v1',
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM call_ai_protocol_rule
    WHERE id_campaign = 102 AND call_type = 'both' AND rule_name = 'despedida_cordial' AND script_version = 'segtec087-v1'
);