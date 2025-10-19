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
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

$survey = new Survey();
$totalResponses = $survey->countResponsesBySurveyor($currentUser['id']);
$recentResponses = $survey->getResponsesBySurveyor($currentUser['id'], 5);

include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-4">
                    <h2 class="mb-3">
                        <i class="bi bi-person-circle me-2"></i>
                        ¡Bienvenido, <?= htmlspecialchars($currentUser['first_name']) ?>!
                    </h2>
                    <p class="text-muted mb-0">
                        Sistema de Encuestas CCI - Panel de Encuestador
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-primary bg-gradient rounded-circle p-3">
                            <i class="bi bi-clipboard-check text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-primary mb-1"><?= number_format($totalResponses) ?></h3>
                    <p class="text-muted mb-0">Total Encuestas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-success bg-gradient rounded-circle p-3">
                            <i class="bi bi-calendar-check text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-success mb-1"><?= date('j') ?></h3>
                    <p class="text-muted mb-0">Hoy</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-info bg-gradient rounded-circle p-3">
                            <i class="bi bi-graph-up text-white fs-4"></i>
                        </div>
                    </div>
                    <h3 class="text-info mb-1"><?= date('W') ?></h3>
                    <p class="text-muted mb-0">Semana <?= date('W') ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="/cci-surveys/surveyor/survey.php" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-clipboard-plus me-2"></i>
                                Nueva Encuesta
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="/cci-surveys/surveyor/my-responses.php" class="btn btn-outline-primary btn-lg w-100">
                                <i class="bi bi-list-ul me-2"></i>
                                Mis Encuestas
                            </a>
                        </div>
                    </div>
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
                        Encuestas Recientes
                    </h5>
                    <a href="/cci-surveys/surveyor/my-responses.php" class="btn btn-sm btn-outline-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentResponses)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No hay encuestas recientes</h5>
                            <p class="text-muted">Comience creando su primera encuesta</p>
                            <a href="/cci-surveys/surveyor/survey.php" class="btn btn-primary">
                                <i class="bi bi-clipboard-plus me-2"></i>
                                Crear Encuesta
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Encuestado</th>
                                        <th>Población</th>
                                        <th>Encuesta</th>
                                        <th>Edad</th>
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
                                                        <small class="text-muted">
                                                            <?= htmlspecialchars($response['respondent_gender']) ?>
                                                        </small>
                                                    </div>
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
                                                <span class="badge bg-info">
                                                    <?= ucfirst($response['respondent_age_group']) ?>
                                                </span>
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

<script>
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

// Auto-refresh stats every 5 minutes
setInterval(function() {
    // You can add auto-refresh functionality here if needed
}, 300000);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
