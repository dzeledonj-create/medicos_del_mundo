<?php
require_once 'BD.php';

/**
 * Clase Bloque. Representa un bloque de contenido informativo.
 * Cada bloque pertenece a una categoría y tiene una imagen asociada
 * en la tabla 'contenido' (unida mediante LEFT JOIN).
 */
class Bloque {

    private $id_bloque;
    private $titulo;
    private $subtitulo;
    private $contenido;
    private $orden;
    private $fecha_actualizacion;
    private $id_categoria;
    // La imagen viene de la tabla 'contenido', no de 'bloque'
    private $url_imagen;

    public function __construct($id_bloque, $titulo, $subtitulo, $contenido, $orden, $fecha_actualizacion, $id_categoria) {
        $this->id_bloque           = $id_bloque;
        $this->titulo              = $titulo;
        $this->subtitulo           = $subtitulo;
        $this->contenido           = $contenido;
        $this->orden               = $orden;
        $this->fecha_actualizacion = $fecha_actualizacion;
        $this->id_categoria        = $id_categoria;
    }

    // ── Getters ──────────────────────────────────────────────────────────────

    public function getIdBloque() {
        return $this->id_bloque;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function getSubtitulo() {
        return $this->subtitulo;
    }

    public function getContenido() {
        return $this->contenido;
    }

    public function getOrden() {
        return $this->orden;
    }

    public function getIdCategoria() {
        return $this->id_categoria;
    }

    // Si no hay imagen en la BD devuelve una imagen por defecto para no romper el <img>
    public function getUrlImagen() {
        return $this->url_imagen ?: 'assets/imagenes/default.png';
    }

    // ── Setters ──────────────────────────────────────────────────────────────

    // Se usa justo después de crear el objeto para asignarle la imagen del JOIN
    public function setUrlImagen($url) {
        $this->url_imagen = $url;
    }


    // ── Métodos para las páginas PÚBLICAS (contenido.php) ────────────────────

    /**
     * Devuelve todos los bloques de una categoría con su imagen.
     * Usa LEFT JOIN con 'contenido' porque la imagen está en otra tabla.
     * Se usa en contenido.php para mostrar los bloques al usuario.
     */
    public static function obtenerPorCategoria(int $id_categoria): array {
        $conexion = BD::obtenerConexion();
        $query = "SELECT b.*, c.url_externas 
                  FROM bloque b 
                  LEFT JOIN contenido c ON b.id_bloque = c.id_bloque 
                  WHERE b.id_categoria = :id 
                  ORDER BY b.orden ASC";
        $stmt = $conexion->prepare($query);
        $stmt->execute(['id' => $id_categoria]);
        $bloques = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bloque = new Bloque(
                $fila['id_bloque'], $fila['titulo'], $fila['subtitulo'],
                $fila['contenido'], $fila['orden'], $fila['fecha_actualizacion'],
                $fila['id_categoria']
            );
            $bloque->setUrlImagen($fila['url_externas']);
            $bloques[] = $bloque;
        }
        return $bloques;
    }


    // ── Métodos para el PANEL DE ADMIN ────────────────────────────────────────

