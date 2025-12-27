<?php

namespace Controladores;

use Clases\Sesion;
use Clases\Request;
use Modelos\Cliente;

class AdminClienteController extends BaseController
{
    public function index(): void
    {
        $admin = Sesion::usuario();

        if (!$admin || !$admin->esAdmin()) {
            Request::redirigir('/');
        }

        $clientes = Cliente::obtenerTodos();

        $this->render('admin/clientes.twig', [
            'clientes' => $clientes
        ]);
    }
}
