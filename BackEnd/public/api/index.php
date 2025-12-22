<?php
// public/api/index.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Database;

// Inicializar conexión a BD
$database = new Database();
$pdo = $database->getConnection();

// Obtener método y ruta
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Eliminar /api del path si está presente
$path = str_replace('/api', '', $path);
$path = trim($path, '/');
$segments = explode('/', $path);

// Router básico
try {
    // Endpoint raíz
    if (empty($path) || $path === 'index.php') {
        echo json_encode([
            'status' => 'success',
            'message' => 'API Kung Fu Manager v1.0',
            'endpoints' => [
                'POST /api/auth/login' => 'Iniciar sesión',
                'GET /api/usuarios' => 'Obtener usuarios',
                'GET /api/alumnos' => 'Obtener alumnos',
                'GET /api/profesores' => 'Obtener profesores',
                'GET /api/grupos' => 'Obtener grupos',
                'GET /api/clases' => 'Obtener clases',
                'GET /api/horario' => 'Obtener horario semanal',
                'GET /api/ausencias' => 'Obtener ausencias',
                'GET /api/inventario' => 'Obtener inventario',
                'GET /api/facturas' => 'Obtener facturas',
                'GET /api/pagos' => 'Obtener pagos'
            ]
        ]);
        exit;
    }

    // Routing basado en el primer segmento
    $resource = $segments[0] ?? '';
    $id = $segments[1] ?? null;

    switch ($resource) {
        case 'auth':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController($pdo);
            handleAuthRoutes($controller, $method, $segments);
            break;

        case 'usuarios':
            require_once __DIR__ . '/controllers/UsuarioController.php';
            $controller = new UsuarioController($pdo);
            handleUsuarioRoutes($controller, $method, $id, $segments);
            break;

        // case 'alumnos':
        //     require_once __DIR__ . '/controllers/UsuarioController.php';
        //     $controller = new UsuarioController($pdo);
        //     handleAlumnoRoutes($controller, $method, $id, $segments);
        //     break;

        // case 'profesores':
        //     require_once __DIR__ . '/controllers/UsuarioController.php';
        //     $controller = new UsuarioController($pdo);
        //     handleProfesorRoutes($controller, $method, $id);
        //     break;

        // case 'grupos':
        //     require_once __DIR__ . '/controllers/GrupoController.php';
        //     $controller = new GrupoController($pdo);
        //     handleGrupoRoutes($controller, $method, $id, $segments);
        //     break;

        // case 'clases':
        //     require_once __DIR__ . '/controllers/ClaseController.php';
        //     $controller = new ClaseController($pdo);
        //     handleClaseRoutes($controller, $method, $id, $segments);
        //     break;

        // case 'horario':
        //     require_once __DIR__ . '/controllers/ClaseController.php';
        //     $controller = new ClaseController($pdo);
        //     $controller->obtenerHorarioSemanal();
        //     break;

        // case 'ausencias':
        //     require_once __DIR__ . '/controllers/AusenciaController.php';
        //     $controller = new AusenciaController($pdo);
        //     handleAusenciaRoutes($controller, $method, $id, $segments);
        //     break;

        // case 'inventario':
        //     require_once __DIR__ . '/controllers/InventarioController.php';
        //     $controller = new InventarioController($pdo);
        //     handleInventarioRoutes($controller, $method, $id, $segments);
        //     break;

        // case 'solicitudes':
        //     require_once __DIR__ . '/controllers/SolicitudController.php';
        //     $controller = new SolicitudController($pdo);
        //     handleSolicitudRoutes($controller, $method, $id, $segments);
        //     break;

        // case 'facturas':
        //     require_once __DIR__ . '/controllers/FacturaController.php';
        //     $controller = new FacturaController($pdo);
        //     handleFacturaRoutes($controller, $method, $id, $segments);
        //     break;

        // case 'pagos':
        //     require_once __DIR__ . '/controllers/PagoController.php';
        //     $controller = new PagoController($pdo);
        //     handlePagoRoutes($controller, $method, $id, $segments);
        //     break;

        // case 'dashboard':
        //     require_once __DIR__ . '/controllers/DashboardController.php';
        //     $controller = new DashboardController($pdo);
        //     handleDashboardRoutes($controller, $method, $segments);
        //     break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'message' => $e->getMessage()
    ]);
}

// Funciones de routing para cada recurso
function handleAuthRoutes($controller, $method, $segments) {
    $action = $segments[1] ?? '';
    
    switch ($method) {
        case 'POST':
            if ($action === 'login') {
                $controller->login();
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint de auth no encontrado']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
}

function handleUsuarioRoutes($controller, $method, $id, $segments) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $controller->obtenerPorId($id);
            } else {
                $controller->obtenerTodos();
            }
            break;
        case 'POST':
            $controller->crear();
            break;
        case 'PUT':
            if ($id) {
                $controller->actualizar($id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Se requiere ID de usuario']);
            }
            break;
        case 'DELETE':
            if ($id) {
                $controller->eliminar($id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Se requiere ID de usuario']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
}

// Funciones similares para otros recursos...
// (Se implementarían siguiendo el mismo patrón)