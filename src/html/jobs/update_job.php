<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Job;
use App\config\Database;

if (!isset($_GET['id'])) {
    header('Location: /index.php?error=' . urlencode('ID de treball no proporcionat'));
    exit;
}

try {
    $job = Job::find($_GET['id']);
    if (!$job) {
        header('Location: /index.php?error=' . urlencode('Treball no trobat'));
        exit;
    }
} catch (Exception $e) {
    header('Location: /index.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Actualitzar Treball</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Actualitzar Treball</h2>
                    <a href="run_jobs.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="../../models/Job.php" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="update">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="job_id">ID Treball:</label>
                                <input type="text" id="job_id" name="job_id" class="form-control" 
                                    value="<?= htmlspecialchars($job->getJobId()) ?>" readonly>
                                <small class="text-muted">Codi únic del treball (no modificable)</small>
                            </div>
                            <div class="form-group">
                                <label for="job_title">Títol del Treball:</label>
                                <input type="text" id="job_title" name="job_title" class="form-control" 
                                    value="<?= htmlspecialchars($job->getJobTitle()) ?>" 
                                    maxlength="35" required>
                                <small class="text-muted">Màxim 35 caràcters</small>
                                <div class="invalid-feedback">
                                    El títol del treball és obligatori (màxim 35 caràcters)
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_salary">Salari Mínim:</label>
                                <input type="number" id="min_salary" name="min_salary" step="1" min="0" 
                                    class="form-control" value="<?= htmlspecialchars($job->getMinSalary()) ?>">
                                <small class="text-muted">Valor mínim: 0</small>
                                <div class="invalid-feedback">
                                    El salari mínim ha de ser major o igual a 0
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="max_salary">Salari Màxim:</label>
                                <input type="number" id="max_salary" name="max_salary" step="1" min="0" 
                                    class="form-control" value="<?= htmlspecialchars($job->getMaxSalary()) ?>">
                                <small class="text-muted">Ha de ser major que el salari mínim</small>
                                <div class="invalid-feedback">
                                    El salari màxim ha de ser major que el salari mínim
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualitzar Treball
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    // Validación personalizada para salarios
                    var minSalary = document.getElementById('min_salary').value;
                    var maxSalary = document.getElementById('max_salary').value;
                    
                    if (Number(maxSalary) <= Number(minSalary)) {
                        event.preventDefault();
                        event.stopPropagation();
                        document.getElementById('max_salary').setCustomValidity(
                            'El salari màxim ha de ser major que el salari mínim'
                        );
                    } else {
                        document.getElementById('max_salary').setCustomValidity('');
                    }

                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
    </script>
</body>
</html> 