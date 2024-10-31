<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

// Verificación de sesión
if (!isset($_SESSION['username'])) {
    header("Location: /src/html/login.php");
    exit();
}

use App\models\Employee;
use App\config\Database;
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestió</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/src/css/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            

            <div class="col-md-3 col-lg-2 px-0 bg-light sidebar">
                <div class="position-sticky">
                    <div class="category-header">
                        <i class="fas fa-building"></i> Sistema de Gestió
                    </div>
                    
                    
                    <div class="category-header">
                        <i class="fas fa-users"></i> HR
                    </div>
                    <nav class="nav flex-column submenu">
                        <a class="nav-link" href="/src/html/run_employees.php">
                            <i class="fas fa-user-tie"></i> Empleats
                        </a>
                        <a class="nav-link" href="/src/html/run_departments.php">
                            <i class="fas fa-sitemap"></i> Departaments
                        </a>
                        <a class="nav-link" href="/src/html/run_jobs.php">
                            <i class="fas fa-briefcase"></i> Treballs
                        </a>
                        <a class="nav-link" href="/src/html/run_regions.php">
                            <i class="fas fa-globe"></i> Regions
                        </a>
                        <a class="nav-link" href="/src/html/run_countries.php">
                            <i class="fas fa-flag"></i> Països
                        </a>
                        <a class="nav-link" href="/src/html/run_locations.php">
                            <i class="fas fa-map-marker-alt"></i> Ubicacions
                        </a>
                    </nav>

                    
                    <div class="category-header">
                        <i class="fas fa-shopping-cart"></i> OE
                    </div>
                    <nav class="nav flex-column submenu">
                        <a class="nav-link" href="/src/html/run_customers.php">
                            <i class="fas fa-user-friends"></i> Clients
                        </a>
                        <a class="nav-link" href="/src/html/run_orders.php">
                            <i class="fas fa-file-invoice"></i> Comandes
                        </a>
                        <a class="nav-link" href="/src/html/run_order_items.php">
                            <i class="fas fa-box"></i> Línies de Comanda
                        </a>
                        <a class="nav-link" href="/src/html/run_products.php">
                            <i class="fas fa-boxes"></i> Productes
                        </a>
                    </nav>
                </div>
            </div>

            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <?php
                
                if (isset($_GET['success'])) {
                    $message = '';
                    switch ($_GET['success']) {
                        case 'created':
                            $message = 'Registre creat correctament.';
                            break;
                        case 'updated':
                            $message = 'Registre actualitzat correctament.';
                            break;
                        case 'deleted':
                            $message = 'Registre eliminat correctament.';
                            break;
                    }
                    if ($message) {
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                {$message}
                                <button type='button' class='close' data-dismiss='alert'>
                                    <span>&times;</span>
                                </button>
                            </div>";
                    }
                }

                
                if (isset($_GET['error'])) {
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            {$_GET['error']}
                            <button type='button' class='close' data-dismiss='alert'>
                                <span>&times;</span>
                            </button>
                        </div>";
                }
                ?>

                <div class="row">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between w-100 mb-3">
                            <span class="navbar-text">
                                Benvingut, <?= htmlspecialchars($_SESSION['username']) ?>
                            </span>
                            <a href="logout.php" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt"></i> Tancar Sessió
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Benvingut al Sistema de Gestió</h1>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-users"></i> Recursos Humans
                            </div>
                            <div class="card-body">
                                <p>Gestió d'empleats, departaments i altres recursos humans.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-shopping-cart"></i> Operacions
                            </div>
                            <div class="card-body">
                                <p>Gestió de clients, comandes i productes.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        
        $(document).ready(function() {
            var path = window.location.pathname;
            $('.nav-link').each(function() {
                if (path.includes($(this).attr('href'))) {
                    $(this).addClass('active');
                }
            });
        });

        
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    </script>
</body>
</html>
