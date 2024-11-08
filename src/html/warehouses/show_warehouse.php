<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Warehouse;

if (!isset($_GET['id'])) {
    header('Location: /index.php?error=' . urlencode('ID de magatzem no proporcionat'));
    exit;
}

try {
    $warehouse = Warehouse::find($_GET['id']);
    if (!$warehouse) {
        header('Location: /index.php?error=' . urlencode('Magatzem no trobat'));
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
    <title>Detalls del Magatzem</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detalls del Magatzem</h3>
                    <a href="run_warehouses.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="border-bottom pb-2">Informació Bàsica</h4>
                        <dl class="row">
                            <dt class="col-sm-4">ID:</dt>
                            <dd class="col-sm-8"><?= displayValue($warehouse->getWarehouseId()) ?></dd>

                            <dt class="col-sm-4">Nom:</dt>
                            <dd class="col-sm-8"><?= displayValue($warehouse->getWarehouseName()) ?></dd>

                            <dt class="col-sm-4">ID Localització:</dt>
                            <dd class="col-sm-8">
                                <?php if ($warehouse->getLocationId()): ?>
                                    <a href="/src/html/locations/show_location.php?id=<?= displayValue($warehouse->getLocationId()) ?>">
                                        <?= displayValue($warehouse->getLocationId()) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>

                    <div class="col-md-6">
                        <h4 class="border-bottom pb-2">Especificacions i Ubicació</h4>
                        <dl class="row">
                            <dt class="col-sm-4">Especificacions:</dt>
                            <dd class="col-sm-8">
                                <?php if ($warehouse->getWarehouseSpec()): ?>
                                    <?= displayValue($warehouse->getWarehouseSpec()) ?>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Geolocalització:</dt>
                            <dd class="col-sm-8">
                                <?php if ($warehouse->getWhGeoLocation()): ?>
                                    <?php
                                    $geoLocation = $warehouse->getWhGeoLocation();
                                    echo displayValue($geoLocation);
                                    // Si las coordenadas están en formato "latitud,longitud"
                                    if (strpos($geoLocation, ',') !== false) {
                                        list($lat, $lng) = explode(',', $geoLocation);
                                        echo "<br><a href='https://www.google.com/maps?q=$lat,$lng' 
                                                target='_blank' class='btn btn-sm btn-info mt-2'>
                                                <i class='fas fa-map-marker-alt'></i> Veure al mapa
                                            </a>";
                                    }
                                    ?>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <div class="btn-group">
                        <a href="update_warehouse.php?id=<?= $warehouse->getWarehouseId() ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="../../models/Warehouse.php" method="POST" class="d-inline ml-2">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="warehouse_id" value="<?= $warehouse->getWarehouseId() ?>">
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Estàs segur que vols eliminar aquest magatzem?')">
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