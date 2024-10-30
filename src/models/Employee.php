<?php

namespace App\models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/Database.php';

use App\config\Database;
use Exception;

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

            // Verificar si existe el empleado
            $stmt = $conn->prepare("SELECT employee_id FROM $table WHERE employee_id = ?");
            $stmt->bind_param("i", $this->employee_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("L'empleat no existeix.");
            }

            // Eliminar el empleado
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $action = $_POST['action'] ?? '';
                
                switch($action) {
                    case 'create':
                    case 'update':
                        $employee = new self(
                            $_POST['employee_id'] ?? null,
                            $_POST['first_name'] ?? null,
                            $_POST['last_name'] ?? null,
                            $_POST['email'] ?? null,
                            $_POST['phone_number'] ?? null,
                            $_POST['hire_date'] ?? null,
                            $_POST['job_id'] ?? null,
                            $_POST['salary'] ?? null,
                            $_POST['commission_pct'] ?? null,
                            $_POST['manager_id'] ?? null,
                            $_POST['department_id'] ?? null
                        );
                        
                        if ($employee->save()) {
                            header('Location: /index.php?success=' . ($action === 'create' ? 'created' : 'updated'));
                        }
                        break;
                        
                    case 'delete':
                        if (!isset($_POST['employee_id'])) {
                            throw new Exception("ID de empleado no proporcionado");
                        }
                        
                        $employee = new self($_POST['employee_id']);
                        if ($employee->destroy()) {
                            header('Location: /index.php?success=deleted');
                        }
                        break;
                        
                    default:
                        throw new Exception("Acción no válida");
                }
                
            } catch (Exception $e) {
                header('Location: /index.php?error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }
}

// Manejar las acciones cuando se accede directamente a este archivo
if (basename($_SERVER['PHP_SELF']) === 'Employee.php') {
    Employee::handleAction();
}