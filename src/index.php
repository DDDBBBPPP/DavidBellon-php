<?php

require_once "autoload.php";

use Clases\Sesion;
use Clases\Request;
use Clases\Auth;
use Modelos\Administrador;
use Modelos\Cliente;

$modelo = $_GET["modelo"] ?? "auth";
$metodo = $_GET["metodo"] ?? "index";

// Control de acceso global
if (Sesion::activa()) {
    Sesion::actualizar();

    // Si está logueado y quiere ir al login, lo mandamos a su INICIO real
    if ($modelo === "auth" && $metodo === "index") {
        $u = Sesion::usuario();

        if ($u instanceof Administrador) {
            Request::redirigirARuta("admin.inicio");
        } elseif ($u instanceof Cliente) {
            Request::redirigirARuta("cliente.inicio");
        } else {
            Auth::logout();
            Request::redirigir("/login");
        }
    }
} else {
    // No hay sesión: solo se permite auth/*
    if ($modelo !== "auth") {
        Request::redirigir("/login");
    }
}

// Construimos e invocamos controlador
$nombreControlador = ucfirst("{$modelo}Controller");
$nombreClase = "Controladores\\{$nombreControlador}";

$controlador = new $nombreClase;
$controlador->$metodo();
