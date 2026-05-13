<?php
require_once '../clases/faq.php';

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST["eliminar"])) {
    Faq::eliminar((int)$_POST["id_faq"]);
    header("Location: gestion_faqs.php");
    exit;
}

if (isset($_POST["crear"])) {
    Faq::anadir(
        $_POST["pregunta"],
        $_POST["respuesta"]
    );
    header("Location: gestion_faqs.php");
    exit;
}

$todasLasFaqs = Faq::obtenerTodas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin FAQs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>

<?php include_once '../includes/nav_admin.php'; ?>

<section class="panel-general-admin">
    <main class="gestion-contenido">

        <section class="gestion-cabecera">
            <section>
                <h1>Gestión de FAQs</h1>
                <p>Crea y administra las preguntas frecuentes del portal.</p>
            </section>
        </section>

        <section class="gestion-formulario">
            <h2>Crear FAQ</h2>
            <form method="POST">
                <section class="campo-grupo">
                    <label>Pregunta</label>
                    <input type="text" name="pregunta" placeholder="Ej: ¿Cuántos días de vacaciones tengo?" required>
                </section>

                <section class="campo-grupo margen-arriba-15px">
                    <label>Respuesta</label>
                    <textarea name="respuesta" class="caja-texto-descripcion"></textarea>
                </section>

                <button type="submit" name="crear" class="btn-primario margen-arriba-20px">
                    <i class="bi bi-save"></i> Guardar FAQ
                </button>
            </form>
        </section>

        <section class="gestion-tabla">
            <table class="tabla-admin">
                <thead>
                <tr>
                    <th>Pregunta</th>
                    <th>Respuesta</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($todasLasFaqs as $faq) { ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($faq->getPregunta()) ?></strong></td>
                        <td><?= htmlspecialchars($faq->getRespuesta()) ?></td>
                        <td>
                            <a href="editar_faqs.php?id=<?= $faq->getIdFaq() ?>" class="btn-editar">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar esta FAQ?')">
                                <input type="hidden" name="id_faq" value="<?= $faq->getIdFaq() ?>">
                                <button type="submit" name="eliminar" class="btn-eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </section>

    </main>
</section>

</body>
</html>