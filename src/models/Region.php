<?php

namespace App\models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\config\Database;
use Exception;
use Faker\Factory;

class Region extends Model {
    protected static $table = 'regions';
    protected static $primaryKey = 'region_id';

    private ?int $REGION_ID = null;
    private ?string $REGION_NAME = null;

    public function __construct(
        ?int $REGION_ID = null,
        ?string $REGION_NAME = null
    ) {
        $this->REGION_ID = $REGION_ID;
        $this->REGION_NAME = $REGION_NAME;
    }

    public function getRegionId(): ?int {
        return $this->REGION_ID;
    }

    public function setRegionId(?int $REGION_ID): void {
        $this->REGION_ID = $REGION_ID;
    }

    public function getRegionName(): ?string {
        return $this->REGION_NAME;
    }

    public function setRegionName(?string $REGION_NAME): void {
        $this->REGION_NAME = $REGION_NAME;
    }

    public function save() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $table = static::$table;

            if (!isset($this->REGION_ID)) {
                throw new Exception("ID regió no informat.");
            }

            $sql = "INSERT INTO $table (
                    REGION_ID, REGION_NAME
                ) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE
                    REGION_NAME = VALUES(REGION_NAME)";

            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparant la consulta: " . $conn->error);
            }

            $stmt->bind_param("is", 
                $this->REGION_ID,
                $this->REGION_NAME
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

            if (!isset($this->REGION_ID)) {
                throw new Exception("ID regió no informat.");
            }

            $stmt = $conn->prepare("SELECT region_id FROM $table WHERE region_id = ?");
            $stmt->bind_param("i", $this->REGION_ID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("La regió no existeix.");
            }

            $stmt = $conn->prepare("DELETE FROM $table WHERE region_id = ?");
            $stmt->bind_param("i", $this->REGION_ID);
            
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error eliminant la regió: " . $stmt->error);
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
                        
                        $result = $conn->query("SELECT MAX(region_id) as max_id FROM regions");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $regions = [
                            'Europe',
                            'Americas',
                            'Asia',
                            'Middle East and Africa',
                            'Oceania',
                            'Antarctica',
                            'Caribbean',
                            'Central America',
                            'North America',
                            'South America'
                        ];
                        
                        $region = new self(
                            $nextId,
                            $faker->randomElement($regions)
                        );
                        
                        if ($region->save()) {
                            $conn->commit();
                            header('Location: /src/html/regions/run_regions.php?success=created');
                            exit;
                        }
                        break;

                    case 'create':
                        $result = $conn->query("SELECT MAX(region_id) as max_id FROM regions");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $region = new self(
                            $nextId,
                            $_POST['region_name'] ?? null
                        );
                        
                        if ($region->save()) {
                            $conn->commit();
                            header('Location: /src/html/regions/run_regions.php?success=created');
                            exit;
                        }
                        break;

                    case 'update':
                        $region = new self(
                            isset($_POST['region_id']) ? (int)$_POST['region_id'] : null,
                            $_POST['region_name'] ?? null
                        );
                        
                        if ($region->save()) {
                            $conn->commit();
                            header('Location: /src/html/regions/run_regions.php?success=updated');
                            exit;
                        }
                        break;

                    case 'delete':
                        if (!isset($_POST['region_id'])) {
                            throw new Exception("ID de regió no proporcionat");
                        }
                        
                        $region = new self((int)$_POST['region_id']);
                        
                        if ($region->destroy()) {
                            $conn->commit();
                            header('Location: /src/html/regions/run_regions.php?success=deleted');
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
                header('Location: /src/html/regions/run_regions.php?error=' . urlencode($e->getMessage()));
                exit;
            } finally {
                if (isset($conn)) {
                    $conn->close();
                }
            }
        }
    }
}

if (basename($_SERVER['PHP_SELF']) === 'Region.php') {
    Region::handleAction();
}
?> 