<?php
namespace app\Models;

use PDO;

class RolModel{
    private PDO $pdo;

    public function  __construct(PDO $pdo)
    {
        $this ->pdo= $pdo;
    }

    //FUNCIONES PARA LOS ROLES
    
    //OBTENER TODOS LOS ROLES
    public function obtenerTodos(): array{
        $stmt = $this->pdo->query("SELECT * FROM roles ORDER BY id_rol ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //ASIGNAR UN ROL A UN USUARIO
    public function asignarRol(int $idUsuario, int $idRol): bool{
        $stmt = $this->pdo->prepare("INSERT INTO usuarios_roles (is_usuario, id_rol) VALUES (:usuario, :rol");
        return $stmt->execute([
            ':usuario' =>$idUsuario,
            ':rol' => $idRol
        ]);
    }

    //QUITAR UN ROL
    public function quitarRol(int $idUsuario, int $idRol): bool{
        $stmt = $this->pdo->prepare(
            "DELETE FROM usuario_roles WHERE id_usuario = :usuario AND id_rol = :rol"
        );
        return $stmt->execute([
            ':usuario' => $idUsuario,
            ':rol' => $idRol
        ]);
    }

    //OBTEMER ROL DE UN USUARIO
    public function obtenerRolesDeUsuario(int $idUsuario): array{
         $stmt = $this->pdo->prepare(
            "SELECT r.* 
             FROM roles r
             JOIN usuario_roles ur ON r.id_rol = ur.id_rol
             WHERE ur.id_usuario = :usuario"
        );
        $stmt->execute([':usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}