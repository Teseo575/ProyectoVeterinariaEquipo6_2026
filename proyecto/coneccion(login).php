<?php
session_start();
include 'connection.php';

function limpiar($valor) {
    return trim(htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit();
}

$accion = $_POST['accion'] ?? 'registrar';

if ($accion === 'login') {
    $email = limpiar($_POST['Gmail'] ?? '');
    $password = $_POST['Contraseña'] ?? '';

    if ($email === '' || $password === '') {
        echo "Completa el correo y la contraseña.";
        exit();
    }

    $sql = "SELECT * FROM usuario WHERE Gmail = ? AND Contraseña = ? LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $_SESSION['login'] = true;
        $_SESSION['nombre'] = $usuario['Nombre'];
        $_SESSION['email'] = $usuario['Gmail'];

        header('Location: menu.html');
        exit();
    }

    echo "Correo o contraseña incorrectos.";
    $stmt->close();
    $con->close();
    exit();
}

$nombre = limpiar($_POST['Nombre'] ?? '');
$email = limpiar($_POST['Gmail'] ?? '');
$password = $_POST['Contraseña'] ?? '';

if ($nombre === '' || $email === '' || $password === '') {
    echo "Completa todos los campos.";
    exit();
}

$verificar = $con->prepare("SELECT id_usuario FROM usuario WHERE Gmail = ? LIMIT 1");
$verificar->bind_param('s', $email);
$verificar->execute();
$verificar->store_result();

if ($verificar->num_rows > 0) {
    echo "Ese correo ya está registrado.";
    $verificar->close();
    $con->close();
    exit();
}

$insertar = $con->prepare("INSERT INTO usuario (Nombre, Gmail, Contraseña) VALUES (?, ?, ?)");
$insertar->bind_param('sss', $nombre, $email, $password);

if ($insertar->execute()) {
    $_SESSION['login'] = true;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['email'] = $email;

    echo "<script>
        alert('Usuario registrado correctamente');
        window.location.href = 'menu.html';
    </script>";
} else {
    echo "Error al registrar: " . $con->error;
}

$insertar->close();
$con->close();
?>