<?php

namespace Modelos;

abstract class Usuario
{
    public function __construct(
        protected int $id_usuario,
        protected string $nombre,
        protected ?string $apellidos,
        protected string $email,
        protected string $password,
        protected string $fecha_registro,
        protected string $tipo_usuario
    ) {}


    public function verificarPassword(string $passwordPlano): bool
    {
        return password_verify($passwordPlano, $this->password);
    }

    /*
       Getters
     */

    public function getId(): int
    {
        return $this->id_usuario;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFechaRegistro(): string
    {
        return $this->fecha_registro;
    }

    public function getTipo(): string
    {
        return $this->tipo_usuario;
    }

    /*
       Para el rol
     */

    public function esAdmin(): bool
    {
        return false;
    }

    public function esCliente(): bool
    {
        return false;
    }

}
