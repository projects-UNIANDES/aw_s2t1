<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Cliente.php';
require_once __DIR__ . '/models/ValidadorClientes.php';
require_once __DIR__ . '/interfaces/RepositorioClientes.php';
require_once __DIR__ . '/repositories/ClienteRepositorioMysql.php';
require_once __DIR__ . '/controllers/ClienteController.php';

$database = new Database();
$repositorio = new ClienteRepositorioMysql($database->conectar());
$controlador = new ClienteController($repositorio, new ValidadorClientes());

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
