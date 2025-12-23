<?php

namespace app\Api\Models;

require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;
use PDO;

Dotenv::createImmutable(__DIR__ . '/../../../')->load();


class GrupoModel
{

    private PDO $pdo;

    //Crear Grupo
    public static function crearGrupo(string $nombre, string $descripcion = "", string $nivel = "")
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query("INSERT INTO grupos(nombre,descripcion,nivel) VALUES (:n,:d,:nivel) ");
            return $stmt->execute([
                ':n' => $nombre,
                ':d' => $descripcion,
                ':nivel' => $nivel
            ]);
        } catch (\PDOException $e) {
            error_log("Error al crear el Grupo: " . $e->getMessage());
        }
    }

    //Ver grupos
    public static function obtenerGrupos()
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query("SELECT * FROM grupos ORDER BY id_grupo");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerTodos: " . $e->getMessage());
            return [];
        }
    }

    //Ver por nombre de grupo
    public static function obtenerGrupoPorNombre(string $nombre)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT * FROM grupos WHERE nombre = :n"
        );
        $stmt->execute([':n' => $nombre]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Eliminar Grupo
    public static function eliminarGrupo(string $nombre)
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("DELETE FROM grupo WHERE nombre = :n");
        $stmt = $stmt->execute([':n' => $nombre]);
    }

    //Actualizar
    public static function actualizarGrupo(string $nombre, string $descripcion, string $nivel)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "UPDATE grupos SET nombre = :n, descripcion = :d, nivel = :nivel WHERE id_usuario = :id"
        );

        return $stmt->execute([':n' => $nombre, ':d' => $descripcion, ':nivel' => $nivel]);
    }
}
