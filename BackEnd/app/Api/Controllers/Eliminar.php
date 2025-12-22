<?php
require_once __DIR__ . '/../vendor/autoload.php';
 
use Dotenv\Dotenv;
use App\Config\Database;
use App\Api\Models\UsuarioModel;
 
Dotenv::createImmutable(__DIR__ . '/../')->load();
 
$pdo = Database::connect();
$model = new UsuarioModel($pdo);
 
$id = (int) $_GET['id'];
$model->eliminar($id);
 
header("Location: index.php");
exit;