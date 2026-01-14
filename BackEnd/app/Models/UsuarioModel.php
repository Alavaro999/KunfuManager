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
    try {
        $stmt = $this->pdo->query("SELECT * FROM usuario ORDER BY id_usuario DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        // Manejo de error: tabla inexistente u otro error
        error_log("Error en obtenerTodos: " . $e->getMessage());
        return []; // Retorna un array vacío si hay error
    }
}
 
    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuarios WHERE id_usuario = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function obtenerPorLogName(string $logname): array|false{
        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuario WHERE logname = :logname"
        );
        $stmt->execute([':logname' => $logname]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
      public function actualizar(
        int $id,
        string $nombre,
        string $email
    ): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE usuario
             SET nombre = :nombre,
                 email = :email
             WHERE id_usuario = :id"
        );

        return $stmt->execute([
            ':id'     => $id,
            ':nombre' => $nombre,
            ':email'  => $email
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