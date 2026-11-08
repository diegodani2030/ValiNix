<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Solicitud de cotización enviada desde el formulario de contacto:
 * limpia los datos, los valida y envía el correo.
 */
final class ContactRequest
{
    /** @var string */
    public $nombre = '';
    /** @var string */
    public $empresa = '';
    /** @var string */
    public $correo = '';
    /** @var string */
    public $telefono = '';
    /** @var string */
    public $tipoProyecto = '';
    /** @var string */
    public $presupuesto = '';
    /** @var string */
    public $mensaje = '';
    /** @var string Campo trampa: los humanos no lo ven, los bots lo llenan. */
    private $honeypot = '';

    /** @param array<string, mixed> $input Normalmente $_POST. */
    public static function fromInput(array $input): self
    {
        $request = new self();

        $request->nombre       = self::clean($input['nombre'] ?? '', 100);
        $request->empresa      = self::clean($input['empresa'] ?? '', 120);
        $request->correo       = self::clean(filter_var((string) self::scalar($input['correo'] ?? ''), FILTER_SANITIZE_EMAIL), 150);
        $request->telefono     = self::clean($input['telefono'] ?? '', 25);
        $request->tipoProyecto = self::clean($input['tipo_proyecto'] ?? '', 30);
        $request->presupuesto  = self::clean($input['presupuesto'] ?? '', 30);
        $request->mensaje      = self::clean($input['mensaje'] ?? '', 3000, true);
        $request->honeypot     = self::clean($input['website'] ?? '', 200);

        return $request;
    }

    public function isSpam(): bool
    {
        return $this->honeypot !== '';
    }

    /** @return string[] Lista de errores (vacía si todo está bien). */
    public function validate(): array
    {
        $errors = [];

        if (mb_strlen($this->nombre) < 2) {
            $errors[] = 'El nombre es obligatorio.';
        }
        if (!filter_var($this->correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo electrónico no es válido.';
        }
        if (!preg_match('/^[0-9+\s\-()]{7,20}$/', $this->telefono)) {
            $errors[] = 'El teléfono no es válido.';
        }
        if (mb_strlen($this->mensaje) < 10) {
            $errors[] = 'Cuéntanos un poco más en el mensaje (mínimo 10 caracteres).';
        }
        if ($this->tipoProyecto !== '' && !array_key_exists($this->tipoProyecto, SiteContent::projectTypes())) {
            $errors[] = 'El tipo de proyecto no es válido.';
        }
        if (!array_key_exists($this->presupuesto, SiteContent::budgetRanges())) {
            $errors[] = 'El presupuesto seleccionado no es válido.';
        }

        return $errors;
    }

    /** Datos del formulario para volver a llenarlo si hubo errores. @return array<string, string> */
    public function toArray(): array
    {
        return [
            'nombre'        => $this->nombre,
            'empresa'       => $this->empresa,
            'correo'        => $this->correo,
            'telefono'      => $this->telefono,
            'tipo_proyecto' => $this->tipoProyecto,
            'presupuesto'   => $this->presupuesto,
            'mensaje'       => $this->mensaje,
        ];
    }

    /**
     * Envía la solicitud por correo con mail() de PHP.
     * Devuelve false si el servidor no tiene correo configurado.
     */
    public function send(string $to, string $from): bool
    {
        $types   = SiteContent::projectTypes();
        $budgets = SiteContent::budgetRanges();

        $subject = 'Nueva solicitud de cotización — ' . self::oneLine($this->nombre);

        $body  = "Nombre: {$this->nombre}\n";
        $body .= "Empresa: {$this->empresa}\n";
        $body .= "Correo: {$this->correo}\n";
        $body .= "Teléfono: {$this->telefono}\n";
        $body .= 'Tipo de proyecto: ' . ($types[$this->tipoProyecto] ?? $this->tipoProyecto) . "\n";
        $body .= 'Presupuesto aproximado: ' . ($budgets[$this->presupuesto] ?? $this->presupuesto) . "\n";
        $body .= "Mensaje:\n{$this->mensaje}\n";

        $headers  = 'From: ' . self::oneLine($from) . "\r\n";
        $headers .= 'Reply-To: ' . self::oneLine($this->correo) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        return @mail($to, mb_encode_mimeheader($subject, 'UTF-8'), $body, $headers);
    }

    /** @param mixed $value */
    private static function clean($value, int $maxLength, bool $multiline = false): string
    {
        $value = trim(strip_tags((string) self::scalar($value)));

        if (!$multiline) {
            $value = self::oneLine($value);
        }

        return mb_substr($value, 0, $maxLength);
    }

    /**
     * Ignora arreglos u objetos enviados a propósito en lugar de texto.
     *
     * @param mixed $value
     * @return string|int|float|bool
     */
    private static function scalar($value)
    {
        return is_scalar($value) ? $value : '';
    }

    /** Quita saltos de línea (evita inyección de cabeceras en el correo). */
    private static function oneLine(string $value): string
    {
        return trim((string) preg_replace('/[\r\n\t]+/', ' ', $value));
    }
}
