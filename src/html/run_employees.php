<?php


require_once __DIR__ . '/../../vendor/autoload.php';

use App\models\Employee;
use App\config\Database;
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Gestió d'Empleats</title>
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
                    <li class="nav-item active">
                        <a class="nav-link" href="/src/html/run_employees.php">Empleats</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/src/html/run_customers.php">Clients</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php
        // Mostrar mensajes de éxito
        if (isset($_GET['success'])) {
            $message = '';
            switch ($_GET['success']) {
                case 'created':
                    $message = "Empleat creat amb èxit";
                    break;
                case 'updated':
                    $message = "Empleat actualitzat amb èxit";
                    break;
                case 'deleted':
                    $message = "Empleat eliminat amb èxit";
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

        // Mostrar mensajes de error
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
            <h1>Gestió d'Empleats</h1>
            <a href="create_employee.html" class="btn btn-success">
                <i class="fas fa-plus"></i> Nou Empleat
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Llinatge</th>
                                <th>Email</th>
                                <th>Telèfon</th>
                                <th>Data Contractació</th>
                                <th>Salari</th>
                                <th>Accions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $employees = Employee::all();
                                
                                if (empty($employees)) {
                                    echo "<tr><td colspan='8' class='text-center'>No hi ha empleats registrats</td></tr>";
                                } else {
                                    foreach($employees as $employee) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($employee->employee_id) . "</td>";
                                        echo "<td>" . htmlspecialchars($employee->first_name) . "</td>";
                                        echo "<td>" . htmlspecialchars($employee->last_name) . "</td>";
                                        echo "<td>" . htmlspecialchars($employee->email) . "</td>";
                                        echo "<td>" . htmlspecialchars($employee->phone_number) . "</td>";
                                        echo "<td>" . htmlspecialchars($employee->hire_date) . "</td>";
                                        echo "<td>" . number_format($employee->salary, 2, ',', '.') . " €</td>";
                                        echo "<td class='text-center'>";
                                        // Botón Ver
                                        echo "<a href='/src/html/show_employee.php?id=" . $employee->employee_id . "' 
                                                class='btn btn-info btn-sm mr-1' title='Veure detalls'>
                                                <i class='fas fa-eye'></i>
                                            </a>";
                                        // Botón Editar
                                        echo "<a href='/src/html/update_employee.php?id=" . $employee->employee_id . "' 
                                                class='btn btn-primary btn-sm mr-1' title='Editar'>
                                                <i class='fas fa-edit'></i>
                                            </a>";
                                        // Botón Eliminar
                                        echo "<form action='src/models/Employee.php' method='POST' style='display:inline;'>";
                                        echo "<input type='hidden' name='action' value='delete'>";
                                        echo "<input type='hidden' name='employee_id' value='" . $employee->employee_id . "'>";
                                        echo "<button type='submit' class='btn btn-danger btn-sm' 
                                                onclick='return confirm(\"Estàs segur que vols eliminar aquest empleat?\")' 
                                                title='Eliminar'>
                                                <i class='fas fa-trash'></i>
                                            </button>";
                                        echo "</form>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }
                            } catch (Exception $e) {
                                echo "<tr><td colspan='8' class='text-center text-danger'>Error: " . 
                                     htmlspecialchars($e->getMessage()) . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Script para ocultar las alertas automáticamente -->
    <script>
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    </script>
</body>
</html>
