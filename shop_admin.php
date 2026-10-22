<?php
require_once __DIR__ . "/auth.php";
exigirAdmin();
require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/tienda_db.php";

inicializarTienda($conexion);
if (!isset($_SESSION["tokenAdminTienda"])) {
    $_SESSION["tokenAdminTienda"] = bin2hex(random_bytes(32));
}
$token = $_SESSION["tokenAdminTienda"];
$error = "";
$mensaje = (string) ($_SESSION["mensajeAdminTienda"] ?? "");
unset($_SESSION["mensajeAdminTienda"]);
$imagenes = [
    "imgs/shop/alimento-perro.svg",
    "imgs/shop/alimento-gato.svg",
    "imgs/shop/pelota.svg",
    "imgs/shop/collar.svg",
];
$transaccionIniciada = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($token, (string) ($_POST["token"] ?? ""))) {
        http_response_code(403);
        $error = "La sesión del formulario venció. Actualizá la página e intentá de nuevo.";
    } else {
        $accion = (string) ($_POST["accion"] ?? "");
        try {
            if ($accion === "guardar_producto") {
                $id = filter_var($_POST["id"] ?? 0, FILTER_VALIDATE_INT);
                $nombre = trim((string) ($_POST["nombre"] ?? ""));
                $descripcion = trim((string) ($_POST["descripcion"] ?? ""));
                $precio = filter_var($_POST["precio"] ?? null, FILTER_VALIDATE_FLOAT);
                $stock = filter_var($_POST["stock"] ?? null, FILTER_VALIDATE_INT);
                $imagen = (string) ($_POST["imagen"] ?? "");
                if ($id === false || $id < 0 || $nombre === "" || strlen($nombre) > 120 || $descripcion === "" || strlen($descripcion) > 500 || $precio === false || $precio <= 0 || $stock === false || $stock < 0 || !in_array($imagen, $imagenes, true)) {
                    throw new InvalidArgumentException("Completá los campos con valores válidos.");
                }
                if ($id && $id > 0) {
                    $consulta = $conexion->prepare("UPDATE productos_tienda SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ?, activo = 1 WHERE id = ?");
                    $consulta->bind_param("ssdisi", $nombre, $descripcion, $precio, $stock, $imagen, $id);
                } else {
                    $consulta = $conexion->prepare("INSERT INTO productos_tienda (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)");
                    $consulta->bind_param("ssdis", $nombre, $descripcion, $precio, $stock, $imagen);
                }
                $consulta->execute();
                $consulta->close();
                $mensaje = "Producto guardado.";
            } elseif ($accion === "desactivar_producto") {
                $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);
                if ($id === false || $id < 1) {
                    throw new InvalidArgumentException("El producto indicado no es válido.");
                }
                $consulta = $conexion->prepare("UPDATE productos_tienda SET activo = 0 WHERE id = ?");
                $consulta->bind_param("i", $id);
                $consulta->execute();
                $consulta->close();
                $mensaje = "Producto retirado del catálogo.";
            } elseif ($accion === "cambiar_estado") {
                $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);
                $estado = (string) ($_POST["estado"] ?? "");
                if ($id === false || $id < 1 || !in_array($estado, ["pendiente", "confirmado", "entregado", "cancelado"], true)) {
                    throw new InvalidArgumentException("El pedido o estado indicado no es válido.");
                }
                $conexion->begin_transaction();
                $transaccionIniciada = true;
                $consulta = $conexion->prepare("SELECT estado FROM pedidos_tienda WHERE id = ? FOR UPDATE");
                $consulta->bind_param("i", $id);
                $consulta->execute();
                $pedidoActual = $consulta->get_result()->fetch_assoc();
                $consulta->close();
                if (!$pedidoActual) {
                    throw new InvalidArgumentException("No se encontró el pedido indicado.");
                }
                if ($pedidoActual["estado"] === "cancelado" && $estado !== "cancelado") {
                    throw new InvalidArgumentException("Un pedido cancelado no se puede reactivar.");
                }
                if ($estado === "cancelado" && $pedidoActual["estado"] !== "cancelado") {
                    $consulta = $conexion->prepare(
                        "SELECT producto_id, cantidad FROM pedido_tienda_items WHERE pedido_id = ? AND producto_id IS NOT NULL"
                    );
                    $consulta->bind_param("i", $id);
                    $consulta->execute();
                    $itemsPedido = $consulta->get_result();
                    $restaurarStock = $conexion->prepare("UPDATE productos_tienda SET stock = stock + ? WHERE id = ?");
                    while ($item = $itemsPedido->fetch_assoc()) {
                        $productoId = (int) $item["producto_id"];
                        $cantidad = (int) $item["cantidad"];
                        $restaurarStock->bind_param("ii", $cantidad, $productoId);
                        $restaurarStock->execute();
                    }
                    $restaurarStock->close();
                    $consulta->close();
                }
                $consulta = $conexion->prepare("UPDATE pedidos_tienda SET estado = ? WHERE id = ?");
                $consulta->bind_param("si", $estado, $id);
                $consulta->execute();
                $consulta->close();
                $conexion->commit();
                $transaccionIniciada = false;
                $mensaje = "Estado del pedido actualizado.";
            }
        } catch (InvalidArgumentException $exception) {
            if ($transaccionIniciada) {
                $conexion->rollback();
                $transaccionIniciada = false;
            }
            $error = $exception->getMessage();
        } catch (Throwable $exception) {
            if ($transaccionIniciada) {
                $conexion->rollback();
                $transaccionIniciada = false;
            }
            error_log("Error al administrar Pet Shop: " . $exception->getMessage());
            $error = "No se pudo guardar el cambio. Intentá nuevamente.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $mensaje !== "" && $error === "") {
    $_SESSION["mensajeAdminTienda"] = $mensaje;
    header("Location: shop_admin.php");
    exit();
}

