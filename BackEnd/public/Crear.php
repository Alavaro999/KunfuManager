<?php
require_once __DIR__ . '/../vendor/autoload.php';
 
use Dotenv\Dotenv;
use App\Config\Database;
use App\Models\UsuarioModel;
 
Dotenv::createImmutable(__DIR__ . '/../')->load();
 
$pdo = Database::connect();
$model = new UsuarioModel($pdo);
 
if ($_POST) {
    $model->crear($_POST['nombre'], $_POST['email']);
    header("Location: index.php");
    exit;
}
?>
 
<h1>Crear usuario</h1>
 
<form method="post">
<input name="nombre" placeholder="Nombre" required>
<input name="email" type="email" placeholder="Email" required>
<button>Guardar</button>
</form>