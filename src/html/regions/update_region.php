<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Region;
use App\config\Database;

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
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Actualitzar Regió</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Actualitzar Regió</h2>
                    <a href="run_regions.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="../../models/Region.php" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="update">
                    
                    <div class="form-group">
                        <label for="region_id">ID Regió:</label>
                        <input type="number" id="region_id" name="region_id" class="form-control" 
                            value="<?= htmlspecialchars($region->REGION_ID) ?>" readonly>
                        <small class="text-muted">Número enter únic (no modificable)</small>
                    </div>

                    <div class="form-group">
                        <label for="region_name">Nom de la Regió:</label>
                        <input type="text" id="region_name" name="region_name" class="form-control" 
                            value="<?= htmlspecialchars($region->REGION_NAME) ?>" 
                            maxlength="25" required>
                        <small class="text-muted">Camp obligatori. Màxim 25 caràcters</small>
                        <div class="invalid-feedback">
                            El nom de la regió és obligatori (màxim 25 caràcters)
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualitzar Regió
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