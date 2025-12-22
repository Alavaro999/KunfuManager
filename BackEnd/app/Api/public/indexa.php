<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Api\Controllers\Crear;
use App\Api\Controllers\UsuarioController;

Dotenv::createImmutable(__DIR__ . '/../../../')->load();

header('Content-Type: application/json');

// Tomamos la URL


$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/')); 
$method = $_SERVER['REQUEST_METHOD'];

// Ajusta según la estructura de tu servidor
$resource = strtolower($uri[count($uri)-1]); // toma 'users' al final

if ($resource === 'users') {
    $controller = new UsuarioController();

    if ($method === 'POST') {
        $controller->store();
    }

}