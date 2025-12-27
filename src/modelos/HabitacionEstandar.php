<?php

namespace Modelos ;

class HabitacionEstandar extends Habitacion
{
    public function __construct(
        int $id_habitacion,
        int $numero,
        int $piso,
        int $capacidad,
        float $precio,
        private bool $tiene_tv = true
    ) {
        parent::__construct(
            $id_habitacion,
            $numero,
            $piso,
            $capacidad,
            $precio,
            'estandar'
        );
    }
}
