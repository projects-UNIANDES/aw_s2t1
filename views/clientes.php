<?php

$clientes ??= [];
$ciudades ??= [];
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
    <title>Gestión de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar bg-body-tertiary mb-4 shadow-sm">
        <div class="container">
            <span class="navbar-brand mb-0">Gestión de Clientes</span>
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
            <div class="card-header"><?= $editando ? 'Editar cliente' : 'Nuevo cliente' ?></div>
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
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" maxlength="16" required
                            pattern="\+?\d{7,15}" value="<?= e($datos['telefono'] ?? '') ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="ciudad" class="form-label">Ciudad</label>
                        <select class="form-select" id="ciudad" name="ciudad" required>
                            <option value="">Seleccione una ciudad</option>
                            <?php foreach ($ciudades as $ciudad): ?>
                                <option value="<?= e($ciudad) ?>" <?= ($datos['ciudad'] ?? '') === $ciudad ? 'selected' : '' ?>><?= e($ciudad) ?></option>
                            <?php endforeach; ?>
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
                            <?= $editando ? 'Guardar cambios' : 'Registrar cliente' ?>
                        </button>
                        <?php if ($editando): ?>
                            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Clientes registrados</span>
                <span class="badge rounded-pill text-bg-primary"><?= count($clientes) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Ciudad</th>
                            <th scope="col" class="text-center">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($clientes === []): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay clientes registrados.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td class="text-muted"><?= e($cliente->id) ?></td>
                                <td>
                                    <div class="fw-semibold"><?= e($cliente->nombre) ?></div>
                                    <a href="mailto:<?= e($cliente->correo) ?>" class="small text-body-secondary text-decoration-none">
                                        <?= e($cliente->correo) ?>
                                    </a>
                                </td>
                                <td><?= e($cliente->telefono) ?></td>
                                <td><?= e($cliente->ciudad) ?></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?= $cliente->activo ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                        <?= $cliente->activo ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Acciones">
                                        <a href="index.php?accion=editar&id=<?= (int) $cliente->id ?>" class="btn btn-outline-primary">
                                            Editar
                                        </a>
                                        <button type="submit" form="eliminar-<?= (int) $cliente->id ?>" class="btn btn-outline-danger">
                                            Eliminar
                                        </button>
                                    </div>
                                    <form id="eliminar-<?= (int) $cliente->id ?>" method="post" action="index.php" class="d-none"
                                        onsubmit="return confirm('¿Eliminar a este cliente?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $cliente->id ?>">
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
