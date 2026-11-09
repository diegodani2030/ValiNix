<?php /** Usuario registrado intentando una acción de administrador (403). */ ?>
            <div class="admin-card admin-empty">
                <h2>No tienes permisos para esta acción</h2>
                <p>Tu cuenta es de <strong>Usuario registrado</strong>: puedes consultar la información del panel, pero solo un Administrador puede crear, editar o eliminar registros.</p>
                <a href="<?= e(url('admin')) ?>" class="btn btn--accent btn--sm">Volver al inicio del panel</a>
            </div>
