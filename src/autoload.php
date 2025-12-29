<?php


$composerAutoload = __DIR__ . "/vendor/autoload.php";
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

spl_autoload_register(function ($class) {

    $ruta = str_replace("\\", "/", $class) . ".php";


    $ruta = preg_replace_callback('/^([^\/]+)\//', function ($m) {
        return strtolower($m[1]) . "/";
    }, $ruta);

    $fullPath = __DIR__ . "/" . $ruta;

    if (file_exists($fullPath)) {
        require_once $fullPath;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
