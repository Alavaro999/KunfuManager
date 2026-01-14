<?php
namespace App\Models;

use PDO;

class SolicitudesInventarioModel
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
            "INSERT INTO solicitudes_inventario 
             (id_solicitante, id_item, cantidad, estado) 
             VALUES (:id_solicitante, :id_item, :cantidad, :estado)"
        );

        return $stmt->execute([
            ':id_solicitante' => $datos['id_solicitante'] ?? null,
            ':id_item' => $datos['id_item'],
            ':cantidad' => $datos['cantidad'] ?? 1,
            ':estado' => $datos['estado'] ?? 'pendiente'
        ]);
    }

    // READ
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT si.*, 
                    u.nombre as solicitante_nombre,
                    i.nombre as item_nombre,
                    i.tipo as item_tipo,
                    a.nombre as admin_resolucion_nombre
             FROM solicitudes_inventario si
             LEFT JOIN usuario u ON si.id_solicitante = u.id_usuario
             LEFT JOIN inventario i ON si.id_item = i.id_item
             LEFT JOIN usuario a ON si.id_admin_resolucion = a.id_usuario
             ORDER BY si.fecha_solicitud DESC"
        )->fetchAll();
    }

    public function obtenerPorId(int $id_solicitud): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT si.*, 
                    u.nombre as solicitante_nombre,
                    i.nombre as item_nombre,
                    i.tipo as item_tipo,
                    a.nombre as admin_resolucion_nombre
             FROM solicitudes_inventario si
             LEFT JOIN usuario u ON si.id_solicitante = u.id_usuario
             LEFT JOIN inventario i ON si.id_item = i.id_item
             LEFT JOIN usuario a ON si.id_admin_resolucion = a.id_usuario
             WHERE si.id_solicitud = :id"
        );
        $stmt->execute([':id' => $id_solicitud]);
        return $stmt->fetch();
    }

    public function obtenerPorEstado(string $estado): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT si.*, 
                    u.nombre as solicitante_nombre,
                    i.nombre as item_nombre
             FROM solicitudes_inventario si
             LEFT JOIN usuario u ON si.id_solicitante = u.id_usuario
             LEFT JOIN inventario i ON si.id_item = i.id_item
             WHERE si.estado = :estado
             ORDER BY si.fecha_solicitud DESC"
        );
        $stmt->execute([':estado' => $estado]);
        return $stmt->fetchAll();
    }

    public function obtenerPorSolicitante(int $id_solicitante): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT si.*, i.nombre as item_nombre, i.tipo as item_tipo
             FROM solicitudes_inventario si
             LEFT JOIN inventario i ON si.id_item = i.id_item
             WHERE si.id_solicitante = :id_solicitante
             ORDER BY si.fecha_solicitud DESC"
        );
        $stmt->execute([':id_solicitante' => $id_solicitante]);
        return $stmt->fetchAll();
    }

    // UPDATE
    public function actualizar(int $id_solicitud, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE solicitudes_inventario 
             SET id_solicitante = :id_solicitante, 
                 id_item = :id_item, 
                 cantidad = :cantidad,
                 estado = :estado
             WHERE id_solicitud = :id"
        );

        return $stmt->execute([
            ':id' => $id_solicitud,
            ':id_solicitante' => $datos['id_solicitante'] ?? null,
            ':id_item' => $datos['id_item'],
            ':cantidad' => $datos['cantidad'] ?? 1,
            ':estado' => $datos['estado'] ?? 'pendiente'
        ]);
    }

    // Resolver solicitud
    public function resolver(int $id_solicitud, string $estado, int $id_admin_resolucion): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE solicitudes_inventario 
             SET estado = :estado,
                 id_admin_resolucion = :id_admin,
                 fecha_resolucion = NOW()
             WHERE id_solicitud = :id"
        );

        return $stmt->execute([
            ':id' => $id_solicitud,
            ':estado' => $estado,
            ':id_admin' => $id_admin_resolucion
        ]);
    }

    // DELETE
    public function eliminar(int $id_solicitud): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM solicitudes_inventario WHERE id_solicitud = :id"
        );
        return $stmt->execute([':id' => $id_solicitud]);
    }

    // Cancelar solicitud (solo si está pendiente)
    public function cancelar(int $id_solicitud): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE solicitudes_inventario 
             SET estado = 'cancelado', fecha_resolucion = NOW()
             WHERE id_solicitud = :id AND estado = 'pendiente'"
        );
        return $stmt->execute([':id' => $id_solicitud]);
    }

    // Obtener estadísticas
    public function obtenerEstadisticas(): array
    {
        $result = $this->pdo->query(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) as rechazadas,
                SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as canceladas
             FROM solicitudes_inventario"
        )->fetch();
        
        return $result ?: [];
    }
}