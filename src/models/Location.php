<?php

namespace App\models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\config\Database;
use Exception;
use Faker\Factory;

class Location extends Model {
    protected static $table = 'locations';
    protected static $primaryKey = 'LOCATION_ID';

    private ?int $LOCATION_ID = null;
    private ?string $STREET_ADDRESS = null;
    private ?string $POSTAL_CODE = null;
    private ?string $CITY = null;
    private ?string $STATE_PROVINCE = null;
    private ?string $COUNTRY_ID = null;

    public function __construct(
        ?int $LOCATION_ID = null,
        ?string $STREET_ADDRESS = null,
        ?string $POSTAL_CODE = null,
        ?string $CITY = null,
        ?string $STATE_PROVINCE = null,
        ?string $COUNTRY_ID = null
    ) {
        $this->LOCATION_ID = $LOCATION_ID;
        $this->STREET_ADDRESS = $STREET_ADDRESS;
        $this->POSTAL_CODE = $POSTAL_CODE;
        $this->CITY = $CITY;
        $this->STATE_PROVINCE = $STATE_PROVINCE;
        $this->COUNTRY_ID = $COUNTRY_ID;
    }

    // Getters y Setters
    public function getLocationId(): ?int {
        return $this->LOCATION_ID;
    }

    public function setLocationId(?int $LOCATION_ID): void {
        $this->LOCATION_ID = $LOCATION_ID;
    }

    public function getStreetAddress(): ?string {
        return $this->STREET_ADDRESS;
    }

    public function setStreetAddress(?string $STREET_ADDRESS): void {
        $this->STREET_ADDRESS = $STREET_ADDRESS;
    }

    public function getPostalCode(): ?string {
        return $this->POSTAL_CODE;
    }

    public function setPostalCode(?string $POSTAL_CODE): void {
        $this->POSTAL_CODE = $POSTAL_CODE;
    }

    public function getCity(): ?string {
        return $this->CITY;
    }

    public function setCity(?string $CITY): void {
        $this->CITY = $CITY;
    }

    public function getStateProvince(): ?string {
        return $this->STATE_PROVINCE;
    }

    public function setStateProvince(?string $STATE_PROVINCE): void {
        $this->STATE_PROVINCE = $STATE_PROVINCE;
    }

    public function getCountryId(): ?string {
        return $this->COUNTRY_ID;
    }

    public function setCountryId(?string $COUNTRY_ID): void {
        $this->COUNTRY_ID = $COUNTRY_ID;
    }

    public function save() {
        try {
            // Validaciones
            if (strlen($this->STREET_ADDRESS) > 40) {
                throw new Exception("L'adreça no pot tenir més de 40 caràcters");
            }
            if (strlen($this->POSTAL_CODE) > 12) {
                throw new Exception("El codi postal no pot tenir més de 12 caràcters");
            }
            if (strlen($this->CITY) > 30) {
                throw new Exception("La ciutat no pot tenir més de 30 caràcters");
            }
            if (strlen($this->STATE_PROVINCE) > 25) {
                throw new Exception("La província no pot tenir més de 25 caràcters");
            }
            if (!preg_match('/^[A-Z]{2}$/', $this->COUNTRY_ID)) {
                throw new Exception("L'ID del país ha de ser de 2 lletres majúscules");
            }

            $db = new Database();
            $conn = $db->getConnection();
            $table = static::$table;

            // Verificar que el país existe
            if ($this->COUNTRY_ID !== null) {
                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM countries WHERE COUNTRY_ID = ?");
                if (!$stmt) {
                    throw new Exception("Error preparant la consulta de verificació del país");
                }
                
                $stmt->bind_param("s", $this->COUNTRY_ID);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] == 0) {
                    throw new Exception("El país amb ID '{$this->COUNTRY_ID}' no existeix");
                }
                $stmt->close();
            }

            $sql = "INSERT INTO $table (
                    LOCATION_ID, STREET_ADDRESS, POSTAL_CODE, CITY, 
                    STATE_PROVINCE, COUNTRY_ID
                ) VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    STREET_ADDRESS = VALUES(STREET_ADDRESS),
                    POSTAL_CODE = VALUES(POSTAL_CODE),
                    CITY = VALUES(CITY),
                    STATE_PROVINCE = VALUES(STATE_PROVINCE),
                    COUNTRY_ID = VALUES(COUNTRY_ID)";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $conn->error);
            }

            $stmt->bind_param("isssss", 
                $this->LOCATION_ID,
                $this->STREET_ADDRESS,
                $this->POSTAL_CODE,
                $this->CITY,
                $this->STATE_PROVINCE,
                $this->COUNTRY_ID
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

            if (!isset($this->LOCATION_ID)) {
                throw new Exception("ID de localització no proporcionat");
            }

            $stmt = $conn->prepare("DELETE FROM $table WHERE LOCATION_ID = ?");
            $stmt->bind_param("i", $this->LOCATION_ID);
            $result = $stmt->execute();

            if (!$result) {
                throw new Exception("Error eliminant la localització: " . $stmt->error);
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
                    case 'create':
                    case 'update':
                        // Validaciones
                        if (empty($_POST['city'])) {
                            throw new Exception("La ciutat és obligatòria");
                        }
                        if (empty($_POST['country_id'])) {
                            throw new Exception("L'ID del país és obligatori");
                        }
                        

                        $location = new self(
                            isset($_POST['location_id']) ? (int)$_POST['location_id'] : null,
                            $_POST['street_address'] ?? null,
                            $_POST['postal_code'] ?? null,
                            $_POST['city'] ?? null,
                            $_POST['state_province'] ?? null,
                            $_POST['country_id'] ?? null
                        );

                        if ($location->save()) {
                            $conn->commit();
                            header('Location: /src/html/locations/run_locations.php?success=created');
                            exit;
                        }
                        break;

                    case 'delete':
                        if (!isset($_POST['location_id'])) {
                            throw new Exception("ID de localización no proporcionado");
                        }

                        $location = new self((int)$_POST['location_id']);

                        if ($location->destroy()) {
                            $conn->commit();
                            header('Location: /src/html/locations/run_locations.php?success=deleted');
                            exit;
                        }
                        break;

                    case 'faker':
                        $faker = Factory::create('ca_ES');
                        
                        $result = $conn->query("SELECT MAX(LOCATION_ID) as max_id FROM locations");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $result = $conn->query("SELECT COUNTRY_ID FROM countries ORDER BY RAND() LIMIT 1");
                        
                        $countryId = $result->fetch_assoc()['COUNTRY_ID'] ?? 'ES';
                        
                        $location = new self(
                            $nextId,
                            $faker->streetAddress(),
                            $faker->postcode(),
                            $faker->city(),
                            $faker->state(),
                            $countryId
                        );
                        
                        if ($location->save()) {
                            $conn->commit();
                            header('Location: /src/html/locations/run_locations.php?success=created');
                            exit;
                        }
                        break;

                    default:
                        throw new Exception('Acción no válida');
                }

            } catch (Exception $e) {
                if (isset($conn)) {
                    $conn->rollback();
                }
                header('Location: /src/html/locations/run_locations.php?error=' . urlencode($e->getMessage()));
                exit;
            } finally {
                if (isset($conn)) {
                    $conn->close();
                }
            }
        }
    }
}

if (basename($_SERVER['PHP_SELF']) === 'Location.php') {
    Location::handleAction();
} 