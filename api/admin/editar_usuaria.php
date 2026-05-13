<?php
require_once '../clases/usuarios.php';
session_start();

//si no está logueado en $_SESSION échalo al login
if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}
// Solo la Administradora (id_rol = 1) puede acceder a la gestión de usuarios
if ($_SESSION["usuario"]->getIdRol() != 1) {
    header("Location: panel_admin.php");
    exit;
}

// 1. Recogemos el ID de la URL (ej: editar_usuario.php?id=3)
if (!isset($_GET['id'])) {
    header("Location: gestion_usuarias.php");
    exit;
}
$id = (int)$_GET['id'];

// 2. Buscamos al usuario en la BD para rellenar los datos
$usuario_editar = Usuario::obtenerPorId($id);

// Si alguien pone un ID falso en la URL, lo devolvemos
if ($usuario_editar == null) {
    header("Location: gestion_usuarias.php");
    exit;
}

// 3. Si se pulsó el botón de "Actualizar"
if (isset($_POST["actualizar"])) {
    Usuario::editar($id, $_POST["nombre"], $_POST["email"], $_POST["password"], (int)$_POST["id_rol"]);
    header("Location: gestion_usuarias.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>

<?php include_once '../includes/nav_admin.php'; ?>

<section class="panel-general-admin">
    <main class="gestion-contenido">
        <!-- título y nombre de quien editas-->
        <section class="gestion-cabecera">
            <section>
                <h1>Editar usuario</h1>
                <p class="panel-subtitulo">Modificando a: <?= htmlspecialchars($usuario_editar->getNombre()) ?></p>
            </section>
            <a href="gestion_categorias.php" class="btn-eliminar">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </section>

        <section class="gestion-formulario">
            <form method="POST" action="editar_usuaria.php?id=<?= $id ?>">
                <!-- inputs con los datos rellenos-->
                <section class="formulario-fila">
                    <section class="campo-grupo">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($usuario_editar->getNombre()) ?>" required>
                    </section>
                    <section class="campo-grupo">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($usuario_editar->getEmail()) ?>" required>
                    </section>
                    <section class="campo-grupo">
                        <label>Contraseña</label>
                        <input type="text" name="password" value="<?= htmlspecialchars($usuario_editar->getPassword()) ?>" required>
                    </section>
                    <section class="campo-grupo">
                        <label>Rol</label>
                        <!--if/else para que ponga seleccionado el rol que ya tiene para no modificarlo sin querer.
                        Se usa selected para que <select> del html detecte cuál es el seleccionado -->

                        <select name="id_rol">
                            <option value="1" <?php if ($usuario_editar->getIdRol() == 1) { echo 'selected'; } ?>>Administrador</option>
                            <option value="2" <?php if ($usuario_editar->getIdRol() == 2) { echo 'selected'; } ?>>Usuario</option>
                        </select>
                    </section>
                </section>
                <!-- los botones de actualizar y cancelar-->
                <section>
                    <button type="submit" name="actualizar" class="btn-primario">
                        <i class="bi bi-save"></i> Actualizar usuario
                    </button>
                </section>
            </form>
        </section>

    </main>
</section>

</body>
</html>
