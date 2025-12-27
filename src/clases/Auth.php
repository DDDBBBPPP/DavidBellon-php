<?php

namespace Clases;

use Modelos\UsuarioModelo;
use Modelos\Usuario;
use Clases\Sesion;

final class Auth
{
    /**
     * Intenta iniciar sesión.
     * Devuelve el Usuario o false.
     */
    public static function login(string $email, string $password): Usuario|false
    {
        $usuario = UsuarioModelo::login($email, $password);

        if ($usuario instanceof Usuario) {
            Sesion::iniciar($usuario);
            return $usuario;
        }

        return false;
    }

    /**
     * Cierra la sesión
     */
    public static function logout(): void
    {
        Sesion::cerrar();
    }
}
