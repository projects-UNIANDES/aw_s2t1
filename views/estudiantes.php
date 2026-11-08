<?php

$estudiantes ??= [];
$carreras ??= [];
$errores ??= [];
$datos ??= [];
$editando ??= false;
$mensaje ??= null;

function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Estudiantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar bg-body-tertiary mb-4">
        <div class="container">
            <span class="navbar-brand mb-0">Gestión de Estudiantes</span>
        </div>
    </nav>

    <main class="container pb-5">
        <?php if ($mensaje !== null): ?>
            <div class="alert alert-<?= e($mensaje[0]) ?> alert-dismissible fade show" role="alert">
                <?= e($mensaje[1]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>

        <?php if ($errores !== []): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errores as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header"><?= $editando ? 'Editar estudiante' : 'Nuevo estudiante' ?></div>
            <div class="card-body">
                <form method="post" action="index.php" class="row g-3">
                    <input type="hidden" name="accion" value="<?= $editando ? 'actualizar' : 'crear' ?>">
                    <input type="hidden" name="id" value="<?= e($datos['id'] ?? '') ?>">

                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="120" required
                            value="<?= e($datos['nombre'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo" maxlength="150" required
                            value="<?= e($datos['correo'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="carrera" class="form-label">Carrera</label>
                        <select class="form-select" id="carrera" name="carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?= e($carrera) ?>" <?= ($datos['carrera'] ?? '') === $carrera ? 'selected' : '' ?>><?= e($carrera) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="semestre" class="form-label">Semestre</label>
                        <select class="form-select" id="semestre" name="semestre" required>
                            <option value="">Seleccione</option>
                            <?php for ($s = 1; $s <= 10; $s++): ?>
                                <option value="<?= $s ?>" <?= (string) ($datos['semestre'] ?? '') === (string) $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                <?= ($datos['activo'] ?? true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <?= $editando ? 'Guardar cambios' : 'Registrar estudiante' ?>
                        </button>
                        <?php if ($editando): ?>
                            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Estudiantes registrados (<?= count($estudiantes) ?>)</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Carrera</th>
                            <th>Semestre</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($estudiantes === []): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No hay estudiantes registrados.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($estudiantes as $estudiante): ?>
                            <tr>
                                <td><?= e($estudiante->id) ?></td>
                                <td><?= e($estudiante->nombre) ?></td>
                                <td><?= e($estudiante->correo) ?></td>
                                <td><?= e($estudiante->carrera) ?></td>
                                <td><?= e($estudiante->semestre) ?></td>
                                <td>
                                    <span class="badge <?= $estudiante->activo ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                        <?= $estudiante->activo ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="index.php?accion=editar&id=<?= (int) $estudiante->id ?>" class="btn btn-sm btn-outline-primary">
                                        Editar
                                    </a>
                                    <form method="post" action="index.php" class="d-inline"
                                        onsubmit="return confirm('¿Eliminar a este estudiante?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $estudiante->id ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>