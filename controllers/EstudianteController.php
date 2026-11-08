<?php

class EstudianteController
{
    private const MENSAJES = [
        'creado' => ['success', 'Estudiante registrado correctamente.'],
        'actualizado' => ['success', 'Estudiante actualizado correctamente.'],
        'eliminado' => ['success', 'Estudiante eliminado correctamente.'],
        'noencontrado' => ['warning', 'No se encontró el estudiante solicitado.'],
        'invalido' => ['danger', 'La acción solicitada no es válida.'],
    ];

    private const CARRERAS = ['Software', 'Derecho', 'Medicina', 'Contabilidad', 'Enfermería', 'Administración'];

    public function __construct(
        private RepositorioEstudiantes $repositorio,
        private ValidadorEstudiantes $validador,
    ) {}

    public function listar(): void
    {
        $this->mostrar();
    }

    public function editar(): void
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $estudiante = $id === false ? null : $this->repositorio->consultar($id);

        if ($estudiante === null) {
            $this->redirigir('noencontrado');
        }

        $this->mostrar([], [
            'id' => $estudiante->id,
            'nombre' => $estudiante->nombre,
            'correo' => $estudiante->correo,
            'carrera' => $estudiante->carrera,
            'semestre' => $estudiante->semestre,
            'activo' => $estudiante->activo,
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

        $this->repositorio->registrar($this->aEstudiante($datos, null));
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

        $existe = $this->repositorio->actualizar($this->aEstudiante($datos, $id));
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
            'carrera' => trim((string) ($_POST['carrera'] ?? '')),
            'semestre' => trim((string) ($_POST['semestre'] ?? '')),
            'activo' => isset($_POST['activo']),
        ];
    }

    private function validar(array $datos): array
    {
        $errores = $this->validador->validar($datos['nombre'], $datos['correo'], $datos['carrera'], $datos['semestre']);

        if (!in_array($datos['carrera'], self::CARRERAS, true) && $datos['carrera'] !== '') {
            $errores[] = 'La carrera seleccionada no es válida.';
        }

        return $errores;
    }

    private function aEstudiante(array $datos, ?int $id): Estudiante
    {
        return new Estudiante($id, $datos['nombre'], $datos['correo'], $datos['carrera'], (int) $datos['semestre'], $datos['activo']);
    }

    private function mostrar(array $errores = [], array $datos = []): void
    {
        $estudiantes = $this->repositorio->listar();
        $carreras = self::CARRERAS;
        $editando = ($datos['id'] ?? null) !== null;
        $mensaje = self::MENSAJES[$_GET['mensaje'] ?? ''] ?? null;

        require __DIR__ . '/../views/estudiantes.php';
    }
}