$productoEditar = null;
$idEditar = filter_var($_GET["editar"] ?? null, FILTER_VALIDATE_INT);
if ($idEditar && $idEditar > 0) {
    $consulta = $conexion->prepare("SELECT id, nombre, descripcion, precio, stock, imagen FROM productos_tienda WHERE id = ?");
    $consulta->bind_param("i", $idEditar);
    $consulta->execute();
    $productoEditar = $consulta->get_result()->fetch_assoc() ?: null;
    $consulta->close();
}
$productos = $conexion->query("SELECT id, nombre, precio, stock, imagen, activo FROM productos_tienda ORDER BY activo DESC, nombre")->fetch_all(MYSQLI_ASSOC);
$pedidos = $conexion->query(
    "SELECT p.id, p.cliente, p.email, p.telefono, p.notas, p.total, p.estado, p.creado_en, i.articulos
     FROM pedidos_tienda p
     LEFT JOIN (
         SELECT pedido_id, GROUP_CONCAT(CONCAT(producto_nombre, ' × ', cantidad) ORDER BY id SEPARATOR ', ') AS articulos
         FROM pedido_tienda_items
         GROUP BY pedido_id
     ) i ON i.pedido_id = p.id
     ORDER BY p.creado_en DESC"
)->fetch_all(MYSQLI_ASSOC);
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
  <title>Administrar Pet Shop | Animalada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar bg-white border-bottom">
  <div class="container"><a class="navbar-brand fw-bold" href="indexD.php">Panel administrador</a><a href="shop.php" class="btn btn-outline-primary">Ver tienda</a></div>
