<?php

namespace Clases;

final class Request
{
    const RUTAS = [
        "login"    => "login",
        "logout"   => "logout",
        "registro" => "registro",

        "cliente.inicio"        => "cliente",
        "cliente.habitaciones"  => "cliente/habitaciones",
        "cliente.reservas"      => "cliente/reservas",
        "cliente.reserva.nueva" => "cliente/reserva/nueva",

        "admin.inicio"            => "admin",
        "admin.reservas"          => "admin/reservas",
        "admin.reserva.aceptar"   => "admin/reserva/aceptar",
        "admin.reserva.cancelar"  => "admin/reserva/cancelar",
        "admin.reserva.finalizar" => "admin/reserva/finalizar",

        "admin.habitaciones"      => "admin/habitaciones",
        "admin.habitacion.nueva"  => "admin/habitacion/nueva",
        "admin.habitacion.borrar" => "admin/habitacion/borrar",

        "admin.clientes"          => "admin/clientes",
    ];

    private function __construct() {}

    public static function esMetodo(string $metodo): bool
    {
        return strtolower($metodo) === strtolower($_SERVER["REQUEST_METHOD"]);
    }

    public static function obtener(string $clave): ?string
    {
        return $_POST[$clave] ?? $_GET[$clave] ?? null;
    }

    public static function rutaCompleta(string $nombre): string
    {
        $key = strtolower($nombre);
        $path = self::RUTAS[$key] ?? "";
        return "http://" . $_SERVER["HTTP_HOST"] . "/" . $path;
    }

    public static function redirigir(string $url): never
    {
        header("Location: {$url}");
        exit();
    }

    public static function redirigirARuta(string $ruta): never
    {
        self::redirigir(self::rutaCompleta($ruta));
    }
}
