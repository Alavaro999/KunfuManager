<?php

namespace app\Models;

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;
use App\Config\Database;

Dotenv::createImmutable(__DIR__ . '/../')->load();

class RolModel
{
    private PDO $pdo;

    //FUNCIONES PARA LOS ROLES

    //OBTENER TODOS LOS ROLES
    public function obtenerTodos(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM roles ORDER BY id_rol ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //ASIGNAR UN ROL A UN USUARIO
    public function asignarRol(int $idUsuario, int $idRol): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO usuarios_roles (id_usuario, id_rol) VALUES (:usuario, :rol");
        return $stmt->execute([
            ':usuario' => $idUsuario,
            ':rol' => $idRol
        ]);
    }

    //QUITAR UN ROL
    public function quitarRol(int $idUsuario, int $idRol): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "DELETE FROM usuario_roles WHERE id_usuario = :usuario AND id_rol = :rol"
        );
        return $stmt->execute([
            ':usuario' => $idUsuario,
            ':rol' => $idRol
        ]);
    }

    //OBTENER ROL DE UN USUARIO
    public function obtenerRolesDeUsuario(int $idUsuario): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT r.* 
             FROM roles r
             JOIN usuario_roles ur ON r.id_rol = ur.id_rol
             WHERE ur.id_usuario = :usuario"
        );
        $stmt->execute([':usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
