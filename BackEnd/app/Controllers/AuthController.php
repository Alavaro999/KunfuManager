<?php
namespace App\Controllers;

use PDO;
use App\Models\UsuarioModel;

class AuthController
{
    private PDO $pdo;
    private UsuarioModel $usuarioModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->usuarioModel = new UsuarioModel($pdo);
    }

    public function login()
    {
        // Obtener datos del request
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['dni']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'DNI y contraseña son requeridos']);
            return;
        }

        $dni = $data['dni'];
        $password = $data['password'];

        // Verificar credenciales
        $usuario = $this->usuarioModel->verificarCredenciales($dni, $password);
        
        if ($usuario) {
            // Generar token JWT simple (en producción usaría una librería)
            $token = $this->generarToken($usuario);
            
            echo json_encode([
                'success' => true,
                'token' => $token,
                'usuario' => [
                    'id' => $usuario['id_usuario'],
                    'nombre' => $usuario['nombre'],
                    'apellidos' => $usuario['apellidos'],
                    'email' => $usuario['email'],
                    'dni' => $usuario['dni'],
                    'cinturon' => $usuario['cinturon'],
                    'rol' => $this->determinarRol($usuario['id_usuario'])
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Credenciales incorrectas']);
        }
    }

    private function generarToken(array $usuario): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'sub' => $usuario['id_usuario'],
            'dni' => $usuario['dni'],
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60) // 24 horas
        ]);
        
        $base64Header = base64_encode($header);
        $base64Payload = base64_encode($payload);
        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", 'secreto_kungfu', true);
        $base64Signature = base64_encode($signature);
        
        return "$base64Header.$base64Payload.$base64Signature";
    }

    private function determinarRol(int $id_usuario): string
    {
        // Verificar si es profesor
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM clases WHERE id_profesor = ?");
        $stmt->execute([$id_usuario]);
        $esProfesor = $stmt->fetchColumn() > 0;
        
        return $esProfesor ? 'profesor' : 'alumno';
    }
}