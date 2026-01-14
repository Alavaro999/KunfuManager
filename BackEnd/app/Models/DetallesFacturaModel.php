<?php
namespace App\Models;

use PDO;

class DetallesFacturaModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // CREATE
    public function crear(int $id_factura, array $detalle): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO detalles (id_producto, id_factura, nombre, descripcion, cantidad, precio_unitario) 
             VALUES (:id_producto, :id_factura, :nombre, :descripcion, :cantidad, :precio_unitario)"
        );

        return $stmt->execute([
            ':id_producto' => $detalle['id_producto'] ?? null,
            ':id_factura' => $id_factura,
            ':nombre' => $detalle['nombre'],
            ':descripcion' => $detalle['descripcion'] ?? null,
            ':cantidad' => $detalle['cantidad'] ?? 1,
            ':precio_unitario' => $detalle['precio_unitario'] ?? 0.00
        ]);
    }

    // READ
    public function obtenerPorFactura(int $id_factura): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT d.*, i.nombre as producto_nombre, i.tipo as producto_tipo
             FROM detalles d
             LEFT JOIN inventario i ON d.id_producto = i.id_item
             WHERE d.id_factura = :id_factura
             ORDER BY d.nombre"
        );
        $stmt->execute([':id_factura' => $id_factura]);
        return $stmt->fetchAll();
    }

    public function obtenerPorProducto(int $id_producto): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT d.*, f.fecha_emision, f.tipo as factura_tipo
             FROM detalles d
             INNER JOIN facturas f ON d.id_factura = f.id_factura
             WHERE d.id_producto = :id_producto
             ORDER BY f.fecha_emision DESC"
        );
        $stmt->execute([':id_producto' => $id_producto]);
        return $stmt->fetchAll();
    }

    // UPDATE
    public function actualizar(int $id_factura, int $id_producto, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE detalles SET 
                nombre = :nombre,
                descripcion = :descripcion,
                cantidad = :cantidad,
                precio_unitario = :precio_unitario
             WHERE id_factura = :id_factura AND id_producto = :id_producto"
        );

        return $stmt->execute([
            ':id_factura' => $id_factura,
            ':id_producto' => $id_producto,
            ':nombre' => $datos['nombre'],
            ':descripcion' => $datos['descripcion'] ?? null,
            ':cantidad' => $datos['cantidad'] ?? 1,
            ':precio_unitario' => $datos['precio_unitario'] ?? 0.00
        ]);
    }

    // DELETE
    public function eliminar(int $id_factura, int $id_producto): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM detalles 
             WHERE id_factura = :id_factura AND id_producto = :id_producto"
        );
        return $stmt->execute([
            ':id_factura' => $id_factura,
            ':id_producto' => $id_producto
        ]);
    }

    public function eliminarTodosDeFactura(int $id_factura): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM detalles WHERE id_factura = :id_factura"
        );
        return $stmt->execute([':id_factura' => $id_factura]);
    }

    // Añadir múltiples detalles
    public function agregarDetalles(int $id_factura, array $detalles): bool
    {
        $this->pdo->beginTransaction();
        
        try {
            foreach ($detalles as $detalle) {
                $this->crear($id_factura, $detalle);
            }
            
            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    // Calcular subtotal por detalle
    public function calcularSubtotal(int $id_factura, int $id_producto): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT cantidad * precio_unitario as subtotal
             FROM detalles
             WHERE id_factura = :id_factura AND id_producto = :id_producto"
        );
        $stmt->execute([
            ':id_factura' => $id_factura,
            ':id_producto' => $id_producto
        ]);
        $result = $stmt->fetch();
        return $result ? (float)$result['subtotal'] : 0.00;
    }
}