</nav>
<main class="container py-4">
  <h1 class="fw-bold">Administrar Pet Shop</h1>
  <p class="text-secondary">Editá los productos de ejemplo y revisá los pedidos para coordinar su retiro y pago.</p>
  <?php if ($mensaje !== "") { ?><div class="alert alert-success"><?php echo $escapar($mensaje); ?></div><?php } ?>
  <?php if ($error !== "") { ?><div class="alert alert-danger"><?php echo $escapar($error); ?></div><?php } ?>
  <div class="row g-4">
    <section class="col-lg-5">
      <div class="card border-0 shadow-sm"><div class="card-body">
        <h2 class="h4"><?php echo $productoEditar ? "Editar producto" : "Agregar producto"; ?></h2>
        <form method="post" action="shop_admin.php<?php echo $productoEditar ? "?editar=" . (int) $productoEditar["id"] : ""; ?>">
          <input type="hidden" name="token" value="<?php echo $escapar($token); ?>">
          <input type="hidden" name="accion" value="guardar_producto">
          <input type="hidden" name="id" value="<?php echo (int) ($productoEditar["id"] ?? 0); ?>">
          <label class="form-label" for="nombre">Nombre</label><input class="form-control mb-3" id="nombre" name="nombre" maxlength="120" required value="<?php echo $escapar($productoEditar["nombre"] ?? ""); ?>">
          <label class="form-label" for="descripcion">Descripción</label><textarea class="form-control mb-3" id="descripcion" name="descripcion" maxlength="500" rows="3" required><?php echo $escapar($productoEditar["descripcion"] ?? ""); ?></textarea>
          <div class="row">
            <div class="col"><label class="form-label" for="precio">Precio ($)</label><input class="form-control mb-3" id="precio" name="precio" type="number" min="0.01" step="0.01" required value="<?php echo $escapar($productoEditar["precio"] ?? ""); ?>"></div>
            <div class="col"><label class="form-label" for="stock">Stock</label><input class="form-control mb-3" id="stock" name="stock" type="number" min="0" step="1" required value="<?php echo $escapar($productoEditar["stock"] ?? 0); ?>"></div>
          </div>
          <label class="form-label" for="imagen">Imagen</label><select class="form-select mb-3" id="imagen" name="imagen" required>
            <?php foreach ($imagenes as $imagen) { ?><option value="<?php echo $escapar($imagen); ?>" <?php echo (($productoEditar["imagen"] ?? $imagenes[0]) === $imagen) ? "selected" : ""; ?>><?php echo $escapar(ucwords(str_replace(["imgs/shop/", ".svg", "-"], ["", "", " "], $imagen))); ?></option><?php } ?>
          </select>
          <button class="btn btn-primary" type="submit">Guardar producto</button>
          <?php if ($productoEditar) { ?><a class="btn btn-outline-secondary" href="shop_admin.php">Cancelar</a><?php } ?>
        </form>
      </div></div>
    </section>
    <section class="col-lg-7">
      <div class="card border-0 shadow-sm"><div class="card-body">
        <h2 class="h4">Catálogo</h2>
        <div class="table-responsive"><table class="table align-middle">
          <thead><tr><th>Producto</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead><tbody>
          <?php foreach ($productos as $producto) { ?>
            <tr>
              <td><div class="d-flex align-items-center gap-2"><img src="<?php echo $escapar($producto["imagen"]); ?>" alt="" width="52" height="44"><span><?php echo $escapar($producto["nombre"]); ?><?php if (!$producto["activo"]) { ?><small class="d-block text-secondary">Retirado</small><?php } ?></span></div></td>
              <td>$<?php echo number_format((float) $producto["precio"], 2, ",", "."); ?></td><td><?php echo (int) $producto["stock"]; ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="shop_admin.php?editar=<?php echo (int) $producto["id"]; ?>">Editar</a>
                <?php if ($producto["activo"]) { ?><form class="d-inline" method="post" action="shop_admin.php" onsubmit="return confirm('¿Retirar este producto de la tienda?')"><input type="hidden" name="token" value="<?php echo $escapar($token); ?>"><input type="hidden" name="accion" value="desactivar_producto"><input type="hidden" name="id" value="<?php echo (int) $producto["id"]; ?>"><button class="btn btn-sm btn-outline-danger" type="submit">Retirar</button></form><?php } ?>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table></div>
      </div></div>
    </section>
  </div>
  <section class="card border-0 shadow-sm mt-4"><div class="card-body">
    <h2 class="h4">Pedidos</h2>
    <?php if (!$pedidos) { ?><p class="text-secondary mb-0">Todavía no hay pedidos.</p><?php } else { ?>
      <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>N.º / fecha</th><th>Cliente</th><th>Pedido</th><th>Total</th><th>Estado</th></tr></thead><tbody>
        <?php foreach ($pedidos as $pedido) { ?>
          <tr>
            <td>#<?php echo (int) $pedido["id"]; ?><small class="d-block text-secondary"><?php echo $escapar($pedido["creado_en"]); ?></small></td>
            <td><?php echo $escapar($pedido["cliente"]); ?><small class="d-block"><?php echo $escapar($pedido["email"]); ?></small><small><?php echo $escapar($pedido["telefono"]); ?></small></td>
            <td><?php echo $escapar($pedido["articulos"] ?? ""); ?><?php if ($pedido["notas"] !== "") { ?><small class="d-block text-secondary">Nota: <?php echo $escapar($pedido["notas"]); ?></small><?php } ?></td>
            <td>$<?php echo number_format((float) $pedido["total"], 2, ",", "."); ?></td>
            <td><form method="post" action="shop_admin.php" class="d-flex gap-2"><input type="hidden" name="token" value="<?php echo $escapar($token); ?>"><input type="hidden" name="accion" value="cambiar_estado"><input type="hidden" name="id" value="<?php echo (int) $pedido["id"]; ?>"><select class="form-select form-select-sm" name="estado" aria-label="Estado del pedido <?php echo (int) $pedido["id"]; ?>"><?php foreach (["pendiente" => "Pendiente", "confirmado" => "Confirmado", "entregado" => "Entregado", "cancelado" => "Cancelado"] as $valor => $etiqueta) { ?><option value="<?php echo $valor; ?>" <?php echo $pedido["estado"] === $valor ? "selected" : ""; ?>><?php echo $etiqueta; ?></option><?php } ?></select><button class="btn btn-sm btn-outline-primary" type="submit">Guardar</button></form></td>
          </tr>
        <?php } ?>
        </tbody></table></div>
    <?php } ?>
  </div></section>
</main>
</body>
</html>
