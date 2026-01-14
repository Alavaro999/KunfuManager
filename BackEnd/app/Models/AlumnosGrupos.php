<?php
namespace App\Models;

use PDO;

class AlumnosGruposModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // CREATE - Inscribir alumno en grupo
    public function crear(int $id_alumno, int $id_grupo): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO alumnos_grupos (id_alumno, id_grupo) VALUES (:id_alumno, :id_grupo)"
        );
        return $stmt->execute([
            ':id_alumno' => $id_alumno,
            ':id_grupo' => $id_grupo
        ]);
    }

    // READ - Obtener todos los registros
    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT ag.*, u.nombre as alumno_nombre, g.nombre as grupo_nombre
             FROM alumnos_grupos ag
             INNER JOIN usuario u ON ag.id_alumno = u.id_usuario
             INNER JOIN grupos g ON ag.id_grupo = g.id_grupo
             ORDER BY ag.fecha_alta DESC"
        )->fetchAll();
    }

    // Obtener grupos de un alumno
    public function obtenerGruposPorAlumno(int $id_alumno): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT g.*, ag.fecha_alta
             FROM alumnos_grupos ag
             INNER JOIN grupos g ON ag.id_grupo = g.id_grupo
             WHERE ag.id_alumno = :id_alumno
             ORDER BY g.nombre"
        );
        $stmt->execute([':id_alumno' => $id_alumno]);
        return $stmt->fetchAll();
    }

    // Obtener alumnos de un grupo
    public function obtenerAlumnosPorGrupo(int $id_grupo): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.*, ag.fecha_alta
             FROM alumnos_grupos ag
             INNER JOIN usuario u ON ag.id_alumno = u.id_usuario
             WHERE ag.id_grupo = :id_grupo
             ORDER BY u.nombre"
        );
        $stmt->execute([':id_grupo' => $id_grupo]);
        return $stmt->fetchAll();
    }

    // DELETE - Eliminar inscripción
    public function eliminar(int $id_alumno, int $id_grupo): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM alumnos_grupos WHERE id_alumno = :id_alumno AND id_grupo = :id_grupo"
        );
        return $stmt->execute([
            ':id_alumno' => $id_alumno,
            ':id_grupo' => $id_grupo
        ]);
    }

    // Verificar si alumno está en grupo
    public function existe(int $id_alumno, int $id_grupo): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM alumnos_grupos WHERE id_alumno = :id_alumno AND id_grupo = :id_grupo"
        );
        $stmt->execute([
            ':id_alumno' => $id_alumno,
            ':id_grupo' => $id_grupo
        ]);
        return $stmt->fetchColumn() > 0;
    }
}