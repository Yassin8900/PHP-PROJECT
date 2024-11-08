<?php

namespace App\models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\config\Database;
use Exception;
use Faker\Factory;

class Job extends Model {
    protected static $table = 'jobs';
    protected static $primaryKey = 'job_id';

    private ?string $JOB_ID = null;
    private ?string $JOB_TITLE = null;
    private ?float $MIN_SALARY = null;
    private ?float $MAX_SALARY = null;

    public function __construct(
        ?string $JOB_ID = null,
        ?string $JOB_TITLE = null,
        ?float $MIN_SALARY = null,
        ?float $MAX_SALARY = null
    ) {
        $this->JOB_ID = $JOB_ID;
        $this->JOB_TITLE = $JOB_TITLE;
        $this->MIN_SALARY = $MIN_SALARY;
        $this->MAX_SALARY = $MAX_SALARY;
    }

    // Getters y Setters
    public function getJobId(): ?string {
        return $this->JOB_ID;
    }

    public function setJobId(?string $JOB_ID): void {
        $this->JOB_ID = $JOB_ID;
    }

    public function getJobTitle(): ?string {
        return $this->JOB_TITLE;
    }

    public function setJobTitle(?string $JOB_TITLE): void {
        $this->JOB_TITLE = $JOB_TITLE;
    }

    public function getMinSalary(): ?float {
        return $this->MIN_SALARY;
    }

    public function setMinSalary(?float $MIN_SALARY): void {
        $this->MIN_SALARY = $MIN_SALARY;
    }

    public function getMaxSalary(): ?float {
        return $this->MAX_SALARY;
    }

    public function setMaxSalary(?float $MAX_SALARY): void {
        $this->MAX_SALARY = $MAX_SALARY;
    }

    public function save() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $table = static::$table;

            if (!isset($this->JOB_ID)) {
                throw new Exception("ID del treball no informat.");
            }

            // Validar que el salari mínim sigui menor que el màxim
            if ($this->MIN_SALARY !== null && $this->MAX_SALARY !== null && 
                $this->MIN_SALARY > $this->MAX_SALARY) {
                throw new Exception("El salari mínim no pot ser major que el màxim.");
            }

            $sql = "INSERT INTO $table (
                    JOB_ID, JOB_TITLE, MIN_SALARY, MAX_SALARY
                ) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    JOB_TITLE = VALUES(JOB_TITLE),
                    MIN_SALARY = VALUES(MIN_SALARY),
                    MAX_SALARY = VALUES(MAX_SALARY)";

            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparant la consulta: " . $conn->error);
            }

            $stmt->bind_param("ssdd", 
                $this->JOB_ID,
                $this->JOB_TITLE,
                $this->MIN_SALARY,
                $this->MAX_SALARY
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

            if (!isset($this->JOB_ID)) {
                throw new Exception("ID del treball no informat.");
            }

            // Verificar si el treball existeix
            $stmt = $conn->prepare("SELECT JOB_ID FROM $table WHERE JOB_ID = ?");
            $stmt->bind_param("s", $this->JOB_ID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("El treball no existeix.");
            }

            // Verificar si hi ha empleats utilitzant aquest treball
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM employees WHERE job_id = ?");
            $stmt->bind_param("s", $this->JOB_ID);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                throw new Exception("No es pot eliminar el treball perquè hi ha empleats assignats.");
            }

            $stmt = $conn->prepare("DELETE FROM $table WHERE JOB_ID = ?");
            $stmt->bind_param("s", $this->JOB_ID);
            
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error eliminant el treball: " . $stmt->error);
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
                        
                        // Generar un ID de treball únic
                        do {
                            $jobId = strtoupper($faker->lexify('??_????'));
                            $exists = $conn->query("SELECT COUNT(*) as count FROM jobs 
                                                  WHERE JOB_ID = '$jobId'")->fetch_assoc()['count'] > 0;
                        } while ($exists);
                        
                        $minSalary = $faker->numberBetween(20000, 50000);
                        $maxSalary = $faker->numberBetween($minSalary, 100000);
                        
                        $job = new self(
                            $jobId,
                            $faker->jobTitle(),
                            $minSalary,
                            $maxSalary
                        );
                        
                        if ($job->save()) {
                            $conn->commit();
                            header('Location: /src/html/jobs/run_jobs.php?success=created');
                            exit;
                        }
                        break;

                    case 'create':
                        $job = new self(
                            strtoupper($_POST['job_id'] ?? ''),
                            $_POST['job_title'] ?? null,
                            isset($_POST['min_salary']) ? (float)$_POST['min_salary'] : null,
                            isset($_POST['max_salary']) ? (float)$_POST['max_salary'] : null
                        );
                        
                        if ($job->save()) {
                            $conn->commit();
                            header('Location: /src/html/jobs/run_jobs.php?success=created');
                            exit;
                        }
                        break;

                    case 'update':
                        $job = new self(
                            strtoupper($_POST['job_id'] ?? ''),
                            $_POST['job_title'] ?? null,
                            isset($_POST['min_salary']) ? (float)$_POST['min_salary'] : null,
                            isset($_POST['max_salary']) ? (float)$_POST['max_salary'] : null
                        );
                        
                        if ($job->save()) {
                            $conn->commit();
                            header('Location: /src/html/jobs/run_jobs.php?success=updated');
                            exit;
                        }
                        break;

                    case 'delete':
                        if (!isset($_POST['job_id'])) {
                            throw new Exception("ID del treball no proporcionat");
                        }
                        
                        $job = new self($_POST['job_id']);
                        
                        if ($job->destroy()) {
                            $conn->commit();
                            header('Location: /src/html/jobs/run_jobs.php?success=deleted');
                            exit;
                        }
                        break;

                    default:
                        throw new Exception("Acció no vàlida");
                }
                
            } catch (Exception $e) {
                if (isset($conn)) {
                    $conn->rollback();
                }
                header('Location: /src/html/jobs/run_jobs.php?error=' . urlencode($e->getMessage()));
                exit;
            } finally {
                if (isset($conn)) {
                    $conn->close();
                }
            }
        }
    }
}

if (basename($_SERVER['PHP_SELF']) === 'Job.php') {
    Job::handleAction();
} 
?>