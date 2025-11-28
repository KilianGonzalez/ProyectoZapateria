<?php

require_once __DIR__ . '/connectaDB.php';
require_once __DIR__ . '/../app/core/helpers.php';

function obtenerTodosProductos(): array {
    $pdo = getConnection();

    $sql = 'SELECT p.id, p.color, p.talla, p.precio, p.sexo,
                   m.nombre AS marca, t.nombre AS tipo
            FROM productos p
            JOIN marcas m ON p.idMarca = m.id
            JOIN tipoproductos t ON p.idTipo = t.id';

    $stmt = $pdo->query($sql);
    $productos = $stmt->fetchAll();

    // Añadir URL de imagen a cada producto
    foreach ($productos as &$p) {
        $p['imagen'] = urlImagenProducto($p['marca'], $p['tipo'], $p['color']);
    }

    return $productos;
}

function buscarProductosPorTermino(string $term): array {
    try {
        $pdo = getConnection();

        $termLike = '%' . $term . '%';

        $sql = 'SELECT p.id, p.color, p.talla, p.precio, p.sexo,
                       m.nombre AS marca, t.nombre AS tipo
                FROM productos p
                JOIN marcas m ON p.idMarca = m.id
                JOIN tipoproductos t ON p.idTipo = t.id
                WHERE p.color LIKE :term
                   OR p.talla LIKE :term
                   OR m.nombre LIKE :term
                   OR t.nombre LIKE :term
                   OR p.sexo LIKE :term';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':term', $termLike, PDO::PARAM_STR);
        
        // Debug: mostrar la SQL y el término
        error_log("SQL: " . $sql);
        error_log("Término LIKE: " . $termLike);
        
        $stmt->execute();
        $productos = $stmt->fetchAll();

        error_log("Resultados encontrados: " . count($productos));

        // Añadir URL de imagen a cada producto
        foreach ($productos as &$p) {
            $p['imagen'] = urlImagenProducto($p['marca'], $p['tipo'], $p['color']);
        }

        return $productos;
    } catch (Exception $e) {
        // En caso de error, devolver un array vacío para evitar 500
        error_log('Error en buscarProductosPorTermino: ' . $e->getMessage());
        return [];
    }
}
