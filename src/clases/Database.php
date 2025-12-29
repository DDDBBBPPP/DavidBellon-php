<?php

namespace Clases;

final class Database
{
    // Defaults = entorno Docker local (docker-compose)
    private const DEFAULT_HOST = "db";
    private const DEFAULT_PORT = "3306";
    private const DEFAULT_USER = "user";
    private const DEFAULT_PASS = "password";
    private const DEFAULT_NAME = "gestor_hotelero";

    private function __clone() {}
    private function __construct() {}

    public static function conectar(): ?\PDO
    {
        try {
            // Railway: usa variables de entorno. Local: cae a defaults.
            $host = getenv('DB_HOST') ?: self::DEFAULT_HOST;
            $port = getenv('DB_PORT') ?: self::DEFAULT_PORT;
            $user = getenv('DB_USER') ?: self::DEFAULT_USER;
            $pass = getenv('DB_PASS') ?: self::DEFAULT_PASS;
            $name = getenv('DB_NAME') ?: self::DEFAULT_NAME;

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            return new \PDO($dsn, $user, $pass);
        } catch (\PDOException $e) {
            die("Error al conectar con la base de datos.");
        }
    }
}
