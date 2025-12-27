<?php

namespace Modelos ;

class HabitacionSuite extends Habitacion
{
    public function __construct(
        int $id_habitacion,
        int $numero,
        int $piso,
        int $capacidad,
        float $precio,
        private bool $jacuzzi,
        private ?string $vistas,
        private bool $terraza,
        private bool $minibar
    ) {
        parent::__construct(
            $id_habitacion,
            $numero,
            $piso,
            $capacidad,
            $precio,
            'suite'
        );
    }
}
