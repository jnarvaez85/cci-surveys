<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

class Survey {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener encuestas activas
     */
    public function getActiveSurveys() {
        try {
            $stmt = $this->db->prepare("
                SELECT s.id, s.name, s.description, s.created_at,
                       u.first_name, u.last_name
                FROM surveys s
                JOIN users u ON s.created_by = u.id
                WHERE s.is_active = 1
                ORDER BY s.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting active surveys: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener poblaciones asociadas a una encuesta
     */
    public function getPopulationsBySurvey($surveyId) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.id, p.name, p.description
                FROM populations p
                JOIN survey_populations sp ON p.id = sp.population_id
                WHERE sp.survey_id = :survey_id AND p.is_active = 1
                ORDER BY p.name
            ");
            $stmt->bindParam(':survey_id', $surveyId);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting populations by survey: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener preguntas de una encuesta con sus opciones
     */
    public function getQuestionsBySurvey($surveyId) {
        try {
            $stmt = $this->db->prepare("
                SELECT sq.id, sq.question_text, sq.question_order
                FROM survey_questions sq
                WHERE sq.survey_id = :survey_id AND sq.is_active = 1
                ORDER BY sq.question_order
            ");
            $stmt->bindParam(':survey_id', $surveyId);
            $stmt->execute();
            $questions = $stmt->fetchAll();
            
            // Obtener opciones para cada pregunta
            foreach ($questions as &$question) {
                $stmt = $this->db->prepare("
                    SELECT id, option_text, option_order
                    FROM question_options
                    WHERE question_id = :question_id AND is_active = 1
                    ORDER BY option_order
                ");
                $stmt->bindParam(':question_id', $question['id']);
                $stmt->execute();
                $question['options'] = $stmt->fetchAll();
            }
            
            return $questions;
        } catch (Exception $e) {
            error_log("Error getting questions by survey: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Enviar respuesta de encuesta
     */
    public function submitSurveyResponse($data, $surveyorId) {
        try {
            $this->db->beginTransaction();
            
            // Validar datos requeridos
            $requiredFields = [
                'survey_id', 'population_id', 'respondent_first_name', 'respondent_last_name',
                'respondent_age_group', 'respondent_address', 'respondent_gender',
                'surveyor_first_name', 'surveyor_last_name', 'surveyor_document_number'
            ];
            
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("El campo {$field} es requerido");
                }
            }
            
            // Verificar que la encuesta y población existen y están activas
            $stmt = $this->db->prepare("
                SELECT s.id as survey_id, p.id as population_id
                FROM surveys s
                JOIN survey_populations sp ON s.id = sp.survey_id
                JOIN populations p ON sp.population_id = p.id
                WHERE s.id = :survey_id AND p.id = :population_id 
                AND s.is_active = 1 AND p.is_active = 1
            ");
            $stmt->bindParam(':survey_id', $data['survey_id']);
            $stmt->bindParam(':population_id', $data['population_id']);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                throw new Exception("La encuesta o población seleccionada no es válida");
            }
            
            // Insertar respuesta principal
            $stmt = $this->db->prepare("
                INSERT INTO survey_responses (
                    survey_id, population_id, surveyor_id,
                    respondent_first_name, respondent_last_name, respondent_phone,
                    respondent_age_group, respondent_address, respondent_gender,
                    respondent_document_type, respondent_document_number,
                    surveyor_first_name, surveyor_last_name, surveyor_document_number,
                    ip_address, user_agent
                ) VALUES (
                    :survey_id, :population_id, :surveyor_id,
                    :respondent_first_name, :respondent_last_name, :respondent_phone,
                    :respondent_age_group, :respondent_address, :respondent_gender,
                    :respondent_document_type, :respondent_document_number,
                    :surveyor_first_name, :surveyor_last_name, :surveyor_document_number,
                    :ip_address, :user_agent
                )
            ");
            
            $stmt->bindParam(':survey_id', $data['survey_id']);
            $stmt->bindParam(':population_id', $data['population_id']);
            $stmt->bindParam(':surveyor_id', $surveyorId);
            $stmt->bindParam(':respondent_first_name', $data['respondent_first_name']);
            $stmt->bindParam(':respondent_last_name', $data['respondent_last_name']);
            $stmt->bindParam(':respondent_phone', $data['respondent_phone'] ?? null);
            $stmt->bindParam(':respondent_age_group', $data['respondent_age_group']);
            $stmt->bindParam(':respondent_address', $data['respondent_address']);
            $stmt->bindParam(':respondent_gender', $data['respondent_gender']);
            $stmt->bindParam(':respondent_document_type', $data['respondent_document_type'] ?? null);
            $stmt->bindParam(':respondent_document_number', $data['respondent_document_number'] ?? null);
            $stmt->bindParam(':surveyor_first_name', $data['surveyor_first_name']);
            $stmt->bindParam(':surveyor_last_name', $data['surveyor_last_name']);
            $stmt->bindParam(':surveyor_document_number', $data['surveyor_document_number']);
            $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
            
            $stmt->execute();
            $responseId = $this->db->lastInsertId();
            
            // Insertar respuestas a las preguntas
            $questions = $this->getQuestionsBySurvey($data['survey_id']);
            foreach ($questions as $question) {
                $questionKey = 'question_' . $question['id'];
                if (isset($data[$questionKey]) && !empty($data[$questionKey])) {
                    $optionId = $data[$questionKey];
                    
                    // Verificar que la opción pertenece a la pregunta
                    $stmt = $this->db->prepare("
                        SELECT id FROM question_options 
                        WHERE id = :option_id AND question_id = :question_id AND is_active = 1
                    ");
                    $stmt->bindParam(':option_id', $optionId);
                    $stmt->bindParam(':question_id', $question['id']);
                    $stmt->execute();
                    
                    if ($stmt->fetch()) {
                        $stmt = $this->db->prepare("
                            INSERT INTO response_answers (response_id, question_id, option_id)
                            VALUES (:response_id, :question_id, :option_id)
                        ");
                        $stmt->bindParam(':response_id', $responseId);
                        $stmt->bindParam(':question_id', $question['id']);
                        $stmt->bindParam(':option_id', $optionId);
                        $stmt->execute();
                    }
                }
            }
            
            $this->db->commit();
            
            // Registrar en logs
            $this->logSurveySubmission($surveyorId, $responseId, $data);
            
            return [
                'success' => true,
                'message' => 'Encuesta enviada correctamente',
                'response_id' => $responseId
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error submitting survey response: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener estadísticas de respuestas por encuesta
     */
    public function getSurveyStats($surveyId) {
        try {
            // Estadísticas generales
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_responses,
                    COUNT(DISTINCT population_id) as populations_count,
                    COUNT(DISTINCT surveyor_id) as surveyors_count
                FROM survey_responses 
                WHERE survey_id = :survey_id
            ");
            $stmt->bindParam(':survey_id', $surveyId);
            $stmt->execute();
            $generalStats = $stmt->fetch();
            
            // Estadísticas por población
            $stmt = $this->db->prepare("
                SELECT p.name, COUNT(*) as count
                FROM survey_responses sr
                JOIN populations p ON sr.population_id = p.id
                WHERE sr.survey_id = :survey_id
                GROUP BY p.id, p.name
                ORDER BY count DESC
            ");
            $stmt->bindParam(':survey_id', $surveyId);
            $stmt->execute();
            $populationStats = $stmt->fetchAll();
            
            // Estadísticas por edad
            $stmt = $this->db->prepare("
                SELECT respondent_age_group, COUNT(*) as count
                FROM survey_responses 
                WHERE survey_id = :survey_id
                GROUP BY respondent_age_group
                ORDER BY count DESC
            ");
            $stmt->bindParam(':survey_id', $surveyId);
            $stmt->execute();
            $ageStats = $stmt->fetchAll();
            
            // Estadísticas por género
            $stmt = $this->db->prepare("
                SELECT respondent_gender, COUNT(*) as count
                FROM survey_responses 
                WHERE survey_id = :survey_id
                GROUP BY respondent_gender
                ORDER BY count DESC
            ");
            $stmt->bindParam(':survey_id', $surveyId);
            $stmt->execute();
            $genderStats = $stmt->fetchAll();
            
            return [
                'general' => $generalStats,
                'populations' => $populationStats,
                'ages' => $ageStats,
                'genders' => $genderStats
            ];
            
        } catch (Exception $e) {
            error_log("Error getting survey stats: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener respuestas detalladas por pregunta
     */
    public function getQuestionResponses($surveyId, $questionId = null) {
        try {
            $sql = "
                SELECT 
                    qo.option_text,
                    COUNT(ra.id) as count,
                    ROUND(COUNT(ra.id) * 100.0 / (
                        SELECT COUNT(*) 
                        FROM response_answers ra2 
                        JOIN survey_questions sq2 ON ra2.question_id = sq2.id 
                        WHERE sq2.survey_id = :survey_id AND sq2.id = qo.question_id
                    ), 2) as percentage
                FROM question_options qo
                LEFT JOIN response_answers ra ON qo.id = ra.option_id
                JOIN survey_questions sq ON qo.question_id = sq.id
                WHERE sq.survey_id = :survey_id
            ";
            
            $params = [':survey_id' => $surveyId];
            
            if ($questionId) {
                $sql .= " AND sq.id = :question_id";
                $params[':question_id'] = $questionId;
            }
            
            $sql .= " GROUP BY qo.id, qo.option_text, qo.option_order ORDER BY qo.option_order";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error getting question responses: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Registrar envío de encuesta en logs
     */
    private function logSurveySubmission($surveyorId, $responseId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO system_logs (user_id, action, table_name, record_id, new_values, ip_address, user_agent)
                VALUES (:user_id, 'SUBMIT_SURVEY', 'survey_responses', :record_id, :new_values, :ip_address, :user_agent)
            ");
            
            $logData = [
                'survey_id' => $data['survey_id'],
                'population_id' => $data['population_id'],
                'respondent_name' => $data['respondent_first_name'] . ' ' . $data['respondent_last_name']
            ];
            
            $stmt->bindParam(':user_id', $surveyorId);
            $stmt->bindParam(':record_id', $responseId);
            $stmt->bindParam(':new_values', json_encode($logData));
            $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
            
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error logging survey submission: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener respuestas por encuestador
     */
    public function getResponsesBySurveyor($surveyorId, $limit = 50, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    sr.id,
                    sr.respondent_first_name,
                    sr.respondent_last_name,
                    sr.respondent_age_group,
                    sr.respondent_gender,
                    sr.submission_date,
                    s.name as survey_name,
                    p.name as population_name
                FROM survey_responses sr
                JOIN surveys s ON sr.survey_id = s.id
                JOIN populations p ON sr.population_id = p.id
                WHERE sr.surveyor_id = :surveyor_id
                ORDER BY sr.submission_date DESC
                LIMIT :limit OFFSET :offset
            ");
            
            $stmt->bindParam(':surveyor_id', $surveyorId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error getting responses by surveyor: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Contar total de respuestas por encuestador
     */
    public function countResponsesBySurveyor($surveyorId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total
                FROM survey_responses 
                WHERE surveyor_id = :surveyor_id
            ");
            $stmt->bindParam(':surveyor_id', $surveyorId);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
            
        } catch (Exception $e) {
            error_log("Error counting responses by surveyor: " . $e->getMessage());
            return 0;
        }
    }
}
?>
