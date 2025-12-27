<?php

namespace Modelos;

use Clases\Database;
use PDO;

class Habitacion
{
    public function __construct(
        protected int $id_habitacion,
        protected int $numero,
        protected int $piso,
        protected int $capacidad,
        protected float $precio,
        protected string $tipo
    ) {}

    public static function obtenerDisponiblesParaCliente(): array
    {
        $pdo = Database::conectar();

        $sql = "
            SELECT 
                h.id_habitacion,
                h.numero,
                h.piso,
                h.capacidad,
                h.precio,
                h.tipo,

                he.tiene_tv,
                hs.jacuzzi,
                hs.vistas AS vistas_suite,
                hs.terraza AS terraza_suite,
                hs.minibar,

                hp.terraza AS terraza_premium,
                hp.vistas AS vistas_premium,
                hp.servicio_habitacion_24h
            FROM habitacion h
            LEFT JOIN habitacion_estandar he ON he.id_habitacion = h.id_habitacion
            LEFT JOIN habitacion_suite hs ON hs.id_habitacion = h.id_habitacion
            LEFT JOIN habitacion_premium hp ON hp.id_habitacion = h.id_habitacion
            WHERE h.id_habitacion NOT IN (
                SELECT r.id_habitacion
                FROM reserva r
                WHERE r.estado = 'aceptada'
            )
            ORDER BY h.tipo, h.numero
        ";

        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerTodas(): array
    {
        $pdo = Database::conectar();

        $sql = "
            SELECT 
                h.*,
                he.tiene_tv,
                hs.jacuzzi,
                hs.vistas AS vistas_suite,
                hs.terraza AS terraza_suite,
                hs.minibar,
                hp.terraza AS terraza_premium,
                hp.vistas AS vistas_premium,
                hp.servicio_habitacion_24h
            FROM habitacion h
            LEFT JOIN habitacion_estandar he ON he.id_habitacion = h.id_habitacion
            LEFT JOIN habitacion_suite hs ON hs.id_habitacion = h.id_habitacion
            LEFT JOIN habitacion_premium hp ON hp.id_habitacion = h.id_habitacion
            ORDER BY h.numero
        ";

        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear(array $datos): void
    {
        $pdo = Database::conectar();

        $stmt = $pdo->prepare("
            INSERT INTO habitacion (numero, piso, capacidad, precio, tipo)
            VALUES (:numero, :piso, :capacidad, :precio, :tipo)
        ");

        $stmt->execute([
            ':numero'    => $datos['numero'],
            ':piso'      => $datos['piso'],
            ':capacidad' => $datos['capacidad'],
            ':precio'    => $datos['precio'],
            ':tipo'      => $datos['tipo'],
        ]);

        $id = $pdo->lastInsertId();

        if ($datos['tipo'] === 'estandar') {
            $pdo->prepare("
                INSERT INTO habitacion_estandar (id_habitacion, tiene_tv)
                VALUES (:id, 1)
            ")->execute([':id' => $id]);
        }

        if ($datos['tipo'] === 'suite') {
            $pdo->prepare("
                INSERT INTO habitacion_suite
                (id_habitacion, jacuzzi, vistas, terraza, minibar)
                VALUES (:id, 0, NULL, 0, 0)
            ")->execute([':id' => $id]);
        }

        if ($datos['tipo'] === 'premium') {
            $pdo->prepare("
                INSERT INTO habitacion_premium
                (id_habitacion, terraza, vistas, servicio_habitacion_24h)
                VALUES (:id, 0, NULL, 1)
            ")->execute([':id' => $id]);
        }
    }

    /**
     * Borrar habitación (solo superadmin desde el controller)
     * Borra especialización + reservas asociadas + habitación.
     */
    public static function borrar(int $idHabitacion): bool
    {
        $pdo = Database::conectar();

        try {
            $pdo->beginTransaction();

            // borrar reservas ligadas
            $stmt = $pdo->prepare("DELETE FROM reserva WHERE id_habitacion = :id");
            $stmt->execute([':id' => $idHabitacion]);

            // borrar especializaciones (si existieran)
            $stmt = $pdo->prepare("DELETE FROM habitacion_estandar WHERE id_habitacion = :id");
            $stmt->execute([':id' => $idHabitacion]);

            $stmt = $pdo->prepare("DELETE FROM habitacion_suite WHERE id_habitacion = :id");
            $stmt->execute([':id' => $idHabitacion]);

            $stmt = $pdo->prepare("DELETE FROM habitacion_premium WHERE id_habitacion = :id");
            $stmt->execute([':id' => $idHabitacion]);

            // borrar habitación
            $stmt = $pdo->prepare("DELETE FROM habitacion WHERE id_habitacion = :id");
            $stmt->execute([':id' => $idHabitacion]);

            $pdo->commit();

            return $stmt->rowCount() > 0;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }
}
