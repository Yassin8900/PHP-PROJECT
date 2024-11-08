<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Location;
use App\config\Database;

if (!isset($_GET['id'])) {
    header('Location: run_locations.php?error=' . urlencode('ID de localització no proporcionat'));
    exit;
}

try {
    $location = Location::find($_GET['id']);
    if (!$location) {
        header('Location: run_locations.php?error=' . urlencode('Localització no trobada'));
        exit;
    }
} catch (Exception $e) {
    header('Location: run_locations.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Actualitzar Localització</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Actualitzar Localització</h2>
                    <a href="run_locations.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="../../models/Location.php" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="location_id" value="<?= displayValue($location->getLocationId()) ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="street_address">Direcció:</label>
                                <input type="text" id="street_address" name="street_address" 
                                       class="form-control" maxlength="40" 
                                       value="<?= displayValue($location->getStreetAddress()) ?>">
                                <small class="text-muted">Màxim 40 caràcters</small>
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Còdex Postal:</label>
                                <input type="text" id="postal_code" name="postal_code" 
                                       class="form-control" maxlength="12" 
                                       value="<?= displayValue($location->getPostalCode()) ?>">
                                <small class="text-muted">Màxim 12 caràcters</small>
                            </div>
                            <div class="form-group">
                                <label for="city">Ciutat:</label>
                                <input type="text" id="city" name="city" 
                                       class="form-control" maxlength="30" 
                                       value="<?= displayValue($location->getCity()) ?>" required>
                                <small class="text-muted">Camp obligatori. Màxim 30 caràcters</small>
                                <div class="invalid-feedback">
                                    La ciutat és obligatoria
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state_province">Estat/Província:</label>
                                <input type="text" id="state_province" name="state_province" 
                                       class="form-control" maxlength="25" 
                                       value="<?= displayValue($location->getStateProvince()) ?>">
                                <small class="text-muted">Màxim 25 caràcters</small>
                            </div>
                            <div class="form-group">
                                <label for="country_id">ID País:</label>
                                <input type="text" id="country_id" name="country_id" 
                                       class="form-control" maxlength="2" 
                                       value="<?= displayValue($location->getCountryId()) ?>" required>
                                <small class="text-muted">Camp obligatori. Màxim 2 caràcters</small>
                                <div class="invalid-feedback">
                                    L'ID del país és obligatori
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualitzar Localització
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
            Array.prototype.filter.call(forms, function(form) {
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