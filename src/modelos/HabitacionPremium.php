<?php

namespace Modelos ;

class HabitacionPremium extends Habitacion
{
    public function __construct(
        int $id_habitacion,
        int $numero,
        int $piso,
        int $capacidad,
        float $precio,
        private bool $terraza,
        private ?string $vistas,
        private bool $servicio_habitacion_24h = true
    ) {
        parent::__construct(
            $id_habitacion,
            $numero,
            $piso,
            $capacidad,
            $precio,
            'premium'
        );
    }
}
