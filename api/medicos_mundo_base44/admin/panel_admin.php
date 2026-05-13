<?php
require_once '../clases/usuarios.php';
session_start();

// Si no hay usuario logueado lo mandamos al login
if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

$usuario = $_SESSION["usuario"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>

<?php include_once '../includes/nav_admin.php'; ?>

<section class="panel-general-admin">

    <main class="panel-contenido">
        <h1>Bienvenido, <?= $usuario->getNombre() ?></h1>
        <p class="panel-subtitulo">¿Qué quieres gestionar hoy?</p>

        <section class="panel-accesos">
            <a href="gestion_categorias.php" class="panel-acceso">
                <i class="bi bi-grid"></i>
                <span>Categorías</span>
            </a>
            <?php if ($usuario->getIdRol() == 1) { ?>
                <a href="gestion_usuarias.php" class="panel-acceso">
                    <i class="bi bi-people"></i>
                    <span>Usuarios</span>
                </a>
            <?php } ?>
            <a href="gestion_contenido.php" class="panel-acceso">
                <i class="bi bi-file-text"></i>
                <span>Contenido</span>
            </a>
            <a href="gestion_faqs.php" class="panel-acceso">
                <i class="bi bi-question-circle"></i>
                <span>FAQs</span>
            </a>
        </section>
    </main>

</section>

</body>
</html>