    /**
     * Devuelve todos los bloques con su imagen, ordenados por categoría y orden.
     * Se usa en gestion_contenido.php para mostrar la tabla del admin.
     */
    public static function obtenerTodosAdmin(): array {
        $conexion = BD::obtenerConexion();
        $query = "SELECT b.*, c.url_externas 
                  FROM bloque b 
                  LEFT JOIN contenido c ON b.id_bloque = c.id_bloque 
                  ORDER BY b.id_categoria ASC, b.orden ASC";
        $stmt = $conexion->prepare($query);
        $stmt->execute();
        $bloques = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bloque = new Bloque(
                $fila['id_bloque'], $fila['titulo'], $fila['subtitulo'],
                $fila['contenido'], $fila['orden'], $fila['fecha_actualizacion'],
                $fila['id_categoria']
            );
            $bloque->setUrlImagen($fila['url_externas']);
            $bloques[] = $bloque;
        }
        return $bloques;
    }

    /**
     * Busca y devuelve un bloque concreto por su ID, incluyendo su imagen.
     * Se usa en editar_contenido.php para rellenar el formulario con los datos actuales.
     */
    public static function obtenerPorId(int $id_bloque) {
        $conexion = BD::obtenerConexion();
        $query = "SELECT b.*, c.url_externas 
                  FROM bloque b 
                  LEFT JOIN contenido c ON b.id_bloque = c.id_bloque 
                  WHERE b.id_bloque = :id";
        $stmt = $conexion->prepare($query);
        $stmt->execute(['id' => $id_bloque]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fila == false) {
            return null;
        }
        $bloque = new Bloque(
            $fila['id_bloque'], $fila['titulo'], $fila['subtitulo'],
            $fila['contenido'], $fila['orden'], $fila['fecha_actualizacion'],
            $fila['id_categoria']
        );
        $bloque->setUrlImagen($fila['url_externas']);
        return $bloque;
    }

    /**
     * Crea un nuevo bloque y su imagen.
     * Son dos INSERTs porque los datos están en dos tablas distintas (bloque y contenido).
     * Se usa en gestion_contenido.php al enviar el formulario de crear.
     */
    public static function crear($titulo, $subtitulo, $contenido, $orden, $id_categoria, $url_imagen) {
        $conexion = BD::obtenerConexion();

        // Primero insertamos el bloque
        $query1 = "INSERT INTO bloque (titulo, subtitulo, contenido, orden, fecha_actualizacion, id_categoria) 
                   VALUES (:titulo, :subtitulo, :contenido, :orden, CURRENT_DATE, :id_categoria)";
        $stmt1 = $conexion->prepare($query1);
        $stmt1->execute([
            'titulo'       => $titulo,
            'subtitulo'    => $subtitulo,
            'contenido'    => $contenido,
            'orden'        => $orden,
            'id_categoria' => $id_categoria
        ]);

        // lastInsertId() nos da el ID que PostgreSQL asignó al bloque recién creado
        $nuevo_id = $conexion->lastInsertId('bloque_id_bloque_seq');

        // Luego insertamos la imagen en contenido enlazada con ese ID
        $query2 = "INSERT INTO contenido (url_externas, id_bloque) VALUES (:url, :id_bloque)";
        $stmt2  = $conexion->prepare($query2);
        $stmt2->execute(['url' => $url_imagen, 'id_bloque' => $nuevo_id]);
    }

    /**
     * Actualiza los datos de un bloque y su imagen.
     * Son dos operaciones porque los datos están en dos tablas distintas.
     * Usa UPSERT en contenido por si el bloque no tenía imagen todavía:
     * si existe la fila → actualiza, si no existe → la crea.
     * Se usa en editar_contenido.php al enviar el formulario de actualizar.
     */
    public static function actualizar($id_bloque, $titulo, $subtitulo, $contenido, $orden, $id_categoria, $url_imagen) {
        $conexion = BD::obtenerConexion();

        // Actualizamos los datos del bloque
        $query1 = "UPDATE bloque 
                   SET titulo = :titulo, subtitulo = :subtitulo, contenido = :contenido,
                       orden = :orden, id_categoria = :id_categoria, fecha_actualizacion = CURRENT_DATE
                   WHERE id_bloque = :id";
        $stmt1 = $conexion->prepare($query1);
        $stmt1->execute([
            'titulo'       => $titulo,
            'subtitulo'    => $subtitulo,
            'contenido'    => $contenido,
            'orden'        => $orden,
            'id_categoria' => $id_categoria,
            'id'           => $id_bloque
        ]);

        // UPSERT: si ya existe fila en contenido → actualiza, si no → inserta
        $query2 = "INSERT INTO contenido (url_externas, id_bloque) 
                   VALUES (:url, :id)
                   ON CONFLICT (id_bloque) 
                   DO UPDATE SET url_externas = :url";
        $stmt2 = $conexion->prepare($query2);
        $stmt2->execute(['url' => $url_imagen, 'id' => $id_bloque]);
    }

    /**
     * Elimina un bloque y su imagen.
     * Hay que borrar primero en contenido y luego en bloque por la FK entre tablas.
     * Se usa en gestion_contenido.php al pulsar el botón de eliminar.
     */
    public static function eliminar(int $id) {
        $conexion = BD::obtenerConexion();
        $query1 = "DELETE FROM contenido WHERE id_bloque = :id";
        $stmt1  = $conexion->prepare($query1);
        $stmt1->execute(['id' => $id]);
        $query2 = "DELETE FROM bloque WHERE id_bloque = :id";
        $stmt2  = $conexion->prepare($query2);
        return $stmt2->execute(['id' => $id]);
    }
}