-- Script de creación de base de datos para CCI Surveys
-- Ejecutar este script para crear todas las tablas necesarias

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS surveys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE surveys;

-- Tabla de usuarios del sistema
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    document_type VARCHAR(20),
    document_number VARCHAR(20),
    role ENUM('admin', 'surveyor') DEFAULT 'surveyor',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
);

-- Tabla de poblaciones
CREATE TABLE IF NOT EXISTS populations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_active (is_active)
);

-- Tabla de encuestas (surveys)
CREATE TABLE IF NOT EXISTS surveys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_name (name),
    INDEX idx_active (is_active),
    INDEX idx_created_by (created_by)
);

-- Tabla de asociación entre encuestas y poblaciones
CREATE TABLE IF NOT EXISTS survey_populations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_id INT NOT NULL,
    population_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE,
    FOREIGN KEY (population_id) REFERENCES populations(id) ON DELETE CASCADE,
    UNIQUE KEY unique_survey_population (survey_id, population_id),
    INDEX idx_survey_id (survey_id),
    INDEX idx_population_id (population_id)
);

-- Tabla de preguntas de encuesta
CREATE TABLE IF NOT EXISTS survey_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_order INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE,
    INDEX idx_survey_id (survey_id),
    INDEX idx_order (question_order),
    INDEX idx_active (is_active)
);

-- Tabla de opciones de respuesta
CREATE TABLE IF NOT EXISTS question_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    option_order INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES survey_questions(id) ON DELETE CASCADE,
    INDEX idx_question_id (question_id),
    INDEX idx_order (option_order),
    INDEX idx_active (is_active)
);

-- Tabla principal de respuestas de encuesta
CREATE TABLE IF NOT EXISTS survey_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_id INT NOT NULL,
    population_id INT NOT NULL,
    surveyor_id INT NOT NULL,
    -- Datos del encuestado
    respondent_first_name VARCHAR(50) NOT NULL,
    respondent_last_name VARCHAR(50) NOT NULL,
    respondent_phone VARCHAR(20),
    respondent_age_group ENUM('joven', 'adulto', 'adulto mayor', 'otro') NOT NULL,
    respondent_address TEXT NOT NULL,
    respondent_gender ENUM('Mujer', 'Hombre') NOT NULL,
    respondent_document_type VARCHAR(20),
    respondent_document_number VARCHAR(20),
    -- Datos del encuestador
    surveyor_first_name VARCHAR(50) NOT NULL,
    surveyor_last_name VARCHAR(50) NOT NULL,
    surveyor_document_number VARCHAR(20) NOT NULL,
    -- Metadatos
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE RESTRICT,
    FOREIGN KEY (population_id) REFERENCES populations(id) ON DELETE RESTRICT,
    FOREIGN KEY (surveyor_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_survey_id (survey_id),
    INDEX idx_population_id (population_id),
    INDEX idx_surveyor_id (surveyor_id),
    INDEX idx_age_group (respondent_age_group),
    INDEX idx_gender (respondent_gender),
    INDEX idx_submission_date (submission_date)
);

-- Tabla de respuestas específicas a preguntas
CREATE TABLE IF NOT EXISTS response_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    response_id INT NOT NULL,
    question_id INT NOT NULL,
    option_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (response_id) REFERENCES survey_responses(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES survey_questions(id) ON DELETE CASCADE,
    FOREIGN KEY (option_id) REFERENCES question_options(id) ON DELETE CASCADE,
    UNIQUE KEY unique_response_question (response_id, question_id),
    INDEX idx_response_id (response_id),
    INDEX idx_question_id (question_id),
    INDEX idx_option_id (option_id)
);

-- Tabla de logs del sistema
CREATE TABLE IF NOT EXISTS system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_table_name (table_name),
    INDEX idx_created_at (created_at)
);

-- Tabla de sesiones (para manejo de sesiones más seguro)
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);

