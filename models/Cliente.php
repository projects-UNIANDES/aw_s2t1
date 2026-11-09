<?php

class Cliente
{
    public function __construct(
        public ?int $id,
        public string $nombre,
        public string $correo,
        public string $telefono,
        public string $ciudad,
        public bool $activo = true,
    ) {}
}
