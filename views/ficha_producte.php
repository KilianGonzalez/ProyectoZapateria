<?php
require_once __DIR__ . '/../model/productos.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('ID de producto no válido');
}

// Obtener producto y colores disponibles
$pdo = getConnection();
$stmt = $pdo->prepare('
    SELECT p.id, p.color, p.talla, p.precio, p.sexo,
           m.nombre AS marca, t.nombre AS tipo
    FROM productos p
    JOIN marcas m ON p.idMarca = m.id
    JOIN tipoproductos t ON p.idTipo = t.id
    WHERE p.id = :id
');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$producto = $stmt->fetch();

if (!$producto) {
    die('Producto no encontrado');
}

// Obtener colores disponibles para este modelo (mismo tipo y marca)
$stmt = $pdo->prepare('
    SELECT DISTINCT p.color
    FROM productos p
    JOIN marcas m ON p.idMarca = m.id
    JOIN tipoproductos t ON p.idTipo = t.id
    WHERE m.nombre = :marca AND t.nombre = :tipo
    ORDER BY p.color
');
$stmt->bindValue(':marca', $producto['marca']);
$stmt->bindValue(':tipo', $producto['tipo']);
$stmt->execute();
$colores = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

$producto['imagen'] = urlImagenProducto($producto['marca'], $producto['tipo'], $producto['color']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($producto['tipo'] . ' ' . $producto['marca'], ENT_QUOTES, 'UTF-8'); ?> - Zapatería</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f5f5; }
        header { background: #222; color: white; padding: 1rem; text-align: center; }
        .container { max-width: 800px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .producto-imagen { text-align: center; margin-bottom: 2rem; }
        .producto-imagen img { max-width: 100%; height: 300px; object-fit: cover; border-radius: 4px; }
        .producto-info h2 { margin: 0 0 1rem; }
        .producto-info .precio { font-size: 1.5rem; color: #c00; margin: 1rem 0; }
        .producto-info .detalles { color: #666; margin: 1rem 0; }
        .colores { margin: 1.5rem 0; }
        .colores label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .colores select { padding: 0.5rem; font-size: 1rem; }
        .acciones { margin-top: 2rem; }
        .btn { display: inline-block; padding: 0.8rem 1.5rem; background: #337ab7; color: white; text-decoration: none; border-radius: 4px; font-size: 1rem; }
        .btn:hover { background: #285f8f; }
        .btn-comprar { background: #5cb85c; }
        .btn-comprar:hover { background: #4cae4c; }
        .back-link { display: block; margin-top: 1rem; color: #337ab7; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <h1>Zapatería Online</h1>
    </header>

    <div class="container">
        <div class="producto-imagen">
            <img id="imagen-producto" src="<?php echo htmlspecialchars($producto['imagen'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($producto['tipo'] . ' ' . $producto['marca'] . ' ' . $producto['color'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="producto-info">
            <h2><?php echo htmlspecialchars($producto['tipo'] . ' ' . $producto['marca'], ENT_QUOTES, 'UTF-8'); ?></h2>
            
            <div class="precio"><?php echo number_format((float)$producto['precio'], 2); ?> €</div>
            
            <div class="detalles">
                <p><strong>Color:</strong> <?php echo htmlspecialchars($producto['color'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Talla:</strong> <?php echo htmlspecialchars($producto['talla'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Sexo:</strong> <?php echo htmlspecialchars($producto['sexo'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <div class="colores">
                <label for="color-selector">Seleccionar color:</label>
                <select id="color-selector">
                    <?php foreach ($colores as $color): ?>
                        <option value="<?php echo htmlspecialchars($color, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $color === $producto['color'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($color, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="acciones">
                <a href="#" class="btn btn-comprar">Añadir al carrito</a>
                <a href="index.php" class="back-link">← Volver al listado</a>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const selectColor = document.getElementById('color-selector');
        const imgProducto = document.getElementById('imagen-producto');
        const precioElement = document.querySelector('.precio');
        const detallesElement = document.querySelector('.detalles');

        function actualizarProducto(color) {
            const url = 'controller/obtener_producto_color.php?id=<?php echo (int)$producto['id']; ?>&color=' + encodeURIComponent(color);

            fetch(url, { method: 'GET' })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Error HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    // Actualizar imagen
                    imgProducto.src = data.imagen;
                    imgProducto.alt = data.tipo + ' ' + data.marca + ' ' + data.color;

                    // Actualizar precio
                    precioElement.textContent = Number(data.precio).toFixed(2) + ' €';

                    // Actualizar detalles
                    detallesElement.innerHTML = '<p><strong>Color:</strong> ' + data.color + '</p>' +
                                              '<p><strong>Talla:</strong> ' + data.talla + '</p>' +
                                              '<p><strong>Sexo:</strong> ' + data.sexo + '</p>';
                })
                .catch(function(error) {
                    console.error('Error al cambiar el color:', error);
                });
        }

        selectColor.addEventListener('change', function() {
            actualizarProducto(this.value);
        });
    })();
    </script>
</body>
</html>
