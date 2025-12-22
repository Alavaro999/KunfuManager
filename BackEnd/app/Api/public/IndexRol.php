<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;

Dotenv::createImmutable(__DIR__ . '/../')->load();

$pdo = Database::connect();

$stmt = $pdo->query("SELECT * FROM roles ORDER BY id_rol ASC");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Roles</h1>

<a href="Crear.php">Crear nuevo rol</a>

<ul>
<?php foreach ($roles as $rol): ?>
    <li>
        <?= htmlspecialchars($rol['nombre_rol']) ?>
        <a href="Editar.php?id=<?= $rol['id_rol'] ?>">Editar</a>
        <a href="Eliminar.php?id=<?= $rol['id_rol'] ?>" onclick="return confirm('Eliminar rol?')">Eliminar</a>
    </li>
<?php endforeach; ?>
</ul>
