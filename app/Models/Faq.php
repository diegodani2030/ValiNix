<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/** Tabla faqs (preguntas frecuentes). */
final class Faq extends Model
{
    /** @return array<int, array<string, mixed>> */
    public function todas(): array
    {
        return $this->all('SELECT id, pregunta, respuesta FROM faqs ORDER BY orden, id');
    }
}
