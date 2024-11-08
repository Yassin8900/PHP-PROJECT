<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

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
                <form method="POST" action="../../models/Employee.php" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="update">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_id">ID Empleat:</label>
                                <input type="number" id="employee_id" name="employee_id" class="form-control" 
                                    value="<?= htmlspecialchars($employee->getEmployeeId()) ?>" readonly>
                                <small class="text-muted">Número enter únic (no modificable)</small>
                            </div>
                            <div class="form-group">
                                <label for="first_name">Nom:</label>
                                <input type="text" id="first_name" name="first_name" class="form-control" 
                                    value="<?= htmlspecialchars($employee->getFirstName()) ?>" 
                                    maxlength="20" required>
                                <small class="text-muted">Màxim 20 caràcters</small>
                                <div class="invalid-feedback">
                                    El nombre es requerido (máximo 20 caracteres)
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Llinatge:</label>
                                <input type="text" id="last_name" name="last_name" class="form-control" 
                                    value="<?= htmlspecialchars($employee->getLastName()) ?>" 
                                    maxlength="25" required>
                                <small class="text-muted">Màxim 25 caràcters</small>
                                <div class="invalid-feedback">
                                    El apellido es requerido (máximo 25 caracteres)
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                    value="<?= htmlspecialchars($employee->getEmail()) ?>" 
                                    maxlength="25" required>
                                <small class="text-muted">Format: exemple@domini.com (Màxim 25 caràcters)</small>
                                <div class="invalid-feedback">
                                    Por favor ingrese un email válido (máximo 25 caracteres)
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone_number">Telèfon:</label>
                                <input type="text" id="phone_number" name="phone_number" class="form-control" 
                                    value="<?= htmlspecialchars($employee->getPhoneNumber()) ?>" 
                                    maxlength="20">
                                <small class="text-muted">Format opcional: +XX.XXXXXXXXX (Màxim 20 caràcters)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hire_date">Data Contractació:</label>
                                <input type="date" id="hire_date" name="hire_date" class="form-control" 
                                    value="<?= htmlspecialchars($employee->getHireDate()) ?>" required>
                                <small class="text-muted">Format: AAAA-MM-DD</small>
                                <div class="invalid-feedback">
                                    La fecha de contratación es requerida
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="job_id">Treball:</label>
                                <select id="job_id" name="job_id" class="form-control" required>
                                    <option value="">Selecciona un treball</option>
                                    <?php
                                    try {
                                        $db = new Database();
                                        $conn = $db->getConnection();
                                        $jobs = $conn->query("SELECT job_id, job_title FROM jobs ORDER BY job_title");
                                        while ($job = $jobs->fetch_assoc()) {
                                            $selected = ($job['job_id'] === $employee->getJobId()) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($job['job_id']) . "' $selected>" . 
                                                 htmlspecialchars($job['job_title']) . "</option>";
                                        }
                                    } catch (Exception $e) {
                                        echo "<option value=''>Error cargando trabajos</option>";
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Has de seleccionar un treball
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="salary">Salari:</label>
                                <input type="number" id="salary" name="salary" step="0.01" min="0.01" 
                                    class="form-control" value="<?= htmlspecialchars($employee->getSalary()) ?>" required>
                                <small class="text-muted">Ha de ser major que 0 (Format: XXXXXX.XX)</small>
                                <div class="invalid-feedback">
                                    El salario debe ser mayor que 0
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="commission_pct">Comissió (%):</label>
                                <input type="number" id="commission_pct" name="commission_pct" 
                                    step="0.01" min="0" max="0.99" class="form-control" 
                                    value="<?= htmlspecialchars($employee->getCommissionPct()) ?>">
                                <small class="text-muted">Valor entre 0 i 0.99 (Format: 0.XX)</small>
                                <div class="invalid-feedback">
                                    La comisión debe estar entre 0 y 0.99
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="manager_id">Gerent:</label>
                                <select id="manager_id" name="manager_id" class="form-control">
                                    <option value="">Selecciona un gerent</option>
                                    <?php
                                    try {
                                        $managers = $conn->query("SELECT employee_id, CONCAT(first_name, ' ', last_name) as full_name 
                                                            FROM employees WHERE employee_id != " . $employee->getEmployeeId() . 
                                                            " ORDER BY first_name, last_name");
                                        while ($manager = $managers->fetch_assoc()) {
                                            $selected = ($manager['employee_id'] == $employee->getManagerId()) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($manager['employee_id']) . "' $selected>" . 
                                                 htmlspecialchars($manager['full_name']) . "</option>";
                                        }
                                    } catch (Exception $e) {
                                        echo "<option value=''>Error cargando gerentes</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="department_id">Departament:</label>
                                <select id="department_id" name="department_id" class="form-control">
                                    <option value="">Selecciona un departament</option>
                                    <?php
                                    try {
                                        $departments = $conn->query("SELECT department_id, department_name FROM departments ORDER BY department_name");
                                        while ($dept = $departments->fetch_assoc()) {
                                            $selected = ($dept['department_id'] == $employee->getDepartmentId()) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($dept['department_id']) . "' $selected>" . 
                                                 htmlspecialchars($dept['department_name']) . "</option>";
                                        }
                                    } catch (Exception $e) {
                                        echo "<option value=''>Error cargando departamentos</option>";
                                    }
                                    ?>
                                </select>
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