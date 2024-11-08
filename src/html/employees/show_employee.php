<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Employee;
use App\config\Database;

if (!isset($_GET['id'])) {
    header('Location: /index.php?error=' . urlencode('ID de empleat no proporcionat'));
    exit;
}

try {
    $employee = Employee::find($_GET['id']);
    if (!$employee) {
        header('Location: /index.php?error=' . urlencode('Empleat no trobat'));
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Obtener información del trabajo
    $jobId = $employee->getJobId();
    $jobStmt = $conn->prepare("SELECT job_title FROM jobs WHERE job_id = ?");
    $jobStmt->bind_param("s", $jobId);
    $jobStmt->execute();
    $jobResult = $jobStmt->get_result();
    $jobTitle = $jobResult->fetch_assoc()['job_title'] ?? 'No disponible';

    // Obtener información del departamento
    $deptName = 'No disponible';
    $departmentId = $employee->getDepartmentId();
    if ($departmentId) {
        $deptStmt = $conn->prepare("SELECT department_name FROM departments WHERE department_id = ?");
        $deptStmt->bind_param("i", $departmentId);
        $deptStmt->execute();
        $deptResult = $deptStmt->get_result();
        $deptName = $deptResult->fetch_assoc()['department_name'] ?? 'No disponible';
    }

    // Obtener información del manager
    $managerName = 'No disponible';
    $managerId = $employee->getManagerId();
    if ($managerId) {
        $mgrStmt = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) as manager_name 
                                  FROM employees WHERE employee_id = ?");
        $mgrStmt->bind_param("i", $managerId);
        $mgrStmt->execute();
        $mgrResult = $mgrStmt->get_result();
        $managerName = $mgrResult->fetch_assoc()['manager_name'] ?? 'No disponible';
    }
} catch (Exception $e) {
    header('Location: /index.php?error=' . urlencode($e->getMessage()));
    exit;
}


function displayValue($value) {
    return $value === null ? '' : htmlspecialchars($value);
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detalls de l'Empleat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detalls de l'Empleat</h3>
                    <a href="run_employees.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="border-bottom pb-2">Informació Personal</h4>
                        <dl class="row">
                            <dt class="col-sm-4">ID:</dt>
                            <dd class="col-sm-8"><?= displayValue($employee->getEmployeeId()) ?></dd>

                            <dt class="col-sm-4">Nom:</dt>
                            <dd class="col-sm-8"><?= displayValue($employee->getFirstName()) ?></dd>

                            <dt class="col-sm-4">Llinatges:</dt>
                            <dd class="col-sm-8"><?= displayValue($employee->getLastName()) ?></dd>

                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->getEmail()): ?>
                                    <a href="mailto:<?= displayValue($employee->getEmail()) ?>">
                                        <?= displayValue($employee->getEmail()) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Telèfon:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->getPhoneNumber()): ?>
                                    <a href="tel:<?= displayValue($employee->getPhoneNumber()) ?>">
                                        <?= displayValue($employee->getPhoneNumber()) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>

                    <div class="col-md-6">
                        <h4 class="border-bottom pb-2">Informació Laboral</h4>
                        <dl class="row">
                            <dt class="col-sm-4">Data Contracte:</dt>
                            <dd class="col-sm-8"><?= displayValue($employee->getHireDate()) ?></dd>

                            <dt class="col-sm-4">Treball:</dt>
                            <dd class="col-sm-8">
                                <?= htmlspecialchars($jobTitle) ?>
                                <small class="text-muted d-block">ID: <?= displayValue($employee->getJobId()) ?></small>
                            </dd>

                            <dt class="col-sm-4">Salari:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->getSalary()): ?>
                                    <?= number_format($employee->getSalary(), 2, ',', '.') ?> €
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Comissió:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->getCommissionPct()): ?>
                                    <?= number_format($employee->getCommissionPct() * 100, 2, ',', '.') ?> %
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Gerent:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->getManagerId()): ?>
                                    <a href="?id=<?= displayValue($employee->getManagerId()) ?>">
                                        <?= htmlspecialchars($managerName) ?>
                                    </a>
                                    <small class="text-muted d-block">ID: <?= displayValue($employee->getManagerId()) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Departament:</dt>
                            <dd class="col-sm-8">
                                <?= htmlspecialchars($deptName) ?>
                                <?php if ($employee->getDepartmentId()): ?>
                                    <small class="text-muted d-block">ID: <?= displayValue($employee->getDepartmentId()) ?></small>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <div class="btn-group">
                        <a href="update_employee.php?id=<?= $employee->getEmployeeId() ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="../../models/Employee.php" method="POST" class="d-inline ml-2">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="employee_id" value="<?= $employee->getEmployeeId() ?>">
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Estàs segur que vols eliminar aquest empleat?')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 