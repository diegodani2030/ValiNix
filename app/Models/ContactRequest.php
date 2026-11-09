<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DatabaseException;
use PDOException;

/**
 * Solicitud enviada desde el formulario de contacto:
 * limpia los datos, los valida y los guarda en la tabla mensajes.
 * Si no hay base de datos, intenta enviarlos por correo con mail().
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
    public $asunto = '';
    /** @var string Id del servicio elegido ('' = ninguno). */
    public $servicio = '';
    /** @var string Clave del plan elegido ('' o 'no-seguro' = ninguno). */
    public $plan = '';
    /** @var string */
    public $mensaje = '';
    /** @var string Campo trampa: los humanos no lo ven, los bots lo llenan. */
    private $honeypot = '';

    /** @param array<string, mixed> $input Normalmente $_POST. */
    public static function fromInput(array $input): self
    {
        $request = new self();

        $request->nombre   = self::clean($input['nombre'] ?? '', 100);
        $request->empresa  = self::clean($input['empresa'] ?? '', 120);
        $request->correo   = self::clean(filter_var((string) self::scalar($input['correo'] ?? ''), FILTER_SANITIZE_EMAIL), 150);
        $request->telefono = self::clean($input['telefono'] ?? '', 25);
        $request->asunto   = self::clean($input['asunto'] ?? '', 150);
        $request->servicio = self::clean($input['servicio'] ?? '', 10);
        $request->plan     = self::clean($input['plan'] ?? '', 20);
        $request->mensaje  = self::clean($input['mensaje'] ?? '', 3000, true);
        $request->honeypot = self::clean($input['website'] ?? '', 200);

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

        if ($this->nombre === '') {
            $errors[] = 'El campo Nombre es obligatorio.';
        } elseif (mb_strlen($this->nombre) < 2) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres.';
        }
        if ($this->correo === '') {
            $errors[] = 'El campo Correo electrónico es obligatorio.';
        } elseif (!filter_var($this->correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo electrónico no es válido.';
        }
        if ($this->telefono === '') {
            $errors[] = 'El campo Teléfono es obligatorio.';
        } elseif (!preg_match('/^[0-9+\s\-()]{7,20}$/', $this->telefono)) {
            $errors[] = 'El teléfono no es válido (solo números, espacios, +, - y paréntesis).';
        }
        if ($this->asunto === '') {
            $errors[] = 'El campo Asunto es obligatorio.';
        } elseif (mb_strlen($this->asunto) < 3) {
            $errors[] = 'El asunto debe tener al menos 3 caracteres.';
        }
        if (mb_strlen($this->mensaje) < 10) {
            $errors[] = 'Cuéntanos un poco más en el mensaje (mínimo 10 caracteres).';
        }
        if ($this->servicio !== '' && (!ctype_digit($this->servicio) || !array_key_exists((int) $this->servicio, Contenido::serviceOptions()))) {
            $errors[] = 'El servicio seleccionado no es válido.';
        }
        if (!array_key_exists($this->plan, Contenido::planOptions())) {
            $errors[] = 'El plan seleccionado no es válido.';
        }

        return $errors;
    }

    /** Datos del formulario para volver a llenarlo si hubo errores. @return array<string, string> */
    public function toArray(): array
    {
        return [
            'nombre'   => $this->nombre,
            'empresa'  => $this->empresa,
            'correo'   => $this->correo,
            'telefono' => $this->telefono,
            'asunto'   => $this->asunto,
            'servicio' => $this->servicio,
            'plan'     => $this->plan,
            'mensaje'  => $this->mensaje,
        ];
    }

    /**
     * Guarda la solicitud en la tabla mensajes (el panel la muestra en "Mensajes").
     *
     * @throws DatabaseException Si la base de datos no está disponible.
     * @throws PDOException      Si la consulta falla.
     */
    public function save(): int
    {
        $plan = null;
        if ($this->plan !== '' && $this->plan !== 'no-seguro') {
            $fila = (new Plan())->buscarPorClave($this->plan);
            $plan = $fila !== null ? (int) $fila['id'] : null;
        }

        return (new Mensaje())->crear([
            'servicio_id' => $this->servicio !== '' ? (int) $this->servicio : null,
            'plan_id'     => $plan,
            'nombre'      => $this->nombre,
            'empresa'     => $this->empresa,
            'correo'      => $this->correo,
            'telefono'    => $this->telefono,
            'asunto'      => $this->asunto,
            'mensaje'     => $this->mensaje,
        ]);
    }

    /**
     * Plan B: envía la solicitud por correo con mail() de PHP.
     * Devuelve false si el servidor no tiene correo configurado (InfinityFree gratis no lo tiene).
     */
    public function send(string $to, string $from): bool
    {
        $services = Contenido::serviceOptions();
        $plans    = Contenido::planOptions();

        $subject = 'Contacto ValiNix — ' . self::oneLine($this->asunto);

        $body  = "Nombre: {$this->nombre}\n";
        $body .= "Empresa: {$this->empresa}\n";
        $body .= "Correo: {$this->correo}\n";
        $body .= "Teléfono: {$this->telefono}\n";
        $body .= "Asunto: {$this->asunto}\n";
        $body .= 'Servicio: ' . ($services[(int) $this->servicio] ?? '-') . "\n";
        $body .= 'Plan: ' . ($plans[$this->plan] ?? '-') . "\n";
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
        // mb_scrub: si llega texto con una codificación rota, se reemplazan los bytes inválidos.
        $value = trim(strip_tags(mb_scrub((string) self::scalar($value), 'UTF-8')));

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
