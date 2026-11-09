<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Validación del lado del servidor para los formularios del panel.
 *
 * Uso:
 *   $v = new Validator($datos, ['titulo' => 'Título']);
 *   $v->required('titulo')->max('titulo', 100);
 *   if ($v->fails()) { $errores = $v->errors(); }
 *
 * Cada campo guarda solo su primer error (el más importante).
 */
final class Validator
{
    /** @var array<string, mixed> */
    private $data;

    /** @var array<string, string> Nombre visible de cada campo. */
    private $labels;

    /** @var array<string, string> */
    private $errors = [];

    /**
     * @param array<string, mixed>  $data
     * @param array<string, string> $labels
     */
    public function __construct(array $data, array $labels = [])
    {
        $this->data   = $data;
        $this->labels = $labels;
    }

    public function required(string ...$fields): self
    {
        foreach ($fields as $field) {
            if (trim((string) ($this->data[$field] ?? '')) === '') {
                $this->add($field, 'El campo ' . $this->label($field) . ' es obligatorio.');
            }
        }

        return $this;
    }

    public function min(string $field, int $length): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value !== '' && mb_strlen($value) < $length) {
            $this->add($field, 'El campo ' . $this->label($field) . " debe tener al menos {$length} caracteres.");
        }

        return $this;
    }

    public function max(string $field, int $length): self
    {
        if (mb_strlen((string) ($this->data[$field] ?? '')) > $length) {
            $this->add($field, 'El campo ' . $this->label($field) . " admite máximo {$length} caracteres.");
        }

        return $this;
    }

    public function email(string $field): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->add($field, 'El campo ' . $this->label($field) . ' debe ser un correo válido (ej. nombre@dominio.com).');
        }

        return $this;
    }

    /** Solo letras, números, punto, guion y guion bajo (ej. nombres de usuario). */
    public function username(string $field): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value !== '' && !preg_match('/^[A-Za-z0-9._-]+$/', $value)) {
            $this->add($field, 'El campo ' . $this->label($field) . ' solo admite letras, números, punto, guion y guion bajo (sin espacios).');
        }

        return $this;
    }

    /** Enlace http(s) válido, o "#" para "sin enlace". */
    public function url(string $field): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value === '' || $value === '#') {
            return $this;
        }

        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
        if (!filter_var($value, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
            $this->add($field, 'El campo ' . $this->label($field) . ' debe ser un enlace que empiece por http:// o https:// (o # si no tiene).');
        }

        return $this;
    }

    public function integer(string $field, int $min, int $max): self
    {
        $value = $this->data[$field] ?? '';
        if ($value === '' || $value === null) {
            return $this;
        }

        if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]) === false) {
            $this->add($field, 'El campo ' . $this->label($field) . " debe ser un número entero entre {$min} y {$max}.");
        }

        return $this;
    }

    /** @param array<int|string, mixed> $allowed Valores permitidos. */
    public function in(string $field, array $allowed): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value !== '' && !in_array($value, array_map('strval', $allowed), true)) {
            $this->add($field, 'El valor elegido en ' . $this->label($field) . ' no es válido.');
        }

        return $this;
    }

    public function same(string $field, string $other): self
    {
        if ((string) ($this->data[$field] ?? '') !== (string) ($this->data[$other] ?? '')) {
            $this->add($field, 'Las contraseñas no coinciden.');
        }

        return $this;
    }

    /** Regla personalizada: si $failed es true, se agrega el mensaje. */
    public function check(bool $failed, string $field, string $message): self
    {
        if ($failed) {
            $this->add($field, $message);
        }

        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> campo => mensaje */
    public function errors(): array
    {
        return $this->errors;
    }

    private function add(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    private function label(string $field): string
    {
        return $this->labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
