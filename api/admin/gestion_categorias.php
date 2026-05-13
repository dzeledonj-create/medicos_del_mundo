<?php
require_once __DIR__ . '/../clases/categoria.php';

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: /admin/login.php");
    exit;
}

if (isset($_POST["eliminar"])) {
    Categoria::eliminar((int)$_POST["id_categoria"]);
    header("Location: /admin/gestion_categorias.php");
    exit;
}

if (isset($_POST["crear"])) {
    $id_madre = (int)$_POST["id_madre"] === 0 ? null : (int)$_POST["id_madre"];

    // comprobamos el archivo (imagen) subido
    $ruta_icono = "";
    if ($_FILES["icono"]["error"] === UPLOAD_ERR_OK) {
        $nombre_archivo = basename($_FILES["icono"]["name"]);
        move_uploaded_file($_FILES["icono"]["tmp_name"], __DIR__ . "/../assets/imagenes/icono_categoria/" . $nombre_archivo);
        $ruta_icono = "assets/imagenes/icono_categoria/" . $nombre_archivo;
    }

    Categoria::crear(
            $_POST["titulo"],
            $_POST["descripcion"],
            $ruta_icono,
            $id_madre,
            (int)$_POST["orden"]
    );
    header("Location: /admin/gestion_categorias.php");
    exit;
}

$todasLasCategorias = Categoria::obtenerTodasAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Categorías</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/estilos.css">
</head>
<body>

<?php include_once __DIR__ . '/includes/nav_admin.php'; ?>

<section class="panel-general-admin">
    <main class="gestion-contenido">

        <section class="gestion-cabecera">
            <section>
                <h1>Gestión de Categorías</h1>
                <p>Configura las secciones principales y subcategorías del portal.</p>
            </section>
        </section>

        <section class="gestion-formulario">
            <h2>Crear Categoría</h2>
            <!-- enctype para permitir recibir archivos -->
            <form method="POST" enctype="multipart/form-data">
                <section class="formulario-fila">
                    <section class="campo-grupo">
                        <label>Título</label>
                        <input type="text" name="titulo" placeholder="Ej: Vivienda" required>
                    </section>

                    <!-- para meter la imagen que adjunte en vez de meter ruta -->
                    <section class="campo-grupo">
                        <label>Icono</label>
                        <input type="file" name="icono" accept="image/*">
                    </section>

                    <section class="campo-grupo">
                        <label>Pertenece a:</label>
                        <select name="id_madre">
                            <option value="0"> ◀ Es Categoría Principal ▶ </option>
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
                    <label>Descripción</label>
                    <textarea name="descripcion" class="caja-texto-descripcion"></textarea>
                </section>

                <button type="submit" name="crear" class="btn-primario margen-arriba-20px">
                    <i class="bi bi-save"></i> Guardar Categoría
                </button>
            </form>
        </section>

        <section class="gestion-tabla">
            <table class="tabla-admin">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Icono</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Orden</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($todasLasCategorias as $categoria) { ?>
                    <tr>
                        <td><?= $categoria->getIdCategoria() ?></td>

                        <td>
                            <img src="/<?= $categoria->getIcono() ?>" class="icono-pequeno-tabla" alt="">
                        </td>

                        <td><strong><?= htmlspecialchars($categoria->getTitulo()) ?></strong></td>

                        <td>
                            <?php if ($categoria->getIdMadre()) { ?>
                                <span class="texto-gris-subcategoria">
                                    Subcategoría (ID Madre: <?= $categoria->getIdMadre() ?>)
                                </span>
                            <?php } else { ?>
                                <span class="texto-morado-principal">
                                    Principal
                                </span>
                            <?php } ?>
                        </td>

                        <td><?= $categoria->getOrden() ?></td>

                        <td>
                            <a href="editar_categoria.php?id=<?= $categoria->getIdCategoria() ?>" class="btn-editar">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar esta categoría?')">
                                <input type="hidden" name="id_categoria" value="<?= $categoria->getIdCategoria() ?>">
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