<?php
require_once __DIR__ . '/clases/categoria.php';

// Llamamos al método estático 'obtenerCategorias' de la clase Categoria.
// Un método estático se llama directamente sobre la clase (con ::) sin necesidad de crear un objeto primero.
// Le pasamos $conexion de "conexion.php" para que pregunte a nuestra bd.
// El resultado (un array de objetos Categoria) lo guardamos en $categorias.
$categorias = Categoria::obtenerCategorias();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicos del Mundo - Inicio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Fuente 'Inter' de Google Fonts . -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>

<?php include_once __DIR__ . '/includes/nav.php'; ?>

<main class="contenido_principal">

    <section class="bienvenida">
        <section class="bienvenida-text">
            <span class="bienvenida-info">Información para ti</span>
            <h1>Conoce tus <span class="bienvenida-rojo">derechos laborales</span></h1>
            <p>Aquí encontrarás información clara y sencilla sobre tus derechos como trabajada en España. No importa tu situación, tienes derechos y estamos aquí para ayudarte a conocerlos.</p>
        </section>
        <section class="bienvenida-imagen">
            <img src="/assets/imagenes/trabajadoras_ilustracion.jpg" alt="Ilustración de trabajadores de diferentes sectores">
        </section>
    </section>
    <!-- Sección del encabezado de debajo del nav -->
    <section class="texto_arriba">
        <span></span>
        <h1>¿En qué podemos ayudarte hoy?</h1>
        <p>Selecciona una categoría para acceder a información oficial, guías paso a paso y recursos sobre tus derechos.</p>
    </section>

    <!-- Sección donde se muestran las tarjetas de cada categoría -->
    <section class="categorias">
        <!-- En cada vuelta del foreach, $categoria_individual contiene UN objeto Categoria.-->
        <?php foreach ($categorias as $categoria_individual) { ?>
            <!--Llamamos a getEnlace() para obtener la URL a la que debe apuntar ese enlace.
                Le paso $conexion porque puede necesitar consultar la BD para construir la URL-->
            <a href="<?php echo $categoria_individual->getEnlace(); ?>" class="tarjetas-links">
                <section class="tarjetas">

                    <!-- Sección del icono de la tarjeta -->
                    <section class="iconos">
                        <!-- getIcono() devuelve la ruta del img icono -->
                        <img src="<?php echo $categoria_individual->getIcono(); ?>" alt="icono">
                    </section>

                    <!-- getTitulo() devuelve el nombre de la categoría "Salud", "Vivienda"... -->
                    <h3><?php echo $categoria_individual->getTitulo(); ?></h3>

                    <!-- getDescripcion() devuelve el texto explicativo de la categoría -->
                    <p><?php echo $categoria_individual->getDescripcion(); ?></p>
                </section>
            </a>
        <?php } // Fin del foreach ?>
    </section>
</main>

<?php
// Para incluir el pie de página.
include_once __DIR__ . '/includes/footer.php';
?>

</body>
</html>