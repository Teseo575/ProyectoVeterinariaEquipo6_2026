<?php
require_once __DIR__ . "/auth.php";
// Si no hay sesion, auth.php envia al visitante a login.php.
exigirSesion();
require_once __DIR__ . "/conexion.php";

// La CI viene del login. No se pide en el formulario para que el alta quede vinculada al cliente actual.
$ci = (int) ($_SESSION["idPersona"] ?? 0);
$error = "";
$registrada = isset($_GET["registrada"]);

$consultaCliente = $conexion->prepare("SELECT CI FROM cliente WHERE CI = ? LIMIT 1");
$consultaCliente->bind_param("i", $ci);
$consultaCliente->execute();
$esCliente = $consultaCliente->get_result()->num_rows > 0;
$consultaCliente->close();

// La tabla cliente confirma que la persona autenticada tiene el tipo de cuenta esperado.
if (!$esCliente) {
    http_response_code(403);
    exit("Esta cuenta no tiene permiso para registrar mascotas.");
}

$nombre = "";
$especie = "";
$raza = "";
$estado = "";
$sexo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Estos cinco valores salen de los campos name del formulario de mascota.
    $nombre = trim($_POST["Nombre"] ?? "");
    $especie = trim($_POST["Especie"] ?? "");
    $raza = trim($_POST["Raza"] ?? "");
    $estado = trim($_POST["Estado"] ?? "");
    $sexo = trim($_POST["Sexo"] ?? "");

    if ($nombre === "" || $especie === "" || $raza === "" || $estado === "" || $sexo === "") {
        $error = "Completá todos los campos.";
    } else {
        try {
      // Una mascota se considera repetida si ese cliente ya tiene igual nombre, especie y raza.
        $consultaDuplicada = $conexion->prepare(
          "SELECT ID_Mascotas FROM mascotas
           WHERE CI = ? AND Nombre = ? AND Especie = ? AND Raza = ?
           LIMIT 1"
        );
        $consultaDuplicada->bind_param("isss", $ci, $nombre, $especie, $raza);
        $consultaDuplicada->execute();

        if ($consultaDuplicada->get_result()->num_rows > 0) {
          $error = "Ya registraste una mascota con ese nombre, especie y raza.";
            } else {
          // La tabla oficial no tiene AUTO_INCREMENT; se calcula el ID a partir del mayor existente.
          $siguienteId = $conexion->query(
            "SELECT COALESCE(MAX(ID_Mascotas), 0) + 1 AS siguiente FROM mascotas"
          )->fetch_assoc();
          $idMascota = (int) $siguienteId["siguiente"];

          $insertar = $conexion->prepare(
            "INSERT INTO mascotas (ID_Mascotas, Nombre, Especie, Raza, Estado, Sexo, CI)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
          );
          $insertar->bind_param(
            // Orden: ID entero, cinco textos de la mascota y CI entera del propietario.
            "isssssi",
            $idMascota,
            $nombre,
            $especie,
            $raza,
            $estado,
            $sexo,
            $ci
          );
          $insertar->execute();
          $insertar->close();

                header("Location: mascota.php?registrada=1");
                exit();
            }
        } catch (mysqli_sql_exception $excepcion) {
            $error = "No se pudo registrar la mascota. Revisá los datos e intentá nuevamente.";
        }
    }
}

$nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES, "UTF-8");
$especieSegura = htmlspecialchars($especie, ENT_QUOTES, "UTF-8");
$razaSegura = htmlspecialchars($raza, ENT_QUOTES, "UTF-8");
$estadoSeguro = htmlspecialchars($estado, ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar mascota | Veterinaria Animalada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body { min-height: 100vh; background: #f4f7f3; color: #24352e; font-family: "DM Sans", "Segoe UI", sans-serif; }
    .barra { min-height: 72px; background: #fbfcfa; border-bottom: 1px solid #e2e9e2; }
    .marca { color: #173d32; font-weight: 800; text-decoration: none; }
    .formulario-mascota { width: min(100%, 680px); margin: 0 auto; }
    .panel { padding: 32px; border: 1px solid #e0e9e2; border-radius: 10px; background: #fff; box-shadow: 0 14px 36px rgba(36, 94, 75, 0.08); }
    .btn-animalada { border: 0; background: #2e7057; color: #fff; font-weight: 700; }
    .btn-animalada:hover { background: #245e4b; color: #fff; }
    @media (max-width: 575px) { .panel { padding: 24px 18px; } }
  </style>
</head>
<body>
  <nav class="navbar barra">
    <div class="container">
      <a class="marca" href="menu.php">Veterinaria Animalada</a>
      <a class="btn btn-outline-secondary btn-sm" href="menu.php">Volver al inicio</a>
    </div>
  </nav>

  <main class="container py-5">
    <section class="formulario-mascota">
      <p class="text-success fw-semibold text-uppercase small mb-2">Área de clientes</p>
      <h1 class="h2 fw-bold mb-4">Registrar mascota</h1>

      <?php if ($registrada) { ?>
        <div class="alert alert-success" role="status">La mascota se registró correctamente.</div>
      <?php } ?>
      <?php if ($error !== "") { ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></div>
      <?php } ?>

      <form class="panel" method="POST" action="mascota.php">
        <div class="mb-3">
          <label class="form-label" for="nombre">Nombre</label>
          <input class="form-control" id="nombre" name="Nombre" maxlength="100" value="<?php echo $nombreSeguro; ?>" required>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="especie">Especie</label>
            <input class="form-control" id="especie" name="Especie" maxlength="100" value="<?php echo $especieSegura; ?>" placeholder="Perro, gato..." required>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="raza">Raza</label>
            <input class="form-control" id="raza" name="Raza" maxlength="100" value="<?php echo $razaSegura; ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="estado">Estado</label>
            <input class="form-control" id="estado" name="Estado" maxlength="100" value="<?php echo $estadoSeguro; ?>" placeholder="Saludable, en tratamiento..." required>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="sexo">Sexo</label>
            <select class="form-select" id="sexo" name="Sexo" required>
              <option value="" disabled <?php echo $sexo === "" ? "selected" : ""; ?>>Seleccioná una opción</option>
              <option value="Macho" <?php echo $sexo === "Macho" ? "selected" : ""; ?>>Macho</option>
              <option value="Hembra" <?php echo $sexo === "Hembra" ? "selected" : ""; ?>>Hembra</option>
              <option value="No especificado" <?php echo $sexo === "No especificado" ? "selected" : ""; ?>>No especificado</option>
            </select>
          </div>
        </div>
        <button class="btn btn-animalada w-100 mt-4 py-2" type="submit">
          <i class="bi bi-plus-circle me-2" aria-hidden="true"></i>Guardar mascota
        </button>
      </form>
    </section>
  </main>
</body>
</html>