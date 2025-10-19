<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Survey.php';

$auth = new Auth();
$auth->requireAdmin();

$currentUser = $auth->getCurrentUser();
$pageTitle = 'Dashboard Administrativo';
$currentPage = 'dashboard';

$survey = new Survey();
$db = Database::getInstance()->getConnection();

// Obtener estadísticas generales
$stats = [];

// Total de encuestas
$stmt = $db->prepare("SELECT COUNT(*) as total FROM surveys WHERE is_active = 1");
$stmt->execute();
$stats['total_surveys'] = $stmt->fetch()['total'];

// Total de respuestas
$stmt = $db->prepare("SELECT COUNT(*) as total FROM survey_responses");
$stmt->execute();
$stats['total_responses'] = $stmt->fetch()['total'];

// Total de usuarios activos
$stmt = $db->prepare("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
$stmt->execute();
$stats['total_users'] = $stmt->fetch()['total'];

// Total de poblaciones
$stmt = $db->prepare("SELECT COUNT(*) as total FROM populations WHERE is_active = 1");
$stmt->execute();
$stats['total_populations'] = $stmt->fetch()['total'];

// Respuestas de hoy
$stmt = $db->prepare("SELECT COUNT(*) as total FROM survey_responses WHERE DATE(submission_date) = CURDATE()");
$stmt->execute();
$stats['responses_today'] = $stmt->fetch()['total'];

// Respuestas de esta semana
$stmt = $db->prepare("SELECT COUNT(*) as total FROM survey_responses WHERE WEEK(submission_date) = WEEK(NOW())");
$stmt->execute();
$stats['responses_week'] = $stmt->fetch()['total'];

// Top poblaciones
$stmt = $db->prepare("
    SELECT p.name, COUNT(sr.id) as count
    FROM populations p
    LEFT JOIN survey_responses sr ON p.id = sr.population_id
    WHERE p.is_active = 1
    GROUP BY p.id, p.name
    ORDER BY count DESC
    LIMIT 5
");
$stmt->execute();
$topPopulations = $stmt->fetchAll();

// Respuestas recientes
$stmt = $db->prepare("
    SELECT 
        sr.id,
        sr.respondent_first_name,
        sr.respondent_last_name,
        sr.submission_date,
        s.name as survey_name,
        p.name as population_name,
        u.first_name as surveyor_first_name,
        u.last_name as surveyor_last_name
    FROM survey_responses sr
    JOIN surveys s ON sr.survey_id = s.id
    JOIN populations p ON sr.population_id = p.id
    JOIN users u ON sr.surveyor_id = u.id
    ORDER BY sr.submission_date DESC
    LIMIT 10
");
$stmt->execute();
$recentResponses = $stmt->fetchAll();

// Estadísticas por edad
$stmt = $db->prepare("
    SELECT respondent_age_group, COUNT(*) as count
    FROM survey_responses
    GROUP BY respondent_age_group
    ORDER BY count DESC
");
$stmt->execute();
$ageStats = $stmt->fetchAll();

// Estadísticas por género
$stmt = $db->prepare("
    SELECT respondent_gender, COUNT(*) as count
    FROM survey_responses
    GROUP BY respondent_gender
    ORDER BY count DESC
");
$stmt->execute();
$genderStats = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-4">
                    <h2 class="mb-3">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard Administrativo
                    </h2>
                    <p class="text-muted mb-0">
                        Bienvenido, <?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-primary bg-gradient rounded-circle p-3">
                            <i class="bi bi-clipboard-data text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-primary mb-1"><?= number_format($stats['total_responses']) ?></h3>
                    <p class="text-muted mb-0">Total Respuestas</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-success bg-gradient rounded-circle p-3">
                            <i class="bi bi-calendar-check text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-success mb-1"><?= number_format($stats['responses_today']) ?></h3>
                    <p class="text-muted mb-0">Hoy</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-info bg-gradient rounded-circle p-3">
                            <i class="bi bi-calendar-week text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-info mb-1"><?= number_format($stats['responses_week']) ?></h3>
                    <p class="text-muted mb-0">Esta Semana</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-warning bg-gradient rounded-circle p-3">
                            <i class="bi bi-people text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-warning mb-1"><?= number_format($stats['total_users']) ?></h3>
                    <p class="text-muted mb-0">Usuarios Activos</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
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
    
    <!-- Additional Stats Row -->
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
        
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy me-2"></i>
                        Top Poblaciones
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($topPopulations)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">No hay datos disponibles</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($topPopulations as $index => $population): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <span class="text-white fw-bold"><?= $index + 1 ?></span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($population['name']) ?></h6>
                                            <small class="text-muted"><?= number_format($population['count']) ?> respuestas</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="progress" style="width: 100px; height: 8px;">
                                                <div class="progress-bar bg-primary" 
                                                     style="width: <?= $population['count'] > 0 ? ($population['count'] / $topPopulations[0]['count']) * 100 : 0 ?>%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Responses -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Respuestas Recientes
                    </h5>
                    <a href="/cci-surveys/admin/responses.php" class="btn btn-sm btn-outline-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentResponses)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No hay respuestas recientes</h5>
                            <p class="text-muted">Las respuestas aparecerán aquí cuando se envíen encuestas</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Encuestado</th>
                                        <th>Encuestador</th>
                                        <th>Población</th>
                                        <th>Encuesta</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentResponses as $response): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 35px; height: 35px;">
                                                        <span class="text-white fw-bold">
                                                            <?= strtoupper(substr($response['respondent_first_name'], 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">
                                                            <?= htmlspecialchars($response['respondent_first_name'] . ' ' . $response['respondent_last_name']) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 150px;">
                                                    <?= htmlspecialchars($response['surveyor_first_name'] . ' ' . $response['surveyor_last_name']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($response['population_name']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;">
                                                    <?= htmlspecialchars($response['survey_name']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($response['submission_date'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewResponse(<?= $response['id'] ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Populations Chart
    const populationsCtx = document.getElementById('populationsChart').getContext('2d');
    const populationsData = <?= json_encode($topPopulations) ?>;
    
    new Chart(populationsCtx, {
        type: 'bar',
        data: {
            labels: populationsData.map(p => p.name),
            datasets: [{
                label: 'Respuestas',
                data: populationsData.map(p => p.count),
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(118, 75, 162, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(102, 126, 234, 1)',
                    'rgba(118, 75, 162, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
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
    
    // Age Chart
    const ageCtx = document.getElementById('ageChart').getContext('2d');
    const ageData = <?= json_encode($ageStats) ?>;
    
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
    
    // Gender Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    const genderData = <?= json_encode($genderStats) ?>;
    
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
});

function viewResponse(responseId) {
    showLoading();
    
    fetch(`/cci-surveys/api/get-response-details.php?id=${responseId}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                document.getElementById('responseDetails').innerHTML = data.html;
                const modal = new bootstrap.Modal(document.getElementById('responseModal'));
                modal.show();
            } else {
                showNotification('Error al cargar los detalles', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showNotification('Error al cargar los detalles', 'danger');
        });
}
</script>

<!-- Response Details Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clipboard-data me-2"></i>
                    Detalles de la Encuesta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="responseDetails">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
