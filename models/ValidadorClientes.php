<?php

class ValidadorClientes
{
    /** @return string[] */
    public function validar(string $nombre, string $correo, string $telefono, string $ciudad): array
    {
        $errores = [];

        if (trim($nombre) === '') {
            $errores[] = 'El nombre es obligatorio.';
        }

        if (filter_var(trim($correo), FILTER_VALIDATE_EMAIL) === false) {
            $errores[] = 'El correo electrónico no es válido.';
        }

        if (preg_match('/^\+?\d{7,15}$/', trim($telefono)) !== 1) {
            $errores[] = 'El teléfono debe tener entre 7 y 15 dígitos.';
        }

        if (trim($ciudad) === '') {
            $errores[] = 'La ciudad es obligatoria.';
        }

        return $errores;
    }
}
