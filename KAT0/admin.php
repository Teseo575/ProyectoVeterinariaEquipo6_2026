<?php
// 1. Conexión a la base de datos
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

// 2. Consulta SQL

if ($gmail == "martin@gmail.com") {
    $consulta = "SELECT * FROM persona";
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows > 0) {
        // Mostrar los datos de cada fila
        while ($fila = $resultado->fetch_assoc()) {
            echo "CI: " . $fila["CI"] . "<br>";
            echo "Nombre: " . $fila["Nombre"] . "<br>";
            echo "Apellido: " . $fila["Apellido"] . "<br>";
            echo "Dirección: " . $fila["Dirección"] . "<br>";
            echo "Departamento: " . $fila["Departamento"] . "<br>";
            echo "Contraseña: " . $fila["Contraseña"] . "<br>";
            echo "Gmail: " . $fila["Gmail"] . "<br><hr>";
        }
    } else {
        echo "No se encontraron resultados.";
    }
} else {
    echo "Acceso denegado. No tenés permisos para ver esta información.";

}
?>