<?php
require_once '../clases/BD.php';

/**
 * Clase Usuario.
 * Representa un usuario del sistema y gestiona el login y su administración.
 */
class Usuario {

    private $id_usuario;
    private $email_usuario;
    private $password;
    private $nombre;
    private $id_rol;

    public function __construct($id_usuario, $email_usuario, $password, $nombre, $id_rol) {
        $this->id_usuario    = $id_usuario;
        $this->email_usuario = $email_usuario;
        $this->password      = $password;
        $this->nombre        = $nombre;
        $this->id_rol        = $id_rol;
    }

    // ── Getters ──────────────────────────────────────────────────────────────

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getEmail() {
        return $this->email_usuario;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getIdRol() {
        return $this->id_rol;
    }

    // Necesario para rellenar el formulario de edición en el panel admin
    public function getPassword() {
        return $this->password;
    }


    // ── Métodos para las páginas PÚBLICAS (login.php) ─────────────────────────

    /**
     * Busca un usuario por email y comprueba la contraseña.
     * Si todo es correcto inicia la sesión y devuelve el objeto Usuario.
     * Si el email no existe o la contraseña no coincide, devuelve null.
     * Se usa en login.php al enviar el formulario.
     */
    public static function login($email, $password):Usuario|null {
        $conexion = BD::obtenerConexion();
        $query = "SELECT * FROM usuarios WHERE email_usuario = :email";
        $stmt  = $conexion->prepare($query);
        $stmt->execute(['email' => $email]);

        // fetch() sin bucle porque solo puede existir un usuario con ese email
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no encontró ningún usuario con ese email, devolvemos null
        if ($fila == false) {
            return null;
        }

        // Comparamos la contraseña escrita con la guardada en la BD.
        if ($password != $fila['password']) {
            return null;
        }

        // Email y contraseña correctos: creamos el objeto y guardamos en sesión
        $usuario = new Usuario(
            $fila['id_usuario'],
            $fila['email_usuario'],
            $fila['password'],
            $fila['nombre'],
            $fila['id_rol']
        );
        // $_SESSION es un array que PHP mantiene entre páginas para ese usuario.
        // primero se llama, luego se puede usar
        session_start();
        $_SESSION['usuario'] = $usuario;

        return $usuario;
    }


    // ── Métodos para el PANEL DE ADMIN ────────────────────────────────────────

    /**
     * Devuelve todos los usuarios de la BD ordenados por ID.
     * Se usa en admin_usuarios.php para mostrar la tabla de usuarios.
     */
    public static function obtenerTodos(): array {
        $conexion = BD::obtenerConexion();
        $stmt = $conexion->prepare("SELECT * FROM usuarios ORDER BY id_usuario ASC");
        $stmt->execute();
        $usuarios = [];
        // while porque son muchas filas, fetch recoge filas y assoc accede a los datos por columna
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario(
                $fila['id_usuario'],
                $fila['email_usuario'],
                $fila['password'],
                $fila['nombre'],
                $fila['id_rol']
            );
        }
        return $usuarios;
    }

    /**
     * Devuelve un único usuario buscando por su ID.
     * Se usa en editar_usuario.php para rellenar el formulario con sus datos actuales.
     */
    public static function obtenerPorId(int $id) {
        $conexion = BD::obtenerConexion();
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
        $stmt->execute(['id' => $id]);
        // fetch recoge filas y assoc accede a los datos por columna
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fila == false) {
            return null;
        }
        return new Usuario(
            $fila['id_usuario'],
            $fila['email_usuario'],
            $fila['password'],
            $fila['nombre'],
            $fila['id_rol']
        );
    }

    /**
     * Crea un nuevo usuario en la BD.
     * Se usa en admin_usuarios.php al enviar el formulario de crear usuario.
     */
    public static function crear(string $nombre, string $email, string $password, int $id_rol): void {
        $conexion = BD::obtenerConexion();
        $stmt = $conexion->prepare(
            "INSERT INTO usuarios (nombre, email_usuario, password, id_rol) VALUES (:nombre, :email, :password, :id_rol)"
        );
        $stmt->execute([
            'nombre'   => $nombre,
            'email'    => $email,
            'password' => $password,
            'id_rol'   => $id_rol
        ]);
    }

    /**
     * Actualiza los datos de un usuario existente.
     * Se usa en editar_usuario.php al enviar el formulario de editar.
     */
    public static function editar(int $id, string $nombre, string $email, string $password, int $id_rol): void {
        $conexion = BD::obtenerConexion();
        $stmt = $conexion->prepare(
            "UPDATE usuarios SET nombre = :nombre, email_usuario = :email, password = :password, id_rol = :id_rol WHERE id_usuario = :id"
        );
        $stmt->execute([
            'nombre'   => $nombre,
            'email'    => $email,
            'password' => $password,
            'id_rol'   => $id_rol,
            'id'       => $id
        ]);
    }

    /**
     * Elimina un usuario por su ID.
     * Se usa en admin_usuarios.php al pulsar el botón de eliminar.
     */
    public static function eliminar(int $id): void {
        $conexion = BD::obtenerConexion();
        $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
        $stmt->execute(['id' => $id]);
    }
}