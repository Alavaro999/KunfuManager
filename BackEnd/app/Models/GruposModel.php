<?php
namespace App\Models;
 
use PDO;
 
class GruposModel
{
    private PDO $pdo;
 
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
 
    // CREATE
    public function crear(string $nombre, ?string $descripcion, ?string $nivel): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO grupos (nombre, descripcion, nivel ) VALUES (:n, :d, :ni)"
        );
 
        return $stmt->execute([
            ':n' => $nombre,
            ':d' => $descripcion,
            ':ni' => $nivel
            
        ]);
    }
 
    // READ
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT * FROM grupos"
        )->fetchAll();
    }
 
    public function obtenerPorId(int $id_grupo): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM grupos WHERE id_grupo = :id_grupo"
        );
        $stmt->execute([':id_grupo' => $id_grupo]);
        return $stmt->fetch();
    }
 
    // UPDATE
    public function actualizar(string $nombre, ?string $descripcion, ?string $nivel): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE grupo SET nombre=:n, descripcion=:d, nivel=:ni WHERE id_grupo=:id_grupo"
        );
 
        return $stmt->execute([
            ':n' => $nombre,
            ':d' => $descripcion,
            ':ni' => $nivel
            
        ]);
    }
 
    // DELETE
    public function eliminar(int $id_grupo): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM grupo WHERE id_grupo=:id_grupo"
        );
 
        return $stmt->execute([':id_grupo' => $id_grupo]);
    }
}