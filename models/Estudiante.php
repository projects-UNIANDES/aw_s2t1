<?php

class Estudiante
{
    public function __construct(
        public ?int $id,
        public string $nombre,
        public string $correo,
        public string $carrera,
        public int $semestre,
        public bool $activo = true,
    ) {}
}
