<?php
namespace App\Models;

use PDO;

class ClientesModel
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
            "INSERT INTO clientes (nombre_responsable, dni, direccion_facturacion, telefono) 
             VALUES (:nombre, :dni, :direccion, :telefono)"
        );

        return $stmt->execute([
            ':nombre' => $datos['nombre_responsable'] ?? null,
            ':dni' => $datos['dni'],
            ':direccion' => $datos['direccion_facturacion'] ?? null,
            ':telefono' => $datos['telefono'] ?? null
        ]);
    }

    // READ
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT * FROM clientes ORDER BY nombre_responsable"
        )->fetchAll();
    }

    public function obtenerPorId(int $id_cliente): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM clientes WHERE id_cliente = :id"
        );
        $stmt->execute([':id' => $id_cliente]);
        return $stmt->fetch();
    }

    public function obtenerPorDNI(string $dni): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM clientes WHERE dni = :dni"
        );
        $stmt->execute([':dni' => $dni]);
        return $stmt->fetch();
    }

    // UPDATE
    public function actualizar(int $id_cliente, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE clientes SET 
                nombre_responsable = :nombre,
                dni = :dni,
                direccion_facturacion = :direccion,
                telefono = :telefono
             WHERE id_cliente = :id"
        );

        return $stmt->execute([
            ':id' => $id_cliente,
            ':nombre' => $datos['nombre_responsable'] ?? null,
            ':dni' => $datos['dni'],
            ':direccion' => $datos['direccion_facturacion'] ?? null,
            ':telefono' => $datos['telefono'] ?? null
        ]);
    }

    // DELETE
    public function eliminar(int $id_cliente): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM clientes WHERE id_cliente = :id"
        );
        return $stmt->execute([':id' => $id_cliente]);
    }

    // Búsqueda
    public function buscar(string $termino): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM clientes 
             WHERE nombre_responsable LIKE :termino 
                OR dni LIKE :termino
                OR telefono LIKE :termino
             ORDER BY nombre_responsable"
        );
        $stmt->execute([':termino' => "%$termino%"]);
        return $stmt->fetchAll();
    }
}