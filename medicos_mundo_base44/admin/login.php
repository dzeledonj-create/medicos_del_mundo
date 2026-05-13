<?php
session_start();
require_once "../clases/usuarios.php";

// Si hay usuario logueado lo mandamos al panel de admin directo
if (isset($_SESSION["usuario"])) {
    header("Location: panel_admin.php");
    exit;
}

if (isset($_POST["comprobar"])) {
    $email    = $_POST["email"];
    $password = $_POST["password"];

    $usuario = Usuario::login($email, $password);

    // Si el usuario existe, guarda su sesión y lo envía al administrador.
    // Si falla, prepara el mensaje de error para mostrar en el formulario.
    if ($usuario !== null) {
        $_SESSION["usuario"] = $usuario;
        header("Location: panel_admin.php");
        exit;
    } else {
        $mensaje_error = "Email o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicos del Mundo - Inicio</title>
    <!-- Fuente 'Inter' de Google Fonts . -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>
<!-- Fondo completo de la página, centra la tarjeta verticalmente y horizontalmente -->
<section class="pagina-login">

    <!-- Tarjeta blanca centrada con el formulario -->
    <section class="tarjeta-login">

        <!-- Cabecera de la tarjeta: título "Iniciar sesión" -->
        <section class="cabecera-login">
            <h1>Iniciar sesión</h1>
        </section>

        <!-- Cuerpo del formulario: campos de email y contraseña -->
        <section class="formulario-login">
            <form method="POST" action="login.php">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Tu email" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Tu contraseña" required>

                <input type="submit" name="comprobar" value="Entrar">

                <!-- Mensaje de error si el email o contraseña son incorrectos -->
                <?php if (isset($mensaje_error)) { ?>
                    <section class="error-login">
                        <p><?= $mensaje_error ?></p>
                    </section>
                <?php } ?>

                <!-- El botón de volver al index -->
                <p class="volver"><a href="../index.php">Volver al Inicio</a></p>

            </form>
        </section>
    </section>
</section>
</body>
</html>