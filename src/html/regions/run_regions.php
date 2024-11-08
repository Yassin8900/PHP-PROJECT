<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\models\Region;
use App\config\Database;
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Gestió de Regions</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/customers/run_customers.php">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/departments/run_departments.php">Departaments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/jobs/run_jobs.php">Treballs</a>
                    </li>
                    <li class="nav-item active">
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
                    $message = "Regió creada amb èxit";
                    break;
                case 'updated':
                    $message = "Regió actualitzada amb èxit";
                    break;
                case 'deleted':
                    $message = "Regió eliminada amb èxit";
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
            <h1>Gestió de Regions</h1>
            <div>
                <a href="/src/models/Region.php?action=faker" class="btn btn-info mr-2">
                    <i class="fas fa-random"></i> Generar Regió
                </a>
                <a href="create_region.html" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nova Regió
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
                                <th>Accions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $regions = Region::all();
                                
                                if (empty($regions)) {
                                    echo "<tr><td colspan='3' class='text-center'>No hi ha regions registrades</td></tr>";
                                } else {
                                    foreach($regions as $region) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($region->getRegionId()) . "</td>";
                                        echo "<td>" . htmlspecialchars($region->getRegionName()) . "</td>";
                                        echo "<td class='text-center'>";
                                        echo "<a href='show_region.php?id=" . $region->getRegionId() . "' 
                                                class='btn btn-info btn-sm mr-1' title='Veure detalls'>
                                                <i class='fas fa-eye'></i>
                                            </a>";
                                        echo "<a href='update_region.php?id=" . $region->getRegionId() . "' 
                                                class='btn btn-primary btn-sm mr-1' title='Editar'>
                                                <i class='fas fa-edit'></i>
                                            </a>";
                                        echo "<form action='../../models/Region.php' method='POST' style='display:inline;'>";
                                        echo "<input type='hidden' name='action' value='delete'>";
                                            echo "<input type='hidden' name='region_id' value='" . $region->getRegionId() . "'>";
                                        echo "<button type='submit' class='btn btn-danger btn-sm' 
                                                onclick='return confirm(\"Estàs segur que vols eliminar aquesta regió?\")' 
                                                title='Eliminar'>
                                                <i class='fas fa-trash'></i>
                                            </button>";
                                        echo "</form>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }
                            } catch (Exception $e) {
                                echo "<tr><td colspan='3' class='text-center text-danger'>Error: " . 
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