<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Survey.php';

header('Content-Type: application/json');

try {
    $auth = new Auth();
    
    if (!$auth->isAuthenticated()) {
        json_response(['success' => false, 'message' => 'No autorizado'], 401);
    }
    
    $surveyId = $_GET['survey_id'] ?? null;
    
    if (!$surveyId) {
        json_response(['success' => false, 'message' => 'ID de encuesta requerido'], 400);
    }
    
    $survey = new Survey();
    $questions = $survey->getQuestionsBySurvey($surveyId);
    
    json_response([
        'success' => true,
        'questions' => $questions
    ]);
    
} catch (Exception $e) {
    error_log("Error in get-questions.php: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'Error interno del servidor'], 500);
}
?>