-- Crear usuario administrador por defecto
INSERT INTO users (username, email, password_hash, first_name, last_name, role) 
VALUES ('admin', 'admin@cci-surveys.com', '$argon2id$v=19$m=65536,t=4,p=3$VGVzdFBhc3N3b3JkMTIz$TestHash', 'Administrador', 'Sistema', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Crear poblaciones de ejemplo
INSERT INTO populations (name, description) VALUES
('Olaya', 'Barrio Olaya - Zona norte de Cartagena'),
('Villa del Sol', 'Villa del Sol - Sector residencial'),
('Parque Heredia', 'Parque Heredia - Zona centro histórico'),
('Ternera', 'Ternera - Sector residencial'),
('San Fernando', 'San Fernando - Zona sur de Cartagena')
ON DUPLICATE KEY UPDATE name=name;

-- Crear encuesta de ejemplo
INSERT INTO surveys (name, description, created_by) VALUES
('SEGURIDAD DE LA POBLACIÓN', 'Encuesta sobre percepción de seguridad ciudadana', 1)
ON DUPLICATE KEY UPDATE name=name;

-- Obtener el ID de la encuesta creada
SET @survey_id = LAST_INSERT_ID();

-- Crear preguntas de la encuesta
INSERT INTO survey_questions (survey_id, question_text, question_order) VALUES
(@survey_id, '¿Qué tan seguro(a) se siente viviendo en esta ciudad?', 1),
(@survey_id, '¿Cuál considera que es el principal problema de inseguridad de la ciudad?', 2),
(@survey_id, '¿Qué tan efectiva considera la labor de la alcaldía o autoridades municipales en la prevención del delito?', 3),
(@survey_id, '¿Cuál de los siguientes temas socio económicos considera que genera inseguridad de la ciudad?', 4),
(@survey_id, '¿Cree usted que Dios lo(a) protege en su vida diaria?', 5),
(@survey_id, '¿Con qué frecuencia ora pidiendo a Dios?', 6),
(@survey_id, '¿Le gustaría que oráramos por usted?', 7)
ON DUPLICATE KEY UPDATE question_text=question_text;

-- Crear opciones de respuesta para cada pregunta
INSERT INTO question_options (question_id, option_text, option_order) VALUES
-- Pregunta 1
((SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey_id), 'Muy seguro', 1),
((SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey_id), 'Seguro', 2),
((SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey_id), 'Inseguro', 3),
((SELECT id FROM survey_questions WHERE question_order = 1 AND survey_id = @survey_id), 'Muy inseguro', 4),

-- Pregunta 2
((SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey_id), 'Robo', 1),
((SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey_id), 'Homicidios', 2),
((SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey_id), 'Microtráfico', 3),
((SELECT id FROM survey_questions WHERE question_order = 2 AND survey_id = @survey_id), 'Pandillas', 4),

-- Pregunta 3
((SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey_id), 'Buena', 1),
((SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey_id), 'Regular', 2),
((SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey_id), 'Mala', 3),
((SELECT id FROM survey_questions WHERE question_order = 3 AND survey_id = @survey_id), 'Muy deficiente', 4),

-- Pregunta 4
((SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey_id), 'Pobreza', 1),
((SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey_id), 'Falta de educación', 2),
((SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey_id), 'Falta de empleo/oportunidades', 3),
((SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey_id), 'Ansiedad', 4),
((SELECT id FROM survey_questions WHERE question_order = 4 AND survey_id = @survey_id), 'Pánico', 5),

-- Pregunta 5
((SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey_id), 'Siempre', 1),
((SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey_id), 'A veces', 2),
((SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey_id), 'Rara vez', 3),
((SELECT id FROM survey_questions WHERE question_order = 5 AND survey_id = @survey_id), 'Nunca', 4),

-- Pregunta 6
((SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey_id), 'A diario', 1),
((SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey_id), 'Varias veces por semana', 2),
((SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey_id), 'Solo en momentos de dificultad', 3),
((SELECT id FROM survey_questions WHERE question_order = 6 AND survey_id = @survey_id), 'Nunca', 4),

-- Pregunta 7
((SELECT id FROM survey_questions WHERE question_order = 7 AND survey_id = @survey_id), 'Sí', 1),
((SELECT id FROM survey_questions WHERE question_order = 7 AND survey_id = @survey_id), 'No', 2)
ON DUPLICATE KEY UPDATE option_text=option_text;

-- Asociar poblaciones a la encuesta
INSERT INTO survey_populations (survey_id, population_id)
SELECT @survey_id, id FROM populations
ON DUPLICATE KEY UPDATE survey_id=survey_id;
