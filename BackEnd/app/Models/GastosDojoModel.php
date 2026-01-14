<?php
namespace App\Models;

use PDO;

class GastosDojoModel
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
            "INSERT INTO gastos_dojo (id_factura, concepto, monto, fecha, estado) 
             VALUES (:id_factura, :concepto, :monto, :fecha, :estado)"
        );

        return $stmt->execute([
            ':id_factura' => $datos['id_factura'],
            ':concepto' => $datos['concepto'],
            ':monto' => $datos['monto'],
            ':fecha' => $datos['fecha'],
            ':estado' => $datos['estado'] ?? 'pendiente'
        ]);
    }

    // READ
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT g.*, f.nombre_cliente, f.fecha_emision as fecha_factura
             FROM gastos_dojo g
             INNER JOIN facturas f ON g.id_factura = f.id_factura
             ORDER BY g.fecha DESC"
        )->fetchAll();
    }

    public function obtenerPorId(int $id_gasto): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT g.*, f.nombre_cliente, f.fecha_emision as fecha_factura
             FROM gastos_dojo g
             INNER JOIN facturas f ON g.id_factura = f.id_factura
             WHERE g.id_gasto = :id"
        );
        $stmt->execute([':id' => $id_gasto]);
        return $stmt->fetch();
    }

    public function obtenerPorEstado(string $estado): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT g.*, f.nombre_cliente
             FROM gastos_dojo g
             INNER JOIN facturas f ON g.id_factura = f.id_factura
             WHERE g.estado = :estado
             ORDER BY g.fecha DESC"
        );
        $stmt->execute([':estado' => $estado]);
        return $stmt->fetchAll();
    }

    public function obtenerPorRangoFechas(string $fecha_inicio, string $fecha_fin): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT g.*, f.nombre_cliente
             FROM gastos_dojo g
             INNER JOIN facturas f ON g.id_factura = f.id_factura
             WHERE g.fecha BETWEEN :fecha_inicio AND :fecha_fin
             ORDER BY g.fecha"
        );
        $stmt->execute([
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
        return $stmt->fetchAll();
    }

    // UPDATE
    public function actualizar(int $id_gasto, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE gastos_dojo SET 
                id_factura = :id_factura,
                concepto = :concepto,
                monto = :monto,
                fecha = :fecha,
                estado = :estado
             WHERE id_gasto = :id"
        );

        return $stmt->execute([
            ':id' => $id_gasto,
            ':id_factura' => $datos['id_factura'],
            ':concepto' => $datos['concepto'],
            ':monto' => $datos['monto'],
            ':fecha' => $datos['fecha'],
            ':estado' => $datos['estado'] ?? 'pendiente'
        ]);
    }

    // Pagar gasto
    public function pagar(int $id_gasto): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE gastos_dojo SET estado = 'pagado' WHERE id_gasto = :id"
        );
        return $stmt->execute([':id' => $id_gasto]);
    }

    // DELETE
    public function eliminar(int $id_gasto): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM gastos_dojo WHERE id_gasto = :id"
        );
        return $stmt->execute([':id' => $id_gasto]);
    }

    // Obtener totales
    public function obtenerTotales(): array
    {
        $stmt = $this->pdo->query(
            "SELECT 
                SUM(monto) as total_gastos,
                SUM(CASE WHEN estado = 'pendiente' THEN monto ELSE 0 END) as total_pendiente,
                SUM(CASE WHEN estado = 'pagado' THEN monto ELSE 0 END) as total_pagado,
                COUNT(*) as cantidad_gastos
             FROM gastos_dojo"
        );
        return $stmt->fetch() ?: [];
    }
}