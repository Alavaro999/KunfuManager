<?php
namespace App\Models;
 
use PDO;
 
class UsuarioModel
{
    private PDO $pdo;
 
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
 
    // CREATE
    public function crear(string $nombre, string $email, string $logname, string $pass, string $dni): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuario (nombre, email, logname, pass, dni) VALUES (:n, :e, :l, :c, :d)"
        );
 
        return $stmt->execute([
            ':n' => $nombre,
            ':e' => $email,
            ':l' => $logname,
            ':c' => $pass,
            ':d' => $dni
        ]);
    }
 
    // READ
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT * FROM usuario"
        )->fetchAll();
    }
 
    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuarios WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
 
    // UPDATE
    public function actualizar(int $id, string $nombre, string $email): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE usuarios SET nombre=:n, email=:e WHERE id=:id"
        );
 
        return $stmt->execute([
            ':n' => $nombre,
            ':e' => $email,
            ':id' => $id
        ]);
    }
 
    // DELETE
    public function eliminar(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM usuarios WHERE id=:id"
        );
 
        return $stmt->execute([':id' => $id]);
    }
}