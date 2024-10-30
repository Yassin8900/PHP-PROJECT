<?php

namespace App\models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/Database.php';

use App\config\Database;
use Exception;

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
        public ?string $CREDIT_LIMIT = null,
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
            $table = static::$table;

            if (!isset($this->CUSTOMER_ID)) {
                throw new Exception("ID client no informat.");
            }

            $sql = "INSERT INTO $table (
                    CUSTOMER_ID, CUST_FIRST_NAME, CUST_LAST_NAME, CUST_STREET_ADDRESS,
                    CUST_POSTAL_CODE, CUST_CITY, CUST_STATE, CUST_COUNTRY,
                    PHONE_NUMBERS, NLS_LANGUAGE, NLS_TERRITORY, CREDIT_LIMIT,
                    CUST_EMAIL, ACCOUNT_MGR_ID, CUST_GEO_LOCATION, DATE_OF_BIRTH,
                    customer_id, cust_first_name, cust_last_name, cust_address,
                    cust_city, cust_state, cust_postal_code, cust_email,
                    phone_number, credit_limit, marital_status, gender,
                    income_level, account_mgr_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    cust_first_name = VALUES(cust_first_name),
                    cust_last_name = VALUES(cust_last_name),
                    cust_address = VALUES(cust_address),
                    cust_city = VALUES(cust_city),
                    cust_state = VALUES(cust_state),
                    cust_postal_code = VALUES(cust_postal_code),
                    cust_email = VALUES(cust_email),
                    phone_number = VALUES(phone_number),
                    credit_limit = VALUES(credit_limit),
                    marital_status = VALUES(marital_status),
                    gender = VALUES(gender),
                    income_level = VALUES(income_level),
                    account_mgr_id = VALUES(account_mgr_id)";

            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparant la consulta: " . $conn->error);
            }

            $stmt->bind_param("isssssssssssii", 
                $this->customer_id,
                $this->cust_first_name,
                $this->cust_last_name,
                $this->cust_address,
                $this->cust_city,
                $this->cust_state,
                $this->cust_postal_code,
                $this->cust_email,
                $this->phone_number,
                $this->credit_limit,
                $this->marital_status,
                $this->gender,
                $this->income_level,
                $this->account_mgr_id
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $customerId = $_POST['customer_id'] ?? null;

            try {
                switch ($action) {
                    case 'delete':
                        if (!$customerId) {
                            throw new Exception('ID de client no proporcionat');
                        }

                        $db = new Database();
                        $conn = $db->getConnection();

                        // Iniciamos una transacción
                        $conn->begin_transaction();

                        try {
                            // 1. Eliminamos la foreign key constraint
                            $sqlDropFK = "ALTER TABLE orders DROP FOREIGN KEY ORD_CUS_FK";
                            $conn->query($sqlDropFK);

                            // 2. Eliminamos el cliente
                            $sqlDelete = "DELETE FROM customers WHERE customer_id = ?";
                            $stmtDelete = $conn->prepare($sqlDelete);
                            $stmtDelete->bind_param("i", $customerId);
                            $stmtDelete->execute();

                            // 3. Recreamos la foreign key constraint
                            $sqlAddFK = "ALTER TABLE orders ADD CONSTRAINT ORD_CUS_FK 
                                       FOREIGN KEY (CUSTOMER_ID) 
                                       REFERENCES customers(CUSTOMER_ID)
                                       ON DELETE SET NULL";
                            $conn->query($sqlAddFK);

                            // Si todo fue bien, confirmamos la transacción
                            $conn->commit();
                            header('Location: /src/html/run_customers.php?success=Client+eliminat+correctament');
                            exit;

                        } catch (Exception $e) {
                            // Si algo falló, revertimos los cambios
                            $conn->rollback();
                            throw new Exception("Error en eliminar el client: " . $e->getMessage());
                        }
                        break;

                    case 'create':
                        $customer = new self(
                            $_POST['customer_id'] ?? null,
                            $_POST['cust_first_name'] ?? null,
                            $_POST['cust_last_name'] ?? null,
                            $_POST['cust_address'] ?? null,
                            $_POST['cust_city'] ?? null,
                            $_POST['cust_state'] ?? null,
                            $_POST['cust_postal_code'] ?? null,
                            $_POST['cust_email'] ?? null,
                            $_POST['phone_number'] ?? null,
                            $_POST['credit_limit'] ?? null,
                            $_POST['marital_status'] ?? null,
                            $_POST['gender'] ?? null,
                            $_POST['income_level'] ?? null,
                            $_POST['account_mgr_id'] ?? null
                        );
                        
                        if ($customer->save()) {
                            header('Location: /customers/index.php?success=' . ($action === 'create' ? 'created' : 'updated'));
                        }
                        break;
                        
                    case 'update':
                        $customer = new self(
                            $_POST['customer_id'] ?? null,
                            $_POST['cust_first_name'] ?? null,
                            $_POST['cust_last_name'] ?? null,
                            $_POST['cust_address'] ?? null,
                            $_POST['cust_city'] ?? null,
                            $_POST['cust_state'] ?? null,
                            $_POST['cust_postal_code'] ?? null,
                            $_POST['cust_email'] ?? null,
                            $_POST['phone_number'] ?? null,
                            $_POST['credit_limit'] ?? null,
                            $_POST['marital_status'] ?? null,
                            $_POST['gender'] ?? null,
                            $_POST['income_level'] ?? null,
                            $_POST['account_mgr_id'] ?? null
                        );
                        
                        if ($customer->save()) {
                            header('Location: /customers/index.php?success=' . ($action === 'create' ? 'created' : 'updated'));
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

// Ejecutar handleAction al final del archivo
Customer::handleAction();
?>