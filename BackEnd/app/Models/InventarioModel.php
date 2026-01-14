<?php
namespace App\Models;

use PDO;

class InventarioModel
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
            "INSERT INTO inventario (nombre, tipo, descripcion, cantidad_total, cantidad_disponible) 
             VALUES (:nombre, :tipo, :descripcion, :cantidad_total, :cantidad_disponible)"
        );

        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':tipo' => $datos['tipo'],
            ':descripcion' => $datos['descripcion'] ?? null,
            ':cantidad_total' => $datos['cantidad_total'] ?? 1,
            ':cantidad_disponible' => $datos['cantidad_disponible'] ?? $datos['cantidad_total'] ?? 1
        ]);
    }

    // READ
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT * FROM inventario ORDER BY nombre"
        )->fetchAll();
    }

    public function obtenerPorId(int $id_item): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM inventario WHERE id_item = :id"
        );
        $stmt->execute([':id' => $id_item]);
        return $stmt->fetch();
    }

    public function obtenerPorTipo(string $tipo): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM inventario WHERE tipo = :tipo ORDER BY nombre"
        );
        $stmt->execute([':tipo' => $tipo]);
        return $stmt->fetchAll();
    }

    public function obtenerDisponibles(): array
    {
        return $this->pdo->query(
            "SELECT * FROM inventario WHERE cantidad_disponible > 0 ORDER BY nombre"
        )->fetchAll();
    }

    public function obtenerAgotados(): array
    {
        return $this->pdo->query(
            "SELECT * FROM inventario WHERE cantidad_disponible = 0 ORDER BY nombre"
        )->fetchAll();
    }

    // UPDATE
    public function actualizar(int $id_item, array $datos): bool
    {
        $campos = [];
        $valores = [':id' => $id_item];

        foreach ($datos as $key => $value) {
            $campos[] = "$key = :$key";
            $valores[":$key"] = $value;
        }

        $sql = "UPDATE inventario SET " . implode(', ', $campos) . " WHERE id_item = :id";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute($valores);
    }

    // Actualizar cantidades
    public function actualizarCantidad(int $id_item, int $cantidad_total, int $cantidad_disponible): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE inventario SET cantidad_total = :total, cantidad_disponible = :disponible 
             WHERE id_item = :id"
        );
        
        return $stmt->execute([
            ':id' => $id_item,
            ':total' => $cantidad_total,
            ':disponible' => $cantidad_disponible
        ]);
    }

    // Reservar/liberar cantidad
    public function reservar(int $id_item, int $cantidad): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE inventario SET cantidad_disponible = cantidad_disponible - :cantidad 
             WHERE id_item = :id AND cantidad_disponible >= :cantidad"
        );
        
        return $stmt->execute([
            ':id' => $id_item,
            ':cantidad' => $cantidad
        ]);
    }

    public function liberar(int $id_item, int $cantidad): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE inventario SET cantidad_disponible = cantidad_disponible + :cantidad 
             WHERE id_item = :id AND cantidad_disponible + :cantidad <= cantidad_total"
        );
        
        return $stmt->execute([
            ':id' => $id_item,
            ':cantidad' => $cantidad
        ]);
    }

    // DELETE
    public function eliminar(int $id_item): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM inventario WHERE id_item = :id"
        );
        return $stmt->execute([':id' => $id_item]);
    }

    // Búsqueda
    public function buscar(string $termino): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM inventario 
             WHERE nombre LIKE :termino OR descripcion LIKE :termino
             ORDER BY nombre"
        );
        $stmt->execute([':termino' => "%$termino%"]);
        return $stmt->fetchAll();
    }
}