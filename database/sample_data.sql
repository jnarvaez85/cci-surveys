-- Script de datos de muestra para pruebas
-- Ejecutar después del schema.sql

USE surveys;

-- Insertar usuarios de prueba
INSERT INTO users (username, email, password_hash, first_name, last_name, document_type, document_number, role) VALUES
('encuestador1', 'encuestador1@test.com', '$argon2id$v=19$m=65536,t=4,p=3$VGVzdFBhc3N3b3JkMTIz$TestHash', 'María', 'González', 'CC', '12345678', 'surveyor'),
('encuestador2', 'encuestador2@test.com', '$argon2id$v=19$m=65536,t=4,p=3$VGVzdFBhc3N3b3JkMTIz$TestHash', 'Carlos', 'Rodríguez', 'CC', '87654321', 'surveyor'),
('admin2', 'admin2@test.com', '$argon2id$v=19$m=65536,t=4,p=3$VGVzdFBhc3N3b3JkMTIz$TestHash', 'Ana', 'Martínez', 'CC', '11223344', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Obtener IDs para usar en las respuestas
SET @survey_id = 1; -- ID de la encuesta creada
SET @surveyor1_id = 2; -- ID del primer encuestador
SET @surveyor2_id = 3; -- ID del segundo encuestador

-- Insertar respuestas de muestra para diferentes poblaciones
-- Respuestas para Olaya (population_id = 1)
INSERT INTO survey_responses (
    survey_id, population_id, surveyor_id,
    respondent_first_name, respondent_last_name, respondent_phone, respondent_age_group, 
    respondent_address, respondent_gender, respondent_document_type, respondent_document_number,
    surveyor_first_name, surveyor_last_name, surveyor_document_number,
    ip_address, user_agent
) VALUES
(@survey_id, 1, @surveyor1_id, 'Juan', 'Pérez', '3001234567', 'adulto', 'Calle 10 #5-20, Olaya', 'Hombre', 'CC', '1001234567', 'María', 'González', '12345678', '192.168.1.100', 'Mozilla/5.0...'),
(@survey_id, 1, @surveyor1_id, 'Carmen', 'López', '3002345678', 'adulto mayor', 'Carrera 15 #8-30, Olaya', 'Mujer', 'CC', '1002345678', 'María', 'González', '12345678', '192.168.1.101', 'Mozilla/5.0...'),
(@survey_id, 1, @surveyor2_id, 'Roberto', 'Sánchez', '3003456789', 'joven', 'Calle 20 #12-45, Olaya', 'Hombre', 'TI', '1003456789', 'Carlos', 'Rodríguez', '87654321', '192.168.1.102', 'Mozilla/5.0...');

-- Respuestas para Villa del Sol (population_id = 2)
INSERT INTO survey_responses (
    survey_id, population_id, surveyor_id,
    respondent_first_name, respondent_last_name, respondent_phone, respondent_age_group, 
    respondent_address, respondent_gender, respondent_document_type, respondent_document_number,
    surveyor_first_name, surveyor_last_name, surveyor_document_number,
    ip_address, user_agent
) VALUES
(@survey_id, 2, @surveyor1_id, 'Isabel', 'Martín', '3004567890', 'adulto', 'Calle 30 #15-60, Villa del Sol', 'Mujer', 'CC', '1004567890', 'María', 'González', '12345678', '192.168.1.103', 'Mozilla/5.0...'),
(@survey_id, 2, @surveyor2_id, 'Miguel', 'Fernández', '3005678901', 'adulto', 'Carrera 25 #18-75, Villa del Sol', 'Hombre', 'CC', '1005678901', 'Carlos', 'Rodríguez', '87654321', '192.168.1.104', 'Mozilla/5.0...'),
(@survey_id, 2, @surveyor1_id, 'Lucía', 'García', '3006789012', 'joven', 'Calle 35 #22-90, Villa del Sol', 'Mujer', 'CC', '1006789012', 'María', 'González', '12345678', '192.168.1.105', 'Mozilla/5.0...');

-- Respuestas para Parque Heredia (population_id = 3)
INSERT INTO survey_responses (
    survey_id, population_id, surveyor_id,
    respondent_first_name, respondent_last_name, respondent_phone, respondent_age_group, 
    respondent_address, respondent_gender, respondent_document_type, respondent_document_number,
    surveyor_first_name, surveyor_last_name, surveyor_document_number,
    ip_address, user_agent
) VALUES
(@survey_id, 3, @surveyor2_id, 'Antonio', 'Jiménez', '3007890123', 'adulto mayor', 'Calle 40 #25-105, Parque Heredia', 'Hombre', 'CC', '1007890123', 'Carlos', 'Rodríguez', '87654321', '192.168.1.106', 'Mozilla/5.0...'),
(@survey_id, 3, @surveyor1_id, 'Rosa', 'Vargas', '3008901234', 'adulto', 'Carrera 35 #28-120, Parque Heredia', 'Mujer', 'CC', '1008901234', 'María', 'González', '12345678', '192.168.1.107', 'Mozilla/5.0...');

-- Respuestas para Ternera (population_id = 4)
INSERT INTO survey_responses (
    survey_id, population_id, surveyor_id,
    respondent_first_name, respondent_last_name, respondent_phone, respondent_age_group, 
    respondent_address, respondent_gender, respondent_document_type, respondent_document_number,
    surveyor_first_name, surveyor_last_name, surveyor_document_number,
    ip_address, user_agent
) VALUES
(@survey_id, 4, @surveyor2_id, 'Pedro', 'Morales', '3009012345', 'adulto', 'Calle 50 #32-135, Ternera', 'Hombre', 'CC', '1009012345', 'Carlos', 'Rodríguez', '87654321', '192.168.1.108', 'Mozilla/5.0...'),
(@survey_id, 4, @surveyor1_id, 'Elena', 'Reyes', '3010123456', 'joven', 'Carrera 45 #35-150, Ternera', 'Mujer', 'TI', '1010123456', 'María', 'González', '12345678', '192.168.1.109', 'Mozilla/5.0...'),
(@survey_id, 4, @surveyor2_id, 'Francisco', 'Torres', '3011234567', 'adulto mayor', 'Calle 55 #38-165, Ternera', 'Hombre', 'CC', '1011234567', 'Carlos', 'Rodríguez', '87654321', '192.168.1.110', 'Mozilla/5.0...');

-- Respuestas para San Fernando (population_id = 5)
INSERT INTO survey_responses (
    survey_id, population_id, surveyor_id,
    respondent_first_name, respondent_last_name, respondent_phone, respondent_age_group, 
    respondent_address, respondent_gender, respondent_document_type, respondent_document_number,
    surveyor_first_name, surveyor_last_name, surveyor_document_number,
    ip_address, user_agent
) VALUES
(@survey_id, 5, @surveyor1_id, 'Patricia', 'Herrera', '3012345678', 'adulto', 'Calle 60 #42-180, San Fernando', 'Mujer', 'CC', '1012345678', 'María', 'González', '12345678', '192.168.1.111', 'Mozilla/5.0...'),
(@survey_id, 5, @surveyor2_id, 'Jorge', 'Mendoza', '3013456789', 'adulto', 'Carrera 55 #45-195, San Fernando', 'Hombre', 'CC', '1013456789', 'Carlos', 'Rodríguez', '87654321', '192.168.1.112', 'Mozilla/5.0...');

-- Insertar respuestas específicas a las preguntas
-- Obtener IDs de las preguntas
SET @q1_id = (SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey_id);
SET @q2_id = (SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey_id);
SET @q3_id = (SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey_id);
SET @q4_id = (SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey_id);
SET @q5_id = (SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey_id);
SET @q6_id = (SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey_id);
SET @q7_id = (SELECT id FROM survey_questions WHERE question_order = 7 AND survey_id = @survey_id);

-- Insertar respuestas para cada encuesta (usando los IDs de respuesta generados)
-- Respuesta 1 (Juan Pérez - Olaya)
INSERT INTO response_answers (response_id, question_id, option_id) VALUES
(1, @q1_id, (SELECT id FROM question_options WHERE question_id = @q1_id AND option_text = 'Seguro')),
(1, @q2_id, (SELECT id FROM question_options WHERE question_id = @q2_id AND option_text = 'Robo')),
(1, @q3_id, (SELECT id FROM question_options WHERE question_id = @q3_id AND option_text = 'Regular')),
(1, @q4_id, (SELECT id FROM question_options WHERE question_id = @q4_id AND option_text = 'Falta de empleo/oportunidades')),
(1, @q5_id, (SELECT id FROM question_options WHERE question_id = @q5_id AND option_text = 'A veces')),
(1, @q6_id, (SELECT id FROM question_options WHERE question_id = @q6_id AND option_text = 'Varias veces por semana')),
(1, @q7_id, (SELECT id FROM question_options WHERE question_id = @q7_id AND option_text = 'Sí'));

-- Respuesta 2 (Carmen López - Olaya)
INSERT INTO response_answers (response_id, question_id, option_id) VALUES
(2, @q1_id, (SELECT id FROM question_options WHERE question_id = @q1_id AND option_text = 'Muy inseguro')),
(2, @q2_id, (SELECT id FROM question_options WHERE question_id = @q2_id AND option_text = 'Homicidios')),
(2, @q3_id, (SELECT id FROM question_options WHERE question_id = @q3_id AND option_text = 'Muy deficiente')),
(2, @q4_id, (SELECT id FROM question_options WHERE question_id = @q4_id AND option_text = 'Pobreza')),
(2, @q5_id, (SELECT id FROM question_options WHERE question_id = @q5_id AND option_text = 'Siempre')),
(2, @q6_id, (SELECT id FROM question_options WHERE question_id = @q6_id AND option_text = 'A diario')),
(2, @q7_id, (SELECT id FROM question_options WHERE question_id = @q7_id AND option_text = 'Sí'));

-- Respuesta 3 (Roberto Sánchez - Olaya)
INSERT INTO response_answers (response_id, question_id, option_id) VALUES
(3, @q1_id, (SELECT id FROM question_options WHERE question_id = @q1_id AND option_text = 'Inseguro')),
(3, @q2_id, (SELECT id FROM question_options WHERE question_id = @q2_id AND option_text = 'Microtráfico')),
(3, @q3_id, (SELECT id FROM question_options WHERE question_id = @q3_id AND option_text = 'Mala')),
(3, @q4_id, (SELECT id FROM question_options WHERE question_id = @q4_id AND option_text = 'Falta de educación')),
(3, @q5_id, (SELECT id FROM question_options WHERE question_id = @q5_id AND option_text = 'Nunca')),
(3, @q6_id, (SELECT id FROM question_options WHERE question_id = @q6_id AND option_text = 'Nunca')),
(3, @q7_id, (SELECT id FROM question_options WHERE question_id = @q7_id AND option_text = 'No'));

-- Continuar con más respuestas para tener datos variados...
-- Respuesta 4 (Isabel Martín - Villa del Sol)
INSERT INTO response_answers (response_id, question_id, option_id) VALUES
(4, @q1_id, (SELECT id FROM question_options WHERE question_id = @q1_id AND option_text = 'Muy seguro')),
(4, @q2_id, (SELECT id FROM question_options WHERE question_id = @q2_id AND option_text = 'Robo')),
(4, @q3_id, (SELECT id FROM question_options WHERE question_id = @q3_id AND option_text = 'Buena')),
(4, @q4_id, (SELECT id FROM question_options WHERE question_id = @q4_id AND option_text = 'Ansiedad')),
(4, @q5_id, (SELECT id FROM question_options WHERE question_id = @q5_id AND option_text = 'Siempre')),
(4, @q6_id, (SELECT id FROM question_options WHERE question_id = @q6_id AND option_text = 'A diario')),
(4, @q7_id, (SELECT id FROM question_options WHERE question_id = @q7_id AND option_text = 'Sí'));

-- Respuesta 5 (Miguel Fernández - Villa del Sol)
INSERT INTO response_answers (response_id, question_id, option_id) VALUES
(5, @q1_id, (SELECT id FROM question_options WHERE question_id = @q1_id AND option_text = 'Seguro')),
(5, @q2_id, (SELECT id FROM question_options WHERE question_id = @q2_id AND option_text = 'Pandillas')),
(5, @q3_id, (SELECT id FROM question_options WHERE question_id = @q3_id AND option_text = 'Regular')),
(5, @q4_id, (SELECT id FROM question_options WHERE question_id = @q4_id AND option_text = 'Pánico')),
(5, @q5_id, (SELECT id FROM question_options WHERE question_id = @q5_id AND option_text = 'A veces')),
(5, @q6_id, (SELECT id FROM question_options WHERE question_id = @q6_id AND option_text = 'Solo en momentos de dificultad')),
(5, @q7_id, (SELECT id FROM question_options WHERE question_id = @q7_id AND option_text = 'No'));

-- Continuar con el resto de respuestas para tener una muestra completa...
-- (Se pueden agregar más respuestas siguiendo el mismo patrón)

-- Insertar algunos logs del sistema
INSERT INTO system_logs (user_id, action, table_name, record_id, new_values, ip_address) VALUES
(1, 'CREATE', 'surveys', 1, '{"name":"SEGURIDAD DE LA POBLACIÓN"}', '127.0.0.1'),
(2, 'CREATE', 'survey_responses', 1, '{"respondent_first_name":"Juan"}', '192.168.1.100'),
(3, 'CREATE', 'survey_responses', 2, '{"respondent_first_name":"Carmen"}', '192.168.1.101');

COMMIT;
