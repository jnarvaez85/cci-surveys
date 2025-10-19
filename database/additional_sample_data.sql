-- Script adicional de datos de muestra para pruebas más completas
-- Ejecutar después del schema.sql y sample_data.sql

USE surveys;

-- Insertar más usuarios de prueba
INSERT INTO users (username, email, password_hash, first_name, last_name, document_type, document_number, role) VALUES
('encuestador3', 'encuestador3@test.com', '$argon2id$v=19$m=65536,t=4,p=3$VGVzdFBhc3N3b3JkMTIz$TestHash', 'Laura', 'Hernández', 'CC', '99887766', 'surveyor'),
('encuestador4', 'encuestador4@test.com', '$argon2id$v=19$m=65536,t=4,p=3$VGVzdFBhc3N3b3JkMTIz$TestHash', 'Diego', 'Silva', 'CC', '55443322', 'surveyor'),
('encuestador5', 'encuestador5@test.com', '$argon2id$v=19$m=65536,t=4,p=3$VGVzdFBhc3N3b3JkMTIz$TestHash', 'Sofía', 'Ramírez', 'CC', '11223344', 'surveyor'),
('admin3', 'admin3@test.com', '$argon2id$v=19$m=65536,t=4,p=3$VGVzdFBhc3N3b3JkMTIz$TestHash', 'Carlos', 'Administrador', 'CC', '55667788', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Insertar más poblaciones
INSERT INTO populations (name, description) VALUES
('Bocagrande', 'Bocagrande - Zona turística y hotelera'),
('Getsemaní', 'Getsemaní - Barrio histórico y cultural'),
('Manga', 'Manga - Zona residencial y comercial'),
('Castillo Grande', 'Castillo Grande - Sector residencial de clase media'),
('Pie de la Popa', 'Pie de la Popa - Zona residencial popular'),
('La Boquilla', 'La Boquilla - Zona costera y turística'),
('El Amparo', 'El Amparo - Sector residencial'),
('Los Caracoles', 'Los Caracoles - Barrio popular')
ON DUPLICATE KEY UPDATE name=name;

-- Crear segunda encuesta de ejemplo
INSERT INTO surveys (name, description, created_by) VALUES
('SATISFACCIÓN CON SERVICIOS PÚBLICOS', 'Encuesta sobre satisfacción con servicios públicos municipales', 1)
ON DUPLICATE KEY UPDATE name=name;

-- Obtener el ID de la segunda encuesta
SET @survey2_id = (SELECT id FROM surveys WHERE name = 'SATISFACCIÓN CON SERVICIOS PÚBLICOS' LIMIT 1);

-- Crear preguntas para la segunda encuesta
INSERT INTO survey_questions (survey_id, question_text, question_order) VALUES
(@survey2_id, '¿Cómo califica el servicio de acueducto en su barrio?', 1),
(@survey2_id, '¿Cómo califica el servicio de alcantarillado?', 2),
(@survey2_id, '¿Cómo califica el servicio de recolección de basuras?', 3),
(@survey2_id, '¿Cómo califica el mantenimiento de las vías públicas?', 4),
(@survey2_id, '¿Cómo califica la iluminación pública?', 5),
(@survey2_id, '¿Cómo califica los servicios de salud pública?', 6),
(@survey2_id, '¿Cómo califica la educación pública?', 7),
(@survey2_id, '¿Considera que los impuestos municipales son justos?', 8)
ON DUPLICATE KEY UPDATE question_text=question_text;

-- Crear opciones de respuesta para la segunda encuesta
INSERT INTO question_options (question_id, option_text, option_order) VALUES
-- Pregunta 1 - Acueducto
((SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey2_id), 'Excelente', 1),
((SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey2_id), 'Bueno', 2),
((SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey2_id), 'Regular', 3),
((SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey2_id), 'Malo', 4),
((SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey2_id), 'Muy malo', 5),

-- Pregunta 2 - Alcantarillado
((SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey2_id), 'Excelente', 1),
((SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey2_id), 'Bueno', 2),
((SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey2_id), 'Regular', 3),
((SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey2_id), 'Malo', 4),
((SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey2_id), 'Muy malo', 5),

-- Pregunta 3 - Recolección de basuras
((SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey2_id), 'Excelente', 1),
((SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey2_id), 'Bueno', 2),
((SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey2_id), 'Regular', 3),
((SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey2_id), 'Malo', 4),
((SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey2_id), 'Muy malo', 5),

-- Pregunta 4 - Mantenimiento de vías
((SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey2_id), 'Excelente', 1),
((SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey2_id), 'Bueno', 2),
((SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey2_id), 'Regular', 3),
((SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey2_id), 'Malo', 4),
((SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey2_id), 'Muy malo', 5),

-- Pregunta 5 - Iluminación pública
((SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey2_id), 'Excelente', 1),
((SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey2_id), 'Bueno', 2),
((SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey2_id), 'Regular', 3),
((SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey2_id), 'Malo', 4),
((SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey2_id), 'Muy malo', 5),

-- Pregunta 6 - Servicios de salud
((SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey2_id), 'Excelente', 1),
((SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey2_id), 'Bueno', 2),
((SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey2_id), 'Regular', 3),
((SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey2_id), 'Malo', 4),
((SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey2_id), 'Muy malo', 5),

-- Pregunta 7 - Educación pública
((SELECT id FROM survey_questions WHERE question_order = 7 AND survey_id = @survey2_id), 'Excelente', 1),
((SELECT id FROM survey_questions WHERE question_order = 7 AND survey_id = @survey2_id), 'Bueno', 2),
((SELECT id FROM survey_questions WHERE question_order = 7 AND survey_id = @survey2_id), 'Regular', 3),
((SELECT id FROM survey_questions WHERE question_order = 7 AND survey_id = @survey2_id), 'Malo', 4),
((SELECT id FROM survey_questions WHERE question_order = 7 AND survey_id = @survey2_id), 'Muy malo', 5),

-- Pregunta 8 - Impuestos municipales
((SELECT id FROM survey_questions WHERE question_order = 8 AND survey_id = @survey2_id), 'Muy justos', 1),
((SELECT id FROM survey_questions WHERE question_order = 8 AND survey_id = @survey2_id), 'Justos', 2),
((SELECT id FROM survey_questions WHERE question_order = 8 AND survey_id = @survey2_id), 'Regular', 3),
((SELECT id FROM survey_questions WHERE question_order = 8 AND survey_id = @survey2_id), 'Injustos', 4),
((SELECT id FROM survey_questions WHERE question_order = 8 AND survey_id = @survey2_id), 'Muy injustos', 5)
ON DUPLICATE KEY UPDATE option_text=option_text;

-- Asociar nuevas poblaciones a ambas encuestas
INSERT INTO survey_populations (survey_id, population_id)
SELECT 1, id FROM populations WHERE id > 5  -- Asociar nuevas poblaciones a la primera encuesta
ON DUPLICATE KEY UPDATE survey_id=survey_id;

INSERT INTO survey_populations (survey_id, population_id)
SELECT @survey2_id, id FROM populations  -- Asociar todas las poblaciones a la segunda encuesta
ON DUPLICATE KEY UPDATE survey_id=survey_id;

-- Insertar más respuestas de muestra con datos variados
-- Respuestas para diferentes poblaciones y encuestadores

-- Bocagrande (population_id = 6)
INSERT INTO survey_responses (
    survey_id, population_id, surveyor_id,
    respondent_first_name, respondent_last_name, respondent_phone, respondent_age_group, 
    respondent_address, respondent_gender, respondent_document_type, respondent_document_number,
    surveyor_first_name, surveyor_last_name, surveyor_document_number,
    ip_address, user_agent
) VALUES
(1, 6, 2, 'Alejandro', 'Mendoza', '3001111111', 'adulto', 'Calle 1 #2-3, Bocagrande', 'Hombre', 'CC', '1111111111', 'María', 'González', '12345678', '192.168.1.200', 'Mozilla/5.0...'),
(1, 6, 3, 'Valentina', 'Castro', '3002222222', 'joven', 'Calle 2 #4-5, Bocagrande', 'Mujer', 'CC', '2222222222', 'Carlos', 'Rodríguez', '87654321', '192.168.1.201', 'Mozilla/5.0...'),
(1, 6, 4, 'Ricardo', 'Vega', '3003333333', 'adulto mayor', 'Calle 3 #6-7, Bocagrande', 'Hombre', 'CC', '3333333333', 'Laura', 'Hernández', '99887766', '192.168.1.202', 'Mozilla/5.0...');

-- Getsemaní (population_id = 7)
INSERT INTO survey_responses (
    survey_id, population_id, surveyor_id,
    respondent_first_name, respondent_last_name, respondent_phone, respondent_age_group, 
    respondent_address, respondent_gender, respondent_document_type, respondent_document_number,
    surveyor_first_name, surveyor_last_name, surveyor_document_number,
    ip_address, user_agent
) VALUES
(1, 7, 2, 'Camila', 'Jiménez', '3004444444', 'joven', 'Calle 4 #8-9, Getsemaní', 'Mujer', 'TI', '4444444444', 'María', 'González', '12345678', '192.168.1.203', 'Mozilla/5.0...'),
(1, 7, 3, 'Andrés', 'Morales', '3005555555', 'adulto', 'Calle 5 #10-11, Getsemaní', 'Hombre', 'CC', '5555555555', 'Carlos', 'Rodríguez', '87654321', '192.168.1.204', 'Mozilla/5.0...'),
(1, 7, 5, 'Natalia', 'Ortiz', '3006666666', 'adulto', 'Calle 6 #12-13, Getsemaní', 'Mujer', 'CC', '6666666666', 'Sofía', 'Ramírez', '11223344', '192.168.1.205', 'Mozilla/5.0...');

-- Manga (population_id = 8)
INSERT INTO survey_responses (
    survey_id, population_id, surveyor_id,
    respondent_first_name, respondent_last_name, respondent_phone, respondent_age_group, 
    respondent_address, respondent_gender, respondent_document_type, respondent_document_number,
    surveyor_first_name, surveyor_last_name, surveyor_document_number,
    ip_address, user_agent
) VALUES
(1, 8, 4, 'Sebastián', 'López', '3007777777', 'adulto', 'Calle 7 #14-15, Manga', 'Hombre', 'CC', '7777777777', 'Laura', 'Hernández', '99887766', '192.168.1.206', 'Mozilla/5.0...'),
(1, 8, 5, 'Gabriela', 'Torres', '3008888888', 'joven', 'Calle 8 #16-17, Manga', 'Mujer', 'CC', '8888888888', 'Sofía', 'Ramírez', '11223344', '192.168.1.207', 'Mozilla/5.0...');

-- Castillo Grande (population_id = 9)
INSERT INTO survey_responses (
    survey_id, population_id, surveyor_id,
    respondent_first_name, respondent_last_name, respondent_phone, respondent_age_group, 
    respondent_address, respondent_gender, respondent_document_type, respondent_document_number,
    surveyor_first_name, surveyor_last_name, surveyor_document_number,
    ip_address, user_agent
) VALUES
(1, 9, 2, 'Fernando', 'Ruiz', '3009999999', 'adulto mayor', 'Calle 9 #18-19, Castillo Grande', 'Hombre', 'CC', '9999999999', 'María', 'González', '12345678', '192.168.1.208', 'Mozilla/5.0...'),
(1, 9, 3, 'Isabella', 'García', '3010000000', 'adulto', 'Calle 10 #20-21, Castillo Grande', 'Mujer', 'CC', '1010101010', 'Carlos', 'Rodríguez', '87654321', '192.168.1.209', 'Mozilla/5.0...'),
(1, 9, 4, 'Daniel', 'Martín', '3011111111', 'joven', 'Calle 11 #22-23, Castillo Grande', 'Hombre', 'TI', '1111111112', 'Laura', 'Hernández', '99887766', '192.168.1.210', 'Mozilla/5.0...');

-- Insertar respuestas específicas para las nuevas encuestas
-- Obtener IDs de las preguntas de la primera encuesta
SET @q1_1 = (SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = 1);
SET @q1_2 = (SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = 1);
SET @q1_3 = (SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = 1);
SET @q1_4 = (SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = 1);
SET @q1_5 = (SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = 1);
SET @q1_6 = (SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = 1);
SET @q1_7 = (SELECT id FROM survey_questions WHERE question_order = 7 AND survey_id = 1);

-- Respuestas para las nuevas encuestas (usando los IDs de respuesta generados)
-- Respuesta 8 (Alejandro Mendoza - Bocagrande)
INSERT INTO response_answers (response_id, question_id, option_id) VALUES
(8, @q1_1, (SELECT id FROM question_options WHERE question_id = @q1_1 AND option_text = 'Muy seguro')),
(8, @q1_2, (SELECT id FROM question_options WHERE question_id = @q1_2 AND option_text = 'Robo')),
(8, @q1_3, (SELECT id FROM question_options WHERE question_id = @q1_3 AND option_text = 'Buena')),
(8, @q1_4, (SELECT id FROM question_options WHERE question_id = @q1_4 AND option_text = 'Falta de empleo/oportunidades')),
(8, @q1_5, (SELECT id FROM question_options WHERE question_id = @q1_5 AND option_text = 'Siempre')),
(8, @q1_6, (SELECT id FROM question_options WHERE question_id = @q1_6 AND option_text = 'A diario')),
(8, @q1_7, (SELECT id FROM question_options WHERE question_id = @q1_7 AND option_text = 'Sí'));

-- Respuesta 9 (Valentina Castro - Bocagrande)
INSERT INTO response_answers (response_id, question_id, option_id) VALUES
(9, @q1_1, (SELECT id FROM question_options WHERE question_id = @q1_1 AND option_text = 'Seguro')),
(9, @q1_2, (SELECT id FROM question_options WHERE question_id = @q1_2 AND option_text = 'Microtráfico')),
(9, @q1_3, (SELECT id FROM question_options WHERE question_id = @q1_3 AND option_text = 'Regular')),
(9, @q1_4, (SELECT id FROM question_options WHERE question_id = @q1_4 AND option_text = 'Pobreza')),
(9, @q1_5, (SELECT id FROM question_options WHERE question_id = @q1_5 AND option_text = 'A veces')),
(9, @q1_6, (SELECT id FROM question_options WHERE question_id = @q1_6 AND option_text = 'Varias veces por semana')),
(9, @q1_7, (SELECT id FROM question_options WHERE question_id = @q1_7 AND option_text = 'No'));

-- Continuar con más respuestas para tener una muestra más completa...
-- (Se pueden agregar más respuestas siguiendo el mismo patrón)

-- Insertar algunas respuestas para la segunda encuesta
INSERT INTO survey_responses (
    survey_id, population_id, surveyor_id,
    respondent_first_name, respondent_last_name, respondent_phone, respondent_age_group, 
    respondent_address, respondent_gender, respondent_document_type, respondent_document_number,
    surveyor_first_name, surveyor_last_name, surveyor_document_number,
    ip_address, user_agent
) VALUES
(@survey2_id, 1, 2, 'Roberto', 'Pérez', '3022222222', 'adulto', 'Calle 12 #24-25, Olaya', 'Hombre', 'CC', '1212121212', 'María', 'González', '12345678', '192.168.1.211', 'Mozilla/5.0...'),
(@survey2_id, 2, 3, 'Lucía', 'Gómez', '3033333333', 'adulto mayor', 'Calle 13 #26-27, Villa del Sol', 'Mujer', 'CC', '1313131313', 'Carlos', 'Rodríguez', '87654321', '192.168.1.212', 'Mozilla/5.0...'),
(@survey2_id, 6, 4, 'Mario', 'Díaz', '3044444444', 'joven', 'Calle 14 #28-29, Bocagrande', 'Hombre', 'CC', '1414141414', 'Laura', 'Hernández', '99887766', '192.168.1.213', 'Mozilla/5.0...');

-- Insertar respuestas específicas para la segunda encuesta
-- Obtener IDs de las preguntas de la segunda encuesta
SET @q2_1 = (SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey2_id);
SET @q2_2 = (SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey2_id);
SET @q2_3 = (SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey2_id);
SET @q2_4 = (SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey2_id);
SET @q2_5 = (SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey2_id);
SET @q2_6 = (SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey2_id);
SET @q2_7 = (SELECT id FROM survey_questions WHERE question_order = 7 AND survey_id = @survey2_id);
SET @q2_8 = (SELECT id FROM survey_questions WHERE question_order = 8 AND survey_id = @survey2_id);

-- Respuesta para la segunda encuesta (Roberto Pérez - Olaya)
INSERT INTO response_answers (response_id, question_id, option_id) VALUES
((SELECT MAX(id) - 2 FROM survey_responses), @q2_1, (SELECT id FROM question_options WHERE question_id = @q2_1 AND option_text = 'Bueno')),
((SELECT MAX(id) - 2 FROM survey_responses), @q2_2, (SELECT id FROM question_options WHERE question_id = @q2_2 AND option_text = 'Regular')),
((SELECT MAX(id) - 2 FROM survey_responses), @q2_3, (SELECT id FROM question_options WHERE question_id = @q2_3 AND option_text = 'Bueno')),
((SELECT MAX(id) - 2 FROM survey_responses), @q2_4, (SELECT id FROM question_options WHERE question_id = @q2_4 AND option_text = 'Malo')),
((SELECT MAX(id) - 2 FROM survey_responses), @q2_5, (SELECT id FROM question_options WHERE question_id = @q2_5 AND option_text = 'Regular')),
((SELECT MAX(id) - 2 FROM survey_responses), @q2_6, (SELECT id FROM question_options WHERE question_id = @q2_6 AND option_text = 'Malo')),
((SELECT MAX(id) - 2 FROM survey_responses), @q2_7, (SELECT id FROM question_options WHERE question_id = @q2_7 AND option_text = 'Regular')),
((SELECT MAX(id) - 2 FROM survey_responses), @q2_8, (SELECT id FROM question_options WHERE question_id = @q2_8 AND option_text = 'Justos'));

-- Insertar más logs del sistema
INSERT INTO system_logs (user_id, action, table_name, record_id, new_values, ip_address) VALUES
(2, 'CREATE', 'survey_responses', 8, '{"respondent_first_name":"Alejandro"}', '192.168.1.200'),
(3, 'CREATE', 'survey_responses', 9, '{"respondent_first_name":"Valentina"}', '192.168.1.201'),
(4, 'CREATE', 'survey_responses', 10, '{"respondent_first_name":"Ricardo"}', '192.168.1.202'),
(1, 'CREATE', 'surveys', @survey2_id, '{"name":"SATISFACCIÓN CON SERVICIOS PÚBLICOS"}', '127.0.0.1');

COMMIT;
