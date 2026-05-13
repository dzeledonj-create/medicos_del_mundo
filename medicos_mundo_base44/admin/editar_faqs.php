<?php
require_once '../clases/faq.php';

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: gestion_faqs.php");
    exit;
}

$id_faq = (int)$_GET['id'];

if (isset($_POST["actualizar"])) {
    Faq::actualizar(
        $id_faq,
        $_POST["pregunta"],
        $_POST["respuesta"]
    );
    header("Location: gestion_faqs.php");
    exit;
}

// Cargamos los datos de la FAQ para rellenar el formulario
$faq_editar = Faq::obtenerPorId($id_faq);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar FAQ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>

<?php include_once '../includes/nav_admin.php'; ?>

<section class="panel-general-admin">
    <main class="gestion-contenido">

        <section class="gestion-cabecera">
            <section>
                <h1>Editar FAQ</h1>
                <p>Modifica los datos de la pregunta seleccionada.</p>
            </section>
            <a href="gestion_faqs.php" class="btn-eliminar">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </section>

        <section class="gestion-formulario">
            <h2>Datos de la FAQ</h2>
            <form method="POST">
                <section class="campo-grupo">
                    <label>Pregunta</label>
                    <input type="text" name="pregunta" value="<?= htmlspecialchars($faq_editar->getPregunta()) ?>" required>
                </section>

                <section class="campo-grupo margen-arriba-15px">
                    <label>Respuesta</label>
                    <textarea name="respuesta" class="caja-texto-descripcion"><?= htmlspecialchars($faq_editar->getRespuesta()) ?></textarea>
                </section>

                <button type="submit" name="actualizar" class="btn-primario margen-arriba-20px">
                    <i class="bi bi-save"></i> Actualizar FAQ
                </button>
            </form>
        </section>

    </main>
</section>

</body>
</html>