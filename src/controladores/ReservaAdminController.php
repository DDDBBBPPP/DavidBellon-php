<?php

namespace Controladores;

use Clases\Sesion;
use Clases\Request;
use Modelos\Reserva;

class ReservaAdminController extends BaseController
{
    public function index(): void
    {
        $usuario = Sesion::usuario();

        // Seguridad: solo admins
        if (!$usuario || !$usuario->esAdmin()) {
            Request::redirigir('/');
        }

        $reservas = Reserva::obtenerTodas();

        $this->render('admin/reservas.twig', [
            'reservas' => $reservas,
            'admin' => $usuario
        ]);
    }

    public function aceptar(): void
    {
        $admin = Sesion::usuario();

        if (!$admin || !$admin->esAdmin()) {
            Request::redirigir('/');
        }

        $id = (int) Request::obtener('id');

        Reserva::cambiarEstado($id, 'aceptada');

        Request::redirigirARuta('admin.reservas');
    }

    public function cancelar(): void
    {
        $admin = Sesion::usuario();

        if (!$admin || !$admin->esSuperAdmin()) {
            Request::redirigir('/');
        }

        $id = (int) Request::obtener('id');

        Reserva::cambiarEstado($id, 'cancelada');

        Request::redirigirARuta('admin.reservas');
    }

    /**
     * Finalizar una reserva (solo superadmins)
     */
    public function finalizar(): void
    {
        $admin = Sesion::usuario();

        if (!$admin || !$admin->esSuperAdmin()) {
            Request::redirigir('/');
        }

        $id = (int) Request::obtener('id');

        Reserva::cambiarEstado($id, 'finalizada');

        Request::redirigirARuta('admin.reservas');
    }
}
