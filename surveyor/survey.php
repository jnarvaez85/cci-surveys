<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Survey.php';

$auth = new Auth();
$auth->requireAuth();

if (!$auth->hasRole('surveyor')) {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso denegado');
}

$currentUser = $auth->getCurrentUser();
$pageTitle = 'Nueva Encuesta';
$currentPage = 'survey';

$survey = new Survey();
$availableSurveys = $survey->getActiveSurveys();

$error = '';
$success = '';

// Procesar formulario de encuesta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido.';
    } else {
        try {
            $result = $survey->submitSurveyResponse($_POST, $currentUser['id']);
            
            if ($result['success']) {
                $success = 'Encuesta enviada correctamente.';
                // Limpiar formulario
                $_POST = [];
            } else {
                $error = $result['message'];
            }
        } catch (Exception $e) {
            $error = 'Error al procesar la encuesta: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-clipboard-plus me-2"></i>
                        Nueva Encuesta
                    </h4>
                    <p class="mb-0 opacity-75">Complete todos los campos requeridos para registrar una nueva encuesta</p>
                </div>
                
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form id="surveyForm" method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        
                        <!-- Selección de Encuesta y Población -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="survey_id" class="form-label">
                                    <i class="bi bi-clipboard-data me-1"></i>
                                    Tipo de Encuesta <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="survey_id" name="survey_id" required>
                                    <option value="">Seleccione una encuesta</option>
                                    <?php foreach ($availableSurveys as $s): ?>
                                        <option value="<?= $s['id'] ?>" 
                                                <?= ($_POST['survey_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="population_id" class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    Población <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="population_id" name="population_id" required>
                                    <option value="">Seleccione una población</option>
                                    <!-- Se llenará dinámicamente con JavaScript -->
                                </select>
                            </div>
                        </div>
                        
                        <!-- Datos del Encuestado -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="bi bi-person me-2"></i>
                                    Datos del Encuestado
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="respondent_first_name" class="form-label">
                                            Nombres <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="respondent_first_name" 
                                               name="respondent_first_name" 
                                               value="<?= htmlspecialchars($_POST['respondent_first_name'] ?? '') ?>"
                                               required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="respondent_last_name" class="form-label">
                                            Apellidos <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="respondent_last_name" 
                                               name="respondent_last_name" 
                                               value="<?= htmlspecialchars($_POST['respondent_last_name'] ?? '') ?>"
                                               required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="respondent_phone" class="form-label">
                                            <i class="bi bi-telephone me-1"></i>
                                            Teléfono
                                        </label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="respondent_phone" 
                                               name="respondent_phone" 
                                               value="<?= htmlspecialchars($_POST['respondent_phone'] ?? '') ?>"
                                               placeholder="Ej: 3001234567">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="respondent_age_group" class="form-label">
                                            <i class="bi bi-calendar me-1"></i>
                                            Grupo de Edad <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="respondent_age_group" name="respondent_age_group" required>
                                            <option value="">Seleccione grupo de edad</option>
                                            <option value="joven" <?= ($_POST['respondent_age_group'] ?? '') == 'joven' ? 'selected' : '' ?>>Joven</option>
                                            <option value="adulto" <?= ($_POST['respondent_age_group'] ?? '') == 'adulto' ? 'selected' : '' ?>>Adulto</option>
                                            <option value="adulto mayor" <?= ($_POST['respondent_age_group'] ?? '') == 'adulto mayor' ? 'selected' : '' ?>>Adulto Mayor</option>
                                            <option value="otro" <?= ($_POST['respondent_age_group'] ?? '') == 'otro' ? 'selected' : '' ?>>Otro</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label for="respondent_address" class="form-label">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            Dirección <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" 
                                                  id="respondent_address" 
                                                  name="respondent_address" 
                                                  rows="2" 
                                                  required><?= htmlspecialchars($_POST['respondent_address'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="respondent_gender" class="form-label">
                                            <i class="bi bi-gender-ambiguous me-1"></i>
                                            Sexo <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="respondent_gender" name="respondent_gender" required>
                                            <option value="">Seleccione sexo</option>
                                            <option value="Mujer" <?= ($_POST['respondent_gender'] ?? '') == 'Mujer' ? 'selected' : '' ?>>Mujer</option>
                                            <option value="Hombre" <?= ($_POST['respondent_gender'] ?? '') == 'Hombre' ? 'selected' : '' ?>>Hombre</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="respondent_document_type" class="form-label">
                                            <i class="bi bi-card-text me-1"></i>
                                            Tipo de Documento
                                        </label>
                                        <select class="form-select" id="respondent_document_type" name="respondent_document_type">
                                            <option value="">Seleccione tipo</option>
                                            <option value="CC" <?= ($_POST['respondent_document_type'] ?? '') == 'CC' ? 'selected' : '' ?>>Cédula de Ciudadanía</option>
                                            <option value="TI" <?= ($_POST['respondent_document_type'] ?? '') == 'TI' ? 'selected' : '' ?>>Tarjeta de Identidad</option>
                                            <option value="CE" <?= ($_POST['respondent_document_type'] ?? '') == 'CE' ? 'selected' : '' ?>>Cédula de Extranjería</option>
                                            <option value="PA" <?= ($_POST['respondent_document_type'] ?? '') == 'PA' ? 'selected' : '' ?>>Pasaporte</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="respondent_document_number" class="form-label">
                                            <i class="bi bi-hash me-1"></i>
                                            Número de Documento
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="respondent_document_number" 
                                               name="respondent_document_number" 
                                               value="<?= htmlspecialchars($_POST['respondent_document_number'] ?? '') ?>"
                                               placeholder="Ej: 12345678">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Datos del Encuestador -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="bi bi-person-badge me-2"></i>
                                    Datos del Encuestador
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="surveyor_first_name" class="form-label">
                                            Nombres del Encuestador <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="surveyor_first_name" 
                                               name="surveyor_first_name" 
                                               value="<?= htmlspecialchars($currentUser['first_name']) ?>"
                                               readonly>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="surveyor_last_name" class="form-label">
                                            Apellidos del Encuestador <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="surveyor_last_name" 
                                               name="surveyor_last_name" 
                                               value="<?= htmlspecialchars($currentUser['last_name']) ?>"
                                               readonly>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="surveyor_document_number" class="form-label">
                                            <i class="bi bi-card-text me-1"></i>
                                            Número de Documento del Encuestador <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="surveyor_document_number" 
                                               name="surveyor_document_number" 
                                               value="<?= htmlspecialchars($_POST['surveyor_document_number'] ?? $currentUser['document_number'] ?? '') ?>"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Preguntas de la Encuesta -->
                        <div id="surveyQuestions" class="card mb-4" style="display: none;">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="bi bi-question-circle me-2"></i>
                                    Preguntas de la Encuesta
                                </h5>
                            </div>
                            <div class="card-body" id="questionsContainer">
                                <!-- Las preguntas se cargarán dinámicamente con JavaScript -->
                            </div>
                        </div>
                        
                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between">
                            <a href="/cci-surveys/surveyor/dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Volver al Dashboard
                            </a>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="bi bi-check-circle me-2"></i>
                                Enviar Encuesta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const surveySelect = document.getElementById('survey_id');
    const populationSelect = document.getElementById('population_id');
    const surveyQuestions = document.getElementById('surveyQuestions');
    const questionsContainer = document.getElementById('questionsContainer');
    const submitBtn = document.getElementById('submitBtn');
    
    // Cargar poblaciones cuando se selecciona una encuesta
    surveySelect.addEventListener('change', function() {
        const surveyId = this.value;
        
        // Limpiar selecciones anteriores
        populationSelect.innerHTML = '<option value="">Seleccione una población</option>';
        surveyQuestions.style.display = 'none';
        questionsContainer.innerHTML = '';
        submitBtn.disabled = true;
        
        if (surveyId) {
            // Cargar poblaciones para la encuesta seleccionada
            fetch(`/cci-surveys/api/get-populations.php?survey_id=${surveyId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.populations.forEach(population => {
                            const option = document.createElement('option');
                            option.value = population.id;
                            option.textContent = population.name;
                            populationSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading populations:', error);
                    showNotification('Error al cargar las poblaciones', 'danger');
                });
        }
    });
    
    // Cargar preguntas cuando se selecciona una población
    populationSelect.addEventListener('change', function() {
        const surveyId = surveySelect.value;
        const populationId = this.value;
        
        if (surveyId && populationId) {
            loadSurveyQuestions(surveyId);
        } else {
            surveyQuestions.style.display = 'none';
            submitBtn.disabled = true;
        }
    });
    
    function loadSurveyQuestions(surveyId) {
        showLoading();
        
        fetch(`/cci-surveys/api/get-questions.php?survey_id=${surveyId}`)
            .then(response => response.json())
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    questionsContainer.innerHTML = '';
                    
                    data.questions.forEach((question, index) => {
                        const questionHtml = `
                            <div class="question-group mb-4">
                                <h6 class="question-title mb-3">
                                    <span class="badge bg-primary me-2">${index + 1}</span>
                                    ${question.question_text}
                                </h6>
                                <div class="options-container">
                                    ${question.options.map(option => `
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="question_${question.id}" 
                                                   id="option_${option.id}" 
                                                   value="${option.id}" 
                                                   required>
                                            <label class="form-check-label" for="option_${option.id}">
                                                ${option.option_text}
                                            </label>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                        
                        questionsContainer.insertAdjacentHTML('beforeend', questionHtml);
                    });
                    
                    surveyQuestions.style.display = 'block';
                    submitBtn.disabled = false;
                } else {
                    showNotification('Error al cargar las preguntas', 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error loading questions:', error);
                showNotification('Error al cargar las preguntas', 'danger');
            });
    }
    
    // Validación del formulario
    const form = document.getElementById('surveyForm');
    form.addEventListener('submit', function(e) {
        if (!validateForm('surveyForm')) {
            e.preventDefault();
            showNotification('Por favor complete todos los campos requeridos', 'warning');
            return;
        }
        
        // Verificar que se hayan respondido todas las preguntas
        const requiredQuestions = questionsContainer.querySelectorAll('input[type="radio"][required]');
        const questionGroups = {};
        
        requiredQuestions.forEach(input => {
            const questionId = input.name.replace('question_', '');
            if (!questionGroups[questionId]) {
                questionGroups[questionId] = false;
            }
            if (input.checked) {
                questionGroups[questionId] = true;
            }
        });
        
        const unansweredQuestions = Object.values(questionGroups).some(answered => !answered);
        
        if (unansweredQuestions) {
            e.preventDefault();
            showNotification('Por favor responda todas las preguntas de la encuesta', 'warning');
            return;
        }
        
        // Confirmar envío
        if (!confirm('¿Está seguro de enviar esta encuesta? Esta acción no se puede deshacer.')) {
            e.preventDefault();
            return;
        }
        
        showLoading();
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
