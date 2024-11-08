<?php

namespace App\models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\config\Database;
use Exception;
use Faker\Factory;

class Warehouse extends Model {
    protected static $table = 'warehouses';
    protected static $primaryKey = 'warehouse_id';

    private ?int $warehouse_id = null;
    private ?string $warehouse_name = null;
    private ?int $location_id = null;
    private ?string $warehouse_spec = null;
    private ?string $wh_geo_location = null;

    public function __construct(
        ?int $warehouse_id = null,
        ?string $warehouse_name = null,
        ?int $location_id = null,
        ?string $warehouse_spec = null,
        ?string $wh_geo_location = null
    ) {
        $this->warehouse_id = $warehouse_id;
        $this->warehouse_name = $warehouse_name;
        $this->location_id = $location_id;
        $this->warehouse_spec = $warehouse_spec;
        $this->wh_geo_location = $wh_geo_location;
    }

    // Getters y Setters
    public function getWarehouseId(): ?int {
        return $this->warehouse_id;
    }

    public function setWarehouseId(?int $warehouse_id): void {
        $this->warehouse_id = $warehouse_id;
    }

    public function getWarehouseName(): ?string {
        return $this->warehouse_name;
    }

    public function setWarehouseName(?string $warehouse_name): void {
        $this->warehouse_name = $warehouse_name;
    }

    public function getLocationId(): ?int {
        return $this->location_id;
    }

    public function setLocationId(?int $location_id): void {
        $this->location_id = $location_id;
    }

    public function getWarehouseSpec(): ?string {
        return $this->warehouse_spec;
    }

    public function setWarehouseSpec(?string $warehouse_spec): void {
        $this->warehouse_spec = $warehouse_spec;
    }

    public function getWhGeoLocation(): ?string {
        return $this->wh_geo_location;
    }

    public function setWhGeoLocation(?string $wh_geo_location): void {
        $this->wh_geo_location = $wh_geo_location;
    }

    public function save() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $table = static::$table;

            if (!isset($this->warehouse_id)) {
                throw new Exception("ID magatzem no informat.");
            }

            // Verificar si el nombre ya existe
            $stmt = $conn->prepare("SELECT warehouse_id FROM $table WHERE warehouse_name = ? AND warehouse_id != ?");
            $stmt->bind_param("si", $this->warehouse_name, $this->warehouse_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                throw new Exception("El nom del magatzem ja està en ús.");
            }

            // Verificar existencia de location_id
            if ($this->location_id) {
                $stmt = $conn->prepare("SELECT location_id FROM locations WHERE location_id = ?");
                $stmt->bind_param("i", $this->location_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception("La localització no existeix.");
                }
            }

            $sql = "INSERT INTO $table (
                    warehouse_id, warehouse_name, location_id, 
                    warehouse_spec, wh_geo_location
                ) VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    warehouse_name = VALUES(warehouse_name),
                    location_id = VALUES(location_id),
                    warehouse_spec = VALUES(warehouse_spec),
                    wh_geo_location = VALUES(wh_geo_location)";

            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparant la consulta: " . $conn->error);
            }

            $stmt->bind_param("isis", 
                $this->warehouse_id,
                $this->warehouse_name,
                $this->location_id,
                $this->warehouse_spec,
                $this->wh_geo_location
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

            if (!isset($this->warehouse_id)) {
                throw new Exception("ID magatzem no informat.");
            }

            $stmt = $conn->prepare("SELECT warehouse_id FROM $table WHERE warehouse_id = ?");
            $stmt->bind_param("i", $this->warehouse_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("El magatzem no existeix.");
            }

            $stmt = $conn->prepare("DELETE FROM $table WHERE warehouse_id = ?");
            $stmt->bind_param("i", $this->warehouse_id);
            
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error eliminant el magatzem: " . $stmt->error);
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
                        
                        $result = $conn->query("SELECT MAX(warehouse_id) as max_id FROM warehouses");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $warehouse = new self(
                            $nextId,
                            $faker->company() . " Warehouse",
                            $faker->numberBetween(1000, 3000),
                            $faker->sentence(4),
                            $faker->latitude() . ',' . $faker->longitude()
                        );
                        
                        if ($warehouse->save()) {
                            $conn->commit();
                            header('Location: /src/html/warehouses/run_warehouses.php?success=created');
                            exit;
                        }
                        break;

                    case 'create':
                        $result = $conn->query("SELECT MAX(warehouse_id) as max_id FROM warehouses");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $warehouse = new self(
                            $nextId,
                            $_POST['warehouse_name'] ?? null,
                            !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null,
                            $_POST['warehouse_spec'] ?? null,
                            $_POST['wh_geo_location'] ?? null
                        );
                        
                        if ($warehouse->save()) {
                            $conn->commit();
                            header('Location: /src/html/warehouses/run_warehouses.php?success=created');
                            exit;
                        }
                        break;

                    case 'update':
                        $warehouse = new self(
                            isset($_POST['warehouse_id']) ? (int)$_POST['warehouse_id'] : null,
                            $_POST['warehouse_name'] ?? null,
                            !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null,
                            $_POST['warehouse_spec'] ?? null,
                            $_POST['wh_geo_location'] ?? null
                        );
                        
                        if ($warehouse->save()) {
                            $conn->commit();
                            header('Location: /src/html/warehouses/run_warehouses.php?success=updated');
                            exit;
                        }
                        break;

                    case 'delete':
                        if (!isset($_POST['warehouse_id'])) {
                            throw new Exception("ID de magatzem no proporcionat");
                        }
                        
                        $warehouse = new self((int)$_POST['warehouse_id']);
                        
                        if ($warehouse->destroy()) {
                            $conn->commit();
                            header('Location: /src/html/warehouses/run_warehouses.php?success=deleted');
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
                header('Location: /src/html/warehouses/run_warehouses.php?error=' . urlencode($e->getMessage()));
                exit;
            } finally {
                if (isset($conn)) {
                    $conn->close();
                }
            }
        }
    }
}

if (basename($_SERVER['PHP_SELF']) === 'Warehouse.php') {
    Warehouse::handleAction();
} 

?>