<?php

class Database
{
    private string $host;
    private string $port;
    private string $db_name;
    private string $username;
    private string $password;

    public ?PDO $conn = null;

    public function __construct()
    {
        $this->host     = getenv('DB_HOST') ?: '127.0.0.1';
        $this->port     = getenv('DB_PORT') ?: '3306';
        $this->db_name  = getenv('DB_NAME') ?: 'industria_atunera';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
    }

    public function getConnection(): ?PDO
    {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $exception) {
            error_log("Error de conexión: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
