<?php

// Abre la conexion con MySQL usando la base de datos del proyecto.
$con = new mysqli('localhost', 'root', '', 'bd_veterinaria');

// Permite guardar y leer correctamente tildes y otros caracteres especiales.
$con -> set_charset("utf8");
?>