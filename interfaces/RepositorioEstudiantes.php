<?php

interface RepositorioEstudiantes
{
    public function registrar(Estudiante $estudiante): void;

    public function consultar(int $id): ?Estudiante;

    /** @return Estudiante[] */
    public function listar(): array;

    public function actualizar(Estudiante $estudiante): bool;

    public function eliminar(int $id): bool;
}
