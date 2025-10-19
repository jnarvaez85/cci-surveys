<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

header('Content-Type: application/json');

try {
    $auth = new Auth();
    
    if (!$auth->isAuthenticated()) {
        json_response(['success' => false, 'message' => 'No autorizado'], 401);
    }
    
    $responseId = $_GET['id'] ?? null;
    
    if (!$responseId) {
        json_response(['success' => false, 'message' => 'ID de respuesta requerido'], 400);
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Obtener datos principales de la respuesta
    $stmt = $db->prepare("
        SELECT 
            sr.*,
            s.name as survey_name,
            p.name as population_name,
            u.first_name as surveyor_first_name,
            u.last_name as surveyor_last_name
        FROM survey_responses sr
        JOIN surveys s ON sr.survey_id = s.id
        JOIN populations p ON sr.population_id = p.id
        JOIN users u ON sr.surveyor_id = u.id
        WHERE sr.id = :response_id
    ");
    $stmt->bindParam(':response_id', $responseId);
    $stmt->execute();
    $response = $stmt->fetch();
    
    if (!$response) {
        json_response(['success' => false, 'message' => 'Respuesta no encontrada'], 404);
    }
    
    // Obtener respuestas a las preguntas
    $stmt = $db->prepare("
        SELECT 
            sq.question_text,
            sq.question_order,
            qo.option_text,
            ra.created_at
        FROM response_answers ra
        JOIN survey_questions sq ON ra.question_id = sq.id
        JOIN question_options qo ON ra.option_id = qo.id
        WHERE ra.response_id = :response_id
        ORDER BY sq.question_order
    ");
    $stmt->bindParam(':response_id', $responseId);
    $stmt->execute();
    $answers = $stmt->fetchAll();
    
    // Generar HTML
    $html = '
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary mb-3">
                    <i class="bi bi-person me-2"></i>
                    Datos del Encuestado
                </h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Nombre:</strong></td>
                        <td>' . htmlspecialchars($response['respondent_first_name'] . ' ' . $response['respondent_last_name']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Teléfono:</strong></td>
                        <td>' . htmlspecialchars($response['respondent_phone'] ?: 'No especificado') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Edad:</strong></td>
                        <td><span class="badge bg-info">' . ucfirst($response['respondent_age_group']) . '</span></td>
                    </tr>
                    <tr>
                        <td><strong>Género:</strong></td>
                        <td>' . htmlspecialchars($response['respondent_gender']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Dirección:</strong></td>
                        <td>' . htmlspecialchars($response['respondent_address']) . '</td>
                    </tr>
                    ' . ($response['respondent_document_type'] && $response['respondent_document_number'] ? '
                    <tr>
                        <td><strong>Documento:</strong></td>
                        <td>' . htmlspecialchars($response['respondent_document_type'] . ' ' . $response['respondent_document_number']) . '</td>
                    </tr>
                    ' : '') . '
                </table>
            </div>
            
            <div class="col-md-6">
                <h6 class="text-primary mb-3">
                    <i class="bi bi-person-badge me-2"></i>
                    Datos del Encuestador
                </h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Nombre:</strong></td>
                        <td>' . htmlspecialchars($response['surveyor_first_name'] . ' ' . $response['surveyor_last_name']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Documento:</strong></td>
                        <td>' . htmlspecialchars($response['surveyor_document_number']) . '</td>
                    </tr>
                </table>
                
                <h6 class="text-primary mb-3 mt-4">
                    <i class="bi bi-info-circle me-2"></i>
                    Información General
                </h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Encuesta:</strong></td>
                        <td>' . htmlspecialchars($response['survey_name']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Población:</strong></td>
                        <td><span class="badge bg-secondary">' . htmlspecialchars($response['population_name']) . '</span></td>
                    </tr>
                    <tr>
                        <td><strong>Fecha:</strong></td>
                        <td>' . date('d/m/Y H:i:s', strtotime($response['submission_date'])) . '</td>
                    </tr>
                </table>
            </div>
        </div>
    ';
    
    if (!empty($answers)) {
        $html .= '
            <hr class="my-4">
            <h6 class="text-primary mb-3">
                <i class="bi bi-question-circle me-2"></i>
                Respuestas a las Preguntas
            </h6>
            <div class="row">
        ';
        
        foreach ($answers as $answer) {
            $html .= '
                <div class="col-12 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body py-3">
                            <h6 class="mb-2">
                                <span class="badge bg-primary me-2">' . $answer['question_order'] . '</span>
                                ' . htmlspecialchars($answer['question_text']) . '
                            </h6>
                            <p class="mb-0 fw-bold text-success">
                                <i class="bi bi-check-circle me-2"></i>
                                ' . htmlspecialchars($answer['option_text']) . '
                            </p>
                        </div>
                    </div>
                </div>
            ';
        }
        
        $html .= '</div>';
    }
    
    json_response([
        'success' => true,
        'html' => $html
    ]);
    
} catch (Exception $e) {
    error_log("Error in get-response-details.php: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'Error interno del servidor'], 500);
}
?>
