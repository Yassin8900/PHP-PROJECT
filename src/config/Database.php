<?php

namespace App\config;

use Exception;
use mysqli;

class Database {
    private $host;
    private $port;
    private $database;
    private $username;
    private $password;
    private $conn;

    public function __construct($configFile = 'C:/temp/config.db') {
        
        if (!file_exists($configFile)) {
            throw new Exception("El archivo de configuración no existe.");
        }

        
        $config = $this->parseConfigFile($configFile);
        $this->host = $config['DB_HOST'];
        $this->port = $config['DB_PORT'];
        $this->database = $config['DB_DATABASE'];
        $this->username = $config['DB_USERNAME'];
        $this->password = $config['DB_PASSWORD'];
    }

    
    private function parseConfigFile($file) {
        $config = [];
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            list($key, $value) = explode('=', $line, 2);
            $config[trim($key)] = trim($value);
        }

        return $config;
    }

    
    public function connect() {
        
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
        
        
        if ($this->conn->connect_error) {
            throw new Exception("Error de conexión: " . $this->conn->connect_error);
        }
    }

    
    public function getConnection() {
        
        if (!$this->conn) {
            $this->connect();
        }
        return $this->conn;
    }
    
    
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
