<?php

namespace Controladores;

use Clases\Sesion;
use Clases\Request;
use Modelos\Administrador;

class AdminController extends BaseController
{
    public function inicio(): void
    {
        $usuario = Sesion::usuario();

        if (!$usuario instanceof Administrador) {
            Request::redirigir("/login");
        }

        $this->render("admin/inicio.twig", [
            "usuario" => $usuario
        ]);
    }
}
