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

    private ?int $employee_id = null;
    private ?string $first_name = null;
    private ?string $last_name = null;
    private ?string $email = null;
    private ?string $phone_number = null;
    private ?string $hire_date = null;
    private ?string $job_id = null;
    private ?float $salary = null;
    private ?float $commission_pct = null;
    private ?int $manager_id = null;
    private ?int $department_id = null;

    public function __construct(
        ?int $employee_id = null,
        ?string $first_name = null,
        ?string $last_name = null,
        ?string $email = null,
        ?string $phone_number = null,
        ?string $hire_date = null,
        ?string $job_id = null,
        ?float $salary = null,
        ?float $commission_pct = null,
        ?int $manager_id = null,
        ?int $department_id = null
    ) {
        $this->employee_id = $employee_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone_number = $phone_number;
        $this->hire_date = $hire_date;
        $this->job_id = $job_id;
        $this->salary = $salary;
        $this->commission_pct = $commission_pct;
        $this->manager_id = $manager_id;
        $this->department_id = $department_id;
    }

    // Getters y Setters
    public function getEmployeeId(): ?int {
        return $this->employee_id;
    }

    public function setEmployeeId(?int $employee_id): void {
        $this->employee_id = $employee_id;
    }

    public function getFirstName(): ?string {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): void {
        $this->first_name = $first_name;
    }

    public function getLastName(): ?string {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): void {
        $this->last_name = $last_name;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function getPhoneNumber(): ?string {
        return $this->phone_number;
    }

    public function setPhoneNumber(?string $phone_number): void {
        $this->phone_number = $phone_number;
    }

    public function getHireDate(): ?string {
        return $this->hire_date;
    }

    public function setHireDate(?string $hire_date): void {
        $this->hire_date = $hire_date;
    }

    public function getJobId(): ?string {
        return $this->job_id;
    }

    public function setJobId(?string $job_id): void {
        $this->job_id = $job_id;
    }

    public function getSalary(): ?float {
        return $this->salary;
    }

    public function setSalary(?float $salary): void {
        $this->salary = $salary;
    }

    public function getCommissionPct(): ?float {
        return $this->commission_pct;
    }

    public function setCommissionPct(?float $commission_pct): void {
        $this->commission_pct = $commission_pct;
    }

    public function getManagerId(): ?int {
        return $this->manager_id;
    }

    public function setManagerId(?int $manager_id): void {
        $this->manager_id = $manager_id;
    }

    public function getDepartmentId(): ?int {
        return $this->department_id;
    }

    public function setDepartmentId(?int $department_id): void {
        $this->department_id = $department_id;
    }

    public function save() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $table = static::$table;

            if (!isset($this->employee_id)) {
                throw new Exception("ID empleat no informat.");
            }

            // Verificar si el email ya existe
            $stmt = $conn->prepare("SELECT employee_id FROM $table WHERE email = ? AND employee_id != ?");
            $stmt->bind_param("si", $this->email, $this->employee_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                throw new Exception("El email ya está en uso.");
            }
            
            //Verificar si el manager_id existe como empleado
            $stmt = $conn->prepare("SELECT employee_id FROM $table WHERE employee_id = ?");
            $stmt->bind_param("i", $this->manager_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("El manager_id no existe.");
            }

            // Verificar existencia de job_id
            if ($this->job_id) {
                $stmt = $conn->prepare("SELECT job_id FROM jobs WHERE job_id = ?");
                $stmt->bind_param("s", $this->job_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception("El job_id no existe.");
                }
            }

            // Verificar existencia de department_id
            if ($this->department_id) {
                $stmt = $conn->prepare("SELECT department_id FROM departments WHERE department_id = ?");
                $stmt->bind_param("i", $this->department_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception("El department_id no existe.");
                }
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
                            $faker->firstName(),
                            $faker->lastName(),
                            $faker->email(),
                            $faker->phoneNumber(),
                            $faker->date('Y-m-d', 'now'),
                            'IT_PROG',
                            $faker->numberBetween(30000, 120000),
                            $faker->randomFloat(2, 0, 0.99),
                            $faker->numberBetween(100, 200),
                            $faker->randomElement([10, 20, 30, 40, 50, 60, 70, 80, 90, 100])
                        );
                        
                        if ($employee->save()) {
                            $conn->commit();
                            header('Location: /src/html/employees/run_employees.php?success=created');
                            exit;
                        }
                        break;

                    case 'create':
                        $result = $conn->query("SELECT MAX(employee_id) as max_id FROM employees");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $employee = new self(
                            $nextId,
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
                            header('Location: /src/html/employees/run_employees.php?success=created');
                            exit;
                        }
                        break;

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
                            header('Location: /src/html/employees/run_employees.php?success=updated');
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
                            header('Location: /src/html/employees/run_employees.php?success=deleted');
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
                header('Location: /src/html/employees/run_employees.php?error=' . urlencode($e->getMessage()));
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