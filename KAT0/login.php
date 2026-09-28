<?php
// auth.php inicia la sesión. Una sesión es como una ficha temporal que PHP
// recuerda entre páginas para saber quién ya se identificó.
require_once "auth.php";

// Si la persona ya inició sesión, no necesita volver a llenar el formulario.
if (!empty($_SESSION["autenticado"])) {
  header("Location: menu.php");
  exit();
}

// Este texto se muestra debajo del formulario cuando algo no sale bien.
$error = "";

// El navegador envía el formulario usando POST al pulsar "Ingresar".
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  require_once __DIR__ . "/conexion.php";

  // trim quita espacios accidentales al principio/final del correo;
  // strtolower permite tratar Ana@Ejemplo.com como ana@ejemplo.com.
  $gmail = strtolower(trim($_POST["Gmail"] ?? ""));
  $contrasena = $_POST["Contrasena"] ?? "";

  if ($gmail === "" || $contrasena === "") {
    $error = "Ingresá tu correo y contraseña.";
  } else {
    // Busca una cuenta existente por Gmail; iniciar sesion no crea una persona nueva.
    // El signo ? recibe el correo mediante bind_param.
    $consulta = $conexion->prepare(
      "SELECT CI, Nombre, Gmail, `Contraseña` FROM persona WHERE Gmail = ? LIMIT 1"
    );

    if ($consulta === false) {
      $error = "No se pudo consultar la cuenta. Revisá la conexión con la base oficial.";
    } else {
      $consulta->bind_param("s", $gmail);
      $consulta->execute();
      $resultado = $consulta->get_result();
      // fetch_assoc devuelve las columnas seleccionadas de la fila encontrada.
      $persona = $resultado->fetch_assoc();

      if (!is_array($persona)) {
        // Usamos el mismo mensaje para no revelar qué correos están registrados.
        $error = "El correo o la contraseña no son correctos.";
      } else {
        $contrasenaGuardada = (string) $persona["Contraseña"];

        // En esta etapa compara directamente el texto escrito con el valor de la base.
        if ($contrasena === $contrasenaGuardada) {
          // Cambiar el identificador evita reutilizar el mismo ID de sesión
          // antes y después de iniciar sesión.
          session_regenerate_id(true);

          // La base guarda la cuenta de forma permanente; $_SESSION recuerda quien navega ahora.
          // menu.php usa Nombre y autenticado para mostrar el perfil del cliente.
          $_SESSION["autenticado"] = true;
          $_SESSION["idPersona"] = (int) $persona["CI"];
          $_SESSION["Nombre"] = $persona["Nombre"];
          $_SESSION["Gmail"] = $persona["Gmail"];

          $consulta->close();
          $conexion->close();

          // La portada muestra el nombre y las acciones del perfil autenticado.
          header("Location: menu.php");
          exit();
        }

        $error = "El correo o la contraseña no son correctos.";
      }

      $consulta->close();
    }
  }

  $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión | Veterinaria Animalada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --verde-oscuro: #245e4b;
      --verde-suave: #e7f2eb;
      --texto: #263a32;
      --texto-secundario: #68766f;
    }

    body {
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 24px;
      background: linear-gradient(135deg, #edf5ef, #f9fbf9);
      color: var(--texto);
      font-family: "Segoe UI", Arial, sans-serif;
    }

    .acceso {
      width: min(100%, 460px);
      padding: 36px;
      border: 1px solid #e0e9e2;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 18px 48px rgba(36, 94, 75, 0.1);
    }

    .foto-veterinaria {
      display: block;
      width: 100%;
      height: 170px;
      margin-bottom: 24px;
      border-radius: 8px;
      object-fit: cover;
      object-position: center;
    }

    .logo {
      display: block;
      width: 112px;
      height: 112px;
      margin: 0 auto 16px;
      object-fit: contain;
    }

    .subtitulo {
      color: var(--texto-secundario);
    }

    .btn-animalada {
      border: 0;
      background: var(--verde-oscuro);
      color: #fff;
      font-weight: 700;
    }

    .btn-animalada:hover {
      background: #194a39;
      color: #fff;
    }

    a {
      color: var(--verde-oscuro);
    }

    @media (max-width: 480px) {
      .acceso {
        padding: 28px 22px;
      }
    }
  </style>
</head>
<body>
  <main class="acceso">
    <img class="foto-veterinaria" src="Veterinaria(perros).jpg" alt="Perros junto al equipo de la veterinaria">
    <a href="menu.php" aria-label="Volver a Veterinaria Animalada">
      <img class="logo" src="KAT0 (Principal).png" alt="Logo de Veterinaria Animalada">
    </a>
    <h1 class="h3 text-center fw-bold">Iniciar sesión</h1>
    <p class="subtitulo text-center mb-4">Ingresá con la cuenta que registraste.</p>

    <!-- registro.php agrega ?registrado=1 a la dirección cuando crea la cuenta. -->
    <?php if (isset($_GET["registrado"])) { ?>
      <div class="alert alert-success" role="status">Tu cuenta fue creada. Ya podés iniciar sesión.</div>
    <?php } ?>

    <?php if ($error !== "") { ?>
      <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></div>
    <?php } ?>

    <!-- method="POST" envía los datos al servidor sin ponerlos en la dirección. -->
    <form action="login.php" method="POST">
      <div class="mb-3">
        <label for="gmail" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="gmail" name="Gmail" autocomplete="email" required>
      </div>
      <div class="mb-4">
        <label for="contrasena" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="contrasena" name="Contrasena" autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-animalada w-100 py-2">Ingresar</button>
    </form>

    <p class="subtitulo text-center mt-4 mb-0">¿Todavía no tenés cuenta? <a href="index.html">Registrate</a></p>
    <p class="text-center mt-3 mb-0"><a href="menu.php">Volver al sitio</a></p>
  </main>
</body>
</html>
