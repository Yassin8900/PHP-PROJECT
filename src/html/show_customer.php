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

// Función auxiliar para mostrar el estado civil en catalán
function getMaritalStatusText($status) {
    $statusMap = [
        'single' => 'Solter/a',
        'married' => 'Casat/da',
        'divorced' => 'Divorciat/da',
        'widowed' => 'Vidu/a'
    ];
    return $statusMap[$status] ?? $status;
}

// Función auxiliar para mostrar el género en catalán
function getGenderText($gender) {
    $genderMap = [
        'M' => 'Home',
        'F' => 'Dona',
        'O' => 'Altre'
    ];
    return $genderMap[$gender] ?? $gender;
}

// Función auxiliar para manejar valores nulos
function displayValue($value) {
    return $value === null ? '' : htmlspecialchars($value);
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detalls del Client</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detalls del Client</h3>
                    <a href="run_customers.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tornar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="border-bottom pb-2">Informació Personal</h4>
                        <dl class="row">
                            <dt class="col-sm-4">ID:</dt>
                            <dd class="col-sm-8"><?= displayValue($customer->CUSTOMER_ID) ?></dd>

                            <dt class="col-sm-4">Nom Complet:</dt>
                            <dd class="col-sm-8">
                                <?= displayValue($customer->CUST_FIRST_NAME . ' ' . $customer->CUST_LAST_NAME) ?>
                            </dd>

                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">
                                <?php if ($customer->CUST_EMAIL): ?>
                                    <a href="mailto:<?= displayValue($customer->CUST_EMAIL) ?>">
                                        <?= displayValue($customer->CUST_EMAIL) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Telèfon:</dt>
                            <dd class="col-sm-8">
                                <?php if ($customer->PHONE_NUMBERS): ?>
                                    <?= displayValue($customer->PHONE_NUMBERS) ?>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Estat Civil:</dt>
                            <dd class="col-sm-8">
                                <?= displayValue(getMaritalStatusText($customer->MARITAL_STATUS)) ?>
                            </dd>

                            <dt class="col-sm-4">Gènere:</dt>
                            <dd class="col-sm-8">
                                <?= displayValue(getGenderText($customer->GENDER)) ?>
                            </dd>
                        </dl>
                    </div>

                    <div class="col-md-6">
                        <h4 class="border-bottom pb-2">Informació de Contacte i Financera</h4>
                        <dl class="row">
                            <dt class="col-sm-4">Adreça:</dt>
                            <dd class="col-sm-8">
                                <?php if ($customer->CUST_STREET_ADDRESS): ?>
                                    <?= displayValue($customer->CUST_STREET_ADDRESS) ?>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Ciutat:</dt>
                            <dd class="col-sm-8"><?= displayValue($customer->CUST_CITY) ?></dd>

                            <dt class="col-sm-4">Codi Postal:</dt>
                            <dd class="col-sm-8"><?= displayValue($customer->CUST_POSTAL_CODE) ?></dd>

                            <dt class="col-sm-4">Límit Crèdit:</dt>
                            <dd class="col-sm-8">
                                <?php if ($customer->CREDIT_LIMIT): ?>
                                    <?= number_format($customer->CREDIT_LIMIT, 2, ',', '.') ?> €
                                <?php else: ?>
                                    <span class="text-muted">No establert</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Nivell Ingressos:</dt>
                            <dd class="col-sm-8">
                                <?php if ($customer->INCOME_LEVEL): ?>
                                    <?= displayValue($customer->INCOME_LEVEL) ?>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4">Gestor Compte:</dt>
                            <dd class="col-sm-8">
                                <?php if ($customer->ACCOUNT_MGR_ID): ?>
                                    <?= displayValue($customer->ACCOUNT_MGR_ID) ?>
                                <?php else: ?>
                                    <span class="text-muted">No assignat</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <div class="btn-group">
                        <a href="update_customer.php?id=<?= $customer->CUSTOMER_ID ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="../models/Customer.php" method="POST" class="d-inline ml-2">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="customer_id" value="<?= $customer->CUSTOMER_ID ?>">
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Estàs segur que vols eliminar aquest client?')">
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