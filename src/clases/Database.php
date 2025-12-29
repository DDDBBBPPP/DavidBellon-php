<?php

namespace Clases;

final class Database
{
    private const DEFAULT_HOST = "db";          // docker-compose
    private const DEFAULT_PORT = "3306";
    private const DEFAULT_USER = "user";
    private const DEFAULT_PASS = "password";
    private const DEFAULT_NAME = "gestor_hotelero";

    private function __construct() {}
    private function __clone() {}

    public static function conectar(): \PDO
    {
        $host = getenv('DB_HOST') ?: self::DEFAULT_HOST;
        $port = getenv('DB_PORT') ?: self::DEFAULT_PORT;
        $user = getenv('DB_USER') ?: self::DEFAULT_USER;
        $pass = getenv('DB_PASS') ?: self::DEFAULT_PASS;
        $name = getenv('DB_NAME') ?: self::DEFAULT_NAME;

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        try {
            $pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            return $pdo;
        } catch (\PDOException $e) {
            // Mantén tu mensaje simple (como ya haces)
            die("Error al conectar con la base de datos.");
        }
    }
}
