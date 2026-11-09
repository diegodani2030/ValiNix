<?php
/**
 * Página /login (Formulario de Inicio de Sesión del manual).
 *
 * @var string      $usuario      Lo que escribió antes (si falló).
 * @var string|null $error
 * @var string|null $ok
 * @var string      $token
 * @var bool        $dbConfigured
 */

require APP_PATH . '/Views/partials/page-header.php';
?>
    <section class="login">
        <div class="container">
            <form class="login__card contact__form" action="<?= e(url('login')) ?>" method="POST" id="loginForm">
<?php if (!$dbConfigured): ?>
                <div class="form-alert form-alert--error">El panel aún no está conectado a la base de datos. Revisa la configuración (README → Base de datos).</div>
<?php endif; ?>
<?php if (!empty($error)): ?>
                <div class="form-alert form-alert--error" role="alert"><?= e($error) ?></div>
<?php elseif (!empty($ok)): ?>
                <div class="form-alert form-alert--success" role="status"><?= e($ok) ?></div>
<?php endif; ?>

                <input type="hidden" name="_token" value="<?= e($token) ?>">

                <div class="form-group">
                    <label for="usuario">Usuario o correo electrónico *</label>
                    <input type="text" id="usuario" name="usuario" required maxlength="150" autocomplete="username" value="<?= e($usuario) ?>" autofocus>
                </div>

                <div class="form-group">
                    <label for="contrasena">Contraseña *</label>
                    <input type="password" id="contrasena" name="contrasena" required maxlength="72" autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn--accent btn--block">Ingresar</button>

                <p class="login__help">¿Olvidaste tu contraseña? Comunícate con el administrador del sistema. Si eres visitante, no necesitas cuenta para navegar el sitio.</p>
            </form>
        </div>
    </section>
