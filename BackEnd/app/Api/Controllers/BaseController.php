<?php

namespace App\Api\Controllers;

class BaseController
{
    /**
     * Devuelve un JSON con código HTTP
     * @param mixed $data
     * @param int $status
     */
    protected function respond($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Devuelve los datos del body en JSON
     * @return array
     */
    protected function getBody(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Validar que existan campos obligatorios en el body
     * @param array $data
     * @param array $requiredFields
     */
    protected function validate(array $data, array $requiredFields): void
    {
        $missing = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            $this->respond([
                'error' => 'Faltan campos obligatorios',
                'fields' => $missing
            ], 422);
        }
    }

    /**
     * Manejar errores internos
     * @param string $message
     * @param int $status
     */
    protected function error(string $message = 'Error interno', int $status = 500): void
    {
        $this->respond(['error' => $message], $status);
    }

    /**
     * Opcional: habilitar CORS
     */
    protected function enableCORS(): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }
}