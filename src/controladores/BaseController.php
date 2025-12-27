<?php

namespace Controladores;

use Clases\Sesion;
use Clases\Request;

abstract class BaseController
{
    protected \Twig\Environment $twig;

    public function __construct()
    {
        require_once "./vendor/autoload.php";

        $loader = new \Twig\Loader\FilesystemLoader("./vistas");
        $this->twig = new \Twig\Environment($loader);

        $this->twig->addFunction(new \Twig\TwigFunction(
            'activa',
            fn () => Sesion::activa()
        ));

        $this->twig->addFunction(new \Twig\TwigFunction(
            'usuario',
            fn () => Sesion::usuario()
        ));

        $this->twig->addFunction(new \Twig\TwigFunction(
            'es_admin',
            fn () => Sesion::usuario()?->esAdmin() ?? false
        ));

        $this->twig->addFunction(new \Twig\TwigFunction(
            'es_cliente',
            function () {
                $u = Sesion::usuario();
                return $u && $u->esCliente();
            }
        ));

        // Superadmin real
        $this->twig->addFunction(new \Twig\TwigFunction(
            'es_admin_super',
            function () {
                $u = Sesion::usuario();
                return $u && method_exists($u, 'esSuperAdmin') && $u->esSuperAdmin();
            }
        ));

        $this->twig->addFunction(new \Twig\TwigFunction(
            'ruta',
            fn ($nombre) => Request::rutaCompleta($nombre)
        ));

        $this->twig->addFunction(new \Twig\TwigFunction(
            'error',
            fn () => isset($_GET['error'])
        ));

        $this->twig->addFunction(new \Twig\TwigFunction(
            'registrado',
            fn () => isset($_GET['registrado'])
        ));
    }

    public function render(string $vista, array $args = []): void
    {
        echo $this->twig->render($vista, $args);
    }
}
