<?php

namespace App\models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\config\Database;
use Exception;
use Faker\Factory;

class Employee extends Model {
    protected static $table = 'employees';
    protected static $primaryKey = 'employee_id';

    public function __construct(
        public ?int $employee_id = null,
        public ?string $first_name = null,
        public ?string $last_name = null,
        public ?string $email = null,
        public ?string $phone_number = null,
        public ?string $hire_date = null,
        public ?string $job_id = null,
        public ?float $salary = null,
        public ?float $commission_pct = null,
        public ?int $manager_id = null,
        public ?int $department_id = null
    ) {}

    public function save() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $table = static::$table;

            if (!isset($this->employee_id)) {
                throw new Exception("ID empleat no informat.");
            }

            $sql = "INSERT INTO $table (
                    employee_id, first_name, last_name, email, 
                    phone_number, hire_date, job_id, salary, 
                    commission_pct, manager_id, department_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    first_name = VALUES(first_name),
                    last_name = VALUES(last_name),
                    email = VALUES(email),
                    phone_number = VALUES(phone_number),
                    hire_date = VALUES(hire_date),
                    job_id = VALUES(job_id),
                    salary = VALUES(salary),
                    commission_pct = VALUES(commission_pct),
                    manager_id = VALUES(manager_id),
                    department_id = VALUES(department_id)";

            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $conn->error);
            }

            $stmt->bind_param("issssssddii", 
                $this->employee_id,
                $this->first_name,
                $this->last_name,
                $this->email,
                $this->phone_number,
                $this->hire_date,
                $this->job_id,
                $this->salary,
                $this->commission_pct,
                $this->manager_id,
                $this->department_id
            );

            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $db->close();
            return true;

        } catch (Exception $e) {
            throw new Exception("Error en save(): " . $e->getMessage());
        }
    }

    public function destroy() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $table = static::$table;

            if (!isset($this->employee_id)) {
                throw new Exception("ID empleat no informat.");
            }

            
            $stmt = $conn->prepare("SELECT employee_id FROM $table WHERE employee_id = ?");
            $stmt->bind_param("i", $this->employee_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("L'empleat no existeix.");
            }

            
            $stmt = $conn->prepare("DELETE FROM $table WHERE employee_id = ?");
            $stmt->bind_param("i", $this->employee_id);
            
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error eliminando el empleado: " . $stmt->error);
            }

            $db->close();
            return true;

        } catch (Exception $e) {
            throw new Exception("Error en destroy(): " . $e->getMessage());
        }
    }

    public static function handleAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || 
            ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'faker')) {
            
            $db = new Database();
            $conn = $db->getConnection();
            $conn->autocommit(FALSE);
            
            try {
                $action = $_POST['action'] ?? $_GET['action'] ?? '';
                
                switch($action) {
                    case 'faker':
                        $faker = Factory::create('es_ES');
                        
                        
                        $result = $conn->query("SELECT MAX(employee_id) as max_id FROM employees");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $employee = new self(
                            $nextId,
                            $faker->firstName,
                            $faker->lastName,
                            $faker->email,
                            $faker->phoneNumber,
                            $faker->date('Y-m-d', 'now'),
                            'IT_PROG',
                            $faker->numberBetween(30000, 120000),
                            $faker->randomFloat(2, 0, 0.99),
                            null,
                            $faker->numberBetween(10, 110)
                        );
                        
                        if ($employee->save()) {
                            $conn->commit();
                            header('Location: /src/html/run_employees.php?success=created');
                            exit;
                        }
                        break;

                    case 'create':
                    case 'update':
                        $employee = new self(
                            isset($_POST['employee_id']) ? (int)$_POST['employee_id'] : null,
                            $_POST['first_name'] ?? null,
                            $_POST['last_name'] ?? null,
                            $_POST['email'] ?? null,
                            $_POST['phone_number'] ?? null,
                            $_POST['hire_date'] ?? null,
                            $_POST['job_id'] ?? null,
                            isset($_POST['salary']) ? (float)$_POST['salary'] : null,
                            isset($_POST['commission_pct']) ? (float)$_POST['commission_pct'] : null,
                            !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null,
                            !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null
                        );
                        
                        if ($employee->save()) {
                            $conn->commit();
                            header('Location: /src/html/run_employees.php?success=' . ($action === 'create' ? 'created' : 'updated'));
                            exit;
                        }
                        break;

                    case 'delete':
                        if (!isset($_POST['employee_id'])) {
                            throw new Exception("ID de empleado no proporcionado");
                        }
                        
                        $employee = new self(
                            (int)$_POST['employee_id'],
                            null, null, null, null, null, null, 
                            null, null, null, null
                        );
                        
                        if ($employee->destroy()) {
                            $conn->commit();
                            header('Location: /src/html/run_employees.php?success=deleted');
                            exit;
                        }
                        break;

                    default:
                        throw new Exception("Acción no válida");
                }
                
            } catch (Exception $e) {
                if (isset($conn)) {
                    $conn->rollback();
                }
                header('Location: /src/html/run_employees.php?error=' . urlencode($e->getMessage()));
                exit;
            } finally {
                if (isset($conn)) {
                    $conn->close();
                }
            }
        }
    }
}


if (basename($_SERVER['PHP_SELF']) === 'Employee.php') {
    Employee::handleAction();
}