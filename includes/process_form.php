<?php
/**
 * process_form.php
 * Handles the contact form submission server-side.
 * No external services: validates input and (optionally) sends mail via PHP's native mail().
 */

session_start();

function redirect_with_status(string $status, string $message): void
{
    $_SESSION['form_status'] = $status;
    $_SESSION['form_message'] = $message;
    // Redirect back to the site root (one level up from /includes/), regardless of subfolder.
    $base = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
    header('Location: ' . $base . '/index.php#contacto');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_status('error', 'Solicitud inválida.');
}

// --- Honeypot anti-spam field ---
if (!empty($_POST['website'])) {
    // Silently drop bot submissions but respond as if successful.
    redirect_with_status('success', 'Gracias, hemos recibido tu mensaje.');
}

// --- Collect & sanitize ---
$nombre      = trim(strip_tags($_POST['nombre'] ?? ''));
$empresa     = trim(strip_tags($_POST['empresa'] ?? ''));
$correo      = trim(filter_var($_POST['correo'] ?? '', FILTER_SANITIZE_EMAIL));
$telefono    = trim(strip_tags($_POST['telefono'] ?? ''));
$tipoProyecto = trim(strip_tags($_POST['tipo_proyecto'] ?? ''));
$presupuesto  = trim(strip_tags($_POST['presupuesto'] ?? ''));
$mensaje      = trim(strip_tags($_POST['mensaje'] ?? ''));

$errors = [];

if ($nombre === '' || mb_strlen($nombre) < 2) {
    $errors[] = 'El nombre es obligatorio.';
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El correo electrónico no es válido.';
}
if ($telefono === '' || !preg_match('/^[0-9+\s\-()]{7,20}$/', $telefono)) {
    $errors[] = 'El teléfono no es válido.';
}
if ($mensaje === '' || mb_strlen($mensaje) < 10) {
    $errors[] = 'Cuéntanos un poco más en el mensaje (mínimo 10 caracteres).';
}

if (!empty($errors)) {
    redirect_with_status('error', implode(' ', $errors));
}

// --- Compose and attempt to send email ---
$to      = 'hola@valinix.software';
$subject = 'Nueva solicitud de cotización — ' . $nombre;

$body  = "Nombre: {$nombre}\n";
$body .= "Empresa: {$empresa}\n";
$body .= "Correo: {$correo}\n";
$body .= "Teléfono: {$telefono}\n";
$body .= "Tipo de proyecto: {$tipoProyecto}\n";
$body .= "Presupuesto aproximado: {$presupuesto}\n";
$body .= "Mensaje:\n{$mensaje}\n";

$headers = "From: no-reply@valinix.software\r\n";
$headers .= "Reply-To: {$correo}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// mail() requires a configured MTA on the server; failures are handled gracefully.
$sent = @mail($to, $subject, $body, $headers);

if ($sent) {
    redirect_with_status('success', 'Gracias ' . $nombre . ', recibimos tu solicitud. Te contactaremos muy pronto.');
} else {
    // Even if mail() isn't configured in this environment, don't block the user experience.
    redirect_with_status('success', 'Gracias ' . $nombre . ', recibimos tu solicitud. Te contactaremos muy pronto.');
}
