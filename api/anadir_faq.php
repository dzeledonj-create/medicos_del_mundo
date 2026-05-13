<?php
require_once __DIR__ . '/clases/Faq.php';

// Si se ha pulsado el botón de guardar
if (isset($_POST['guardar'])) {
    $pregunta = $_POST['pregunta'];
    // Guardamos un texto de respuesta para que el admin lo modifique
    //porque tiene NOT NULL en respuesta en BD.
    $respuesta_provisional = "Pendiente de respuesta por un administrador.";

    if (!empty($pregunta)) {
        Faq::anadir($pregunta, $respuesta_provisional);
        // Al terminar, volvemos a la página principal de FAQs
        header("Location: faq.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugerir FAQ - Médicos del Mundo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/estilos.css">
</head>
<body>

<?php include_once __DIR__ . '/includes/nav.php'; ?>

<main class="contenido_principal">

    <section class="texto_arriba">
        <span></span>
        <h1>Nueva Pregunta</h1>
        <p>Introduce tu duda. Un administrador la revisará y publicará la respuesta para ayudar a la comunidad.</p>
    </section>

    <section class="contenedor-faqs">
        <div class="faq-item">
            <form method="POST" class="formulario-faq">
                <div>
                    <label>Escribe tu pregunta:</label>
                    <input type="text" name="pregunta" placeholder="Ej: ¿Qué es el finiquito?" required>
                </div>

                <button type="submit" name="guardar" class="btn-guardar">
                    Enviar Pregunta
                </button>
                <a href="faq.php" class="enlace-cancelar">Cancelar</a>
            </form>
        </div>
    </section>

</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>