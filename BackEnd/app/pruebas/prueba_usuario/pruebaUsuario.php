<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;
use App\Models\UsuarioModel;

Dotenv::createImmutable(__DIR__ . '/../../../')->load();

$pdo = Database::connect();
$model = new UsuarioModel($pdo);

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'create') {
        $model->crear($_POST['nombre'], $_POST['email'], $_POST['logname'], $_POST['pass'], $_POST['dni']);
        header("Location: ?action=list");
        exit;
    } elseif ($_POST['action'] === 'update') {
        $model->actualizar((int)$_POST['id'], $_POST['nombre'], $_POST['email']);
        header("Location: ?action=list");
        exit;
    } elseif ($_POST['action'] === 'delete') {
        $model->eliminar((int)$_POST['id']);
        header("Location: ?action=list");
        exit;
    }
}

if ($id && ($action === 'edit' || $action === 'delete')) {
    $usuario = $model->obtenerPorId($id);
}
?>

<?php if ($action === 'list'): ?>
    <h1>Listado de usuarios</h1>
    <a href="?action=create">➕ Nuevo usuario</a>
    
    <?php $usuarios = $model->obtenerTodos(); ?>
    <table border="1">
    <tr><th>Nombre</th><th>Email</th><th>Acciones</th></tr>
    <?php foreach ($usuarios as $u): ?>
    <tr>
        <td><?= htmlspecialchars($u['nombre']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td>
            <a href="?action=edit&id=<?= $u['id_usuario'] ?>">✏️ Editar</a>
            <a href="?action=delete&id=<?= $u['id_usuario'] ?>" onclick="return confirm('¿Seguro?')">🗑️ Borrar</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </table>

<?php elseif ($action === 'create'): ?>
    <h1>Crear usuario</h1>
    <form method="post">
        <input type="hidden" name="action" value="create">
        <input name="nombre" placeholder="Nombre" required>
        <input name="email" type="email" placeholder="Email" required>
        <input name="logname" placeholder="Logname" required>
        <input name="pass" type="password" placeholder="Contraseña" required>
        <input name="dni" placeholder="DNI" required>
        <button>Guardar</button>
    </form>

<?php elseif ($action === 'edit' && isset($usuario)): ?>
    <h1>Editar usuario</h1>
    <form method="post">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">
        <input name="nombre" value="<?= $usuario['nombre'] ?>" required>
        <input name="email" value="<?= $usuario['email'] ?>" required>
        <button>Actualizar</button>
    </form>

<?php elseif ($action === 'delete' && isset($usuario)): ?>
    <h1>Eliminar usuario</h1>
    <p>¿Seguro que quieres eliminar a <?= htmlspecialchars($usuario['nombre']) ?>?</p>
    <form method="post">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">
        <button>Si, eliminar</button>
        <a href="?action=list">Cancelar</a>
    </form>
<?php endif; ?>