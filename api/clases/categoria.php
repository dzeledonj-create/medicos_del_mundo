<?php
require_once __DIR__ . '/BD.php';

/**
 * Clase Categoria, Representa una categoría del sistema (puede ser madre o hija de otra).
 */
class Categoria {

    private $id_categoria;
    private string $titulo;
    private $descripcion;
    private $icono;
    private $id_madre;
    private $fecha_actualizacion;
    private $orden;

    public function __construct($id_categoria, $titulo, $descripcion, $icono, $id_madre = null, $fecha_actualizacion, $orden = 0) {
        $this->id_categoria = $id_categoria;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->icono = $icono;
        $this->id_madre = $id_madre;
        $this->fecha_actualizacion = $fecha_actualizacion;
        $this->orden = $orden;
    }

    // ── Getters ──────────────────────────────────────────────────────────────

    public function getIdCategoria() {
        return $this->id_categoria;
    }

    public function getTitulo(): string {
        return $this->titulo;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getIcono() {
        return $this->icono;
    }

    public function getIdMadre() {
        return $this->id_madre;
    }

    public function getOrden() {
        return $this->orden;
    }

    public function getFechaActualizacion() {
        return $this->fecha_actualizacion;
    }


    // ── Métodos para las páginas PÚBLICAS (index, subcategorias, contenido) ──

    /**
     * Devuelve todas las categorías raíz (sin madre).
     * Se usa en index.php para mostrar las tarjetas principales al usuario.
     */
    public static function obtenerCategorias(): array {
        $conexion = BD::obtenerConexion();
        $query = "SELECT * FROM CATEGORIA WHERE id_madre IS NULL ORDER BY orden ASC";
        $stmt = $conexion->prepare($query);
        $stmt->execute();
        $categorias = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categorias[] = new Categoria(
                $fila['id_categoria'], $fila['titulo'], $fila['descripcion'],
                $fila['icono'], $fila['id_madre'], $fila['fecha_actualizacion'], $fila['orden']
            );
        }
        return $categorias;
    }

    /**
     * Devuelve las subcategorías hijas de una categoría concreta.
     * Se usa en subcategorias.php pasando el id de la madre desde la URL.
     */
    public static function obtenerHijas(int $id_madre): array {
        $conexion = BD::obtenerConexion();
        $query = "SELECT * FROM CATEGORIA WHERE id_madre = :id_madre ORDER BY orden ASC";
        $stmt = $conexion->prepare($query);
        $stmt->execute(['id_madre' => $id_madre]);
        $subcategorias = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subcategorias[] = new Categoria(
                $fila['id_categoria'], $fila['titulo'], $fila['descripcion'],
                $fila['icono'], $fila['id_madre'], $fila['fecha_actualizacion'], $fila['orden']
            );
        }
        return $subcategorias;
    }

    /**
     * Busca y devuelve una categoría concreta por su ID.
     * Se usa en contenido.php para mostrar el título de la categoría actual.
     */
    public static function obtenerPorId($id_categoria) {
        $conexion = BD::obtenerConexion();
        $query = "SELECT * FROM categoria WHERE id_categoria = :id";
        $stmt = $conexion->prepare($query);
        $stmt->execute(['id' => $id_categoria]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fila == false) {
            return null;
        }
        return new Categoria(
            $fila['id_categoria'], $fila['titulo'], $fila['descripcion'],
            $fila['icono'], $fila['id_madre'], $fila['fecha_actualizacion'], $fila['orden']
        );
    }

    /**
     * Comprueba si una categoría tiene hijas. Devuelve true o false.
     * La usa getEnlace() para decidir a dónde redirigir al usuario.
     */
    public static function tieneHijas($id_categoria): bool{
        $conexion = BD::obtenerConexion();
        $query = "SELECT COUNT(*) FROM categoria WHERE id_madre = :id";
        $stmt = $conexion->prepare($query);
        $stmt->execute(['id' => $id_categoria]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Devuelve la URL a la que apunta el enlace de una tarjeta.
     * Si tiene hijas → subcategorias.php para seguir navegando.
     * Si no tiene hijas → contenido.php porque ya es categoría final.
     * Se usa en index.php y subcategorias.php dentro del foreach de tarjetas.
     */
    public function getEnlace() {
        if (Categoria::tieneHijas($this->id_categoria)) {
            return "/../subcategorias.php?id=" . $this->id_categoria;
        } else {
            return "/../contenido.php?id=" . $this->id_categoria;
        }
    }


    // ── Métodos para el PANEL DE ADMIN ────────────────────────────────────────

    /**
     * Devuelve TODAS las categorías ordenadas para que cada principal
     * aparezca seguida de sus hijas. Se usa en los selects y tablas del admin.
     */
    public static function obtenerTodasAdmin(): array {
        $conexion = BD::obtenerConexion();
        $query = "SELECT * FROM categoria ORDER BY COALESCE(id_madre, id_categoria), id_madre NULLS FIRST, orden ASC";
        $stmt = $conexion->prepare($query);
        $stmt->execute();
        $categorias = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categorias[] = new Categoria(
                $fila['id_categoria'], $fila['titulo'], $fila['descripcion'],
                $fila['icono'], $fila['id_madre'], $fila['fecha_actualizacion'], $fila['orden']
            );
        }
        return $categorias;
    }

    /**
     * Crea una nueva categoría en la BD.
     * Se usa en gestion_categorias.php al enviar el formulario de crear.
     */
    public static function crear($titulo, $descripcion, $icono, $id_madre, $orden) {
        $conexion = BD::obtenerConexion();
        $id_madre = ($id_madre == 0) ? null : $id_madre;
        $query = "INSERT INTO categoria (titulo, descripcion, icono, id_madre, orden, fecha_actualizacion) 
                  VALUES (:titulo, :descripcion, :icono, :id_madre, :orden, CURRENT_DATE)";
        $stmt = $conexion->prepare($query);
        return $stmt->execute([
            'titulo' => $titulo, 'descripcion' => $descripcion,
            'icono' => $icono, 'id_madre' => $id_madre, 'orden' => $orden
        ]);
    }

    /**
     * Actualiza los datos de una categoría existente.
     * Se usa en editar_categoria.php al enviar el formulario de actualizar.
     */
    public static function actualizar($id_categoria, $titulo, $descripcion, $icono, $id_madre, $orden) {
        $conexion = BD::obtenerConexion();
        $madre_sql = ($id_madre === null) ? "NULL" : $id_madre;
        $sql = "UPDATE categoria 
                SET titulo = '$titulo', 
                    descripcion = '$descripcion', 
                    icono = '$icono', 
                    id_madre = $madre_sql, 
                    orden = $orden, 
                    fecha_actualizacion = CURRENT_DATE
                WHERE id_categoria = $id_categoria";
        $conexion->query($sql);
    }

    /**
     * Elimina una categoría por su ID.
     * Si tiene hijas, el DELETE fallará por FK — eso es bueno, evita borrados accidentales.
     * Se usa en gestion_categorias.php al pulsar el botón de eliminar.
     */
    public static function eliminar($id) {
        $conexion = BD::obtenerConexion();
        $query = "DELETE FROM categoria WHERE id_categoria = :id";
        $stmt = $conexion->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
}