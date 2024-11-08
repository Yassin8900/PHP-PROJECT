<?php

namespace App\models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\config\Database;
use Exception;
use Faker\Factory;

class Country extends Model {
    protected static $table = 'countries';
    protected static $primaryKey = 'COUNTRY_ID';

    private ?string $COUNTRY_ID = null;
    private ?string $COUNTRY_NAME = null;
    private ?int $REGION_ID = null;

    public function __construct(
        ?string $COUNTRY_ID = null,
        ?string $COUNTRY_NAME = null,
        ?int $REGION_ID = null
    ) {
        $this->COUNTRY_ID = $COUNTRY_ID;
        $this->COUNTRY_NAME = $COUNTRY_NAME;
        $this->REGION_ID = $REGION_ID;
    }

    // Getters y Setters
    public function getCountryId(): ?string {
        return $this->COUNTRY_ID;
    }

    public function setCountryId(?string $COUNTRY_ID): void {
        $this->COUNTRY_ID = $COUNTRY_ID;
    }

    public function getCountryName(): ?string {
        return $this->COUNTRY_NAME;
    }

    public function setCountryName(?string $COUNTRY_NAME): void {
        $this->COUNTRY_NAME = $COUNTRY_NAME;
    }

    public function getRegionId(): ?int {
        return $this->REGION_ID;
    }

    public function setRegionId(?int $REGION_ID): void {
        $this->REGION_ID = $REGION_ID;
    }

    public function save() {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Verificar que la región existe
            if ($this->REGION_ID !== null) {
                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM regions WHERE REGION_ID = ?");
                if (!$stmt) {
                    throw new Exception("Error preparant la consulta de verificació de la regió");
                }
                
                $stmt->bind_param("i", $this->REGION_ID);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] == 0) {
                    throw new Exception("La regió amb ID '{$this->REGION_ID}' no existeix");
                }
                $stmt->close();
            }

            $table = static::$table;
            $sql = "INSERT INTO $table (
                    COUNTRY_ID, COUNTRY_NAME, REGION_ID
                ) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    COUNTRY_NAME = VALUES(COUNTRY_NAME),
                    REGION_ID = VALUES(REGION_ID)";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparant la consulta: " . $conn->error);
            }

            $stmt->bind_param("ssi", 
                $this->COUNTRY_ID,
                $this->COUNTRY_NAME,
                $this->REGION_ID
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

            if (!isset($this->COUNTRY_ID)) {
                throw new Exception("ID de país no proporcionat.");
            }

            $stmt = $conn->prepare("DELETE FROM $table WHERE COUNTRY_ID = ?");
            $stmt->bind_param("s", $this->COUNTRY_ID);
            $result = $stmt->execute();

            if (!$result) {
                throw new Exception("Error eliminant el país: " . $stmt->error);
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
                        if (empty($_POST['country_name'])) {
                            throw new Exception("El nom del país és obligatori");
                        }
                        if (empty($_POST['region_id']) || $_POST['region_id'] <= 0) {
                            throw new Exception("L'ID de la regió ha de ser un número positiu");
                        }
                        if (isset($_POST['country_id']) && !preg_match('/^[A-Z]{2}$/', $_POST['country_id'])) {
                            throw new Exception("L'ID del país ha de ser de 2 lletres majúscules");
                        }
                        
                        $country = new self(
                            $_POST['country_id'] ?? null,
                            $_POST['country_name'] ?? null,
                            !empty($_POST['region_id']) ? (int)$_POST['region_id'] : null
                        );
                        
                        if ($country->save()) {
                            $conn->commit();
                            header('Location: /src/html/countries/run_countries.php?success=created');
                            exit;
                        }
                        break;

                    case 'delete':
                        if (!isset($_POST['country_id'])) {
                            throw new Exception("ID de país no proporcionado");
                        }
                        
                        $country = new self(
                            $_POST['country_id'],
                            null,
                            null
                        );
                        
                        if ($country->destroy()) {
                            $conn->commit();
                            header('Location: /src/html/countries/run_countries.php?success=deleted');
                            exit;
                        }
                        break;

                    case 'faker':
                        $faker = Factory::create('es_ES');
                        
                        // Generar un ID de país único de 2 letras
                        do {
                            $countryId = strtoupper($faker->lexify('??'));
                            $exists = $conn->query("SELECT COUNT(*) as count FROM countries 
                                                  WHERE COUNTRY_ID = '$countryId'")->fetch_assoc()['count'] > 0;
                        } while ($exists);
                        
                        $country = new self(
                            $countryId,
                            $faker->country,
                            $faker->numberBetween(1, 4)
                        );
                        
                        if ($country->save()) {
                            $conn->commit();
                            header('Location: /src/html/countries/run_countries.php?success=created');
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
                header('Location: /src/html/countries/run_countries.php?error=' . urlencode($e->getMessage()));
                exit;
            } finally {
                if (isset($conn)) {
                    $conn->close();
                }
            }
        }
    }
}

if (basename($_SERVER['PHP_SELF']) === 'Country.php') {
    Country::handleAction();
} 