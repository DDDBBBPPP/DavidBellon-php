<?php

namespace Controladores;

use Clases\Sesion;
use Clases\Request;
use Modelos\Reserva;
use Modelos\Usuario;

class ReservaClienteController extends BaseController
{
    /**
     * Listar reservas del cliente logueado
     */
    public function index(): void
    {
        $usuario = Sesion::usuario();

        if (!$usuario || !$usuario->esCliente()) {
            Request::redirigir('/');
        }

        $reservas = Reserva::obtenerPorCliente($usuario->getId());

        $this->render('cliente/reservas.twig', [
            'reservas' => $reservas
        ]);
    }

    /**
     * Crear nueva reserva
     */
    public function crear(): void
    {
        $usuario = Sesion::usuario();

        if (!$usuario || !$usuario->esCliente()) {
            Request::redirigir('/');
        }

        $habitacion = (int) Request::obtener('habitacion');

        // Fecha de entrada SIEMPRE mañana
        $fechaEntrada = (new \DateTime('tomorrow'))->format('Y-m-d');

        if (Request::esMetodo('post')) {

            $errores = [];
            $fechaSalida = Request::obtener('fecha_salida');

            if (!$fechaSalida) {
                $errores[] = "La fecha de salida es obligatoria.";
            } else {
                $salida = new \DateTime($fechaSalida);
                $entrada = new \DateTime($fechaEntrada);

                if ($salida <= $entrada) {
                    $errores[] = "La fecha de salida debe ser posterior a la de entrada.";
                }
            }

            if (empty($errores)) {
                Reserva::crear(
                    $usuario->getId(),
                    $habitacion,
                    $fechaEntrada,
                    $fechaSalida
                );

                Request::redirigirARuta('cliente.reservas');
            }

            $this->render('cliente/nueva_reserva.twig', [
                'errores' => $errores,
                'fecha_entrada' => $fechaEntrada,
                'fecha_salida' => $fechaSalida
            ]);
            return;
        }

        // GET
        $this->render('cliente/nueva_reserva.twig', [
            'fecha_entrada' => $fechaEntrada
        ]);
    }


    /**
     * Cancelar reserva (solo si es suya)
     */
    public function cancelar(): void
    {
        $usuario = Sesion::usuario();

        if (!$usuario || !$usuario->esCliente()) {
            Request::redirigir('/');
        }

        $idReserva = (int) Request::obtener('id');

        Reserva::cancelar($idReserva, $usuario->getId());

        Request::redirigirARuta('cliente.reservas');
    }


}
