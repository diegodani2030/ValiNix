/* =========================================================
   ValiNix — Panel administrativo
   - Confirmación antes de eliminar (formularios con data-confirm)
   - Filtros que se aplican al cambiar el select (data-autosubmit)
   - Validación en el navegador (formularios con data-validate);
     el servidor vuelve a validar todo de todas formas.
   ========================================================= */
(function () {
    'use strict';

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-autosubmit]').forEach(function (select) {
        select.addEventListener('change', function () {
            if (select.form) select.form.submit();
        });
    });

    function labelOf(field) {
        var label = field.form.querySelector('label[for="' + field.id + '"]');
        return label ? label.textContent.replace('*', '').trim() : field.name;
    }

    function messageFor(field) {
        var value = field.value.trim();
        var name = labelOf(field);

        if (field.required && value === '') return 'El campo ' + name + ' es obligatorio.';
        if (value === '') return '';

        var min = field.getAttribute('minlength');
        if (min && value.length < Number(min)) return 'El campo ' + name + ' debe tener al menos ' + min + ' caracteres.';

        if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            return 'El campo ' + name + ' debe ser un correo válido (ej. nombre@dominio.com).';
        }
        if (field.type === 'number') {
            var n = Number(value);
            var lo = field.getAttribute('min');
            var hi = field.getAttribute('max');
            if (!/^-?\d+$/.test(value) || (lo !== null && n < Number(lo)) || (hi !== null && n > Number(hi))) {
                return 'El campo ' + name + ' debe ser un número entero entre ' + lo + ' y ' + hi + '.';
            }
        }
        if (field.getAttribute('inputmode') === 'url' && value !== '#' && !/^https?:\/\/\S+\.\S+/i.test(value)) {
            return 'El campo ' + name + ' debe empezar por http:// o https:// (o # si no tiene).';
        }
        var pattern = field.getAttribute('pattern');
        if (pattern && !new RegExp('^(?:' + pattern + ')$').test(value)) {
            return 'El campo ' + name + ' tiene caracteres no permitidos.';
        }
        var match = field.getAttribute('data-match');
        if (match) {
            var other = field.form.querySelector('[name="' + match + '"]');
            if (other && other.value !== field.value) return 'Las contraseñas no coinciden.';
        }
        return '';
    }

    function showError(field, message) {
        var group = field.closest('.form-group');
        var error = group ? group.querySelector('.field-error') : null;

        if (message) {
            if (!error && group) {
                error = document.createElement('span');
                error.className = 'field-error';
                error.id = 'error-' + field.name;
                group.appendChild(error);
            }
            if (error) error.textContent = message;
            field.setAttribute('aria-invalid', 'true');
            if (error) field.setAttribute('aria-describedby', error.id);
        } else {
            if (error) error.remove();
            field.removeAttribute('aria-invalid');
            field.removeAttribute('aria-describedby');
        }
        return !message;
    }

    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        var fields = form.querySelectorAll('input:not([type=hidden]):not([type=checkbox]), select, textarea');

        fields.forEach(function (field) {
            field.addEventListener('blur', function () { showError(field, messageFor(field)); });
        });

        form.addEventListener('submit', function (event) {
            var firstInvalid = null;
            fields.forEach(function (field) {
                if (!showError(field, messageFor(field)) && !firstInvalid) firstInvalid = field;
            });
            if (firstInvalid) {
                event.preventDefault();
                firstInvalid.focus();
            }
        });
    });
})();
