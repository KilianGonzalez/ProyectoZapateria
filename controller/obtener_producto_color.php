<?php

require_once __DIR__ . '/../model/productos.php';

header('Content-Type: application/json; charset=utf-8');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$color = isset($_GET['color']) ? trim($_GET['color']) : '';

if ($id <= 0 || $color === '') {
    echo json_encode(['error' => 'Parámetros inválidos']);
    exit;
}

try {
    $pdo = getConnection();

    $stmt = $pdo->prepare('
        SELECT p.id, p.color, p.talla, p.precio, p.sexo,
               m.nombre AS marca, t.nombre AS tipo
        FROM productos p
        JOIN marcas m ON p.idMarca = m.id
        JOIN tipoproductos t ON p.idTipo = t.id
        WHERE p.id = :id AND p.color = :color
    ');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':color', $color, PDO::PARAM_STR);
    $stmt->execute();

    $producto = $stmt->fetch();

    if (!$producto) {
        echo json_encode(['error' => 'Producto no encontrado para este color']);
        exit;
    }

    // Añadir URL de imagen
    require_once __DIR__ . '/../app/core/helpers.php';
    $producto['imagen'] = urlImagenProducto($producto['marca'], $producto['tipo'], $producto['color']);

    echo json_encode($producto);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener el producto']);
}
