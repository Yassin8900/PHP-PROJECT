<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Job;

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

function displayValue($value) {
    return $value === null ? '' : htmlspecialchars($value);
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detalls del Treball</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detalls del Treball</h3>
                    <a href="run_jobs.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="border-bottom pb-2">Informació del Treball</h4>
                        <dl class="row">
                            <dt class="col-sm-3">ID:</dt>
                            <dd class="col-sm-9"><?= displayValue($job->getJobId()) ?></dd>

                            <dt class="col-sm-3">Títol:</dt>
                            <dd class="col-sm-9"><?= displayValue($job->getJobTitle()) ?></dd>

                            <dt class="col-sm-3">Salari Mínim:</dt>
                            <dd class="col-sm-9">
                                <?php if ($job->getMinSalary()): ?>
                                    <?= number_format($job->getMinSalary(), 2, ',', '.') ?> €
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-3">Salari Màxim:</dt>
                            <dd class="col-sm-9">
                                <?php if ($job->getMaxSalary()): ?>
                                    <?= number_format($job->getMaxSalary(), 2, ',', '.') ?> €
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-3">Rang Salarial:</dt>
                            <dd class="col-sm-9">
                                <?php if ($job->getMinSalary() && $job->getMaxSalary()): ?>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: 100%;" 
                                             aria-valuenow="100" 
                                             aria-valuemin="<?= $job->getMinSalary() ?>" 
                                             aria-valuemax="<?= $job->getMaxSalary() ?>">
                                            <?= number_format($job->getMinSalary(), 0, ',', '.') ?> € - 
                                            <?= number_format($job->getMaxSalary(), 0, ',', '.') ?> €
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <div class="btn-group">
                        <a href="update_job.php?id=<?= urlencode($job->getJobId()) ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="../../models/Job.php" method="POST" class="d-inline ml-2">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="job_id" value="<?= htmlspecialchars($job->getJobId()) ?>">
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Estàs segur que vols eliminar aquest treball?')">
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