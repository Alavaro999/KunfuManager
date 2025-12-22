<?php
namespace App\Api\Models;
require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;
use PDO;
Dotenv::createImmutable(__DIR__ . '/../../../')->load();

class UsuarioModel
{

    // CREATE
    public function crear(string $nombre, string $email, string $logname, string $pass, string $dni): bool
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare(
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

    // Obtener Usuarios
    public function obtenerTodos(): array
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query("SELECT * FROM usuario ORDER BY id_usuario DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Manejo de error: tabla inexistente u otro error
            error_log("Error en obtenerTodos: " . $e->getMessage());
            return []; // Retorna un array vacío si hay error
        }
    }
    //Obtener Por Id
    public function obtenerPorId(int $id): array|false
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT * FROM usuarios WHERE id_usuario = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function obtenerPorLogName(string $logname): array|false
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT * FROM usuario WHERE logname = :logname"
        );
        $stmt->execute([':logname' => $logname]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function actualizar(int $id, string $nombre, string $email): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "UPDATE usuario SET nombre = :nombre, email = :email WHERE id_usuario = :id"
        );

        return $stmt->execute([':id' => $id, ':nombre' => $nombre, ':email'  => $email]);
    }

    // DELETE
    public function eliminar(int $id): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "DELETE FROM usuarios WHERE id=:id"
        );

        return $stmt->execute([':id' => $id]);
    }
}
