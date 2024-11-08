<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Country;

if (!isset($_GET['id'])) {
    header('Location: run_countries.php?error=' . urlencode('ID de país no proporcionat'));
    exit;
}

try {
    $country = Country::find($_GET['id']);
    if (!$country) {
        header('Location: run_countries.php?error=' . urlencode('País no trobat'));
        exit;
    }
} catch (Exception $e) {
    header('Location: run_countries.php?error=' . urlencode($e->getMessage()));
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
    <title>Detalls del País</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detalls del País</h3>
                    <a href="run_countries.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="border-bottom pb-2">Informació del País</h4>
                        <dl class="row">
                            <dt class="col-sm-4">ID País:</dt>
                            <dd class="col-sm-8"><?= displayValue($country->getCountryId()) ?></dd>

                            <dt class="col-sm-4">Nom del País:</dt>
                            <dd class="col-sm-8"><?= displayValue($country->getCountryName()) ?></dd>
                        </dl>
                    </div>

                    <div class="col-md-6">
                        <h4 class="border-bottom pb-2">Informació Regional</h4>
                        <dl class="row">
                            <dt class="col-sm-4">ID Regió:</dt>
                            <dd class="col-sm-8">
                                <?php if ($country->getRegionId()): ?>
                                    <a href="../regions/show_region.php?id=<?= displayValue($country->getRegionId()) ?>">
                                        <?= displayValue($country->getRegionId()) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <div class="btn-group">
                        <a href="update_country.php?id=<?= $country->getCountryId() ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="../../models/Country.php" method="POST" class="d-inline ml-2">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="country_id" value="<?= $country->getCountryId() ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Estàs segur que vols eliminar aquest país?')">
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