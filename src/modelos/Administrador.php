<?php

namespace Modelos;

class Administrador extends Usuario
{
    public function __construct(
        int $id_usuario,
        string $nombre,
        ?string $apellidos,
        string $email,
        string $password,
        string $fecha_registro,
        private string $nivel_acceso = 'basico'
    ) {
        parent::__construct(
            $id_usuario,
            $nombre,
            $apellidos,
            $email,
            $password,
            $fecha_registro,
            'administrador'
        );
    }

    /**
     * ¿Es administrador?
     */
    public function esAdmin(): bool
    {
        return true;
    }

    /**
     * Nivel de acceso (basico | super)
     */
    public function getNivelAcceso(): string
    {
        return $this->nivel_acceso;
    }

    /**
     * ¿Es superadmin?
     */
    public function esSuperAdmin(): bool
    {
        return $this->nivel_acceso === 'super';
    }
}
