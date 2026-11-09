<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\DatabaseException;
use App\Core\Session;
use App\Models\ContactRequest;
use App\Models\Contenido;
use App\Models\SiteContent;
use PDOException;

/**
 * Página de contacto y formulario de solicitud de cotización.
 */
final class ContactController extends Controller
{
    /** GET /contacto (acepta ?plan=basico|profesional|premium para preseleccionar el plan). */
    public function show(): void
    {
        $planOptions = Contenido::planOptions();
        $old         = Session::pullFlash('form_old', []);

        $plan = is_string($_GET['plan'] ?? null) ? $_GET['plan'] : '';
        if ($old === [] && $plan !== '' && array_key_exists($plan, $planOptions)) {
            $old = ['plan' => $plan];
        }

        $this->view('pages/contact', [
            'page'           => SiteContent::page('contacto'),
            'serviceOptions' => Contenido::serviceOptions(),
            'planOptions'    => $planOptions,
            'form'           => [
                'status'  => Session::pullFlash('form_status'),
                'message' => Session::pullFlash('form_message'),
                'old'     => $old,
                'token'   => Session::csrfToken(),
            ],
        ]);
    }

    /** POST /contacto: valida, guarda en la base de datos y vuelve a /contacto con un mensaje. */
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

        if (!$this->deliver($request)) {
            $this->finish(
                'error',
                'Error al guardar: no pudimos registrar tu mensaje en este momento. Inténtalo de nuevo o escríbenos por WhatsApp.',
                $request->toArray()
            );
        }

        $this->finish('success', 'Gracias ' . $request->nombre . ', recibimos tu solicitud. Te contactaremos muy pronto.');
    }

    /** Guarda en la tabla mensajes; si no hay base de datos, intenta por correo. */
    private function deliver(ContactRequest $request): bool
    {
        try {
            $request->save();
            return true;
        } catch (DatabaseException | PDOException $exception) {
            error_log('ValiNix: no se pudo guardar el mensaje de contacto: ' . $exception->getMessage());
        }

        return $request->send((string) config('contact.email'), (string) config('contact.from'));
    }

    /** @param array<string, string> $old Datos para volver a llenar el formulario. */
    private function finish(string $status, string $message, array $old = []): void
    {
        Session::flash('form_status', $status);
        Session::flash('form_message', $message);
        if ($old !== []) {
            Session::flash('form_old', $old);
        }

        $this->redirect('/contacto', 'contactForm');
    }
}
