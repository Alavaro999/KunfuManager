<?php
namespace App\Api\Controllers;
use App\Api\Models\UsuarioModel;

$model = new UsuarioModel($pdo);

$id = (int) $_GET['id'];
$usuario = $model->obtenerPorId($id);

if ($_POST) {
    $model->actualizar($id, $_POST['nombre'], $_POST['email']);
    header("Location: index.php");
    exit;
}
?>

<h1>Editar usuario</h1>

<form method="post">
    <input name="nombre" value="<?= $usuario['nombre'] ?>" required>
    <input name="email" value="<?= $usuario['email'] ?>" required>
    <button>Actualizar</button>
</form>