<?php
function inicializarTienda(mysqli $conexion): void
{
    $conexion->query(
        "CREATE TABLE IF NOT EXISTS productos_tienda (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(120) NOT NULL,
            descripcion VARCHAR(500) NOT NULL,
            precio DECIMAL(10,2) NOT NULL,
            stock INT NOT NULL DEFAULT 0,
            imagen VARCHAR(255) NOT NULL,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
    $conexion->query(
        "CREATE TABLE IF NOT EXISTS pedidos_tienda (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente VARCHAR(120) NOT NULL,
            email VARCHAR(190) NOT NULL,
            telefono VARCHAR(40) NOT NULL,
            notas VARCHAR(500) NOT NULL DEFAULT '',
            total DECIMAL(10,2) NOT NULL,
            estado ENUM('pendiente','confirmado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
            creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
    $conexion->query(
        "CREATE TABLE IF NOT EXISTS pedido_tienda_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pedido_id INT NOT NULL,
            producto_id INT NULL,
            producto_nombre VARCHAR(120) NOT NULL,
            precio DECIMAL(10,2) NOT NULL,
            cantidad INT NOT NULL,
            CONSTRAINT fk_pedido_tienda FOREIGN KEY (pedido_id)
                REFERENCES pedidos_tienda(id) ON DELETE CASCADE,
            CONSTRAINT fk_producto_tienda FOREIGN KEY (producto_id)
                REFERENCES productos_tienda(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $totalProductos = (int) $conexion->query("SELECT COUNT(*) AS total FROM productos_tienda")->fetch_assoc()["total"];
    if ($totalProductos > 0) {
        return;
    }

    $productos = [
        ["Alimento balanceado para perro", "Nutrición completa para perros adultos. Bolsa de 3 kg.", 890, 20, "imgs/shop/alimento-perro.svg"],
        ["Alimento para gato", "Receta equilibrada para gatos adultos. Bolsa de 1,5 kg.", 620, 18, "imgs/shop/alimento-gato.svg"],
        ["Pelota resistente", "Juguete liviano para juegos y paseos.", 240, 30, "imgs/shop/pelota.svg"],
        ["Collar ajustable", "Collar cómodo con hebilla segura. Talle regulable.", 390, 15, "imgs/shop/collar.svg"],
    ];
    $insertar = $conexion->prepare(
        "INSERT INTO productos_tienda (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)"
    );
    foreach ($productos as $producto) {
        [$nombre, $descripcion, $precio, $stock, $imagen] = $producto;
        $insertar->bind_param("ssdis", $nombre, $descripcion, $precio, $stock, $imagen);
        $insertar->execute();
    }
    $insertar->close();
}
