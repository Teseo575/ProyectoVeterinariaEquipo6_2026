<?php
// Las sesiones guardan datos temporales de la persona que inició sesión.
// Por ejemplo, $_SESSION["Nombre"] permite mostrar su nombre en usuario.php.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Protege una página privada: si no hay una sesión iniciada, vuelve al login.
function exigirSesion()
{
    // Una sesión se considera autenticada cuando el login guardó este valor.
    if (empty($_SESSION["autenticado"])) {
        header("Location: login.php");
        exit();
    }
}
