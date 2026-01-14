<?php
namespace App\Models;

use PDO;

class FacturasModel
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
            "INSERT INTO facturas (nombre_cliente, fecha_emision, tipo, estado, total) 
             VALUES (:nombre_cliente, :fecha_emision, :tipo, :estado, :total)"
        );

        return $stmt->execute([
            ':nombre_cliente' => $datos['nombre_cliente'] ?? null,
            ':fecha_emision' => $datos['fecha_emision'],
            ':tipo' => $datos['tipo'] ?? 'gasto',
            ':estado' => $datos['estado'] ?? 'pendiente',
            ':total' => $datos['total'] ?? 0.00
        ]);
    }

    // READ
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT * FROM facturas ORDER BY fecha_emision DESC"
        )->fetchAll();
    }

    public function obtenerPorId(int $id_factura): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM facturas WHERE id_factura = :id"
        );
        $stmt->execute([':id' => $id_factura]);
        return $stmt->fetch();
    }

    public function obtenerPorTipo(string $tipo): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM facturas WHERE tipo = :tipo ORDER BY fecha_emision DESC"
        );
        $stmt->execute([':tipo' => $tipo]);
        return $stmt->fetchAll();
    }

    public function obtenerPorEstado(string $estado): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM facturas WHERE estado = :estado ORDER BY fecha_emision DESC"
        );
        $stmt->execute([':estado' => $estado]);
        return $stmt->fetchAll();
    }

    public function obtenerPorRangoFechas(string $fecha_inicio, string $fecha_fin): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM facturas 
             WHERE fecha_emision BETWEEN :fecha_inicio AND :fecha_fin
             ORDER BY fecha_emision"
        );
        $stmt->execute([
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
        return $stmt->fetchAll();
    }

    // UPDATE
    public function actualizar(int $id_factura, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE facturas SET 
                nombre_cliente = :nombre_cliente,
                fecha_emision = :fecha_emision,
                tipo = :tipo,
                estado = :estado,
                total = :total
             WHERE id_factura = :id"
        );

        return $stmt->execute([
            ':id' => $id_factura,
            ':nombre_cliente' => $datos['nombre_cliente'] ?? null,
            ':fecha_emision' => $datos['fecha_emision'],
            ':tipo' => $datos['tipo'] ?? 'gasto',
            ':estado' => $datos['estado'] ?? 'pendiente',
            ':total' => $datos['total'] ?? 0.00
        ]);
    }

    // Cambiar estado
    public function cambiarEstado(int $id_factura, string $estado): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE facturas SET estado = :estado WHERE id_factura = :id"
        );
        return $stmt->execute([
            ':id' => $id_factura,
            ':estado' => $estado
        ]);
    }

    // Calcular total
    public function calcularTotal(int $id_factura): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT SUM(cantidad * precio_unitario) as total
             FROM detalles
             WHERE id_factura = :id"
        );
        $stmt->execute([':id' => $id_factura]);
        $result = $stmt->fetch();
        return $result ? (float)$result['total'] : 0.00;
    }

    // Actualizar total automáticamente
    public function actualizarTotal(int $id_factura): bool
    {
        $total = $this->calcularTotal($id_factura);
        
        $stmt = $this->pdo->prepare(
            "UPDATE facturas SET total = :total WHERE id_factura = :id"
        );
        return $stmt->execute([
            ':id' => $id_factura,
            ':total' => $total
        ]);
    }

    // DELETE
    public function eliminar(int $id_factura): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM facturas WHERE id_factura = :id"
        );
        return $stmt->execute([':id' => $id_factura]);
    }

    // Obtener estadísticas
    public function obtenerEstadisticas(string $periodo = 'mes'): array
    {
        $groupBy = '';
        switch ($periodo) {
            case 'dia':
                $groupBy = 'DATE(fecha_emision)';
                break;
            case 'semana':
                $groupBy = 'YEARWEEK(fecha_emision)';
                break;
            case 'mes':
            default:
                $groupBy = 'DATE_FORMAT(fecha_emision, "%Y-%m")';
                break;
        }

        $sql = "SELECT 
                    $groupBy as periodo,
                    SUM(CASE WHEN tipo = 'ingreso' THEN total ELSE 0 END) as total_ingresos,
                    SUM(CASE WHEN tipo = 'gasto' THEN total ELSE 0 END) as total_gastos,
                    COUNT(*) as cantidad_facturas
                FROM facturas
                WHERE estado = 'pagado'
                GROUP BY $groupBy
                ORDER BY periodo DESC";

        return $this->pdo->query($sql)->fetchAll();
    }
}