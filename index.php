<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

// Verificación de sesión
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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
            <!-- Barra de navegación -->
            <div class="col-md-3 col-lg-2 px-0 bg-light sidebar">
                <div class="position-sticky">
                    <div class="category-header">
                        <i class="fas fa-building"></i> Sistema de Gestió
                    </div>
                    
                    
                    <div class="category-header">
                        <i class="fas fa-users"></i> HR
                    </div>
                    <nav class="nav flex-column submenu">
                        <a class="nav-link" href="/src/html/employees/run_employees.php">
                            <i class="fas fa-user-tie"></i> Empleats
                        </a>
                        <a class="nav-link" href="/src/html/departments/run_departments.php">
                            <i class="fas fa-sitemap"></i> Departaments
                        </a>
                        <a class="nav-link" href="/src/html/jobs/run_jobs.php">
                            <i class="fas fa-briefcase"></i> Treballs
                        </a>
                        <a class="nav-link" href="/src/html/regions/run_regions.php">
                            <i class="fas fa-globe"></i> Regions
                        </a>
                        <a class="nav-link" href="/src/html/countries/run_countries.php">
                            <i class="fas fa-flag"></i> Països
                        </a>
                        <a class="nav-link" href="/src/html/locations/run_locations.php">
                            <i class="fas fa-map-marker-alt"></i> Ubicacions
                        </a>
                    </nav>

                    
                    <div class="category-header">
                        <i class="fas fa-shopping-cart"></i> OE
                    </div>
                    <nav class="nav flex-column submenu">
                        <a class="nav-link" href="/src/html/customers/run_customers.php">
                            <i class="fas fa-user-friends"></i> Clients
                        </a>
                        <a class="nav-link" href="/src/html/warehouses/run_warehouses.php">
                            <i class="fas fa-warehouse"></i> Magatzems
                        </a>
                        <a class="nav-link" href="/src/html/orders/run_orders.php">
                            <i class="fas fa-file-invoice"></i> Comandes
                        </a>
                        <a class="nav-link" href="/src/html/order_items/run_order_items.php">
                            <i class="fas fa-box"></i> Línies de Comanda
                        </a>
                        <a class="nav-link" href="/src/html/products/run_products.php">
                            <i class="fas fa-boxes"></i> Productes
                        </a>
                    </nav>
                </div>
            </div>

            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <!-- Mensaje de bienvenida + boton de logout -->
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

                <!-- Cuadros explicativos justo debajo de la bienvenida -->
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

                <!-- Nueva fila para estadísticas -->
                <!-- Apartado meramente decorativo, no funcional -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb">
                            <div class="card-header">
                                <i class="fas fa-chart-line"></i> Nuevos Clientes (Últimos 30 días)
                            </div>
                            <div class="card-body">
                                <canvas id="newCustomersChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-chart-bar"></i> Ventas por Departamento
                            </div>
                            <div class="card-body">
                                <canvas id="salesByDepartmentChart"></canvas>
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
        //Para resaltar en el menú superior del "run_tabla.php la tabla seleccionada
        $(document).ready(function() {
            var path = window.location.pathname;
            $('.nav-link').each(function() {
                if (path.includes($(this).attr('href'))) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
    
    <!-- Añadir Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Configuración del gráfico de nuevos clientes
        const newCustomersCtx = document.getElementById('newCustomersChart');
        new Chart(newCustomersCtx, {
            type: 'line',
            data: {
                labels: ['Hace 30 días', 'Hace 25 días', 'Hace 20 días', 'Hace 15 días', 'Hace 10 días', 'Hace 5 días', 'Hoy'],
                datasets: [{
                    label: 'Nuevos Clientes',
                    data: [12, 19, 3, 5, 2, 3, 7],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            }
        });

        // Configuración del gráfico de ventas por departamento
        const salesCtx = document.getElementById('salesByDepartmentChart');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: ['RRHH', 'Ventas', 'IT', 'Marketing', 'Operaciones'],
                datasets: [{
                    label: 'Ventas €',
                    data: [12000, 19000, 3000, 5000, 2000],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    </script>
</body>
</html>
