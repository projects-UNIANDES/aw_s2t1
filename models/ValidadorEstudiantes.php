<?php

class ValidadorEstudiantes
{
    /** @return string[] */
    public function validar(string $nombre, string $correo, string $carrera, string $semestre): array
    {
        $errores = [];

        if (trim($nombre) === '') {
            $errores[] = 'El nombre es obligatorio.';
        }

        if (filter_var(trim($correo), FILTER_VALIDATE_EMAIL) === false) {
            $errores[] = 'El correo electrónico no es válido.';
        }

        if (trim($carrera) === '') {
            $errores[] = 'La carrera es obligatoria.';
        }

        if (filter_var($semestre, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]]) === false) {
            $errores[] = 'El semestre debe ser un número entero entre 1 y 10.';
        }

        return $errores;
    }
}
