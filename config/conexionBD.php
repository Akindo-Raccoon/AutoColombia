<?php

class Conectar
{
    // Se llama sin crear un objeto
    public static function conec()
    {
        $host = "localhost";
        $user = "root";
        $pass = "admin";
        $db_name = "concesionario_db";

        // Conectar al servidor MySQL
        $link = mysqli_connect($host, $user, $pass)
            or die("ERROR al conectar a MySQL: " . mysqli_connect_error());

        // Seleccionar la base de datos
        mysqli_select_db($link, $db_name)
            or die("ERROR al seleccionar la BD: " . mysqli_error($link));

        // Configurar charset UTF-8
        mysqli_set_charset($link, "utf8");

        return $link;
    }
}