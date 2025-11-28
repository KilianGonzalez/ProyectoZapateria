<?php
require_once __DIR__ . '/../model/productos.php';
$productos = obtenerTodosProductos();

// Debug: muestra información sobre los productos
error_log('Número de productos encontrados: ' . count($productos));
if (!empty($productos)) {
    error_log('Primer producto: ' . print_r($productos[0], true));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Zapatería - Tienda Online</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f5f5; }
        header { background: #222; color: white; padding: 1rem; text-align: center; }
        .buscador-container { max-width: 600px; margin: 1rem auto; }
        #buscador { width: 100%; padding: 0.5rem; font-size: 1rem; }
        #resultados { margin: 1rem auto; max-width: 800px; }
        .producto-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; max-width: 1000px; margin: 1rem auto; padding: 0 1rem; }
        .producto { background: white; border: 1px solid #ddd; border-radius: 4px; padding: 1rem; text-align: center; }
        .producto img { max-width: 100%; height: 150px; object-fit: cover; border-radius: 3px; }
        .producto .nombre { font-weight: bold; margin: 0.5rem 0; }
        .producto .precio { color: #c00; font-size: 1.2rem; }
        .producto .detalles { font-size: 0.9rem; color: #666; }
        .producto a { text-decoration: none; color: #337ab7; }
        .producto a:hover { text-decoration: underline; }
        #resultados ul { list-style: none; padding: 0; }
        #resultados li { background: white; border: 1px solid #ddd; padding: 0.5rem; margin-bottom: 0.5rem; border-radius: 3px; }
    </style>
</head>
<body>
    <header>
        <h1>Zapatería Online</h1>
    </header>

    <div class="buscador-container">
        <label for="buscador">Buscar productos:</label>
        <input type="text" id="buscador" name="buscador" autocomplete="off" placeholder="Color, marca, tipo, talla...">
    </div>

    <div id="resultados"></div>

    <h2 style="text-align: center;">Todos los productos</h2>
    
    <!-- Debug info -->
    <div style="background: #f0f0f0; padding: 1rem; margin: 1rem auto; max-width: 800px; border-radius: 4px;">
        <strong>Debug:</strong><br>
        Número de productos encontrados: <?php echo count($productos); ?><br>
        <?php if (empty($productos)): ?>
            <span style="color: red;">No se encontraron productos. Revisa:</span><br>
            - Que la BD 'zapateria' exista<br>
            - Que las tablas 'productos', 'marcas', 'tipoproductos' existan<br>
            - Que haya datos en esas tablas<br>
            - Que el usuario 'root' sin contraseña tenga acceso
        <?php endif; ?>
    </div>

    <div class="producto-grid">
        <?php foreach ($productos as $p): ?>
            <div class="producto">
                <img src="<?php echo htmlspecialchars($p['imagen'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['tipo'] . ' ' . $p['marca'] . ' ' . $p['color'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="nombre"><?php echo htmlspecialchars($p['tipo'] . ' ' . $p['marca'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="detalles">
                    Color: <?php echo htmlspecialchars($p['color'], ENT_QUOTES, 'UTF-8'); ?><br>
                    Talla: <?php echo htmlspecialchars($p['talla'], ENT_QUOTES, 'UTF-8'); ?><br>
                    Sexo: <?php echo htmlspecialchars($p['sexo'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="precio"><?php echo number_format((float)$p['precio'], 2); ?> €</div>
                <a href="index.php?action=ficha&id=<?php echo (int)$p['id']; ?>">Ver detalles</a>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
    (function() {
        const input = document.getElementById('buscador');
        const contenedor = document.getElementById('resultados');

        function renderResultados(data) {
            // Mostrar debug en consola
            console.log('=== DEBUG AJAX ===');
            console.log('Término recibido:', data.debug?.term_recibido);
            console.log('Número de productos:', data.debug?.num_productos);
            console.log('Productos de ejemplo:', data.debug?.productos_ejemplo);
            console.log('Respuesta completa:', data);
            console.log('==================');

            contenedor.innerHTML = '';

            const productos = data.productos || [];

            if (!Array.isArray(productos) || productos.length === 0) {
                contenedor.textContent = 'No se han encontrado productos.';
                return;
            }

            const ul = document.createElement('ul');

            productos.forEach(function(p) {
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
                .then(function(data) {
                    console.log('Respuesta AJAX:', data); // Debug
                    renderResultados(data);
                })
                .catch(function(error) {
                    console.error('Error al realizar la búsqueda:', error);
                    contenedor.textContent = 'Error al realizar la búsqueda.';
                });
        });
    })();
    </script>
</body>
</html>
