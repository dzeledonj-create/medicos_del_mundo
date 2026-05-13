<?php
require_once __DIR__ . '/clases/bloque.php';
require_once __DIR__ . '/clases/categoria.php';

// Obtención del ID de categoría de forma segura
$id_categoria = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// La función obtenerPorCategoria ahora realiza el JOIN con la tabla 'contenido'
$bloques = Bloque::obtenerPorCategoria($id_categoria);
// Obtenemos el objeto de la categoría actual
$categoria_actual = Categoria::obtenerPorId($id_categoria);
//Extraemos el título (con un fallback por si no existe)
$titulo_categoria = $categoria_actual ? $categoria_actual->getTitulo() : 'esta categoría';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenido - Médicos del Mundo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/estilos.css">
</head>
<body>

<?php include_once __DIR__ . '/includes/nav.php'; ?>

<main class="contenido_principal">
    <div style="
    position: relative;
    z-index: 999;
    padding-top: 80px;
    margin-bottom: -60px;
    text-align: right;
    ">
        <button onclick="history.back()" style="
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: none;
    border: 2px solid var(--primary-dk);
    color: var(--primary-dk);
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
" onmouseover="this.style.background='var(--primary-dk)'; this.style.color='#fff'"
   onmouseout="this.style.background='none'; this.style.color='var(--primary-dk)'">
    <i class="bi bi-arrow-left" style="color: inherit; font-size: 1rem;"></i> Volver
</button>
    </div>
    <section class="bienvenida">
        <section class="bienvenida-text">
            <span class="bienvenida-info">C</span>
            <h1>Conoce los detalles de <span class="bienvenida-rojo"><?= htmlspecialchars($titulo_categoria) ?></span></h1>
            <p>Consulta a continuación la guía detallada paso a paso sobre esta categoría. Cada punto te ofrece información clave y recursos visuales.</p>
        </section>
        <section class="bienvenida-imagen">
            <img src="/assets/imagenes/trabajadoras_ilustracion.jpg" alt="Ilustración general">
        </section>
    </section>

    <section class="bloques-container">
        <?php if (count($bloques) > 0): ?>
            <?php $numero = 1; ?>
            <?php foreach ($bloques as $bloque): ?>

                <section class="bienvenida">
                    <section class="bienvenida-text">
                        <span class="bienvenida-info">Punto <?= $numero ?></span>

                        <h1><?= htmlspecialchars($bloque->getTitulo()) ?></h1>

                        <?php if ($bloque->getSubtitulo()): ?>
                            <p class="bienvenida-rojo" style="font-weight: 700; font-size: 1.2rem; margin-bottom: 5px;">
                                <?= htmlspecialchars($bloque->getSubtitulo()) ?>
                            </p>
                        <?php endif; ?>

                        <p><?= nl2br(htmlspecialchars($bloque->getContenido())) ?></p>
                    </section>

                    <section class="bienvenida-imagen">
                        <img src="<?= htmlspecialchars($bloque->getUrlImagen()) ?>"
                             alt="<?= htmlspecialchars($bloque->getTitulo()) ?>">
                    </section>
                </section>

                <?php $numero++; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <section class="bienvenida" style="justify-content: center;">
                <p class="sin-contenido">Aún no hay información detallada en esta categoría.</p>
            </section>
        <?php endif; ?>
    </section>

</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>