<?php

namespace App\Api\Controllers;

use App\Api\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
        $this->enableCORS(); // Si quieres acceder desde frontend
    }

    /**
     * GET /users
     */
    public function index(): void
    {
        $usuarios = $this->model->obtenerTodos();
        $this->respond($usuarios);
    }

    /**
     * GET /users/{id}
     */
    public function show(int $id): void
    {
        $usuario = $this->model->obtenerPorId($id);
        if (!$usuario) {
            $this->respond(['error' => 'Usuario no encontrado'], 404);
        } else {
            $this->respond($usuario);
        }
    }

    /**
     * POST /users
     */
    public function store(): void
    {
        $data = $this->getBody();
        $this->validate($data, ['nombre', 'email', 'pass', 'logname']); // campos obligatorios

        $success = $this->model->crear(
            $data['nombre'],
            $data['email'],
            $data['logname'],
            $data['pass'],
            $data['dni'] ?? ''
        );

        if ($success) {
            $this->respond(['message' => 'Usuario creado'], 201);
        } else {
            $this->error('Error al crear usuario');
        }
    }

    /**
     * PUT /users/{id}
     */
    public function update(int $id): void
    {
        $data = $this->getBody();
        $this->validate($data, ['nombre', 'email']);

        $success = $this->model->actualizar($id, $data['nombre'], $data['email']);
        if ($success) {
            $this->respond(['message' => 'Usuario actualizado']);
        } else {
            $this->error('Error al actualizar usuario');
        }
    }

    /**
     * DELETE /users/{id}
     */
    public function destroy(int $id): void
    {
        $success = $this->model->eliminar($id);
        if ($success) {
            $this->respond(['message' => 'Usuario eliminado']);
        } else {
            $this->error('Error al eliminar usuario');
        }
    }
}
