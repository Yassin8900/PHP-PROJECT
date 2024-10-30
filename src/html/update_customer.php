<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\models\Customer;

if (!isset($_GET['id'])) {
    header('Location: /customers/index.php?error=' . urlencode('ID de client no proporcionat'));
    exit;
}

try {
    $customer = Customer::find($_GET['id']);
    if (!$customer) {
        header('Location: /customers/index.php?error=' . urlencode('Client no trobat'));
        exit;
    }
} catch (Exception $e) {
    header('Location: /customers/index.php?error=' . urlencode($e->getMessage()));
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
    <title>Actualitzar Client</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Actualitzar Client</h3>
                    <a href="run_customers.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="../models/Customer.php" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="update">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="border-bottom pb-2">Informació Personal</h4>
                            
                            <div class="form-group">
                                <label for="customer_id">ID Client:</label>
                                <input type="number" id="customer_id" name="customer_id" class="form-control" 
                                    value="<?= displayValue($customer->CUSTOMER_ID) ?>" readonly>
                                <small class="text-muted">ID únic (no modificable)</small>
                            </div>

                            <div class="form-group">
                                <label for="cust_first_name">Nom:</label>
                                <input type="text" id="cust_first_name" name="cust_first_name" 
                                    class="form-control" required maxlength="20"
                                    value="<?= displayValue($customer->CUST_FIRST_NAME) ?>">
                                <div class="invalid-feedback">
                                    El nom és obligatori
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cust_last_name">Llinatges:</label>
                                <input type="text" id="cust_last_name" name="cust_last_name" 
                                    class="form-control" required maxlength="20"
                                    value="<?= displayValue($customer->CUST_LAST_NAME) ?>">
                                <div class="invalid-feedback">
                                    Els llinatges són obligatoris
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cust_email">Email:</label>
                                <input type="email" id="cust_email" name="cust_email" 
                                    class="form-control" maxlength="30"
                                    value="<?= displayValue($customer->CUST_EMAIL) ?>">
                            </div>

                            <div class="form-group">
                                <label for="phone_numbers">Telèfon:</label>
                                <input type="text" id="phone_numbers" name="phone_numbers" 
                                    class="form-control"
                                    value="<?= displayValue($customer->PHONE_NUMBERS) ?>">
                            </div>

                            <div class="form-group">
                                <label for="marital_status">Estat Civil:</label>
                                <select id="marital_status" name="marital_status" class="form-control">
                                    <option value="">Selecciona...</option>
                                    <option value="single" <?= $customer->MARITAL_STATUS === 'single' ? 'selected' : '' ?>>
                                        Solter/a
                                    </option>
                                    <option value="married" <?= $customer->MARITAL_STATUS === 'married' ? 'selected' : '' ?>>
                                        Casat/da
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="gender">Gènere:</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="">Selecciona...</option>
                                    <option value="M" <?= $customer->GENDER === 'M' ? 'selected' : '' ?>>Home</option>
                                    <option value="F" <?= $customer->GENDER === 'F' ? 'selected' : '' ?>>Dona</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h4 class="border-bottom pb-2">Informació de Contacte i Financera</h4>
                            
                            <div class="form-group">
                                <label for="cust_street_address">Adreça:</label>
                                <input type="text" id="cust_street_address" name="cust_street_address" 
                                    class="form-control" maxlength="40"
                                    value="<?= displayValue($customer->CUST_STREET_ADDRESS) ?>">
                            </div>

                            <div class="form-group">
                                <label for="cust_postal_code">Codi Postal:</label>
                                <input type="text" id="cust_postal_code" name="cust_postal_code" 
                                    class="form-control" maxlength="10"
                                    value="<?= displayValue($customer->CUST_POSTAL_CODE) ?>">
                            </div>

                            <div class="form-group">
                                <label for="cust_city">Ciutat:</label>
                                <input type="text" id="cust_city" name="cust_city" 
                                    class="form-control" maxlength="30"
                                    value="<?= displayValue($customer->CUST_CITY) ?>">
                            </div>

                            <div class="form-group">
                                <label for="credit_limit">Límit de Crèdit:</label>
                                <input type="number" id="credit_limit" name="credit_limit" 
                                    class="form-control" step="0.01" max="5000"
                                    value="<?= displayValue($customer->CREDIT_LIMIT) ?>">
                                <small class="text-muted">Màxim 5000€</small>
                            </div>

                            <div class="form-group">
                                <label for="income_level">Nivell d'Ingressos:</label>
                                <input type="text" id="income_level" name="income_level" 
                                    class="form-control"
                                    value="<?= displayValue($customer->INCOME_LEVEL) ?>">
                            </div>

                            <div class="form-group">
                                <label for="account_mgr_id">Gestor de Compte:</label>
                                <input type="number" id="account_mgr_id" name="account_mgr_id" 
                                    class="form-control"
                                    value="<?= displayValue($customer->ACCOUNT_MGR_ID) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualitzar Client
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
            var validation = Array.prototype.filter.call(forms, function(form) {
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