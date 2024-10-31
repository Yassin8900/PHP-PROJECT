<?php

namespace App\models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\config\Database;
use Exception;
use Faker\Factory;

class Customer extends Model {
    protected static $table = 'customers';
    protected static $primaryKey = 'customer_id';

    public function __construct(
        public ?int $CUSTOMER_ID = null,
        public ?string $CUST_FIRST_NAME = null,
        public ?string $CUST_LAST_NAME = null,
        public ?string $CUST_STREET_ADDRESS = null,
        public ?string $CUST_POSTAL_CODE = null,
        public ?string $CUST_CITY = null,
        public ?string $CUST_STATE = null,
        public ?string $CUST_COUNTRY = null,
        public ?string $PHONE_NUMBERS = null,
        public ?string $NLS_LANGUAGE = null,
        public ?string $NLS_TERRITORY = null,
        public ?float $CREDIT_LIMIT = null,
        public ?string $CUST_EMAIL = null,
        public ?int $ACCOUNT_MGR_ID = null,
        public ?string $CUST_GEO_LOCATION = null,
        public ?string $DATE_OF_BIRTH = null,
        public ?string $MARITAL_STATUS = null,
        public ?string $GENDER = null,
        public ?string $INCOME_LEVEL = null
    ) {}

    public function save() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $conn->autocommit(FALSE);
            $table = static::$table;

            if (!isset($this->CUSTOMER_ID)) {
                throw new Exception("ID client no informat.");
            }

            if ($this->CREDIT_LIMIT > 5000) {
                throw new Exception("El límit de crèdit no pot ser superior a 5000");
            }

