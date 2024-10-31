<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\models\Employee;

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
                            <dd class="col-sm-8"><?= displayValue($employee->employee_id) ?></dd>

                            <dt class="col-sm-4">Nom:</dt>
                            <dd class="col-sm-8"><?= displayValue($employee->first_name) ?></dd>

                            <dt class="col-sm-4">Llinatges:</dt>
                            <dd class="col-sm-8"><?= displayValue($employee->last_name) ?></dd>

                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->email): ?>
                                    <a href="mailto:<?= displayValue($employee->email) ?>">
                                        <?= displayValue($employee->email) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Telèfon:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->phone_number): ?>
                                    <a href="tel:<?= displayValue($employee->phone_number) ?>">
                                        <?= displayValue($employee->phone_number) ?>
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
                            <dd class="col-sm-8"><?= displayValue($employee->hire_date) ?></dd>

                            <dt class="col-sm-4">ID Treball:</dt>
                            <dd class="col-sm-8"><?= displayValue($employee->job_id) ?></dd>

                            <dt class="col-sm-4">Salari:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->salary): ?>
                                    <?= number_format($employee->salary, 2, ',', '.') ?> €
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Comissió:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->commission_pct): ?>
                                    <?= number_format($employee->commission_pct * 100, 2, ',', '.') ?> %
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">ID Manager:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->manager_id): ?>
                                    <a href="?id=<?= displayValue($employee->manager_id) ?>">
                                        <?= displayValue($employee->manager_id) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">ID Departament:</dt>
                            <dd class="col-sm-8">
                                <?php if ($employee->department_id): ?>
                                    <?= displayValue($employee->department_id) ?>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <div class="btn-group">
                        <a href="/src/html/update_employee.php?id=<?= $employee->employee_id ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="../models/Employee.php" method="POST" class="d-inline ml-2">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="employee_id" value="<?= $employee->employee_id ?>">
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