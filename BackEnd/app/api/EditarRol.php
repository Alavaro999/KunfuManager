<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;
use App\Models\RolModel;

Dotenv::createImmutable(__DIR__ . '/../')->load();

$pdo = Database::connect();
$model = new RolModel($pdo);

$id = (int) $_GET['id'];

// Obtener el rol
$stmt = $pdo->prepare("SELECT * FROM roles WHERE id_rol = :id");
$stmt->execute([':id' => $id]);
$rol = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    $nombre = trim($_POST['nombre']);
    if ($nombre !== '') {
        $stmt = $pdo->prepare("UPDATE roles SET nombre_rol = :nombre WHERE id_rol = :id");
        $stmt->execute([':nombre' => $nombre, ':id' => $id]);
        header("Location: index.php");
        exit;
    }
}
?>

<h1>Editar rol</h1>

<form method="post">
    <input name="nombre" value="<?= htmlspecialchars($rol['nombre_rol']) ?>" required>
    <button>Actualizar</button>
</form>
