<?php
require_once __DIR__ . '/../vendor/autoload.php';
 
use Dotenv\Dotenv;
use App\Config\Database;
use App\Models\GruposModel;
 
Dotenv::createImmutable(__DIR__ . '/../')->load();
 
$pdo = Database::connect();
$model = new GruposModel($pdo);
 
if ($_POST) {
    $model->crear($_POST['nombre'], $_POST['descripcion'], $_POST['nivel']);
    header("Location: index.php");
    exit;
}
?>
 
<h1>Crear Grupo</h1>
 
<form method="post">
<input name="nombre" placeholder="Nombre" required>
<input name="descripcion" type="text" placeholder="Descripcion" required>
<input name="nivel" type="number" placeholder="NIvel" required>
<button>Guardar</button>
</form>