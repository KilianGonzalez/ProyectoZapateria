<?php
// Front controller sencillo: redirige a la vista principal de productos o a la ficha
require_once __DIR__ . '/model/connectaDB.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'listar';

switch ($action) {
    case 'ficha':
        require_once __DIR__ . '/views/ficha_producte.php';
        break;
    case 'listar':
    default:
        require_once __DIR__ . '/model/productos.php';
        require_once __DIR__ . '/views/llistar_productes.php';
        break;
}
