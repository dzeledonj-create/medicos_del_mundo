<?php
// Cargamos la clase Usuario (que incluye BD.php internamente)
require_once '../clases/usuarios.php';

// Iniciamos sesión para comprobar si hay alguien logueado
session_start();

// Si no hay usuario logueado lo mandamos al login
if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

// Solo la Administradora (id_rol = 1) puede acceder a la gestión de usuarios
if ($_SESSION["usuario"]->getIdRol() != 1) {
    header("Location: panel_admin.php");
    exit;
}

// Si se envió el formulario de eliminar
if (isset($_POST["eliminar"])) {
    $id = (int)$_POST["id_usuario"]; // (int) para asegurarnos de que es un número
    Usuario::eliminar($id);
    header("Location: gestion_usuarias.php");
    exit;
}

// Si se envió el formulario de crear
if (isset($_POST["crear"])) {
    Usuario::crear($_POST["nombre"], $_POST["email"], $_POST["password"], (int)$_POST["id_rol"]);
    header("Location: gestion_usuarias.php");
    exit;
}

// Cargamos todos los usuarios para mostrarlos en la tabla
$usuarios = Usuario::obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>

<?php include_once '../includes/nav_admin.php'; ?>

<section class="panel-general-admin">
    <main class="gestion-contenido">

        <!-- Cabecera con título -->
        <section class="gestion-cabecera">
            <div>
                <h1>Gestión de usuarios</h1>
                <p class="panel-subtitulo">Administra los usuarios del sistema</p>
            </div>
        </section>

        <!-- Formulario para añadir un nuevo usuario, siempre visible -->
        <section class="gestion-formulario">
            <h2>Añadir usuario</h2>
            <!--
                method="POST" envía los datos de forma segura.
                action="admin_usuarios.php" envía al mismo archivo para procesarlo arriba.
            -->
            <form method="POST" action="gestion_usuarias.php">
                <section class="formulario-fila">
                    <section class="campo-grupo">
                        <label>Nombre</label>
                        <input type="text" name="nombre" placeholder="Nombre" required>
                    </section>
                    <section class="campo-grupo">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Email" required>
                    </section>
                    <section class="campo-grupo">
                        <label>Contraseña</label>
                        <input type="password" name="password" placeholder="Contraseña" required>
                    </section>
                    <section class="campo-grupo">
                        <label>Rol</label>
                        <!-- value="1" = Administrador, value="2" = Usuario (según tabla rol en BD) -->
                        <select name="id_rol">
                            <option value="1">Administrador</option>
                            <option value="2">Usuario</option>
                        </select>
                    </section>
                </section>
                <!--
                    name="crear" es lo que detecta el if(isset($_POST["crear"])) de arriba.
                    Sin ese name, el PHP no sabría que se pulsó este botón.
                -->
                <button type="submit" name="crear" class="btn-primario">
                    <i class="bi bi-check-lg"></i> Guardar usuario
                </button>
            </form>
        </section>

        <!-- Tabla con todos los usuarios de la BD -->
        <section class="gestion-tabla">
            <table class="tabla-admin">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <!-- Recorremos el array $usuarios, cada $usuario es un objeto Usuario -->
                <?php foreach ($usuarios as $usuario) { ?>
                    <tr>
                        <td><?= $usuario->getIdUsuario() ?></td>
                        <td><?= $usuario->getNombre() ?></td>
                        <td><?= $usuario->getEmail() ?></td>
                        <!-- Si id_rol es 1 mostramos "Administrador", si no "Usuario" -->
                        <td><?= $usuario->getIdRol() == 1 ? 'Administrador' : 'Usuario' ?></td>
                        <td>
                            <a href="editar_usuaria.php?id=<?= $usuario->getIdUsuario() ?>" class="btn-editar">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <!--
                                Cada fila tiene su propio formulario de eliminar.
                                El input hidden manda el ID del usuario a PHP sin mostrarlo.
                                name="eliminar" es lo que detecta el if(isset($_POST["eliminar"])).
                                onclick="return confirm(...)" pide confirmación antes de eliminar.
                            -->
                            <form method="POST" action="gestion_usuarias.php">
                                <input type="hidden" name="id_usuario" value="<?= $usuario->getIdUsuario() ?>">
                                <!-- onlick return para que pide confirmación-->
                                <button type="submit" name="eliminar" class="btn-eliminar"
                                        onclick="return confirm('¿Seguro que quieres eliminar a <?= $usuario->getNombre() ?>?')">
                                    <i class="bi bi-trash"></i> Eliminar
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