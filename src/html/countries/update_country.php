<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Country;

if (!isset($_GET['id'])) {
    header('Location: run_countries.php?error=' . urlencode('ID de país no proporcionado'));
    exit;
}

try {
    $country = Country::find($_GET['id']);
    if (!$country) {
        header('Location: /index.php?error=' . urlencode('País no encontrado'));
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
    <title>Actualitzar País</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Actualitzar País</h2>
                    <a href="run_countries.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="../../models/Country.php" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="update">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country_id">ID País:</label>
                                <input type="text" id="country_id" name="country_id" class="form-control" 
                                    value="<?= htmlspecialchars($country->getCountryId()) ?>" readonly>
                                <small class="text-muted">Codi de país de 2 lletres (no modificable)</small>
                            </div>
                            <div class="form-group">
                                <label for="country_name">Nom del País:</label>
                                <input type="text" id="country_name" name="country_name" class="form-control" 
                                    value="<?= htmlspecialchars($country->getCountryName()) ?>" 
                                    maxlength="40" required>
                                <small class="text-muted">Camp obligatori. Màxim 40 caràcters</small>
                                <div class="invalid-feedback">
                                    El nom del país és obligatori (màxim 40 caràcters)
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="region_id">ID Regió:</label>
                                <input type="number" id="region_id" name="region_id" class="form-control" 
                                    value="<?= htmlspecialchars($country->getRegionId()) ?>" 
                                    min="1" required>
                                <small class="text-muted">Camp obligatori. ID numèric de la regió (major que 0)</small>
                                <div class="invalid-feedback">
                                    L'ID de la regió és obligatori i ha de ser major que 0
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualitzar País
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