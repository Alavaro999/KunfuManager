<?php
namespace App\Models;

use PDO;

class AusenciasModel
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
            "INSERT INTO ausencias (id_ausente, fecha, motivo) VALUES (:id_ausente, :fecha, :motivo)"
        );

        return $stmt->execute([
            ':id_ausente' => $datos['id_ausente'],
            ':fecha' => $datos['fecha'],
            ':motivo' => $datos['motivo'] ?? null
        ]);
    }

    // READ
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT a.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM ausencias a
             INNER JOIN usuario u ON a.id_ausente = u.id_usuario
             ORDER BY a.fecha DESC"
        )->fetchAll();
    }

    public function obtenerPorId(int $id_ausencia): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT a.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM ausencias a
             INNER JOIN usuario u ON a.id_ausente = u.id_usuario
             WHERE a.id_ausencia = :id"
        );
        $stmt->execute([':id' => $id_ausencia]);
        return $stmt->fetch();
    }

    public function obtenerPorAlumno(int $id_ausente): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT a.* FROM ausencias a
             WHERE a.id_ausente = :id_ausente
             ORDER BY a.fecha DESC"
        );
        $stmt->execute([':id_ausente' => $id_ausente]);
        return $stmt->fetchAll();
    }

    public function obtenerPorFecha(string $fecha): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT a.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM ausencias a
             INNER JOIN usuario u ON a.id_ausente = u.id_usuario
             WHERE a.fecha = :fecha
             ORDER BY u.nombre"
        );
        $stmt->execute([':fecha' => $fecha]);
        return $stmt->fetchAll();
    }

    public function obtenerPorRangoFechas(string $fecha_inicio, string $fecha_fin): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT a.*, u.nombre as alumno_nombre, u.apellidos as alumno_apellidos
             FROM ausencias a
             INNER JOIN usuario u ON a.id_ausente = u.id_usuario
             WHERE a.fecha BETWEEN :fecha_inicio AND :fecha_fin
             ORDER BY a.fecha DESC, u.nombre"
        );
        $stmt->execute([
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
        return $stmt->fetchAll();
    }

    // UPDATE
    public function actualizar(int $id_ausencia, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE ausencias SET id_ausente = :id_ausente, fecha = :fecha, motivo = :motivo
             WHERE id_ausencia = :id"
        );

        return $stmt->execute([
            ':id' => $id_ausencia,
            ':id_ausente' => $datos['id_ausente'],
            ':fecha' => $datos['fecha'],
            ':motivo' => $datos['motivo'] ?? null
        ]);
    }

    // DELETE
    public function eliminar(int $id_ausencia): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM ausencias WHERE id_ausencia = :id"
        );
        return $stmt->execute([':id' => $id_ausencia]);
    }

    // Verificar si alumno ya tiene ausencia en fecha
    public function existeAusencia(int $id_ausente, string $fecha): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM ausencias 
             WHERE id_ausente = :id_ausente AND fecha = :fecha"
        );
        $stmt->execute([
            ':id_ausente' => $id_ausente,
            ':fecha' => $fecha
        ]);
        return $stmt->fetchColumn() > 0;
    }
}