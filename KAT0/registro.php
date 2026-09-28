<?php
// Este endpoint procesa unicamente el formulario enviado desde index.html.
// Si alguien abre la URL sin enviar el formulario, vuelve a la pantalla de registro.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit();
}

require_once __DIR__ . "/conexion.php";

// Las claves de $_POST coinciden con los atributos name del formulario HTML.
$nombre = trim($_POST["Nombre"] ?? "");
$apellido = trim($_POST["Apellido"] ?? "");
$ci = trim($_POST["CI"] ?? "");
$direccion = trim($_POST["Direccion"] ?? "");
$departamento = trim($_POST["Departamento"] ?? "");
$gmail = strtolower(trim($_POST["Gmail"] ?? ""));
$contrasena = $_POST["Contrasena"] ?? "";

// Evita guardar una cuenta incompleta o datos con formato incorrecto.
if ($nombre === "" || $apellido === "" || $direccion === "" || $departamento === "" || $gmail === "" || $contrasena === "") {
    exit("Completá todos los campos.");
}

if (!ctype_digit($ci) || strlen($ci) > 8 || !filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
    exit("La CI o el correo no tienen un formato válido.");
}

$ci = (int) $ci;
// La CI identifica a la persona y Gmail se usa para iniciar sesion; ninguno debe repetirse.
$consulta = $conexion->prepare("SELECT CI FROM persona WHERE CI = ? OR Gmail = ? LIMIT 1");
$consulta->bind_param("is", $ci, $gmail);
$consulta->execute();

if ($consulta->get_result()->num_rows > 0) {
    $consulta->close();
    exit("La CI o el correo ya están registrados. <a href='index.html'>Volver</a>");
}
$consulta->close();

// La cuenta tiene dos filas relacionadas por CI; la transaccion evita crear solo una de ellas.
$conexion->begin_transaction();

try {
    $insertarPersona = $conexion->prepare(
        "INSERT INTO persona (CI, Nombre, Apellido, `Dirección`, Departamento, `Contraseña`, Gmail)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $insertarPersona->bind_param(
        // "i" corresponde a CI (entero) y cada "s" a uno de los seis textos siguientes.
        "issssss",
        $ci,
        $nombre,
        $apellido,
        $direccion,
        $departamento,
        $contrasena,
        $gmail
    );
    $insertarPersona->execute();
    $insertarPersona->close();

    $insertarCliente = $conexion->prepare("INSERT INTO cliente (CI) VALUES (?)");
    $insertarCliente->bind_param("i", $ci);
    $insertarCliente->execute();
    $insertarCliente->close();

    $conexion->commit();
    // La persona ya existe en la base; ahora debe iniciar sesion para crear su sesion PHP.
    header("Location: login.php?registrado=1");
    exit();
} catch (mysqli_sql_exception $error) {
    // Si falla persona o cliente, se deshacen ambas inserciones.
    $conexion->rollback();
    http_response_code(500);
    exit("No se pudo completar el registro. Verificá la base de datos e intentá nuevamente.");
}
?>
