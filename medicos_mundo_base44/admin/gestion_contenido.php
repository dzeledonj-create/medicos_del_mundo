<?php
require_once '../clases/bloque.php';
require_once '../clases/categoria.php';

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST["eliminar"])) {
    Bloque::eliminar((int)$_POST["id_bloque"]);
    header("Location: gestion_contenido.php");
    exit;
}

if (isset($_POST["crear"])) {

    // comprobamos el archivo (imagen) subido
    $ruta_icono = "";
    if ($_FILES["icono"]["error"] === UPLOAD_ERR_OK) {
        $nombre_archivo = basename($_FILES["icono"]["name"]);
        move_uploaded_file($_FILES["icono"]["tmp_name"], "../assets/imagenes/icono_contenido/" . $nombre_archivo);
        $ruta_icono = "assets/imagenes/icono_contenido/" . $nombre_archivo;
    }

    Bloque::crear(
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

// Necesitamos todos los bloques para la tabla y todas las categorías para el select
$todosLosBloques    = Bloque::obtenerTodosAdmin();
$todasLasCategorias = Categoria::obtenerTodasAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Contenido</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>

<?php include_once '../includes/nav_admin.php'; ?>

<section class="panel-general-admin">
    <main class="gestion-contenido">

        <section class="gestion-cabecera">
            <section>
                <h1>Gestión de Contenido</h1>
                <p>Crea y administra los bloques de información del portal.</p>
            </section>
        </section>

        <section class="gestion-formulario">
            <h2>Crear Bloque</h2>
            <form method="POST" enctype="multipart/form-data">
                <section class="formulario-fila">
                    <section class="campo-grupo">
                        <label>Título</label>
                        <input type="text" name="titulo" placeholder="Ej: Definición" required>
                    </section>

                    <section class="campo-grupo">
                        <label>Subtítulo</label>
                        <input type="text" name="subtitulo" placeholder="Ej: Trabajo Efectivo">
                    </section>

                    <section class="campo-grupo">
                        <label>Categoría</label>
                        <select name="id_categoria">
                            <?php foreach ($todasLasCategorias as $cat) { ?>
                                <option value="<?= $cat->getIdCategoria() ?>">
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
                        <input type="number" name="orden" value="1">
                    </section>
                </section>

                <section class="campo-grupo margen-arriba-15px">
                    <label>Imagen (Ruta)</label>
                    <input type="file" name="icono" accept="image/*">
                </section>

                <section class="campo-grupo margen-arriba-15px">
                    <label>Contenido</label>
                    <textarea name="contenido" class="caja-texto-descripcion"></textarea>
                </section>

                <button type="submit" name="crear" class="btn-primario margen-arriba-20px">
                    <i class="bi bi-save"></i> Guardar Bloque
                </button>
            </form>
        </section>

        <section class="gestion-tabla">
            <table class="tabla-admin">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Título</th>
                    <th>Subtítulo</th>
                    <th>Categoría</th>
                    <th>Orden</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($todosLosBloques as $bloque) { ?>
                    <tr>
                        <td><?= $bloque->getIdBloque() ?></td>

                        <td>
                            <img src="../<?= htmlspecialchars($bloque->getUrlImagen()) ?>" class="icono-pequeno-tabla" alt="icono de cada contenido">
                        </td>

                        <td><strong><?= htmlspecialchars($bloque->getTitulo()) ?></strong></td>
                        <td><?= htmlspecialchars($bloque->getSubtitulo()) ?></td>
                        <td>
                            <span class="texto-gris-subcategoria">
                                ID: <?= $bloque->getIdCategoria() ?>
                            </span>
                        </td>
                        <td><?= $bloque->getOrden() ?></td>
                        <td>
                            <a href="editar_contenido.php?id=<?= $bloque->getIdBloque() ?>" class="btn-editar">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este bloque?')">
                                <input type="hidden" name="id_bloque" value="<?= $bloque->getIdBloque() ?>">
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