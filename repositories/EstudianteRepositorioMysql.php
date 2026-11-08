<?php

class EstudianteRepositorioMysql implements RepositorioEstudiantes
{
    private const COLUMNAS = 'id, nombre, correo, carrera, semestre, activo';

    public function __construct(private mysqli $conexion) {}

    public function registrar(Estudiante $estudiante): void
    {
        $stmt = $this->conexion->prepare(
            'INSERT INTO estudiantes (nombre, correo, carrera, semestre, activo) VALUES (?, ?, ?, ?, ?)'
        );
        $activo = (int) $estudiante->activo;
        $stmt->bind_param('sssii', $estudiante->nombre, $estudiante->correo, $estudiante->carrera, $estudiante->semestre, $activo);
        $stmt->execute();
        $estudiante->id = $this->conexion->insert_id;
        $stmt->close();
    }

    public function consultar(int $id): ?Estudiante
    {
        $stmt = $this->conexion->prepare('SELECT ' . self::COLUMNAS . ' FROM estudiantes WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $fila === null ? null : $this->aEstudiante($fila);
    }

    public function listar(): array
    {
        $stmt = $this->conexion->prepare('SELECT ' . self::COLUMNAS . ' FROM estudiantes ORDER BY id');
        $stmt->execute();
        $resultado = $stmt->get_result();

        $estudiantes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $estudiantes[] = $this->aEstudiante($fila);
        }
        $stmt->close();

        return $estudiantes;
    }

    public function actualizar(Estudiante $estudiante): bool
    {
        $stmt = $this->conexion->prepare(
            'UPDATE estudiantes SET nombre = ?, correo = ?, carrera = ?, semestre = ?, activo = ? WHERE id = ?'
        );
        $activo = (int) $estudiante->activo;
        $stmt->bind_param('sssiii', $estudiante->nombre, $estudiante->correo, $estudiante->carrera, $estudiante->semestre, $activo, $estudiante->id);
        $stmt->execute();
        $filas = $stmt->affected_rows;
        $stmt->close();

        return $filas > 0 || $this->consultar($estudiante->id) !== null;
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->conexion->prepare('DELETE FROM estudiantes WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $filas = $stmt->affected_rows;
        $stmt->close();

        return $filas > 0;
    }

    private function aEstudiante(array $fila): Estudiante
    {
        return new Estudiante(
            (int) $fila['id'],
            $fila['nombre'],
            $fila['correo'],
            $fila['carrera'],
            (int) $fila['semestre'],
            (bool) $fila['activo'],
        );
    }
}
