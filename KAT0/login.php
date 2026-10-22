<?php
// auth.php inicia la sesión. Una sesión es como una ficha temporal que PHP
// recuerda entre páginas para saber quién ya se identificó.
require_once "auth.php";

// ADMIN_PASSWORD permite reemplazar el PIN predeterminado en el entorno.
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "admin") {
  $contrasenaAdmin = getenv("ADMIN_PASSWORD");
  if ($contrasenaAdmin === false || $contrasenaAdmin === "") {
    $contrasenaAdmin = "admin";
  }
  $contrasenaIngresada = $_POST["contrasena_admin"] ?? "";

  if (is_string($contrasenaIngresada) && hash_equals($contrasenaAdmin, $contrasenaIngresada)) {
    session_regenerate_id(true);
    $_SESSION["adminAutenticado"] = true;
    header("Location: indexD.php");
    exit();
  } else {
    $error = "La contraseña de administrador no es correcta.";
  }
}

// Si la persona ya inició sesión, no necesita volver a llenar el formulario.
if (!empty($_SESSION["autenticado"])) {
  header("Location: menu.php");
  exit();
}

// Este texto se muestra debajo del formulario cuando algo no sale bien.
$error = $error ?? "";
$adminIntento = $_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "admin";

// El navegador envía el formulario usando POST al pulsar "Ingresar".
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") !== "admin") {
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
      --clr-veterinaria: #02A2EC;
      --clr-principal: #02A2EC;
      --clr-peligro: #ff7782;
      --clr-exito: #41f1b6;
      --clr-blanco: #fff;
      --clr-info-oscuro: #7d8da1;
      --clr-info-claro: #dce1eb;
      --clr-oscuro: #363949;
      --clr-advertencia: #ff4edc;
      --clr-claro: rgba(132, 139, 200, 0.18);
      --clr-variante-principal: #111e88;
      --clr-variante-oscura: #677483;
      --clr-fondo: #f6f6f9;
      --sombra-caja: 0 2rem 3rem var(--clr-claro);
      --verde-oscuro: var(--clr-principal);
      --verde-suave: var(--clr-fondo);
      --texto: var(--clr-oscuro);
      --texto-secundario: var(--clr-variante-oscura);
    }

    body {
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 24px;
      background: linear-gradient(135deg, var(--clr-info-claro), var(--clr-fondo));
      color: var(--texto);
      font-family: "Segoe UI", Arial, sans-serif;
    }

    .acceso {
      width: min(100%, 460px);
      padding: 36px;
      border: 1px solid var(--clr-info-claro);
      border-radius: 12px;
      background: #fff;
      box-shadow: var(--sombra-caja);
    }

    .foto-veterinaria {
      display: block;
      width: 100%;
      height: 170px;
      border-radius: 8px;
      object-fit: cover;
      object-position: center;
    }

    .foto-trigger {
      display: block;
      width: 100%;
      margin-bottom: 24px;
      padding: 0;
      border: 0;
      border-radius: 8px;
      background: transparent;
      cursor: pointer;
    }

    .logo {
      display: block;
      width: 88px;
      height: 88px;
      object-fit: contain;
    }

    .logos-login {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      margin-bottom: 16px;
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
      background: var(--clr-variante-principal);
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
    <button class="foto-trigger" id="admin-trigger" type="button" aria-label="Imagen de la veterinaria">
      <img class="foto-veterinaria" src="Veterinaria(perros).jpg" alt="Perros junto al equipo de la veterinaria">
    </button>
    <a href="menu.php" aria-label="Volver a Veterinaria Animalada">
      <div class="logos-login">
        <img class="logo" src="imgs/KAT0.png" alt="Logo KAT0">
        <img class="logo" src="imgs/LogoVeterinaria.png" alt="Logo de Veterinaria Animalada">
      </div>
    </a>

    <section id="login-panel" <?php if ($adminIntento) { echo "hidden"; } ?>>
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
    </section>

    <section id="admin-panel" <?php if (!$adminIntento) { echo "hidden"; } ?>>
      <h1 class="h3 text-center fw-bold">Acceso de administrador</h1>
      <p class="subtitulo text-center mb-4">Ingresá la contraseña de administrador.</p>
      <?php if ($adminIntento && $error !== "") { ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></div>
      <?php } ?>
      <form action="login.php" method="POST">
        <input type="hidden" name="accion" value="admin">
        <div class="mb-4">
          <label for="contrasena-admin" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="contrasena-admin" name="contrasena_admin" autocomplete="current-password" required autofocus>
        </div>
        <button type="submit" class="btn btn-animalada w-100 py-2">Ingresar al panel</button>
      </form>
      <button class="btn btn-link w-100 mt-2" id="volver-login" type="button">Volver al inicio de sesión</button>
    </section>
  </main>
  <script>
    const adminTrigger = document.getElementById("admin-trigger");
    const loginPanel = document.getElementById("login-panel");
    const adminPanel = document.getElementById("admin-panel");
    let toquesEnImagen = 0;

    adminTrigger.addEventListener("click", () => {
      toquesEnImagen += 1;
      if (toquesEnImagen === 5) {
        loginPanel.hidden = true;
        adminPanel.hidden = false;
        document.getElementById("contrasena-admin").focus();
      }
    });

    document.getElementById("volver-login").addEventListener("click", () => {
      adminPanel.hidden = true;
      loginPanel.hidden = false;
      toquesEnImagen = 0;
    });
  </script>
</body>
</html>
