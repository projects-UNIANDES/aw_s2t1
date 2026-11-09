<?php

interface RepositorioClientes
{
    public function registrar(Cliente $cliente): void;

    public function consultar(int $id): ?Cliente;

    /** @return Cliente[] */
    public function listar(): array;

    public function actualizar(Cliente $cliente): bool;

    public function eliminar(int $id): bool;
}
