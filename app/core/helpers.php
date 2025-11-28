<?php

/**
 * Genera la URL a la imagen de un producto según marca, modelo y color.
 * Convenión: public/img/{marca}/{modelo}_{color}.jpg
 * Si no existe el archivo, devuelve una imagen por defecto.
 */
function urlImagenProducto(string $marca, string $modelo, string $color): string {
    $base = 'img';  // Ruta relativa desde la raíz del proyecto
    $sanitizedMarca = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower(trim($marca)));
    $sanitizedModelo = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower(trim($modelo)));
    $sanitizedColor = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower(trim($color)));

    $pathRelativo = "$base/$sanitizedMarca/{$sanitizedModelo}_{$sanitizedColor}.jpg";

    // Comprobar si el archivo existe físicamente (opcional, para desarrollo)
    $absPath = __DIR__ . '/../public/' . $pathRelativo;
    if (!file_exists($absPath)) {
        // Imagen por defecto si no existe la específica
        $pathRelativo = "$base/placeholder.jpg";
    }

    return $pathRelativo;
}
