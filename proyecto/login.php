<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
include 'connection.php';

$nombre = $_POST['Nombre'] ?? '';
$email = $_POST['Gmail'] ?? '';
$pass = $_POST['Contraseña'] ?? '';

if ($nombre == '' || $email == '' || $pass == '') {
    echo "Completa todos los campos.";
    exit();
}

$buscar = mysqli_query($con, "SELECT * FROM usuario WHERE Gmail = '$email'");

if (mysqli_num_rows($buscar) > 0) {
    echo "Ese correo ya existe.";
    exit();
}

$insertar = mysqli_query($con, "INSERT INTO usuario (Nombre, Gmail, Contraseña) VALUES ('$nombre', '$email', '$pass')");

if ($insertar) {
    $_SESSION['usuario'] = $nombre;
    echo "<script>
        window.location.href = 'menu.html';
    </script>";
    exit();
} else {
    echo "Error al registrar: " . mysqli_error($con);
    exit();
}

mysqli_close($con);
?>