            $sql = "INSERT INTO $table (
                    CUSTOMER_ID, CUST_FIRST_NAME, CUST_LAST_NAME, CUST_STREET_ADDRESS,
                    CUST_POSTAL_CODE, CUST_CITY, CUST_STATE, CUST_COUNTRY,
                    PHONE_NUMBERS, NLS_LANGUAGE, NLS_TERRITORY, CREDIT_LIMIT,
                    CUST_EMAIL, ACCOUNT_MGR_ID, CUST_GEO_LOCATION, DATE_OF_BIRTH,
                    MARITAL_STATUS, GENDER, INCOME_LEVEL
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    CUST_FIRST_NAME = VALUES(CUST_FIRST_NAME),
                    CUST_LAST_NAME = VALUES(CUST_LAST_NAME),
                    CUST_STREET_ADDRESS = VALUES(CUST_STREET_ADDRESS),
                    CUST_POSTAL_CODE = VALUES(CUST_POSTAL_CODE),
                    CUST_CITY = VALUES(CUST_CITY),
                    CUST_STATE = VALUES(CUST_STATE),
                    CUST_COUNTRY = VALUES(CUST_COUNTRY),
                    PHONE_NUMBERS = VALUES(PHONE_NUMBERS),
                    NLS_LANGUAGE = VALUES(NLS_LANGUAGE),
                    NLS_TERRITORY = VALUES(NLS_TERRITORY),
                    CREDIT_LIMIT = VALUES(CREDIT_LIMIT),
                    CUST_EMAIL = VALUES(CUST_EMAIL),
                    ACCOUNT_MGR_ID = VALUES(ACCOUNT_MGR_ID),
                    CUST_GEO_LOCATION = VALUES(CUST_GEO_LOCATION),
                    DATE_OF_BIRTH = VALUES(DATE_OF_BIRTH),
                    MARITAL_STATUS = VALUES(MARITAL_STATUS),
                    GENDER = VALUES(GENDER),
                    INCOME_LEVEL = VALUES(INCOME_LEVEL)";

            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                $conn->rollback();
                throw new Exception("Error preparant la consulta: " . $conn->error);
            }

            $stmt->bind_param("issssssssssdsisssss", 
                $this->CUSTOMER_ID,
                $this->CUST_FIRST_NAME,
                $this->CUST_LAST_NAME,
                $this->CUST_STREET_ADDRESS,
                $this->CUST_POSTAL_CODE,
                $this->CUST_CITY,
                $this->CUST_STATE,
                $this->CUST_COUNTRY,
                $this->PHONE_NUMBERS,
                $this->NLS_LANGUAGE,
                $this->NLS_TERRITORY,
                $this->CREDIT_LIMIT,
                $this->CUST_EMAIL,
                $this->ACCOUNT_MGR_ID,
                $this->CUST_GEO_LOCATION,
                $this->DATE_OF_BIRTH,
                $this->MARITAL_STATUS,
                $this->GENDER,
                $this->INCOME_LEVEL
            );

            $result = $stmt->execute();
            
            if (!$result) {
                $conn->rollback();
                throw new Exception("Error executant la consulta: " . $stmt->error);
            }

            $conn->commit();
            return true;

        } catch (Exception $e) {
            if (isset($conn)) {
                $conn->rollback();
            }
            throw new Exception("Error en save(): " . $e->getMessage());
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conn)) {
                $conn->close();
            }
        }
    }

    public function destroy(): bool {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $sql = "DELETE FROM customers WHERE customer_id = ?";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparant la consulta: " . $conn->error);
            }
            
            $stmt->bind_param("i", $this->CUSTOMER_ID);
            
            if (!$stmt->execute()) {
                throw new Exception("Error executant la consulta: " . $stmt->error);
            }
            
            return $stmt->affected_rows > 0;
            
        } catch (Exception $e) {
            error_log("Error en destroy(): " . $e->getMessage());
            throw $e;
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conn)) {
                $conn->close();
            }
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
                        
                        $result = $conn->query("SELECT MAX(customer_id) as max_id FROM customers");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $firstName = substr($faker->firstName, 0, 20);
                        $lastName = substr($faker->lastName, 0, 20);
                        $streetAddress = substr($faker->streetAddress, 0, 100);
                        $postalCode = substr($faker->postcode, 0, 10);
                        $city = substr($faker->city, 0, 20);
                        $state = substr($faker->state, 0, 20);
                        $country = substr('Spain', 0, 20);
                        $phone = substr($faker->phoneNumber, 0, 100);
                        $email = substr($faker->email, 0, 30);
                        
                        // Crear un objeto JSON válido para la geolocalización
                        $geoLocation = json_encode([
                            "type" => "Point",
                            "coordinates" => [
                                $faker->longitude,
                                $faker->latitude
                            ]
                        ]);
                        
                        $customer = new self(
                            $nextId,
                            $firstName,
                            $lastName,
                            $streetAddress,
                            $postalCode,
                            $city,
                            $state,
                            $country,
                            $phone,
                            'es',  // NLS_LANGUAGE (3 chars)
                            'ES',  // NLS_TERRITORY (30 chars max)
                            $faker->randomFloat(2, 0, 5000), // CREDIT_LIMIT max 5000
                            $email,
                            $faker->numberBetween(145, 179),
                            $geoLocation,
                            $faker->date('Y-m-d', '-18 years'),
                            $faker->randomElement(['single', 'married']), // Solo 'single' o 'married' permitidos
                            $faker->randomElement(['M', 'F']),
                            $faker->randomElement(['A: Below 30,000', 'B: 30,000 - 49,999', 'C: 50,000 - 69,999', 
                                                 'D: 70,000 - 89,999', 'E: 90,000 - 109,999', 'F: 110,000 - 129,999', 
                                                 'G: 130,000 - 149,999', 'H: 150,000 - 169,999', 'I: 170,000 - 189,999', 
                                                 'J: 190,000 - 249,999', 'K: 250,000 - 299,999', 'L: 300,000 and above'])
                        );
                        
                        if ($customer->save()) {
                            $conn->commit();
                            header('Location: /src/html/run_customers.php?success=created');
                            exit;
                        }
                        break;

                    case 'create':
                    case 'update':
                        
                        if (empty($_POST['cust_street_address'])) {
                            throw new Exception("L'adreça és obligatòria");
                        }

                        $customer = new self(
                            $_POST['customer_id'] ? (int)$_POST['customer_id'] : null,
                            $_POST['cust_first_name'] ?? null,
                            $_POST['cust_last_name'] ?? null,
                            $_POST['cust_street_address'] ?? null,
                            $_POST['cust_postal_code'] ?? null,
                            $_POST['cust_city'] ?? null,
                            $_POST['cust_state'] ?? null,
                            $_POST['cust_country'] ?? null,
                            $_POST['phone_numbers'] ?? null,
                            $_POST['nls_language'] ?? null,
                            $_POST['nls_territory'] ?? null,
                            isset($_POST['credit_limit']) ? (float)$_POST['credit_limit'] : null,
                            $_POST['cust_email'] ?? null,
                            $_POST['account_mgr_id'] ? (int)$_POST['account_mgr_id'] : null,
                            $_POST['cust_geo_location'] ?? null,
                            $_POST['date_of_birth'] ?? null,
                            strtolower($_POST['marital_status'] ?? ''),
                            $_POST['gender'] ?? null,
                            $_POST['income_level'] ?? null
                        );
                        

                        if ($customer->save()) {
                            $action = ($_POST['action'] === 'create') ? 'created' : 'updated';
                            header("Location: /src/html/run_customers.php?success=$action");
                            exit;
                        }
                        break;
                    case 'delete':
                        // Obtener el ID del cliente del POST
                        $customerId = $_POST['customer_id'] ?? null;
                        
                        if (!$customerId) {
                            throw new Exception('ID de client no proporcionat');
                        }

                        $db = new Database();
                        $conn = $db->getConnection();

                        // Iniciamos una transacción
                        $conn->begin_transaction();

                        try {
                            
                            $sqlDelete = "DELETE FROM customers WHERE customer_id = ?";
                            $stmtDelete = $conn->prepare($sqlDelete);
                            $stmtDelete->bind_param("i", $customerId);
                            
                            if (!$stmtDelete->execute()) {
                                throw new Exception("Error al eliminar el client: " . $stmtDelete->error);
                            }

                            
                            $conn->commit();
                            header('Location: /src/html/run_customers.php?success=deleted');
                            exit;

                        } catch (Exception $e) {
                            
                            $conn->rollback();
                            throw new Exception("Error en eliminar el client: " . $e->getMessage());
                        } finally {
                            if (isset($stmtDelete)) {
                                $stmtDelete->close();
                            }
                            if (isset($conn)) {
                                $conn->close();
                            }
                        }
                        break;
                    case 'delete':
                        // Obtener el ID del cliente del POST
                        $customerId = $_POST['customer_id'] ?? null;
                        
                        if (!$customerId) {
                            throw new Exception('ID de client no proporcionat');
                        }

                        $db = new Database();
                        $conn = $db->getConnection();

                        // Iniciamos una transacción
                        $conn->begin_transaction();

                        try {
                            
                            $sqlDelete = "DELETE FROM customers WHERE customer_id = ?";
                            $stmtDelete = $conn->prepare($sqlDelete);
                            $stmtDelete->bind_param("i", $customerId);
                            
                            if (!$stmtDelete->execute()) {
                                throw new Exception("Error al eliminar el client: " . $stmtDelete->error);
                            }

                            
                            $conn->commit();
                            header('Location: /src/html/run_customers.php?success=deleted');
                            exit;

                        } catch (Exception $e) {
                            
                            $conn->rollback();
                            throw new Exception("Error en eliminar el client: " . $e->getMessage());
                        } finally {
                            if (isset($stmtDelete)) {
                                $stmtDelete->close();
                            }
                            if (isset($conn)) {
                                $conn->close();
                            }
                        }
                        break;

                    default:
                        throw new Exception('Acció no vàlida');
                }
            } catch (Exception $e) {
                header('Location: /src/html/run_customers.php?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }
}


Customer::handleAction();
?>