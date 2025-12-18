<?php
require_once __DIR__ . '/../vendor/autoload.php';
 
use Dotenv\Dotenv;
use App\Config\Database;
use App\Models\UsuarioModel;
 
Dotenv::createImmutable(__DIR__ . '/../')->load();
 
$pdo = Database::connect();
$model = new UsuarioModel($pdo);
 
if ($_POST) {
    $model->crear($_POST['nombre'], $_POST['email'], $_POST['logname'], $_POST['pass'], $_POST['dni']);
    header("Location: index.php");
    exit;
}
?>
 
<h1>Crear usuario</h1>
 
<form method="post">
<input name="nombre" placeholder="Nombre" required>
<input name="email" type="email" placeholder="Email" required>
<input name="logname" type="text" placeholder="Logname" required>
<input name="pass" type="password" placeholder="pass" required>
<input name="dni" type="text" placeholder="Dni" required>
<button>Guardar</button>
</form>