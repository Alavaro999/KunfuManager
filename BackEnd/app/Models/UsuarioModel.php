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
    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuario (nombre, apellidos, pass, dni, telefono, email, cinturon) 
             VALUES (:nombre, :apellidos, :pass, :dni, :telefono, :email, :cinturon)"
        );

        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':apellidos' => $datos['apellidos'] ?? null,
            ':pass' => password_hash($datos['pass'], PASSWORD_DEFAULT),
            ':dni' => $datos['dni'],
            ':telefono' => $datos['telefono'] ?? null,
            ':email' => $datos['email'] ?? null,
            ':cinturon' => $datos['cinturon'] ?? null
        ]);
    }

    // READ
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT * FROM usuarios"
        )->fetchAll();
    }

    public function obtenerPorId(int $id_usuario): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuario WHERE id_usuario = :id"
        );
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetch();
    }

    public function obtenerPorDNI(string $dni): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuario WHERE dni = :dni"
        );
        $stmt->execute([':dni' => $dni]);
        return $stmt->fetch();
    }

    public function obtenerPorEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuario WHERE email = :email"
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    // UPDATE
    public function actualizar(int $id_usuario, array $datos): bool
    {
        // Si se actualiza la contraseña, hay que hashearla
        if (isset($datos['pass'])) {
            $datos['pass'] = password_hash($datos['pass'], PASSWORD_DEFAULT);
        }

        $campos = [];
        $valores = [':id' => $id_usuario];

        foreach ($datos as $key => $value) {
            if ($key === 'pass' && empty($value)) {
                continue; // No actualizar contraseña si está vacía
            }
            $campos[] = "$key = :$key";
            $valores[":$key"] = $value;
        }

        $sql = "UPDATE usuario SET " . implode(', ', $campos) . " WHERE id_usuario = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($valores);
    }

    // DELETE
    public function eliminar(int $id_usuario): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM usuario WHERE id_usuario = :id"
        );
        return $stmt->execute([':id' => $id_usuario]);
    }

    // Búsqueda
    public function buscar(string $termino): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuario 
             WHERE nombre LIKE :termino 
                OR apellidos LIKE :termino 
                OR dni LIKE :termino 
                OR email LIKE :termino
             ORDER BY nombre"
        );
        $stmt->execute([':termino' => "%$termino%"]);
        return $stmt->fetchAll();
    }

    // Autenticación
    public function verificarCredenciales(string $dni, string $pass): array|false
    {
        $usuario = $this->obtenerPorDNI($dni);

        if ($usuario && password_verify($pass, $usuario['pass'])) {
            unset($usuario['pass']); // Eliminar contraseña del array
            return $usuario;
        }

        return false;
    }

    // Obtener alumnos (usuarios que no son profesores)
    public function obtenerAlumnos(): array
    {
        return $this->pdo->query(
            "SELECT u.* FROM usuario u
             LEFT JOIN clases c ON u.id_usuario = c.id_profesor
             WHERE c.id_profesor IS NULL
             ORDER BY u.nombre"
        )->fetchAll();
    }

    // Obtener profesores
    public function obtenerProfesores(): array
    {
        return $this->pdo->query(
            "SELECT DISTINCT u.* FROM usuario u
             INNER JOIN clases c ON u.id_usuario = c.id_profesor
             ORDER BY u.nombre"
        )->fetchAll();
    }
}
