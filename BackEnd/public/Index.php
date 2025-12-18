<?php
require_once __DIR__ . '/../vendor/autoload.php';
 
use Dotenv\Dotenv;
use App\Config\Database;
use App\Models\UsuarioModel;
 
Dotenv::createImmutable(__DIR__ . '/../')->load();
 
$pdo = Database::connect();
$model = new UsuarioModel($pdo);
 
$usuarios = $model->obtenerTodos();
?>
 
<h1>Listado de usuarios</h1>
 
<a href="crear.php">➕ Nuevo usuario</a>
 
<table border="1">
<tr>
<th>Nombre</th>
<th>Email</th>
<th>Acciones</th>
</tr>
 
    <?php foreach ($usuarios as $u): ?>
<tr>
<td><?= htmlspecialchars($u['nombre']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td>
<a href="editar.php?id=<?= $u['id_usuario'] ?>">✏️ Editar</a>
<a href="eliminar.php?id=<?= $u['id_usuario'] ?>" 
                   onclick="return confirm('¿Seguro?')">🗑️ Borrar</a>
</td>
</tr>
<?php endforeach; ?>
</table>