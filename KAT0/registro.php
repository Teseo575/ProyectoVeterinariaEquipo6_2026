<?php
// =====================================================
// REGISTRO.PHP
// =====================================================
// Este archivo recibe los datos del formulario,
// los guarda en MySQL y después lleva al usuario
// al menú principal.
//
// IMPORTANTE:
// El usuario NO entra directamente a este archivo.
// Llega aquí cuando pulsa "Registrarme" en index.html.
// =====================================================

// Abrimos la conexión que registro.php necesita para consultar y guardar cuentas.
include("conexion.php");


// =====================================================
// COMPROBAR QUE EL FORMULARIO FUE ENVIADO
// =====================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // =================================================
    // RECIBIR LOS DATOS
    // =================================================

    // Cada clave entre corchetes coincide con el atributo name del formulario.
    // Por ejemplo, name="Gmail" en index.html se lee aquí como $_POST["Gmail"].
    $nombre = $_POST["Nombre"];
    $apellido = $_POST["Apellido"];
    $ci = $_POST["CI"];
    $fechaNacimiento = $_POST["FechaNacimiento"];
    $gmail = $_POST["Gmail"];
    $contrasena = $_POST["Contrasena"];

    // No guardamos la contraseña original. password_hash crea una versión protegida
    // que login.php puede comprobar después con password_verify.
    $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);


    // =================================================
    // COMPROBAR SI LA CI YA EXISTE
    // =================================================

    // El ? es un espacio para el dato; así la CI no se mezcla con el texto SQL.
    $consulta = $conn->prepare(
        "SELECT id FROM personas WHERE CI = ?"
    );

    // "s" indica que la CI se envía como texto (string).
    $consulta->bind_param("s", $ci);
    $consulta->execute();

    $resultado = $consulta->get_result();

    if ($resultado->num_rows > 0) {
        // die detiene el registro: no queremos crear una segunda cuenta con esa CI.
        die("Error: esa CI ya está registrada. <br><a href='index.html'>Volver</a>");
    }


    // =================================================
    // COMPROBAR SI EL GMAIL YA EXISTE
    // =================================================

    // Se repite la comprobación para que cada correo pertenezca a una sola cuenta.
    $consulta = $conn->prepare(
        "SELECT id FROM personas WHERE Gmail = ?"
    );

    $consulta->bind_param("s", $gmail);
    $consulta->execute();

    $resultado = $consulta->get_result();

    if ($resultado->num_rows > 0) {
        die("Error: ese Gmail ya está registrado. <br><a href='index.html'>Volver</a>");
    }


    // =================================================
    // GUARDAR EL USUARIO
    // =================================================

    // Los seis signos ? se reemplazan abajo por los seis datos de la persona.
    $sql = "INSERT INTO personas
            (Nombre, Apellido, CI, FechaNacimiento, Gmail, Contrasena)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    // "ssssss" significa que los seis valores se envían como texto.
    // El último valor es el hash, no la contraseña original.
    $stmt->bind_param(
        "ssssss",
        $nombre,
        $apellido,
        $ci,
        $fechaNacimiento,
        $gmail,
        $contrasenaHash
    );


    // =================================================
    // EJECUTAR INSERT
    // =================================================

    if ($stmt->execute()) {

        // =============================================
        // REGISTRO CORRECTO
        // =============================================
        // Al terminar el registro, puede iniciar sesión con sus credenciales.

        header("Location: login.php?registrado=1");
        exit();

    } else {

        // =============================================
        // ERROR
        // =============================================

        echo "Error al registrar: " . $conn->error;
        echo "<br><br>";
        echo "<a href='index.html'>Volver</a>";
    }

} else {

    // Si alguien entra directamente a registro.php
    // lo mandamos al formulario.

    header("Location: index.html");
    exit();
}
?>
