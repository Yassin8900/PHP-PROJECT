<?php

namespace App\models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\config\Database;
use Exception;
use Faker\Factory;

class Department extends Model {
    protected static $table = 'departments';
    protected static $primaryKey = 'department_id';

    private ?int $DEPARTMENT_ID = null;
    private ?string $DEPARTMENT_NAME = null;
    private ?int $MANAGER_ID = null;
    private ?int $LOCATION_ID = null;

    public function __construct(
        ?int $DEPARTMENT_ID = null,
        ?string $DEPARTMENT_NAME = null,
        ?int $MANAGER_ID = null,
        ?int $LOCATION_ID = null
    ) {
        $this->DEPARTMENT_ID = $DEPARTMENT_ID;
        $this->DEPARTMENT_NAME = $DEPARTMENT_NAME;
        $this->MANAGER_ID = $MANAGER_ID;
        $this->LOCATION_ID = $LOCATION_ID;
    }

    // Getters y Setters
    public function getDepartmentId(): ?int {
        return $this->DEPARTMENT_ID;
    }

    public function setDepartmentId(?int $DEPARTMENT_ID): void {
        $this->DEPARTMENT_ID = $DEPARTMENT_ID;
    }

    public function getDepartmentName(): ?string {
        return $this->DEPARTMENT_NAME;
    }

    public function setDepartmentName(?string $DEPARTMENT_NAME): void {
        $this->DEPARTMENT_NAME = $DEPARTMENT_NAME;
    }

    public function getManagerId(): ?int {
        return $this->MANAGER_ID;
    }

    public function setManagerId(?int $MANAGER_ID): void {
        $this->MANAGER_ID = $MANAGER_ID;
    }

    public function getLocationId(): ?int {
        return $this->LOCATION_ID;
    }

    public function setLocationId(?int $LOCATION_ID): void {
        $this->LOCATION_ID = $LOCATION_ID;
    }

    public function save() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $table = static::$table;

            if (!isset($this->DEPARTMENT_ID)) {
                throw new Exception("ID departament no informat.");
            }

            // Verificar existencia de MANAGER_ID
            if ($this->MANAGER_ID) {
                $stmt = $conn->prepare("SELECT employee_id FROM employees WHERE employee_id = ?");
                $stmt->bind_param("i", $this->MANAGER_ID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception("El MANAGER_ID no existe.");
                }
            }

            // Verificar existencia de LOCATION_ID
            if ($this->LOCATION_ID) {
                $stmt = $conn->prepare("SELECT location_id FROM locations WHERE location_id = ?");
                $stmt->bind_param("i", $this->LOCATION_ID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception("El LOCATION_ID no existe.");
                }
            }

            $sql = "INSERT INTO $table (
                    DEPARTMENT_ID, DEPARTMENT_NAME, MANAGER_ID, LOCATION_ID
                ) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    DEPARTMENT_NAME = VALUES(DEPARTMENT_NAME),
                    MANAGER_ID = VALUES(MANAGER_ID),
                    LOCATION_ID = VALUES(LOCATION_ID)";

            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparant la consulta: " . $conn->error);
            }

            $stmt->bind_param("isii", 
                $this->DEPARTMENT_ID,
                $this->DEPARTMENT_NAME,
                $this->MANAGER_ID,
                $this->LOCATION_ID
            );

            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error executant la consulta: " . $stmt->error);
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

            if (!isset($this->DEPARTMENT_ID)) {
                throw new Exception("ID departament no informat.");
            }

            $stmt = $conn->prepare("SELECT department_id FROM $table WHERE department_id = ?");
            $stmt->bind_param("i", $this->DEPARTMENT_ID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("El departament no existeix.");
            }

            $stmt = $conn->prepare("DELETE FROM $table WHERE department_id = ?");
            $stmt->bind_param("i", $this->DEPARTMENT_ID);
            
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error eliminant el departament: " . $stmt->error);
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
                        
                        $result = $conn->query("SELECT MAX(department_id) as max_id FROM departments");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $departments = [
                            'IT',
                            'Human Resources',
                            'Marketing',
                            'Sales',
                            'Finance',
                            'Operations',
                            'Research',
                            'Development',
                            'Customer Service',
                            'Legal'
                        ];
                        
                        $department = new self(
                            $nextId,
                            $faker->randomElement($departments),
                            $faker->numberBetween(100, 200),
                            $faker->numberBetween(1000, 2000)
                        );
                        
                        if ($department->save()) {
                            $conn->commit();
                            header('Location: /src/html/departments/run_departments.php?success=created');
                            exit;
                        }
                        break;

                    case 'create':
                        $result = $conn->query("SELECT MAX(department_id) as max_id FROM departments");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $department = new self(
                            $nextId,
                            $_POST['department_name'] ?? null,
                            !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null,
                            !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null
                        );
                        
                        if ($department->save()) {
                            $conn->commit();
                            header('Location: /src/html/departments/run_departments.php?success=created');
                            exit;
                        }
                        break;

                    case 'update':
                        $department = new self(
                            isset($_POST['department_id']) ? (int)$_POST['department_id'] : null,
                            $_POST['department_name'] ?? null,
                            !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null,
                            !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null
                        );
                        
                        if ($department->save()) {
                            $conn->commit();
                            header('Location: /src/html/departments/run_departments.php?success=updated');
                            exit;
                        }
                        break;

                    case 'delete':
                        if (!isset($_POST['department_id'])) {
                            throw new Exception("ID de departament no proporcionat");
                        }
                        
                        $department = new self((int)$_POST['department_id']);
                        
                        if ($department->destroy()) {
                            $conn->commit();
                            header('Location: /src/html/departments/run_departments.php?success=deleted');
                            exit;
                        }
                        break;

                    default:
                        throw new Exception('Acció no vàlida');
                }
                
            } catch (Exception $e) {
                if (isset($conn)) {
                    $conn->rollback();
                }
                header('Location: /src/html/departments/run_departments.php?error=' . urlencode($e->getMessage()));
                exit;
            } finally {
                if (isset($conn)) {
                    $conn->close();
                }
            }
        }
    }
}

if (basename($_SERVER['PHP_SELF']) === 'Department.php') {
    Department::handleAction();
}
?> 