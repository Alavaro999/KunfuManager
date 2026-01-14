<?php

namespace App\Api\Controllers;

use App\Api\Models\UsuarioModel;

class Crear
{
    public function __construct()
    {
        
    }
    public function crearUsuario()
    {
        // Recibimos datos JSON
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            exit;
        }

        // Validación mínima
        if (empty($data['nombre']) || empty($data['email']) || empty($data['pass'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Campos obligatorios faltan']);
            exit;
        }

        // Llamamos al modelo
        $model = new UsuarioModel();
        $model->crear(
            $data['nombre'], 
            $data['email'], 
            $data['logname'] ?? null, 
            $data['pass'], 
            $data['dni'] ?? null
        );

        // Devolvemos respuesta JSON
        http_response_code(201);
        echo json_encode(['message' => 'Usuario creado']);
        exit;
    }
}
