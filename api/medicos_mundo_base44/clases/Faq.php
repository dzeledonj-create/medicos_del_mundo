<?php
require_once 'BD.php';

class Faq {
    private $id_faq;
    private $pregunta;
    private $respuesta;

    public function __construct($id_faq, $pregunta, $respuesta) {
        $this->id_faq    = $id_faq;
        $this->pregunta  = $pregunta;
        $this->respuesta = $respuesta;
    }

    // ── Getters ──────────────────────────────────────────────────────────────

    public function getIdFaq() { return $this->id_faq; }
    public function getPregunta() { return $this->pregunta; }
    public function getRespuesta() { return $this->respuesta; }

    // ── Métodos Estáticos (Lógica de BD) ─────────────────────────────────────

    /**
     * Devuelve todas las FAQs de la base de datos.
     */
    public static function obtenerTodas(): array {
        $conexion = BD::obtenerConexion();
        $stmt = $conexion->prepare("SELECT * FROM faq ORDER BY id_faq ASC");
        $stmt->execute();

        $faqs = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $faqs[] = new Faq(
                $fila['id_faq'],
                $fila['pregunta'],
                $fila['respuesta']
            );
        }
        return $faqs;
    }

    /**
     * Crea una nueva FAQ (Para tu panel de admin).
     */
    public static function anadir(string $pregunta, string $respuesta): bool {
        $conexion = BD::obtenerConexion();
        $stmt = $conexion->prepare(
            "INSERT INTO faq (pregunta, respuesta) VALUES (:pregunta, :respuesta)"
        );
        return $stmt->execute([
            'pregunta'  => $pregunta,
            'respuesta' => $respuesta
        ]);
    }

    /**
     * Elimina una FAQ por su ID.
     */
    public static function eliminar(int $id): bool {
        $conexion = BD::obtenerConexion();
        $stmt = $conexion->prepare("DELETE FROM faq WHERE id_faq = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Devuelve una FAQ concreta por su ID.
     * Se usa en editar_faqs.php para rellenar el formulario con los datos actuales.
     */
    public static function obtenerPorId(int $id) {
        $conexion = BD::obtenerConexion();
        $stmt = $conexion->prepare("SELECT * FROM faq WHERE id_faq = :id");
        $stmt->execute(['id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fila == false) {
            return null;
        }
        return new Faq($fila['id_faq'], $fila['pregunta'], $fila['respuesta']);
    }

    /**
     * Actualiza los datos de una FAQ existente.
     * Se usa en editar_faqs.php al enviar el formulario de actualizar.
     */
    public static function actualizar(int $id, string $pregunta, string $respuesta): bool {
        $conexion = BD::obtenerConexion();
        $stmt = $conexion->prepare(
            "UPDATE faq SET pregunta = :pregunta, respuesta = :respuesta WHERE id_faq = :id"
        );
        return $stmt->execute(['pregunta' => $pregunta, 'respuesta' => $respuesta, 'id' => $id]);
    }
}