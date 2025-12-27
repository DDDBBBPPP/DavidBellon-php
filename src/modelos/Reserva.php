<?php

namespace Modelos;

use Clases\Database;
use PDO;

class Reserva
{
    // PDO::FETCH_CLASS rellenará estas props directamente
    public int $id_reserva;
    public int $id_cliente;
    public int $id_habitacion;
    public string $fecha_entrada;
    public string $fecha_salida;
    public string $estado;

    // Extras para mostrar en vistas
    public ?int $habitacion_numero = null;
    public ?string $tipo_habitacion = null;

    /**
     * Obtener reservas de un cliente
     * (incluye número y tipo de habitación)
     */
    public static function obtenerPorCliente(int $idCliente): array
    {
        $pdo = Database::conectar();

        $stmt = $pdo->prepare("
            SELECT
                r.*,
                h.numero AS habitacion_numero,
                h.tipo AS tipo_habitacion
            FROM reserva r
            JOIN habitacion h ON h.id_habitacion = r.id_habitacion
            WHERE r.id_cliente = :id
            ORDER BY r.fecha_entrada DESC
        ");

        $stmt->execute([':id' => $idCliente]);

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Crear una nueva reserva (estado = solicitada)
     */
    public static function crear(
        int $idCliente,
        int $idHabitacion,
        string $fechaEntrada,
        string $fechaSalida
    ): void {
        $pdo = Database::conectar();

        $stmt = $pdo->prepare("
            INSERT INTO reserva
            (id_cliente, id_habitacion, fecha_entrada, fecha_salida, estado)
            VALUES (:cliente, :habitacion, :entrada, :salida, 'solicitada')
        ");

        $stmt->execute([
            ':cliente'    => $idCliente,
            ':habitacion' => $idHabitacion,
            ':entrada'    => $fechaEntrada,
            ':salida'     => $fechaSalida,
        ]);
    }

    /**
     * Cancelar reserva (cliente)
     */
    public static function cancelar(int $idReserva, int $idCliente): void
    {
        $pdo = Database::conectar();

        $stmt = $pdo->prepare("
            UPDATE reserva
            SET estado = 'cancelada'
            WHERE id_reserva = :reserva
              AND id_cliente = :cliente
              AND estado IN ('solicitada', 'aceptada')
        ");

        $stmt->execute([
            ':reserva' => $idReserva,
            ':cliente' => $idCliente,
        ]);
    }

    /**
     * Obtener todas las reservas (admin)
     */
    public static function obtenerTodas(): array
    {
        $pdo = Database::conectar();

        $sql = "
            SELECT
                r.id_reserva,
                r.fecha_entrada,
                r.fecha_salida,
                r.estado,
                u.nombre AS cliente_nombre,
                u.email AS cliente_email,
                h.numero AS habitacion_numero,
                h.tipo AS tipo_habitacion
            FROM reserva r
            JOIN cliente c ON c.id_cliente = r.id_cliente
            JOIN usuario u ON u.id_usuario = c.id_cliente
            JOIN habitacion h ON h.id_habitacion = r.id_habitacion
            ORDER BY r.fecha_entrada DESC
        ";

        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cambiar estado (admin)
     * - aceptar: solo si estaba solicitada
     * - cancelar: solo si estaba solicitada
     * - finalizar: solo si estaba aceptada
     */
    public static function cambiarEstado(int $idReserva, string $nuevoEstado): bool
    {
        $pdo = Database::conectar();

        $permitidos = ['aceptada', 'cancelada', 'finalizada'];
        if (!in_array($nuevoEstado, $permitidos, true)) {
            return false;
        }

        $condicionEstadoActual = "";
        if ($nuevoEstado === 'aceptada') {
            $condicionEstadoActual = " AND estado = 'solicitada'";
        } elseif ($nuevoEstado === 'cancelada') {
            $condicionEstadoActual = " AND estado = 'solicitada'";
        } elseif ($nuevoEstado === 'finalizada') {
            $condicionEstadoActual = " AND estado = 'aceptada'";
        }

        $stmt = $pdo->prepare("
            UPDATE reserva
            SET estado = :estado
            WHERE id_reserva = :id
            {$condicionEstadoActual}
        ");

        $stmt->execute([
            ':estado' => $nuevoEstado,
            ':id'     => $idReserva
        ]);

        return $stmt->rowCount() > 0;
    }
}
