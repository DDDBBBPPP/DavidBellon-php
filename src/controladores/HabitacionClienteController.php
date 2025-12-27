<?php

namespace Controladores;

use Clases\Sesion;
use Clases\Request;
use Modelos\Habitacion;

class HabitacionClienteController extends BaseController
{
    public function index(): void
    {
        $usuario = Sesion::usuario();

        if (!$usuario || !$usuario->esCliente()) {
            Request::redirigir('/');
        }

        $habitaciones = Habitacion::obtenerDisponiblesParaCliente();

        $this->render('cliente/habitaciones.twig', [
            'habitaciones' => $habitaciones
        ]);
    }
}
