<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;

Dotenv::createImmutable(__DIR__ . '/../')->load();

$pdo = Database::connect();

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM roles WHERE id_rol = :id");
$stmt->execute([':id' => $id]);

header("Location: index.php");
exit;
