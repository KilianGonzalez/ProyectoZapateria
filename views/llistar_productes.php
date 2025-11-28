<?php
require_once __DIR__ . '/../model/productos.php';
$productos = obtenerTodosProductos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Zapatería - Listado de productos</title>
</head>
<body>
    <h1>Zapatería - Productos</h1>

    <label for="buscador">Buscar productos:</label>
    <input type="text" id="buscador" name="buscador" autocomplete="off" placeholder="Color, marca, tipo, talla...">

    <h2>Resultados de búsqueda</h2>
    <div id="resultados"></div>

    <h2>Todos los productos</h2>
    <ul>
        <?php foreach ($productos as $p): ?>
            <li>
                <?php echo htmlspecialchars($p['tipo'] . ' ' . $p['marca'] . ' ' . $p['color'] . ' ' . $p['talla'], ENT_QUOTES, 'UTF-8'); ?>
                - <?php echo number_format((float)$p['precio'], 2); ?> €
            </li>
        <?php endforeach; ?>
    </ul>

    <script>
    (function() {
        const input = document.getElementById('buscador');
        const contenedor = document.getElementById('resultados');
        let lastController = null;

        function renderResultados(data) {
            contenedor.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                contenedor.textContent = 'No se han encontrado productos.';
                return;
            }

            const ul = document.createElement('ul');

            data.forEach(function(p) {
                const li = document.createElement('li');
                const texto = (p.tipo || '') + ' ' + (p.marca || '') + ' ' + (p.color || '') + ' ' + (p.talla || '');
                li.textContent = texto.trim() + ' - ' + Number(p.precio).toFixed(2) + ' €';
                ul.appendChild(li);
            });

            contenedor.appendChild(ul);
        }

        input.addEventListener('keyup', function() {
            const term = input.value.trim();

            if (term === '') {
                contenedor.innerHTML = '';
                return;
            }

            const url = 'controller/buscar_productos.php?term=' + encodeURIComponent(term);

            fetch(url, { method: 'GET' })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Error HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(renderResultados)
                .catch(function(error) {
                    console.error(error);
                    contenedor.textContent = 'Error al realizar la búsqueda.';
                });
        });
    })();
    </script>
</body>
</html>
