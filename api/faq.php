<?php
require_once __DIR__ . '/clases/Faq.php';
$faqs = Faq::obtenerTodas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicos del Mundo - FAQ</title>
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
            <span class="bienvenida-info">Centro de ayuda</span>
            <h1>Resolvemos tus <span class="bienvenida-rojo">dudas</span></h1>
            <p>Encuentra respuestas rápidas sobre tus derechos y trámites. Estamos aquí para informarte.</p>
        </section>
        <section class="bienvenida-imagen">
            <img src="/assets/imagenes/trabajadoras_ilustracion.jpg" alt="Ilustración ayuda">
        </section>
    </section>

    <section class="texto_arriba">
        <span></span>
        <h1>Preguntas Frecuentes</h1>
        <p>Haz clic en una pregunta para ver la respuesta.</p>
    </section>

    <section class="contenedor-faqs">
        <?php if (count($faqs) > 0) { ?>

            <?php foreach ($faqs as $preguntas) { ?>
                    <!-- si la respuesta es la provisional y no esta respondida no se muestra la pregunta-->
                <?php if ($preguntas->getRespuesta() != "Pendiente de respuesta por un administrador.") { ?>
                    <!-- usamos details para que no se muestre la respuesta por defecto
                    y summary para lo que sí que se muestra que es el título de lo que
                    se va a desplegar-->
                    <details class="faq-item">
                        <summary>
                            <?= htmlspecialchars($preguntas->getPregunta()) ?>
                        </summary>
                        <section class="faq-respuesta">
                            <?= htmlspecialchars($preguntas->getRespuesta()) ?>
                        </section>
                    </details>
                <?php } ?>
            <?php } ?>

        <?php } else { ?>

            <div class="sin-faqs">
                <p>Aún no se han añadido preguntas frecuentes.</p>
            </div>

        <?php } ?>
    </section>
    <a href="/admin/anadir_faq.php" class="anadir_faq">
        <i class="bi bi-chat-dots"></i> Deja tus preguntas
    </a>
</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>