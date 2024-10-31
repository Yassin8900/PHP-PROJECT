<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\models\Employee;
use App\config\Database;


if (!isset($_GET['id'])) {
    header('Location: /index.php?error=' . urlencode('ID de empleado no proporcionado'));
    exit;
}

try {
    $employee = Employee::find($_GET['id']);
    if (!$employee) {
        header('Location: /index.php?error=' . urlencode('Empleado no encontrado'));
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
    <title>Actualitzar Empleat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Actualitzar Empleat</h2>
                    <a href="run_employees.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="/src/models/Employee.php" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="update">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_id">ID Empleat:</label>
                                <input type="number" id="employee_id" name="employee_id" class="form-control" 
                                    value="<?= htmlspecialchars($employee->employee_id) ?>" readonly>
                                <small class="text-muted">Número enter únic (no modificable)</small>
                            </div>
                            <div class="form-group">
                                <label for="first_name">Nom:</label>
                                <input type="text" id="first_name" name="first_name" class="form-control" 
                                    value="<?= htmlspecialchars($employee->first_name) ?>" 
                                    maxlength="20" required>
                                <small class="text-muted">Màxim 20 caràcters</small>
                                <div class="invalid-feedback">
                                    El nombre es requerido (máximo 20 caracteres)
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Llinatge:</label>
                                <input type="text" id="last_name" name="last_name" class="form-control" 
                                    value="<?= htmlspecialchars($employee->last_name) ?>" 
                                    maxlength="25" required>
                                <small class="text-muted">Màxim 25 caràcters</small>
                                <div class="invalid-feedback">
                                    El apellido es requerido (máximo 25 caracteres)
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                    value="<?= htmlspecialchars($employee->email) ?>" 
                                    maxlength="25" required>
                                <small class="text-muted">Format: exemple@domini.com (Màxim 25 caràcters)</small>
                                <div class="invalid-feedback">
                                    Por favor ingrese un email válido (máximo 25 caracteres)
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone_number">Telèfon:</label>
                                <input type="text" id="phone_number" name="phone_number" class="form-control" 
                                    value="<?= htmlspecialchars($employee->phone_number) ?>" 
                                    maxlength="20">
                                <small class="text-muted">Format opcional: +XX.XXXXXXXXX (Màxim 20 caràcters)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hire_date">Data Contractació:</label>
                                <input type="date" id="hire_date" name="hire_date" class="form-control" 
                                    value="<?= htmlspecialchars($employee->hire_date) ?>" required>
                                <small class="text-muted">Format: AAAA-MM-DD</small>
                                <div class="invalid-feedback">
                                    La fecha de contratación es requerida
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="job_id">ID Treball:</label>
                                <input type="text" id="job_id" name="job_id" class="form-control" 
                                    value="<?= htmlspecialchars($employee->job_id) ?>" 
                                    maxlength="10" required>
                                <small class="text-muted">Màxim 10 caràcters</small>
                                <div class="invalid-feedback">
                                    El ID de trabajo es requerido (máximo 10 caracteres)
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="salary">Salari:</label>
                                <input type="number" id="salary" name="salary" step="0.01" min="0.01" 
                                    class="form-control" value="<?= htmlspecialchars($employee->salary) ?>" required>
                                <small class="text-muted">Ha de ser major que 0 (Format: XXXXXX.XX)</small>
                                <div class="invalid-feedback">
                                    El salario debe ser mayor que 0
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="commission_pct">Comissió (%):</label>
                                <input type="number" id="commission_pct" name="commission_pct" 
                                    step="0.01" min="0" max="0.99" class="form-control" 
                                    value="<?= htmlspecialchars($employee->commission_pct) ?>">
                                <small class="text-muted">Valor entre 0 i 0.99 (Format: 0.XX)</small>
                                <div class="invalid-feedback">
                                    La comisión debe estar entre 0 y 0.99
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="manager_id">ID Gerent:</label>
                                <input type="number" id="manager_id" name="manager_id" class="form-control"
                                    value="<?= htmlspecialchars($employee->manager_id) ?>">
                                <small class="text-muted">ID numèric del gerent (opcional)</small>
                            </div>
                            <div class="form-group">
                                <label for="department_id">ID Departament:</label>
                                <input type="number" id="department_id" name="department_id" class="form-control"
                                    value="<?= htmlspecialchars($employee->department_id) ?>">
                                <small class="text-muted">ID numèric del departament (opcional)</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualitzar Empleat
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