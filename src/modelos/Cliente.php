<?php

namespace Modelos;

class Cliente extends Usuario
{
    public function __construct(
        int $id_usuario,
        string $nombre,
        ?string $apellidos,
        string $email,
        string $password,
        string $fecha_registro,
        private ?string $telefono = null,
        private ?string $direccion = null
    ) {
        parent::__construct(
            $id_usuario,
            $nombre,
            $apellidos,
            $email,
            $password,
            $fecha_registro,
            'cliente'
        );
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function esCliente(): bool
    {
        return true;
    }

    /**
     * @return array
     * Obtiene todos los clientes registrados para el admin.
     */
    public static function obtenerTodos(): array
    {
        $pdo = \Clases\Database::conectar();

        $sql = "
        SELECT 
            u.id_usuario,
            u.nombre,
            u.apellidos,
            u.email,
            c.telefono,
            c.direccion
        FROM usuario u
        JOIN cliente c ON c.id_cliente = u.id_usuario
        ORDER BY u.nombre
    ";

        return $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
