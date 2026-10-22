<?php
// =====================================================
// CONEXIÓN A LA BASE DE DATOS
// =====================================================

// Datos de conexión de XAMPP
$servidor = "localhost";
$usuario = "root";
$contrasenaBaseDatos = "";
$baseDatos = "bdd_equipo6";

// Crear conexión
$conexion = new mysqli($servidor, $usuario, $contrasenaBaseDatos, $baseDatos);

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Usar UTF-8
$conexion->set_charset("utf8mb4");

// Mantiene el manejo de errores SQL consistente también en PHP 8.0.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
