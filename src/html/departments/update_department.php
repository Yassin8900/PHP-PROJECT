<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Department;
use App\config\Database;

if (!isset($_GET['id'])) {
    header('Location: run_departments.php?error=' . urlencode('ID de departament no proporcionat'));
    exit;
}

try {
    $department = Department::find($_GET['id']);
    if (!$department) {
        header('Location: run_departments.php?error=' . urlencode('Departament no trobat'));
        exit;
    }
} catch (Exception $e) {
    header('Location: run_departments.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Actualitzar Departament</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Actualitzar Departament</h2>
                    <a href="run_departments.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="../../models/Department.php" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="update">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department_id">ID Departament:</label>
                                <input type="number" id="department_id" name="department_id" class="form-control" 
                                    value="<?= htmlspecialchars($department->getDepartmentId()) ?>" readonly>
                                <small class="text-muted">ID no modificable</small>
                            </div>

                            <div class="form-group">
                                <label for="department_name">Nom del Departament:</label>
                                <input type="text" id="department_name" name="department_name" 
                                    class="form-control" maxlength="30" required
                                    value="<?= htmlspecialchars($department->getDepartmentName()) ?>">
                                <small class="text-muted">Màxim 30 caràcters</small>
                                <div class="invalid-feedback">
                                    El nom del departament és obligatori (màxim 30 caràcters)
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="manager_id">ID del Gerent:</label>
                                <input type="number" id="manager_id" name="manager_id" class="form-control"
                                    value="<?= htmlspecialchars($department->getManagerId()) ?>">
                                <small class="text-muted">ID numèric del gerent (opcional)</small>
                            </div>

                            <div class="form-group">
                                <label for="location_id">ID de Localització:</label>
                                <input type="number" id="location_id" name="location_id" class="form-control"
                                    value="<?= htmlspecialchars($department->getLocationId()) ?>">
                                <small class="text-muted">ID numèric de la localització (opcional)</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualitzar Departament
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
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
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