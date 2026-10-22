<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/tienda_db.php";

inicializarTienda($conexion);
if (!isset($_SESSION["tokenTienda"])) {
    $_SESSION["tokenTienda"] = bin2hex(random_bytes(32));
}
if (!isset($_SESSION["carritoTienda"]) || !is_array($_SESSION["carritoTienda"])) {
    $_SESSION["carritoTienda"] = [];
}

$mensaje = (string) ($_SESSION["mensajeTienda"] ?? "");
unset($_SESSION["mensajeTienda"]);
$error = "";
$token = $_SESSION["tokenTienda"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($token, (string) ($_POST["token"] ?? ""))) {
        http_response_code(403);
        $error = "La sesión del formulario venció. Actualizá la página e intentá de nuevo.";
    } else {
        $accion = (string) ($_POST["accion"] ?? "");
        $idProducto = filter_var($_POST["producto_id"] ?? null, FILTER_VALIDATE_INT);

        if ($accion === "agregar" && $idProducto !== false && $idProducto > 0) {
            $consulta = $conexion->prepare("SELECT stock FROM productos_tienda WHERE id = ? AND activo = 1");
            $consulta->bind_param("i", $idProducto);
            $consulta->execute();
            $producto = $consulta->get_result()->fetch_assoc();
            $consulta->close();
            if (!$producto || (int) $producto["stock"] < 1) {
                $error = "Ese producto no está disponible por el momento.";
            } else {
                $cantidad = (int) ($_SESSION["carritoTienda"][$idProducto] ?? 0) + 1;
                $_SESSION["carritoTienda"][$idProducto] = min($cantidad, (int) $producto["stock"]);
                $mensaje = "Producto agregado al carrito.";
            }
        } elseif ($accion === "actualizar") {
            $cantidades = $_POST["cantidades"] ?? [];
            if (!is_array($cantidades)) {
                $cantidades = [];
            }
            foreach ($cantidades as $id => $cantidad) {
                $id = filter_var($id, FILTER_VALIDATE_INT);
                $cantidad = filter_var($cantidad, FILTER_VALIDATE_INT);
                if ($id === false || $cantidad === false) {
                    continue;
                }
                if ($cantidad < 1) {
                    unset($_SESSION["carritoTienda"][$id]);
                    continue;
                }
                $consulta = $conexion->prepare("SELECT stock FROM productos_tienda WHERE id = ? AND activo = 1");
                $consulta->bind_param("i", $id);
                $consulta->execute();
                $producto = $consulta->get_result()->fetch_assoc();
                $consulta->close();
                if (!$producto) {
                    unset($_SESSION["carritoTienda"][$id]);
                } else {
                    $_SESSION["carritoTienda"][$id] = min($cantidad, (int) $producto["stock"]);
                }
            }
            $mensaje = "Carrito actualizado.";
        } elseif ($accion === "confirmar_pedido") {
            $nombre = trim((string) ($_POST["cliente"] ?? ""));
            $email = trim((string) ($_POST["email"] ?? ""));
            $telefono = trim((string) ($_POST["telefono"] ?? ""));
            $notas = trim((string) ($_POST["notas"] ?? ""));
            $carrito = $_SESSION["carritoTienda"];

            if (!$carrito) {
                $error = "Agregá al menos un producto antes de confirmar el pedido.";
            } elseif ($nombre === "" || strlen($nombre) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190 || $telefono === "" || strlen($telefono) > 40 || strlen($notas) > 500) {
                $error = "Revisá el nombre, correo, teléfono y notas del pedido.";
            } else {
                try {
                    $conexion->begin_transaction();
                    $items = [];
                    $total = 0.0;
                    foreach ($carrito as $id => $cantidad) {
                        $id = (int) $id;
                        $cantidad = (int) $cantidad;
                        $consulta = $conexion->prepare("SELECT nombre, precio, stock FROM productos_tienda WHERE id = ? AND activo = 1 FOR UPDATE");
                        $consulta->bind_param("i", $id);
                        $consulta->execute();
                        $producto = $consulta->get_result()->fetch_assoc();
                        $consulta->close();
                        if (!$producto || (int) $producto["stock"] < $cantidad) {
                            throw new RuntimeException("No hay stock suficiente para completar el pedido. Actualizá el carrito.");
                        }
                        $precio = (float) $producto["precio"];
                        $items[] = [$id, $producto["nombre"], $precio, $cantidad];
                        $total += $precio * $cantidad;
                    }

                    $insertarPedido = $conexion->prepare(
                        "INSERT INTO pedidos_tienda (cliente, email, telefono, notas, total) VALUES (?, ?, ?, ?, ?)"
                    );
                    $insertarPedido->bind_param("ssssd", $nombre, $email, $telefono, $notas, $total);
                    $insertarPedido->execute();
                    $pedidoId = (int) $conexion->insert_id;
                    $insertarPedido->close();

                    $insertarItem = $conexion->prepare(
                        "INSERT INTO pedido_tienda_items (pedido_id, producto_id, producto_nombre, precio, cantidad) VALUES (?, ?, ?, ?, ?)"
                    );
                    $actualizarStock = $conexion->prepare("UPDATE productos_tienda SET stock = stock - ? WHERE id = ?");
                    foreach ($items as [$id, $nombreProducto, $precio, $cantidad]) {
                        $insertarItem->bind_param("iisdi", $pedidoId, $id, $nombreProducto, $precio, $cantidad);
                        $insertarItem->execute();
                        $actualizarStock->bind_param("ii", $cantidad, $id);
                        $actualizarStock->execute();
                    }
                    $insertarItem->close();
                    $actualizarStock->close();
                    $conexion->commit();
                    $_SESSION["carritoTienda"] = [];
                    header("Location: shop.php?pedido=" . $pedidoId);
                    exit();
                } catch (Throwable $exception) {
                    $conexion->rollback();
                    if ($exception instanceof RuntimeException) {
                        $error = $exception->getMessage();
                    } else {
                        error_log("Error al registrar pedido de tienda: " . $exception->getMessage());
                        $error = "No se pudo registrar el pedido. Intentá nuevamente.";
                    }
                }
            }
        } else {
            $error = "La acción solicitada no es válida.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $mensaje !== "" && $error === "") {
    $_SESSION["mensajeTienda"] = $mensaje;
    header("Location: shop.php#carrito");
    exit();
}

$productos = $conexion->query(
    "SELECT id, nombre, descripcion, precio, stock, imagen FROM productos_tienda WHERE activo = 1 ORDER BY id"
)->fetch_all(MYSQLI_ASSOC);
$idsCarrito = array_map("intval", array_keys($_SESSION["carritoTienda"]));
$productosCarrito = [];
$totalCarrito = 0.0;
if ($idsCarrito) {
    $idsSeguros = implode(",", $idsCarrito);
    $consulta = $conexion->prepare(
        "SELECT id, nombre, precio, stock, imagen FROM productos_tienda WHERE activo = 1 AND id IN ($idsSeguros)"
    );
    $consulta->execute();
    $resultado = $consulta->get_result();
    while ($producto = $resultado->fetch_assoc()) {
        $id = (int) $producto["id"];
        $cantidad = min((int) $_SESSION["carritoTienda"][$id], (int) $producto["stock"]);
        if ($cantidad < 1) {
            unset($_SESSION["carritoTienda"][$id]);
            continue;
        }
        $_SESSION["carritoTienda"][$id] = $cantidad;
        $producto["cantidad"] = $cantidad;
        $producto["subtotal"] = $cantidad * (float) $producto["precio"];
        $totalCarrito += $producto["subtotal"];
        $productosCarrito[] = $producto;
    }
    $consulta->close();
}
$conexion->close();
$escapar = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pet Shop | Veterinaria Animalada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f6f8fb; color: #363949; }
    .barra-tienda { background: #fff; }
    .producto { border: 0; box-shadow: 0 1rem 2rem rgba(54,57,73,.08); }
    .producto img { width: 100%; height: 210px; object-fit: cover; background: #eef5f9; }
    .precio { color: #078fc9; font-size: 1.35rem; font-weight: 800; }
    .btn-animalada { border: 0; background: #02a2ec; color: #fff; font-weight: 700; }
    .btn-animalada:hover { background: #111e88; color: #fff; }
    .checkout { position: sticky; top: 1rem; }
  </style>
</head>
<body>
<nav class="navbar barra-tienda border-bottom">
  <div class="container d-flex justify-content-between">
    <a class="navbar-brand fw-bold" href="menu.php">Veterinaria Animalada</a>
    <div class="d-flex gap-2">
      <?php if (!empty($_SESSION["adminAutenticado"])) { ?><a class="btn btn-outline-primary" href="shop_admin.php">Administrar tienda</a><?php } ?>
      <a class="btn btn-outline-secondary" href="menu.php">Volver al sitio</a>
    </div>
  </div>
</nav>
<main class="container py-5">
  <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div><p class="text-uppercase text-primary fw-bold mb-1">Pet Shop</p><h1 class="fw-bold mb-1">Todo para su bienestar</h1><p class="text-secondary mb-0">Agregá productos al carrito y coordinamos el retiro y el pago.</p></div>
    <a class="btn btn-animalada" href="#carrito">Ver carrito (<?php echo count($_SESSION["carritoTienda"]); ?>)</a>
  </div>
  <?php if ($mensaje !== "") { ?><div class="alert alert-success" role="status"><?php echo $escapar($mensaje); ?></div><?php } ?>
  <?php if ($error !== "") { ?><div class="alert alert-danger" role="alert"><?php echo $escapar($error); ?></div><?php } ?>
  <?php if (isset($_GET["pedido"]) && ctype_digit((string) $_GET["pedido"])) { ?>
    <div class="alert alert-success"><h2 class="h5">¡Pedido recibido!</h2>Tu número de pedido es <strong>#<?php echo $escapar($_GET["pedido"]); ?></strong>. Nos comunicaremos para coordinar el retiro y el pago.</div>
  <?php } ?>
  <?php if (!$productos) { ?><div class="alert alert-info">Todavía no hay productos disponibles.</div><?php } ?>
  <div class="row g-4">
    <section class="col-lg-8" aria-label="Catálogo de productos">
      <div class="row g-4">
        <?php foreach ($productos as $producto) { ?>
          <div class="col-md-6">
            <article class="card producto h-100">
              <img src="<?php echo $escapar($producto["imagen"]); ?>" class="card-img-top" alt="<?php echo $escapar($producto["nombre"]); ?>">
              <div class="card-body d-flex flex-column">
                <h2 class="h5 fw-bold"><?php echo $escapar($producto["nombre"]); ?></h2>
                <p class="text-secondary"><?php echo $escapar($producto["descripcion"]); ?></p>
                <p class="precio mt-auto mb-1">$<?php echo number_format((float) $producto["precio"], 2, ",", "."); ?></p>
                <p class="small text-secondary">Disponibles: <?php echo (int) $producto["stock"]; ?></p>
                <form method="post" action="shop.php">
                  <input type="hidden" name="token" value="<?php echo $escapar($token); ?>">
                  <input type="hidden" name="accion" value="agregar">
                  <input type="hidden" name="producto_id" value="<?php echo (int) $producto["id"]; ?>">
                  <button class="btn btn-animalada w-100" type="submit" <?php echo (int) $producto["stock"] < 1 ? "disabled" : ""; ?>><?php echo (int) $producto["stock"] < 1 ? "Sin stock" : "Agregar al carrito"; ?></button>
                </form>
              </div>
            </article>
          </div>
        <?php } ?>
      </div>
    </section>
    <aside class="col-lg-4" id="carrito">
      <div class="card border-0 shadow-sm checkout">
        <div class="card-body p-4">
          <h2 class="h4 fw-bold">Tu carrito</h2>
          <?php if (!$productosCarrito) { ?>
            <p class="text-secondary">Todavía no agregaste productos.</p>
          <?php } else { ?>
            <form method="post" action="shop.php#carrito">
              <input type="hidden" name="token" value="<?php echo $escapar($token); ?>">
              <input type="hidden" name="accion" value="actualizar">
              <?php foreach ($productosCarrito as $producto) { ?>
                <div class="d-flex gap-3 align-items-center border-bottom py-3">
                  <img src="<?php echo $escapar($producto["imagen"]); ?>" alt="" width="58" height="58">
                  <div class="flex-grow-1"><div class="fw-semibold"><?php echo $escapar($producto["nombre"]); ?></div><div class="small">$<?php echo number_format((float) $producto["subtotal"], 2, ",", "."); ?></div></div>
                  <input class="form-control form-control-sm" type="number" min="0" max="<?php echo (int) $producto["stock"]; ?>" name="cantidades[<?php echo (int) $producto["id"]; ?>]" value="<?php echo (int) $producto["cantidad"]; ?>" aria-label="Cantidad de <?php echo $escapar($producto["nombre"]); ?>" style="width:76px">
                </div>
              <?php } ?>
              <button class="btn btn-outline-primary btn-sm mt-3" type="submit">Actualizar carrito</button>
            </form>
            <div class="d-flex justify-content-between fs-5 fw-bold mt-4 mb-3"><span>Total</span><span>$<?php echo number_format($totalCarrito, 2, ",", "."); ?></span></div>
            <hr>
            <h3 class="h5 fw-bold">Coordinar pedido</h3>
            <form method="post" action="shop.php">
              <input type="hidden" name="token" value="<?php echo $escapar($token); ?>">
              <input type="hidden" name="accion" value="confirmar_pedido">
              <label class="form-label" for="cliente">Nombre</label><input class="form-control mb-2" id="cliente" name="cliente" maxlength="120" required>
              <label class="form-label" for="email">Correo</label><input class="form-control mb-2" type="email" id="email" name="email" maxlength="190" required>
              <label class="form-label" for="telefono">Teléfono</label><input class="form-control mb-2" id="telefono" name="telefono" maxlength="40" required>
              <label class="form-label" for="notas">Notas (opcional)</label><textarea class="form-control mb-3" id="notas" name="notas" maxlength="500" rows="2"></textarea>
              <button class="btn btn-animalada w-100" type="submit">Confirmar pedido</button>
              <p class="small text-secondary mt-2 mb-0">El pago se coordina al retirar el pedido. Este sitio no procesa tarjetas.</p>
            </form>
          <?php } ?>
        </div>
      </div>
    </aside>
  </div>
</main>
</body>
</html>
