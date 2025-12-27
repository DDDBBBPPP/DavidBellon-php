<?php

spl_autoload_register(function ($class) {
    $ruta = str_replace("\\", "/", $class) . ".php";

    if (file_exists($ruta)) {
        require_once $ruta;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
