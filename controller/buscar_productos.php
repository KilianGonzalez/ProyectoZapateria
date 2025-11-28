<?php

require_once __DIR__ . '/../model/productos.php';

header('Content-Type: application/json; charset=utf-8');

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if ($term === '') {
    echo json_encode([
        'productos' => [],
        'debug' => [
            'term_recibido' => $term,
            'mensaje' => 'Term vacío, devolviendo array vacío'
        ]
    ]);
    exit;
}

try {
    $productos = buscarProductosPorTermino($term);
    
    echo json_encode([
        'productos' => $productos,
        'debug' => [
            'term_recibido' => $term,
            'num_productos' => count($productos),
            'productos_ejemplo' => array_slice($productos, 0, 2) // Muestra hasta 2 productos de ejemplo
        ]
    ]);
} catch (Throwable $e) {
    $debug = [
        'error' => 'Error al buscar productos',
        'details' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ];
    http_response_code(500);
    echo json_encode([
        'productos' => [],
        'debug' => $debug
    ]);
}
