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

    private ?int $CUSTOMER_ID = null;
    private ?string $CUST_FIRST_NAME = null;
    private ?string $CUST_LAST_NAME = null;
    private ?string $CUST_STREET_ADDRESS = null;
    private ?string $CUST_POSTAL_CODE = null;
    private ?string $CUST_CITY = null;
    private ?string $CUST_STATE = null;
    private ?string $CUST_COUNTRY = null;
    private ?string $PHONE_NUMBERS = null;
    private ?string $NLS_LANGUAGE = null;
    private ?string $NLS_TERRITORY = null;
    private ?float $CREDIT_LIMIT = null;
    private ?string $CUST_EMAIL = null;
    private ?int $ACCOUNT_MGR_ID = null;
    private ?string $CUST_GEO_LOCATION = null;
    private ?string $DATE_OF_BIRTH = null;
    private ?string $MARITAL_STATUS = null;
    private ?string $GENDER = null;
    private ?string $INCOME_LEVEL = null;

    public function __construct(
        ?int $CUSTOMER_ID = null,
        ?string $CUST_FIRST_NAME = null,
        ?string $CUST_LAST_NAME = null,
        ?string $CUST_STREET_ADDRESS = null,
        ?string $CUST_POSTAL_CODE = null,
        ?string $CUST_CITY = null,
        ?string $CUST_STATE = null,
        ?string $CUST_COUNTRY = null,
        ?string $PHONE_NUMBERS = null,
        ?string $NLS_LANGUAGE = null,
        ?string $NLS_TERRITORY = null,
        ?float $CREDIT_LIMIT = null,
        ?string $CUST_EMAIL = null,
        ?int $ACCOUNT_MGR_ID = null,
        ?string $CUST_GEO_LOCATION = null,
        ?string $DATE_OF_BIRTH = null,
        ?string $MARITAL_STATUS = null,
        ?string $GENDER = null,
        ?string $INCOME_LEVEL = null
    ) {
        $this->CUSTOMER_ID = $CUSTOMER_ID;
        $this->CUST_FIRST_NAME = $CUST_FIRST_NAME;
        $this->CUST_LAST_NAME = $CUST_LAST_NAME;
        $this->CUST_STREET_ADDRESS = $CUST_STREET_ADDRESS;
        $this->CUST_POSTAL_CODE = $CUST_POSTAL_CODE;
        $this->CUST_CITY = $CUST_CITY;
        $this->CUST_STATE = $CUST_STATE;
        $this->CUST_COUNTRY = $CUST_COUNTRY;
        $this->PHONE_NUMBERS = $PHONE_NUMBERS;
        $this->NLS_LANGUAGE = $NLS_LANGUAGE;
        $this->NLS_TERRITORY = $NLS_TERRITORY;
        $this->CREDIT_LIMIT = $CREDIT_LIMIT;
        $this->CUST_EMAIL = $CUST_EMAIL;
        $this->ACCOUNT_MGR_ID = $ACCOUNT_MGR_ID;
        $this->CUST_GEO_LOCATION = $CUST_GEO_LOCATION;
        $this->DATE_OF_BIRTH = $DATE_OF_BIRTH;
        $this->MARITAL_STATUS = $MARITAL_STATUS;
        $this->GENDER = $GENDER;
        $this->INCOME_LEVEL = $INCOME_LEVEL;
    }

    // Getters y Setters
    public function getCustomerId(): ?int {
        return $this->CUSTOMER_ID;
    }

    public function setCustomerId(?int $CUSTOMER_ID): void {
        $this->CUSTOMER_ID = $CUSTOMER_ID;
    }

    public function getCustFirstName(): ?string {
        return $this->CUST_FIRST_NAME;
    }

    public function setCustFirstName(?string $CUST_FIRST_NAME): void {
        $this->CUST_FIRST_NAME = $CUST_FIRST_NAME;
    }

    public function getCustLastName(): ?string {
        return $this->CUST_LAST_NAME;
    }

    public function setCustLastName(?string $CUST_LAST_NAME): void {
        $this->CUST_LAST_NAME = $CUST_LAST_NAME;
    }

    public function getCustStreetAddress(): ?string {
        return $this->CUST_STREET_ADDRESS;
    }

    public function setCustStreetAddress(?string $CUST_STREET_ADDRESS): void {
        $this->CUST_STREET_ADDRESS = $CUST_STREET_ADDRESS;
    }

    public function getCustPostalCode(): ?string {
        return $this->CUST_POSTAL_CODE;
    }

    public function setCustPostalCode(?string $CUST_POSTAL_CODE): void {
        $this->CUST_POSTAL_CODE = $CUST_POSTAL_CODE;
    }

    public function getCustCity(): ?string {
        return $this->CUST_CITY;
    }

    public function setCustCity(?string $CUST_CITY): void {
        $this->CUST_CITY = $CUST_CITY;
    }

    public function getCustState(): ?string {
        return $this->CUST_STATE;
    }

    public function setCustState(?string $CUST_STATE): void {
        $this->CUST_STATE = $CUST_STATE;
    }

    public function getCustCountry(): ?string {
        return $this->CUST_COUNTRY;
    }

    public function setCustCountry(?string $CUST_COUNTRY): void {
        $this->CUST_COUNTRY = $CUST_COUNTRY;
    }

    public function getPhoneNumbers(): ?string {
        return $this->PHONE_NUMBERS;
    }

    public function setPhoneNumbers(?string $PHONE_NUMBERS): void {
        $this->PHONE_NUMBERS = $PHONE_NUMBERS;
    }

    public function getNlsLanguage(): ?string {
        return $this->NLS_LANGUAGE;
    }

    public function setNlsLanguage(?string $NLS_LANGUAGE): void {
        $this->NLS_LANGUAGE = $NLS_LANGUAGE;
    }

    public function getNlsTerritory(): ?string {
        return $this->NLS_TERRITORY;
    }

    public function setNlsTerritory(?string $NLS_TERRITORY): void {
        $this->NLS_TERRITORY = $NLS_TERRITORY;
    }

    public function getCreditLimit(): ?float {
        return $this->CREDIT_LIMIT;
    }

    public function setCreditLimit(?float $CREDIT_LIMIT): void {
        $this->CREDIT_LIMIT = $CREDIT_LIMIT;
    }

    public function getCustEmail(): ?string {
        return $this->CUST_EMAIL;
    }

    public function setCustEmail(?string $CUST_EMAIL): void {
        $this->CUST_EMAIL = $CUST_EMAIL;
    }

    public function getAccountMgrId(): ?int {
        return $this->ACCOUNT_MGR_ID;
    }

    public function setAccountMgrId(?int $ACCOUNT_MGR_ID): void {
        $this->ACCOUNT_MGR_ID = $ACCOUNT_MGR_ID;
    }

    public function getCustGeoLocation(): ?string {
        return $this->CUST_GEO_LOCATION;
    }

    public function setCustGeoLocation(?string $CUST_GEO_LOCATION): void {
        $this->CUST_GEO_LOCATION = $CUST_GEO_LOCATION;
    }

    public function getDateOfBirth(): ?string {
        return $this->DATE_OF_BIRTH;
    }

    public function setDateOfBirth(?string $DATE_OF_BIRTH): void {
        $this->DATE_OF_BIRTH = $DATE_OF_BIRTH;
    }

    public function getMaritalStatus(): ?string {
        return $this->MARITAL_STATUS;
    }

    public function setMaritalStatus(?string $MARITAL_STATUS): void {
        $this->MARITAL_STATUS = $MARITAL_STATUS;
    }

    public function getGender(): ?string {
        return $this->GENDER;
    }

    public function setGender(?string $GENDER): void {
        $this->GENDER = $GENDER;
    }

    public function getIncomeLevel(): ?string {
        return $this->INCOME_LEVEL;
    }

    public function setIncomeLevel(?string $INCOME_LEVEL): void {
        $this->INCOME_LEVEL = $INCOME_LEVEL;
    }

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

            // Verificar si el email ya existe
            $stmt = $conn->prepare("SELECT customer_id FROM $table WHERE CUST_EMAIL = ? AND customer_id != ?");
            $stmt->bind_param("si", $this->CUST_EMAIL, $this->CUSTOMER_ID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                throw new Exception("El email ya está en uso.");
            }

            // Verificar existencia de ACCOUNT_MGR_ID
            if ($this->ACCOUNT_MGR_ID) {
                $stmt = $conn->prepare("SELECT employee_id FROM employees WHERE employee_id = ?");
                $stmt->bind_param("i", $this->ACCOUNT_MGR_ID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception("El ACCOUNT_MGR_ID no existe.");
                }
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
                        
                        $customer = new self(
                            $nextId,
                            $faker->firstName(),
                            $faker->lastName(),
                            $faker->streetAddress(),
                            $faker->postcode(),
                            $faker->city(),
                            $faker->state(),
                            'Spain',
                            $faker->phoneNumber(),
                            'es',
                            'ES',
                            $faker->randomFloat(2, 0, 5000),
                            $faker->email(),
                            $faker->numberBetween(145, 179),
                            null,
                            $faker->date('Y-m-d', '-18 years'),
                            $faker->randomElement(['single', 'married']),
                            $faker->randomElement(['M', 'F']),
                            $faker->randomElement(['A: Below 30,000', 'B: 30,000 - 49,999', 'C: 50,000 - 69,999', 
                                                   'D: 70,000 - 89,999', 'E: 90,000 - 109,999', 'F: 110,000 - 129,999'])
                        );
                        
                        if ($customer->save()) {
                            $conn->commit();
                            header('Location: /src/html/customers/run_customers.php?success=created');
                            exit;
                        }
                        break;

                    case 'create':
                        // Obtener el siguiente ID disponible
                        $result = $conn->query("SELECT MAX(customer_id) as max_id FROM customers");
                        $row = $result->fetch_assoc();
                        $nextId = ($row['max_id'] ?? 0) + 1;
                        
                        $customer = new self(
                            $nextId,  // Usar el nuevo ID generado
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
                            $conn->commit();
                            header('Location: /src/html/customers/run_customers.php?success=created');
                            exit;
                        }
                        break;
                    case 'update':
                        $customer = new self(
                            isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
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
                            $conn->commit();
                            header('Location: /src/html/customers/run_customers.php?success=updated');
                            exit;
                        }
                        break;
                    case 'delete':
                        if (!isset($_POST['customer_id'])) {
                            throw new Exception('ID de client no proporcionat');
                        }
                        
                        $customer = new self((int)$_POST['customer_id']);
                        
                        if ($customer->destroy()) {
                            $conn->commit();
                            header('Location: /src/html/customers/run_customers.php?success=deleted');
                            exit;
                        } else {
                            throw new Exception("Error al eliminar el client");
                        }
                        break;

                    default:
                        throw new Exception('Acció no vàlida');
                }
            } catch (Exception $e) {
                header('Location: /src/html/customers/run_customers.php?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    
}


Customer::handleAction();
?>