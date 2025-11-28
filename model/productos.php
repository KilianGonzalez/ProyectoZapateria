<?php

require_once __DIR__ . '/connectaDB.php';

function obtenerTodosProductos(): array {
    $pdo = getConnection();

    $sql = 'SELECT p.id, p.color, p.talla, p.precio, p.sexo, 
                   m.nombre AS marca, t.nombre AS tipo
            FROM productos p
            JOIN marcas m ON p.idMarca = m.id
            JOIN tipoproductos t ON p.idTipo = t.id';

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function buscarProductosPorTermino(string $term): array {
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
    $stmt->execute();

    return $stmt->fetchAll();
}
