<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Estudiante.php';
require_once __DIR__ . '/models/ValidadorEstudiantes.php';
require_once __DIR__ . '/interfaces/RepositorioEstudiantes.php';
require_once __DIR__ . '/repositories/EstudianteRepositorioMysql.php';
require_once __DIR__ . '/controllers/EstudianteController.php';

$database = new Database();
$repositorio = new EstudianteRepositorioMysql($database->conectar());
$controlador = new EstudianteController($repositorio, new ValidadorEstudiantes());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    match ($_POST['accion'] ?? '') {
        'crear' => $controlador->crear(),
        'actualizar' => $controlador->actualizar(),
        'eliminar' => $controlador->eliminar(),
        default => $controlador->redirigir('invalido'),
    };
} elseif (($_GET['accion'] ?? '') === 'editar') {
    $controlador->editar();
} else {
    $controlador->listar();
}
