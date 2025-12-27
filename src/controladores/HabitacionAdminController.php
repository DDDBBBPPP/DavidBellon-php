<?php

namespace Controladores;

use Clases\Sesion;
use Clases\Request;
use Modelos\Habitacion;

class HabitacionAdminController extends BaseController
{
    public function index(): void
    {
        $admin = Sesion::usuario();

        if (!$admin || !$admin->esAdmin()) {
            Request::redirigir('/');
        }

        $show = Habitacion::obtenerTodas();

        $this->render('admin/habitaciones.twig', [
            'habitaciones' => $show
        ]);
    }

    public function crear(): void
    {
        $admin = Sesion::usuario();

        if (!$admin || !$admin->esAdmin()) {
            Request::redirigir('/');
        }

        if (Request::esMetodo('post')) {

            $numero    = (int) (Request::obtener('numero') ?? 0);
            $piso      = (int) (Request::obtener('piso') ?? 0);
            $capacidad = (int) (Request::obtener('capacidad') ?? 0);
            $precio    = (float) (Request::obtener('precio') ?? 0);
            $tipo      = trim(Request::obtener('tipo') ?? '');

            $errores = [];

            if ($numero < 1 || $numero > 9999) $errores[] = "El número debe estar entre 1 y 9999.";
            if ($piso < 0 || $piso > 200) $errores[] = "El piso debe estar entre 0 y 200.";
            if ($capacidad < 1 || $capacidad > 10) $errores[] = "La capacidad debe estar entre 1 y 10.";
            if ($precio <= 0 || $precio > 99999) $errores[] = "El precio debe ser mayor que 0 y razonable.";
            if (!in_array($tipo, ['estandar', 'suite', 'premium'], true)) $errores[] = "Tipo inválido.";

            if ($errores) {
                $this->render('admin/nueva_habitacion.twig', [
                    'errores' => $errores,
                    'old' => [
                        'numero' => $numero,
                        'piso' => $piso,
                        'capacidad' => $capacidad,
                        'precio' => $precio,
                        'tipo' => $tipo
                    ]
                ]);
                return;
            }

            try {
                Habitacion::crear([
                    'numero' => $numero,
                    'piso' => $piso,
                    'capacidad' => $capacidad,
                    'precio' => $precio,
                    'tipo' => $tipo
                ]);
            } catch (\Throwable $e) {
                $this->render('admin/nueva_habitacion.twig', [
                    'errores' => ["No se pudo crear la habitación. Revisa los valores."],
                    'old' => [
                        'numero' => $numero,
                        'piso' => $piso,
                        'capacidad' => $capacidad,
                        'precio' => $precio,
                        'tipo' => $tipo
                    ]
                ]);
                return;
            }

            Request::redirigirARuta('admin.habitaciones');
        }

        $this->render('admin/nueva_habitacion.twig');
    }

    public function borrar(): void
    {
        $admin = Sesion::usuario();

        // Solo superadmin
        if (!$admin || !method_exists($admin, 'esSuperAdmin') || !$admin->esSuperAdmin()) {
            Request::redirigir('/');
        }


        $id = (int) (Request::obtener('id') ?? 0);
        if ($id > 0) {
            Habitacion::borrar($id);
        }

        Request::redirigirARuta('admin.habitaciones');
    }
}
