<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Region;

if (!isset($_GET['id'])) {
    header('Location: run_regions.php?error=' . urlencode('ID de regió no proporcionat'));
    exit;
}

try {
    $region = Region::find($_GET['id']);
    if (!$region) {
        header('Location: /index.php?error=' . urlencode('Regió no trobada'));
        exit;
    }
} catch (Exception $e) {
    header('Location: /index.php?error=' . urlencode($e->getMessage()));
    exit;
}

function displayValue($value) {
    return $value === null ? '<span class="text-muted">No disponible</span>' : htmlspecialchars($value);
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detalls de la Regió</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detalls de la Regió</h3>
                    <a href="run_regions.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <dl class="row">
                            <dt class="col-sm-4">ID:</dt>
                            <dd class="col-sm-8"><?= displayValue($region->getRegionId()) ?></dd>

                            <dt class="col-sm-4">Nom:</dt>
                            <dd class="col-sm-8"><?= displayValue($region->getRegionName()) ?></dd>
                        </dl>

                        <div class="text-center mt-4">
                            <div class="btn-group">
                                <a href="/src/html/update_region.php?id=<?= $region->getRegionId() ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="../../models/Region.php" method="POST" class="d-inline ml-2">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="region_id" value="<?= $region->getRegionId() ?>">
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('Estàs segur que vols eliminar aquesta regió?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
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