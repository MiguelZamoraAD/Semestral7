<?php
require_once (__DIR__ . '/../conexion.php');
require_once __DIR__ . '/../sanitiza.php';

class Libro{
    private $conexion;

    public function __construct() {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function registrarReserva($libroId, $usuarioCorreo, $diasReservado) {
        $conn = $this->conexion;
        
        if ($diasReservado < 1 || $diasReservado > 31) {
        return ['success' => false, 'message' => 'La cantidad de días debe estar entre 1 y 31.'];
    }

   try {
           // Verificar unidades disponibles
            $stmt = $conn->prepare("SELECT unidades FROM libros WHERE id = ?");
            $stmt->execute([$libroId]);
            $disponibles = $stmt->fetchColumn();

            if ($disponibles === false) {
                return ['success' => false, 'message' => 'Libro no encontrado'];
            }

            if ($disponibles <= 0) {
                return ['success' => false, 'message' => 'No hay unidades disponibles para este libro'];
            }
            // Verificar si ya existe una reserva activa del mismo usuario y libro
            $stmt = $conn->prepare("SELECT COUNT(*) FROM reservas WHERE libro_id = ? AND usuario = ? AND estado = 'reservado'");
            $stmt->execute([$libroId, $usuarioCorreo]);
            $yaReservado = $stmt->fetchColumn();

            if ($yaReservado > 0) {
                return ['success' => false, 'message' => 'Ya tienes una reserva activa para este libro'];
            }
            // Fechas
            $fechaReserva = date('Y-m-d');
            $fechaDevolucion = date('Y-m-d', strtotime("+$diasReservado days"));

            // Insertar la reserva
            $stmt = $conn->prepare("INSERT INTO reservas (libro_id, usuario, fecha_reserva, dias_reservado, fecha_devolucion, estado)
                                    VALUES (?, ?, ?, ?, ?, 'reservado')");
            $stmt->execute([$libroId, $usuarioCorreo, $fechaReserva, $diasReservado, $fechaDevolucion]);

            // Disminuir unidades en 1
            $stmt = $conn->prepare("UPDATE libros SET unidades = unidades - 1 WHERE id = ?");
            $stmt->execute([$libroId]);

            return ['success' => true, 'message' => 'Reserva registrada exitosamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al registrar reserva: ' . $e->getMessage()];
        }
     }
     public function devolverLibro($libroId, $usuarioCorreo) {
        $conn = $this->conexion;

        try {
            // Verificar si hay una reserva activa
            $stmt = $conn->prepare("SELECT id FROM reservas WHERE libro_id = ? AND usuario = ? AND estado = 'reservado' LIMIT 1");
            $stmt->execute([$libroId, $usuarioCorreo]);
            $reserva = $stmt->fetch();

            if (!$reserva) {
                return ['success' => false, 'message' => 'No tienes una reserva activa para este libro.'];
            }

            // Marcar como devuelto
            $stmt = $conn->prepare("UPDATE reservas SET estado = 'devuelto' WHERE id = ?");
            $stmt->execute([$reserva['id']]);

            // Aumentar unidades del libro
            $stmt = $conn->prepare("UPDATE libros SET unidades = unidades + 1 WHERE id = ?");
            $stmt->execute([$libroId]);

            return ['success' => true, 'message' => 'Libro devuelto correctamente.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al devolver libro: ' . $e->getMessage()];
        }
    }

}
?>