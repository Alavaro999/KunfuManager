<?php
namespace App\Models;

use PDO;

class PagosEntrenamientoModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // CREATE
    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO pagos_entrenamiento 
             (id_alumno, tipo, fecha_inicio, fecha_fin, estado, metodo_pago, monto) 
             VALUES (:id_alumno, :tipo, :fecha_inicio, :fecha_fin, :estado, :metodo_pago, :monto)"
        );

        return $stmt->execute([
            ':id_alumno' => $datos['id_alumno'],
            ':tipo' => $datos['tipo'],
            ':fecha_inicio' => $datos['fecha_inicio'],
            ':fecha_fin' => $datos['fecha_fin'],
            ':estado' => $datos['estado'] ?? 'pendiente',
            ':metodo_pago' => $datos['metodo_pago'] ?? null,
            ':monto' => $datos['monto']
        ]);
    }

    // READ
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT p.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM pagos_entrenamiento p
             INNER JOIN usuario u ON p.id_alumno = u.id_usuario
             ORDER BY p.fecha_pago DESC"
        )->fetchAll();
    }

    public function obtenerPorId(int $id_pago): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM pagos_entrenamiento p
             INNER JOIN usuario u ON p.id_alumno = u.id_usuario
             WHERE p.id_pago = :id"
        );
        $stmt->execute([':id' => $id_pago]);
        return $stmt->fetch();
    }

    public function obtenerPorAlumno(int $id_alumno): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.* FROM pagos_entrenamiento p
             WHERE p.id_alumno = :id_alumno
             ORDER BY p.fecha_inicio DESC"
        );
        $stmt->execute([':id_alumno' => $id_alumno]);
        return $stmt->fetchAll();
    }

    public function obtenerPorEstado(string $estado): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM pagos_entrenamiento p
             INNER JOIN usuario u ON p.id_alumno = u.id_usuario
             WHERE p.estado = :estado
             ORDER BY p.fecha_pago DESC"
        );
        $stmt->execute([':estado' => $estado]);
        return $stmt->fetchAll();
    }

    public function obtenerPorTipo(string $tipo): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM pagos_entrenamiento p
             INNER JOIN usuario u ON p.id_alumno = u.id_usuario
             WHERE p.tipo = :tipo
             ORDER BY p.fecha_pago DESC"
        );
        $stmt->execute([':tipo' => $tipo]);
        return $stmt->fetchAll();
    }

    public function obtenerPorRangoFechas(string $fecha_inicio, string $fecha_fin): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM pagos_entrenamiento p
             INNER JOIN usuario u ON p.id_alumno = u.id_usuario
             WHERE p.fecha_pago BETWEEN :fecha_inicio AND :fecha_fin
             ORDER BY p.fecha_pago"
        );
        $stmt->execute([
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
        return $stmt->fetchAll();
    }

    public function obtenerActivos(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM pagos_entrenamiento p
             INNER JOIN usuario u ON p.id_alumno = u.id_usuario
             WHERE p.estado = 'confirmado' 
                AND p.fecha_fin >= CURDATE()
             ORDER BY p.fecha_fin"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerVencidos(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM pagos_entrenamiento p
             INNER JOIN usuario u ON p.id_alumno = u.id_usuario
             WHERE p.estado = 'confirmado' 
                AND p.fecha_fin < CURDATE()
             ORDER BY p.fecha_fin DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // UPDATE
    public function actualizar(int $id_pago, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE pagos_entrenamiento SET 
                id_alumno = :id_alumno,
                tipo = :tipo,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin,
                estado = :estado,
                metodo_pago = :metodo_pago,
                monto = :monto
             WHERE id_pago = :id"
        );

        return $stmt->execute([
            ':id' => $id_pago,
            ':id_alumno' => $datos['id_alumno'],
            ':tipo' => $datos['tipo'],
            ':fecha_inicio' => $datos['fecha_inicio'],
            ':fecha_fin' => $datos['fecha_fin'],
            ':estado' => $datos['estado'] ?? 'pendiente',
            ':metodo_pago' => $datos['metodo_pago'] ?? null,
            ':monto' => $datos['monto']
        ]);
    }

    // Confirmar pago
    public function confirmar(int $id_pago, string $metodo_pago = null): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE pagos_entrenamiento 
             SET estado = 'confirmado', 
                 metodo_pago = COALESCE(:metodo_pago, metodo_pago)
             WHERE id_pago = :id"
        );
        return $stmt->execute([
            ':id' => $id_pago,
            ':metodo_pago' => $metodo_pago
        ]);
    }

    // Anular pago
    public function anular(int $id_pago): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE pagos_entrenamiento SET estado = 'anulado' WHERE id_pago = :id"
        );
        return $stmt->execute([':id' => $id_pago]);
    }

    // DELETE
    public function eliminar(int $id_pago): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM pagos_entrenamiento WHERE id_pago = :id"
        );
        return $stmt->execute([':id' => $id_pago]);
    }

    // Verificar si alumno tiene pago activo
    public function tienePagoActivo(int $id_alumno): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM pagos_entrenamiento 
             WHERE id_alumno = :id_alumno 
                AND estado = 'confirmado' 
                AND fecha_fin >= CURDATE()"
        );
        $stmt->execute([':id_alumno' => $id_alumno]);
        return $stmt->fetchColumn() > 0;
    }

    // Obtener pago activo de alumno
    public function obtenerPagoActivoAlumno(int $id_alumno): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM pagos_entrenamiento 
             WHERE id_alumno = :id_alumno 
                AND estado = 'confirmado' 
                AND fecha_fin >= CURDATE()
             ORDER BY fecha_fin DESC
             LIMIT 1"
        );
        $stmt->execute([':id_alumno' => $id_alumno]);
        return $stmt->fetch();
    }

    // Obtener estadísticas
    public function obtenerEstadisticas(string $periodo = 'mes'): array
    {
        $groupBy = '';
        switch ($periodo) {
            case 'dia':
                $groupBy = 'DATE(fecha_pago)';
                break;
            case 'semana':
                $groupBy = 'YEARWEEK(fecha_pago)';
                break;
            case 'mes':
            default:
                $groupBy = 'DATE_FORMAT(fecha_pago, "%Y-%m")';
                break;
        }

        $sql = "SELECT 
                    $groupBy as periodo,
                    SUM(monto) as total_recaudado,
                    COUNT(*) as cantidad_pagos,
                    SUM(CASE WHEN estado = 'confirmado' THEN monto ELSE 0 END) as total_confirmado,
                    SUM(CASE WHEN estado = 'pendiente' THEN monto ELSE 0 END) as total_pendiente
                FROM pagos_entrenamiento
                WHERE estado IN ('confirmado', 'pendiente')
                GROUP BY $groupBy
                ORDER BY periodo DESC";

        return $this->pdo->query($sql)->fetchAll();
    }
}