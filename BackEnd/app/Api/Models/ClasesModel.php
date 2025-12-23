<?php

namespace App\Api\Models;

require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;
use PDO;

Dotenv::createImmutable(__DIR__ . '/../../../')->load();


class ClasesModel
{
    private PDO $pdo;
    //Creando la clase 
    //En el dia de la seamana, el 0 es Domingo, y el 6 es sabado
    public static function crearClase(int $diaSemana, int $horaInicio, int $horaFin)
    {
        $pdo = Database::connect();

        $stmt = $pdo->query("INSERT INTO clases(id_grupo,id_usuario,dia_semana,hora_inicio,hora_fin) VALUES (:idG,:idU,:diaS,:horaI,:horaF)");
    }
}
