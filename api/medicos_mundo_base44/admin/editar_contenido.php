<?php
require_once '../clases/bloque.php';
require_once '../clases/categoria.php';

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: gestion_contenido.php");
    exit;
}

$id_bloque = (int)$_GET['id'];

if (isset($_POST["actualizar"])) {

    // Si el usuario sube una imagen nueva la procesamos, si no, conservamos la que ya tenía
    if ($_FILES["icono"]["error"] === UPLOAD_ERR_OK) {
        $nombre_archivo = basename($_FILES["icono"]["name"]);
        move_uploaded_file($_FILES["icono"]["tmp_name"], "../assets/imagenes/icono_contenido/" . $nombre_archivo);
        $ruta_icono = "assets/imagenes/icono_contenido/" . $nombre_archivo;
    } else {
        // No subió imagen nueva, mantenemos la ruta que ya tenía en la BD
        $ruta_icono = $_POST["icono_actual"];
    }

    Bloque::actualizar(
        $id_bloque,
        $_POST["titulo"],
        $_POST["subtitulo"],
        $_POST["contenido"],
        (int)$_POST["orden"],
        (int)$_POST["id_categoria"],
        $ruta_icono
    );
    header("Location: gestion_contenido.php");
    exit;
}

// Cargamos los datos actuales del bloque para rellenar el formulario
$bloque_editar = Bloque::obtenerPorId($id_bloque);
// Necesitamos todas las categorías para el select
$todasLasCategorias = Categoria::obtenerTodasAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Bloque</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>

<?php include_once '../includes/nav_admin.php'; ?>

<section class="panel-general-admin">
    <main class="gestion-contenido">

        <section class="gestion-cabecera">
            <section>
                <h1>Editar Bloque</h1>
                <p>Modifica los datos del bloque seleccionado.</p>
            </section>
            <a href="gestion_contenido.php" class="btn-eliminar">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </section>

        <section class="gestion-formulario">
            <h2>Datos del bloque</h2>
            <form method="POST" enctype="multipart/form-data">
                <section class="formulario-fila">
                    <section class="campo-grupo">
                        <label>Título</label>
                        <input type="text" name="titulo" value="<?= htmlspecialchars($bloque_editar->getTitulo()) ?>" required>
                    </section>

                    <section class="campo-grupo">
                        <label>Subtítulo</label>
                        <input type="text" name="subtitulo" value="<?= htmlspecialchars($bloque_editar->getSubtitulo()) ?>">
                    </section>

                    <section class="campo-grupo">
                        <label>Categoría</label>
                        <select name="id_categoria">
                            <?php foreach ($todasLasCategorias as $cat) { ?>
                                <option value="<?= $cat->getIdCategoria() ?>"
                                    <?php if ($bloque_editar->getIdCategoria() == $cat->getIdCategoria()) { echo 'selected'; } ?>>
                                    <?php if ($cat->getIdMadre() == null) { ?>
                                        ▶ <?= htmlspecialchars($cat->getTitulo()) ?>
                                    <?php } else { ?>
                                        -- <?= htmlspecialchars($cat->getTitulo()) ?>
                                    <?php } ?>
                                </option>
                            <?php } ?>
                        </select>
                    </section>

                    <section class="campo-grupo">
                        <label>Orden</label>
                        <input type="number" name="orden" value="<?= $bloque_editar->getOrden() ?>">
                    </section>
                </section>

                <section class="campo-grupo margen-arriba-15px">
                    <label>Imagen (Ruta)</label>
                    <!-- Guardamos la ruta actual en un campo oculto para recuperarla si no se sube imagen nueva -->
                    <input type="hidden" name="icono_actual" value="<?= htmlspecialchars($bloque_editar->getUrlImagen()) ?>">
                    <img src="../<?= htmlspecialchars($bloque_editar->getUrlImagen()) ?>" class="icono-pequeno-tabla" alt="imagen actual">
                    <input type="file" name="icono" accept="image/*">
                </section>

                <section class="campo-grupo margen-arriba-15px">
                    <label>Contenido</label>
                    <textarea name="contenido" class="caja-texto-descripcion"><?= htmlspecialchars($bloque_editar->getContenido()) ?></textarea>
                </section>

                <button type="submit" name="actualizar" class="btn-primario margen-arriba-20px">
                    <i class="bi bi-save"></i> Actualizar Bloque
                </button>
            </form>
        </section>
    </main>
</section>
</body>
</html>