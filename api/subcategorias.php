<?php
include_once __DIR__ . '/includes/nav.php';
include_once __DIR__ . '/clases/categoria.php';
// Obtenemos el ID de la categoría madre desde la URL
if (isset($_GET['id'])) {
    $id_madre = (int)$_GET['id'];
} else {
    $id_madre = 0;
}

$subcategorias = Categoria::obtenerHijas($id_madre);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicos del Mundo - Inicio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/estilos.css">
</head>
<body>

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
    <section class="texto_arriba">
        <span></span>
        <h1>¿En qué podemos ayudarte hoy?</h1>
        <p>Selecciona una categoría para acceder a información oficial, guías paso a paso y recursos sobre tus derechos.</p>
    </section>

    <section class="categorias">
        <?php foreach ($subcategorias as $cat_individual) { ?>
            <a href="<?php echo $cat_individual->getEnlace(); ?>" class="tarjetas-links">
                <section class="tarjetas">
                    <section class="iconos">
                        <img src="<?php echo $cat_individual->getIcono(); ?>" alt="icono">
                    </section>
                    <h3><?php echo $cat_individual->getTitulo(); ?></h3>
                    <p><?php echo $cat_individual->getDescripcion(); ?></p>
                </section>
            </a>
        <?php } ?>

    </section>
</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
