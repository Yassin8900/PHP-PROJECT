<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Warehouse;
use App\config\Database;

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
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Actualitzar Magatzem</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Actualitzar Magatzem</h2>
                    <a href="run_warehouses.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="../../models/Warehouse.php" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="update">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="warehouse_id">ID Magatzem:</label>
                                <input type="number" id="warehouse_id" name="warehouse_id" class="form-control" 
                                    value="<?= htmlspecialchars($warehouse->getWarehouseId()) ?>" readonly>
                                <small class="text-muted">Número enter únic (no modificable)</small>
                            </div>

                            <div class="form-group">
                                <label for="warehouse_name">Nom del Magatzem:</label>
                                <input type="text" id="warehouse_name" name="warehouse_name" class="form-control" 
                                    value="<?= htmlspecialchars($warehouse->getWarehouseName()) ?>" 
                                    maxlength="35" required>
                                <small class="text-muted">Màxim 35 caràcters</small>
                                <div class="invalid-feedback">
                                    El nom del magatzem és obligatori (màxim 35 caràcters)
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="location_id">ID Localització:</label>
                                <input type="number" id="location_id" name="location_id" class="form-control"
                                    value="<?= htmlspecialchars($warehouse->getLocationId()) ?>" required>
                                <small class="text-muted">ID numèric de la localització</small>
                                <div class="invalid-feedback">
                                    La localització és obligatòria
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="warehouse_spec">Especificacions:</label>
                                <textarea id="warehouse_spec" name="warehouse_spec" class="form-control" 
                                    maxlength="100" rows="3"><?= htmlspecialchars($warehouse->getWarehouseSpec()) ?></textarea>
                                <small class="text-muted">Màxim 100 caràcters</small>
                            </div>

                            <div class="form-group">
                                <label for="wh_geo_location">Geolocalització:</label>
                                <input type="text" id="wh_geo_location" name="wh_geo_location" class="form-control" 
                                    value="<?= htmlspecialchars($warehouse->getWhGeoLocation()) ?>" 
                                    maxlength="100" pattern="^-?\d+(\.\d+)?,\s*-?\d+(\.\d+)?$">
                                <small class="text-muted">Format: latitud,longitud (ex: 41.40338,2.17403)</small>
                                <div class="invalid-feedback">
                                    Format incorrecte. Ha de ser: latitud,longitud
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualitzar Magatzem
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                }
            });
        });
    })();
    </script>
</body>
</html> 