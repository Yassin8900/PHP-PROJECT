<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Customer;
use App\config\Database;



function displayValue($value) {
    return $value === null ? '' : htmlspecialchars($value);
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Gestió de Clients</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/index.php">DWES</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">Inici</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/employees/run_employees.php">Empleats</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="/src/html/customers/run_customers.php">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/departments/run_departments.php">Departaments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/jobs/run_jobs.php">Treballs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/regions/run_regions.php">Regions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/locations/run_locations.php">Localitzacions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/countries/run_countries.php">Països</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/warehouses/run_warehouses.php">Magatzems</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        
        <?php
        if (isset($_GET['success'])) {
            $message = '';
            switch ($_GET['success']) {
                case 'created':
                    $message = "Client creat amb èxit";
                    break;
                case 'updated':
                    $message = "Client actualitzat amb èxit";
                    break;
                case 'deleted':
                    $message = "Client eliminat amb èxit";
                    break;
            }
            if ($message) {
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        $message
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>";
            }
        }

        
        if (isset($_GET['error'])) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    " . htmlspecialchars($_GET['error']) . "
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>";
        }
        ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestió de Clients</h1>
            <div>
                <a href="/src/models/Customer.php?action=faker" class="btn btn-info mr-2">
                    <i class="fas fa-random"></i> Generar Client
                </a>
                <a href="create_customer.html" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nou Client
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Llinatges</th>
                                <th>Email</th>
                                <th>Accions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $customers = Customer::all();
                                if (empty($customers)) {
                                    echo "<tr><td colspan='5' class='text-center'>No hi ha clients registrats</td></tr>";
                                } else {
                                    foreach ($customers as $customer) {
                                        echo "<tr>";
                                        echo "<td>" . displayValue($customer->getCustomerId()) . "</td>";
                                        echo "<td>" . displayValue($customer->getCustFirstName()) . "</td>";
                                        echo "<td>" . displayValue($customer->getCustLastName()) . "</td>";
                                        echo "<td>" . displayValue($customer->getCustEmail()) . "</td>";
                                        echo "<td class='text-center'>";
                                        echo "<a href='show_customer.php?id=" . $customer->getCustomerId() . "' 
                                               class='btn btn-info btn-sm mr-1' title='Veure detalls'>
                                               <i class='fas fa-eye'></i>
                                            </a>";
                                        echo "<a href='update_customer.php?id=" . $customer->getCustomerId() . "' 
                                               class='btn btn-primary btn-sm mr-1' title='Editar'>
                                               <i class='fas fa-edit'></i>
                                            </a>";
                                        echo "<form action='../../models/Customer.php' method='POST' style='display:inline;'>";
                                        echo "<input type='hidden' name='action' value='delete'>";
                                        echo "<input type='hidden' name='customer_id' value='" . $customer->getCustomerId() . "'>";
                                        echo "<button type='submit' class='btn btn-danger btn-sm' 
                                                onclick='return confirm(\"Estàs segur que vols eliminar aquest client?\")' 
                                                title='Eliminar'>
                                               <i class='fas fa-trash'></i>
                                            </button>";
                                        echo "</form>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } 
                            } catch (Exception $e) {
                                echo "<tr><td colspan='5' class='text-center text-danger'>Error: " . 
                                     htmlspecialchars($e->getMessage()) . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    
    <script>
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    </script>
</body>
</html>
