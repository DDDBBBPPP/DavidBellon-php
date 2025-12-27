<?php

namespace Controladores;

use Clases\Sesion;
use Clases\Request;
use Modelos\Cliente;

class ClienteController extends BaseController
{
    public function inicio(): void
    {
        $usuario = Sesion::usuario();

        if (!$usuario instanceof Cliente) {
            Request::redirigir("/login");
        }

        $this->render("cliente/inicio.twig", [
            "usuario" => $usuario
        ]);
    }
}
