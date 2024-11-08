<?php

namespace App\models;

require_once __DIR__ . '/../config/Database.php';

use App\config\Database;
use Exception;

class Model {
    protected static $table;
    protected static $primaryKey = 'id';

    public static function all() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $table = static::$table;
            
            $sql = "SELECT * FROM $table";
            $result = $conn->query($sql);

            if (!$result) {
                throw new Exception("Error en la consulta: " . $conn->error);
            }

            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = new static(...array_values($row));
            }

            $db->close();
            return $rows;

        } catch (Exception $e) {
            throw new Exception("Error en all(): " . $e->getMessage());
        }
    }

    public static function find($id) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $table = static::$table;
            $primaryKey = static::$primaryKey;

            $stmt = $conn->prepare("SELECT * FROM $table WHERE $primaryKey = ?");
            
            // Determinar el tipo de parámetro basado en la clase que llama
            $paramType = "i"; // Por defecto integer
            if (static::class === "App\models\Country") {
                $paramType = "s"; // String para Country
            }
            
            $stmt->bind_param($paramType, $id);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $db->close();
            return $row ? new static(...array_values($row)) : null;

        } catch (Exception $e) {
            throw new Exception("Error en find(): " . $e->getMessage());
        }
    }
}