<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ContactRequest;

/**
 * Formulario de contacto / solicitud de cotización.
 */
final class ContactController extends Controller
{
    /** GET /contacto: lleva a la sección de contacto de la página principal. */
    public function show(): void
    {
        $this->redirect('/', 'contacto');
    }

    /** POST /contacto: procesa el formulario y vuelve a la página con un mensaje. */
    public function store(): void
    {
        $request = ContactRequest::fromInput($_POST);

        // Bots: se descartan en silencio, pero se les responde como si todo saliera bien.
        if ($request->isSpam()) {
            $this->finish('success', 'Gracias, hemos recibido tu mensaje.');
        }

        if (!Session::verifyCsrf($_POST['_token'] ?? null)) {
            $this->finish('error', 'Tu sesión expiró. Por favor, envía el formulario de nuevo.', $request->toArray());
        }

        $errors = $request->validate();
        if ($errors !== []) {
            $this->finish('error', implode(' ', $errors), $request->toArray());
        }

        $sent = $request->send((string) config('contact.email'), (string) config('contact.from'));
        if (!$sent) {
            // El hosting gratuito no siempre permite mail(); se registra para revisarlo.
            error_log('ValiNix: mail() no pudo enviar la solicitud de ' . $request->correo);
        }

        $this->finish('success', 'Gracias ' . $request->nombre . ', recibimos tu solicitud. Te contactaremos muy pronto.');
    }

    /** @param array<string, string> $old Datos para volver a llenar el formulario. */
    private function finish(string $status, string $message, array $old = []): void
    {
        Session::flash('form_status', $status);
        Session::flash('form_message', $message);
        if ($old !== []) {
            Session::flash('form_old', $old);
        }

        $this->redirect('/', 'contacto');
    }
}
