<?php

namespace Clases;

final class Database
{
    private const DBHOST = "db";
    private const DBUSER = "user";
    private const DBPASS = "password";
    private const DBNAME = "gestor_hotelero";

    private function __clone() {}
    private function __construct() {}

    /**
     * @return \PDO|null
     */
    public static function conectar(): ?\PDO
    {
        try {
            $dsn = "mysql:host=" . self::DBHOST .
                ";dbname=" . self::DBNAME .
                ";charset=utf8";

            return new \PDO($dsn, self::DBUSER, self::DBPASS);

        } catch (\PDOException $e) {
            // No exponemos detalles al usuario
            die("Error al conectar con la base de datos.");
        }
    }
}
