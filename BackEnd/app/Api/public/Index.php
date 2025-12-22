<?php
use Dotenv\Dotenv;
use App\Config\Database;
use App\Api\Models\UsuarioModel;


 
header('Content-Type: application/json; charset=utf-8');

// Habilitar CORS (si frontend distinto)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Responder OPTIONS para preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Autoload Composer si existe
if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
}

// Configuración de rutas y controllers
$routes = [
    'users' => \App\Api\Controllers\UsuarioController::class,
    // 'roles' => \App\Api\Controllers\RolController::class,
    // 'grupos' => \App\Api\Controllers\GrupoController::class,
    // 'inventario' => \App\Api\Controllers\InventarioController::class,
    // Agregar más aquí según tus modelos y controllers
];

// Tomar URL
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/')); 

// Extraer recurso y posible ID
// Dependiendo del servidor, ajusta el índice de $uri
$resource = strtolower($uri[count($uri)-1]); 
$id = is_numeric($uri[count($uri)-1]) ? (int)$uri[count($uri)-1] : null;

// Método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Función para enviar JSON de error
function respondError(string $message, int $status = 400) {
    http_response_code($status);
    echo json_encode(['error' => $message]);
    exit;
}

// Verificar recurso
if (!isset($routes[$resource])) {
    respondError("Recurso '$resource' no encontrado", 404);
}

// Instanciar controller
$controllerClass = $routes[$resource];
$controller = new $controllerClass();

// Determinar acción según método HTTP
try {
    switch ($method) {
        case 'GET':
            if ($id) {
                if (method_exists($controller, 'show')) {
                    $controller->show($id);
                } else {
                    respondError("Método GET con ID no implementado", 405);
                }
            } else {
                if (method_exists($controller, 'index')) {
                    $controller->index();
                } else {
                    respondError("Método GET no implementado", 405);
                }
            }
            break;

        case 'POST':
            if (method_exists($controller, 'store')) {
                $controller->store();
            } else {
                respondError("Método POST no implementado", 405);
            }
            break;

        case 'PUT':
            if ($id && method_exists($controller, 'update')) {
                $controller->update($id);
            } else {
                respondError("Método PUT no implementado o ID faltante", 405);
            }
            break;

        case 'DELETE':
            if ($id && method_exists($controller, 'destroy')) {
                $controller->destroy($id);
            } else {
                respondError("Método DELETE no implementado o ID faltante", 405);
            }
            break;

        default:
            respondError("Método HTTP no permitido", 405);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor', 'details' => $e->getMessage()]);
    exit;
}
