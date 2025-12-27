<?php

namespace Clases;

use Modelos\Usuario;

final class Sesion
{
    const MAX_TIEMPO = 2000000;

    private function __construct() {}

    private static function arrancar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function actualizar(): void
    {
        self::arrancar();
        self::setClave("tiempo", time());
    }

    public static function iniciar(Usuario $usuario): void
    {
        self::arrancar();
        self::setClave("usuario", $usuario);
        self::actualizar();
    }

    public static function activa(): bool
    {
        self::arrancar();

        return (self::getValor("usuario") instanceof Usuario)
            && ((time() - (int) self::getValor("tiempo")) <= self::MAX_TIEMPO);
    }

    public static function usuario(): Usuario|false
    {
        return self::activa() ? self::getValor("usuario") : false;
    }

    public static function setClave(string $clave, mixed $valor): void
    {
        self::arrancar();
        $_SESSION[$clave] = $valor;
    }

    public static function getValor(string $clave): mixed
    {
        self::arrancar();
        return $_SESSION[$clave] ?? null;
    }

    public static function cerrar(): void
    {
        self::arrancar();
        $_SESSION = [];
        session_destroy();
    }
}
