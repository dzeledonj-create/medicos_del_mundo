<?php
require_once __DIR__ . '/../clases/categoria.php';

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

// Si alguien entra a esta página sin un ID en la URL, lo echamos de vuelta a la tabla
if (!isset($_GET['id'])) {
    header("Location: gestion_categorias.php");
    exit;
}

// Guardamos el ID que viene por la URL
$id_categoria = (int)$_GET['id'];

// Procesar el formulario cuando el usuario hace clic en "Actualizar"
if (isset($_POST["actualizar"])) {
    $id_madre = (int)$_POST["id_madre"] === 0 ? null : (int)$_POST["id_madre"];

    // Si el usuario sube una imagen nueva la procesamos, si no, conservamos la que ya tenía
    if ($_FILES["icono"]["error"] === UPLOAD_ERR_OK) {
        $nombre_archivo = basename($_FILES["icono"]["name"]);
        move_uploaded_file($_FILES["icono"]["tmp_name"], __DIR__ . "/../assets/imagenes/icono_categoria/" . $nombre_archivo);
        $ruta_icono = "/assets/imagenes/icono_categoria/" . $nombre_archivo;
    } else {
        // No subió imagen nueva, mantenemos la ruta que ya tenía en la BD
        $ruta_icono = $_POST["icono_actual"];
    }

    Categoria::actualizar(
        $id_categoria,
        $_POST["titulo"],
        $_POST["descripcion"],
        $ruta_icono,
        $id_madre,
        (int)$_POST["orden"]
    );

    // Volvemos a la tabla después de actualizar
    header("Location: gestion_categorias.php");
    exit;
}

// Obtenemos los datos de la categoría actual para rellenar los huecos del formulario
$categoria_editar = Categoria::obtenerPorId($id_categoria);
// Necesitamos TODAS las categorías (principales y subcategorías) porque ahora
// cualquier categoría puede ser madre de otra, no solo las principales.
// Antes usábamos obtenerPrincipales() que filtra WHERE id_madre IS NULL,
// pero eso solo mostraba el primer nivel y no permitía hacer sub-subcategorías.
$todasLasCategorias = Categoria::obtenerTodasAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoría</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/../assets/estilos.css">
</head>
<body>

<?php include_once __DIR__ . '/admin/../includes/nav_admin.php'; ?>

<section class="panel-general-admin">
    <main class="gestion-contenido">

        <section class="gestion-cabecera">
            <section>
                <h1>Editar Categoría</h1>
                <p>Modifica los datos de la categoría seleccionada.</p>
            </section>
            <a href="gestion_categorias.php" class="btn-eliminar">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </section>

        <section class="gestion-formulario">
            <h2>Datos de la categoría</h2>
            <form method="POST" enctype="multipart/form-data">
                <section class="formulario-fila">
                    <section class="campo-grupo">
                        <label>Título</label>
                        <input type="text" name="titulo" value="<?= htmlspecialchars($categoria_editar->getTitulo()) ?>" required>
                    </section>

                    <section class="campo-grupo">
                        <label>Icono</label>
                        <!-- Guardamos la ruta actual en un campo oculto para recuperarla si no se sube imagen nueva -->
                        <input type="hidden" name="icono_actual" value="<?= htmlspecialchars($categoria_editar->getIcono()) ?>">
                        <img src="/../<?= htmlspecialchars($categoria_editar->getIcono()) ?>" class="icono-pequeno-tabla" alt="icono actual">
                        <input type="file" name="icono" accept="image/*">
                    </section>

                    <section class="campo-grupo">
                        <label>Pertenece a:</label>
                        <select name="id_madre">
                            <option value="0" <?php if ($categoria_editar->getIdMadre() == null) { echo 'selected'; } ?>>
                                ◀ Es Categoría Principal ▶
                            </option>

                            <?php foreach ($todasLasCategorias as $cat) { ?>
                                <option value="<?= $cat->getIdCategoria() ?>"
                                        <?php
                                        // Comparamos la madre de la categoría que estamos editando con el ID de cada opción.
                                        // Si coinciden, añadimos 'selected' para que el select muestre la madre correcta al cargar.
                                        if ($categoria_editar->getIdMadre() == $cat->getIdCategoria()) { echo 'selected'; }
                                        ?>>
                                    <?php if ($cat->getIdMadre() == null) { ?>
                                        ▶  <?= htmlspecialchars($cat->getTitulo()) ?>
                                    <?php } else { ?>
                                        <!-- Si la categoría del bucle ya es ella misma una subcategoría, le ponemos "--" delante -->
                                        <!-- así el admin puede distinguir visualmente qué es principal y qué no -->
                                        -- <?= htmlspecialchars($cat->getTitulo()) ?>
                                    <?php } ?>
                                </option>
                            <?php } ?>
                        </select>
                    </section>

                    <section class="campo-grupo">
                        <label>Orden</label>
                        <input type="number" name="orden" value="<?= $categoria_editar->getOrden() ?>">
                    </section>
                </section>

                <section class="campo-grupo margen-arriba-15px">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="caja-texto-descripcion"><?= htmlspecialchars($categoria_editar->getDescripcion()) ?></textarea>
                </section>

                <button type="submit" name="actualizar" class="btn-primario margen-arriba-20px">
                    <i class="bi bi-save"></i> Actualizar Categoría
                </button>
            </form>
        </section>

    </main>
</section>

</body>
</html>
