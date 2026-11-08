<?php

class Database
{
    public function conectar(): mysqli
    {
        try {
            $conexion = new mysqli('localhost', 'root', 'root', 'aplicaciones_web');
            $conexion->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            http_response_code(503);
            die('No se pudo conectar con la base de datos.');
        }

        return $conexion;
    }
}
