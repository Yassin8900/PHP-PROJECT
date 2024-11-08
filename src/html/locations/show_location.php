<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Location;

if (!isset($_GET['id'])) {
    header('Location: run_locations.php?error=' . urlencode('ID de localització no proporcionat'));
    exit;
}

try {
    $location = Location::find($_GET['id']);
    if (!$location) {
        header('Location: /index.php?error=' . urlencode('Localització no trobada'));
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
    <title>Detalls de la Localització</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detalls de la Localització</h3>
                    <a href="run_locations.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="border-bottom pb-2">Informació de la Localització</h4>
                        <dl class="row">
                            <dt class="col-sm-4">ID:</dt>
                            <dd class="col-sm-8"><?= displayValue($location->getLocationId()) ?></dd>

                            <dt class="col-sm-4">Direcció:</dt>
                            <dd class="col-sm-8"><?= displayValue($location->getStreetAddress()) ?></dd>

                            <dt class="col-sm-4">Codi Postal:</dt>
                            <dd class="col-sm-8"><?= displayValue($location->getPostalCode()) ?></dd>
                        </dl>
                    </div>

                    <div class="col-md-6">
                        <h4 class="border-bottom pb-2">Informació Geogràfica</h4>
                        <dl class="row">
                            <dt class="col-sm-4">Ciutat:</dt>
                            <dd class="col-sm-8"><?= displayValue($location->getCity()) ?></dd>

                            <dt class="col-sm-4">Estat/Província:</dt>
                            <dd class="col-sm-8"><?= displayValue($location->getStateProvince()) ?></dd>

                            <dt class="col-sm-4">País:</dt>
                            <dd class="col-sm-8">
                                <?php if ($location->getCountryId()): ?>
                                    <a href="../countries/show_country.php?id=<?= displayValue($location->getCountryId()) ?>">
                                        <?= displayValue($location->getCountryId()) ?>
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
                        <a href="update_location.php?id=<?= $location->getLocationId() ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="../../models/Location.php" method="POST" class="d-inline ml-2">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="location_id" value="<?= $location->getLocationId() ?>">
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Estàs segur que vols eliminar aquesta localització?')">
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