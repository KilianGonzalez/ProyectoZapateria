<?php
// Front controller sencillo: redirige a la vista principal de productos
require_once __DIR__ . '/model/connectaDB.php';
require_once __DIR__ . '/model/productos.php';

// Cargamos directamente la vista de listado de productos
require __DIR__ . '/views/llistar_productes.php';
