<?php
// =====================================================
// CONEXIÓN A LA BASE DE DATOS
// =====================================================

// Datos de conexión de XAMPP
$servidor = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "kat0";

// Crear conexión
$conn = new mysqli($servidor, $usuario, $password, $baseDatos);

// Comprobar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Usar UTF-8
$conn->set_charset("utf8mb4");
?>
