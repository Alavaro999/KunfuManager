<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;
use App\Models\RolModel;

Dotenv::createImmutable(__DIR__ . '/../')->load();

$pdo = Database::connect();
$model = new RolModel($pdo);

if ($_POST) {
    $nombre = trim($_POST['nombre']);
    if ($nombre !== '') {
        // Insertar rol
        $stmt = $pdo->prepare("INSERT INTO roles (nombre_rol) VALUES (:nombre)");
        $stmt->execute([':nombre' => $nombre]);
        header("Location: index.php");
        exit;
    }
}
?>

<h1>Crear rol</h1>

<form method="post">
    <input name="nombre" placeholder="Nombre del rol" required>
    <button>Crear</button>
</form>




































