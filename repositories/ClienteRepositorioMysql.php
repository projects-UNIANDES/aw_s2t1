<?php

class ClienteRepositorioMysql implements RepositorioClientes
{
    private const COLUMNAS = 'id, nombre, correo, telefono, ciudad, activo';

    public function __construct(private mysqli $conexion) {}

    public function registrar(Cliente $cliente): void
    {
        $stmt = $this->conexion->prepare(
            'INSERT INTO clientes (nombre, correo, telefono, ciudad, activo) VALUES (?, ?, ?, ?, ?)'
        );
        $activo = (int) $cliente->activo;
        $stmt->bind_param('ssssi', $cliente->nombre, $cliente->correo, $cliente->telefono, $cliente->ciudad, $activo);
        $stmt->execute();
        $cliente->id = $this->conexion->insert_id;
        $stmt->close();
    }

    public function consultar(int $id): ?Cliente
    {
        $stmt = $this->conexion->prepare('SELECT ' . self::COLUMNAS . ' FROM clientes WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $fila === null ? null : $this->aCliente($fila);
    }

    public function listar(): array
    {
        $stmt = $this->conexion->prepare('SELECT ' . self::COLUMNAS . ' FROM clientes ORDER BY id');
        $stmt->execute();
        $resultado = $stmt->get_result();

        $clientes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $clientes[] = $this->aCliente($fila);
        }
        $stmt->close();

        return $clientes;
    }

    public function actualizar(Cliente $cliente): bool
    {
        $stmt = $this->conexion->prepare(
            'UPDATE clientes SET nombre = ?, correo = ?, telefono = ?, ciudad = ?, activo = ? WHERE id = ?'
        );
        $activo = (int) $cliente->activo;
        $stmt->bind_param('ssssii', $cliente->nombre, $cliente->correo, $cliente->telefono, $cliente->ciudad, $activo, $cliente->id);
        $stmt->execute();
        $filas = $stmt->affected_rows;
        $stmt->close();

        return $filas > 0 || $this->consultar($cliente->id) !== null;
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->conexion->prepare('DELETE FROM clientes WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $filas = $stmt->affected_rows;
        $stmt->close();

        return $filas > 0;
    }

    private function aCliente(array $fila): Cliente
    {
        return new Cliente(
            (int) $fila['id'],
            $fila['nombre'],
            $fila['correo'],
            $fila['telefono'],
            $fila['ciudad'],
            (bool) $fila['activo'],
        );
    }
}
