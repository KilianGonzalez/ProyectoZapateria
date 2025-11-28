<?php

require_once __DIR__ . '/../model/productos.php';

header('Content-Type: application/json; charset=utf-8');

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if ($term === '') {
    echo json_encode([]);
    exit;
}

try {
    $productos = buscarProductosPorTermino($term);
    echo json_encode($productos);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al buscar productos']);
}
