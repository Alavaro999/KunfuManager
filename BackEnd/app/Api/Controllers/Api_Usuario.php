<?php

namespace App\Api\Controllers\api_Usuario;

use App\Api\Models\UsuarioModel;


class Api_Usuario
{

    static public function crearUsuario(string $nombre, string $email, string $logname, string $pass, string $dni)
    {
        if ($_POST) {
            $model = new UsuarioModel;
            $model->crear($_POST['nombre'], $_POST['email'], $_POST['logname'], $_POST['pass'], $_POST['dni']);
            header("Location: index.php");
            exit;
        }
    }
    static public function eliminarUsuario() {}
    static public function editarUsuario(string $logname)
    {
        $model = new UsuarioModel;

        if ($_POST) {
            $model->actualizar($logname, $_POST['nombre'], $_POST['email']);
            header("Location: index.php");
            exit;
        }
    }
    static public function obetenerUsuario() {}
    static public function obtenerPorId() {}
}
