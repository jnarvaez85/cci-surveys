<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Survey.php';

$auth = new Auth();
$auth->requireAdmin();

$currentUser = $auth->getCurrentUser();
$pageTitle = 'Reportes y Estadísticas';
$currentPage = 'reports';

$survey = new Survey();
$db = Database::getInstance()->getConnection();

// Obtener encuestas disponibles
$stmt = $db->prepare("SELECT id, name FROM surveys WHERE is_active = 1 ORDER BY name");
$stmt->execute();
$surveys = $stmt->fetchAll();

// Obtener poblaciones disponibles
$stmt = $db->prepare("SELECT id, name FROM populations WHERE is_active = 1 ORDER BY name");
$stmt->execute();
$populations = $stmt->fetchAll();

// Parámetros de filtro
$selectedSurvey = $_GET['survey_id'] ?? '';
$selectedPopulation = $_GET['population_id'] ?? '';
$selectedAgeGroup = $_GET['age_group'] ?? '';
$selectedGender = $_GET['gender'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

// Construir consulta base
$whereConditions = ['sr.id IS NOT NULL'];
$params = [];

if ($selectedSurvey) {
    $whereConditions[] = 'sr.survey_id = :survey_id';
    $params[':survey_id'] = $selectedSurvey;
}

if ($selectedPopulation) {
    $whereConditions[] = 'sr.population_id = :population_id';
    $params[':population_id'] = $selectedPopulation;
}

if ($selectedAgeGroup) {
    $whereConditions[] = 'sr.respondent_age_group = :age_group';
    $params[':age_group'] = $selectedAgeGroup;
}

if ($selectedGender) {
    $whereConditions[] = 'sr.respondent_gender = :gender';
    $params[':gender'] = $selectedGender;
}

if ($dateFrom) {
    $whereConditions[] = 'DATE(sr.submission_date) >= :date_from';
    $params[':date_from'] = $dateFrom;
}

if ($dateTo) {
    $whereConditions[] = 'DATE(sr.submission_date) <= :date_to';
    $params[':date_to'] = $dateTo;
}

$whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

// Obtener estadísticas generales
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total_responses,
        COUNT(DISTINCT sr.population_id) as populations_count,
        COUNT(DISTINCT sr.surveyor_id) as surveyors_count,
        COUNT(DISTINCT sr.survey_id) as surveys_count
    FROM survey_responses sr
    {$whereClause}
");

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$generalStats = $stmt->fetch();

// Obtener estadísticas por población
$stmt = $db->prepare("
    SELECT p.name, COUNT(sr.id) as count
    FROM populations p
    LEFT JOIN survey_responses sr ON p.id = sr.population_id {$whereClause.replace('sr.id IS NOT NULL', '1=1')}
    WHERE p.is_active = 1
    GROUP BY p.id, p.name
    ORDER BY count DESC
");

$stmt->execute();
$populationStats = $stmt->fetchAll();

// Obtener estadísticas por edad
$stmt = $db->prepare("
    SELECT respondent_age_group, COUNT(*) as count
    FROM survey_responses sr
    {$whereClause}
    GROUP BY respondent_age_group
    ORDER BY count DESC
");

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$ageStats = $stmt->fetchAll();

// Obtener estadísticas por género
$stmt = $db->prepare("
    SELECT respondent_gender, COUNT(*) as count
    FROM survey_responses sr
    {$whereClause}
    GROUP BY respondent_gender
    ORDER BY count DESC
");

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$genderStats = $stmt->fetchAll();

// Obtener respuestas por pregunta (si hay encuesta seleccionada)
$questionStats = [];
if ($selectedSurvey) {
    $questionStats = $survey->getQuestionResponses($selectedSurvey);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-3">
                        <i class="bi bi-bar-chart me-2"></i>
                        Reportes y Estadísticas
                    </h2>
                    <p class="text-muted mb-0">Análisis detallado de las encuestas y respuestas</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>
                        Filtros de Búsqueda
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-3">
                            <label for="survey_id" class="form-label">Encuesta</label>
                            <select class="form-select" id="survey_id" name="survey_id">
                                <option value="">Todas las encuestas</option>
                                <?php foreach ($surveys as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= $selectedSurvey == $s['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="population_id" class="form-label">Población</label>
                            <select class="form-select" id="population_id" name="population_id">
                                <option value="">Todas las poblaciones</option>
                                <?php foreach ($populations as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $selectedPopulation == $p['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="age_group" class="form-label">Grupo de Edad</label>
                            <select class="form-select" id="age_group" name="age_group">
                                <option value="">Todos</option>
                                <option value="joven" <?= $selectedAgeGroup == 'joven' ? 'selected' : '' ?>>Joven</option>
                                <option value="adulto" <?= $selectedAgeGroup == 'adulto' ? 'selected' : '' ?>>Adulto</option>
                                <option value="adulto mayor" <?= $selectedAgeGroup == 'adulto mayor' ? 'selected' : '' ?>>Adulto Mayor</option>
                                <option value="otro" <?= $selectedAgeGroup == 'otro' ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="gender" class="form-label">Género</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Todos</option>
                                <option value="Mujer" <?= $selectedGender == 'Mujer' ? 'selected' : '' ?>>Mujer</option>
                                <option value="Hombre" <?= $selectedGender == 'Hombre' ? 'selected' : '' ?>>Hombre</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Desde</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $dateFrom ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Hasta</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $dateTo ?>">
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>
                                Aplicar Filtros
                            </button>
                            <a href="reports.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Limpiar Filtros
                            </a>
                            <button type="button" class="btn btn-success" onclick="exportReport()">
                                <i class="bi bi-download me-2"></i>
                                Exportar Reporte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-primary bg-gradient rounded-circle p-3">
                            <i class="bi bi-clipboard-data text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-primary mb-1"><?= number_format($generalStats['total_responses']) ?></h3>
                    <p class="text-muted mb-0">Total Respuestas</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-success bg-gradient rounded-circle p-3">
                            <i class="bi bi-geo-alt text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-success mb-1"><?= number_format($generalStats['populations_count']) ?></h3>
                    <p class="text-muted mb-0">Poblaciones</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-info bg-gradient rounded-circle p-3">
                            <i class="bi bi-people text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-info mb-1"><?= number_format($generalStats['surveyors_count']) ?></h3>
                    <p class="text-muted mb-0">Encuestadores</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-warning bg-gradient rounded-circle p-3">
                            <i class="bi bi-clipboard text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-warning mb-1"><?= number_format($generalStats['surveys_count']) ?></h3>
                    <p class="text-muted mb-0">Encuestas</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>
                        Respuestas por Población
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="populationsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribución por Edad
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="ageChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-gender-ambiguous me-2"></i>
                        Distribución por Género
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="genderChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <?php if (!empty($questionStats)): ?>
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        Análisis por Preguntas
                    </h5>
                </div>
                <div class="card-body">
                    <div id="questionsAnalysis">
                        <!-- Se llenará con JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Tabla de Resumen -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-table me-2"></i>
                        Resumen Detallado
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Población</th>
                                    <th>Joven</th>
                                    <th>Adulto</th>
                                    <th>Adulto Mayor</th>
                                    <th>Otro</th>
                                    <th>Mujer</th>
                                    <th>Hombre</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stmt = $db->prepare("
                                    SELECT 
                                        p.name,
                                        SUM(CASE WHEN sr.respondent_age_group = 'joven' THEN 1 ELSE 0 END) as joven,
                                        SUM(CASE WHEN sr.respondent_age_group = 'adulto' THEN 1 ELSE 0 END) as adulto,
                                        SUM(CASE WHEN sr.respondent_age_group = 'adulto mayor' THEN 1 ELSE 0 END) as adulto_mayor,
                                        SUM(CASE WHEN sr.respondent_age_group = 'otro' THEN 1 ELSE 0 END) as otro,
                                        SUM(CASE WHEN sr.respondent_gender = 'Mujer' THEN 1 ELSE 0 END) as mujer,
                                        SUM(CASE WHEN sr.respondent_gender = 'Hombre' THEN 1 ELSE 0 END) as hombre,
                                        COUNT(sr.id) as total
                                    FROM populations p
                                    LEFT JOIN survey_responses sr ON p.id = sr.population_id {$whereClause.replace('sr.id IS NOT NULL', '1=1')}
                                    WHERE p.is_active = 1
                                    GROUP BY p.id, p.name
                                    ORDER BY total DESC
                                ");
                                $stmt->execute();
                                $summaryData = $stmt->fetchAll();
                                
                                foreach ($summaryData as $row):
                                ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                                        <td><span class="badge bg-info"><?= $row['joven'] ?></span></td>
                                        <td><span class="badge bg-primary"><?= $row['adulto'] ?></span></td>
                                        <td><span class="badge bg-success"><?= $row['adulto_mayor'] ?></span></td>
                                        <td><span class="badge bg-warning"><?= $row['otro'] ?></span></td>
                                        <td><span class="badge bg-pink"><?= $row['mujer'] ?></span></td>
                                        <td><span class="badge bg-blue"><?= $row['hombre'] ?></span></td>
                                        <td><strong><?= $row['total'] ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para los gráficos
    const populationsData = <?= json_encode($populationStats) ?>;
    const ageData = <?= json_encode($ageStats) ?>;
    const genderData = <?= json_encode($genderStats) ?>;
    const questionData = <?= json_encode($questionStats) ?>;
    
    // Gráfico de poblaciones
    const populationsCtx = document.getElementById('populationsChart').getContext('2d');
    new Chart(populationsCtx, {
        type: 'bar',
        data: {
            labels: populationsData.map(p => p.name),
            datasets: [{
                label: 'Respuestas',
                data: populationsData.map(p => p.count),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Gráfico de edad
    const ageCtx = document.getElementById('ageChart').getContext('2d');
    new Chart(ageCtx, {
        type: 'doughnut',
        data: {
            labels: ageData.map(a => a.respondent_age_group.charAt(0).toUpperCase() + a.respondent_age_group.slice(1)),
            datasets: [{
                data: ageData.map(a => a.count),
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(118, 75, 162, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
                ],
                borderColor: [
                    'rgba(102, 126, 234, 1)',
                    'rgba(118, 75, 162, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Gráfico de género
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'pie',
        data: {
            labels: genderData.map(g => g.respondent_gender),
            datasets: [{
                data: genderData.map(g => g.count),
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(118, 75, 162, 0.8)'
                ],
                borderColor: [
                    'rgba(102, 126, 234, 1)',
                    'rgba(118, 75, 162, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Análisis de preguntas
    if (questionData.length > 0) {
        displayQuestionsAnalysis(questionData);
    }
});

function displayQuestionsAnalysis(data) {
    const container = document.getElementById('questionsAnalysis');
    let html = '';
    
    // Agrupar por pregunta
    const questions = {};
    data.forEach(item => {
        if (!questions[item.question_text]) {
            questions[item.question_text] = [];
        }
        questions[item.question_text].push(item);
    });
    
    Object.keys(questions).forEach((questionText, index) => {
        if (index < 3) { // Mostrar solo las primeras 3 preguntas
            html += `
                <div class="mb-4">
                    <h6 class="text-primary">${questionText}</h6>
                    <div class="row">
            `;
            
            questions[questionText].forEach(option => {
                html += `
                    <div class="col-6 mb-2">
                        <div class="d-flex justify-content-between">
                            <small>${option.option_text}</small>
                            <small class="fw-bold">${option.count} (${option.percentage}%)</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" style="width: ${option.percentage}%"></div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }
    });
    
    container.innerHTML = html;
}

function exportReport() {
    showNotification('Funcionalidad de exportación en desarrollo', 'info');
}
</script>

<style>
.bg-pink {
    background-color: #e83e8c !important;
}
.bg-blue {
    background-color: #007bff !important;
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
