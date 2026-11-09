<?php

class ClienteController
{
    private const MENSAJES = [
        'creado' => ['success', 'Cliente registrado correctamente.'],
        'actualizado' => ['success', 'Cliente actualizado correctamente.'],
        'eliminado' => ['success', 'Cliente eliminado correctamente.'],
        'noencontrado' => ['warning', 'No se encontró el cliente solicitado.'],
        'invalido' => ['danger', 'La acción solicitada no es válida.'],
    ];

    private const CIUDADES = ['Quito', 'Guayaquil', 'Cuenca', 'Ambato', 'Manta', 'Loja'];

    public function __construct(
        private RepositorioClientes $repositorio,
        private ValidadorClientes $validador,
    ) {}

    public function listar(): void
    {
        $this->mostrar();
    }

    public function editar(): void
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $cliente = $id === false ? null : $this->repositorio->consultar($id);

        if ($cliente === null) {
            $this->redirigir('noencontrado');
        }

        $this->mostrar([], [
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
            'correo' => $cliente->correo,
            'telefono' => $cliente->telefono,
            'ciudad' => $cliente->ciudad,
            'activo' => $cliente->activo,
        ]);
    }

    public function crear(): void
    {
        $datos = $this->leerFormulario();
        $errores = $this->validar($datos);

        if ($errores !== []) {
            $this->mostrar($errores, $datos);
            return;
        }

        $this->repositorio->registrar($this->aCliente($datos, null));
        $this->redirigir('creado');
    }

    public function actualizar(): void
    {
        $id = $this->leerId();
        if ($id === false) {
            $this->redirigir('invalido');
        }

        $datos = $this->leerFormulario();
        $datos['id'] = $id;
        $errores = $this->validar($datos);

        if ($errores !== []) {
            $this->mostrar($errores, $datos);
            return;
        }

        $existe = $this->repositorio->actualizar($this->aCliente($datos, $id));
        $this->redirigir($existe ? 'actualizado' : 'noencontrado');
    }

    public function eliminar(): void
    {
        $id = $this->leerId();
        if ($id === false) {
            $this->redirigir('invalido');
        }

        $this->redirigir($this->repositorio->eliminar($id) ? 'eliminado' : 'noencontrado');
    }

    public function redirigir(string $mensaje): never
    {
        header('Location: index.php?mensaje=' . urlencode($mensaje));
        exit;
    }

    private function leerId(): int|false
    {
        return filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    }

    private function leerFormulario(): array
    {
        return [
            'id' => null,
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'correo' => trim((string) ($_POST['correo'] ?? '')),
            'telefono' => trim((string) ($_POST['telefono'] ?? '')),
            'ciudad' => trim((string) ($_POST['ciudad'] ?? '')),
            'activo' => isset($_POST['activo']),
        ];
    }

    private function validar(array $datos): array
    {
        $errores = $this->validador->validar($datos['nombre'], $datos['correo'], $datos['telefono'], $datos['ciudad']);

        if (!in_array($datos['ciudad'], self::CIUDADES, true) && $datos['ciudad'] !== '') {
            $errores[] = 'La ciudad seleccionada no es válida.';
        }

        return $errores;
    }

    private function aCliente(array $datos, ?int $id): Cliente
    {
        return new Cliente($id, $datos['nombre'], $datos['correo'], $datos['telefono'], $datos['ciudad'], $datos['activo']);
    }

    private function mostrar(array $errores = [], array $datos = []): void
    {
        $clientes = $this->repositorio->listar();
        $ciudades = self::CIUDADES;
        $editando = ($datos['id'] ?? null) !== null;
        $mensaje = self::MENSAJES[$_GET['mensaje'] ?? ''] ?? null;

        require __DIR__ . '/../views/clientes.php';
    }
}
