<?php

namespace Modelos;

use Clases\Database;
use PDO;

final class UsuarioModelo
{
    /**
     * Autenticación de usuario.
     *
     * - Devuelve un objeto Cliente o Administrador
     * - Devuelve false si las credenciales no son válidas
     *
     * Esta clase EXISTE solo porque Usuario es abstracta.
     */
    public static function login(string $email, string $password): Usuario|false
    {
        // Conexión a la base de datos (TU método)
        $pdo = Database::conectar();

        /*
         * Buscamos siempre en la tabla usuario (base común)
         * y traemos los datos específicos según el tipo.
         *
         * LEFT JOIN porque:
         * - un usuario solo será cliente O administrador
         * - la otra tabla no tendrá fila
         */
        $sql = "
            SELECT 
                u.id_usuario,
                u.nombre,
                u.apellidos,
                u.email,
                u.password,
                u.fecha_registro,
                u.tipo_usuario,
                a.nivel_acceso,
                c.telefono,
                c.direccion
            FROM usuario u
            LEFT JOIN administrador a ON a.id_admin = u.id_usuario
            LEFT JOIN cliente c ON c.id_cliente = u.id_usuario
            WHERE u.email = :email
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([":email" => $email]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        // Usuario no existe
        if (!$fila) {
            return false;
        }

        // Contraseña incorrecta
        if (!password_verify($password, $fila["password"])) {
            return false;
        }

        // Administrador
        if ($fila["tipo_usuario"] === "administrador") {
            return new Administrador(
                (int)$fila["id_usuario"],
                $fila["nombre"],
                $fila["apellidos"],
                $fila["email"],
                $fila["password"],
                $fila["fecha_registro"],
                $fila["nivel_acceso"] ?? "basico"
            );
        }

        // Cliente
        return new Cliente(
            (int)$fila["id_usuario"],
            $fila["nombre"],
            $fila["apellidos"],
            $fila["email"],
            $fila["password"],
            $fila["fecha_registro"],
            $fila["telefono"] ?? null,
            $fila["direccion"] ?? null
        );
    }


    /**
     * @param string $nombre
     * @param string|null $apellidos
     * @param string $email
     * @param string $password
     * @param string|null $telefono
     * @param string|null $direccion
     * @return bool|string
     */
    public static function registrarCliente(
        string $nombre,
        ?string $apellidos,
        string $email,
        string $password,
        ?string $telefono,
        ?string $direccion
    ): bool
    {
        $pdo = \Clases\Database::conectar();

        // Comprobacion email
        $stmt = $pdo->prepare(
            "SELECT 1 FROM usuario WHERE email = :email LIMIT 1"
        );
        $stmt->execute([":email" => $email]);

        if ($stmt->fetch()) {
            return false;
        }

        // 2 Insertar usuario (password hash)
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
        INSERT INTO usuario (nombre, apellidos, email, password, tipo_usuario)
        VALUES (:nombre, :apellidos, :email, :password, 'cliente')
    ");

        $stmt->execute([
            ":nombre"    => $nombre,
            ":apellidos" => $apellidos,
            ":email"     => $email,
            ":password"  => $hash
        ]);

        $idUsuario = (int) $pdo->lastInsertId();

        // 3 Insertar datos de  cliente
        $stmt = $pdo->prepare("
        INSERT INTO cliente (id_cliente, telefono, direccion)
        VALUES (:id, :telefono, :direccion)
    ");

        $stmt->execute([
            ":id"        => $idUsuario,
            ":telefono"  => $telefono,
            ":direccion" => $direccion
        ]);

        return true;
    }

}
